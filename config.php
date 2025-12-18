<?php

function loadEnv($path)
{
    if (!file_exists($path)) {
        throw new Exception('.env file not found');
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Ignorera kommentarer
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Dela upp vid första =
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Sätt som miljövariabel
        putenv("$name=$value");
        $_ENV[$name] = $value;
    }
}

// Ladda .env-filen
loadEnv(__DIR__ . '/.env');

// Nu kan du använda API-nyckeln
define('API_KEY', $_ENV['API_KEY']);

try {
    $database = new PDO('sqlite:' . __DIR__ . '/yrgopelag.db');
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
