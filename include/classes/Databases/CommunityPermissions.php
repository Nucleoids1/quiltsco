<?php
    namespace Databases;

    class CommunityPermissions extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['community_id', 'user_id', 'permission'];
        protected array $primaryKey = ['community_id', 'user_id', 'permission'];
        protected string $tableName = 'community_permissions';



        public function countAdministratedCommunitiesByUser(int $userId): int
        {
            return $this->count([
                'user_id' => $userId,
                'permission' => 'administrator',
            ]);
        }

        public function hasPermissionByCommunityAndUser(int $communityId, int $userId, string $permission): bool
        {
            return (bool)$this->count([
                'community_id' => $communityId,
                'user_id' => $userId,
                'permission' => $permission,
            ]);
        }

        public function findByCommunityAndUser(int $communityId, int $userId): ?array
        {
            return $this->selectWhereRow([
                'community_id' => $communityId,
                'user_id' => $userId,
            ]);
        }

        public function selectAdministratorCommunitiesByUser(int $userId): array
        {
            return $this->sqlRead(
                "SELECT * FROM community_permissions, community WHERE community_permissions.user_id = ? AND community_permissions.permission = 'administrator' AND community_permissions.community_id = community.community_id AND community.community_deleted = '0'",
                [$userId]
            );
        }

        public function selectPermissionsByCommunityAndUser(int $communityId, int $userId): array
        {
            return $this->selectWhere([
                'community_id' => $communityId,
                'user_id' => $userId,
            ], '', 0, 'permission');
        }

        public function selectDistinctModeratorsByCommunity(int $communityId): array
        {
            return $this->sqlRead(
                'SELECT DISTINCT(user_id) AS user_id, community_id FROM community_permissions WHERE community_id = ? AND permission = ?',
                [$communityId, 'administrator']
            );
        }

        public function selectDistinctUserIdsByCommunity(int $communityId): array
        {
            return $this->sqlRead(
                'SELECT DISTINCT(user_id) AS user_id FROM community_permissions WHERE community_id = ?',
                [$communityId]
            );
        }

        public function selectDistinctNonModeratorAdminsByCommunity(int $communityId): array
        {
            return $this->sqlRead(
                'SELECT DISTINCT(user_id) AS user_id, community_id FROM community_permissions WHERE community_id = ? AND permission != ?',
                [$communityId, 'administrator']
            );
        }

        public function selectDistinctPermissions(): array
        {
            return $this->sqlRead('SELECT DISTINCT(permission) AS permission FROM community_permissions ORDER BY permission');
        }

        public function selectDistinctPermissionsExceptAdministration(): array
        {
            return $this->selectDistinctPermissionsExcept(['administration', 'administrator']);
        }

        public function selectDistinctPermissionsExcept(array $permissions): array
        {
            $query = 'SELECT DISTINCT(permission) AS permission FROM community_permissions';
            $values = [];

            if (count($permissions))
            {
                $query .= ' WHERE ' . implode(' AND ', array_fill(0, count($permissions), 'permission != ?'));
                $values = $permissions;
            }

            $query .= ' ORDER BY permission';

            return $this->sqlRead($query, $values);
        }

        public function grantPermission(int $communityId, int $userId, string $permission): void
        {
            $this->insertArray([
                'community_id' => $communityId,
                'user_id' => $userId,
                'permission' => $permission,
            ]);
        }

        public function deleteByUserAndCommunity(int $communityId, int $userId): void
        {
            $this->deleteWhere(['community_id' => $communityId, 'user_id' => $userId]);
        }

        public function selectAdministratorsByUser(int $userId): array
        {
            return $this->selectWhere(['user_id' => $userId, 'permission' => 'administrator']);
        }

    }
