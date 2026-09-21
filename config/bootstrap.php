<?php
declare(strict_types=1);

/*
 * Shared application bootstrap.
 *
 * Configuration is read from environment variables so credentials never live
 * in the repository. DATABASE_URL supports Replit PostgreSQL as well as hosted
 * MySQL services; DB_* variables remain useful for local MySQL development.
 */
date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'Africa/Douala');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('srdvvip_session');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'samesite' => 'Lax',
    ]);
    session_start();
}

function env_value(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return $value === false || $value === '' ? $default : $value;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $databaseUrl = env_value('DATABASE_URL');
    if ($databaseUrl && preg_match('/^(postgres(?:ql)?|pgsql):\\/\\//i', $databaseUrl)) {
        $parts = parse_url($databaseUrl);
        $host = $parts['host'] ?? '127.0.0.1';
        $port = $parts['port'] ?? 5432;
        $name = ltrim($parts['path'] ?? '', '/');
        $user = rawurldecode($parts['user'] ?? '');
        $password = rawurldecode($parts['pass'] ?? '');
        $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $name);
    } elseif ($databaseUrl && str_starts_with($databaseUrl, 'mysql://')) {
        $parts = parse_url($databaseUrl);
        $host = $parts['host'] ?? '127.0.0.1';
        $port = $parts['port'] ?? 3306;
        $name = ltrim($parts['path'] ?? '', '/');
        $user = rawurldecode($parts['user'] ?? '');
        $password = rawurldecode($parts['pass'] ?? '');
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);
    } else {
        $host = env_value('DB_HOST', '127.0.0.1');
        $port = env_value('DB_PORT', '3306');
        $name = env_value('DB_NAME', 'srdvvip');
        $user = env_value('DB_USER', 'root');
        $password = env_value('DB_PASSWORD', '');
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);
    }

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function db_or_null(): ?PDO
{
    try {
        return db();
    } catch (Throwable) {
        return null;
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function require_csrf(): void
{
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        http_response_code(419);
        exit('Jeton de sécurité invalide. Veuillez réessayer.');
    }
}

function is_admin(): bool
{
    return !empty($_SESSION['admin_authenticated']);
}

function require_admin(): void
{
    if (!is_admin()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function request_json(): array
{
    $body = file_get_contents('php://input');
    $data = json_decode($body ?: '{}', true);
    return is_array($data) ? $data : [];
}

function money(int|float $amount): string
{
    return number_format((float) $amount, 0, ',', ' ') . ' FCFA';
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function pull_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}