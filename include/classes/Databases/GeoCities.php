<?php
    namespace Databases;

    class GeoCities extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['city_id', 'city_name', 'region_id', 'latitude', 'longitude', 'priority'];
        protected string $primaryKey = 'city_id';
        protected string $tableName = 'geo_cities';

        public function findByRegionId(int $regionId): array
        {
            return $this->selectWhere(['region_id' => $regionId], 'city_name');
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

    }
