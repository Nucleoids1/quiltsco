<?php
    namespace Databases;

    class MessagesIndex extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['receiver_id', 'sender_id', 'last_activity', 'message_id_received', 'message_id_sent'];
        protected array $primaryKey = ['receiver_id', 'last_activity', 'sender_id'];
        protected string $tableName = 'messages_index';

        public function countInboxVisibleToUser(int $receiverId): int
        {
            $row = $this->sqlReadRow(
                'SELECT COUNT(*) AS num FROM messages_index WHERE receiver_id = ? AND sender_id NOT IN (SELECT link_id FROM ignored WHERE user_id = ?)',
                [$receiverId, $receiverId]
            );

            return (int)($row['num'] ?? 0);
        }

        public function truncateTable(): void
        {
            $this->sqlWrite('TRUNCATE TABLE messages_index');
        }

        public function selectInboxVisibleToUserPage(int $receiverId, int $offset, int $limit): array
        {
            return $this->sqlRead(
                'SELECT * FROM messages_index WHERE receiver_id = ? AND sender_id NOT IN (SELECT link_id FROM ignored WHERE user_id = ?) ORDER BY last_received DESC, last_sent DESC, sender_id' . $this->buildLimitOffsetClause($limit, $offset),
                [$receiverId, $receiverId]
            );
        }

        public function trackMessageReceived(int $senderId, int $receiverId, int $messageId): void
        {
            $this->insertArray([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'last_received' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
                'message_id_received' => $messageId
            ]);
        }

        public function trackMessageSent(int $receiverId, int $senderId, int $messageId): void
        {
            $this->insertArray([
                'receiver_id' => $receiverId,
                'sender_id' => $senderId,
                'last_sent' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
                'message_id_sent' => $messageId
            ]);
        }

        public function indexReceivedMessage(int $senderId, int $receiverId, string $lastReceived, int $messageId): void
        {
            $this->insertArray([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'last_received' => $lastReceived,
                'message_id_received' => $messageId
            ]);
        }

        public function indexSentMessage(int $receiverId, int $senderId, string $lastSent, int $messageId): void
        {
            $this->insertArray([
                'receiver_id' => $receiverId,
                'sender_id' => $senderId,
                'last_sent' => $lastSent,
                'message_id_sent' => $messageId
            ]);
        }

        public function updateLastReceived(int $senderId, int $receiverId, string $lastReceived, int $messageId): void
        {
            $this->updateWhere(
                ['last_received' => $lastReceived, 'message_id_received' => $messageId],
                ['sender_id' => $senderId, 'receiver_id' => $receiverId]
            );
        }

        public function updateLastSent(int $receiverId, int $senderId, string $lastSent, int $messageId): void
        {
            $this->updateWhere(
                ['last_sent' => $lastSent, 'message_id_sent' => $messageId],
                ['receiver_id' => $receiverId, 'sender_id' => $senderId]
            );
        }

        public function findBySenderAndReceiver(int $senderId, int $receiverId): ?array
        {
            return $this->selectWhereRow(['sender_id' => $senderId, 'receiver_id' => $receiverId]);
        }

        public function findByReceiverAndSender(int $receiverId, int $senderId): ?array
        {
            return $this->selectWhereRow(['receiver_id' => $receiverId, 'sender_id' => $senderId]);
        }

    }
