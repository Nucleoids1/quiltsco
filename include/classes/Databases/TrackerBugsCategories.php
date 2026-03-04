<?php
    namespace Databases;

    class TrackerBugsCategories extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['id', 'category_english', 'category_french'];
        protected string $primaryKey = 'id';
        protected string $tableName = 'tracker_bugs_categories';

        public function findAllOrderedById(): array
        {
            return $this->selectWhere([], 'id');
        }

        public function findAllOrderedByLanguage(string $language): array
        {
            return $this->selectWhere([], 'category_' . $language);
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

    }
