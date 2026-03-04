<?php
    namespace DatabasesLocation;

    class DataUsaStates extends \DatabasesSchemes\PrimaryKey {

        protected string $database = 'location';
        protected bool $autoincrement = true;
        protected array $fields = ['id', 'iso2', 'name', 'name_english', 'name_french'];
        protected string $primaryKey = 'id';
        protected string $tableName = 'data_usa_states';

    }
