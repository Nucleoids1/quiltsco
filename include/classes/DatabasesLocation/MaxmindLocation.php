<?php
    namespace DatabasesLocation;

    class MaxmindLocation extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'location';
        protected bool $autoincrement = false;
        protected array $fields = ['locId', 'country', 'region', 'city', 'postalCode', 'latitude', 'longitude', 'metroCode', 'areaCode'];
        protected string $primaryKey = 'locId';
        protected string $tableName = 'maxmind_location';

    }
