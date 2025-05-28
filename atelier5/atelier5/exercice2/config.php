<?php
// Chemin du dossier où stocker les fichiers
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// S'assurer que le dossier uploads existe
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}