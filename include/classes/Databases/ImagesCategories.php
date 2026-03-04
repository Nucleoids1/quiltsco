<?php
    namespace Databases;

    class ImagesCategories extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['id', 'name', 'name_en', 'name_fr', 'name_sp', 'worksafe'];
        protected string $primaryKey = 'id';
        protected string $tableName = 'images_categories';

        public function findAllOrderedByName(): array
        {
            return $this->selectWhere([], 'name ASC');
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

    }
