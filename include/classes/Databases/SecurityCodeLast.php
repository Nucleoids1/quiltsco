<?php
    namespace Databases;

    class SecurityCodeLast extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['user_id', 'code', 'posted_on'];
        protected string $primaryKey = 'user_id';
        protected string $tableName = 'security_code_last';

        public function replaceForUser(int $userId, string $code): void
        {
            $this->sqlWrite(
                'REPLACE INTO security_code_last SET user_id = ?, code = ?, posted_on = "'.date('Y-m-d H:i:s', server('REQUEST_TIME')).'"',
                [$userId, $code]
            );
        }

        public function findByUserId(int $userId): ?array
        {
            return $this->selectWhereRow(['user_id' => $userId]);
        }

        public function deleteByUserId(int $userId): void
        {
            $this->deleteWhere(['user_id' => $userId]);
        }

    }
