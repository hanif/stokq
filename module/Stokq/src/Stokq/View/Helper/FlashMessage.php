<?php

namespace Stokq\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\FlashMessenger;

/**
 * Class FlashMessage
 * @package Stokq\View\Helper
 */
class FlashMessage extends AbstractHelper
{
    /**
     * @return string
     */
    public function __invoke()
    {
        if (method_exists($this->view, 'plugin')) {
            /** @var FlashMessenger $messengerPlugin */
            $messengerPlugin = $this->view->plugin('flashMessenger');
            $messengerPlugin->setMessageOpenFormat('<div %s>
             <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                 &times;
             </button>
             <ul><li>'
            )
                ->setMessageSeparatorString('</li><li>')
                ->setMessageCloseString('</li></ul></div>');

            $messages = [];
            $types = ['success', 'danger', 'info', 'warning', ['error' => 'danger']];
            foreach ($types as $type) {

                $key = $type;
                if (is_array($type)) {
                    list($key, $type) = each($type);
                }

                $messages[] = $messengerPlugin->render($key, [
                    'alert',
                    'main-alert',
                    'alert-dismissable',
                    sprintf('alert-%s', $type)
                ]);
            }

            return join("\n", $messages);
        }

        return '';
    }
}