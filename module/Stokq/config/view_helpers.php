<?php

use Stokq\View\Helper\Config;
use Stokq\View\Helper\FlashMessage;
use Stokq\View\Helper\Form;
use Stokq\View\Helper\Message;
use Stokq\View\Helper\Nav\Nav;
use Stokq\View\Helper\Pagination;
use Stokq\View\Helper\User;

return [
    'invokables' => [
        'pagination' => Pagination::class,
        'message'    => Message::class,
        'config'     => Config::class,
        'flash'      => FlashMessage::class,
        'form'       => Form::class,
        'user'       => User::class,
        'nav'        => Nav::class,
    ],
];