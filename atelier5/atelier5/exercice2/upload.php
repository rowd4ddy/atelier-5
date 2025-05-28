<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    // Vérifier si un fichier est envoyé
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_OK) {
        throw new Exception('Aucun fichier reçu ou erreur d\'upload');
    }

    // Récupérer le fichier et son nom
    $file = $_FILES['file'];
    $filename = basename($file['name']);
    $destination = UPLOAD_DIR . $filename;

    // Déplacer le fichier vers le dossier uploads
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Échec de l\'upload du fichier');
    }

    // Réponse JSON
    echo json_encode([
        'status' => 'success',
        'message' => 'Fichier uploadé avec succès',
        'filename' => $filename
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}