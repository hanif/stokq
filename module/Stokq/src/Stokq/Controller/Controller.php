<?php

namespace Stokq\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Stokq\Controller\Exception\MethodNotAllowedException;
use Stokq\Controller\Mixin\ServiceMixin;
use Stokq\Controller\Plugin\Message;
use Stokq\Stdlib\DataMapper;
use Stokq\Stdlib\ExceptionHandler;
use Zend\Form\Form;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Stdlib\InitializableInterface;

/**
 * Class Controller
 * @package Stokq\Controller
 */
abstract class Controller extends AbstractActionController implements ServiceLocatorAwareInterface
{
    use ServiceMixin;

    /**
     * @var array|DataMapper[]
     */
    protected $mappers = [];

    /**
     * @var ExceptionHandler
     */
    protected $exceptionHandler;

    /**
     * @var bool
     */
    protected $protectRequestMethodByPrefix = false;

    /**
     * @param MvcEvent $event
     * @return mixed
     * @throws \Exception
     */
    public function onDispatch(MvcEvent $event)
    {
        $routeMatch = $event->getRouteMatch();

        if (!$routeMatch) {
            throw new \DomainException('Missing route matches.');
        }

        $handler = $this->getExceptionHandler();
        $action = $routeMatch->getParam('action', 'not-found');

        if ($this instanceof InitializableInterface) {
            try {
                $this->init();
            } catch (\Exception $e) {
                return $handler->tryHandle($e);
            }
        }

        $method = static::getMethodFromAction($action);
        if (!method_exists($this, $method)) {
            $method = 'notFoundAction';
        }

        try {

            if ($this->protectRequestMethodByPrefix) {
                $this->protectRequestMethod($method);
            }

            $actionResponse = $this->{$method}($this->getRequest(), $this->getResponse());
            $event->setResult($actionResponse);
            return $actionResponse;
        } catch (\Exception $e) {
            return $handler->tryHandle($e);
        }
    }

    /**
     * @return ExceptionHandler
     */
    public function getExceptionHandler()
    {
        if (!$this->exceptionHandler) {
            $this->exceptionHandler = $this->getServiceLocator()->get(ExceptionHandler::class);
        }
        return $this->exceptionHandler;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->getServiceLocator()->get(EntityManager::class);
    }

    /**
     * @return EntityManager
     */
    public function em()
    {
        return $this->getEntityManager();
    }

    /**
     * @return Connection
     */
    public function getDatabaseConnection()
    {
        return $this->getEntityManager()->getConnection();
    }

    /**
     * @return Connection
     */
    public function db()
    {
        return $this->getDatabaseConnection();
    }

    /**
     * @param $entities
     * @return $this
     */
    public function persist(...$entities)
    {
        foreach ($entities as $entity) {
            $this->em()->persist($entity);
        }

        return $this;
    }

    /**
     * @param null|object|array $entity
     * @return $this
     */
    public function flush($entity = null)
    {
        $this->em()->flush($entity);
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function commit()
    {
        $this->db()->beginTransaction();

        try {
            $this->flush();
            $this->db()->commit();
        } catch (\Exception $e) {
            $this->db()->rollback();
            throw $e;
        }

        return $this;
    }

    /**
     * @param string $class
     * @param ...$args
     * @return Form
     */
    public function form($class, ...$args)
    {
        $form = $this->instance($class, ...$args);

        if (!$form instanceof Form) {
            throw new \DomainException('Invalid form class given.');
        }

        $this->setupServiceInitialization($form);
        return $form;
    }

    /**
     * @param string $class
     * @param ...$args
     * @return InputFilter
     */
    public function inputFilter($class, ...$args)
    {
        $inputFilter = $this->instance($class, ...$args);

        if (!$inputFilter instanceof InputFilter) {
            throw new \DomainException('Invalid input filter class given.');
        }

        $this->setupServiceInitialization($inputFilter);
        return $inputFilter;
    }

    /**
     * @param string $class
     * @param ...$args
     * @return Form
     */
    public function autoFilledForm($class, ...$args)
    {
        $form = $this->form($class, ...$args);
        $form->setData($this->getInputData());
        return $form;
    }

    /**
     * @param string $class
     * @param ...$args
     * @return InputFilter
     */
    public function autoFilledInputFilter($class, ...$args)
    {
        $inputFilter = $this->inputFilter($class, ...$args);
        $inputFilter->setData($this->getInputData());
        return $inputFilter;
    }

    /**
     * @return array
     */
    public function getInputData()
    {
        $request = $this->getRequest();
        if ($request instanceof Request) {
            switch (true) {
                case $request->isPost():
                case $request->isPut():
                case $request->isDelete():
                    return array_merge_recursive($request->getPost()->toArray(), $request->getFiles()->toArray());

                case $request->isGet():
                    return $request->getQuery()->toArray();

                default:
                    throw new \RuntimeException(sprintf('Cannot handle request method: %s', $request->getMethod()));
            }
        } else if ($request instanceof \Zend\Console\Request) {
            return $request->getParams()->toArray();
        }

        return [];
    }

    /**
     * @param string $entity
     * @return DataMapper
     */
    public function getMapper($entity)
    {
        if (!isset($this->mappers[$entity])) {
            $this->mappers[$entity] = new DataMapper($this->em(), $entity);
        }

        return $this->mappers[$entity];
    }

    /**
     * @param $entity
     * @return DataMapper
     */
    public function mapper($entity)
    {
        return $this->getMapper($entity);
    }

    /**
     * @param string $name
     * @return array|object
     */
    public function getService($name)
    {
        return $this->getServiceLocator()->get($name);
    }

    /**
     * @return ServiceLocatorInterface|ServiceManager
     */
    public function getServiceManager()
    {
        return $this->getServiceLocator();
    }

    /**
     * @param string $name
     * @return array|object
     */
    public function service($name)
    {
        return $this->getService($name);
    }

    /**
     * @param $class
     * @param $args
     * @return array|object
     */
    public function getInstance($class, ...$args)
    {
        switch (count($args)) {
            case 0: return new $class;
            case 1: return new $class($args[0]);
            case 2: return new $class($args[0], $args[1]);
            case 3: return new $class($args[0], $args[1], $args[2]);
            default:
                $r = new \ReflectionClass($class);
                return $r->newInstanceArgs($args);
        }
    }

    /**
     * @param $class
     * @param $args
     * @return array|object
     */
    public function instance($class, ...$args)
    {
        return $this->getInstance($class, ...$args);
    }

    /**
     * @return Message
     */
    public function message()
    {
        return $this->plugin('message');
    }

    /**
     * @return Message
     */
    public function msg()
    {
        return $this->message();
    }

    /**
     * @return Logger
     */
    public function logger()
    {
        return $this->service('logger');
    }

    /**
     * @return Logger
     */
    public function log()
    {
        return $this->logger();
    }

    /**
     * @param $str
     * @return array
     */
    public function routeSpec($str)
    {
        list ($withQuery, $section) = explode('#', $str) + ['', ''];
        list ($spec, $query) = explode('?', $withQuery) + ['', ''];
        $parts = explode('.', $spec);

        switch (count($parts)) {
            case 0:
                $name = 'home';
                $params = [];
                break;

            case 1:
                $name = $parts[0];
                $params = ['controller' => 'index', 'action' => 'index'];
                break;

            case 2:
                $name = $parts[0];
                $params = ['controller' => $parts[1], 'action' => 'index'];
                break;

            case 3:
                $name = $parts[0];
                $params = ['controller' => $parts[1], 'action' => $parts[2]];
                break;

            default:
                $action = array_pop($parts);
                $controller = array_pop($parts);
                $params = ['controller' => $controller, 'action' => $action];
                $name = join('/', $parts);
                break;
        }

        return [$name, $params, compact('section', 'query')];
    }

    /**
     * @param string $str
     * @return string
     */
    public function routeTo($str)
    {
        $spec = $this->routeSpec($str);
        $spec[2]['force_canonical'] = true;
        return $this->url()->fromRoute(...$spec);
    }

    /**
     * @param array $arr
     * @param int $status
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function responseJson(array $arr, $status = 200)
    {
        $response = $this->getResponse();
        $response->setContent(json_encode($arr));

        if ($response instanceof Response) {
            $response->setStatusCode($status);
            $response->getHeaders()->addHeaderLine("Content-Type: application/json");
        }

        return $response;
    }

    /**
     * @param $args
     */
    public function ensure(...$args)
    {
        $request = $this->getRequest();
        $invalidRequests = 0;
        foreach ($args as $arg) {
            switch (true) {
                case $arg == 'get':
                case $arg == 'put':
                case $arg == 'post':
                case $arg == 'delete':
                    if (($request instanceof Request) && !strtolower($request->getMethod()) == strtolower($arg)) {
                        $invalidRequests += 1;
                    }
                    break;

                case $arg == 'xhr':
                case $arg == 'ajax':
                    if (($request instanceof Request) && !$request->isXmlHttpRequest()) {
                        $invalidRequests += 1;
                    }
                    break;

                case is_callable($arg):
                    if (!$arg($this)) {
                        $invalidRequests += 1;
                    }
                    break;
            }
        }

        if ($invalidRequests >= count($args)) {
            throw new MethodNotAllowedException;
        }
    }

    /**
     * @param string|null $name
     * @param mixed $default
     * @return mixed|\Zend\Stdlib\ParametersInterface
     */
    public function getRequestQuery($name = null, $default = null)
    {
        $request = $this->getRequest();
        if ($request instanceof Request) {
            return $request->getQuery($name, $default);
        } else if ($request instanceof \Zend\Console\Request) {
            return $request->getParam($name, $default);
        }

        throw new \RuntimeException('Unexpected request object.');
    }

    /**
     * @param string|null $name
     * @param mixed $default
     * @return mixed|\Zend\Stdlib\ParametersInterface
     */
    public function getRequestPost($name = null, $default = null)
    {
        $request = $this->getRequest();
        if ($request instanceof Request) {
            return $request->getPost($name, $default);
        } else if ($request instanceof \Zend\Console\Request) {
            return $request->getParam($name, $default);
        }

        throw new \RuntimeException('Unexpected request object.');
    }

    /**
     * @param string|null $name
     * @param mixed $default
     * @return mixed|\Zend\Stdlib\ParametersInterface
     */
    public function getRequestFiles($name = null, $default = null)
    {
        $request = $this->getRequest();
        if ($request instanceof Request) {
            return $request->getFiles($name, $default);
        } else if ($request instanceof \Zend\Console\Request) {
            return $request->getParam($name, $default);
        }

        throw new \RuntimeException('Unexpected request object.');
    }

    /**
     * @param int $status
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function responseStatus($status)
    {
        $response = $this->getResponse();
        if ($response instanceof Response) {
            return $response->setStatusCode($status);
        } else if ($response instanceof \Zend\Console\Response) {
            return $response->setContent(sprintf('response status: %s', $status));
        }

        throw new \RuntimeException('Unexpected response object.');
    }

    /**
     * @param int $status
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function ok($status = 200)
    {
        return $this->responseStatus($status);
    }

    /**
     * @param mixed $content
     * @param int $status
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function jsonOk($content, $status = 200)
    {
        $response = $this->ok($status);
        $response->getHeaders()->addHeaderLine("Content-Type: application/json");
        return $response->setContent($this->toJson($content));
    }

    /**
     * @param int $status
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function error($status = 500)
    {
        return $this->responseStatus($status);
    }

    /**
     * @param mixed $content
     * @param int $status
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function jsonError($content, $status = 500)
    {
        $response = $this->error($status);
        $response->getHeaders()->addHeaderLine("Content-Type: application/json");
        return $response->setContent($this->toJson($content));
    }

    /**
     * @param $content
     * @return string
     */
    public function toJson($content)
    {
        switch (true) {
            case (is_array($content)):
                return json_encode($content);

            case (is_object($content)):
                return ($content instanceof \JsonSerializable) ? $content->jsonSerialize() : json_encode($content);

            case (is_scalar($content)):
                return json_encode(['content' => $content]);

            case (is_null($content)):
                return '[]';

            default:
                throw new \InvalidArgumentException('Content passed to okJson method must either array, object, or scalar.');
        }
    }

    /**
     * @return \Zend\View\Model\ConsoleModel|\Zend\View\Model\ViewModel
     */
    public function createNotFoundModel()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        if (($request instanceof Request) && ($response instanceof Response)) {
            return $this->createHttpNotFoundModel($response);
        } else {
            return $this->createConsoleNotFoundModel($response);
        }
    }

    /**
     * @param string $name
     */
    private function protectRequestMethod($name)
    {
        /** @var Request $request */
        $request = $this->getRequest();

        if (!$request instanceof Request) {
            return;
        }

        switch (true) {
            case (substr($name, 0, 3) == 'get' && !$request->isGet()):
            case (substr($name, 0, 3) == 'put' && !$request->isPut()):
            case (substr($name, 0, 4) == 'post' && !$request->isPost()):
            case (substr($name, 0, 6) == 'delete' && !$request->isDelete()):
                throw new MethodNotAllowedException;
        }
    }

    /**
     * @param $service
     * @return mixed
     */
    private function setupServiceInitialization($service)
    {
        if ($service instanceof ServiceLocatorAwareInterface) {
            $service->setServiceLocator($this->getServiceLocator());
        }

        if ($service instanceof ServiceManagerAwareInterface) {
            $service->setServiceManager($this->getServiceManager());
        }

        if ($service instanceof InitializableInterface) {
            $service->init();
        }

        return $service;
    }
}