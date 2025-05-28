<?php
header('Content-Type: application/json');

// Fichier de stockage simple
$file = 'rooms.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $capacity = $_POST['capacity'] ?? '';

    if ($name && $capacity) {
        $rooms = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
        $rooms[] = ['name' => $name, 'capacity' => (int)$capacity];
        file_put_contents($file, json_encode($rooms, JSON_PRETTY_PRINT));
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo file_exists($file) ? file_get_contents($file) : '[]';
    exit;
}
?>
