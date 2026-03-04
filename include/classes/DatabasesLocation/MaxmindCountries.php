<?php
    namespace DatabasesLocation;

    class MaxmindCountries extends \DatabasesSchemes\General {

        protected string $database = 'location';
        protected array $fields = ['i_from', 'i_to', 'ip14', 'ip_from', 'ip_to', 'country_code2', 'country_name'];
        protected string $tableName = 'maxmind_countries';

    }
