<?php
    namespace DatabasesSchemes;

    /**
     * PrimaryKey - Single-Field Primary Key Database Abstraction Layer
     *
     * This class provides a secure ORM-like interface for database tables with single-field
     * primary keys. It wraps the MySQL connector with a simplified API that automatically
     * handles common CRUD operations using prepared statements for security.
     *
     * PURPOSE:
     * PrimaryKey serves as the base class for database table abstractions that have a single
     * primary key field (e.g., 'id', 'user_id', 'member_id'). It provides a consistent,
     * secure interface for all database operations on such tables.
     *
     * WHEN TO USE:
     * - Use PrimaryKey when your table has a SINGLE primary key field
     * - Ideal for tables with auto-increment integer primary keys
     * - Also supports non-auto-increment single-field primary keys (set $autoincrement = false)
     *
     * WHEN NOT TO USE:
     * - If your table has a COMPOSITE primary key (multiple fields), use PrimaryKeyComposite instead
     * - Example: A junction table with primary key (user_id, group_id) requires PrimaryKeyComposite
     *
     * KEY DIFFERENCES FROM PrimaryKeyComposite:
     * - PrimaryKey: Single primary key field (e.g., 'id')
     *   Methods like selectPrimaryKey() accept a single string value
     * - PrimaryKeyComposite: Multiple primary key fields (e.g., 'user_id' + 'group_id')
     *   Methods accept associative arrays for composite key lookups
     *
     * ARCHITECTURE:
     * Child classes extend PrimaryKey and define their table-specific properties:
     *   protected string $database = 'main';           // Database name
     *   protected string $tableName = 'users';         // Table name
     *   protected string $primaryKey = 'user_id';      // Primary key field name
     *   protected bool $autoincrement = true;          // Whether primary key auto-increments
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
     * // Create a table class
     * namespace Databases;
     * class Members extends \DatabasesSchemes\PrimaryKey {
     *     protected string $database = 'main';
     *     protected string $tableName = 'members';
     *     protected string $primaryKey = 'member_id';
     *     protected bool $autoincrement = true;
     * }
     *
     * // Insert a new record (returns auto-increment ID)
     * $memberId = (new \Databases\Members())->insertArray([
     *     'username' => 'john_doe',
     *     'email' => 'john@example.com',
     *     'status' => 'active'
     * ]);
     *
     * // Fetch a single record by primary key
     * $member = (new \Databases\Members())->selectPrimaryKey($memberId);
     * // Returns: ['member_id' => 123, 'username' => 'john_doe', 'email' => 'john@example.com', ...]
     *
     * // Fetch all active members, ordered by username
     * $activeMembers = (new \Databases\Members())->selectWhere(
     *     ['status' => 'active'],
     *     'username ASC'
     * );
     *
     * // Fetch members as associative array keyed by member_id
     * $membersById = (new \Databases\Members())->selectWhere(
     *     ['status' => 'active'],
     *     'username',
     *     0,
     *     ['arrayKey' => 'member_id']
     * );
     * // Returns: [123 => ['member_id' => 123, ...], 456 => ['member_id' => 456, ...]]
     *
     * // Fetch only usernames
     * $usernames = (new \Databases\Members())->selectWhere(
     *     ['status' => 'active'],
     *     'username',
     *     0,
     *     ['arrayValue' => 'username']
     * );
     * // Returns: ['john_doe', 'jane_smith', ...]
     *
     * // Update a record
     * (new \Databases\Members())->updateWhere(
         *     ['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s', server('REQUEST_TIME'))],
     *     ['member_id' => $memberId]
     * );
     *
     * // Delete a record by primary key
     * (new \Databases\Members())->deletePrimaryKey($memberId);
     *
     * // Count records
     * $activeCount = (new \Databases\Members())->count(['status' => 'active']);
     *
     * @package DatabasesSchemes
     * @author RAVE Framework
     * @see PrimaryKeyComposite For tables with composite primary keys
     */
    class PrimaryKey extends Base {

        protected bool $autoincrement = true; // Default to true for backward compatibility
        protected string $primaryKey;

        /**
         * Delete a single record by primary key value
         *
         * Deletes exactly one record from the table where the primary key matches the given value.
         * This operation is performed on the master database and uses a prepared statement.
         *
         * @param string $id The primary key value of the record to delete
         * @return bool True if the record was deleted, false if no matching record was found
         *
         * @example
         * // Delete a member by ID
         * $success = (new \Databases\Members())->deletePrimaryKey('12345');
         * if ($success) {
         *     echo "Member deleted successfully";
         * }
         */
        public function deletePrimaryKey(string $id)
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            $return = $this->sql->deletePrimaryKey($this->tableName, $id);
            return $return;
        }

        /**
         * Delete multiple records matching WHERE conditions
         *
         * Deletes all records from the table that match the specified WHERE conditions.
         * This operation is performed on the master database and uses prepared statements.
         * The $orders parameter can be used to control which records are deleted first
         * (useful when combined with a LIMIT in the underlying implementation).
         *
         * WARNING: If $wheres is empty, this could delete ALL records in the table.
         * Always verify your WHERE conditions before calling this method.
         *
         * @param array $wheres Associative array of WHERE conditions (field => value)
         *                      All conditions are AND-ed together
         *                      Example: ['status' => 'inactive', 'last_login' => null]
         * @param string $orders Optional ORDER BY clause (e.g., 'created_at ASC')
         *                       Useful for controlling deletion order
         * @return int Number of records deleted
         *
         * @example
         * // Delete all inactive members who haven't logged in
         * $deleted = (new \Databases\Members())->deleteWhere([
         *     'status' => 'inactive',
         *     'last_login' => null
         * ]);
         * echo "Deleted $deleted inactive members";
         *
         * @example
         * // Delete oldest pending requests first
         * $deleted = (new \Databases\Requests())->deleteWhere(
         *     ['status' => 'pending'],
         *     'created_at ASC'
         * );
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
         * Insert a new record into the table
         *
         * Inserts a new record with the provided field values. Uses prepared statements
         * for security. If the table has an auto-increment primary key, the generated
         * ID is returned.
         *
         * The primary key field can be omitted from $insertArray if auto-increment is enabled.
         * If auto-increment is disabled, you must include the primary key value.
         *
         * @param array $insertArray Associative array of field names and values to insert
         *                           Keys are column names, values are the data to insert
         *                           Example: ['username' => 'john', 'email' => 'john@example.com']
         * @return string|int For auto-increment tables: the newly generated primary key value
         *                    For non-auto-increment tables: the number of rows affected (typically 1)
         *
         * @example
         * // Insert a new member (auto-increment)
         * $memberId = (new \Databases\Members())->insertArray([
         *     'username' => 'john_doe',
         *     'email' => 'john@example.com',
         *     'status' => 'active',
         *     'created_at' => date('Y-m-d H:i:s')
         * ]);
         * echo "Created member with ID: $memberId";
         *
         * @example
         * // Insert with explicit primary key (non-auto-increment table)
         * $rows = (new \Databases\Settings())->insertArray([
         *     'setting_key' => 'site_title',
         *     'setting_value' => 'My Website',
         *     'updated_at' => date('Y-m-d H:i:s')
         * ]);
         */
        public function insertArray(array $insertArray)
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            $this->sql->setAutoIncrement($this->autoincrement);
            $return = $this->sql->insertArray($this->tableName, $insertArray);
            return $return;
        }

        /**
         * Insert a record, or update it if it already exists (UPSERT)
         *
         * Attempts to insert a new record. If a duplicate key error occurs (record with
         * the same primary key or unique key already exists), it updates the existing
         * record with the values from $updateArray instead.
         *
         * This implements MySQL's INSERT ... ON DUPLICATE KEY UPDATE functionality.
         *
         * IMPORTANT: This requires either:
         * - A duplicate primary key value in $insertArray (for non-auto-increment), OR
         * - A duplicate unique key value in $insertArray
         *
         * @param array $insertArray Associative array of field names and values for INSERT
         *                           Used when creating a new record
         *                           Example: ['user_id' => 123, 'login_count' => 1, 'last_login' => $now]
         * @param array $updateArray Associative array of field names and values for UPDATE
         *                           Used when the record already exists
         *                           Example: ['login_count' => 'login_count + 1', 'last_login' => $now]
         * @return string|int For inserts: the primary key value (auto-increment ID or affected rows)
         *                    For updates: typically 2 (indicating an update occurred)
         *
         * @example
         * // Track login count with upsert
         * $result = (new \Databases\LoginStats())->insertArrayUpdateRow(
         *     [
         *         'user_id' => $userId,
         *         'login_count' => 1,
         *         'last_login' => date('Y-m-d H:i:s')
         *     ],
         *     [
         *         'login_count' => 'login_count + 1',  // Increment counter
         *         'last_login' => date('Y-m-d H:i:s')  // Update timestamp
         *     ]
         * );
         * // First call: Inserts with login_count = 1
         * // Subsequent calls: Increments login_count and updates last_login
         *
         * @example
         * // Maintain a cache table
         * (new \Databases\Cache())->insertArrayUpdateRow(
         *     ['cache_key' => 'user_123', 'cache_value' => $data, 'expires_at' => $expires],
         *     ['cache_value' => $data, 'expires_at' => $expires]
         * );
         */
        public function insertArrayUpdateRow(array $insertArray, array $updateArray)
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            $this->sql->setAutoIncrement($this->autoincrement);
            $return = $this->sql->insertArrayUpdateRow($this->tableName, $insertArray, $updateArray);
            return $return;
        }

        /**
         * Replace a record (delete existing and insert new)
         *
         * This implements MySQL's REPLACE functionality, which:
         * 1. Attempts to insert the record
         * 2. If a duplicate key conflict occurs, deletes the old record first
         * 3. Then inserts the new record
         *
         * REPLACE is essentially a DELETE + INSERT operation. If a record with the same
         * primary key (or unique key) exists, it is deleted and the new record is inserted.
         *
         * WARNING: Unlike UPDATE, REPLACE deletes the old record entirely. Any columns not
         * specified in $insertArray will be set to their default values, not preserved
         * from the old record.
         *
         * @param array $insertArray Associative array of field names and values
         *                           Must include the primary key value (for non-auto-increment)
         *                           or a unique key value that identifies the record to replace
         *                           Example: ['user_id' => 123, 'username' => 'john', 'email' => 'john@example.com']
         * @return string|int For auto-increment tables: the primary key value (may be new if record was deleted)
         *                    For non-auto-increment tables: the number of affected rows
         *
         * @example
         * // Replace a configuration record
         * (new \Databases\Settings())->replaceArray([
         *     'setting_key' => 'site_title',    // Primary key
         *     'setting_value' => 'New Title',
         *     'updated_at' => date('Y-m-d H:i:s')
         * ]);
         * // If 'site_title' exists: deletes old record, inserts new one
         * // If 'site_title' doesn't exist: simply inserts new record
         *
         * @example
         * // Replace a cache entry (overwrites all fields)
         * (new \Databases\Cache())->replaceArray([
         *     'cache_key' => 'user_data_123',
         *     'cache_value' => json_encode($newData),
         *     'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour'))
         * ]);
         */
        public function replaceArray(array $insertArray)
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            $this->sql->setAutoIncrement($this->autoincrement);
            $return = $this->sql->replaceArray($this->tableName, $insertArray);
            return $return;
        }

        /**
         * Select a single record by its primary key value
         *
         * Fetches exactly one record from the table where the primary key matches the
         * given value. This is the most efficient way to retrieve a single record when
         * you know its primary key.
         *
         * By default, reads from the slave database (read replica). Use $options['master'] = 'master'
         * to force reading from the master database (useful after an insert/update when you need
         * to immediately read the data back).
         *
         * @param string $id The primary key value to search for
         *                   Example: '12345' or 'user_abc123' (depending on your primary key type)
         * @param array $options Optional configuration array:
         *                       - 'arrayValue': (string) Return only this column's value instead of full row
         *                                       Example: ['arrayValue' => 'username'] returns just the username string
         *                       - 'master': (string) 'master' to read from master DB, 'slave' for replica (default: 'slave')
         * @return array|string|null If record found: associative array of all columns and values
         *                           If 'arrayValue' option: returns that column's value (string, int, etc.)
         *                           If record not found: empty array or null
         *
         * @example
         * // Fetch a member by ID
         * $member = (new \Databases\Members())->selectPrimaryKey('12345');
         * // Returns: ['member_id' => '12345', 'username' => 'john_doe', 'email' => 'john@example.com', ...]
         *
         * @example
         * // Fetch just the username
         * $username = (new \Databases\Members())->selectPrimaryKey('12345', ['arrayValue' => 'username']);
         * // Returns: 'john_doe'
         *
         * @example
         * // Fetch from master database (after an update)
         * (new \Databases\Members())->updateWhere(['status' => 'premium'], ['member_id' => $id]);
         * $member = (new \Databases\Members())->selectPrimaryKey($id, ['master' => 'master']);
         * // Ensures we read the updated data from master, not potentially stale slave data
         */
        public function selectPrimaryKey(string $id, array $options = [])
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            $return = $this->sql->selectPrimaryKey($this->tableName, $id, $options);
            return $return;
        }

        /**
         * Select all records from the table with optional ordering and limiting
         *
         * Retrieves every record from the table with no WHERE filtering. This is equivalent
         * to selectWhere([]) but provides a cleaner API for the common "fetch all" use case.
         *
         * By default, reads from the slave database. Use $options['master'] = 'master' to
         * read from the master database.
         *
         * WARNING: On large tables, calling selectAll() without a limit can consume significant
         * memory and time. Consider using pagination with $limit and $options['offset'].
         *
         * @param string $orders Optional ORDER BY clause (e.g., 'id', 'created_at DESC', 'name ASC, id DESC')
         *                       Multiple columns can be comma-separated
         *                       Empty string means no specific ordering (database default order)
         * @param int $limit Maximum number of records to return (0 = no limit)
         *                   Use for pagination: fetch 50 records at a time
         * @param array $options Optional configuration array:
         *                       - 'arrayKey': (string) Index results by this column instead of numeric keys
         *                                     Example: ['arrayKey' => 'member_id'] returns [123 => [...], 456 => [...]]
         *                       - 'arrayValue': (string) Return only this column's values
         *                                       Example: ['arrayValue' => 'username'] returns ['john', 'jane', ...]
         *                       - 'master': (string) 'master' to read from master DB, 'slave' for replica (default: 'slave')
         *                       - 'offset': (int) Skip this many records (for pagination)
         * @return array Array of records. Each record is an associative array of column => value pairs
         *               Returns empty array if no records found
         *               Format varies based on arrayKey/arrayValue options (see examples)
         *
         * @example
         * // Get all members ordered by username
         * $members = (new \Databases\Members())->selectAll('username ASC');
         * // Returns: [
         * //   0 => ['member_id' => 123, 'username' => 'alice', ...],
         * //   1 => ['member_id' => 456, 'username' => 'bob', ...],
         * // ]
         *
         * @example
         * // Get latest 10 posts
         * $latestPosts = (new \Databases\Posts())->selectAll('created_at DESC', 10);
         *
         * @example
         * // Pagination: get page 3 (records 101-150)
         * $page3 = (new \Databases\Members())->selectAll('member_id', 50, ['offset' => 100]);
         *
         * @example
         * // Get all members indexed by their ID
         * $membersById = (new \Databases\Members())->selectAll('username', 0, ['arrayKey' => 'member_id']);
         * // Returns: [
         * //   123 => ['member_id' => 123, 'username' => 'alice', ...],
         * //   456 => ['member_id' => 456, 'username' => 'bob', ...],
         * // ]
         * // Now you can access: $membersById[123]['username']
         *
         * @example
         * // Get just the usernames
         * $usernames = (new \Databases\Members())->selectAll('username', 0, ['arrayValue' => 'username']);
         * // Returns: ['alice', 'bob', 'charlie', ...]
         *
         * @example
         * // Get usernames indexed by member_id
         * $usernamesById = (new \Databases\Members())->selectAll('username', 0, [
         *     'arrayKey' => 'member_id',
         *     'arrayValue' => 'username'
         * ]);
         * // Returns: [123 => 'alice', 456 => 'bob', 789 => 'charlie']
         */
        public function selectAll(string $orders = '', int $limit = 0, array $options = [])
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            return $this->sql->selectWhere($this->tableName, [], $orders, $limit, $options);
        }

        /**
         * Select multiple records matching WHERE conditions
         *
         * This is the most versatile SELECT method. Fetches all records that match the specified
         * WHERE conditions. Multiple conditions are AND-ed together. For OR conditions or complex
         * queries, use sqlRead() with custom SQL.
         *
         * By default, reads from the slave database. Use $options['master'] = 'master' to read
         * from the master database.
         *
         * @param array $values Associative array of WHERE conditions (field => value)
         *                      All conditions are AND-ed together
         *                      Example: ['status' => 'active', 'type' => 'premium']
         *                      Empty array [] selects all records (same as selectAll)
         * @param string $orders Optional ORDER BY clause (e.g., 'created_at DESC', 'name ASC, id DESC')
         *                       Multiple columns can be comma-separated
         *                       Empty string means no specific ordering
         * @param int $limit Maximum number of records to return (0 = no limit)
         *                   Useful for pagination and limiting large result sets
         * @param array $options Optional configuration array:
         *                       - 'arrayKey': (string) Index results by this column
         *                                     Example: ['arrayKey' => 'member_id']
         *                       - 'arrayValue': (string) Return only this column's values
         *                                       Example: ['arrayValue' => 'username']
         *                       - 'master': (string) 'master' for master DB, 'slave' for replica (default: 'slave')
         *                       - 'offset': (int) Skip this many records (for pagination)
         * @return array Array of matching records. Each record is an associative array of column => value
         *               Returns empty array if no matching records found
         *               Format varies based on arrayKey/arrayValue options
         *
         * @example
         * // Find all active premium members
         * $premiumMembers = (new \Databases\Members())->selectWhere([
         *     'status' => 'active',
         *     'type' => 'premium'
         * ], 'created_at DESC');
         *
         * @example
         * // Find recent posts by author, limit to 5
         * $recentPosts = (new \Databases\Posts())->selectWhere(
         *     ['author_id' => $authorId],
         *     'created_at DESC',
         *     5
         * );
         *
         * @example
         * // Pagination: find active members, page 2 (records 11-20)
         * $page2 = (new \Databases\Members())->selectWhere(
         *     ['status' => 'active'],
         *     'username ASC',
         *     10,
         *     ['offset' => 10]
         * );
         *
         * @example
         * // Get members indexed by ID
         * $membersById = (new \Databases\Members())->selectWhere(
         *     ['status' => 'active'],
         *     'username',
         *     0,
         *     ['arrayKey' => 'member_id']
         * );
         * // Returns: [123 => ['member_id' => 123, ...], 456 => ['member_id' => 456, ...]]
         *
         * @example
         * // Get just email addresses of inactive members
         * $inactiveEmails = (new \Databases\Members())->selectWhere(
         *     ['status' => 'inactive'],
         *     'email',
         *     0,
         *     ['arrayValue' => 'email']
         * );
         * // Returns: ['user1@example.com', 'user2@example.com', ...]
         *
         * @example
         * // Get usernames indexed by member_id
         * $usernames = (new \Databases\Members())->selectWhere(
         *     ['status' => 'active'],
         *     'username',
         *     0,
         *     ['arrayKey' => 'member_id', 'arrayValue' => 'username']
         * );
         * // Returns: [123 => 'john_doe', 456 => 'jane_smith']
         *
         * @example
         * // Read from master database (after recent write)
         * (new \Databases\Members())->updateWhere(['last_active' => $now], ['member_id' => $id]);
         * $member = (new \Databases\Members())->selectWhere(
         *     ['member_id' => $id],
         *     '',
         *     1,
         *     ['master' => 'master']
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
         * Select a single record matching WHERE conditions
         *
         * Similar to selectWhere() but returns only the first matching record. This is more
         * efficient than selectWhere() with limit=1 because it's optimized for single-row queries.
         *
         * If multiple records match the WHERE conditions, only the first one is returned
         * (based on the ORDER BY clause if specified).
         *
         * By default, reads from the slave database. Use $options['master'] = 'master' to
         * read from the master database.
         *
         * @param array $values Associative array of WHERE conditions (field => value)
         *                      All conditions are AND-ed together
         *                      Example: ['username' => 'john_doe', 'status' => 'active']
         * @param string $orders Optional ORDER BY clause (e.g., 'created_at DESC')
         *                       Determines which record is returned if multiple matches exist
         *                       Example: 'created_at DESC' returns the most recent match
         * @param array $options Optional configuration array:
         *                       - 'arrayValue': (string) Return only this column's value instead of full row
         *                                       Example: ['arrayValue' => 'email'] returns just the email string
         *                       - 'master': (string) 'master' to read from master DB, 'slave' for replica (default: 'slave')
         * @return array|string|null If record found: associative array of all columns and values
         *                           If 'arrayValue' option: returns that column's value
         *                           If no matching record: empty array or null
         *
         * @example
         * // Find a specific member by username
         * $member = (new \Databases\Members())->selectWhereRow(['username' => 'john_doe']);
         * // Returns: ['member_id' => 123, 'username' => 'john_doe', 'email' => 'john@example.com', ...]
         *
         * @example
         * // Get the most recent login record for a user
         * $lastLogin = (new \Databases\LoginHistory())->selectWhereRow(
         *     ['user_id' => $userId],
         *     'login_time DESC'
         * );
         * // Returns the most recent login record
         *
         * @example
         * // Get just the email address
         * $email = (new \Databases\Members())->selectWhereRow(
         *     ['member_id' => $memberId],
         *     '',
         *     ['arrayValue' => 'email']
         * );
         * // Returns: 'john@example.com'
         *
         * @example
         * // Find oldest pending request
         * $oldestRequest = (new \Databases\Requests())->selectWhereRow(
         *     ['status' => 'pending'],
         *     'created_at ASC'
         * );
         *
         * @example
         * // Read from master after update
         * (new \Databases\Members())->updateWhere(['status' => 'verified'], ['member_id' => $id]);
         * $member = (new \Databases\Members())->selectWhereRow(
         *     ['member_id' => $id],
         *     '',
         *     ['master' => 'master']
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
         * Execute a custom SELECT query with prepared statement parameters
         *
         * For complex queries that cannot be expressed with the standard selectWhere() methods,
         * this allows you to write custom SQL while still benefiting from prepared statement
         * security. Use placeholders (?) in your SQL and provide values in the $values array.
         *
         * By default, reads from the slave database. Use $options['master'] = 'master' to
         * read from the master database.
         *
         * IMPORTANT: Only use this for SELECT queries. For INSERT/UPDATE/DELETE, use sqlWrite().
         *
         * @param string $sql Custom SQL SELECT query with placeholders (?)
         *                    Example: "SELECT * FROM users WHERE age > ? AND status = ? ORDER BY name"
         *                    Use ? for each parameter to be bound (prevents SQL injection)
         * @param array $values Optional array of values to bind to placeholders
         *                      Values are bound in order: [25, 'active'] binds to first and second ?
         *                      Can be empty array if no placeholders in SQL
         * @param array $options Optional configuration array:
         *                       - 'arrayKey': (string) Index results by this column
         *                       - 'arrayValue': (string) Return only this column's values
         *                       - 'master': (string) 'master' for master DB, 'slave' for replica (default: 'slave')
         *                       - 'offset': (int) Skip this many records
         * @return array Array of records. Each record is an associative array of column => value
         *               Returns empty array if no results
         *               Format varies based on arrayKey/arrayValue options
         *
         * @example
         * // Find members with complex conditions
         * $members = (new \Databases\Members())->sqlRead(
         *     "SELECT * FROM members WHERE (status = ? OR type = ?) AND created_at > ? ORDER BY username",
         *     ['active', 'premium', '2024-01-01']
         * );
         *
         * @example
         * // JOIN query with prepared statements
         * $results = (new \Databases\Posts())->sqlRead(
         *     "SELECT p.*, m.username
         *      FROM posts p
         *      INNER JOIN members m ON p.author_id = m.member_id
         *      WHERE p.status = ? AND m.status = ?
         *      ORDER BY p.created_at DESC
         *      LIMIT ?",
         *     ['published', 'active', 10]
         * );
         *
         * @example
         * // Aggregate query
         * $stats = (new \Databases\Orders())->sqlRead(
         *     "SELECT status, COUNT(*) as count, SUM(total) as revenue
         *      FROM orders
         *      WHERE created_at >= ?
         *      GROUP BY status",
         *     [date('Y-m-01')]  // First day of current month
         * );
         *
         * @example
         * // Get just a column of values
         * $userIds = (new \Databases\Members())->sqlRead(
         *     "SELECT member_id FROM members WHERE status = ? ORDER BY created_at DESC LIMIT ?",
         *     ['active', 100],
         *     ['arrayValue' => 'member_id']
         * );
         * // Returns: [123, 456, 789, ...]
         *
         * @example
         * // Read from master database
         * $data = (new \Databases\Analytics())->sqlRead(
         *     "SELECT * FROM analytics WHERE date = ? AND metric = ?",
         *     [date('Y-m-d'), 'pageviews'],
         *     ['master' => 'master']
         * );
         */
        public function sqlRead(string $sql, array $values = [], array $options = [])
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            return parent::sqlRead($sql, $values, $options);
        }

        /**
         * Execute a custom SELECT query that returns a single row
         *
         * Similar to sqlRead() but optimized for queries that return a single record.
         * Use this when you know your query will return at most one row (e.g., queries with
         * LIMIT 1, aggregate functions like COUNT(*), or unique lookups).
         *
         * By default, reads from the slave database. Use $options['master'] = 'master' to
         * read from the master database.
         *
         * @param string $sql Custom SQL SELECT query with placeholders (?)
         *                    Example: "SELECT COUNT(*) as total FROM users WHERE status = ?"
         *                    Use ? for each parameter to be bound
         * @param array $values Optional array of values to bind to placeholders
         *                      Values are bound in order
         *                      Can be empty array if no placeholders
         * @param array $options Optional configuration array:
         *                       - 'arrayValue': (string) Return only this column's value instead of full row
         *                       - 'master': (string) 'master' for master DB, 'slave' for replica (default: 'slave')
         * @return array|string|null If record found: associative array of column => value
         *                           If 'arrayValue' option: returns that column's value
         *                           If no result: empty array or null
         *
         * @example
         * // Get count of active members
         * $result = (new \Databases\Members())->sqlReadRow(
         *     "SELECT COUNT(*) as total FROM members WHERE status = ?",
         *     ['active']
         * );
         * $count = $result['total'];  // Returns: ['total' => 1523]
         *
         * @example
         * // Get aggregate statistics
         * $stats = (new \Databases\Orders())->sqlReadRow(
         *     "SELECT
         *         COUNT(*) as order_count,
         *         SUM(total) as revenue,
         *         AVG(total) as avg_order
         *      FROM orders
         *      WHERE created_at >= ? AND status = ?",
         *     [date('Y-m-01'), 'completed']
         * );
         * // Returns: ['order_count' => 150, 'revenue' => 45000.00, 'avg_order' => 300.00]
         *
         * @example
         * // Get just the count value
         * $total = (new \Databases\Members())->sqlReadRow(
         *     "SELECT COUNT(*) as total FROM members WHERE type = ?",
         *     ['premium'],
         *     ['arrayValue' => 'total']
         * );
         * // Returns: 1523 (just the number)
         *
         * @example
         * // Complex JOIN returning single row
         * $member = (new \Databases\Members())->sqlReadRow(
         *     "SELECT m.*, p.plan_name, p.price
         *      FROM members m
         *      LEFT JOIN plans p ON m.plan_id = p.plan_id
         *      WHERE m.member_id = ?",
         *     [$memberId]
         * );
         *
         * @example
         * // Check if record exists
         * $exists = (new \Databases\Members())->sqlReadRow(
         *     "SELECT 1 as found FROM members WHERE email = ? LIMIT 1",
         *     [$email]
         * );
         * if (!empty($exists)) {
         *     echo "Email already registered";
         * }
         */
        public function sqlReadRow(string $sql, array $values = [], array $options = [])
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            return parent::sqlReadRow($sql, $values, $options);
        }

        /**
         * Execute a custom INSERT, UPDATE, or DELETE query with prepared statement parameters
         *
         * For complex write operations that cannot be expressed with the standard insertArray(),
         * updateWhere(), or deleteWhere() methods, this allows you to write custom SQL while
         * still benefiting from prepared statement security.
         *
         * Always executes on the master database (write operations never use slaves).
         *
         * IMPORTANT: Only use this for INSERT/UPDATE/DELETE queries. For SELECT, use sqlRead()
         * or sqlReadRow() instead.
         *
         * @param string $sql Custom SQL write query with placeholders (?)
         *                    Examples:
         *                    - "UPDATE users SET login_count = login_count + 1 WHERE user_id = ?"
         *                    - "DELETE FROM sessions WHERE expires_at < ?"
         *                    - "INSERT INTO audit_log (user_id, action, timestamp) VALUES (?, ?, ?)"
         *                    Use ? for each parameter to be bound (prevents SQL injection)
         * @param array $values Optional array of values to bind to placeholders
         *                      Values are bound in order: [123, 'login', $timestamp] binds to first, second, third ?
         *                      Can be empty array if no placeholders
         * @return int|string For INSERT with auto-increment: the newly generated primary key ID
         *                    For UPDATE/DELETE: the number of affected rows
         *                    For other operations: typically the number of affected rows
         *
         * @example
         * // Increment a counter
         * $affected = (new \Databases\Members())->sqlWrite(
         *     "UPDATE members SET login_count = login_count + 1, last_login = ? WHERE member_id = ?",
         *     [date('Y-m-d H:i:s'), $memberId]
         * );
         *
         * @example
         * // Delete old records (bulk cleanup)
         * $deleted = (new \Databases\Sessions())->sqlWrite(
         *     "DELETE FROM sessions WHERE expires_at < ? AND user_id != ?",
         *     [date('Y-m-d H:i:s'), $adminUserId]  // Don't delete admin sessions
         * );
         * echo "Deleted $deleted expired sessions";
         *
         * @example
         * // Insert with ON DUPLICATE KEY UPDATE (complex upsert)
         * $result = (new \Databases\Stats())->sqlWrite(
         *     "INSERT INTO daily_stats (date, metric, value)
         *      VALUES (?, ?, ?)
         *      ON DUPLICATE KEY UPDATE value = value + VALUES(value)",
         *     [date('Y-m-d'), 'pageviews', 1]
         * );
         *
         * @example
         * // Update with JOIN
         * $affected = (new \Databases\Orders())->sqlWrite(
         *     "UPDATE orders o
         *      INNER JOIN members m ON o.member_id = m.member_id
         *      SET o.discount = ?
         *      WHERE m.type = ? AND o.status = ?",
         *     [0.15, 'premium', 'pending']
         * );
         *
         * @example
         * // Conditional update with CASE
         * $affected = (new \Databases\Products())->sqlWrite(
         *     "UPDATE products
         *      SET price = CASE
         *          WHEN category = ? THEN price * ?
         *          WHEN category = ? THEN price * ?
         *          ELSE price
         *      END
         *      WHERE updated_at < ?",
         *     ['electronics', 1.10, 'clothing', 1.05, '2024-01-01']
         * );
         *
         * @example
         * // Insert with subquery
         * $result = (new \Databases\Notifications())->sqlWrite(
         *     "INSERT INTO notifications (user_id, message, created_at)
         *      SELECT member_id, ?, ?
         *      FROM members
         *      WHERE status = ? AND type = ?",
         *     ['New feature available!', date('Y-m-d H:i:s'), 'active', 'premium']
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
         * Updates all records that match the specified WHERE conditions with the provided
         * field values. Uses prepared statements for security. Always executes on the
         * master database.
         *
         * All WHERE conditions are AND-ed together. For OR conditions or complex updates,
         * use sqlWrite() with custom SQL.
         *
         * WARNING: If $wheres is empty, this will update ALL records in the table.
         * Always verify your WHERE conditions before calling this method.
         *
         * @param array $updates Associative array of fields to update (field => new_value)
         *                       Keys are column names, values are the new data
         *                       Example: ['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s')]
         * @param array $wheres Associative array of WHERE conditions (field => value)
         *                      All conditions are AND-ed together
         *                      Example: ['member_id' => $id] or ['status' => 'pending', 'created_at <' => '2024-01-01']
         * @return int Number of rows updated (0 if no matching records or no changes made)
         *
         * @example
         * // Update a single member's status
         * $updated = (new \Databases\Members())->updateWhere(
         *     ['status' => 'verified', 'verified_at' => date('Y-m-d H:i:s')],
         *     ['member_id' => $memberId]
         * );
         * if ($updated > 0) {
         *     echo "Member verified successfully";
         * }
         *
         * @example
         * // Update all pending orders for a user
         * $updated = (new \Databases\Orders())->updateWhere(
         *     ['status' => 'cancelled', 'cancelled_at' => date('Y-m-d H:i:s')],
         *     ['user_id' => $userId, 'status' => 'pending']
         * );
         * echo "Cancelled $updated pending orders";
         *
         * @example
         * // Mark old records as archived
         * $updated = (new \Databases\Posts())->updateWhere(
         *     ['archived' => 1, 'archived_at' => date('Y-m-d H:i:s')],
         *     ['status' => 'published', 'created_at <' => date('Y-m-d', strtotime('-1 year'))]
         * );
         *
         * @example
         * // Update with NULL value
         * $updated = (new \Databases\Sessions())->updateWhere(
         *     ['last_activity' => null],
         *     ['user_id' => $userId, 'status' => 'expired']
         * );
         *
         * @example
         * // Combine update with primary key
         * (new \Databases\Members())->updateWhere(
         *     [
         *         'email' => $newEmail,
         *         'email_verified' => 0,
         *         'updated_at' => date('Y-m-d H:i:s')
         *     ],
         *     ['member_id' => $memberId]
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
         * Returns the total number of records that match the specified WHERE conditions.
         * This is more efficient than fetching records and counting them in PHP, as the
         * database can count records without transferring data.
         *
         * All WHERE conditions are AND-ed together. For OR conditions or complex counts,
         * use sqlReadRow() with a custom COUNT query.
         *
         * By default, reads from the slave database. Set $master = 'master' to count
         * from the master database (useful if you need an accurate count immediately
         * after a write operation).
         *
         * @param array $values Associative array of WHERE conditions (field => value)
         *                      All conditions are AND-ed together
         *                      Example: ['status' => 'active', 'type' => 'premium']
         *                      Empty array [] counts all records in the table
         * @param string $master Database connection to use:
         *                       - 'slave': Use read replica (default, may have slight replication lag)
         *                       - 'master': Use master database (guaranteed up-to-date count)
         * @return int Total number of matching records (0 if no matches)
         *
         * @example
         * // Count all active members
         * $activeCount = (new \Databases\Members())->count(['status' => 'active']);
         * echo "Total active members: $activeCount";
         *
         * @example
         * // Count all records in table
         * $totalMembers = (new \Databases\Members())->count();
         *
         * @example
         * // Count with multiple conditions
         * $premiumActiveCount = (new \Databases\Members())->count([
         *     'status' => 'active',
         *     'type' => 'premium',
         *     'verified' => 1
         * ]);
         *
         * @example
         * // Count from master after insert
         * $newId = (new \Databases\Members())->insertArray(['username' => 'john', 'status' => 'active']);
         * $count = (new \Databases\Members())->count(['status' => 'active'], 'master');
         * // Ensures count includes the just-inserted record
         *
         * @example
         * // Check if any records exist
         * $hasPending = (new \Databases\Orders())->count([
         *     'user_id' => $userId,
         *     'status' => 'pending'
         * ]) > 0;
         * if ($hasPending) {
         *     echo "You have pending orders";
         * }
         *
         * @example
         * // Pagination: calculate total pages
         * $perPage = 20;
         * $totalRecords = (new \Databases\Posts())->count(['status' => 'published']);
         * $totalPages = ceil($totalRecords / $perPage);
         */
        public function count(array $values = [], string $master = 'slave')
        {
            $this->sql->setPrimaryKey($this->primaryKey);
            $return = $this->sql->count($this->tableName, $values, $master);
            return $return;
        }

    }
