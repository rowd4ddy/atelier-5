<?php
/**
 * Exercice 2 - Backend API
 * Fichier: files.php
 * Route: GET /api/files.php
 * Fonction: Récupération de la liste des fichiers
 */

// Configuration CORS pour permettre les requêtes depuis le frontend
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Gestion des requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérifier que la méthode est GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Méthode non autorisée. Utilisez GET.'
    ]);
    exit();
}

try {
    // Configuration
    $uploadDir = __DIR__ . '/uploads/';
    $metadataFile = $uploadDir . 'files_metadata.json';
    
    // Vérifier si le dossier uploads existe
    if (!is_dir($uploadDir)) {
        http_response_code(200);
        echo json_encode([]);
        exit();
    }
    
    $files = [];
    
    // Méthode 1: Lire depuis le fichier de métadonnées (préféré)
    if (file_exists($metadataFile)) {
        $jsonContent = file_get_contents($metadataFile);
        $metadata = json_decode($jsonContent, true);
        
        if ($metadata && is_array($metadata)) {
            // Vérifier que les fichiers existent toujours
            foreach ($metadata as $fileInfo) {
                if (isset($fileInfo['path']) && file_exists($fileInfo['path'])) {
                    $files[] = [
                        'id' => $fileInfo['id'],
                        'name' => $fileInfo['name'],
                        'size' => $fileInfo['size'],
                        'type' => $fileInfo['type'],
                        'date' => $fileInfo['date']
                    ];
                }
            }
        }
    }
    
    // Méthode 2: Scanner le dossier si pas de métadonnées (fallback)
    if (empty($files)) {
        $scannedFiles = scandir($uploadDir);
        
        foreach ($scannedFiles as $file) {
            if ($file === '.' || $file === '..' || $file === 'files_metadata.json') {
                continue;
            }
            
            $filePath = $uploadDir . $file;
            
            if (is_file($filePath)) {
                // Extraire le nom original (enlever le timestamp)
                $originalName = preg_replace('/^\d+_/', '', $file);
                
                $files[] = [
                    'id' => $file,
                    'name' => $originalName,
                    'size' => filesize($filePath),
                    'type' => strtolower(pathinfo($file, PATHINFO_EXTENSION)),
                    'date' => date('Y-m-d H:i:s', filemtime($filePath))
                ];
            }
        }
    }
    
    // Trier les fichiers par date (plus récent en premier)
    usort($files, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    // Ajouter des statistiques
    $totalFiles = count($files);
    $totalSize = array_sum(array_column($files, 'size'));
    
    // Réponse de succès
    http_response_code(200);
    echo json_encode($files);
    
    // Log pour debugging
    error_log("API files.php: Retourné $totalFiles fichiers, taille totale: " . formatBytes($totalSize));
    
} catch (Exception $e) {
    // Gestion des erreurs
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur: ' . $e->getMessage()
    ]);
    
    // Log de l'erreur
    error_log('Erreur files.php: ' . $e->getMessage());
}

/**
 * Fonction utilitaire pour formater la taille des fichiers
 */
function formatBytes($size, $precision = 2) {
    if ($size == 0) return '0 B';
    
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $base = log($size, 1024);
    
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $units[floor($base)];
}
?>