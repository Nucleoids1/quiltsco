<?php

declare(strict_types=1);

require_once __DIR__ . '/_functions_test_helpers.php';

if (!defined('SQL_PASSWORD')) {
    define('SQL_PASSWORD', 'config_password');
}
if (!defined('SQL_HOST')) {
    define('SQL_HOST', '127.0.0.1');
}
if (!defined('SQL_LOGIN')) {
    define('SQL_LOGIN', 'config_login');
}

require_once __DIR__ . '/../classes/Connectors/mysql.php';


$reflection = new ReflectionClass(\Connectors\Mysql::class);
$userProperty = $reflection->getProperty('user');
$userProperty->setAccessible(true);
$passwordProperty = $reflection->getProperty('password');
$passwordProperty->setAccessible(true);

putenv('TEST_DB_LOGIN');
putenv('TEST_DB_PASSWORD');
$defaultConnector = \Connectors\Mysql::init('env_override_default_db');
assertSameValue('config_login', $userProperty->getValue($defaultConnector), 'Mysql uses SQL_LOGIN when TEST_DB_LOGIN is unset.');
assertSameValue('config_password', $passwordProperty->getValue($defaultConnector), 'Mysql uses SQL_PASSWORD when TEST_DB_PASSWORD is unset.');

putenv('TEST_DB_LOGIN=test');
putenv('TEST_DB_PASSWORD=test');
$overrideConnector = \Connectors\Mysql::init('env_override_test_db');
assertSameValue('test', $userProperty->getValue($overrideConnector), 'Mysql uses TEST_DB_LOGIN when provided.');
assertSameValue('test', $passwordProperty->getValue($overrideConnector), 'Mysql uses TEST_DB_PASSWORD when provided.');

putenv('TEST_DB_LOGIN=   ');
putenv('TEST_DB_PASSWORD=   ');
$blankConnector = \Connectors\Mysql::init('env_override_blank_db');
assertSameValue('config_login', $userProperty->getValue($blankConnector), 'Mysql ignores blank TEST_DB_LOGIN override values.');
assertSameValue('config_password', $passwordProperty->getValue($blankConnector), 'Mysql ignores blank TEST_DB_PASSWORD override values.');

putenv('TEST_DB_LOGIN');
putenv('TEST_DB_PASSWORD');

finishTest('functions_connectors_mysql.php');
