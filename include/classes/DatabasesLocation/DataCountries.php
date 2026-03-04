<?php
    namespace DatabasesLocation;

    class DataCountries extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'location';
        protected bool $autoincrement = false;
        protected array $fields = ['iso3', 'iso2', 'tld', 'name', 'isono', 'capital', 'region', 'currency', 'currency_code', 'population'];
        protected string $primaryKey = 'iso3';
        protected string $tableName = 'data_countries';

    }
