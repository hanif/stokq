<?php

namespace Stokq\Controller\Web;

use Stokq\Controller\AuthenticatedController;
use Stokq\Form\AccountForm;
use Stokq\Form\ChangePasswordForm;
use Zend\Crypt\Password\PasswordInterface;
use Zend\Http\PhpEnvironment\Request;

/**
 * Class SettingController
 * @package Stokq\Controller\Web
 */
class SettingController extends AuthenticatedController
{
    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        return $this->redirect()->toRoute(...$this->routeSpec('web.setting.general'));
    }

    /**
     * @param Request $request
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function generalAction($request)
    {
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('setting');

        $account = $this->account();
        $accountForm = $this->autoFilledForm(AccountForm::class);
        $accountForm->populateValues($account->getArrayCopy());

        if ($request->isPost()) {
            if ($formValid = $accountForm->isValid()) {
                $account->exchangeArray($accountForm->getData());
                $this->persist($account)->commit();
                $this->flashMessenger()->addSuccessMessage('Setting telah disimpan.');
                return $this->redirect()->toRoute(...$this->routeSpec('web.setting.general'));
            }
        }

        return compact('accountForm', 'formValid');
    }

    /**
     * @param Request $request
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function changePasswordAction($request)
    {
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('setting');

        $changePasswordForm = $this->autoFilledForm(ChangePasswordForm::class);
        if ($request->isPost()) {
            if ($formValid = $changePasswordForm->isValid()) {

                /** @var PasswordInterface $passwordService */
                $passwordService = $this->service(PasswordInterface::class);
                $data = $changePasswordForm->getData();

                if ($passwordService->verify($data['old_password'], $this->user()->getPassword())) {
                    $user = $this->user();
                    $user->setPassword($passwordService->create($data['new_password']));
                    $this->persist($user)->commit();
                    $this->flashMessenger()->addSuccessMessage('Password yang baru telah di simpan.');
                    return $this->redirect()->toRoute(...$this->routeSpec('web.setting.change-password'));
                }

                $this->flashMessenger()->addErrorMessage('Password yang lama tidak cocok.');
                return $this->redirect()->toRoute(...$this->routeSpec('web.setting.change-password'));
            }
        }

        return compact('changePasswordForm', 'formValid');
    }

}