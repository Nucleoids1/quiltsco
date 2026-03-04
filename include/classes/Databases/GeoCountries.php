<?php
    namespace Databases;

    class GeoCountries extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'quiltsco';
        protected bool $autoincrement = true;
        protected array $fields = ['country_id', 'country_code', 'country_name', 'reg_name'];
        protected string $primaryKey = 'country_id';
        protected string $tableName = 'geo_countries';

        public function findAllOrderedByCountryId(): array
        {
            return $this->selectWhere([], 'country_id');
        }

        public function findById(int $id): ?array
        {
            return $this->selectPrimaryKey($id);
        }

    }
