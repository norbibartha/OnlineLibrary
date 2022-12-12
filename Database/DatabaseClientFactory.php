<?php

namespace Database;

class DatabaseClientFactory
{
    public static function getClient(): DatabaseClient
    {
        return DatabaseClient::getInstance(
            getenv('HOST'),
            getenv('PORT'),
            getenv('DBNAME'),
            getenv('USER'),
            getenv('PASSWORD'),
        );
    }
}