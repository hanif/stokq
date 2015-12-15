
Note: set environment variable `ENV=dev` first before running, default is `production`.
For production, you must have `redis-server` running on port 6379

---

For development:

[1] Create `/config/autoload/development/db.local.php`.
```php
return [
    'db' => [
        'driver'         => 'Pdo', // or any other driver supported by Doctrine ORM
        'host'           => 'Your DB host',
        'port'           => 'Your DB port',
        'username'       => 'Your DB user',
        'password'       => 'Your DB pass',
        'dbname'         => 'Your DB name',
        'profiler'       => true,
        'options'        => [
                                'buffer_results' => false,
                            ],
        'driver_options' => [
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                            ],
    ],
];
```


[2] Create `/config/autoload/development/zdt.local.php`.
```php
return [
    'zenddevelopertools' => [
        'profiler' => [
            'enabled'       => true,
            'strict'        => true,
            'flush_early'   => false,
            'cache_dir'     => './data/cache',
            'matcher'       => [],
            'collectors'    => []
        ],
        'events' => [
            'enabled'       => false,
            'collectors'    => [],
            'identifiers'   => []
        ],
        'toolbar' => [
            'enabled'       => true,
            'auto_hide'     => false,
            'position'      => 'bottom',
            'version_check' => false,
            'entries'       => []
        ]
    ]
];
```

---

For production:

[1] Copy `db.local.php` from `/config/autoload/development/db.local.php`, and set `profiler` to `false`.

[2] Create `/config/autoload/production/slack.local.php`.
```php
return [
    'slack' => [
        'webhook' => [
            'help-support' => [
                'url' => 'Your Slack Webhook URL'
            ],
        ],
    ],
];
```

[3] Create `/config/autoload/production/slm_mail.mailgun.local.php`.
```php
return [
    'slm_mail' => [
        'mailgun' => [
            'domain' => 'Your Mailgun Domain',
            'key' => 'Your Mailgun Key',
        ],
    ]
];
```

[4] Create `/config/autoload/production/zend-sentry.local.php`.
```php
return [
    'zend-sentry' => [
        'sentry-api-key' => 'Your Sentry URL',
        'sentry-api-key-js' => 'Your Sentry URL without pass',
    ],
];
```