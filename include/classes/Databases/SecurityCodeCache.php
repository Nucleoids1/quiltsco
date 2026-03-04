<?php
    namespace Databases;

    class SecurityCodeCache extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = false;
        protected array $fields = ['cache', 'code', 'posted_on'];
        protected string $primaryKey = 'cache';
        protected string $tableName = 'security_code_cache';

        public function replaceForCache(string $cache, string $code): void
        {
            $this->sqlWrite(
                'REPLACE INTO security_code_cache SET cache = ?, code = ?, posted_on = "'.date('Y-m-d H:i:s', server('REQUEST_TIME')).'"',
                [$cache, $code]
            );
        }

    }
