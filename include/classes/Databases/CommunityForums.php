<?php
    namespace Databases;

    class CommunityForums extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['forum_id', 'section_id', 'forum_name_en', 'forum_name_fr', 'forum_name_sp', 'forum_description_en', 'forum_description_fr', 'forum_description_sp', 'forum_order_id', 'forum_deleted', 'forum_locked', 'forum_ratings', 'forum_edit_first_message', 'forum_always_show_first_message', 'forum_threads', 'forum_messages'];
        protected string $primaryKey = 'forum_id';
        protected string $tableName = 'community_forums';

        public function findByLegacyId(int $id): ?array
        {
            return $this->selectWhereRow(['id' => $id]);
        }

        public function selectAllLegacyOrderedById(): array
        {
            return $this->selectWhere([], 'id');
        }

        public function selectActiveBySection(int $sectionId): array
        {
            return $this->selectWhere([
                'section_id' => $sectionId,
                'forum_deleted' => '0',
            ]);
        }


        public function selectActiveBySectionOrdered(int $sectionId): array
        {
            return $this->selectWhere([
                'section_id' => $sectionId,
                'forum_deleted' => '0',
            ], 'forum_order_id');
        }

        public function countActiveBySection(int $sectionId): int
        {
            return $this->count([
                'section_id' => $sectionId,
                'forum_deleted' => '0',
            ]);
        }

        public function findForumContext(string $forumId): ?array
        {
            return $this->sqlReadRow(
                'SELECT * FROM community_forums, community_sections, community WHERE community_forums.forum_id = ? AND community_forums.section_id = community_sections.section_id AND community_sections.community_id = community.community_id',
                [$forumId]
            );
        }

        public function findActiveForumContext(string $forumId): ?array
        {
            return $this->sqlReadRow(
                "SELECT * FROM community_forums, community_sections, community WHERE community_forums.forum_id = ? AND community_forums.forum_deleted = '0' AND community_forums.section_id = community_sections.section_id AND community_sections.community_id = community.community_id",
                [$forumId]
            );
        }

        public function findActiveForumContextInActiveSection(string $forumId): ?array
        {
            return $this->sqlReadRow(
                "SELECT * FROM community_forums, community_sections, community WHERE community_forums.forum_id = ? AND community_forums.forum_deleted = '0' AND community_forums.section_id = community_sections.section_id AND community_sections.section_deleted = '0' AND community_sections.community_id = community.community_id",
                [$forumId]
            );
        }


        public function sumForumStatsByCommunity(string $communityId): array
        {
            $row = $this->sqlReadRow(
                'SELECT SUM(forum_threads) AS forum_threads, SUM(forum_messages) AS forum_messages FROM community_forums WHERE section_id IN (SELECT section_id FROM community_sections WHERE community_id = ?)',
                [$communityId]
            );

            return [
                'forum_threads' => (int)($row['forum_threads'] ?? 0),
                'forum_messages' => (int)($row['forum_messages'] ?? 0),
            ];
        }

        public function findPreviousActiveForumByOrder(int $sectionId, int $orderId): ?array
        {
            return $this->selectWhereRow([
                'section_id' => $sectionId,
                'forum_deleted' => '0',
                'forum_order_id <' => $orderId,
            ], 'forum_order_id DESC');
        }

        public function findNextActiveForumByOrder(int $sectionId, int $orderId): ?array
        {
            return $this->selectWhereRow([
                'section_id' => $sectionId,
                'forum_deleted' => '0',
                'forum_order_id >' => $orderId,
            ], 'forum_order_id');
        }

        public function findMaxOrderBySection(int $sectionId): ?array
        {
            return $this->selectWhereRow([
                'section_id' => $sectionId,
            ], 'forum_order_id DESC');
        }

        public function swapOrderIds(int $firstForumId, int $firstOrderId, int $secondForumId, int $secondOrderId): void
        {
            $this->updateWhere([
                'forum_order_id' => $secondOrderId,
            ], [
                'forum_id' => $firstForumId,
            ]);
            $this->updateWhere([
                'forum_order_id' => $firstOrderId,
            ], [
                'forum_id' => $secondForumId,
            ]);
        }

        public function findForumContextInCommunity(string $forumId, string $communityId): ?array
        {
            return $this->sqlReadRow(
                'SELECT * FROM community_forums, community_sections, community WHERE community_forums.forum_id = ? AND community_forums.section_id = community_sections.section_id AND community_sections.community_id = ? AND community_sections.community_id = community.community_id',
                [$forumId, $communityId]
            );
        }

        public function createForum(int $sectionId, string $nameEnglish, string $nameFrench, string $descriptionEnglish, string $descriptionFrench, int $orderId): int
        {
            return $this->insertArray([
                'section_id' => $sectionId,
                'forum_name_english' => $nameEnglish,
                'forum_name_french' => $nameFrench,
                'forum_description_english' => $descriptionEnglish,
                'forum_description_french' => $descriptionFrench,
                'forum_order_id' => $orderId,
            ]);
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

        public function updateForumStats(int $forumId, int $messages, int $threads): void
        {
            $this->updateWhere([
                'forum_messages' => $messages,
                'forum_threads' => $threads,
            ], [
                'forum_id' => $forumId,
            ]);
        }

        public function updateForumMessages(int $forumId, int $messages): void
        {
            $this->updateWhere(['forum_messages' => $messages], ['forum_id' => $forumId]);
        }

        public function markDeleted(int $forumId): void
        {
            $this->updateWhere(['forum_deleted' => 1], ['forum_id' => $forumId]);
        }

        public function markDeletedBySection(int $sectionId): void
        {
            $this->updateWhere(['forum_deleted' => '1'], ['section_id' => $sectionId]);
        }

        public function setLocked(int $forumId, int $locked): void
        {
            $this->updateWhere(['forum_locked' => $locked], ['forum_id' => $forumId]);
        }

        public function setAutomated(int $forumId, int $automated): void
        {
            $this->updateWhere(['forum_automated' => $automated], ['forum_id' => $forumId]);
        }

        public function updateDetails(int $forumId, string $nameEnglish, string $nameFrench, string $descriptionEnglish, string $descriptionFrench): void
        {
            $this->updateWhere([
                'forum_name_english' => $nameEnglish,
                'forum_name_french' => $nameFrench,
                'forum_description_english' => $descriptionEnglish,
                'forum_description_french' => $descriptionFrench,
            ], [
                'forum_id' => $forumId,
            ]);
        }

        /**
         * Update forum statistics (threads and messages count)
         */
        public function updateForumStatistics(int $forumId, int $threadsCount, int $messagesCount): void
        {
            $this->updateWhere([
                'forum_threads' => $threadsCount,
                'forum_messages' => $messagesCount,
            ], ['forum_id' => $forumId]);
        }

        /**
         * Update forum threads count only
         */
        public function updateForumThreadsCount(int $forumId, int $threadsCount): void
        {
            $this->updateWhere(['forum_threads' => $threadsCount], ['forum_id' => $forumId]);
        }

        /**
         * Update forum messages count only
         */
        public function updateForumMessagesCount(int $forumId, int $messagesCount): void
        {
            $this->updateWhere(['forum_messages' => $messagesCount], ['forum_id' => $forumId]);
        }

        /**
         * Update forum last post ID
         */
        public function updateForumLastPostId(int $forumId, int $lastPostId): void
        {
            $this->updateWhere(['forum_last_post_id' => $lastPostId], ['forum_id' => $forumId]);
        }

    }
