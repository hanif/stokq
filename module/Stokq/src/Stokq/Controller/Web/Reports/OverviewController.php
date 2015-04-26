<?php

namespace Stokq\Controller\Web\Reports;

use Stokq\Controller\AuthenticatedController;

/**
 * Class OverviewController
 * @package Stokq\Controller\Web\Reports
 */
class OverviewController extends AuthenticatedController
{
    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('report');

        return [
            'statsService' => $this->getStatsReportService(),
            'account' => $this->account()
        ];
    }
}