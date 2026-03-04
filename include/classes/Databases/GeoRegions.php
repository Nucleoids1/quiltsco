<?php
    namespace Databases;

    class GeoRegions extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['region_id', 'region_name', 'country_id', 'country_name'];
        protected string $primaryKey = 'region_id';
        protected string $tableName = 'geo_regions';

        public function findByCountryId(int $countryId): array
        {
            return $this->selectWhere(['country_id' => $countryId], 'region_name');
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

    }
