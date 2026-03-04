<?php
    namespace Databases;

    class CommunityThreads extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['thread_id', 'forum_id', 'thread_user_id', 'thread_posted_on', 'thread_last_user_id', 'thread_last_posted_on', 'thread_title', 'thread_sticky', 'thread_locked', 'thread_views', 'thread_messages', 'thread_vote', 'thread_ignore_permissions', 'thread_first_message_id', 'thread_last_message_id'];
        protected string $primaryKey = 'thread_id';
        protected string $tableName = 'community_threads';

        /**
         * Return a thread with forum/section/community context for permission checks.
         */
        public function selectPageByForum(int $forumId, int $offset, int $limit): array
        {
            return $this->selectWhere([
                'forum_id' => $forumId,
            ], 'thread_sticky DESC, thread_last_posted_on DESC', $limit, [
                'offset' => $offset,
            ]);
        }

        public function findByLegacyId(int $id): ?array
        {
            return $this->selectWhereRow(['id' => $id]);
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

        public function countByLegacyForumId(int $forumId): int
        {
            return $this->count(['forum_id' => $forumId]);
        }

        public function selectRecentByForum(int $forumId, int $limit): array
        {
            return $this->selectWhere([
                'forum_id' => $forumId,
            ], 'thread_sticky DESC, thread_last_posted_on DESC', $limit);
        }

        public function findThreadContext(string $threadId): ?array
        {
            return $this->sqlReadRow(
                "SELECT *
                FROM community_threads
                INNER JOIN community_forums
                    ON community_threads.forum_id = community_forums.forum_id
                INNER JOIN community_sections
                    ON community_forums.section_id = community_sections.section_id
                INNER JOIN community
                    ON community_sections.community_id = community.community_id
                WHERE community_threads.thread_id = ?
                AND community_forums.forum_deleted = '0'
                AND community_sections.section_deleted = '0'",
                [$threadId]
            );
        }

        public function findThreadContextForDisplay(string $threadId): ?array
        {
            return $this->sqlReadRow(
                'SELECT * FROM community_threads, community_forums, community_sections, community WHERE community_threads.thread_id = ? AND community_threads.forum_id = community_forums.forum_id AND community_forums.section_id = community_sections.section_id AND community_sections.community_id = community.community_id',
                [$threadId]
            );
        }

        public function findLegacyMaxForumThreadIdByForumId(int $forumId): ?array
        {
            return $this->sqlReadRow('SELECT MAX(id) AS max FROM forum_threads WHERE forum_id = ?', [$forumId]);
        }

        public function countByForum(string $forumId): int
        {
            return $this->count(['forum_id' => $forumId]);
        }

        /**
         * Sum all cached thread message counts for a forum.
         */
        public function sumMessagesByForum(string $forumId): int
        {
            $row = $this->sqlReadRow(
                'SELECT SUM(thread_messages) AS sum FROM community_threads WHERE forum_id = ?',
                [$forumId]
            );

            return (int)($row['sum'] ?? 0);
        }

        public function createThread(int $forumId, int $userId, string $title, int $firstMessageId): int
        {
            $now = date('Y-m-d H:i:s', server('REQUEST_TIME'));
            return $this->insertArray([
                'forum_id' => $forumId,
                'thread_user_id' => $userId,
                'thread_posted_on' => $now,
                'thread_last_user_id' => $userId,
                'thread_last_posted_on' => $now,
                'thread_title' => $title,
                'thread_first_message_id' => $firstMessageId,
                'thread_last_message_id' => $firstMessageId
            ]);
        }

        public function incrementViews(int $threadId): void
        {
            $this->sqlWrite('UPDATE community_threads SET thread_views = thread_views + 1 WHERE thread_id = ?', [$threadId]);
        }

        public function setLocked(int $threadId, int $locked): void
        {
            $this->updateWhere(['thread_locked' => $locked], ['thread_id' => $threadId]);
        }

        public function setSticky(int $threadId, int $sticky): void
        {
            $this->updateWhere(['thread_sticky' => $sticky], ['thread_id' => $threadId]);
        }

        public function moveForum(int $threadId, int $forumId): void
        {
            $this->updateWhere(['forum_id' => $forumId], ['thread_id' => $threadId]);
        }

        public function updateTitle(int $threadId, string $title): void
        {
            $this->updateWhere(['thread_title' => $title], ['thread_id' => $threadId]);
        }

        public function updateLastMessage(int $threadId, int $lastUserId, string $lastPostedOn, int $messageCount, int $lastMessageId): void
        {
            $this->updateWhere([
                'thread_last_user_id' => $lastUserId,
                'thread_last_posted_on' => $lastPostedOn,
                'thread_messages' => $messageCount,
                'thread_last_message_id' => $lastMessageId,
            ], ['thread_id' => $threadId]);
        }

        public function deleteByThreadId(int $threadId): void
        {
            $this->deleteWhere(['thread_id' => $threadId]);
        }

        public function selectAllLegacyOrderedById(): array
        {
            return $this->selectWhere([], 'id');
        }

        /**
         * Update thread replies count only
         */
        public function updateThreadRepliesCount(int $threadId, int $repliesCount): void
        {
            $this->updateWhere(['thread_replies' => $repliesCount], ['id' => $threadId]);
        }

        /**
         * Update thread first post ID
         */
        public function updateThreadFirstPostId(int $threadId, int $firstPostId): void
        {
            $this->updateWhere(['thread_first_post_id' => $firstPostId], ['id' => $threadId]);
        }

        /**
         * Update thread last post ID
         */
        public function updateThreadLastPostId(int $threadId, int $lastPostId): void
        {
            $this->updateWhere(['thread_last_post_id' => $lastPostId], ['id' => $threadId]);
        }

        public function countByUserId(int $userId): int
        {
            return $this->count(['thread_user_id' => $userId]);
        }

    }
