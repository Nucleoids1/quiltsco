<?php
    namespace Databases;

    class CommunityBannedIps extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['community_id', 'ip', 'added_on', 'last_used_on', 'expires_on'];
        protected array $primaryKey = ['community_id', 'ip'];
        protected string $tableName = 'community_banned_ips';

        public function selectByCommunityOrdered(int $communityId): array
        {
            return $this->selectWhere([
                'community_id' => $communityId,
            ], 'added_on');
        }

        public function existsByCommunityAndIp(int $communityId, string $ip): bool
        {
            return (bool)$this->count([
                'community_id' => $communityId,
                'ip' => $ip,
            ]);
        }

        public function banIp(int $communityId, string $ip): void
        {
            $this->insertArrayUpdateRow(
                ['community_id' => $communityId, 'ip' => $ip],
                ['community_id' => $communityId]
            );
        }

        public function unbanIp(int $communityId, string $ip): void
        {
            $this->deleteWhere([
                'community_id' => $communityId,
                'ip' => $ip,
            ]);
        }

    }
