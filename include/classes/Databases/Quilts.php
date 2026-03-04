<?php
    namespace Databases;

    class Quilts extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['id', 'name', 'description', 'quilt_width', 'quilt_height', 'tile_width', 'tile_height', 'score_minimum', 'score_maximum', 'timelimit', 'side_pixels', 'level', 'show_all', 'work_on_all', 'start_anywhere', 'multiple', 'photographs_allowed', 'edge_wrap', 'moderated', 'active', 'finished', 'posted_on', 'modified_on', 'data_thumb', 'data_full_jpg', 'data_full_png'];
        protected string $primaryKey = 'id';
        protected string $tableName = 'quilts';

        public function findUnfinishedById(int $quiltId): ?array
        {
            return $this->selectWhereRow([
                'id' => $quiltId,
                'finished' => '0',
            ]);
        }

        public function createQuilt(
            string $name,
            string $description,
            int $quiltWidth,
            int $quiltHeight,
            int $tileWidth,
            int $tileHeight,
            int $timelimit,
            int $sidePixels,
            int $level,
            int $showAll,
            int $workOnAll,
            int $multiple,
            int $moderated
        ): int {
            $now = date('Y-m-d H:i:s', server('REQUEST_TIME'));
            return $this->insertArray([
                'name' => $name,
                'description' => $description,
                'quilt_width' => $quiltWidth,
                'quilt_height' => $quiltHeight,
                'tile_width' => $tileWidth,
                'tile_height' => $tileHeight,
                'timelimit' => $timelimit,
                'side_pixels' => $sidePixels,
                'level' => $level,
                'show_all' => $showAll,
                'work_on_all' => $workOnAll,
                'multiple' => $multiple,
                'moderated' => $moderated,
                'posted_on' => $now,
                'modified_on' => $now,
            ]);
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

        public function countActive(): int
        {
            return $this->count(['finished' => '0']);
        }

        public function countFinished(): int
        {
            return $this->count(['finished' => 1]);
        }

        public function selectActivePage(int $limit, int $offset): array
        {
            return $this->selectWhere(['finished' => '0'], 'modified_on DESC, id DESC', $limit, ['offset' => $offset]);
        }

        public function selectFinishedPage(int $limit, int $offset): array
        {
            return $this->selectWhere(['finished' => 1], 'modified_on DESC, id DESC', $limit, ['offset' => $offset]);
        }

        public function selectAllOrderedById(?int $id = null): array
        {
            return $this->selectWhere($id ? ['id' => $id] : [], 'id');
        }

        public function saveFullJpg(int $id, string $data): void
        {
            $this->updateWhere(['data_full_jpg' => $data], ['id' => $id]);
        }

        public function saveFullPng(int $id, string $data): void
        {
            $this->updateWhere(['data_full_png' => $data], ['id' => $id]);
        }

        public function saveThumbnail(int $id, string $data): void
        {
            $this->updateWhere(['data_thumb' => $data], ['id' => $id]);
        }

        public function touch(int $id): void
        {
            $this->updateWhere(['modified_on' => date('Y-m-d H:i:s', server('REQUEST_TIME'))], ['id' => $id]);
        }

        public function deleteById(int $id): void
        {
            $this->deletePrimaryKey($id);
        }

        public function update(
            int $id,
            string $name,
            string $description,
            int $timelimit,
            int $sidePixels,
            int $level,
            int $showAll,
            int $workOnAll,
            int $multiple,
            int $startAnywhere,
            int $moderated
        ): void {
            $this->updateWhere([
                'name' => $name,
                'description' => $description,
                'timelimit' => $timelimit,
                'side_pixels' => $sidePixels,
                'level' => $level,
                'show_all' => $showAll,
                'work_on_all' => $workOnAll,
                'multiple' => $multiple,
                'start_anywhere' => $startAnywhere,
                'moderated' => $moderated,
            ], ['id' => $id]);
        }

        public function markFinished(int $id): void
        {
            $this->updateWhere(['finished' => '1'], ['id' => $id]);
        }

    }
