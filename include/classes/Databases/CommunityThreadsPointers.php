<?php
    namespace Databases;

    class CommunityThreadsPointers extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['thread_id', 'user_id', 'message_id'];
        protected array $primaryKey = ['user_id', 'thread_id'];
        protected string $tableName = 'community_threads_pointers';

        public function findByUserAndThread(int $userId, int $threadId): ?array
        {
            return $this->selectWhereRow([
                'user_id' => $userId,
                'thread_id' => $threadId,
            ]);
        }

        public function updateReadPointer(int $threadId, int $userId, int $messageId): void
        {
            $this->insertArrayUpdateRow(
                [
                    'thread_id' => $threadId,
                    'user_id' => $userId,
                    'message_id' => $messageId
                ],
                ['message_id' => $messageId]
            );
        }

        public function upsertPointer(int $threadId, int $userId, int $messageId): void
        {
            $this->replaceArray([
                'thread_id' => $threadId,
                'user_id' => $userId,
                'message_id' => $messageId,
            ]);
        }

        public function deleteByThreadId(int $threadId): void
        {
            $this->deleteWhere(['thread_id' => $threadId]);
        }

    }
