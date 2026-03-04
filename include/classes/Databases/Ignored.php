<?php
    namespace Databases;

    class Ignored extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['user_id', 'link_id', 'posted_on'];
        protected array $primaryKey = ['user_id', 'link_id'];
        protected string $tableName = 'ignored';

        public function isIgnoredByUser(int $userId, int $linkId): bool
        {
            return (bool)$this->count([
                'user_id' => $userId,
                'link_id' => $linkId,
            ]);
        }

    }
