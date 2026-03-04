<?php
    namespace Databases;

    class MembersLaston extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = false;
        protected array $fields = ['user_id', 'laston'];
        protected string $primaryKey = 'user_id';
        protected string $tableName = 'members_laston';

        public function createLaston(int $userId): void
        {
            $this->insertArray([
                'user_id' => $userId,
                'laston' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
            ]);
        }

        public function findByUserId(int $userId): ?array
        {
            return $this->selectWhereRow(['user_id' => $userId]);
        }

    }
