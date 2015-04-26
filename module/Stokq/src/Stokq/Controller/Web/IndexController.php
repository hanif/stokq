<?php

namespace Stokq\Controller\Web;

use Stokq\Controller\AuthenticatedController;
use Stokq\Form\HelpForm;
use Zend\Http\PhpEnvironment\Request;

/**
 * Class IndexController
 * @package Stokq\Controller\Web
 */
class IndexController extends AuthenticatedController
{
    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('home');

        return [
            'account' => $this->account(),
            'stats' => $this->getStatsReportService(),
        ];
    }

    /**
     * @param Request $request
     * @return array|\Zend\Http\Response
     * @throws \Exception
     */
    public function helpAction($request)
    {
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('setting');

        $helpForm = $this->autoFilledForm(HelpForm::class);
        $helpForm->populateValues($this->user()->getArrayCopy());

        if ($request->isPost()) {
            if ($formValid = $helpForm->isValid()) {

                $config = $this->service('Config');

                if (is_array($config) && isset($config['slack']['webhook']['help-support'])) {
                    $formData = $helpForm->getData();
                    $data = [
                        'fields' => [
                            [
                                'name'  => 'Name',
                                'value' => $formData['name'],
                                'short' => true,
                            ],
                            [
                                'name'  => 'Email',
                                'value' => $formData['email'],
                                'short' => true,
                            ],
                            [
                                'name'  => 'Contact No.',
                                'value' => $formData['contact_no'],
                                'short' => true,
                            ],
                            [
                                'name'  => 'Type',
                                'value' => $formData['type'],
                                'short' => true,
                            ],
                            [
                                'name'  => 'Severity',
                                'value' => $formData['severity'],
                                'short' => true,
                            ],
                            [
                                'name'  => 'Need Reply?',
                                'value' => $formData['need_reply'],
                                'short' => true,
                            ],
                            [
                                'name'  => 'Message',
                                'value' => $formData['message'],
                                'short' => false,
                            ],
                        ],
                    ];

                    $json = sprintf('payload=%s', json_encode($data));
                    $ch = curl_init($config['slack']['webhook']['help-support']['url']);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($ch);
                    curl_close($ch);

                    $this->flashMessenger()->addSuccessMessage('Terimakasih, pesan Anda telah terkirim.');
                    return $this->redirect()->toRoute(...$this->routeSpec('web.index.help'));
                }

                $this->flashMessenger()->addErrorMessage('Maaf, tidak dapat mengirim pesan Anda saat ini, mohon hubungi admin.');
                return $this->redirect()->toRoute(...$this->routeSpec('web.index.help'));
            }
        }

        return compact('helpForm', 'formValid');
    }
}