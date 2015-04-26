<?php

namespace Stokq\View\Helper;

use Zend\Filter\Word\CamelCaseToDash;
use Zend\Mvc\Application;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\Url;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\View\Helper\AbstractHelper;

/**
 * Class Pagination
 * @package Stokq\View\Helper
 */
class Pagination extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface|AbstractPluginManager
     */
    protected $serviceLocator;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var string
     */
    protected $template = 'partial/pagination';

    /**
     * @var string
     */
    protected $type = 'Sliding';

    /**
     * @var string
     */
    protected $paramType = 'query';

    /**
     * @var string
     */
    protected $paramName = 'page';

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @var CamelCaseToDash
     */
    protected $camelCaseToDashFilter;

    /**
     * @return string
     */
    public function __toString()
    {
        $pages = $this->paginator->getPages($this->type);
        $routeMatch = $this->getApp()->getMvcEvent()->getRouteMatch();

        $routeName = $routeMatch->getMatchedRouteName();
        $params = $routeMatch->getParams();
        $paramName = $this->paramName;
        $paramType = $this->paramType;

        $url = function($page) use($routeName, $params, $paramName, $paramType)
        {
            if (method_exists($this->view, 'plugin')) {
                /** @var Url $url */
                $url = $this->view->plugin('url');

                $query = $_GET;
                if ($paramType == 'query') {
                    $query[$paramName] = $page;
                } else if ($paramName == 'route') {
                    $params[$paramName] = $page;
                } else {
                    throw new \RuntimeException('Currently only `query` and `route` param type supported by pagination helper.');
                }

                // filter controller name:
                // transforms e.g. '\Some\Module\IndexController' into just 'index'
                if (isset($params['controller']) && isset($params['__CONTROLLER__'])) {
                    $params['controller'] = $params['__CONTROLLER__'];
                } else if (isset($params['controller']) && strpos($params['controller'], '\\') !== false) {
                    $parts = array_reverse(explode('\\', $params['controller']));
                    $filteredParts = [];
                    foreach ($parts as $part) {
                        if ((strtolower($part) == 'controller') || (strtolower($part) == 'controllers')) {
                            break;
                        }

                        $tmp = $part;
                        if (substr($part, -10) == 'Controller') {
                            $tmp = substr($part, 0, -10);
                        }
                        array_unshift($filteredParts, strtolower($this->getCamelCaseToDashFilter()->filter($tmp)));
                    }
                    $params['controller'] = join('/', $filteredParts);
                }

                return $url($routeName, $params, compact('query'));
            }

            throw new \Exception('Could not resolve URL helper.');
        };

        $viewModel = new ViewModel(compact('pages', 'url'));
        $viewModel->setTemplate($this->template);

        return $this->getView()->render($viewModel);
    }

    /**
     * @param Paginator $paginator
     * @param string $paramName
     * @param string $paramType
     * @return string
     */
    public function __invoke(Paginator $paginator, $paramName = 'page', $paramType = 'query')
    {
        $this->paginator = $paginator;
        $this->paramName = $paramName;
        $this->paramType = $paramType;
        return $this;
    }

    /**
     * @return CamelCaseToDash
     */
    public function getCamelCaseToDashFilter()
    {
        if (!$this->camelCaseToDashFilter) {
            $this->camelCaseToDashFilter = new CamelCaseToDash();
        }

        return $this->camelCaseToDashFilter;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface|AbstractPluginManager $serviceLocator
     * @throws \InvalidArgumentException
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $this->serviceLocator = $serviceLocator;
            $this->serviceManager = $serviceLocator->getServiceLocator();
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Arg. passed to %s::setServiceLocator() must be instance of %s.',
                static::class, AbstractPluginManager::class
            ));
        }
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        if (!$this->serviceManager) {
            $this->serviceManager = $this->serviceLocator->getServiceLocator();
        }
        return $this->serviceManager;
    }

    /**
     * @return Application
     */
    public function getApp()
    {
        /** @var Application $app */
        return $this->getServiceManager()->get('Application');
    }
}
