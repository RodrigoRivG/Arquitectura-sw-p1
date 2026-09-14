<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private const HOST = 'localhost';
    private const PORT = '5432';
    private const DBNAME = 'arq-sw-p-1';
    private const USER = 'postgres';
    private const PASSWORD = '12345';

    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                self::HOST,
                self::PORT,
                self::DBNAME
            );

            try {
                self::$connection = new PDO($dsn, self::USER, self::PASSWORD, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                throw new PDOException(
                    'Error de conexión a la base de datos: ' . $e->getMessage(),
                    (int) $e->getCode()
                );
            }
        }

        return self::$connection;
    }
}
