<?php
// Instalador idempotente: crea base de datos, tabla y datos de ejemplo.

$config = require __DIR__ . '/../config/database.example.php';
$config['driver'] = getenv('DB_DRIVER') ?: $config['driver'];
$config['host'] = getenv('DB_HOST') ?: $config['host'];
$config['port'] = getenv('DB_PORT') ?: $config['port'];
$config['database'] = getenv('DB_NAME') ?: $config['database'];
$config['username'] = getenv('DB_USER') ?: $config['username'];
$config['password'] = getenv('DB_PASSWORD') ?: $config['password'];
$config['charset'] = getenv('DB_CHARSET') ?: $config['charset'];

$dsnServer = sprintf('%s:host=%s;port=%s;charset=%s', $config['driver'], $config['host'], $config['port'], $config['charset']);
$dsnDb = sprintf('%s:host=%s;port=%s;dbname=%s;charset=%s', $config['driver'], $config['host'], $config['port'], $config['database'], $config['charset']);

try {
    $pdo = new PDO($dsnServer, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec(sprintf(
        "CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
        str_replace('`', '``', $config['database'])
    ));

    $pdo = new PDO($dsnDb, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS students (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(120) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    $count = (int)$pdo->query('SELECT COUNT(*) AS c FROM students')->fetchColumn();
    if ($count === 0) {
        $stmt = $pdo->prepare('INSERT INTO students (name, email, phone) VALUES (:name, :email, :phone)');
        $seed = [
            ['Ada Lovelace', 'ada@example.com', '+44 1234 567890'],
            ['Alan Turing', 'alan@example.com', '+44 9876 543210'],
        ];
        foreach ($seed as [$name, $email, $phone]) {
            $stmt->execute(['name' => $name, 'email' => $email, 'phone' => $phone]);
        }
    }

    echo "Instalación completada. BD: {$config['database']}\n";
} catch (PDOException $e) {
    fwrite(STDERR, 'Error instalando: ' . $e->getMessage() . "\n");
    exit(1);
}
