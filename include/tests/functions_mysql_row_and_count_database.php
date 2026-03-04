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

class FakeMysqliResultRowCount
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
}

class FakeMysqliStatementRowCount
{
    public array $executedValues = [];
    private $result;

    public function __construct($result)
    {
        $this->result = $result;
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

class FakeMysqliConnectionRowCount
{
    public int $errno = 0;
    public string $error = '';
    public array $preparedSql = [];
    private array $queuedStatements = [];

    public function queueStatement(FakeMysqliStatementRowCount $statement): void
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

function setMysqlConnectionsForRowCount(\Connectors\Mysql $mysql, array $connections): void
{
    $reflection = new ReflectionClass($mysql);
    $connectionsProperty = $reflection->getProperty('connections');
    $connectionsProperty->setAccessible(true);
    $connectionsProperty->setValue($mysql, $connections);
}

$mysqlRow = \Connectors\Mysql::init('unit_mysql_row_database');
$rowConnection = new FakeMysqliConnectionRowCount();
$rowStatement = new FakeMysqliStatementRowCount(new FakeMysqliResultRowCount([
    ['id' => 9, 'width' => 64, 'height' => 32],
]));
$rowConnection->queueStatement($rowStatement);
setMysqlConnectionsForRowCount($mysqlRow, ['slave' => $rowConnection]);

$selectedWidth = $mysqlRow->selectWhereRow(
    'images',
    ['width >=' => 64, 'deleted' => null],
    'id DESC',
    ['arrayValue' => 'width', 'offset' => 2, 'fields' => 'id,width,height', 'allowedOrderColumns' => ['id', 'width', 'height', 'deleted']]
);

assertSameValue(
    'SELECT `id`,`width`,`height` FROM `images` WHERE `width` >= ? AND `deleted` IS NULL ORDER BY `id` DESC LIMIT 1 OFFSET 2',
    $rowConnection->preparedSql[0] ?? '',
    'selectWhereRow builds expected SQL for row queries including fields, where clauses, order, and offset.'
);
assertSameValue([[64]], $rowStatement->executedValues, 'selectWhereRow binds comparison values and excludes NULL values from bindings.');
assertSameValue(64, $selectedWidth, 'selectWhereRow returns arrayValue for the selected row.');

$mysqlCount = \Connectors\Mysql::init('unit_mysql_count_database');
$countConnection = new FakeMysqliConnectionRowCount();
$countStatement = new FakeMysqliStatementRowCount(new FakeMysqliResultRowCount([
    ['count' => '12'],
]));
$countConnection->queueStatement($countStatement);
setMysqlConnectionsForRowCount($mysqlCount, ['master' => $countConnection]);

$total = $mysqlCount->count('images', ['width >' => 10, 'deleted' => null], 'master');

assertSameValue(
    'SELECT COUNT(*) AS count FROM `images` WHERE `width` > ? AND `deleted` IS NULL',
    $countConnection->preparedSql[0] ?? '',
    'count builds expected SQL and includes WHERE clause with comparison and NULL handling.'
);
assertSameValue([[10]], $countStatement->executedValues, 'count binds only parameterized where values.');
assertSameValue(12, $total, 'count returns integer value from query result.');

$mysqlInvalidRowOrder = \Connectors\Mysql::init('unit_mysql_row_database_invalid_order');
$invalidRowOrderConnection = new FakeMysqliConnectionRowCount();
setMysqlConnectionsForRowCount($mysqlInvalidRowOrder, ['slave' => $invalidRowOrderConnection]);

assertThrows(
    static fn () => $mysqlInvalidRowOrder->selectWhereRow(
        'images',
        ['deleted' => '0'],
        'height NULLS FIRST',
        ['allowedOrderColumns' => ['id', 'width', 'height', 'deleted']]
    ),
    \InvalidArgumentException::class,
    'selectWhereRow rejects unsupported ORDER BY syntax.'
);
assertSameValue([], $invalidRowOrderConnection->preparedSql, 'selectWhereRow with invalid order does not call prepare().');


finishTest('functions_mysql_row_and_count_database.php');
