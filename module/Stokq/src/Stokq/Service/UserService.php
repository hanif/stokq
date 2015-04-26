<?php

namespace Stokq\Service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Stokq\Entity\Token;
use Stokq\Entity\User;
use Stokq\Stdlib\DataMapper;
use Zend\Math\Rand;

/**
 * Class UserService
 * @package Stokq\Service
 */
class UserService extends AbstractService
{
    const DEFAULT_APP_MAILER = 'notification@stokq.com';

    /**
     * @param DataMapper $mapper
     * @param User $user
     * @param string $type
     * @return Token
     */
    public function createToken(DataMapper $mapper, User $user, $type)
    {
        $this->assert($mapper->getEntityClass() == Token::class);

        $code = preg_replace('/[^a-zA-Z0-9\_\-]+/', null, Rand::getString(100));
        return $mapper->create([
            'user' => $user,
            'code' => sprintf('%d.%d.%s', $user->getId(), time(), $code),
            'type' => $type,
        ]);
    }

    /**
     * @param User $user
     * @param Token $token
     * @return \Zend\Mail\Message
     */
    public function createActivationEmailMessage(User $user, Token $token)
    {
        /** @var MailerService $mailer */
        $mailer = $this->getServiceLocator()->get(MailerService::class);

        $helper = $this->getServiceLocator()->get('ViewHelperManager');
        $urlHelper = $helper->get('url');

        $activationUrl = $urlHelper('web', [
            'controller' => 'access',
            'action'     => 'activate',
        ], [
            'force_canonical' => true,
            'query' => ['code' => $token->getCode()],
        ]);

        $message = $mailer->createHtmlMessage(sprintf(
            '<p>Halo %s,</p>'.
            '<p>Silahkan <a href="%s">klik disini</a> untuk mengaktifkan akun Anda.</p>',
            $user->getName(),
            $activationUrl
        ));

        $config = $this->getServiceLocator()->get('ApplicationConfig');
        $appMailer = isset($config['app_mailer']) ? $config['app_mailer'] : self::DEFAULT_APP_MAILER;

        $message->setFrom($appMailer);
        $message->setTo($user->getEmail());
        $message->setSubject('Aktivasi Akun StokQ');
        return $message;
    }

    /**
     * @param string $code
     * @return bool
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function activate($code)
    {
        try {
            $token = $this->getToken($code, 'activation');
            $expiration = $token->getExpiredAt();

            $query = $this->em()->createQuery("delete" . " from ent:Token t where t.id = :id");
            $query->setParameter('id', $token->getId());
            $query->execute();

            if ($expiration && ($expiration < new \DateTime())) {
                return false;
            }

            return true;
        } catch (NoResultException $e) {
            return false;
        } catch (NonUniqueResultException $e) {
            return false;
        }
    }

    /**
     * @param $code
     * @param $type
     * @return Token
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getToken($code, $type)
    {
        $parts = $this->getTokenParts($code);
        $dql = "select" . " t from ent:Token t where t.code = :code and t.user = :user and t.type = :type";
        $query = $this->em()->createQuery($dql);
        $query->setParameters([
            'code' => $parts['code'],
            'user' => $parts['user'],
            'type' => $type,
        ]);

        return $query->getSingleResult();
    }

    /**
     * @param $code
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function getTokenParts($code)
    {
        list($user, $time, $rand) = explode('.', $code, 3) + [null, null, null];
        $user = $this->em()->getReference(User::class, $user);
        return compact('user', 'time', 'rand', 'code');
    }
}