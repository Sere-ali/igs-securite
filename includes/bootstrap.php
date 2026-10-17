<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']) || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https',
    ]);
    session_start();
}

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            DB_DSN, DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
        $pdo->exec("SET TIME ZONE 'Africa/Abidjan'");
    }
    return $pdo;
}

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function base_url(): string {
    // Racine du site (fonctionne dans un sous-dossier ou à la racine)
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    if (basename($dir) === 'admin') $dir = dirname($dir);
    return rtrim($dir, '/');
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function csrf_check(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419);
        exit('Session expirée. Rechargez la page et réessayez.');
    }
}

function setting(string $key, string $default = ''): string {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            foreach (db()->query('SELECT skey, svalue FROM settings') as $r) $cache[$r['skey']] = $r['svalue'];
        } catch (Throwable $t) {}
    }
    return $cache[$key] ?? $default;
}

function flash(?string $msg = null, string $type = 'ok') {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return; }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function current_user(): ?array {
    if (empty($_SESSION['uid'])) return null;
    static $u = false;
    if ($u === false) {
        $st = db()->prepare('SELECT id, name, email, role, active FROM users WHERE id = ?');
        $st->execute([$_SESSION['uid']]);
        $u = $st->fetch() ?: null;
        if ($u && !$u['active']) { $u = null; unset($_SESSION['uid']); }
    }
    return $u;
}

/** $role = null (tout admin) ou 'superadmin' */
function require_login(?string $role = null): array {
    $u = current_user();
    if (!$u) { header('Location: ' . base_url() . '/admin/login'); exit; }
    if ($role === 'superadmin' && $u['role'] !== 'superadmin') {
        http_response_code(403);
        exit('Accès réservé au super administrateur.');
    }
    return $u;
}

function log_action(string $action, string $detail = ''): void {
    try {
        $st = db()->prepare('INSERT INTO logs (user_id, action, detail, ip) VALUES (?,?,?,?)');
        $st->execute([$_SESSION['uid'] ?? null, $action, mb_substr($detail, 0, 500), client_ip()]);
    } catch (Throwable $t) {}
}

function client_ip(): string {
    $f = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    return $f ? trim(explode(',', $f)[0]) : ($_SERVER['REMOTE_ADDR'] ?? '');
}

function tel_link(string $n): string { return 'tel:+225' . preg_replace('/\D+/', '', $n); }
