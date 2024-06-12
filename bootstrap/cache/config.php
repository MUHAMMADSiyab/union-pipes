<?php return array (
  'app' => 
  array (
    'name' => 'UnionPipes',
    'env' => 'local',
    'debug' => true,
    'url' => 'https://ejazahmad.co',
    'asset_url' => NULL,
    'timezone' => 'Asia/Karachi',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => 'base64:jn9usno/lYN4jI5b42QGAFXGMtpCDECf0HD1EiRcflE=',
    'cipher' => 'AES-256-CBC',
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Cookie\\CookieServiceProvider',
      6 => 'Illuminate\\Database\\DatabaseServiceProvider',
      7 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      8 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      9 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      10 => 'Illuminate\\Hashing\\HashServiceProvider',
      11 => 'Illuminate\\Mail\\MailServiceProvider',
      12 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      13 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      14 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      15 => 'Illuminate\\Queue\\QueueServiceProvider',
      16 => 'Illuminate\\Redis\\RedisServiceProvider',
      17 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      18 => 'Illuminate\\Session\\SessionServiceProvider',
      19 => 'Illuminate\\Translation\\TranslationServiceProvider',
      20 => 'Illuminate\\Validation\\ValidationServiceProvider',
      21 => 'Illuminate\\View\\ViewServiceProvider',
      22 => 'App\\Providers\\AppServiceProvider',
      23 => 'App\\Providers\\AuthServiceProvider',
      24 => 'App\\Providers\\EventServiceProvider',
      25 => 'App\\Providers\\RouteServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Arr' => 'Illuminate\\Support\\Arr',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'Date' => 'Illuminate\\Support\\Facades\\Date',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Http' => 'Illuminate\\Support\\Facades\\Http',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'Str' => 'Illuminate\\Support\\Str',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'api' => 
      array (
        'driver' => 'jwt',
        'provider' => 'users',
        'hash' => false,
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Models\\User',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
        'throttle' => 60,
      ),
    ),
    'password_timeout' => 10800,
  ),
  'backup' => 
  array (
    'backup' => 
    array (
      'name' => 'UnionPipes',
      'source' => 
      array (
        'files' => 
        array (
          'include' => 
          array (
            0 => '/home/siyabdev/workspace/union-pipes',
          ),
          'exclude' => 
          array (
            0 => '/home/siyabdev/workspace/union-pipes/vendor',
            1 => '/home/siyabdev/workspace/union-pipes/node_modules',
          ),
          'follow_links' => false,
          'ignore_unreadable_directories' => false,
          'relative_path' => NULL,
        ),
        'databases' => 
        array (
          0 => 'mysql',
        ),
      ),
      'database_dump_compressor' => NULL,
      'database_dump_file_extension' => '',
      'destination' => 
      array (
        'filename_prefix' => '',
        'disks' => 
        array (
          0 => 'local',
        ),
      ),
      'temporary_directory' => '/home/siyabdev/workspace/union-pipes/storage/app/backup-temp',
      'password' => NULL,
      'encryption' => 'default',
    ),
    'notifications' => 
    array (
      'notifications' => 
      array (
        'Spatie\\Backup\\Notifications\\Notifications\\BackupHasFailed' => 
        array (
          0 => 'mail',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\UnhealthyBackupWasFound' => 
        array (
          0 => 'mail',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\CleanupHasFailed' => 
        array (
          0 => 'mail',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\BackupWasSuccessful' => 
        array (
          0 => 'mail',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\HealthyBackupWasFound' => 
        array (
          0 => 'mail',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\CleanupWasSuccessful' => 
        array (
          0 => 'mail',
        ),
      ),
      'notifiable' => 'Spatie\\Backup\\Notifications\\Notifiable',
      'mail' => 
      array (
        'to' => 'your@example.com',
        'from' => 
        array (
          'address' => NULL,
          'name' => 'UnionPipes',
        ),
      ),
      'slack' => 
      array (
        'webhook_url' => '',
        'channel' => NULL,
        'username' => NULL,
        'icon' => NULL,
      ),
    ),
    'monitor_backups' => 
    array (
      0 => 
      array (
        'name' => 'UnionPipes',
        'disks' => 
        array (
          0 => 'local',
        ),
        'health_checks' => 
        array (
          'Spatie\\Backup\\Tasks\\Monitor\\HealthChecks\\MaximumAgeInDays' => 1,
          'Spatie\\Backup\\Tasks\\Monitor\\HealthChecks\\MaximumStorageInMegabytes' => 5000,
        ),
      ),
    ),
    'cleanup' => 
    array (
      'strategy' => 'Spatie\\Backup\\Tasks\\Cleanup\\Strategies\\DefaultStrategy',
      'default_strategy' => 
      array (
        'keep_all_backups_for_days' => 7,
        'keep_daily_backups_for_days' => 16,
        'keep_weekly_backups_for_weeks' => 8,
        'keep_monthly_backups_for_months' => 4,
        'keep_yearly_backups_for_years' => 2,
        'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
      ),
    ),
  ),
  'broadcasting' => 
  array (
    'default' => 'log',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => '',
        'secret' => '',
        'app_id' => '',
        'options' => 
        array (
          'cluster' => 'mt1',
          'useTLS' => true,
        ),
      ),
      'ably' => 
      array (
        'driver' => 'ably',
        'key' => NULL,
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'cache' => 
  array (
    'default' => 'file',
    'stores' => 
    array (
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      'array' => 
      array (
        'driver' => 'array',
        'serialize' => false,
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
        'lock_connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => '/home/siyabdev/workspace/union-pipes/storage/framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
      ),
      'dynamodb' => 
      array (
        'driver' => 'dynamodb',
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
        'table' => 'cache',
        'endpoint' => NULL,
      ),
    ),
    'prefix' => 'unionpipes_cache',
  ),
  'cors' => 
  array (
    'paths' => 
    array (
      0 => 'api/*',
      1 => 'sanctum/csrf-cookie',
    ),
    'allowed_methods' => 
    array (
      0 => '*',
    ),
    'allowed_origins' => 
    array (
      0 => '*',
    ),
    'allowed_origins_patterns' => 
    array (
    ),
    'allowed_headers' => 
    array (
      0 => '*',
    ),
    'exposed_headers' => 
    array (
    ),
    'max_age' => 0,
    'supports_credentials' => false,
  ),
  'currencies' => 
  array (
    0 => 
    array (
      'code' => 'AED',
      'name' => 'United Arab Emirates Dirham',
    ),
    1 => 
    array (
      'code' => 'AFN',
      'name' => 'Afghan Afghani',
    ),
    2 => 
    array (
      'code' => 'ALL',
      'name' => 'Albanian Lek',
    ),
    3 => 
    array (
      'code' => 'AMD',
      'name' => 'Armenian Dram',
    ),
    4 => 
    array (
      'code' => 'ANG',
      'name' => 'Netherlands Antillean Guilder',
    ),
    5 => 
    array (
      'code' => 'AOA',
      'name' => 'Angolan Kwanza',
    ),
    6 => 
    array (
      'code' => 'ARS',
      'name' => 'Argentine Peso',
    ),
    7 => 
    array (
      'code' => 'AUD',
      'name' => 'Australian Dollar',
    ),
    8 => 
    array (
      'code' => 'AWG',
      'name' => 'Aruban Florin',
    ),
    9 => 
    array (
      'code' => 'AZN',
      'name' => 'Azerbaijani Manat',
    ),
    10 => 
    array (
      'code' => 'BAM',
      'name' => 'Bosnia-Herzegovina Convertible Mark',
    ),
    11 => 
    array (
      'code' => 'BBD',
      'name' => 'Barbadian Dollar',
    ),
    12 => 
    array (
      'code' => 'BDT',
      'name' => 'Bangladeshi Taka',
    ),
    13 => 
    array (
      'code' => 'BGN',
      'name' => 'Bulgarian Lev',
    ),
    14 => 
    array (
      'code' => 'BHD',
      'name' => 'Bahraini Dinar',
    ),
    15 => 
    array (
      'code' => 'BIF',
      'name' => 'Burundian Franc',
    ),
    16 => 
    array (
      'code' => 'BMD',
      'name' => 'Bermudan Dollar',
    ),
    17 => 
    array (
      'code' => 'BND',
      'name' => 'Brunei Dollar',
    ),
    18 => 
    array (
      'code' => 'BOB',
      'name' => 'Bolivian Boliviano',
    ),
    19 => 
    array (
      'code' => 'BRL',
      'name' => 'Brazilian Real',
    ),
    20 => 
    array (
      'code' => 'BSD',
      'name' => 'Bahamian Dollar',
    ),
    21 => 
    array (
      'code' => 'BTC',
      'name' => 'Bitcoin',
    ),
    22 => 
    array (
      'code' => 'BTN',
      'name' => 'Bhutanese Ngultrum',
    ),
    23 => 
    array (
      'code' => 'BWP',
      'name' => 'Botswanan Pula',
    ),
    24 => 
    array (
      'code' => 'BYN',
      'name' => 'Belarusian Ruble',
    ),
    25 => 
    array (
      'code' => 'BZD',
      'name' => 'Belize Dollar',
    ),
    26 => 
    array (
      'code' => 'CAD',
      'name' => 'Canadian Dollar',
    ),
    27 => 
    array (
      'code' => 'CDF',
      'name' => 'Congolese Franc',
    ),
    28 => 
    array (
      'code' => 'CHF',
      'name' => 'Swiss Franc',
    ),
    29 => 
    array (
      'code' => 'CLF',
      'name' => 'Chilean Unit of Account (UF)',
    ),
    30 => 
    array (
      'code' => 'CLP',
      'name' => 'Chilean Peso',
    ),
    31 => 
    array (
      'code' => 'CNH',
      'name' => 'Chinese Yuan (Offshore)',
    ),
    32 => 
    array (
      'code' => 'CNY',
      'name' => 'Chinese Yuan',
    ),
    33 => 
    array (
      'code' => 'COP',
      'name' => 'Colombian Peso',
    ),
    34 => 
    array (
      'code' => 'CRC',
      'name' => 'Costa Rican Colón',
    ),
    35 => 
    array (
      'code' => 'CUC',
      'name' => 'Cuban Convertible Peso',
    ),
    36 => 
    array (
      'code' => 'CUP',
      'name' => 'Cuban Peso',
    ),
    37 => 
    array (
      'code' => 'CVE',
      'name' => 'Cape Verdean Escudo',
    ),
    38 => 
    array (
      'code' => 'CZK',
      'name' => 'Czech Republic Koruna',
    ),
    39 => 
    array (
      'code' => 'DJF',
      'name' => 'Djiboutian Franc',
    ),
    40 => 
    array (
      'code' => 'DKK',
      'name' => 'Danish Krone',
    ),
    41 => 
    array (
      'code' => 'DOP',
      'name' => 'Dominican Peso',
    ),
    42 => 
    array (
      'code' => 'DZD',
      'name' => 'Algerian Dinar',
    ),
    43 => 
    array (
      'code' => 'EGP',
      'name' => 'Egyptian Pound',
    ),
    44 => 
    array (
      'code' => 'ERN',
      'name' => 'Eritrean Nakfa',
    ),
    45 => 
    array (
      'code' => 'ETB',
      'name' => 'Ethiopian Birr',
    ),
    46 => 
    array (
      'code' => 'EUR',
      'name' => 'Euro',
    ),
    47 => 
    array (
      'code' => 'FJD',
      'name' => 'Fijian Dollar',
    ),
    48 => 
    array (
      'code' => 'FKP',
      'name' => 'Falkland Islands Pound',
    ),
    49 => 
    array (
      'code' => 'GBP',
      'name' => 'British Pound Sterling',
    ),
    50 => 
    array (
      'code' => 'GEL',
      'name' => 'Georgian Lari',
    ),
    51 => 
    array (
      'code' => 'GGP',
      'name' => 'Guernsey Pound',
    ),
    52 => 
    array (
      'code' => 'GHS',
      'name' => 'Ghanaian Cedi',
    ),
    53 => 
    array (
      'code' => 'GIP',
      'name' => 'Gibraltar Pound',
    ),
    54 => 
    array (
      'code' => 'GMD',
      'name' => 'Gambian Dalasi',
    ),
    55 => 
    array (
      'code' => 'GNF',
      'name' => 'Guinean Franc',
    ),
    56 => 
    array (
      'code' => 'GTQ',
      'name' => 'Guatemalan Quetzal',
    ),
    57 => 
    array (
      'code' => 'GYD',
      'name' => 'Guyanaese Dollar',
    ),
    58 => 
    array (
      'code' => 'HKD',
      'name' => 'Hong Kong Dollar',
    ),
    59 => 
    array (
      'code' => 'HNL',
      'name' => 'Honduran Lempira',
    ),
    60 => 
    array (
      'code' => 'HRK',
      'name' => 'Croatian Kuna',
    ),
    61 => 
    array (
      'code' => 'HTG',
      'name' => 'Haitian Gourde',
    ),
    62 => 
    array (
      'code' => 'HUF',
      'name' => 'Hungarian Forint',
    ),
    63 => 
    array (
      'code' => 'IDR',
      'name' => 'Indonesian Rupiah',
    ),
    64 => 
    array (
      'code' => 'ILS',
      'name' => 'Israeli New Sheqel',
    ),
    65 => 
    array (
      'code' => 'IMP',
      'name' => 'Manx pound',
    ),
    66 => 
    array (
      'code' => 'INR',
      'name' => 'Indian Rupee',
    ),
    67 => 
    array (
      'code' => 'IQD',
      'name' => 'Iraqi Dinar',
    ),
    68 => 
    array (
      'code' => 'IRR',
      'name' => 'Iranian Rial',
    ),
    69 => 
    array (
      'code' => 'ISK',
      'name' => 'Icelandic Króna',
    ),
    70 => 
    array (
      'code' => 'JEP',
      'name' => 'Jersey Pound',
    ),
    71 => 
    array (
      'code' => 'JMD',
      'name' => 'Jamaican Dollar',
    ),
    72 => 
    array (
      'code' => 'JOD',
      'name' => 'Jordanian Dinar',
    ),
    73 => 
    array (
      'code' => 'JPY',
      'name' => 'Japanese Yen',
    ),
    74 => 
    array (
      'code' => 'KES',
      'name' => 'Kenyan Shilling',
    ),
    75 => 
    array (
      'code' => 'KGS',
      'name' => 'Kyrgystani Som',
    ),
    76 => 
    array (
      'code' => 'KHR',
      'name' => 'Cambodian Riel',
    ),
    77 => 
    array (
      'code' => 'KMF',
      'name' => 'Comorian Franc',
    ),
    78 => 
    array (
      'code' => 'KPW',
      'name' => 'North Korean Won',
    ),
    79 => 
    array (
      'code' => 'KRW',
      'name' => 'South Korean Won',
    ),
    80 => 
    array (
      'code' => 'KWD',
      'name' => 'Kuwaiti Dinar',
    ),
    81 => 
    array (
      'code' => 'KYD',
      'name' => 'Cayman Islands Dollar',
    ),
    82 => 
    array (
      'code' => 'KZT',
      'name' => 'Kazakhstani Tenge',
    ),
    83 => 
    array (
      'code' => 'LAK',
      'name' => 'Laotian Kip',
    ),
    84 => 
    array (
      'code' => 'LBP',
      'name' => 'Lebanese Pound',
    ),
    85 => 
    array (
      'code' => 'LKR',
      'name' => 'Sri Lankan Rupee',
    ),
    86 => 
    array (
      'code' => 'LRD',
      'name' => 'Liberian Dollar',
    ),
    87 => 
    array (
      'code' => 'LSL',
      'name' => 'Lesotho Loti',
    ),
    88 => 
    array (
      'code' => 'LYD',
      'name' => 'Libyan Dinar',
    ),
    89 => 
    array (
      'code' => 'MAD',
      'name' => 'Moroccan Dirham',
    ),
    90 => 
    array (
      'code' => 'MDL',
      'name' => 'Moldovan Leu',
    ),
    91 => 
    array (
      'code' => 'MGA',
      'name' => 'Malagasy Ariary',
    ),
    92 => 
    array (
      'code' => 'MKD',
      'name' => 'Macedonian Denar',
    ),
    93 => 
    array (
      'code' => 'MMK',
      'name' => 'Myanma Kyat',
    ),
    94 => 
    array (
      'code' => 'MNT',
      'name' => 'Mongolian Tugrik',
    ),
    95 => 
    array (
      'code' => 'MOP',
      'name' => 'Macanese Pataca',
    ),
    96 => 
    array (
      'code' => 'MRO',
      'name' => 'Mauritanian Ouguiya (pre-2018)',
    ),
    97 => 
    array (
      'code' => 'MRU',
      'name' => 'Mauritanian Ouguiya',
    ),
    98 => 
    array (
      'code' => 'MUR',
      'name' => 'Mauritian Rupee',
    ),
    99 => 
    array (
      'code' => 'MVR',
      'name' => 'Maldivian Rufiyaa',
    ),
    100 => 
    array (
      'code' => 'MWK',
      'name' => 'Malawian Kwacha',
    ),
    101 => 
    array (
      'code' => 'MXN',
      'name' => 'Mexican Peso',
    ),
    102 => 
    array (
      'code' => 'MYR',
      'name' => 'Malaysian Ringgit',
    ),
    103 => 
    array (
      'code' => 'MZN',
      'name' => 'Mozambican Metical',
    ),
    104 => 
    array (
      'code' => 'NAD',
      'name' => 'Namibian Dollar',
    ),
    105 => 
    array (
      'code' => 'NGN',
      'name' => 'Nigerian Naira',
    ),
    106 => 
    array (
      'code' => 'NIO',
      'name' => 'Nicaraguan Córdoba',
    ),
    107 => 
    array (
      'code' => 'NOK',
      'name' => 'Norwegian Krone',
    ),
    108 => 
    array (
      'code' => 'NPR',
      'name' => 'Nepalese Rupee',
    ),
    109 => 
    array (
      'code' => 'NZD',
      'name' => 'New Zealand Dollar',
    ),
    110 => 
    array (
      'code' => 'OMR',
      'name' => 'Omani Rial',
    ),
    111 => 
    array (
      'code' => 'PAB',
      'name' => 'Panamanian Balboa',
    ),
    112 => 
    array (
      'code' => 'PEN',
      'name' => 'Peruvian Nuevo Sol',
    ),
    113 => 
    array (
      'code' => 'PGK',
      'name' => 'Papua New Guinean Kina',
    ),
    114 => 
    array (
      'code' => 'PHP',
      'name' => 'Philippine Peso',
    ),
    115 => 
    array (
      'code' => 'PKR',
      'name' => 'Pakistani Rupee',
    ),
    116 => 
    array (
      'code' => 'PLN',
      'name' => 'Polish Zloty',
    ),
    117 => 
    array (
      'code' => 'PYG',
      'name' => 'Paraguayan Guarani',
    ),
    118 => 
    array (
      'code' => 'QAR',
      'name' => 'Qatari Rial',
    ),
    119 => 
    array (
      'code' => 'RON',
      'name' => 'Romanian Leu',
    ),
    120 => 
    array (
      'code' => 'RSD',
      'name' => 'Serbian Dinar',
    ),
    121 => 
    array (
      'code' => 'RUB',
      'name' => 'Russian Ruble',
    ),
    122 => 
    array (
      'code' => 'RWF',
      'name' => 'Rwandan Franc',
    ),
    123 => 
    array (
      'code' => 'SAR',
      'name' => 'Saudi Riyal',
    ),
    124 => 
    array (
      'code' => 'SBD',
      'name' => 'Solomon Islands Dollar',
    ),
    125 => 
    array (
      'code' => 'SCR',
      'name' => 'Seychellois Rupee',
    ),
    126 => 
    array (
      'code' => 'SDG',
      'name' => 'Sudanese Pound',
    ),
    127 => 
    array (
      'code' => 'SEK',
      'name' => 'Swedish Krona',
    ),
    128 => 
    array (
      'code' => 'SGD',
      'name' => 'Singapore Dollar',
    ),
    129 => 
    array (
      'code' => 'SHP',
      'name' => 'Saint Helena Pound',
    ),
    130 => 
    array (
      'code' => 'SLL',
      'name' => 'Sierra Leonean Leone',
    ),
    131 => 
    array (
      'code' => 'SOS',
      'name' => 'Somali Shilling',
    ),
    132 => 
    array (
      'code' => 'SRD',
      'name' => 'Surinamese Dollar',
    ),
    133 => 
    array (
      'code' => 'SSP',
      'name' => 'South Sudanese Pound',
    ),
    134 => 
    array (
      'code' => 'STD',
      'name' => 'São Tomé and Príncipe Dobra (pre-2018)',
    ),
    135 => 
    array (
      'code' => 'STN',
      'name' => 'São Tomé and Príncipe Dobra',
    ),
    136 => 
    array (
      'code' => 'SVC',
      'name' => 'Salvadoran Colón',
    ),
    137 => 
    array (
      'code' => 'SYP',
      'name' => 'Syrian Pound',
    ),
    138 => 
    array (
      'code' => 'SZL',
      'name' => 'Swazi Lilangeni',
    ),
    139 => 
    array (
      'code' => 'THB',
      'name' => 'Thai Baht',
    ),
    140 => 
    array (
      'code' => 'TJS',
      'name' => 'Tajikistani Somoni',
    ),
    141 => 
    array (
      'code' => 'TMT',
      'name' => 'Turkmenistani Manat',
    ),
    142 => 
    array (
      'code' => 'TND',
      'name' => 'Tunisian Dinar',
    ),
    143 => 
    array (
      'code' => 'TOP',
      'name' => 'Tongan Paanga',
    ),
    144 => 
    array (
      'code' => 'TRY',
      'name' => 'Turkish Lira',
    ),
    145 => 
    array (
      'code' => 'TTD',
      'name' => 'Trinidad and Tobago Dollar',
    ),
    146 => 
    array (
      'code' => 'TWD',
      'name' => 'New Taiwan Dollar',
    ),
    147 => 
    array (
      'code' => 'TZS',
      'name' => 'Tanzanian Shilling',
    ),
    148 => 
    array (
      'code' => 'UAH',
      'name' => 'Ukrainian Hryvnia',
    ),
    149 => 
    array (
      'code' => 'UGX',
      'name' => 'Ugandan Shilling',
    ),
    150 => 
    array (
      'code' => 'USD',
      'name' => 'United States Dollar',
    ),
    151 => 
    array (
      'code' => 'UYU',
      'name' => 'Uruguayan Peso',
    ),
    152 => 
    array (
      'code' => 'UZS',
      'name' => 'Uzbekistan Som',
    ),
    153 => 
    array (
      'code' => 'VEF',
      'name' => 'Venezuelan Bolívar Fuerte (Old)',
    ),
    154 => 
    array (
      'code' => 'VES',
      'name' => 'Venezuelan Bolívar Soberano',
    ),
    155 => 
    array (
      'code' => 'VND',
      'name' => 'Vietnamese Dong',
    ),
    156 => 
    array (
      'code' => 'VUV',
      'name' => 'Vanuatu Vatu',
    ),
    157 => 
    array (
      'code' => 'WST',
      'name' => 'Samoan Tala',
    ),
    158 => 
    array (
      'code' => 'XAF',
      'name' => 'CFA Franc BEAC',
    ),
    159 => 
    array (
      'code' => 'XAG',
      'name' => 'Silver Ounce',
    ),
    160 => 
    array (
      'code' => 'XAU',
      'name' => 'Gold Ounce',
    ),
    161 => 
    array (
      'code' => 'XCD',
      'name' => 'East Caribbean Dollar',
    ),
    162 => 
    array (
      'code' => 'XDR',
      'name' => 'Special Drawing Rights',
    ),
    163 => 
    array (
      'code' => 'XOF',
      'name' => 'CFA Franc BCEAO',
    ),
    164 => 
    array (
      'code' => 'XPD',
      'name' => 'Palladium Ounce',
    ),
    165 => 
    array (
      'code' => 'XPF',
      'name' => 'CFP Franc',
    ),
    166 => 
    array (
      'code' => 'XPT',
      'name' => 'Platinum Ounce',
    ),
    167 => 
    array (
      'code' => 'YER',
      'name' => 'Yemeni Rial',
    ),
    168 => 
    array (
      'code' => 'ZAR',
      'name' => 'South African Rand',
    ),
    169 => 
    array (
      'code' => 'ZMW',
      'name' => 'Zambian Kwacha',
    ),
    170 => 
    array (
      'code' => 'ZWL',
      'name' => 'Zimbabwean Dollar',
    ),
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'url' => NULL,
        'database' => 'union_pipes',
        'prefix' => '',
        'foreign_key_constraints' => true,
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'union_pipes',
        'username' => 'admin',
        'password' => 'password',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'union_pipes',
        'username' => 'admin',
        'password' => 'password',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'schema' => 'public',
        'sslmode' => 'prefer',
      ),
      'sqlsrv' => 
      array (
        'driver' => 'sqlsrv',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'union_pipes',
        'username' => 'admin',
        'password' => 'password',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'client' => 'phpredis',
      'options' => 
      array (
        'cluster' => 'redis',
        'prefix' => 'unionpipes_database_',
      ),
      'default' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => '0',
      ),
      'cache' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => '1',
      ),
    ),
  ),
  'excel' => 
  array (
    'exports' => 
    array (
      'chunk_size' => 1000,
      'pre_calculate_formulas' => false,
      'strict_null_comparison' => false,
      'csv' => 
      array (
        'delimiter' => ',',
        'enclosure' => '"',
        'line_ending' => '
',
        'use_bom' => false,
        'include_separator_line' => false,
        'excel_compatibility' => false,
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
    ),
    'imports' => 
    array (
      'read_only' => true,
      'ignore_empty' => false,
      'heading_row' => 
      array (
        'formatter' => 'slug',
      ),
      'csv' => 
      array (
        'delimiter' => ',',
        'enclosure' => '"',
        'escape_character' => '\\',
        'contiguous' => false,
        'input_encoding' => 'UTF-8',
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
    ),
    'extension_detector' => 
    array (
      'xlsx' => 'Xlsx',
      'xlsm' => 'Xlsx',
      'xltx' => 'Xlsx',
      'xltm' => 'Xlsx',
      'xls' => 'Xls',
      'xlt' => 'Xls',
      'ods' => 'Ods',
      'ots' => 'Ods',
      'slk' => 'Slk',
      'xml' => 'Xml',
      'gnumeric' => 'Gnumeric',
      'htm' => 'Html',
      'html' => 'Html',
      'csv' => 'Csv',
      'tsv' => 'Csv',
      'pdf' => 'Dompdf',
    ),
    'value_binder' => 
    array (
      'default' => 'Maatwebsite\\Excel\\DefaultValueBinder',
    ),
    'cache' => 
    array (
      'driver' => 'memory',
      'batch' => 
      array (
        'memory_limit' => 60000,
      ),
      'illuminate' => 
      array (
        'store' => NULL,
      ),
    ),
    'transactions' => 
    array (
      'handler' => 'db',
    ),
    'temporary_files' => 
    array (
      'local_path' => '/home/siyabdev/workspace/union-pipes/storage/framework/laravel-excel',
      'remote_disk' => NULL,
      'remote_prefix' => NULL,
      'force_resync_remote' => NULL,
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => '/home/siyabdev/workspace/union-pipes/storage/app',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => '/home/siyabdev/workspace/union-pipes/storage/app/public',
        'url' => 'https://ejazahmad.co/storage',
        'visibility' => 'public',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
        'bucket' => '',
        'url' => NULL,
        'endpoint' => NULL,
      ),
    ),
    'links' => 
    array (
      '/home/siyabdev/workspace/union-pipes/public/storage' => '/home/siyabdev/workspace/union-pipes/storage/app/public',
    ),
  ),
  'hashing' => 
  array (
    'driver' => 'bcrypt',
    'bcrypt' => 
    array (
      'rounds' => 10,
    ),
    'argon' => 
    array (
      'memory' => 1024,
      'threads' => 2,
      'time' => 2,
    ),
  ),
  'jwt' => 
  array (
    'secret' => 'aAQIV0ngJvBLwdVIHsJS5RXMcAyH5Y6TXgSI83MRWGG4mmJ8KvKFKEvYBvBvxI8V',
    'keys' => 
    array (
      'public' => NULL,
      'private' => NULL,
      'passphrase' => NULL,
    ),
    'ttl' => 10080,
    'refresh_ttl' => 20160,
    'algo' => 'HS256',
    'required_claims' => 
    array (
      0 => 'iss',
      1 => 'iat',
      2 => 'exp',
      3 => 'nbf',
      4 => 'sub',
      5 => 'jti',
    ),
    'persistent_claims' => 
    array (
    ),
    'lock_subject' => true,
    'leeway' => 0,
    'blacklist_enabled' => true,
    'blacklist_grace_period' => 0,
    'decrypt_cookies' => false,
    'providers' => 
    array (
      'jwt' => 'Tymon\\JWTAuth\\Providers\\JWT\\Lcobucci',
      'auth' => 'Tymon\\JWTAuth\\Providers\\Auth\\Illuminate',
      'storage' => 'Tymon\\JWTAuth\\Providers\\Storage\\Illuminate',
    ),
  ),
  'logging' => 
  array (
    'default' => 'stack',
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'single',
        ),
        'ignore_exceptions' => false,
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => '/home/siyabdev/workspace/union-pipes/storage/logs/laravel.log',
        'level' => 'debug',
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => '/home/siyabdev/workspace/union-pipes/storage/logs/laravel.log',
        'level' => 'debug',
        'days' => 14,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'debug',
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'formatter' => NULL,
        'with' => 
        array (
          'stream' => 'php://stderr',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'debug',
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'debug',
      ),
      'null' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\NullHandler',
      ),
      'emergency' => 
      array (
        'path' => '/home/siyabdev/workspace/union-pipes/storage/logs/laravel.log',
      ),
    ),
  ),
  'mail' => 
  array (
    'default' => 'smtp',
    'mailers' => 
    array (
      'smtp' => 
      array (
        'transport' => 'smtp',
        'host' => 'mailhog',
        'port' => '1025',
        'encryption' => NULL,
        'username' => NULL,
        'password' => NULL,
        'timeout' => NULL,
        'auth_mode' => NULL,
      ),
      'ses' => 
      array (
        'transport' => 'ses',
      ),
      'mailgun' => 
      array (
        'transport' => 'mailgun',
      ),
      'postmark' => 
      array (
        'transport' => 'postmark',
      ),
      'sendmail' => 
      array (
        'transport' => 'sendmail',
        'path' => '/usr/sbin/sendmail -bs',
      ),
      'log' => 
      array (
        'transport' => 'log',
        'channel' => NULL,
      ),
      'array' => 
      array (
        'transport' => 'array',
      ),
    ),
    'from' => 
    array (
      'address' => NULL,
      'name' => 'UnionPipes',
    ),
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => '/home/siyabdev/workspace/union-pipes/resources/views/vendor/mail',
      ),
    ),
  ),
  'media-library' => 
  array (
    'disk_name' => 'public',
    'max_file_size' => 10485760,
    'queue_name' => '',
    'queue_conversions_by_default' => true,
    'media_model' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
    'temporary_upload_model' => 'Spatie\\MediaLibraryPro\\Models\\TemporaryUpload',
    'enable_temporary_uploads_session_affinity' => true,
    'generate_thumbnails_for_temporary_uploads' => true,
    'file_namer' => 'Spatie\\MediaLibrary\\Support\\FileNamer\\DefaultFileNamer',
    'path_generator' => 'App\\Services\\CustomPathGenerator',
    'url_generator' => 'Spatie\\MediaLibrary\\Support\\UrlGenerator\\DefaultUrlGenerator',
    'moves_media_on_update' => false,
    'version_urls' => false,
    'image_optimizers' => 
    array (
      'Spatie\\ImageOptimizer\\Optimizers\\Jpegoptim' => 
      array (
        0 => '-m85',
        1 => '--strip-all',
        2 => '--all-progressive',
      ),
      'Spatie\\ImageOptimizer\\Optimizers\\Pngquant' => 
      array (
        0 => '--force',
      ),
      'Spatie\\ImageOptimizer\\Optimizers\\Optipng' => 
      array (
        0 => '-i0',
        1 => '-o2',
        2 => '-quiet',
      ),
      'Spatie\\ImageOptimizer\\Optimizers\\Svgo' => 
      array (
        0 => '--disable=cleanupIDs',
      ),
      'Spatie\\ImageOptimizer\\Optimizers\\Gifsicle' => 
      array (
        0 => '-b',
        1 => '-O3',
      ),
      'Spatie\\ImageOptimizer\\Optimizers\\Cwebp' => 
      array (
        0 => '-m 6',
        1 => '-pass 10',
        2 => '-mt',
        3 => '-q 90',
      ),
    ),
    'image_generators' => 
    array (
      0 => 'Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Image',
      1 => 'Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Webp',
      2 => 'Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Pdf',
      3 => 'Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Svg',
      4 => 'Spatie\\MediaLibrary\\Conversions\\ImageGenerators\\Video',
    ),
    'temporary_directory_path' => NULL,
    'image_driver' => 'gd',
    'ffmpeg_path' => '/usr/bin/ffmpeg',
    'ffprobe_path' => '/usr/bin/ffprobe',
    'jobs' => 
    array (
      'perform_conversions' => 'Spatie\\MediaLibrary\\Conversions\\Jobs\\PerformConversionsJob',
      'generate_responsive_images' => 'Spatie\\MediaLibrary\\ResponsiveImages\\Jobs\\GenerateResponsiveImagesJob',
    ),
    'media_downloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
    'remote' => 
    array (
      'extra_headers' => 
      array (
        'CacheControl' => 'max-age=604800',
      ),
    ),
    'responsive_images' => 
    array (
      'width_calculator' => 'Spatie\\MediaLibrary\\ResponsiveImages\\WidthCalculator\\FileSizeOptimizedWidthCalculator',
      'use_tiny_placeholders' => true,
      'tiny_placeholder_generator' => 'Spatie\\MediaLibrary\\ResponsiveImages\\TinyPlaceholderGenerator\\Blurred',
    ),
    'enable_vapor_uploads' => false,
    'default_loading_attribute_value' => NULL,
    'prefix' => '',
  ),
  'querydetector' => 
  array (
    'enabled' => NULL,
    'threshold' => 1,
    'except' => 
    array (
    ),
    'log_channel' => 'daily',
    'output' => 
    array (
      0 => 'BeyondCode\\QueryDetector\\Outputs\\Alert',
      1 => 'BeyondCode\\QueryDetector\\Outputs\\Log',
    ),
  ),
  'queue' => 
  array (
    'default' => 'sync',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
        'after_commit' => false,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => 0,
        'after_commit' => false,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => '',
        'secret' => '',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'default',
        'suffix' => NULL,
        'region' => 'us-east-1',
        'after_commit' => false,
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => NULL,
        'after_commit' => false,
      ),
    ),
    'failed' => 
    array (
      'driver' => 'database-uuids',
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'services' => 
  array (
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
      'endpoint' => 'api.mailgun.net',
    ),
    'postmark' => 
    array (
      'token' => NULL,
    ),
    'ses' => 
    array (
      'key' => '',
      'secret' => '',
      'region' => 'us-east-1',
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => '120',
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => '/home/siyabdev/workspace/union-pipes/storage/framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'unionpipes_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => NULL,
    'http_only' => true,
    'same_site' => 'lax',
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => '/home/siyabdev/workspace/union-pipes/resources/views',
    ),
    'compiled' => '/home/siyabdev/workspace/union-pipes/storage/framework/views',
  ),
  'flare' => 
  array (
    'key' => NULL,
    'reporting' => 
    array (
      'anonymize_ips' => true,
      'collect_git_information' => false,
      'report_queries' => true,
      'maximum_number_of_collected_queries' => 200,
      'report_query_bindings' => true,
      'report_view_data' => true,
      'grouping_type' => NULL,
      'report_logs' => true,
      'maximum_number_of_collected_logs' => 200,
      'censor_request_body_fields' => 
      array (
        0 => 'password',
      ),
    ),
    'send_logs_as_events' => true,
    'censor_request_body_fields' => 
    array (
      0 => 'password',
    ),
  ),
  'ignition' => 
  array (
    'editor' => 'phpstorm',
    'theme' => 'light',
    'enable_share_button' => true,
    'register_commands' => false,
    'ignored_solution_providers' => 
    array (
      0 => 'Facade\\Ignition\\SolutionProviders\\MissingPackageSolutionProvider',
    ),
    'enable_runnable_solutions' => NULL,
    'remote_sites_path' => '',
    'local_sites_path' => '',
    'housekeeping_endpoint_prefix' => '_ignition',
  ),
  'image' => 
  array (
    'driver' => 'gd',
  ),
  'trustedproxy' => 
  array (
    'proxies' => NULL,
    'headers' => 30,
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'alias' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
  ),
);
