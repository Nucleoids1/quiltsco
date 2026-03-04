<?php
    namespace Databases;

    class CommunityMessagesUpdates extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['update_id', 'message_id', 'update_user_id', 'update_body', 'update_posted_on', 'update_ip'];
        protected string $primaryKey = 'update_id';
        protected string $tableName = 'community_messages_updates';


        public function selectByMessageId(int $messageId): array
        {
            return $this->selectWhere([
                'message_id' => $messageId,
            ], 'update_posted_on');
        }

        public function deleteByThreadId(string $threadId): void
        {
            $this->sqlWrite(
                'DELETE FROM community_messages_updates WHERE message_id IN (SELECT message_id FROM community_messages WHERE thread_id = ?)',
                [$threadId]
            );
        }

        public function addUpdate(int $messageId, int $userId, string $body, string $ip): void
        {
            $this->insertArray([
                'message_id' => $messageId,
                'update_user_id' => $userId,
                'update_body' => $body,
                'update_posted_on' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
                'update_ip' => $ip
            ]);
        }

        public function deleteByMessageId(int $messageId): void
        {
            $this->deleteWhere(['message_id' => $messageId]);
        }

    }
