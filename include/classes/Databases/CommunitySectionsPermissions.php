<?php
    namespace Databases;

    class CommunitySectionsPermissions extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['section_id', 'user_id', 'permission'];
        protected array $primaryKey = ['section_id', 'user_id', 'permission'];
        protected string $tableName = 'community_sections_permissions';



        public function findBySectionAndUser(int $sectionId, int $userId): ?array
        {
            return $this->selectWhereRow([
                'section_id' => $sectionId,
                'user_id' => $userId,
            ]);
        }

        public function selectDistinctUserIdsBySection(int $sectionId): array
        {
            return $this->sqlRead(
                'SELECT DISTINCT(user_id) AS user_id FROM community_sections_permissions WHERE section_id = ?',
                [$sectionId]
            );
        }

        public function selectBySectionAndUser(int $sectionId, int $userId): array
        {
            return $this->selectWhere([
                'section_id' => $sectionId,
                'user_id' => $userId,
            ], 'permission');
        }

        public function selectDistinctPermissions(): array
        {
            return $this->sqlRead('SELECT DISTINCT(permission) AS permission FROM community_sections_permissions ORDER BY permission');
        }

        public function selectDistinctPermissionsExcept(string $permission): array
        {
            return $this->sqlRead(
                'SELECT DISTINCT(permission) AS permission FROM community_sections_permissions WHERE permission != ?',
                [$permission]
            );
        }

        public function hasPermissionBySectionAndUser(int $sectionId, int $userId, string $permission): bool
        {
            return (bool)$this->count([
                'section_id' => $sectionId,
                'user_id' => $userId,
                'permission' => $permission,
            ]);
        }

        public function grantPermission(int $sectionId, int $userId, string $permission): void
        {
            $this->insertArray([
                'section_id' => $sectionId,
                'user_id' => $userId,
                'permission' => $permission,
            ]);
        }

        public function grantPermissionOrUpdate(int $sectionId, int $userId, string $permission): void
        {
            $this->insertArrayUpdateRow(
                [
                    'section_id' => $sectionId,
                    'user_id' => $userId,
                    'permission' => $permission,
                ],
                ['permission' => $permission]
            );
        }

        public function existsBySectionAndUser(int $sectionId, int $userId): bool
        {
            return (bool)$this->count(['section_id' => $sectionId, 'user_id' => $userId]);
        }

        public function deleteBySectionAndUser(int $sectionId, int $userId): void
        {
            $this->deleteWhere([
                'section_id' => $sectionId,
                'user_id' => $userId,
            ]);
        }
    }
