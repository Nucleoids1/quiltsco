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
if (!defined('MYSQLI_ASSOC')) {
    define('MYSQLI_ASSOC', 1);
}

require_once __DIR__ . '/../classes/Connectors/mysql.php';

class FakeMysqliResult
{
    public int $num_rows;
    private int $cursor = 0;
    private array $rows;

    public function __construct(array $rows)
    {
        $this->rows = array_values($rows);
        $this->num_rows = count($this->rows);
    }

    public function fetch_assoc()
    {
        if ($this->cursor >= $this->num_rows) {
            return null;
        }

        return $this->rows[$this->cursor++];
    }

    public function fetch_all(int $mode): array
    {
        return $this->rows;
    }
}

class FakeMysqliStatement
{
    public int $affected_rows;
    public array $executedValues = [];
    private $result;

    public function __construct($result, int $affectedRows = 0)
    {
        $this->result = $result;
        $this->affected_rows = $affectedRows;
    }

    public function execute(array $values): bool
    {
        $this->executedValues[] = $values;
        return true;
    }

    public function get_result()
    {
        return $this->result;
    }
}

class FakeMysqliConnection
{
    public int $errno = 0;
    public string $error = '';
    public array $preparedSql = [];
    private array $queuedStatements = [];

    public function queueStatement(FakeMysqliStatement $statement): void
    {
        $this->queuedStatements[] = $statement;
    }

    public function prepare(string $sql)
    {
        $this->preparedSql[] = $sql;
        return array_shift($this->queuedStatements);
    }

    public function close(): void
    {
    }
}

function setMysqlConnections(\Connectors\Mysql $mysql, array $connections): void
{
    $reflection = new ReflectionClass($mysql);
    $connectionsProperty = $reflection->getProperty('connections');
    $connectionsProperty->setAccessible(true);
    $connectionsProperty->setValue($mysql, $connections);
}

$mysql = \Connectors\Mysql::init('unit_mysql_database_calls_select');
$slaveConnection = new FakeMysqliConnection();
$selectResult = new FakeMysqliResult([
    ['id' => 2, 'width' => 100],
    ['id' => 1, 'width' => 90],
]);
$selectStatement = new FakeMysqliStatement($selectResult);
$slaveConnection->queueStatement($selectStatement);
setMysqlConnections($mysql, ['slave' => $slaveConnection]);

$rows = $mysql->selectWhere(
    'images',
    ['width >' => 80, 'deleted' => null],
    'id DESC',
    10,
    ['offset' => 5, 'fields' => 'id,width', 'allowedOrderColumns' => ['id', 'width', 'deleted']]
);

assertSameValue(
    'SELECT `id`,`width` FROM `images` WHERE `width` > ? AND `deleted` IS NULL ORDER BY `id` DESC LIMIT 10 OFFSET 5',
    $slaveConnection->preparedSql[0] ?? '',
    'selectWhere builds expected SQL with operators, NULL checks, ordering, limit, and offset.'
);
assertSameValue([[80]], $selectStatement->executedValues, 'selectWhere executes with bound WHERE values only.');
assertSameValue([
    ['id' => 2, 'width' => 100],
    ['id' => 1, 'width' => 90],
], $rows, 'selectWhere returns rows from database result set.');

$mysqlRead = \Connectors\Mysql::init('unit_mysql_database_calls_read');
$readConnection = new FakeMysqliConnection();
$readResult = new FakeMysqliResult([
    ['id' => 7, 'title' => 'A'],
]);
$readStatement = new FakeMysqliStatement($readResult);
$readConnection->queueStatement($readStatement);
setMysqlConnections($mysqlRead, ['slave' => $readConnection]);

$readRows = $mysqlRead->sqlRead('SELECT id, title FROM images WHERE width > ? AND id = ?', ['width' => 50, 'id' => 7]);

assertSameValue(
    'SELECT id, title FROM images WHERE width > ? AND id = ?',
    $readConnection->preparedSql[0] ?? '',
    'sqlRead uses the provided SQL query unchanged.'
);
assertSameValue([[50, 7]], $readStatement->executedValues, 'sqlRead binds array_values in the provided order.');
assertSameValue([['id' => 7, 'title' => 'A']], $readRows, 'sqlRead returns all rows from the result set.');

$mysqlWrite = \Connectors\Mysql::init('unit_mysql_database_calls_write');
$masterConnection = new FakeMysqliConnection();
$writeStatement = new FakeMysqliStatement(false, 3);
$masterConnection->queueStatement($writeStatement);
setMysqlConnections($mysqlWrite, ['master' => $masterConnection]);

$affected = $mysqlWrite->sqlWrite('UPDATE images SET width = ? WHERE id = ?', [64, 9]);

assertSameValue('UPDATE images SET width = ? WHERE id = ?', $masterConnection->preparedSql[0] ?? '', 'sqlWrite prepares the provided write SQL.');
assertSameValue([[64, 9]], $writeStatement->executedValues, 'sqlWrite binds provided values in order.');
assertSameValue(3, $affected, 'sqlWrite returns affected row count from statement.');

$mysqlInvalidOrder = \Connectors\Mysql::init('unit_mysql_database_calls_invalid_order');
$invalidOrderConnection = new FakeMysqliConnection();
setMysqlConnections($mysqlInvalidOrder, ['slave' => $invalidOrderConnection]);

assertThrows(
    static fn () => $mysqlInvalidOrder->selectWhere(
        'images',
        ['deleted' => '0'],
        'id DESC; DROP TABLE images',
        10,
        ['allowedOrderColumns' => ['id', 'deleted']]
    ),
    \InvalidArgumentException::class,
    'selectWhere rejects malformed order clauses.'
);
assertSameValue([], $invalidOrderConnection->preparedSql, 'selectWhere with invalid order does not call prepare().');

$mysqlInvalidLimit = \Connectors\Mysql::init('unit_mysql_database_calls_invalid_limit');
$invalidLimitConnection = new FakeMysqliConnection();
setMysqlConnections($mysqlInvalidLimit, ['slave' => $invalidLimitConnection]);

assertThrows(
    static fn () => $mysqlInvalidLimit->selectWhere(
        'images',
        ['deleted' => '0'],
        'id DESC',
        0,
        ['limit' => 'ten', 'allowedOrderColumns' => ['id', 'deleted']]
    ),
    \InvalidArgumentException::class,
    'selectWhere rejects non-integer limit values supplied via options.'
);
assertSameValue([], $invalidLimitConnection->preparedSql, 'selectWhere with invalid limit does not call prepare().');


finishTest('functions_mysql_database.php');
