<?php
    namespace Databases;

    class CommunitySections extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['section_id', 'community_id', 'section_order_id', 'section_name_en', 'section_name_fr', 'section_name_sp', 'section_deleted'];
        protected string $primaryKey = 'section_id';
        protected string $tableName = 'community_sections';

        public function selectActiveByCommunity(int $communityId): array
        {
            return $this->selectWhere([
                'community_id' => $communityId,
                'section_deleted' => '0',
            ]);
        }


        public function selectActiveByCommunityOrdered(int $communityId): array
        {
            return $this->selectWhere([
                'community_id' => $communityId,
                'section_deleted' => '0',
            ], 'section_order_id');
        }

        public function findSectionContext(string $sectionId): ?array
        {
            return $this->sqlReadRow(
                'SELECT * FROM community_sections, community WHERE community_sections.section_id = ? AND community_sections.community_id = community.community_id',
                [$sectionId]
            );
        }

        public function findActiveSectionContext(string $sectionId): ?array
        {
            return $this->sqlReadRow(
                "SELECT * FROM community_sections, community WHERE community_sections.section_id = ? AND community_sections.section_deleted = '0' AND community_sections.community_id = community.community_id",
                [$sectionId]
            );
        }

        public function findPreviousActiveSectionByOrder(string $communityId, string $orderId): ?array
        {
            return $this->sqlReadRow(
                "SELECT * FROM community_sections WHERE community_id = ? AND section_deleted = '0' AND section_order_id < ? ORDER BY section_order_id DESC LIMIT 1",
                [$communityId, $orderId]
            );
        }

        public function findMaxOrderByCommunity(int $communityId): ?array
        {
            return $this->selectWhereRow([
                'community_id' => $communityId,
            ], 'section_order_id DESC');
        }

        public function swapOrderIds(int $firstSectionId, int $firstOrderId, int $secondSectionId, int $secondOrderId): void
        {
            $this->updateWhere([
                'section_order_id' => $secondOrderId,
            ], [
                'section_id' => $firstSectionId,
            ]);
            $this->updateWhere([
                'section_order_id' => $firstOrderId,
            ], [
                'section_id' => $secondSectionId,
            ]);
        }

        public function findNextActiveSectionByOrder(string $communityId, string $orderId): ?array
        {
            return $this->sqlReadRow(
                "SELECT * FROM community_sections WHERE community_id = ? AND section_deleted = '0' AND section_order_id > ? ORDER BY section_order_id LIMIT 1",
                [$communityId, $orderId]
            );
        }

        public function createSection(int $communityId, string $nameEnglish, string $nameFrench, int $orderId): int
        {
            return $this->insertArray([
                'community_id' => $communityId,
                'section_name_english' => $nameEnglish,
                'section_name_french' => $nameFrench,
                'section_order_id' => $orderId,
            ]);
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

        public function markDeleted(int $sectionId): void
        {
            $this->updateWhere(['section_deleted' => '1'], ['section_id' => $sectionId]);
        }

        public function updateOrderId(int $sectionId, int $orderId): void
        {
            $this->updateWhere(['section_order_id' => $orderId], ['section_id' => $sectionId]);
        }

        public function updateName(int $sectionId, string $nameEnglish, string $nameFrench): void
        {
            $this->updateWhere([
                'section_name_english' => $nameEnglish,
                'section_name_french' => $nameFrench,
            ], [
                'section_id' => $sectionId,
            ]);
        }

    }
