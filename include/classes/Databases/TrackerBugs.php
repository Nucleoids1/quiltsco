<?php
    namespace Databases;

    class TrackerBugs extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['id', 'user_id', 'posted_on', 'modified_on', 'category', 'priority', 'status', 'assigned_to', 'summary', 'description'];
        protected string $primaryKey = 'id';
        protected string $tableName = 'tracker_bugs';

        public function incrementViews(int $bugId): void
        {
            $this->sqlWrite('UPDATE tracker_bugs SET views = views + 1 WHERE id = ?', [$bugId]);
        }

        public function createBug(
            int $userId,
            int $category,
            int $priority,
            int $status,
            int $assignedTo,
            string $summary,
            string $description
        ): int {
            return $this->insertArray([
                'user_id' => $userId,
                'posted_on' => date('Y-m-d H:i:s', server('REQUEST_TIME')),
                'category' => $category,
                'priority' => $priority,
                'status' => $status,
                'assigned_to' => $assignedTo,
                'summary' => $summary,
                'description' => $description,
            ]);
        }

        public function updateBug(int $id, string $modifiedOn, int $category, int $priority, int $status, int $assignedTo, string $summary, string $description): void
        {
            $this->updateWhere([
                'modified_on' => $modifiedOn,
                'category' => $category,
                'priority' => $priority,
                'status' => $status,
                'assigned_to' => $assignedTo,
                'summary' => $summary,
                'description' => $description,
            ], [
                'id' => $id,
            ]);
        }

        public function findByFilters(int $status, int $category): array
        {
            $where = [];
            if ($status) { $where['status'] = $status; }
            if ($category) { $where['category'] = $category; }
            return $this->selectWhere($where, 'id DESC');
        }

        public function reopen(int $id): void
        {
            $this->updateWhere(['status' => '1'], ['id' => $id]);
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

        public function countFromId(int $id): int
        {
            return $this->count(['id >=' => $id]);
        }

        public function countAll(): int
        {
            return $this->count();
        }

    }
