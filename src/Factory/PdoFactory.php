<?php
declare(strict_types=1);

namespace ByteBuddyApi\Factory;

use PDO;

class PdoFactory
{
    public function __invoke(): PDO
    {
        $host = 'byteBuddy-api';
        $port = 3306;
        $database = 'byteBuddy_api';
        $username = 'testing';
        $password = '1234';

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s', $host, $port, $database);
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }
}