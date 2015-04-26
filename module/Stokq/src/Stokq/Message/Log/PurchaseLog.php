<?php

namespace Stokq\Message\Log;

use Stokq\Entity\Log;

/**
 * Class PurchaseLog
 * @package Stokq\Message\Log
 */
class PurchaseLog
{
    /**
     * @var Log
     */
    protected $log;

    protected $messages = [
        'create' => '{user_name} membuat daftar belanja baru dengan ID #{resource_id}.',
        'change_status' => '{user_name} mengubah status daftar belanja #{resource_id} dari "{from}" ke "{to}".',
        'update' => '{user_name} meng-update data di daftar belanja #{resource_id}.',
    ];

    /**
     * @param Log $log
     */
    function __construct(Log $log)
    {
        $this->log = $log;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->format();
    }

    /**
     * @return string
     */
    public function format()
    {
        switch ($this->log->getAction()) {

            case 'create':
                return str_replace(
                    ['{user_name}', '{resource_id}'],
                    [$this->log->getUserName(), $this->log->getResourceId()],
                    $this->messages['create']
                );
                break;

            case 'change_status':
                $data = json_decode($this->log->getData(), true);
                return str_replace(
                    ['{user_name}', '{resource_id}', '{from}', '{to}'],
                    [$this->log->getUserName(), $this->log->getResourceId(), $data['from'], $data['to']],
                    $this->messages['change_status']
                );
                break;

            case 'update':
                return str_replace(
                    ['{user_name}', '{resource_id}'],
                    [$this->log->getUserName(), $this->log->getResourceId()],
                    $this->messages['update']
                );
                break;

            default:
                return $this->log->getFormattedMessage();
                break;
        }
    }
}