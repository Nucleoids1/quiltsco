<?php
if (php_sapi_name() !== 'cli')
{
    die;
}

$testDatabase = getenv('TEST_DB_NAME');
if ($testDatabase === false || trim($testDatabase) === '')
{
    $testDatabase = 'quiltsco_test';
    putenv('TEST_DB_NAME=' . $testDatabase);
}

$testLogin = getenv('TEST_DB_LOGIN');
if ($testLogin === false || trim($testLogin) === '')
{
    $testLogin = 'test';
    putenv('TEST_DB_LOGIN=' . $testLogin);
}

$testPassword = getenv('TEST_DB_PASSWORD');
if ($testPassword === false || trim($testPassword) === '')
{
    $testPassword = 'test';
    putenv('TEST_DB_PASSWORD=' . $testPassword);
}

echo 'Using TEST_DB_NAME=' . $testDatabase . ' and TEST_DB_LOGIN=' . $testLogin . "\n";

if (getenv('RUN_ROUTE_POLICY_CHECK') === '1')
{
    $exitCode = 0;
    passthru('php ../scripts/check_route_policy_coverage.php', $exitCode);
    if ($exitCode !== 0)
    {
        exit($exitCode);
    }
}

function listAvailableTests()
{
    echo "Available tests:\n";
    $testDir = '../include/tests/';
    if (is_dir($testDir))
    {
        $files = scandir($testDir);
        foreach ($files as $file)
        {
            if ((substr($file, 0, 5) === 'test_' || substr($file, 0, 10) === 'functions_' || substr($file, 0, 5) === 'show_' || substr($file, 0, 7) === 'action_' || substr($file, 0, 8) === 'graphic_') && substr($file, -4) === '.php')
            {
                echo '  - ' . substr($file, 0, -4) . "\n";
            }
        }
    }
}

if (!isset($argv[1]))
{
    echo "Usage: php test.php <test_file_name>\n";
    echo "Example: php test.php functions_coolness\n\n";
    listAvailableTests();
    exit(1);
}

$testFile = $argv[1];
if (substr($testFile, -4) === '.php')
{
    $testFile = substr($testFile, 0, -4);
}

$testPath = '../include/tests/' . $testFile . '.php';
if (!is_file($testPath))
{
    echo "Error: Test file not found: {$testFile}\n";
    listAvailableTests();
    exit(1);
}

$_GET['t'] = $testFile;
include('index.php');
