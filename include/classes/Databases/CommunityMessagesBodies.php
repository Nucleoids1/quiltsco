<?php
    namespace Databases;

    class CommunityMessagesBodies extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = false;
        protected array $fields = ['message_id', 'message_body'];
        protected string $primaryKey = 'message_id';
        protected string $tableName = 'community_messages_bodies';


        public function deleteByThreadId(string $threadId): void
        {
            $this->sqlWrite(
                'DELETE FROM community_messages_bodies WHERE message_id IN (SELECT message_id FROM community_messages WHERE thread_id = ?)',
                [$threadId]
            );
        }

        public function deleteByMessageId(int $messageId): void
        {
            $this->deleteWhere(['message_id' => $messageId]);
        }

        public function addMessageBody(int $messageId, string $messageBody): void
        {
            $this->insertArray([
                'message_id' => $messageId,
                'message_body' => $messageBody
            ]);
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

        public function updateBody(int $messageId, string $messageBody): void
        {
            $this->updateWhere(['message_body' => $messageBody], ['message_id' => $messageId]);
        }

    }
