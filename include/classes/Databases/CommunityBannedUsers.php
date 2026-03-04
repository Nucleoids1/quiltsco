<?php
    namespace Databases;

    class CommunityBannedUsers extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['community_id', 'user_id', 'added_on', 'last_used_on', 'expires_on'];
        protected array $primaryKey = ['community_id', 'user_id'];
        protected string $tableName = 'community_banned_users';

        public function selectByCommunityOrdered(int $communityId): array
        {
            return $this->selectWhere([
                'community_id' => $communityId,
            ], 'added_on');
        }

        public function existsByCommunityAndUser(int $communityId, int $userId): bool
        {
            return (bool)$this->count([
                'community_id' => $communityId,
                'user_id' => $userId,
            ]);
        }

        public function banUser(int $communityId, int $userId): void
        {
            $this->insertArrayUpdateRow(
                ['community_id' => $communityId, 'user_id' => $userId],
                ['community_id' => $communityId]
            );
        }

        public function unbanUser(int $communityId, int $userId): void
        {
            $this->deleteWhere([
                'community_id' => $communityId,
                'user_id' => $userId,
            ]);
        }

    }
