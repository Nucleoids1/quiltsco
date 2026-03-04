<?php
    namespace Databases;

    class Comments extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['id', 'user_id', 'link_id', 'posted_on', 'comment'];
        protected string $primaryKey = 'id';
        protected string $tableName;

        public function __construct(string $tableName)
        {
            $this->tableName = $tableName;
            parent::__construct();
        }

        public function tableName(): string
        {
            return $this->tableName;
        }

        public function addComment(int $linkId, int $userId, string $comment): int
        {
            return $this->insertArray([
                'link_id' => $linkId,
                'user_id' => $userId,
                'comment' => $comment,
                'posted_on' => date('Y-m-d H:i:s', server('REQUEST_TIME'))
            ]);
        }

        /**
         * Delete comment by ID and link ID
         */
        public function deleteComment(int $id, int $linkId): void
        {
            $this->deleteWhere(['id' => $id, 'link_id' => $linkId]);
        }

        /**
         * Delete comment by link ID
         */
        public function deleteByLinkId(int $linkId): void
        {
            $this->deleteWhere(['link_id' => $linkId]);
        }

        /**
         * Delete comment by ID, link ID, and user ID (for user-specific deletion)
         */
        public function deleteCommentByUser(int $id, int $linkId, int $userId): void
        {
            $this->deleteWhere(['id' => $id, 'link_id' => $linkId, 'user_id' => $userId]);
        }

        public function countByLinkId(int $linkId): int
        {
            return $this->count(['link_id' => $linkId]);
        }

        public function findAllByLinkId(int $linkId): array
        {
            return $this->selectWhere(['link_id' => $linkId], 'id DESC');
        }

        public function findLastByLinkId(int $linkId): ?array
        {
            return $this->selectWhereRow(['link_id' => $linkId], 'id DESC');
        }

        public function findLastByLinkIdAndUserId(int $linkId, int $userId): ?array
        {
            return $this->selectWhereRow(['link_id' => $linkId, 'user_id' => $userId], 'id DESC');
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

    }
