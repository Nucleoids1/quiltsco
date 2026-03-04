<?php
    namespace Databases;

    class CommunityThreadsCategories extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = false;
        protected array $fields = ['id', 'name', 'worksafe', 'positive'];
        protected string $primaryKey = 'id';
        protected string $tableName = 'community_threads_categories';

        public function selectAllByName(): array
        {
            return $this->selectWhere([], 'name');
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

    }
