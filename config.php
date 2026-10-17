<?php
/**
 * IGS Sécurité — Configuration (PostgreSQL, compatible Render)
 * Sur Render : la variable d'environnement DATABASE_URL est fournie automatiquement
 * (voir render.yaml). En local, vous pouvez définir DB_HOST/DB_NAME/DB_USER/DB_PASS.
 */
function igs_env(string $k, $d = '') { $v = getenv($k); return ($v === false || $v === '') ? $d : $v; }

$url = igs_env('DATABASE_URL');
if ($url) {
    $p = parse_url($url);
    define('DB_DSN', sprintf('pgsql:host=%s;port=%d;dbname=%s;sslmode=prefer',
        $p['host'], $p['port'] ?? 5432, ltrim($p['path'] ?? '', '/')));
    define('DB_USER', urldecode($p['user'] ?? ''));
    define('DB_PASS', urldecode($p['pass'] ?? ''));
} else {
    define('DB_DSN', sprintf('pgsql:host=%s;port=%s;dbname=%s',
        igs_env('DB_HOST', 'localhost'), igs_env('DB_PORT', '5432'), igs_env('DB_NAME', 'igs_securite')));
    define('DB_USER', igs_env('DB_USER', 'postgres'));
    define('DB_PASS', igs_env('DB_PASS', ''));
}
// Clé secrète demandée par install.php (définie dans Render > Environment)
define('INSTALL_KEY', igs_env('INSTALL_KEY'));

define('SITE_NAME', 'IGS Sécurité');
define('TIMEZONE', 'Africa/Abidjan');
date_default_timezone_set(TIMEZONE);
