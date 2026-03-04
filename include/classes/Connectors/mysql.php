<?php

namespace Connectors;

/**
 * MySQL Database Connector
 *
 * Low-level MySQL database connector with prepared statement support for secure queries.
 * This class provides direct database access methods and is typically used through the
 * DatabasesSchemes\PrimaryKey and DatabasesSchemes\PrimaryKeyComposite wrapper classes.
 *
 * Key Features:
 * - Singleton pattern for connection management per database
 * - Master/slave connection routing (read operations use slave, write operations use master)
 * - Prepared statements for all queries to prevent SQL injection
 * - Support for transactions with automatic connection management
 * - Flexible WHERE clause building with comparison operators (<, <=, >, >=, !=, LIKE)
 * - Auto-close connection management
 *
 * Connection Management:
 * - Use 'master' for write operations (INSERT, UPDATE, DELETE)
 * - Use 'slave' for read operations (SELECT) to distribute load
 * - Connections are automatically reused within the same database instance
 * - Set autoClose=false during transactions to maintain connection state
 *
 * Security:
 * - All values are bound using prepared statements
 * - SQL injection protection built into all query methods
 * - Error logging for debugging without exposing sensitive data
 *
 * Usage Example:
 *   $mysql = \Connectors\Mysql::init('my_database');
 *   $mysql->setPrimaryKey('id');
 *   $result = $mysql->insertArray('users', ['name' => 'John', 'email' => 'john@example.com']);
 *   $users = $mysql->selectWhere('users', ['status' => 'active'], 'created_at DESC', 10);
 *
 * @package Connectors
 */
class Mysql
{

    private static $init;

    private $autoClose = false; // Close connections after each query to prevent connection exhaustion
    private $autoIncrement = true; // Default to true for backward compatibility
    private $connections = [];
    private $database;
    private $password = SQL_PASSWORD;
    private $primaryKey;
    private $serverMaster = SQL_HOST;
    private $serverSlave = SQL_HOST;
    private $user = SQL_LOGIN;

    /**
     * Initialize or retrieve a MySQL connector instance for a specific database
     *
     * Implements singleton pattern to ensure one connection manager per database.
     * Connections are reused across multiple calls for the same database.
     *
     * @param string $database The database name to connect to
     * @return Mysql Instance of MySQL connector for the specified database
     */
    public static function init($database)
    {
        if (!isset(self::$init[$database])) {
            self::$init[$database] = new static();
            self::$init[$database]->database = $database;
            self::$init[$database]->applyEnvironmentOverrides();
        }
        return self::$init[$database];
    }


    /**
     * Apply optional environment-based connection overrides.
     *
     * This keeps production defaults from config constants while allowing
     * test/integration runs to override credentials via environment variables.
     * Supported variables:
     * - TEST_DB_LOGIN
     * - TEST_DB_PASSWORD
     */
    private function applyEnvironmentOverrides(): void
    {
        $login = getenv('TEST_DB_LOGIN');
        if ($login !== false) {
            $login = trim($login);
            if ($login !== '') {
                $this->user = $login;
            }
        }

        $password = getenv('TEST_DB_PASSWORD');
        if ($password !== false) {
            $password = trim($password);
            if ($password !== '') {
                $this->password = $password;
            }
        }
    }

    /**
     * Set whether connections should auto-close after each query
     *
     * When true, connections are closed immediately after query execution.
     * When false, connections remain open for subsequent queries (required for transactions).
     *
     * @param bool $autoClose True to close connections after queries, false to keep open
     * @return void
     */
    public function setAutoClose(bool $autoClose)
    {
        $this->autoClose = $autoClose;
    }

    /**
     * Set the primary key field name for the current table
     *
     * Used by methods like selectPrimaryKey() and deletePrimaryKey() to identify
     * which field is the primary key. Can be a single field name (string) or
     * multiple field names (array) for composite keys.
     *
     * @param string|array $primaryKey The primary key field name(s)
     * @return void
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    /**
     * Set whether the primary key uses auto-increment
     *
     * Controls the return value of insert methods:
     * - When true: Returns last insert ID (for AUTO_INCREMENT columns)
     * - When false: Returns affected rows count
     *
     * @param bool $autoIncrement True for auto-increment keys, false otherwise
     * @return void
     */
    public function setAutoIncrement(bool $autoIncrement)
    {
        $this->autoIncrement = $autoIncrement;
    }

    /**
     * Begin a database transaction
     *
     * Starts a transaction on the master connection and disables auto-close
     * to maintain connection state throughout the transaction.
     *
     * @param string $database Database name (parameter kept for compatibility but uses instance database)
     * @return bool True on success, false on failure
     */
    public function beginTransaction(string $database)
    {
        $this->setAutoClose(false);
        return $this->connections['master']->beginTransaction();
    }

    /**
     * Commit a database transaction
     *
     * Commits the current transaction and re-enables auto-close for future queries.
     *
     * @param string $database Database name (parameter kept for compatibility but uses instance database)
     * @return bool True on success, false on failure
     */
    public function commit(string $database)
    {
        $this->setAutoClose(true);
        $return = $this->connections['master']->commit();
        return $return;
    }

    /**
     * Connect to the database server
     *
     * Creates and caches a mysqli connection. Reuses existing connection if available.
     *
     * @param string $master Connection type: 'master' for write operations, 'slave' for read operations
     * @return \mysqli The database connection object
     */
    public function connect(string $master = 'slave')
    {
        if (!isset($this->connections[$master])) {
            if ($master == 'master') {
                $this->connections[$master] = new \mysqli($this->serverMaster, $this->user, $this->password, $this->database);
            } else {
                $this->connections[$master] = new \mysqli($this->serverSlave, $this->user, $this->password, $this->database);
            }
        }
        return $this->connections[$master];
    }

    /**
     * Parse field name with optional operator
     *
     * Extracts comparison operator from field name if present.
     * Supports: <, <=, >, >=, !=, LIKE
     *
     * @param string $field Field name with optional operator suffix (e.g., 'age >', 'name LIKE')
     * @return array [fieldName, operator] where operator defaults to '=' if not specified
     *
     * @example
     *   parseFieldOperator('fieldname <')  // Returns ['fieldname', '<']
     *   parseFieldOperator('fieldname')    // Returns ['fieldname', '=']
     *   parseFieldOperator('age >=')       // Returns ['age', '>=']
     */
    private function parseFieldOperator(string $field)
    {
        // Trim the field name
        $field = trim($field);

        // Check for operators at the end of the field name
        $operators = ['<=', '>=', '!=', '<', '>', 'LIKE'];
        foreach ($operators as $operator) {
            if (substr($field, -strlen($operator)) === $operator) {
                $fieldName = trim(substr($field, 0, -strlen($operator)));
                return [$fieldName, $operator];
            }
        }

        // Default to equals operator
        return [$field, '='];
    }

    /**
     * Build WHERE clause fields and values from an array of conditions
     * @param array $wheres Array of field => value pairs for WHERE conditions
     * @return array Returns [$wheresFields, $wheresValues]
     */
    private function buildWhereClause(array $wheres)
    {
        $wheresFields = [];
        $wheresValues = [];
        foreach ($wheres as $field => $value) {
            if (is_null($value)) {
                $wheresFields[] = "`$field` IS NULL";
            } elseif (is_numeric($field)) {
                $wheresFields[] = $value;
            } else {
                list($fieldName, $operator) = $this->parseFieldOperator($field);
                if (str_contains($fieldName, '(') && str_contains($fieldName, ')')) {
                    $wheresFields[] = "$fieldName $operator ?";
                } else {
                    $wheresFields[] = "`$fieldName` $operator ?";
                }
                $wheresValues[] = $value;
            }
        }
        return [$wheresFields, $wheresValues];
    }

    /**
     * Delete a row by primary key
     *
     * Deletes a single row from the specified table using the primary key value.
     * Uses prepared statements to prevent SQL injection attacks.
     *
     * Security:
     * - All values are bound using prepared statements
     * - SQL injection protection is built-in
     *
     * @param string $table The table name to delete from
     * @param string $id The primary key value of the row to delete
     * @return int|false Number of affected rows (0 or 1), or false on failure
     *
     * @example
     *   $mysql->setPrimaryKey('id');
     *   $mysql->deletePrimaryKey('users', '123'); // Returns 1 if deleted, 0 if not found
     */
    public function deletePrimaryKey(string $table, string $id)
    {
        $connection = $this->connect('master');
        $sql = "DELETE FROM `{$table}` WHERE " . $this->primaryKey . " = ?";
        $prepare = $this->connections['master']->prepare($sql);
        if (!$this->checkPrepare($prepare, $connection, $sql, [$id])) {
            return false;
        }
        $success = $prepare->execute([$id]);
        $this->sendError($success, func_get_args());
        $this->sendErrorMessage($connection, $sql, [$id]);
        $return = $prepare->affected_rows;
        if ($this->autoClose) {
            $this->connections['master']->close();
            unset($this->connections['master']);
        }
        return $return;
    }

    private function toNonNegativeInt(mixed $value, string $field): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_bool($value) || filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new \InvalidArgumentException('Invalid ' . $field . ' value.');
        }

        $intValue = (int)$value;
        if ($intValue < 0) {
            throw new \InvalidArgumentException('Invalid ' . $field . ' value.');
        }

        return $intValue;
    }

    public function buildOrderByClause(string $orders = '', array $allowedColumns = []): string
    {
        $orders = trim($orders);
        if ($orders === '') {
            return '';
        }

        $allowedLookup = [];
        foreach ($allowedColumns as $column) {
            if (!is_string($column)) {
                continue;
            }
            $column = trim($column);
            if ($column === '') {
                continue;
            }
            $allowedLookup[strtolower($column)] = true;
        }

        $clauses = [];
        foreach (explode(',', $orders) as $orderClause) {
            $orderClause = trim($orderClause);
            if ($orderClause === '') {
                continue;
            }

            if (!preg_match('/^([a-zA-Z0-9_]+)(?:\s+(ASC|DESC))?$/i', $orderClause, $matches)) {
                throw new \InvalidArgumentException('Invalid order clause.');
            }

            $column = $matches[1];
            if ($allowedLookup && !isset($allowedLookup[strtolower($column)])) {
                throw new \InvalidArgumentException('Invalid order column: ' . $column);
            }

            $direction = isset($matches[2]) ? strtoupper($matches[2]) : '';
            $clauses[] = '`' . $column . '`' . ($direction !== '' ? ' ' . $direction : '');
        }

        if (!$clauses) {
            return '';
        }

        return ' ORDER BY ' . implode(', ', $clauses);
    }

    public function buildLimitOffsetClause(mixed $limit = 0, mixed $offset = 0): string
    {
        $safeLimit = $this->toNonNegativeInt($limit, 'limit');
        $safeOffset = $this->toNonNegativeInt($offset, 'offset');

        $sql = '';
        if ($safeLimit > 0) {
            $sql .= ' LIMIT ' . $safeLimit;
        }

        if ($safeOffset > 0) {
            if ($safeLimit === 0) {
                $sql .= ' LIMIT 18446744073709551615';
            }
            $sql .= ' OFFSET ' . $safeOffset;
        }

        return $sql;
    }

    /**
     * Delete rows matching WHERE conditions
     *
     * Deletes one or more rows from the specified table based on WHERE conditions.
     * Supports comparison operators in field names (<, <=, >, >=, !=, LIKE).
     * Uses prepared statements to prevent SQL injection attacks.
     *
     * Security:
     * - All values are bound using prepared statements
     * - SQL injection protection is built-in
     *
     * @param string $table The table name to delete from
     * @param array $wheres Associative array of field => value pairs for WHERE conditions
     *                      Field names can include operators (e.g., 'age >' => 18)
     * @param string $orders Optional ORDER BY clause (e.g., 'created_at DESC')
     * @return int|false Number of affected rows, or false on failure
     *
     * @example
     *   $mysql->deleteWhere('users', ['status' => 'inactive']); // Delete inactive users
     *   $mysql->deleteWhere('logs', ['created_at <' => '2020-01-01'], 'created_at ASC');
     *   $mysql->deleteWhere('sessions', ['last_active <' => time() - 3600]); // Delete old sessions
     */
    public function deleteWhere(string $table, array $wheres, string $orders = '', array $options = [])
    {
        $connection = $this->connect('master');
        $orders = $options['orders'] ?? $orders;
        $orderBySql = $this->buildOrderByClause($orders, $options['allowedOrderColumns'] ?? []);
        list($wheresFields, $wheresValues) = $this->buildWhereClause($wheres);
        $sql = "DELETE FROM `{$table}` WHERE " . implode(' AND ', $wheresFields) . $orderBySql;
        $prepare = $this->connections['master']->prepare($sql);
        if (!$this->checkPrepare($prepare, $connection, $sql, $wheresValues)) {
            return false;
        }
        $success = $prepare->execute($wheresValues);
        $this->sendError($success, func_get_args());
        $this->sendErrorMessage($connection, $sql, $wheresValues);
        $return = $prepare->affected_rows;
        if ($this->autoClose) {
            $this->connections['master']->close();
            unset($this->connections['master']);
        }
        return $return;
    }

    /**
     * Insert a row into a table
     *
     * Inserts a new row into the specified table using the provided data.
     * Uses prepared statements to prevent SQL injection attacks.
     *
     * Return Value Behavior:
     * - When autoIncrement is true (default): Returns the last inserted auto-increment ID
     * - When autoIncrement is false: Returns the number of affected rows (typically 1)
     *
     * Use setAutoIncrement(false) for tables without AUTO_INCREMENT primary keys,
     * such as tables with composite keys or UUID-based primary keys.
     *
     * Security:
     * - All values are bound using prepared statements
     * - SQL injection protection is built-in
     *
     * @param string $table The table name to insert into
     * @param array $insertArray Associative array of field => value pairs to insert
     * @return int|false Last insert ID (autoIncrement=true) or affected rows (autoIncrement=false), or false on failure
     *
     * @example
     *   // Auto-increment table (returns last insert ID)
     *   $mysql->setAutoIncrement(true);
     *   $id = $mysql->insertArray('users', ['name' => 'John', 'email' => 'john@example.com']); // Returns 123
     *
     *   // Non auto-increment table (returns affected rows)
     *   $mysql->setAutoIncrement(false);
     *   $rows = $mysql->insertArray('user_roles', ['user_id' => 1, 'role_id' => 5]); // Returns 1
     */
    public function insertArray(string $table, array $insertArray)
    {
        $connection = $this->connect('master');
        $sql = "INSERT INTO `$table` (`" . implode('`, `', array_keys($insertArray)) . "`) VALUES (" . implode(', ', array_fill(0, count($insertArray), '?')) . ")";
        $prepare = $this->connections['master']->prepare($sql);
        if (!$this->checkPrepare($prepare, $connection, $sql, array_values($insertArray))) {
            return false;
        }
        $success = $prepare->execute(array_values($insertArray));
        $this->sendError($success, func_get_args());
        $this->sendErrorMessage($connection, $sql, array_values($insertArray));
        // Return lastInsertId for autoincrement tables, affectedRows for non-autoincrement
        $return = $this->autoIncrement ? $prepare->insert_id : $prepare->affected_rows;
        if ($this->autoClose) {
            $this->connections['master']->close();
            unset($this->connections['master']);
        }
        return $return;
    }

    /**
     * Insert a row or update if duplicate key exists
     *
     * Inserts a new row into the specified table, or updates specified fields if a
     * duplicate key violation occurs (INSERT ... ON DUPLICATE KEY UPDATE).
     * This is useful for upsert operations on tables with auto-increment primary keys.
     *
     * The updateArray supports three formats:
     * - Numeric key: Uses VALUES() function (e.g., [0 => 'count'] becomes `count` = VALUES(`count`))
     * - '[CUSTOM]' key: Raw SQL expression (e.g., ['[CUSTOM]' => 'count = count + 1'])
     * - String key: Field = value with prepared statement (e.g., ['status' => 'active'])
     *
     * Security:
     * - All values are bound using prepared statements
     * - SQL injection protection is built-in
     * - Be careful with '[CUSTOM]' key as it bypasses prepared statements
     *
     * @param string $table The table name to insert into
     * @param array $insertArray Associative array of field => value pairs to insert
     * @param array $updateArray Array of fields to update on duplicate key
     * @return int|false Number of affected rows (1 for insert, 2 for update), or false on failure
     *
     * @example
     *   // Update specific field on duplicate
     *   $mysql->insertArrayUpdateRow('users',
     *       ['email' => 'john@example.com', 'name' => 'John'],
     *       ['name' => 'John Updated']
     *   );
     *
     *   // Update using VALUES() function
     *   $mysql->insertArrayUpdateRow('stats',
     *       ['user_id' => 1, 'count' => 1],
     *       [0 => 'count'] // Uses VALUES(count)
     *   );
     *
     *   // Custom SQL expression
     *   $mysql->insertArrayUpdateRow('counters',
     *       ['key' => 'views', 'value' => 1],
     *       ['[CUSTOM]' => 'value = value + 1']
     *   );
     */
    public function insertArrayUpdateRow(string $table, array $insertArray, array $updateArray)
    {
        $connection = $this->connect('master');
        $updatesFields = [];
        $updatesValues = [];
        foreach ($updateArray as $updateArrayKey => $updateArrayValue) {
            if (is_int($updateArrayKey)) {
                $updatesFields[] = "`$updateArrayValue` = VALUES(`$updateArrayValue`)";
            } elseif ($updateArrayKey == '[CUSTOM]') {
                $updatesFields[] = $updateArrayValue;
            } else {
                $updatesFields[] = "`$updateArrayKey` = ?";
                $updatesValues[] = $updateArrayValue;
            }
        }
        $sql = "INSERT INTO `$table` (`" . implode('`, `', array_keys($insertArray)) . "`) VALUES (" . implode(', ', array_fill(0, count($insertArray), '?')) . ") ON DUPLICATE KEY UPDATE " . implode(', ', $updatesFields);
        $prepare = $this->connections['master']->prepare($sql);
        $insertArray = array_merge($insertArray, $updatesValues);
        if (!$this->checkPrepare($prepare, $connection, $sql, array_values($insertArray))) {
            return false;
        }
        $success = $prepare->execute(array_values($insertArray));
        $this->sendError($success, func_get_args());
        $this->sendErrorMessage($connection, $sql, array_values($insertArray));
        $return = $prepare->affected_rows;
        if ($this->autoClose) {
            $this->connections['master']->close();
            unset($this->connections['master']);
        }
        return $return;
    }

        /**
     * Replace a row in a table
     *
     * Replaces a row in the specified table using MySQL's REPLACE INTO statement.
     * If a row with the same primary/unique key exists, it is deleted and the new row is inserted.
     * If no matching row exists, the row is simply inserted.
     *
     * Note: REPLACE is effectively a DELETE followed by an INSERT, which means:
     * - Auto-increment values will increment even if replacing existing rows
     * - Foreign key constraints may be triggered
     * - Triggers for DELETE and INSERT will fire
     *
     * Security:
     * - All values are bound using prepared statements
     * - SQL injection protection is built-in
     *
     * @param string $table The table name to replace into
     * @param array $insertArray Associative array of field => value pairs
     * @return int|false Number of affected rows (1 for insert, 2 for replace), or false on failure
     *
     * @example
     *   $mysql->replaceArray('cache', [
     *       'key' => 'user_123',
     *       'value' => 'cached_data',
     *       'expires' => time() + 3600
     *   ]); // Returns 1 if new, 2 if replaced
     */
    public function replaceArray(string $table, array $insertArray)
    {
        $connection = $this->connect('master');
        $sql = "REPLACE INTO `$table` (`" . implode('`, `', array_keys($insertArray)) . "`) VALUES (" . implode(', ', array_fill(0, count($insertArray), '?')) . ")";
        $prepare = $this->connections['master']->prepare($sql);
        if (!$this->checkPrepare($prepare, $connection, $sql, array_values($insertArray))) {
            return false;
        }
        $success = $prepare->execute(array_values($insertArray));
        $this->sendError($success, func_get_args());
        $this->sendErrorMessage($connection, $sql, array_values($insertArray));
        $return = $prepare->affected_rows;
        if ($this->autoClose) {
            $this->connections['master']->close();
            unset($this->connections['master']);
        }
        return $return;
    }

    /**
     * Select a single row by primary key
     *
     * Retrieves a single row from the specified table using the primary key value.
     * Uses prepared statements to prevent SQL injection attacks.
     *
     * The options array supports:
     * - 'arrayKey': Return array keyed by this field (e.g., ['id' => [...row data...]])
     * - 'arrayValue': Return only this field's value instead of full row
     * - 'master': Use 'master' connection instead of default 'slave' (for read-after-write consistency)
     *
     * Security:
     * - All values are bound using prepared statements
     * - SQL injection protection is built-in
     *
     * @param string $table The table name to select from
     * @param string $id The primary key value to search for
     * @param array $options Optional array with 'arrayKey', 'arrayValue', and 'master' keys
     * @return array|string|false Row data as associative array, or specific value, or empty array if not found, or false on failure
     *
     * @example
     *   $mysql->setPrimaryKey('id');
     *   $user = $mysql->selectPrimaryKey('users', '123'); // Returns ['id' => 123, 'name' => 'John', ...]
     *   $name = $mysql->selectPrimaryKey('users', '123', ['arrayValue' => 'name']); // Returns 'John'
     *   $user = $mysql->selectPrimaryKey('users', '123', ['master' => 'master']); // Read from master
     */
    public function selectPrimaryKey(string $table, string $id, array $options = [])
    {
        $arrayKey = $options['arrayKey'] ?? '';
        $arrayValue = $options['arrayValue'] ?? '';
        $master = $options['master'] ?? 'slave';
        $connection = $this->connect($master);
        $sql = "SELECT * FROM " . $table . " WHERE " . $this->primaryKey . " = ?";
        $prepare = $this->connections[$master]->prepare($sql);
        if (!$this->checkPrepare($prepare, $connection, $sql, [$id])) {
            return false;
        }
        $success = $prepare->execute([$id]);
        $this->sendError($success, func_get_args());
        $this->sendErrorMessage($connection, $sql, [$id]);
        $query = $prepare->get_result();
        if ($this->autoClose) {
            $this->connections[$master]->close();
            unset($this->connections[$master]);
        }
        return $this->toArraySingle($query, $arrayKey, $arrayValue);
    }

    /**
     * Select multiple rows matching WHERE conditions
     *
     * Retrieves multiple rows from the specified table based on WHERE conditions.
     * Supports comparison operators in field names (<, <=, >, >=, !=, LIKE).
     * Uses prepared statements to prevent SQL injection attacks.
     *
     * The options array supports:
     * - 'arrayKey': Index results by this field (e.g., ['123' => [...], '124' => [...]])
     * - 'arrayValue': Return only this field's values (e.g., ['John', 'Jane', ...])
     * - 'master': Use 'master' connection instead of default 'slave'
     * - 'limit': Override the limit parameter
     * - 'offset': Number of rows to skip (for pagination)
     *
     * Security:
     * - All values are bound using prepared statements
     * - SQL injection protection is built-in
     *
     * @param string $table The table name to select from
     * @param array $wheres Associative array of field => value pairs for WHERE conditions (empty array = all rows)
     * @param string $orders Optional ORDER BY clause (e.g., 'created_at DESC, name ASC')
     * @param int $limit Optional LIMIT value (0 = no limit, can be overridden by options['limit'])
     * @param array $options Optional array with 'arrayKey', 'arrayValue', 'master', 'limit', and 'offset' keys
     * @return array|false Array of rows as associative arrays, or empty array if no results, or false on failure
     *
     * @example
     *   // Get all active users
     *   $users = $mysql->selectWhere('users', ['status' => 'active'], 'name ASC');
     *
     *   // Get users with age greater than 18
     *   $users = $mysql->selectWhere('users', ['age >' => 18], 'age DESC', 10);
     *
     *   // Get user IDs only
     *   $ids = $mysql->selectWhere('users', ['status' => 'active'], '', 0, ['arrayValue' => 'id']);
     *
     *   // Get users indexed by ID
     *   $users = $mysql->selectWhere('users', [], '', 0, ['arrayKey' => 'id']);
     *
     *   // Pagination
     *   $users = $mysql->selectWhere('users', [], 'created_at DESC', 20, ['offset' => 40]);
     */
    public function selectWhere(string $table, array $wheres = [], string $orders = '', int $limit = 0, array $options = [])
    {
        $options['orders'] = $options['orders'] ?? $orders;
        $options['limit'] = $options['limit'] ?? $limit;
        $options['offset'] = $options['offset'] ?? 0;
        $arrayKey = $options['arrayKey'] ?? '';
        $arrayValue = $options['arrayValue'] ?? '';
        $master = $options['master'] ?? 'slave';
        $orderBySql = $this->buildOrderByClause($options['orders'], $options['allowedOrderColumns'] ?? []);
        $fields = $options['fields'] ?? '*';
        $connection = $this->connect($master);
        $limitOffsetSql = $this->buildLimitOffsetClause($options['limit'], $options['offset']);
        list($wheresFields, $wheresValues) = $this->buildWhereClause($wheres);
        $fieldsSql = '*';
        if ($fields !== '*')
        {
            $fieldsSql = implode(',', array_map(function ($field) {
                $field = trim($field);
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $field))
                {
                    throw new \InvalidArgumentException('Invalid field name: ' . $field);
                }
                return '`' . $field . '`';
            }, explode(',', $fields)));
        }
        $sql = "SELECT {$fieldsSql} FROM `{$table}`" . ($wheresFields ? " WHERE " . implode(' AND ', $wheresFields) : "") . $orderBySql . $limitOffsetSql;
        $prepare = $this->connections[$master]->prepare($sql);
        if (!$this->checkPrepare($prepare, $connection, $sql, $wheresValues)) {
            return false;
        }
        $success = $prepare->execute($wheresValues);
        $this->sendError($success, func_get_args());
        $this->sendErrorMessage($connection, $sql, $wheresValues);
        $query = $prepare->get_result();
        if ($this->autoClose) {
            $this->connections[$master]->close();
            unset($this->connections[$master]);
        }
        return $this->toArray($query, $arrayKey, $arrayValue);
    }

    /**
     * Select a single row matching WHERE conditions
     *
     * Retrieves a single row from the specified table based on WHERE conditions.
     * Automatically adds LIMIT 1 to the query for efficiency.
     * Supports comparison operators in field names (<, <=, >, >=, !=, LIKE).
     * Uses prepared statements to prevent SQL injection attacks.
     *
     * The options array supports:
     * - 'arrayKey': Return array keyed by this field
     * - 'arrayValue': Return only this field's value instead of full row
     * - 'master': Use 'master' connection instead of default 'slave'
     * - 'offset': Number of rows to skip (useful with ORDER BY)
     *
     * Security:
     * - All values are bound using prepared statements
     * - SQL injection protection is built-in
     *
     * @param string $table The table name to select from
     * @param array $wheres Associative array of field => value pairs for WHERE conditions (empty array = first row)
     * @param string $orders Optional ORDER BY clause (e.g., 'created_at DESC')
     * @param array $options Optional array with 'arrayKey', 'arrayValue', 'master', and 'offset' keys
     * @return array|string|false Row data as associative array, or specific value, or empty array if not found, or false on failure
     *
     * @example
     *   // Get first active user
     *   $user = $mysql->selectWhereRow('users', ['status' => 'active'], 'created_at ASC');
     *
     *   // Get user's email only
     *   $email = $mysql->selectWhereRow('users', ['id' => 123], '', ['arrayValue' => 'email']);
     *
     *   // Get second newest user (with offset)
     *   $user = $mysql->selectWhereRow('users', [], 'created_at DESC', ['offset' => 1]);
     */
    public function selectWhereRow(string $table, array $wheres = [], string $orders = '', array $options = [])
    {
        $options['orders'] = $options['orders'] ?? $orders;
        $options['offset'] = $options['offset'] ?? 0;
        $arrayKey = $options['arrayKey'] ?? '';
        $arrayValue = $options['arrayValue'] ?? '';
        $master = $options['master'] ?? 'slave';
        $orderBySql = $this->buildOrderByClause($options['orders'], $options['allowedOrderColumns'] ?? []);
        $fields = $options['fields'] ?? '*';
        $connection = $this->connect($master);
        $offsetSql = $this->buildLimitOffsetClause(1, $options['offset']);
        list($wheresFields, $wheresValues) = $this->buildWhereClause($wheres);
        $fieldsSql = '*';
        if ($fields !== '*')
        {
            $fieldsSql = implode(',', array_map(function ($field) {
                $field = trim($field);
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $field))
                {
                    throw new \InvalidArgumentException('Invalid field name: ' . $field);
                }
                return '`' . $field . '`';
            }, explode(',', $fields)));
        }
        $sql = "SELECT {$fieldsSql} FROM `{$table}`" . ($wheresFields ? " WHERE " . implode(' AND ', $wheresFields) : "") . $orderBySql . $offsetSql;
        $prepare = $this->connections[$master]->prepare($sql);
        if (!$this->checkPrepare($prepare, $connection, $sql, $wheresValues)) {
            return false;
        }
        $success = $prepare->execute($wheresValues);
        $this->sendError($success, func_get_args());
        $this->sendErrorMessage($connection, $sql, $wheresValues);
        $query = $prepare->get_result();
        if ($this->autoClose) {
            $this->connections[$master]->close();
            unset($this->connections[$master]);
        }
        return $this->toArraySingle($query, $arrayKey, $arrayValue);
    }

    /**
     * Count rows matching WHERE conditions
     *
     * Returns the number of rows in the specified table that match the given conditions.
     * Supports comparison operators in field names (<, <=, >, >=, !=, LIKE).
     * Uses prepared statements to prevent SQL injection attacks.
     *
     * Security:
     * - All values are bound using prepared statements
     * - SQL injection protection is built-in
     *
     * @param string $table The table name to count from
     * @param array $wheres Associative array of field => value pairs for WHERE conditions (empty array = count all rows)
     * @param string $master Connection type: 'slave' (default) or 'master'
     * @return int|false Number of matching rows, or false on failure
     *
     * @example
     *   $total = $mysql->count('users'); // Count all users
     *   $active = $mysql->count('users', ['status' => 'active']); // Count active users
     *   $adults = $mysql->count('users', ['age >=' => 18]); // Count users 18 or older
     */
    public function count(string $table, array $wheres = [], string $master = 'slave')
    {
        $connection = $this->connect($master);
        list($wheresFields, $wheresValues) = $this->buildWhereClause($wheres);
        $sql = "SELECT COUNT(*) AS count FROM `{$table}`" . ($wheresFields ? " WHERE " . implode(' AND ', $wheresFields) : "");
        $prepare = $this->connections[$master]->prepare($sql);
        if (!$this->checkPrepare($prepare, $connection, $sql, $wheresValues)) {
            return false;
        }
        $success = $prepare->execute($wheresValues);
        $this->sendError($success, func_get_args());
        $this->sendErrorMessage($connection, $sql, $wheresValues);
        $query = $prepare->get_result();
        if ($this->autoClose) {
            $this->connections[$master]->close();
            unset($this->connections[$master]);
        }
        if (!$query) {
            return false;
        }
        $result = $query->fetch_assoc();
        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Log query execution failures
     *
     * Internal error logging method that writes to PHP error log when a query fails.
     * Called automatically after every query execution to capture failures.
     *
     * @param bool $return The return value from execute() (true on success, false on failure)
     * @param array $data The function arguments that were passed to the calling method
     * @return void
     */
    public function sendError($return, $data)
    {
        if (!$return) {
            error_log('[Mysql->sendError] [' . server('REQUEST_URI') . ']: ' . json_encode([$data, 'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)]));
        }
    }

    /**
     * Log MySQL error messages with query details
     *
     * Internal error logging method that writes MySQL errors to PHP error log.
     * Logs error number, error message, SQL query, and bound values for debugging.
     * Called automatically after every query execution to capture database errors.
     *
     * @param \mysqli $mysqli The MySQL connection object
     * @param string $sql The SQL query that was executed
     * @param array $vals The values that were bound to the prepared statement
     * @return void
     */
    public function sendErrorMessage($mysqli, $sql, $vals)
    {
        if (!$mysqli->errno) {
            return;
        }
        error_log('[Mysql->sendErrorMessage] [' . server('REQUEST_URI') . ']: ' . json_encode([$mysqli->errno, $mysqli->error, $sql, $vals, 'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)]));
    }

    /**
     * Validate prepared statement and log errors
     *
     * Checks if prepared statement creation succeeded and logs detailed error information
     * if it failed. This catches SQL syntax errors, invalid table/column names, and other
     * prepare-time issues before execution.
     *
     * @param \mysqli_stmt|false $prepare The prepared statement object or false on failure
     * @param \mysqli $connection The MySQL connection object
     * @param string $sql The SQL query that was attempted to be prepared
     * @param array $vals The values that would be bound to the prepared statement
     * @return bool True if prepare succeeded, false if it failed
     */
    public function checkPrepare($prepare, $connection, $sql, $vals)
    {
        if ($prepare === false) {
            error_log('[Mysql->checkPrepare] [' . server('REQUEST_URI') . ']: Bad query - prepare() failed: ' . json_encode([
                'error' => $connection->error,
                'errno' => $connection->errno,
                'sql' => $sql,
                'values' => $vals,
                'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
            ]));
            return false;
        }
        return true;
    }

    /**
     * Execute a custom SELECT query
     *
     * Executes a custom SQL read query with prepared statement parameter binding.
     * Useful for complex queries that cannot be built with the standard select methods.
     *
     * The options array supports:
     * - 'arrayKey': Index results by this field
     * - 'arrayValue': Return only this field's values
     * - 'master': Use 'master' connection instead of default 'slave'
     *
     * Security:
     * - All values are bound using prepared statements
     * - SQL injection protection is built-in
     * - Use ? placeholders in SQL and provide values in the values array
     *
     * @param string $sql The SQL SELECT query with ? placeholders for values
     * @param array $values Array of values to bind to the ? placeholders in order
     * @param array $options Optional array with 'arrayKey', 'arrayValue', and 'master' keys
     * @return array|false Array of rows as associative arrays, or empty array if no results, or false on failure
     *
     * @example
     *   $results = $mysql->sqlRead('SELECT * FROM users WHERE age > ? AND status = ?', [18, 'active']);
     *   $names = $mysql->sqlRead('SELECT name FROM users WHERE id IN (?, ?)', [1, 2], ['arrayValue' => 'name']);
     */
    public function sqlRead(string $sql, array $values = [], array $options = [])
    {
        $arrayKey = $options['arrayKey'] ?? '';
        $arrayValue = $options['arrayValue'] ?? '';
        $master = $options['master'] ?? 'slave';
        $connection = $this->connect($master);
        $prepare = $this->connections[$master]->prepare($sql);
        if (!$this->checkPrepare($prepare, $connection, $sql, array_values($values))) {
            return false;
        }
        $success = $prepare->execute(array_values($values));
        $this->sendError($success, func_get_args());
        $this->sendErrorMessage($connection, $sql, array_values($values));
        $query = $prepare->get_result();
        if ($this->autoClose) {
            $this->connections[$master]->close();
            unset($this->connections[$master]);
        }
        return $this->toArray($query, $arrayKey, $arrayValue);
    }

    /**
     * Execute a custom SELECT query for a single row
     *
     * Executes a custom SQL read query with prepared statement parameter binding
     * and returns only the first row. Useful for queries expected to return one result.
     *
     * The options array supports:
     * - 'arrayKey': Return array keyed by this field
     * - 'arrayValue': Return only this field's value instead of full row
     * - 'master': Use 'master' connection instead of default 'slave'
     *
     * Security:
     * - All values are bound using prepared statements
     * - SQL injection protection is built-in
     * - Use ? placeholders in SQL and provide values in the values array
     *
     * @param string $sql The SQL SELECT query with ? placeholders for values
     * @param array $values Array of values to bind to the ? placeholders in order
     * @param array $options Optional array with 'arrayKey', 'arrayValue', and 'master' keys
     * @return array|string|false Row data as associative array, or specific value, or empty array if not found, or false on failure
     *
     * @example
     *   $user = $mysql->sqlReadRow('SELECT * FROM users WHERE email = ?', ['john@example.com']);
     *   $count = $mysql->sqlReadRow('SELECT COUNT(*) as total FROM users WHERE status = ?', ['active'], ['arrayValue' => 'total']);
     */
    public function sqlReadRow(string $sql, array $values = [], array $options = [])
    {
        $arrayKey = $options['arrayKey'] ?? '';
        $arrayValue = $options['arrayValue'] ?? '';
        $master = $options['master'] ?? 'slave';
        $connection = $this->connect($master);
        $prepare = $this->connections[$master]->prepare($sql);
        if (!$this->checkPrepare($prepare, $connection, $sql, array_values($values))) {
            return false;
        }
        $success = $prepare->execute(array_values($values));
        $this->sendError($success, func_get_args());
        $this->sendErrorMessage($connection, $sql, array_values($values));
        $query = $prepare->get_result();
        if ($this->autoClose) {
            $this->connections[$master]->close();
            unset($this->connections[$master]);
        }
        return $this->toArraySingle($query, $arrayKey, $arrayValue);
    }

    /**
     * Execute a custom write query
     *
     * Executes a custom SQL write query (INSERT, UPDATE, DELETE) with prepared statement
     * parameter binding. Useful for complex queries that cannot be built with standard methods.
     *
     * Security:
     * - All values are bound using prepared statements
     * - SQL injection protection is built-in
     * - Use ? placeholders in SQL and provide values in the values array
     *
     * @param string $sql The SQL write query with ? placeholders for values
     * @param array $values Array of values to bind to the ? placeholders in order
     * @return int|false Number of affected rows, or false on failure
     *
     * @example
     *   $affected = $mysql->sqlWrite('UPDATE users SET status = ? WHERE last_login < ?', ['inactive', '2020-01-01']);
     *   $deleted = $mysql->sqlWrite('DELETE FROM logs WHERE created_at < ?', [date('Y-m-d', strtotime('-30 days'))]);
     */
    public function sqlWrite(string $sql, array $values = [])
    {
        $connection = $this->connect('master');
        $prepare = $this->connections['master']->prepare($sql);
        if (!$this->checkPrepare($prepare, $connection, $sql, array_values($values))) {
            return false;
        }
        $success = $prepare->execute(array_values($values));
        $this->sendError($success, func_get_args());
        $this->sendErrorMessage($connection, $sql, array_values($values));
        $return = $prepare->affected_rows;
        if ($this->autoClose) {
            $this->connections['master']->close();
            unset($this->connections['master']);
        }
        return $return;
    }

    /**
     * Convert MySQL result to array
     *
     * Internal method that converts a mysqli_result object into an array of associative arrays.
     * Supports different return formats based on arrayKey and arrayValue parameters.
     *
     * @param \mysqli_result|false $mysqli The MySQL result object
     * @param string $arrayKey Optional field name to use as array keys
     * @param string $arrayValue Optional field name to return only that field's values
     * @return array Formatted result array based on parameters:
     *               - No params: Array of associative arrays (all rows with all fields)
     *               - arrayKey only: Associative array indexed by that field
     *               - arrayValue only: Flat array of that field's values
     *               - Both: Associative array [key => value] pairs
     */
    function toArray($mysqli, string $arrayKey = '', string $arrayValue = '')
    {
        $return = [];
        if ($mysqli && $mysqli->num_rows) {
            if ($arrayKey && $arrayValue) {
                while ($mysqliRow = $mysqli->fetch_assoc()) {
                    $return[$mysqliRow[$arrayKey]] = $mysqliRow[$arrayValue];
                }
            } elseif ($arrayKey) {
                while ($mysqliRow = $mysqli->fetch_assoc()) {
                    $return[$mysqliRow[$arrayKey]] = $mysqliRow;
                }
            } elseif ($arrayValue) {
                while ($mysqliRow = $mysqli->fetch_assoc()) {
                    $return[] = $mysqliRow[$arrayValue];
                }
            } else {
                $return = $mysqli->fetch_all(MYSQLI_ASSOC);
            }
        }
        return $return;
    }

    /**
     * Convert MySQL result to single row array
     *
     * Internal method that converts a mysqli_result object into a single associative array.
     * Supports different return formats based on arrayKey and arrayValue parameters.
     *
     * @param \mysqli_result|false $mysqli The MySQL result object
     * @param string $arrayKey Optional field name to use as array key
     * @param string $arrayValue Optional field name to return only that field's value
     * @return array|string|mixed Formatted result based on parameters:
     *                             - No params: Single associative array (one row with all fields)
     *                             - arrayKey only: [key => row] format
     *                             - arrayValue only: Just the value of that field
     *                             - Both: [key => value] format
     */
    function toArraySingle($mysqli, string $arrayKey = '', string $arrayValue = '')
    {
        $return = [];
        if ($mysqli && $mysqli->num_rows) {
            $return = $mysqli->fetch_assoc();
            if ($arrayKey && $arrayValue) {
                return [$return[$arrayKey] => $return[$arrayValue]];
            }
            if ($arrayKey) {
                return [$return[$arrayKey] => $return];
            }
            if ($arrayValue) {
                return $return[$arrayValue];
            }
        }
        return $return;
    }

    /**
     * Update rows matching WHERE conditions
     *
     * Updates one or more rows in the specified table based on WHERE conditions.
     * Supports comparison operators in field names (<, <=, >, >=, !=, LIKE).
     * Uses prepared statements to prevent SQL injection attacks.
     *
     * The updates array supports:
     * - String key: Field = value with prepared statement (e.g., ['status' => 'active'])
     * - Numeric key: Raw SQL expression (e.g., [0 => 'count = count + 1'])
     * - NULL value: Sets field to NULL (e.g., ['deleted_at' => null])
     *
     * Security:
     * - All values are bound using prepared statements
     * - SQL injection protection is built-in
     * - Be careful with numeric keys as they bypass prepared statements
     *
     * @param string $table The table name to update
     * @param array $updates Associative array of field => value pairs to update
     * @param array $wheres Associative array of field => value pairs for WHERE conditions
     * @return int|false Number of affected rows, or false on failure
     *
     * @example
     *   // Update specific fields
     *   $mysql->updateWhere('users', ['status' => 'active', 'verified' => 1], ['email' => 'john@example.com']);
     *
     *   // Increment a counter
     *   $mysql->updateWhere('posts', [0 => 'views = views + 1'], ['id' => 123]);
     *
     *   // Set field to NULL
     *   $mysql->updateWhere('sessions', ['ended_at' => null], ['user_id' => 1]);
     *
     *   // Update with comparison operators
     *   $mysql->updateWhere('users', ['status' => 'inactive'], ['last_login <' => '2020-01-01']);
     */
    public function updateWhere(string $table, array $updates, array $wheres)
    {
        $connection = $this->connect('master');
        $updatesFields = [];
        $updatesValues = [];
        foreach ($updates as $field => $value) {
            if (is_null($value)) {
                $updatesFields[] = "`$field` = NULL";
            } elseif (is_numeric($field)) {
                $updatesFields[] = $value;
            } else {
                $updatesFields[] = "`$field` = ?";
                $updatesValues[] = $value;
            }
        }
        list($wheresFields, $wheresValues) = $this->buildWhereClause($wheres);
        // Require WHERE clause to prevent accidental whole-table updates
        if (empty($wheresFields)) {
            trigger_error("updateWhere() called with empty WHERE clause on table '{$table}'. Use a specific update method for whole-table updates.", E_USER_WARNING);
            return false;
        }
        $sql = "UPDATE `{$table}` SET " . implode(', ', $updatesFields) . " WHERE " . implode(' AND ', $wheresFields);
        $prepare = $this->connections['master']->prepare($sql);
        if (!$this->checkPrepare($prepare, $connection, $sql, array_merge($updatesValues, $wheresValues))) {
            return false;
        }
        $success = $prepare->execute(array_merge($updatesValues, $wheresValues));
        $this->sendError($success, func_get_args());
        $this->sendErrorMessage($connection, $sql, array_merge($updatesValues, $wheresValues));
        $return = $prepare->affected_rows;
        if ($this->autoClose) {
            $this->connections['master']->close();
            unset($this->connections['master']);
        }
        return $return;
    }
}
