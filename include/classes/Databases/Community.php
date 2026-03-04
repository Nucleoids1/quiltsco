<?php
    namespace Databases;

    class Community extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['community_id', 'community_name', 'community_creator', 'community_created_on', 'community_deleted'];
        protected string $primaryKey = 'community_id';
        protected string $tableName = 'community';

        public function createCommunity(string $name, int $creatorId): int
        {
            return $this->insertArray([
                'community_name' => $name,
                'community_creator' => $creatorId,
                'community_created_on' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
            ]);
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

        public function selectAllOrderedById(): array
        {
            return $this->selectWhere([], 'community_id');
        }

        public function updateName(int $communityId, string $name): void
        {
            $this->updateWhere([
                'community_name' => $name,
            ], [
                'community_id' => $communityId,
            ]);
        }

    }
