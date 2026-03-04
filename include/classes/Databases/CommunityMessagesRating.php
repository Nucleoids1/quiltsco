<?php
    namespace Databases;

    class CommunityMessagesRating extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['message_id', 'user_id', 'vote'];
        protected array $primaryKey = ['message_id', 'user_id'];
        protected string $tableName = 'community_messages_rating';



        public function upsertVote(int $messageId, int $userId, string $vote): void
        {
            $this->replaceArray([
                'message_id' => $messageId,
                'user_id' => $userId,
                'vote' => $vote,
            ]);
        }

        public function countByMessageAndVote(int $messageId, string $vote): int
        {
            return $this->count([
                'message_id' => $messageId,
                'vote' => $vote,
            ]);
        }

        public function hasVoteByMessageAndUser(int $messageId, int $userId): bool
        {
            return (bool)$this->count([
                'message_id' => $messageId,
                'user_id' => $userId,
            ]);
        }

        public function deleteByThreadId(string $threadId): void
        {
            $this->sqlWrite(
                'DELETE FROM community_messages_rating WHERE message_id IN (SELECT message_id FROM community_messages WHERE thread_id = ?)',
                [$threadId]
            );
        }

        public function deleteByMessageId(int $messageId): void
        {
            $this->deleteWhere(['message_id' => $messageId]);
        }

    }
