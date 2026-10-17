<?php declare(strict_types=1);

$env = is_file(__DIR__ . '/.env')
	? (parse_ini_file(__DIR__ . '/.env', false, INI_SCANNER_RAW) ?: [])
	: [];

return [
	'paths' => [
		'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
		'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds',
	],
	'environments' => [
		'default_migration_table' => 'phinxlog',
		'default_environment' => 'development',
		'development' => [
			'adapter' => 'pgsql',
			'host' => $env['DB_HOST'] ?? '127.0.0.1',
			'port' => (int) ($env['DB_PORT'] ?? 5432),
			'name' => $env['DB_NAME'] ?? 'moto',
			'user' => $env['DB_USER'] ?? 'moto',
			'pass' => $env['DB_PASSWORD'] ?? '',
			'charset' => 'utf8',
		],
	],
	'version_order' => 'creation',
];
