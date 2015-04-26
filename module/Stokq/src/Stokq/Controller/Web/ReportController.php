<?php

namespace Stokq\Controller\Web;

use Stokq\Controller\AuthenticatedController;

/**
 * Class ReportController
 * @package Stokq\Controller\Web
 */
class ReportController extends AuthenticatedController
{
    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('report');

        return [];
    }
}