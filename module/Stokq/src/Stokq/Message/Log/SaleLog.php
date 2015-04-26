<?php

namespace Stokq\Message\Log;

use Stokq\Entity\Log;

/**
 * Class SaleLog
 * @package Stokq\Message\Log
 */
class SaleLog
{
    /**
     * @var Log
     */
    protected $log;

    protected $messages = [
        'create' => '{user_name} membuat daftar penjualan baru dengan ID #{resource_id}.',
        'update' => '{user_name} meng-update data daftar penjualan #{resource_id}.',
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