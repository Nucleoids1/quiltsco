<?php
    namespace DatabasesLocation;

    class DataCountriesAllow extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'location';
        protected bool $autoincrement = false;
        protected array $fields = ['iso2'];
        protected string $primaryKey = 'iso2';
        protected string $tableName = 'data_countries_allow';

    }
