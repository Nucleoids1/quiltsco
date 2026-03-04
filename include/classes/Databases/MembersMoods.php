<?php
    namespace Databases;

    class MembersMoods extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['id', 'user_id', 'mood', 'posted_on'];
        protected string $primaryKey = 'id';
        protected string $tableName = 'members_moods';

        public function findLatestByUser(int $userId): ?array
        {
            return $this->selectWhereRow(['user_id' => $userId], 'id DESC');
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

    }
