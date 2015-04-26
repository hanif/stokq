<?php

namespace Stokq\Service;

use Stokq\Entity\Account;
use Stokq\Entity\User;
use Stokq\Form\FirstRunForm;
use Stokq\Stdlib\DataMapper;
use Zend\Crypt\Password\PasswordInterface;

/**
 * Class AccountService
 * @package Stokq\Service
 */
class AccountService extends AbstractService
{
    /**
     * @param DataMapper $accountMapper
     * @param DataMapper $userMapper
     * @param FirstRunForm $form
     * @return Account
     */
    public function setupFirstRun(DataMapper $accountMapper, DataMapper $userMapper, FirstRunForm $form)
    {
        $this->assert($accountMapper->getEntityClass() == Account::class);
        $this->assert($userMapper->getEntityClass() == User::class);

        /** @var PasswordInterface $hasher */
        $hasher = $this->getServiceLocator()->get(PasswordInterface::class);
        $data = $form->getData();

        $user = $userMapper->create([
            'name' => $data['user_name'],
            'email' => $data['user_email'],
            'password_changed' => true,
            'password' => $hasher->create($data['password']),
            'status' => User::STATUS_ACTIVE,
        ]);

        /** @var Account $account */
        $account = $accountMapper->getHydrator()->hydrate($data, new Account());
        $account->setOwner($user);
        $account->setBillingUser($user);
        $accountMapper->create($account);
        return $account;
    }

    /**
     * @param DataMapper $mapper
     * @return Account
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getAccount(DataMapper $mapper)
    {
        $this->assert($mapper->getEntityClass() == Account::class);
        return $mapper->one(1);
    }
}