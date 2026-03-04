<?php
    namespace DatabasesSchemes;

    /**
     * Base - Base Database Abstraction Layer
     *
     * This abstract class serves as the foundation for all database abstraction classes
     * in the RAVE framework. It provides common functionality shared by PrimaryKey,
     * PrimaryKeyComposite, and General classes.
     *
     * PURPOSE:
     * Base consolidates the shared initialization and SQL execution methods that are
     * common across all database abstraction patterns. This reduces code duplication
     * and ensures consistent behavior across all database classes.
     *
     * ARCHITECTURE:
     * This is an abstract base class that should not be instantiated directly.
     * Child classes extend Base and add their specific functionality:
     * - PrimaryKey: For tables with single-field primary keys
     * - PrimaryKeyComposite: For tables with composite (multi-field) primary keys
     * - General: For tables without primary key abstractions
     *
     * COMMON PROPERTIES:
     * - $database: The database name to connect to
     * - $sql: The MySQL connector instance
     * - $tableName: The table name this class represents
     *
     * COMMON METHODS:
     * - __construct(): Initializes the database connection
     * - sqlRead(): Executes custom SELECT queries
     * - sqlReadRow(): Executes custom SELECT queries returning a single row
     * - sqlWrite(): Executes custom INSERT, UPDATE, or DELETE queries
     *
     * @package DatabasesSchemes
     * @author RAVE Framework
     * @see PrimaryKey For tables with single-field primary keys
     * @see PrimaryKeyComposite For tables with composite primary keys
     * @see General For tables without primary key abstractions
     */
    abstract class Base {

        protected string $database;
        protected $sql;
        protected string $tableName;

        protected function getAllowedOrderColumns(): array
        {
            // Most database classes define a canonical $fields allowlist.
            // Fall back to primary key metadata only when $fields is missing.
            if (property_exists($this, 'fields') && is_array($this->fields ?? null) && $this->fields) {
                return array_values(array_unique(array_filter($this->fields, static fn ($column) => is_string($column) && $column !== '')));
            }

            $columns = [];
            if (property_exists($this, 'primaryKey')) {
                if (is_array($this->primaryKey)) {
                    $columns = $this->primaryKey;
                } elseif (is_string($this->primaryKey) && $this->primaryKey !== '') {
                    $columns = [$this->primaryKey];
                }
            }

            return array_values(array_unique(array_filter($columns, static fn ($column) => is_string($column) && $column !== '')));
        }

        /**
         * Initialize the database connection
         *
         * Constructor that establishes a MySQL connection for the specified database.
         * Called automatically when a child class is instantiated.
         */
        public function __construct()
        {
            $this->database = $this->resolveDatabaseName();
            $this->sql = \Connectors\Mysql::init($this->database);
        }

        /**
         * Resolve database name with optional environment override.
         *
         * For tests/integration environments, set TEST_DB_NAME to route all
         * DatabasesSchemes classes to an alternate schema (for example
         * `quiltsco_test`) without editing each Databases/* class.
         *
         * @return string
         */
        protected function resolveDatabaseName(): string
        {
            $overrideDatabase = getenv('TEST_DB_NAME');
            if ($overrideDatabase === false) {
                return $this->database;
            }

            $overrideDatabase = trim($overrideDatabase);
            if ($overrideDatabase === '') {
                return $this->database;
            }

            return $overrideDatabase;
        }



        protected function buildLimitOffsetClause(int|string $limit = 0, int|string $offset = 0): string
        {
            return $this->sql->buildLimitOffsetClause($limit, $offset);
        }
        /**
         * Execute a custom SELECT query with prepared statements
         *
         * Executes a custom SQL SELECT query and returns multiple rows. This method provides
         * flexibility for complex queries while maintaining security through prepared statements.
         * Use placeholders (?) in the SQL and provide values as an array parameter.
         *
         * @param string $sql Custom SQL SELECT query with ? placeholders for values
         * @param array $values Array of values to bind to the placeholders in order
         * @param array $options Optional: 'arrayKey', 'arrayValue', 'master'
         * @return array Array of records returned by the query
         */
        public function sqlRead(string $sql, array $values = [], array $options = [])
        {
            return $this->sql->sqlRead($sql, $values, $options);
        }

        /**
         * Execute a custom SELECT query and return a single row
         *
         * Executes a custom SQL SELECT query and returns only the first row. This is useful
         * for queries that should return a single record or when you only need the first result.
         * Uses prepared statements for security with ? placeholders.
         *
         * @param string $sql Custom SQL SELECT query with ? placeholders for values
         * @param array $values Array of values to bind to the placeholders in order
         * @param array $options Optional: 'arrayValue', 'master'
         * @return array|null Associative array of the first record, or null if no results
         */
        public function sqlReadRow(string $sql, array $values = [], array $options = [])
        {
            return $this->sql->sqlReadRow($sql, $values, $options);
        }

        /**
         * Execute a custom write query (INSERT, UPDATE, DELETE)
         *
         * Executes a custom SQL write operation using prepared statements. This method
         * operates on the master database and allows flexibility for complex write operations.
         * Use placeholders (?) for all dynamic values.
         *
         * @param string $sql Custom SQL write query with ? placeholders for values
         * @param array $values Array of values to bind to the placeholders in order
         * @return mixed Number of affected rows for UPDATE/DELETE, last insert ID for INSERT,
         *               or false on failure
         */
        public function sqlWrite(string $sql, array $values = [])
        {
            return $this->sql->sqlWrite($sql, $values);
        }

    }
