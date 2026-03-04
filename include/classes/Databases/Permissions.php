<?php
    namespace Databases;

    class Permissions extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['user_id', 'permission', 'ip'];
        protected array $primaryKey = ['user_id', 'permission'];
        protected string $tableName = 'permissions';

        public function findByUserAndPermission(int $userId, string $permission): ?array
        {
            return $this->selectWhereRow(['user_id' => $userId, 'permission' => $permission]);
        }

        public function findByUserId(int $userId): array
        {
            return $this->selectWhere(['user_id' => $userId]);
        }

        public function findAllDistinctPermissions(): array
        {
            return $this->sqlRead("SELECT DISTINCT permission FROM permissions");
        }

    }
