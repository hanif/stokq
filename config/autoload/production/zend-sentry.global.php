<?php

return [
    'zend-sentry' => [
        'use-module'                        => true,
        'attach-log-listener'               => true,
        'handle-errors'                     => true,
        'call-existing-error-handler'       => true,
        'handle-shutdown-errors'            => true,
        'handle-exceptions'                 => true,
        'call-existing-exception-handler'   => true,
        'display-exceptions'                => false,
        'default-exception-message'         => 'Oh no. Something went wrong, but we have been notified. If you are testing, tell us your eventID: %s',
        'default-exception-console-message' => "Oh no. Something went wrong, but we have been notified.\n",
        'handle-javascript-errors'          => true,
        'raven-config'                      => [],
    ],
];