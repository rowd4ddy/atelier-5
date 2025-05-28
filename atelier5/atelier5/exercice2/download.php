<?php
require_once 'config.php';

try {
    // Vérifier si l'ID (nom du fichier) est fourni
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('ID du fichier manquant');
    }

    $filename = basename($_GET['id']);
    $filePath = UPLOAD_DIR . $filename;

    // Vérifier si le fichier existe
    if (!file_exists($filePath)) {
        throw new Exception('Fichier non trouvé');
    }

    // Envoyer le fichier
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
} catch (Exception $e) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}