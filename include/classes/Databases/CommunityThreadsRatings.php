<?php
    namespace Databases;

    class CommunityThreadsRatings extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['thread_id', 'user_id', 'category_id', 'posted_on'];
        protected array $primaryKey = ['thread_id', 'user_id'];
        protected string $tableName = 'community_threads_ratings';

        public function upsertRating(int $threadId, int $categoryId, int $userId): void
        {
            $this->replaceArray([
                'thread_id' => $threadId,
                'category_id' => $categoryId,
                'user_id' => $userId,
                'posted_on' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
            ]);
        }

        public function findByThreadAndUser(int $threadId, int $userId): ?array
        {
            return $this->selectWhereRow([
                'thread_id' => $threadId,
                'user_id' => $userId,
            ]);
        }


        public function countByThreadId(int $threadId): int
        {
            return $this->count([
                'thread_id' => $threadId,
            ]);
        }

        public function countPositiveByThreadId(int $threadId): int
        {
            $row = $this->sqlReadRow(
                "SELECT COUNT(*) AS count FROM community_threads_ratings, community_threads_categories WHERE community_threads_ratings.category_id = community_threads_categories.id AND community_threads_ratings.thread_id = ? AND community_threads_categories.positive = '1'",
                [$threadId]
            );

            return (int)($row['count'] ?? 0);
        }

        public function findTopCategoryStatsByThreadId(int $threadId): ?array
        {
            return $this->sqlReadRow(
                'SELECT COUNT(*) AS num, community_threads_ratings.category_id, community_threads_categories.positive FROM community_threads_ratings, community_threads_categories WHERE community_threads_ratings.thread_id = ? AND community_threads_ratings.category_id = community_threads_categories.id GROUP BY community_threads_ratings.category_id ORDER BY num DESC, posted_on ASC LIMIT 1',
                [$threadId]
            );
        }

        public function deleteByThreadId(int $threadId): void
        {
            $this->deleteWhere(['thread_id' => $threadId]);
        }

        public function deleteByThreadIdAndUserId(int $threadId, int $userId): void
        {
            $this->deleteWhere(['thread_id' => $threadId, 'user_id' => $userId]);
        }

    }
