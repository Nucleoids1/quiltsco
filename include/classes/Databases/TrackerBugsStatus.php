<?php
    namespace Databases;

    class TrackerBugsStatus extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['id', 'status_english', 'status_french', 'class'];
        protected string $primaryKey = 'id';
        protected string $tableName = 'tracker_bugs_status';

        public function findAllOrderedById(): array
        {
            return $this->selectWhere([], 'id');
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

    }
