<?php
    namespace Databases;

    class MessagesEmail extends \DatabasesSchemes\PrimaryKeyComposite {

        protected string $database = 'quiltsco';
        protected array $fields = ['user_id', 'link_id', 'posted_on'];
        protected array $primaryKey = ['user_id', 'link_id'];
        protected string $tableName = 'messages_email';

        public function trackEmailNotification(int $userId, int $linkId): void
        {
            $this->insertArray([
                'user_id' => $userId,
                'link_id' => $linkId,
                'posted_on' => date('Y-m-d H:i:s', server('REQUEST_TIME'))
            ]);
        }

        public function findByUserIdAndLinkId(int $userId, int $linkId): ?array
        {
            return $this->selectWhereRow(['user_id' => $userId, 'link_id' => $linkId]);
        }

        public function deleteByUserIdAndLinkId(int $userId, int $linkId): void
        {
            $this->deleteWhere(['user_id' => $userId, 'link_id' => $linkId]);
        }

    }
