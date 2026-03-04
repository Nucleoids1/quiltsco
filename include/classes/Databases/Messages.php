<?php
    namespace Databases;

    class Messages extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['id', 'sender_id', 'recipiant_id', 'body', 'posted_on', 'viewed'];
        protected string $primaryKey = 'id';
        protected string $tableName = 'messages';


        public function findAllOrderedById(): array
        {
            return $this->selectWhere([], 'id ASC');
        }

        public function selectOldMessages(): array
        {
            return $this->sqlRead('SELECT * FROM messages_old ORDER BY id');
        }

        public function countConversationVisibleToUser(int $userId, int $authId): int
        {
            $row = $this->sqlReadRow(
                "SELECT COUNT(id) AS num FROM messages WHERE (sender_id = ? AND recipiant_id = ? AND sender_id NOT IN (SELECT link_id FROM ignored WHERE user_id = ?)) OR (sender_id = ? AND recipiant_id = ? AND recipiant_id NOT IN (SELECT link_id FROM ignored WHERE user_id = ?))",
                [$userId, $authId, $authId, $authId, $userId, $authId]
            );

            return (int)($row['num'] ?? 0);
        }

        public function selectConversationVisibleToUserPage(int $userId, int $authId, int $offset, int $limit): array
        {
            return $this->sqlRead(
                "SELECT body, sender_id, posted_on FROM messages WHERE (sender_id = ? AND recipiant_id = ? AND sender_id NOT IN (SELECT link_id FROM ignored WHERE user_id = ?)) OR (sender_id = ? AND recipiant_id = ? AND recipiant_id NOT IN (SELECT link_id FROM ignored WHERE user_id = ?)) ORDER BY id DESC" . $this->buildLimitOffsetClause($limit, $offset),
                [$userId, $authId, $authId, $authId, $userId, $authId]
            );
        }

        public function sendMessage(int $senderId, int $recipientId, string $body): int
        {
            return $this->insertArray([
                'sender_id' => $senderId,
                'recipiant_id' => $recipientId,
                'body' => $body,
                'posted_on' => date('Y-m-d H:i:s', server('REQUEST_TIME'))
            ]);
        }

        public function sendMessageWithTimestamp(int $senderId, int $recipientId, string $body, string $postedOn): int
        {
            return $this->insertArray([
                'sender_id' => $senderId,
                'recipiant_id' => $recipientId,
                'body' => $body,
                'posted_on' => $postedOn
            ]);
        }

        public function migrateMessage(int $senderId, int $recipientId, string $body, string $postedOn, int $viewed): int
        {
            return $this->insertArray([
                'sender_id' => $senderId,
                'recipiant_id' => $recipientId,
                'body' => $body,
                'posted_on' => $postedOn,
                'viewed' => $viewed
            ]);
        }

        public function markConversationViewed(int $senderId, int $recipiantId): void
        {
            $this->updateWhere(['viewed' => '1'], ['sender_id' => $senderId, 'recipiant_id' => $recipiantId]);
        }

        public function findLastBySenderId(int $senderId): ?array
        {
            return $this->selectWhereRow(['sender_id' => $senderId], 'id DESC');
        }

        public function findLastBySenderIdAndReceiverId(int $senderId, int $receiverId): ?array
        {
            return $this->selectWhereRow(['sender_id' => $senderId, 'receiver_id' => $receiverId], 'id DESC');
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

        public function countUnreadByRecipientId(int $recipiantId): int
        {
            return $this->count(['recipiant_id' => $recipiantId, 'viewed' => 0]);
        }

        public function countUnreadBySenderAndRecipient(int $senderId, int $recipiantId): int
        {
            return $this->count(['sender_id' => $senderId, 'recipiant_id' => $recipiantId, 'viewed' => '0']);
        }

    }
