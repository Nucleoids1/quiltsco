<?php
    namespace DatabasesSchemes;

    /**
     * General - Minimal Database Abstraction Layer for Custom SQL
     *
     * This class provides a lightweight database interface for tables that don't fit the
     * primary key abstraction patterns (PrimaryKey or PrimaryKeyComposite). It offers only
     * the essential SQL execution methods with prepared statement security, giving you full
     * control over your queries while maintaining security best practices.
     *
     * PURPOSE:
     * General serves as a base class for database table abstractions that require custom SQL
     * queries without the overhead of primary key-based CRUD methods. It's designed for:
     * - Tables with complex or non-standard primary key structures
     * - Tables where you need direct SQL control for performance or flexibility
     * - Read-only tables or views where standard CRUD operations don't apply
     * - Specialized use cases that don't fit PrimaryKey or PrimaryKeyComposite patterns
     *
     * WHEN TO USE:
     * - Use General when your table doesn't have a clear primary key abstraction
     * - When you need maximum flexibility with custom SQL queries
     * - For read-only tables, views, or complex queries with JOINs
     * - When the overhead of PrimaryKey methods is unnecessary
     *
     * WHEN NOT TO USE:
     * - If your table has a SINGLE primary key field, use PrimaryKey instead
     * - If your table has a COMPOSITE primary key, use PrimaryKeyComposite instead
     * - PrimaryKey and PrimaryKeyComposite provide convenient CRUD methods that reduce boilerplate
     *
     * KEY DIFFERENCES FROM PrimaryKey/PrimaryKeyComposite:
     * - General: Only provides sqlRead(), sqlReadRow(), and sqlWrite() methods
     *   You write all SQL queries manually (with prepared statement placeholders)
     * - PrimaryKey/PrimaryKeyComposite: Provide high-level methods (insertArray, selectWhere, etc.)
     *   that generate SQL automatically based on your parameters
     *
     * ARCHITECTURE:
     * Child classes extend General and define minimal table-specific properties:
     *   protected string $database = 'main';       // Database name
     *   protected string $tableName = 'logs';      // Table name (for reference, not used by methods)
     *
     * Note: The $tableName property is not automatically used by General methods. It's included
     * for consistency and can be referenced in your child class implementations if needed.
     *
     * SECURITY FEATURES:
     * - All queries use prepared statements via the MySQL connector
     * - User input is automatically bound as parameters with ? placeholders, preventing SQL injection
     * - No raw SQL concatenation - you must use placeholders for dynamic values
     *
     * MASTER/SLAVE DATABASE ROUTING:
     * - Write operations (INSERT, UPDATE, DELETE via sqlWrite) always use master database
     * - Read operations (SELECT via sqlRead/sqlReadRow) default to slave database
     * - Can force reads to use master via $options['master'] = 'master'
     *
     * OPTIONS ARRAY:
     * Read methods accept an $options array with these keys:
     * - 'arrayKey': (string) Return associative array with this column as the key
     * - 'arrayValue': (string) Return only this column's value instead of full rows
     * - 'master': (string) 'master' to force read from master DB, 'slave' for replica
     *
     * COMMON USAGE EXAMPLES:
     *
     * // Create a table class for a log table
     * namespace Databases;
     * class Logs extends \DatabasesSchemes\General {
     *     protected string $database = 'main';
     *     protected string $tableName = 'logs';
     * }
     *
     * // Insert a log entry
     * (new \Databases\Logs())->sqlWrite(
     *     "INSERT INTO logs (posted_on, info, level) VALUES (?, ?, ?)",
     *     [time(), 'User logged in', 'INFO']
     * );
     *
     * // Read recent logs
     * $recentLogs = (new \Databases\Logs())->sqlRead(
     *     "SELECT * FROM logs WHERE posted_on > ? ORDER BY posted_on DESC LIMIT ?",
     *     [strtotime('-1 day'), 100]
     * );
     *
     * // Complex query with JOIN
     * $results = (new \Databases\Logs())->sqlRead(
     *     "SELECT l.*, u.username
     *      FROM logs l
     *      LEFT JOIN users u ON l.user_id = u.id
     *      WHERE l.level = ? AND l.posted_on > ?",
     *     ['ERROR', strtotime('-1 hour')]
     * );
     *
     * // Get a single aggregate result
     * $stats = (new \Databases\Logs())->sqlReadRow(
     *     "SELECT COUNT(*) as total, MAX(posted_on) as latest
     *      FROM logs
     *      WHERE level = ?",
     *     ['ERROR']
     * );
     * echo "Total errors: {$stats['total']}";
     *
     * // Update records
     * (new \Databases\Logs())->sqlWrite(
     *     "UPDATE logs SET processed = 1 WHERE posted_on < ? AND level = ?",
     *     [strtotime('-7 days'), 'INFO']
     * );
     *
     * // Delete old logs
     * (new \Databases\Logs())->sqlWrite(
     *     "DELETE FROM logs WHERE posted_on < ?",
     *     [strtotime('-30 days')]
     * );
     *
     * @package DatabasesSchemes
     * @author RAVE Framework
     * @see PrimaryKey For tables with single-field primary keys
     * @see PrimaryKeyComposite For tables with composite primary keys
     */
    class General extends Base {

        /**
         * Insert a new record into the table
         *
         * Convenience method for inserting records into tables without primary key abstractions.
         * This method provides a simple way to insert data without writing custom SQL queries.
         * Uses prepared statements for security.
         *
         * @param array $insertArray Associative array of field names and values to insert
         *                           Keys are column names, values are the data to insert
         *                           Example: ['posted_on' => '2024-01-01', 'info' => 'Log message']
         * @return int Number of rows affected (typically 1 on success)
         *
         * @example
         * // Insert a log entry
         * $rows = (new \Databases\Logs())->insertArray([
         *     'posted_on' => date('Y-m-d H:i:s'),
         *     'info' => 'User logged in'
         * ]);
         *
         * @example
         * // Insert a search record
         * (new \Databases\Searches())->insertArray([
         *     'query' => 'php database',
         *     'timestamp' => time(),
         *     'user_ip' => server('REMOTE_ADDR')
         * ]);
         */
        public function insertArray(array $insertArray)
        {
            $this->sql->setAutoIncrement(false);
            $return = $this->sql->insertArray($this->tableName, $insertArray);
            return $return;
        }

        /**
         * Delete records matching WHERE conditions
         *
         * Convenience method for deleting records from tables without primary key abstractions.
         * This method provides a simple way to delete data based on conditions without writing
         * custom SQL queries. Uses prepared statements for security.
         *
         * @param array $wheres Associative array of WHERE conditions
         *                      Supports operators: =, <, >, <=, >=, !=, <>, LIKE, IN, NOT IN
         *                      Examples:
         *                      - ['posted_on <' => '2024-01-01'] for less than
         *                      - ['status' => 'active'] for equality
         *                      - ['id IN' => [1,2,3]] for IN clause
         * @param string $orders Optional ORDER BY clause (e.g., 'posted_on DESC')
         *                      Used to control which records are deleted when combined with LIMIT
         * @return int Number of rows deleted
         *
         * @example
         * // Delete old log entries
         * $deleted = (new \Databases\Logs())->deleteWhere([
         *     'posted_on <' => date('Y-m-d H:i:s', strtotime('-30 days'))
         * ]);
         * echo "Deleted $deleted old log entries";
         *
         * @example
         * // Delete specific search records
         * (new \Databases\Searches())->deleteWhere([
         *     'user_ip' => '127.0.0.1',
         *     'timestamp <' => strtotime('-7 days')
         * ], 'timestamp ASC');
         */
        public function deleteWhere(array $wheres, string $orders = '')
        {
            $options = [
                'orders' => $orders,
                'allowedOrderColumns' => $this->getAllowedOrderColumns(),
            ];
            $return = $this->sql->deleteWhere($this->tableName, $wheres, $orders, $options);
            return $return;
        }

    }
