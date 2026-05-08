<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;
use PDOException;

class CreateDatabase extends Command
{
    protected $signature = 'db:create';

    protected $description = 'Create the configured PostgreSQL database if it does not exist';

    public function handle()
    {
        $defaultConnection = config('database.default');

        if ($defaultConnection !== 'pgsql') {
            $this->error("The active database connection is '{$defaultConnection}'. This command currently supports only pgsql.");

            return self::FAILURE;
        }

        $config = config('database.connections.pgsql');
        $database = $config['database'] ?? null;
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 5432;
        $username = $config['username'] ?? null;
        $password = $config['password'] ?? null;
        $maintenanceDatabase = $config['maintenance_database'] ?? 'postgres';

        if (!$database) {
            $this->error('No pgsql database name is configured.');

            return self::FAILURE;
        }

        try {
            $pdo = new PDO(
                "pgsql:host={$host};port={$port};dbname={$maintenanceDatabase}",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $statement = $pdo->prepare('SELECT 1 FROM pg_database WHERE datname = :database');
            $statement->execute(['database' => $database]);

            if ($statement->fetchColumn()) {
                $this->info("Database '{$database}' already exists.");

                return self::SUCCESS;
            }

            $quotedDatabase = '"' . str_replace('"', '""', $database) . '"';
            $pdo->exec("CREATE DATABASE {$quotedDatabase}");

            $this->info("Database '{$database}' created successfully.");

            return self::SUCCESS;
        } catch (PDOException $e) {
            $this->error('Failed to create database: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
