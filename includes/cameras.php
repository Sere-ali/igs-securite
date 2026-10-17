<?php
// Module caméras : table créée automatiquement au premier accès.
function ensure_cameras_table(): void {
    $pdo = db();
    $pk = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite' ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'SERIAL PRIMARY KEY';
    $pdo->exec("CREATE TABLE IF NOT EXISTS cameras (
        id $pk,
        name VARCHAR(120) NOT NULL,
        client VARCHAR(160) NOT NULL DEFAULT '',
        location VARCHAR(200) NOT NULL DEFAULT '',
        ip VARCHAR(120) NOT NULL DEFAULT '',
        stream_type VARCHAR(12) NOT NULL DEFAULT 'hls',
        stream_url VARCHAR(600) NOT NULL DEFAULT '',
        refresh_sec INTEGER NOT NULL DEFAULT 3,
        notes TEXT NOT NULL DEFAULT '',
        active INTEGER NOT NULL DEFAULT 1,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");
}
function camera_types(): array {
    return [
        'hls'      => 'Vidéo en direct (HLS .m3u8) — recommandé',
        'mjpeg'    => 'Vidéo MJPEG (flux continu)',
        'snapshot' => 'Images rafraîchies (snapshot JPEG)',
        'iframe'   => 'Page web intégrée (interface NVR / go2rtc)',
    ];
}
