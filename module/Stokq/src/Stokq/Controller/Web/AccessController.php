<?php

namespace Stokq\Controller\Web;

use Doctrine\ORM\NoResultException;
use Stokq\Controller\Controller;
use Stokq\Entity\Account;
use Stokq\Entity\Category;
use Stokq\Entity\IngredientType;
use Stokq\Entity\StockUnit;
use Stokq\Entity\StorageType;
use Stokq\Entity\Type;
use Stokq\Entity\UnitType;
use Stokq\Entity\User;
use Stokq\Form\CreatePasswordForm;
use Stokq\Form\FirstRunForm;
use Stokq\Form\LoginForm;
use Stokq\Service\AccountService;
use Stokq\Service\StockService;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Crypt\Password\PasswordInterface;
use Zend\Http\PhpEnvironment\Request;
use Zend\View\Model\ViewModel;

/**
 * Class AccessController
 * @package Stokq\Controller\Web
 */
class AccessController extends Controller
{
    /**
     * @param Request $request
     * @return \Zend\Http\Response|ViewModel
     */
    public function loginAction($request)
    {
        $loginForm = $this->autoFilledForm(LoginForm::class);

        if ($request->isPost()) {
            if ($formValid = $loginForm->isValid()) {
                /** @var AuthenticationService $authService */
                $authService = $this->service(AuthenticationService::class);

                /** @var AdapterInterface $authAdapter */
                $authAdapter = $this->service(AdapterInterface::class);

                $result = $authService->authenticate($authAdapter);

                if ($result->isValid()) {
                    return $this->redirect()->toRoute('home');
                }

                $this->message()->error('User atau password tidak cocok.');
            }
        }

        $model = new ViewModel();
        $model->setTemplate('stokq/web/access/login');
        $model->setVariables(compact('loginForm', 'formValid'));
        $model->setTerminal(true);
        return $model;
    }

    /**
     * @param Request $request
     * @return \Zend\Http\Response|ViewModel
     */
    public function logoutAction($request)
    {
        if ($request->isPost()) {
            /** @var AuthenticationService $authService */
            $authService = $this->service(AuthenticationService::class);

            if ($authService->hasIdentity()) {
                $authService->clearIdentity();
                return $this->redirect()->toRoute(...$this->routeSpec('web.access.login'));
            }
        }

        return $this->notFoundAction();
    }

    /**
     * @param Request $request
     * @return \Zend\Http\Response|ViewModel
     */
    public function activateAction($request)
    {
        try {
            $code  = $this->getRequestQuery('code');
            $token = $this->getUserService()->getToken($code, 'activation');
            $form  = $this->autoFilledForm(CreatePasswordForm::class);
            $user  = $token->getUser();

            if ($request->isPost()) {
                if ($formValid = $form->isValid()) {

                    /** @var PasswordInterface $passwordService */
                    $passwordService = $this->service(PasswordInterface::class);

                    $user->setPassword($passwordService->create($form->get('password')->getValue()));
                    $user->setStatus(User::STATUS_ACTIVE);
                    $user->setPasswordChanged(true);
                    $this->persist($user)->commit();

                    $this->getUserService()->activate($code);

                    $this->flashMessenger()->addSuccessMessage('Akun Anda telah aktif, sialahkan login dengan password yang dipilih.');
                    return $this->redirect()->toRoute(...$this->routeSpec('web.access.login'));
                }
            }

            return compact('form', 'formValid', 'user');

        } catch (NoResultException $e) {
            return $this->notFoundAction();
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function firstRunAction($request)
    {
        $accountMapper = $this->mapper(Account::class);

        if ($accountMapper->count()) {
            return $this->notFoundAction();
        }

        /** @var FirstRunForm $firstRunForm */
        $firstRunForm = $this->autoFilledForm(FirstRunForm::class);

        if ($request->isPost()) {
            if ($formValid = $firstRunForm->isValid()) {
                $this->getAccountMService()->setupFirstRun($accountMapper, $this->mapper(User::class), $firstRunForm);
                $this->getStockService()->createDefaultUnit(
                    $this->mapper(StockUnit::class),
                    $this->getStockService()->createDefaultUnitType($this->mapper(UnitType::class))
                );
                $this->getStockService()->createDefaultCategory($this->mapper(Category::class));
                $this->getStockService()->createDefaultStorageType($this->mapper(StorageType::class));
                $this->getMenuService()->createDefaultIngredientType($this->mapper(IngredientType::class));
                $this->getMenuService()->createDefaultMenuType($this->mapper(Type::class));
                $this->commit();

                $this->flashMessenger()->addSuccessMessage('Akun anda telah dibuat, silahkan login dengan user & password yang dipilih.');
                return $this->redirect()->toRoute(...$this->routeSpec('web.access.login'));
            }
        }

        $model = new ViewModel();
        $model->setTemplate('stokq/web/access/first-run');
        $model->setVariables(compact('firstRunForm', 'formValid'));
        $model->setTerminal(true);
        return $model;
    }
}