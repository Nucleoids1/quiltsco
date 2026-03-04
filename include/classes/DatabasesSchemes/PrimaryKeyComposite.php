<?php
    namespace DatabasesSchemes;

    /**
     * PrimaryKeyComposite - Composite Primary Key Database Abstraction Layer
     *
     * This class provides a secure ORM-like interface for database tables with composite
     * (multi-field) primary keys. It wraps the MySQL connector with a simplified API that
     * automatically handles common CRUD operations using prepared statements for security.
     *
     * PURPOSE:
     * PrimaryKeyComposite serves as the base class for database table abstractions that have
     * composite primary keys (multiple fields forming the primary key). Common examples include
     * junction tables, association tables, or any table where uniqueness is defined by a
     * combination of fields (e.g., user_id + group_id, or user_id + link_id + timestamp).
     *
     * WHEN TO USE:
     * - Use PrimaryKeyComposite when your table has a COMPOSITE primary key (multiple fields)
     * - Ideal for junction/association tables (e.g., user_groups, permissions, activity logs)
     * - Tables where uniqueness is defined by a combination of fields
     *
     * WHEN NOT TO USE:
     * - If your table has a SINGLE primary key field, use PrimaryKey instead
     * - Example: A simple users table with primary key 'user_id' should use PrimaryKey
     * - If your table has no primary key structure at all, use General instead
     *
     * KEY DIFFERENCES FROM PrimaryKey:
     * - PrimaryKey: Single primary key field (e.g., 'id')
     *   Methods like selectPrimaryKey() accept a single string value
     * - PrimaryKeyComposite: Multiple primary key fields (e.g., 'user_id' + 'group_id')
     *
     * ARCHITECTURE:
     * Child classes extend PrimaryKeyComposite and define their table-specific properties:
     *   protected string $database = 'main';                      // Database name
     *   protected string $tableName = 'user_groups';              // Table name
     *   protected array $primaryKey = ['user_id', 'group_id'];    // Composite primary key fields
     *
     * SECURITY FEATURES:
     * - All queries use prepared statements via the MySQL connector
     * - User input is automatically bound as parameters, preventing SQL injection
     * - No raw SQL concatenation in standard CRUD methods
     * - Custom queries via sqlRead/sqlWrite also support prepared statement parameters
     *
     * MASTER/SLAVE DATABASE ROUTING:
     * - Write operations (INSERT, UPDATE, DELETE) always use master database
     * - Read operations (SELECT) default to slave database for load balancing
     * - Can force reads to use master via $options['master'] = 'master'
     *
     * OPTIONS ARRAY:
     * Many methods accept an $options array with these common keys:
     * - 'arrayKey': (string) Return associative array with this column as the key
     * - 'arrayValue': (string) Return only this column's value instead of full rows
     * - 'master': (string) 'master' to force read from master DB, 'slave' for replica
     * - 'offset': (int) Skip this many records (for pagination)
     * - 'limit': (int) Maximum number of records to return
     *
     * COMMON USAGE EXAMPLES:
     *
     * // Create a table class for a junction table
     * namespace Databases;
     * class UserGroups extends \DatabasesSchemes\PrimaryKeyComposite {
     *     protected string $database = 'main';
     *     protected string $tableName = 'user_groups';
     *     protected array $primaryKey = ['user_id', 'group_id'];
     * }
     *
     * // Insert a new association (composite insert)
     * (new \Databases\UserGroups())->insertArray([
     *     'user_id' => 123,
     *     'group_id' => 456,
     *     'joined_at' => date('Y-m-d H:i:s')
     * ]);
     *
     * // Insert or update on duplicate composite key
     * (new \Databases\UserGroups())->insertArrayUpdateRow(
     *     ['user_id' => 123, 'group_id' => 456],  // Composite key
     *     ['last_activity' => date('Y-m-d H:i:s')] // Update on duplicate
     * );
     *
     * // Fetch all groups for a user
     * $userGroups = (new \Databases\UserGroups())->selectWhere(
     *     ['user_id' => 123],
     *     'group_id ASC'
     * );
     *
     * // Update a composite key record
     * (new \Databases\UserGroups())->updateWhere(
     *     ['last_activity' => date('Y-m-d H:i:s')],
     *     ['user_id' => 123, 'group_id' => 456]
     * );
     *
     * // Delete a composite key record
     * (new \Databases\UserGroups())->deleteWhere([
     *     'user_id' => 123,
     *     'group_id' => 456
     * ]);
     *
     * // Count records for a user
     * $groupCount = (new \Databases\UserGroups())->count(['user_id' => 123]);
     *
     * @package DatabasesSchemes
     * @author RAVE Framework
     * @see PrimaryKey For tables with single-field primary keys
     * @see General For tables without primary key abstractions
     */
    class PrimaryKeyComposite extends Base {

        protected array $primaryKey;

        /**
         * Delete records matching WHERE conditions
         *
         * Deletes one or more records from the table that match the specified conditions.
         * This operation is performed on the master database and uses prepared statements.
         * Optional ordering allows for controlled deletion when combined with LIMIT.
         *
         * @param array $wheres Associative array of WHERE conditions (field => value)
         *                      Example: ['user_id' => 123, 'group_id' => 456]
         * @param string $orders Optional ORDER BY clause (e.g., 'created_at DESC')
         *                       Useful when deleting a subset of matching records
         * @return int Number of records deleted
         *
         * @example
         * // Delete a specific composite key record
         * $deleted = (new \Databases\UserGroups())->deleteWhere([
         *     'user_id' => 123,
         *     'group_id' => 456
         * ]);
         *
         * @example
         * // Delete all groups for a user
         * $deleted = (new \Databases\UserGroups())->deleteWhere([
         *     'user_id' => 123
         * ]);
         */
        public function deleteWhere(array $wheres, string $orders = '')
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            $options = [
                'orders' => $orders,
                'allowedOrderColumns' => $this->getAllowedOrderColumns(),
            ];
            $return = $this->sql->deleteWhere($this->tableName, $wheres, $orders, $options);
            return $return;
        }

        /**
         * Insert a new record with composite primary key
         *
         * Inserts a new record into the table. Composite key tables require all primary key
         * fields to be provided in the insert array. Returns affected rows (not insert ID,
         * since composite key tables have no auto-increment).
         *
         * @param array $insertArray Associative array of field names and values to insert
         *                          Must include all composite primary key fields
         *                          Example: ['user_id' => 123, 'group_id' => 456, 'role' => 'member']
         * @return int Number of affected rows (typically 1), or false on failure
         */
        public function insertArray(array $insertArray)
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            $this->sql->setAutoIncrement(false);
            $return = $this->sql->insertArray($this->tableName, $insertArray);
            return $return;
        }

        /**
         * Insert a new record or update on duplicate composite key
         *
         * Attempts to insert a new record. If a record with the same composite primary key
         * already exists, updates the existing record with the values from $updateArray.
         * This is useful for "upsert" operations on composite key tables.
         *
         * @param array $insertArray Associative array of field names and values to insert
         *                          Must include all composite primary key fields
         * @param array $updateArray Associative array of fields to update if the composite key exists
         *                          Should NOT include primary key fields
         * @return bool True if insert or update was successful
         *
         * @example
         * // Insert new activity or update timestamp if exists
         * (new \Databases\Activity())->insertArrayUpdateRow(
         *     ['user_id' => 123, 'link_id' => 456, 'last_activity' => time()],
         *     ['last_activity' => time()]  // Update only timestamp on duplicate
         * );
         *
         * @example
         * // Track user group membership with last access
         * (new \Databases\UserGroups())->insertArrayUpdateRowE(
         *     ['user_id' => 123, 'group_id' => 456, 'joined_at' => date('Y-m-d H:i:s')],
         *     ['last_access' => date('Y-m-d H:i:s')]  // Update last_access on duplicate
         * );
         */
        public function insertArrayUpdateRow(array $insertArray, array $updateArray)
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            $return = $this->sql->insertArrayUpdateRow($this->tableName, $insertArray, $updateArray);
            return $return;
        }

        /**
         * Replace a record (delete and insert) with composite primary key
         *
         * Performs a REPLACE operation which deletes any existing record with the same
         * composite primary key and inserts the new record. This is different from update
         * as it completely replaces the record rather than modifying specific fields.
         *
         * @param array $insertArray Associative array of field names and values
         *                          Must include all composite primary key fields
         *                          All fields should be provided as the old record is deleted
         * @return bool True if the replace was successful
         *
         * @example
         * // Replace an entire user-group record
         * (new \Databases\UserGroups())->replaceArray([
         *     'user_id' => 123,
         *     'group_id' => 456,
         *     'role' => 'admin',      // New role
         *     'joined_at' => date('Y-m-d H:i:s')  // Reset join date
         * ]);
         */
        public function replaceArray(array $insertArray)
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            $return = $this->sql->replaceArray($this->tableName, $insertArray);
            return $return;
        }

        /**
         * Select all records with optional ordering
         *
         * @param string $orders String of column names to ORDER BY (e.g., 'id' or 'id DESC' or 'id, name')
         * @param int $limit Maximum number of records to return (0 for no limit)
         * @param array $options Optional: Additional options such as 'arrayKey', 'arrayValue', 'master', 'offset'
         * @return array Array of all records
         */
        public function selectAll(string $orders = '', int $limit = 0, array $options = [])
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            return $this->sql->selectWhere($this->tableName, [], $orders, $limit, $options);
        }

        /**
         * Select records matching WHERE conditions
         *
         * Retrieves records that match the provided WHERE conditions. This is the main
         * query method for filtering records by any field, not just the composite primary key.
         *
         * @param array $values Associative array of WHERE conditions (field => value)
         *                     Empty array [] returns all records (equivalent to selectAll)
         * @param string $orders String of column names to ORDER BY (e.g., 'user_id ASC, created_at DESC')
         * @param int $limit Maximum number of records to return (0 for no limit)
         * @param array $options Optional: 'arrayKey', 'arrayValue', 'master', 'offset'
         * @return array Array of matching records
         *
         * @example
         * // Get all groups for a user
         * $groups = (new \Databases\UserGroups())->selectWhere(
         *     ['user_id' => 123],
         *     'group_id ASC'
         * );
         *
         * @example
         * // Get active memberships, return as associative array keyed by group_id
         * $activeGroups = (new \Databases\UserGroups())->selectWhere(
         *     ['user_id' => 123, 'status' => 'active'],
         *     'joined_at DESC',
         *     10,
         *     ['arrayKey' => 'group_id']
         * );
         */
        public function selectWhere(array $values, string $orders = '', int $limit = 0, array $options = [])
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            $options['orders'] = $options['orders'] ?? $orders;
            $options['limit'] = $options['limit'] ?? $limit;
            $options['allowedOrderColumns'] = $options['allowedOrderColumns'] ?? $this->getAllowedOrderColumns();
            $return = $this->sql->selectWhere($this->tableName, $values, $orders, $limit, $options);
            return $return;
        }

        /**
         * Select a single record (row) matching WHERE conditions
         *
         * Retrieves only the first record that matches the provided WHERE conditions.
         * This is useful when you expect only one result or want to limit the result to one row.
         *
         * @param array $values Associative array of WHERE conditions (field => value)
         * @param string $orders Optional ORDER BY clause to determine which row is returned first
         * @param array $options Optional: 'arrayValue', 'master'
         *                      Note: 'arrayKey' has no effect as only one row is returned
         * @return array|null Associative array of the first matching record, or null if no match
         *
         * @example
         * // Get a specific user-group record
         * $membership = (new \Databases\UserGroups())->selectWhereRow([
         *     'user_id' => 123,
         *     'group_id' => 456
         * ]);
         * if ($membership) {
         *     echo "User is a {$membership['role']} in this group";
         * }
         *
         * @example
         * // Get the most recent activity record for a user
         * $lastActivity = (new \Databases\Activity())->selectWhereRow(
         *     ['user_id' => 123],
         *     'last_activity DESC'
         * );
         */
        public function selectWhereRow(array $values, string $orders = '', array $options = [])
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            $options['orders'] = $options['orders'] ?? $orders;
            $options['allowedOrderColumns'] = $options['allowedOrderColumns'] ?? $this->getAllowedOrderColumns();
            $return = $this->sql->selectWhereRow($this->tableName, $values, $orders, $options);
            return $return;
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
         *
         * @example
         * // Custom JOIN query across tables
         * $results = (new \Databases\UserGroups())->sqlRead(
         *     "SELECT ug.*, g.name AS group_name
         *      FROM user_groups ug
         *      JOIN groups g ON ug.group_id = g.id
         *      WHERE ug.user_id = ? AND ug.status = ?",
         *     [123, 'active']
         * );
         *
         * @example
         * // Complex aggregation query
         * $stats = (new \Databases\Activity())->sqlRead(
         *     "SELECT user_id, COUNT(*) as activity_count
         *      FROM activity
         *      WHERE last_activity > ?
         *      GROUP BY user_id
         *      HAVING activity_count > ?",
         *     [strtotime('-7 days'), 10]
         * );
         */
        public function sqlRead(string $sql, array $values = [], array $options = [])
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            return parent::sqlRead($sql, $values, $options);
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
         *                      Note: 'arrayKey' has no effect as only one row is returned
         * @return array|null Associative array of the first record, or null if no results
         *
         * @example
         * // Get a specific record with JOIN
         * $membership = (new \Databases\UserGroups())->sqlReadRow(
         *     "SELECT ug.*, g.name AS group_name
         *      FROM user_groups ug
         *      JOIN groups g ON ug.group_id = g.id
         *      WHERE ug.user_id = ? AND ug.group_id = ?",
         *     [123, 456]
         * );
         *
         * @example
         * // Get aggregate statistics for a user
         * $stats = (new \Databases\Activity())->sqlReadRow(
         *     "SELECT COUNT(*) as total, MAX(last_activity) as last_seen
         *      FROM activity
         *      WHERE user_id = ?",
         *     [123]
         * );
         */
        public function sqlReadRow(string $sql, array $values = [], array $options = [])
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            return parent::sqlReadRow($sql, $values, $options);
        }

        /**
         * Execute a custom write query (INSERT, UPDATE, DELETE)
         *
         * Executes a custom SQL write operation using prepared statements. Use this for
         * complex INSERT, UPDATE, or DELETE queries that can't be handled by the standard methods.
         * This method operates on the master database.
         *
         * @param string $sql Custom SQL write query with ? placeholders for values
         * @param array $values Array of values to bind to the placeholders in order
         * @return mixed Number of affected rows for UPDATE/DELETE, last insert ID for INSERT,
         *               or false on failure
         *
         * @example
         * // Bulk update multiple records
         * $affected = (new \Databases\UserGroups())->sqlWrite(
         *     "UPDATE user_groups
         *      SET status = ?
         *      WHERE user_id = ? AND joined_at < ?",
         *     ['inactive', 123, date('Y-m-d', strtotime('-1 year'))]
         * );
         *
         * @example
         * // Complex conditional delete
         * $deleted = (new \Databases\Activity())->sqlWrite(
         *     "DELETE FROM activity
         *      WHERE user_id = ?
         *      AND last_activity < ?
         *      AND link_id NOT IN (SELECT id FROM favorites WHERE user_id = ?)",
         *     [123, strtotime('-30 days'), 123]
         * );
         */
        public function sqlWrite(string $sql, array $values = [])
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            return parent::sqlWrite($sql, $values);
        }

        /**
         * Update records matching WHERE conditions
         *
         * Updates one or more records that match the specified WHERE conditions.
         * This operation is performed on the master database using prepared statements.
         *
         * @param array $updates Associative array of fields to update (field => new_value)
         *                      Should NOT include primary key fields
         * @param array $wheres Associative array of WHERE conditions (field => value)
         *                     Often includes composite primary key fields to update specific records
         * @return int Number of records updated
         *
         * @example
         * // Update a specific composite key record
         * $updated = (new \Databases\UserGroups())->updateWhere(
         *     ['role' => 'admin', 'updated_at' => date('Y-m-d H:i:s')],
         *     ['user_id' => 123, 'group_id' => 456]
         * );
         *
         * @example
         * // Update all groups for a user
         * $updated = (new \Databases\UserGroups())->updateWhere(
         *     ['status' => 'inactive'],
         *     ['user_id' => 123]
         * );
         *
         * @example
         * // Update activity timestamp
         * (new \Databases\Activity())->updateWhere(
         *     ['last_activity' => time()],
         *     ['user_id' => 123, 'link_id' => 456]
         * );
         */
        public function updateWhere(array $updates, array $wheres)
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            $return = $this->sql->updateWhere($this->tableName, $updates, $wheres);
            return $return;
        }

        /**
         * Count records matching WHERE conditions
         *
         * @param array $values Associative array of WHERE conditions (field => value)
         *                      Empty array [] counts all records
         * @param string $master Database connection ('slave' for read replica, 'master' for primary)
         * @return int Number of matching records
         */
        public function count(array $values = [], string $master = 'slave')
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            $return = $this->sql->count($this->tableName, $values, $master);
            return $return;
        }

    }
