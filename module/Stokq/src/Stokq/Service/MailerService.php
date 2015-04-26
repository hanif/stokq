<?php

namespace Stokq\Service;

use Zend\Mail\Exception\RuntimeException;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Class MailerService
 * @package Stokq\Service
 */
class MailerService extends AbstractService
{
    /**
     * @var TransportInterface
     */
    protected $transport;

    /**
     * @param string $html
     * @param Message $scope
     * @return Message
     */
    public function createHtmlMessage($html, Message $scope = null)
    {
        $part = new Part($html);
        $part->setType('text/html');

        if (!$scope) {
            $scope = new Message();
        }

        $mime = new MimeMessage();
        $mime->setParts([$part]);

        $scope->setBody($mime);
        return $scope;
    }

    /**
     * @param Message $message
     */
    public function send(Message $message)
    {
        try {
            if (!$this->transport) {
                try {
                    $this->transport = $this->getServiceLocator()->get(TransportInterface::class);
                } catch (ServiceNotFoundException $ex) {
                    $this->transport = new Sendmail();
                }
            }

            $this->transport->send($message);
        } catch (RuntimeException $e) {
            $this->logger()->error($e->getMessage(), $e->getTrace());
        }
    }
}