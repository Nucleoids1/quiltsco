<?php

declare(strict_types=1);

require_once __DIR__ . '/../classes/DatabasesSchemes/Base.php';
require_once __DIR__ . '/_functions_test_helpers.php';

class BaseDatabaseNameResolverProbe extends \DatabasesSchemes\Base
{
    protected string $database = 'quiltsco';

    public function __construct()
    {
        // Intentionally bypass parent constructor to avoid DB connector dependencies.
    }

    public function resolveForTest(): string
    {
        return $this->resolveDatabaseName();
    }
}

$probe = new BaseDatabaseNameResolverProbe();

putenv('TEST_DB_NAME');
assertSameValue('quiltsco', $probe->resolveForTest(), 'resolveDatabaseName uses default database when TEST_DB_NAME is not set.');

putenv('TEST_DB_NAME=quiltsco_test');
assertSameValue('quiltsco_test', $probe->resolveForTest(), 'resolveDatabaseName uses TEST_DB_NAME override when set.');

putenv('TEST_DB_NAME=   ');
assertSameValue('quiltsco', $probe->resolveForTest(), 'resolveDatabaseName ignores blank TEST_DB_NAME values.');

putenv('TEST_DB_NAME');

finishTest('functions_databaseschemes_base.php');
