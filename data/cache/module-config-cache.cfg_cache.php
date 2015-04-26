<?php
return array (
  'router' => 
  array (
    'routes' => 
    array (
      'home' => 
      array (
        'type' => 'literal',
        'options' => 
        array (
          'route' => '/',
          'defaults' => 
          array (
            '__NAMESPACE__' => 'Stokq\\Controller\\Web',
            'controller' => 'Index',
            'action' => 'index',
          ),
        ),
      ),
      'web' => 
      array (
        'type' => 'segment',
        'options' => 
        array (
          'route' => '/:controller[/:action]',
          'defaults' => 
          array (
            '__NAMESPACE__' => 'Stokq\\Controller\\Web',
            'controller' => 'index',
            'action' => 'index',
          ),
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
        ),
      ),
      'report' => 
      array (
        'type' => 'segment',
        'options' => 
        array (
          'route' => '/reports/:controller[/:action]',
          'defaults' => 
          array (
            '__NAMESPACE__' => 'Stokq\\Controller\\Web\\Reports',
            'controller' => 'index',
            'action' => 'index',
          ),
          'constraints' => 
          array (
            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
        ),
      ),
      'doctrine_orm_module_yuml' => 
      array (
        'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/ocra_service_manager_yuml',
          'defaults' => 
          array (
            'controller' => 'DoctrineORMModule\\Yuml\\YumlController',
            'action' => 'index',
          ),
        ),
      ),
    ),
  ),
  'authentication' => 
  array (
    'doctrine_orm' => 
    array (
      'entity_class' => 'Stokq\\Entity\\User',
      'identity_field' => 'email',
      'credentials_field' => 'password',
      'identity_input' => 'email',
      'credentials_input' => 'password',
      'session_name' => 'auth',
    ),
  ),
  'service_manager' => 
  array (
    'abstract_factories' => 
    array (
      0 => 'Stokq\\Factory\\ServiceAbstractFactory',
      'DoctrineModule' => 'DoctrineModule\\ServiceFactory\\AbstractDoctrineServiceFactory',
    ),
    'aliases' => 
    array (
      'Zend\\Session\\SessionManager' => 'session_manager',
      'Zend\\Crypt\\Password\\PasswordInterface' => 'auth_password',
      'Zend\\Authentication\\AuthenticationService' => 'auth_service',
      'Zend\\Authentication\\Adapter\\AdapterInterface' => 'auth_adapter',
      'Stokq\\Stdlib\\ExceptionHandler' => 'exception_handler',
      'Stokq\\Stdlib\\ViewMessage' => 'view_message',
      'Zend\\Mail\\Transport\\TransportInterface' => 'mail_transport',
    ),
    'factories' => 
    array (
      'logger' => 'Stokq\\Factory\\MonologFactory',
      'session_manager' => 'Stokq\\Factory\\SessionManagerFactory',
      'sidebar_nav' => 'Stokq\\Factory\\Nav\\SidebarNavFactory',
      'exception_handler' => 'Stokq\\Factory\\ExceptionHandlerFactory',
      'view_message' => 'Stokq\\Factory\\ViewMessageFactory',
      'mail_transport' => 'Stokq\\Factory\\MailTransportFactory',
      'file_cache' => 'Stokq\\Factory\\Cache\\FileCacheFactory',
      'redis' => 'Stokq\\Factory\\Cache\\RedisFactory',
      'memcache' => 'Stokq\\Factory\\Cache\\MemcacheFactory',
      'doctrine.cache.redis_cache' => 'Stokq\\Factory\\Cache\\DoctrineRedisFactory',
      'doctrine.cache.memcache_cache' => 'Stokq\\Factory\\Cache\\DoctrineMemcacheFactory',
      'auth_password' => 'Stokq\\Factory\\Auth\\PasswordFactory',
      'auth_storage' => 'Stokq\\Factory\\Auth\\StorageFactory',
      'auth_service' => 'Stokq\\Factory\\Auth\\ServiceFactory',
      'auth_adapter' => 'Stokq\\Factory\\Auth\\AdapterFactory',
      'doctrine.cli' => 'DoctrineModule\\Service\\CliFactory',
      'Doctrine\\ORM\\EntityManager' => 'DoctrineORMModule\\Service\\EntityManagerAliasCompatFactory',
      'SlmMail\\Mail\\Transport\\ElasticEmailTransport' => 'SlmMail\\Factory\\ElasticEmailTransportFactory',
      'SlmMail\\Mail\\Transport\\MailgunTransport' => 'SlmMail\\Factory\\MailgunTransportFactory',
      'SlmMail\\Mail\\Transport\\MandrillTransport' => 'SlmMail\\Factory\\MandrillTransportFactory',
      'SlmMail\\Mail\\Transport\\PostageTransport' => 'SlmMail\\Factory\\PostageTransportFactory',
      'SlmMail\\Mail\\Transport\\PostmarkTransport' => 'SlmMail\\Factory\\PostmarkTransportFactory',
      'SlmMail\\Mail\\Transport\\SendGridTransport' => 'SlmMail\\Factory\\SendGridTransportFactory',
      'SlmMail\\Mail\\Transport\\SesTransport' => 'SlmMail\\Factory\\SesTransportFactory',
      'SlmMail\\Service\\ElasticEmailService' => 'SlmMail\\Factory\\ElasticEmailServiceFactory',
      'SlmMail\\Service\\MailgunService' => 'SlmMail\\Factory\\MailgunServiceFactory',
      'SlmMail\\Service\\MandrillService' => 'SlmMail\\Factory\\MandrillServiceFactory',
      'SlmMail\\Service\\PostageService' => 'SlmMail\\Factory\\PostageServiceFactory',
      'SlmMail\\Service\\PostmarkService' => 'SlmMail\\Factory\\PostmarkServiceFactory',
      'SlmMail\\Service\\SendGridService' => 'SlmMail\\Factory\\SendGridServiceFactory',
      'SlmMail\\Service\\SesService' => 'SlmMail\\Factory\\SesServiceFactory',
      'SlmMail\\Http\\Client' => 'SlmMail\\Factory\\HttpClientFactory',
    ),
    'invokables' => 
    array (
      'DoctrineModule\\Authentication\\Storage\\Session' => 'Zend\\Authentication\\Storage\\Session',
      'doctrine.dbal_cmd.runsql' => '\\Doctrine\\DBAL\\Tools\\Console\\Command\\RunSqlCommand',
      'doctrine.dbal_cmd.import' => '\\Doctrine\\DBAL\\Tools\\Console\\Command\\ImportCommand',
      'doctrine.orm_cmd.clear_cache_metadata' => '\\Doctrine\\ORM\\Tools\\Console\\Command\\ClearCache\\MetadataCommand',
      'doctrine.orm_cmd.clear_cache_result' => '\\Doctrine\\ORM\\Tools\\Console\\Command\\ClearCache\\ResultCommand',
      'doctrine.orm_cmd.clear_cache_query' => '\\Doctrine\\ORM\\Tools\\Console\\Command\\ClearCache\\QueryCommand',
      'doctrine.orm_cmd.schema_tool_create' => '\\Doctrine\\ORM\\Tools\\Console\\Command\\SchemaTool\\CreateCommand',
      'doctrine.orm_cmd.schema_tool_update' => '\\Doctrine\\ORM\\Tools\\Console\\Command\\SchemaTool\\UpdateCommand',
      'doctrine.orm_cmd.schema_tool_drop' => '\\Doctrine\\ORM\\Tools\\Console\\Command\\SchemaTool\\DropCommand',
      'doctrine.orm_cmd.convert_d1_schema' => '\\Doctrine\\ORM\\Tools\\Console\\Command\\ConvertDoctrine1SchemaCommand',
      'doctrine.orm_cmd.generate_entities' => '\\Doctrine\\ORM\\Tools\\Console\\Command\\GenerateEntitiesCommand',
      'doctrine.orm_cmd.generate_proxies' => '\\Doctrine\\ORM\\Tools\\Console\\Command\\GenerateProxiesCommand',
      'doctrine.orm_cmd.convert_mapping' => '\\Doctrine\\ORM\\Tools\\Console\\Command\\ConvertMappingCommand',
      'doctrine.orm_cmd.run_dql' => '\\Doctrine\\ORM\\Tools\\Console\\Command\\RunDqlCommand',
      'doctrine.orm_cmd.validate_schema' => '\\Doctrine\\ORM\\Tools\\Console\\Command\\ValidateSchemaCommand',
      'doctrine.orm_cmd.info' => '\\Doctrine\\ORM\\Tools\\Console\\Command\\InfoCommand',
      'doctrine.orm_cmd.ensure_production_settings' => '\\Doctrine\\ORM\\Tools\\Console\\Command\\EnsureProductionSettingsCommand',
      'doctrine.orm_cmd.generate_repositories' => '\\Doctrine\\ORM\\Tools\\Console\\Command\\GenerateRepositoriesCommand',
    ),
  ),
  'controllers' => 
  array (
    'invokables' => 
    array (
      'Stokq\\Controller\\Web\\Access' => 'Stokq\\Controller\\Web\\AccessController',
      'Stokq\\Controller\\Web\\Category' => 'Stokq\\Controller\\Web\\CategoryController',
      'Stokq\\Controller\\Web\\Classification' => 'Stokq\\Controller\\Web\\ClassificationController',
      'Stokq\\Controller\\Web\\Index' => 'Stokq\\Controller\\Web\\IndexController',
      'Stokq\\Controller\\Web\\Ingredient' => 'Stokq\\Controller\\Web\\IngredientController',
      'Stokq\\Controller\\Web\\Menu' => 'Stokq\\Controller\\Web\\MenuController',
      'Stokq\\Controller\\Web\\MenuPrice' => 'Stokq\\Controller\\Web\\MenuPriceController',
      'Stokq\\Controller\\Web\\Outlet' => 'Stokq\\Controller\\Web\\OutletController',
      'Stokq\\Controller\\Web\\Purchase' => 'Stokq\\Controller\\Web\\PurchaseController',
      'Stokq\\Controller\\Web\\Report' => 'Stokq\\Controller\\Web\\ReportController',
      'Stokq\\Controller\\Web\\Sale' => 'Stokq\\Controller\\Web\\SaleController',
      'Stokq\\Controller\\Web\\Setting' => 'Stokq\\Controller\\Web\\SettingController',
      'Stokq\\Controller\\Web\\Stock' => 'Stokq\\Controller\\Web\\StockController',
      'Stokq\\Controller\\Web\\StockItem' => 'Stokq\\Controller\\Web\\StockItemController',
      'Stokq\\Controller\\Web\\StockUnit' => 'Stokq\\Controller\\Web\\StockUnitController',
      'Stokq\\Controller\\Web\\StorageType' => 'Stokq\\Controller\\Web\\StorageTypeController',
      'Stokq\\Controller\\Web\\Supplier' => 'Stokq\\Controller\\Web\\SupplierController',
      'Stokq\\Controller\\Web\\Type' => 'Stokq\\Controller\\Web\\TypeController',
      'Stokq\\Controller\\Web\\User' => 'Stokq\\Controller\\Web\\UserController',
      'Stokq\\Controller\\Web\\Warehouse' => 'Stokq\\Controller\\Web\\WarehouseController',
      'Stokq\\Controller\\Web\\Reports\\CashFlow' => 'Stokq\\Controller\\Web\\Reports\\CashFlowController',
      'Stokq\\Controller\\Web\\Reports\\Menu' => 'Stokq\\Controller\\Web\\Reports\\MenuController',
      'Stokq\\Controller\\Web\\Reports\\Outlet' => 'Stokq\\Controller\\Web\\Reports\\OutletController',
      'Stokq\\Controller\\Web\\Reports\\Overview' => 'Stokq\\Controller\\Web\\Reports\\OverviewController',
      'Stokq\\Controller\\Web\\Reports\\Purchase' => 'Stokq\\Controller\\Web\\Reports\\PurchaseController',
      'Stokq\\Controller\\Web\\Reports\\Sale' => 'Stokq\\Controller\\Web\\Reports\\SaleController',
    ),
    'factories' => 
    array (
      'DoctrineModule\\Controller\\Cli' => 'DoctrineModule\\Service\\CliControllerFactory',
    ),
  ),
  'controller_plugins' => 
  array (
    'invokables' => 
    array (
      'message' => 'Stokq\\Controller\\Plugin\\Message',
    ),
  ),
  'view_manager' => 
  array (
    'doctype' => 'HTML5',
    'not_found_template' => 'error/not-found',
    'exception_template' => 'error/exception',
    'template_map' => 
    array (
      'layout/layout' => '/Users/hanif/Workspace/stokq-com/module/Stokq/config/../view/layout/default.phtml',
      'layout/blank' => '/Users/hanif/Workspace/stokq-com/module/Stokq/config/../view/layout/blank.phtml',
      'layout/default' => '/Users/hanif/Workspace/stokq-com/module/Stokq/config/../view/layout/default.phtml',
      'layout/single-column' => '/Users/hanif/Workspace/stokq-com/module/Stokq/config/../view/layout/single-column.phtml',
      'layout/list-detail' => '/Users/hanif/Workspace/stokq-com/module/Stokq/config/../view/layout/list-detail.phtml',
      'error/not-found' => '/Users/hanif/Workspace/stokq-com/module/Stokq/config/../view/error/not-found.phtml',
      'error/exception' => '/Users/hanif/Workspace/stokq-com/module/Stokq/config/../view/error/exception.phtml',
      'zend-developer-tools/toolbar/doctrine-orm-queries' => '/Users/hanif/Workspace/stokq-com/vendor/doctrine/doctrine-orm-module/config/../view/zend-developer-tools/toolbar/doctrine-orm-queries.phtml',
      'zend-developer-tools/toolbar/doctrine-orm-mappings' => '/Users/hanif/Workspace/stokq-com/vendor/doctrine/doctrine-orm-module/config/../view/zend-developer-tools/toolbar/doctrine-orm-mappings.phtml',
    ),
    'template_path_stack' => 
    array (
      0 => '/Users/hanif/Workspace/stokq-com/module/Stokq/config/../view',
    ),
    'display_not_found_reason' => false,
    'display_exceptions' => false,
  ),
  'view_helpers' => 
  array (
    'invokables' => 
    array (
      'pagination' => 'Stokq\\View\\Helper\\Pagination',
      'message' => 'Stokq\\View\\Helper\\Message',
      'config' => 'Stokq\\View\\Helper\\Config',
      'flash' => 'Stokq\\View\\Helper\\FlashMessage',
      'form' => 'Stokq\\View\\Helper\\Form',
      'user' => 'Stokq\\View\\Helper\\User',
      'nav' => 'Stokq\\View\\Helper\\Nav\\Nav',
    ),
  ),
  'doctrine' => 
  array (
    'configuration' => 
    array (
      'orm_default' => 
      array (
        'entity_namespaces' => 
        array (
          '' => 'Stokq\\Entity',
          'c' => 'Stokq\\Entity',
          'core' => 'Stokq\\Entity',
          'ent' => 'Stokq\\Entity',
          'dto' => 'Stokq\\DTO',
        ),
        'metadata_cache' => 'redis_cache',
        'query_cache' => 'redis_cache',
        'result_cache' => 'redis_cache',
        'hydration_cache' => 'redis_cache',
        'driver' => 'orm_default',
        'generate_proxies' => true,
        'proxy_dir' => './data/proxy',
        'proxy_namespace' => 'DoctrineORMModule\\Proxy',
        'filters' => 
        array (
        ),
        'datetime_functions' => 
        array (
        ),
        'string_functions' => 
        array (
        ),
        'numeric_functions' => 
        array (
        ),
        'types' => 
        array (
        ),
      ),
    ),
    'driver' => 
    array (
      'annotation' => 
      array (
        'paths' => 
        array (
          0 => '/Users/hanif/Workspace/stokq-com/module/Stokq/src/Stokq/Entity',
        ),
        'class' => 'Doctrine\\ORM\\Mapping\\Driver\\AnnotationDriver',
        'cache' => 'redis_cache',
      ),
      'orm_default' => 
      array (
        'drivers' => 
        array (
          'Stokq\\Entity' => 'annotation',
        ),
        'class' => 'Doctrine\\ORM\\Mapping\\Driver\\DriverChain',
      ),
    ),
    'cache' => 
    array (
      'apc' => 
      array (
        'class' => 'Doctrine\\Common\\Cache\\ApcCache',
        'namespace' => 'DoctrineModule',
      ),
      'array' => 
      array (
        'class' => 'Doctrine\\Common\\Cache\\ArrayCache',
        'namespace' => 'DoctrineModule',
      ),
      'filesystem' => 
      array (
        'class' => 'Doctrine\\Common\\Cache\\FilesystemCache',
        'directory' => 'data/DoctrineModule/cache',
        'namespace' => 'DoctrineModule',
      ),
      'memcache' => 
      array (
        'class' => 'Doctrine\\Common\\Cache\\MemcacheCache',
        'instance' => 'my_memcache_alias',
        'namespace' => 'DoctrineModule',
      ),
      'memcached' => 
      array (
        'class' => 'Doctrine\\Common\\Cache\\MemcachedCache',
        'instance' => 'my_memcached_alias',
        'namespace' => 'DoctrineModule',
      ),
      'redis' => 
      array (
        'class' => 'Doctrine\\Common\\Cache\\RedisCache',
        'instance' => 'my_redis_alias',
        'namespace' => 'DoctrineModule',
      ),
      'wincache' => 
      array (
        'class' => 'Doctrine\\Common\\Cache\\WinCacheCache',
        'namespace' => 'DoctrineModule',
      ),
      'xcache' => 
      array (
        'class' => 'Doctrine\\Common\\Cache\\XcacheCache',
        'namespace' => 'DoctrineModule',
      ),
      'zenddata' => 
      array (
        'class' => 'Doctrine\\Common\\Cache\\ZendDataCache',
        'namespace' => 'DoctrineModule',
      ),
    ),
    'authentication' => 
    array (
      'odm_default' => 
      array (
      ),
      'orm_default' => 
      array (
        'objectManager' => 'doctrine.entitymanager.orm_default',
      ),
    ),
    'authenticationadapter' => 
    array (
      'odm_default' => true,
      'orm_default' => true,
    ),
    'authenticationstorage' => 
    array (
      'odm_default' => true,
      'orm_default' => true,
    ),
    'authenticationservice' => 
    array (
      'odm_default' => true,
      'orm_default' => true,
    ),
    'connection' => 
    array (
      'orm_default' => 
      array (
        'configuration' => 'orm_default',
        'eventmanager' => 'orm_default',
        'params' => 
        array (
          'host' => '127.0.0.1',
          'port' => 3306,
          'user' => 'root',
          'password' => '',
          'dbname' => 'stokq',
        ),
        'driverClass' => 'Doctrine\\DBAL\\Driver\\PDOMySql\\Driver',
        'doctrine_type_mappings' => 
        array (
          'enum' => 'string',
        ),
      ),
    ),
    'entitymanager' => 
    array (
      'orm_default' => 
      array (
        'connection' => 'orm_default',
        'configuration' => 'orm_default',
      ),
    ),
    'eventmanager' => 
    array (
      'orm_default' => 
      array (
        'subscribers' => 
        array (
          0 => 'Gedmo\\Sortable\\SortableListener',
          1 => 'Gedmo\\Timestampable\\TimestampableListener',
        ),
      ),
    ),
    'sql_logger_collector' => 
    array (
      'orm_default' => 
      array (
      ),
    ),
    'mapping_collector' => 
    array (
      'orm_default' => 
      array (
      ),
    ),
    'formannotationbuilder' => 
    array (
      'orm_default' => 
      array (
      ),
    ),
    'entity_resolver' => 
    array (
      'orm_default' => 
      array (
      ),
    ),
    'migrations_configuration' => 
    array (
      'orm_default' => 
      array (
        'directory' => './data/migrations',
        'name' => 'Doctrine ORM Migrations',
        'namespace' => 'DoctrineORMModule\\Migrations',
        'table' => 'orm_migrations',
      ),
    ),
    'migrations_cmd' => 
    array (
      'generate' => 
      array (
      ),
      'execute' => 
      array (
      ),
      'migrate' => 
      array (
      ),
      'status' => 
      array (
      ),
      'version' => 
      array (
      ),
      'diff' => 
      array (
      ),
    ),
  ),
  'upload' => 
  array (
    'filesystem' => 
    array (
      'path' => false,
      'tmp_path' => '/Users/hanif/Workspace/stokq-com/var/tmp',
      'rel_path' => '/files',
    ),
  ),
  'nav' => 
  array (
    'sidebar' => 
    array (
      'home' => 
      array (
        'title' => 'Overview',
        'icon' => 'dashboard',
        'url' => '/',
      ),
      'warehouse' => 
      array (
        'title' => 'Gudang',
        'icon' => 'inbox',
        'url' => '/warehouse',
      ),
      'outlet' => 
      array (
        'title' => 'Outlet',
        'icon' => 'sitemap',
        'url' => '/outlet',
      ),
      'stock' => 
      array (
        'title' => 'Stok',
        'icon' => 'tasks',
        'url' => '/stock-item',
      ),
      'menu' => 
      array (
        'title' => 'Menu',
        'icon' => 'cutlery',
        'url' => '/menu',
      ),
      'sale' => 
      array (
        'title' => 'Penjualan',
        'icon' => 'money',
        'url' => '/sale',
      ),
      'purchase' => 
      array (
        'title' => 'Belanja',
        'icon' => 'truck',
        'url' => '/purchase',
      ),
      'report' => 
      array (
        'title' => 'Report',
        'icon' => 'bar-chart-o',
        'url' => '/report',
      ),
      'setting' => 
      array (
        'title' => 'Setting',
        'icon' => 'building-o',
        'url' => '/setting/general',
      ),
    ),
  ),
  'doctrine_factories' => 
  array (
    'cache' => 'DoctrineModule\\Service\\CacheFactory',
    'eventmanager' => 'DoctrineModule\\Service\\EventManagerFactory',
    'driver' => 'DoctrineModule\\Service\\DriverFactory',
    'authenticationadapter' => 'DoctrineModule\\Service\\Authentication\\AdapterFactory',
    'authenticationstorage' => 'DoctrineModule\\Service\\Authentication\\StorageFactory',
    'authenticationservice' => 'DoctrineModule\\Service\\Authentication\\AuthenticationServiceFactory',
    'connection' => 'DoctrineORMModule\\Service\\DBALConnectionFactory',
    'configuration' => 'DoctrineORMModule\\Service\\ConfigurationFactory',
    'entitymanager' => 'DoctrineORMModule\\Service\\EntityManagerFactory',
    'entity_resolver' => 'DoctrineORMModule\\Service\\EntityResolverFactory',
    'sql_logger_collector' => 'DoctrineORMModule\\Service\\SQLLoggerCollectorFactory',
    'mapping_collector' => 'DoctrineORMModule\\Service\\MappingCollectorFactory',
    'formannotationbuilder' => 'DoctrineORMModule\\Service\\FormAnnotationBuilderFactory',
    'migrations_configuration' => 'DoctrineORMModule\\Service\\MigrationsConfigurationFactory',
    'migrations_cmd' => 'DoctrineORMModule\\Service\\MigrationsCommandFactory',
  ),
  'route_manager' => 
  array (
    'factories' => 
    array (
      'symfony_cli' => 'DoctrineModule\\Service\\SymfonyCliRouteFactory',
    ),
  ),
  'console' => 
  array (
    'router' => 
    array (
      'routes' => 
      array (
        'doctrine_cli' => 
        array (
          'type' => 'symfony_cli',
        ),
      ),
    ),
  ),
  'form_elements' => 
  array (
    'aliases' => 
    array (
      'objectselect' => 'DoctrineModule\\Form\\Element\\ObjectSelect',
      'objectradio' => 'DoctrineModule\\Form\\Element\\ObjectRadio',
      'objectmulticheckbox' => 'DoctrineModule\\Form\\Element\\ObjectMultiCheckbox',
    ),
    'factories' => 
    array (
      'DoctrineModule\\Form\\Element\\ObjectSelect' => 'DoctrineORMModule\\Service\\ObjectSelectFactory',
      'DoctrineModule\\Form\\Element\\ObjectRadio' => 'DoctrineORMModule\\Service\\ObjectRadioFactory',
      'DoctrineModule\\Form\\Element\\ObjectMultiCheckbox' => 'DoctrineORMModule\\Service\\ObjectMultiCheckboxFactory',
    ),
  ),
  'hydrators' => 
  array (
    'factories' => 
    array (
      'DoctrineModule\\Stdlib\\Hydrator\\DoctrineObject' => 'DoctrineORMModule\\Service\\DoctrineObjectHydratorFactory',
    ),
  ),
  'zenddevelopertools' => 
  array (
    'profiler' => 
    array (
      'collectors' => 
      array (
        'doctrine.sql_logger_collector.orm_default' => 'doctrine.sql_logger_collector.orm_default',
        'doctrine.mapping_collector.orm_default' => 'doctrine.mapping_collector.orm_default',
      ),
    ),
    'toolbar' => 
    array (
      'entries' => 
      array (
        'doctrine.sql_logger_collector.orm_default' => 'zend-developer-tools/toolbar/doctrine-orm-queries',
        'doctrine.mapping_collector.orm_default' => 'zend-developer-tools/toolbar/doctrine-orm-mappings',
      ),
    ),
  ),
  'slm_mail' => 
  array (
    'http_adapter' => 'Zend\\Http\\Client\\Adapter\\Socket',
    'mailgun' => 
    array (
      'domain' => 'mg.stokq.com',
      'key' => 'key-c8702e902339881ee857ae009ddcd875',
    ),
  ),
  'log' => 
  array (
    'handlers' => 
    array (
      0 => 'Monolog\\Handler\\NullHandler',
    ),
  ),
  'zend-sentry' => 
  array (
    'use-module' => true,
    'attach-log-listener' => true,
    'handle-errors' => true,
    'call-existing-error-handler' => true,
    'handle-shutdown-errors' => true,
    'handle-exceptions' => true,
    'call-existing-exception-handler' => true,
    'display-exceptions' => false,
    'default-exception-message' => 'Oh no. Something went wrong, but we have been notified. If you are testing, tell us your eventID: %s',
    'default-exception-console-message' => 'Oh no. Something went wrong, but we have been notified.
',
    'handle-javascript-errors' => true,
    'raven-config' => 
    array (
    ),
    'sentry-api-key' => 'https://8f36199e06f049adaf47a1bf1c680f13:05a167c6d5d84ece8f68ec8913aca76c@app.getsentry.com/41103',
    'sentry-api-key-js' => 'https://8f36199e06f049adaf47a1bf1c680f13@app.getsentry.com/41103',
  ),
  'db' => 
  array (
    'driver' => 'Pdo',
    'host' => '127.0.0.1',
    'port' => 3306,
    'username' => 'root',
    'password' => '',
    'dbname' => 'stokq',
    'profiler' => false,
    'options' => 
    array (
      'buffer_results' => false,
    ),
    'driver_options' => 
    array (
      3 => 2,
      1002 => 'SET NAMES \'UTF8\'',
    ),
  ),
  'slack' => 
  array (
    'webhook' => 
    array (
      'help-support' => 
      array (
        'url' => 'https://hooks.slack.com/services/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',
      ),
    ),
  ),
);