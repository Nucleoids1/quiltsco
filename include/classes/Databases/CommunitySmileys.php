<?php
    namespace Databases;

    class CommunitySmileys extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = false;
        protected array $fields = ['name', 'filename'];
        protected string $primaryKey = 'name';
        protected string $tableName = 'community_smileys';


        public function selectAllByName(): array
        {
            return $this->selectWhere([], 'name');
        }

    }
