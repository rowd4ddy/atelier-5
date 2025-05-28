<?php
header('Content-Type: application/json');

$filename = 'stocks.json';
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Charger les stocks depuis le fichier
$stocks = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];

// Gérer les actions
switch ($method) {
    case 'GET':
        echo json_encode($stocks);
        break;

    case 'POST':
        $newStock = [
            'id' => time(),
            'product_name' => $input['product_name'],
            'quantity' => $input['quantity']
        ];
        $stocks[] = $newStock;
        file_put_contents($filename, json_encode($stocks, JSON_PRETTY_PRINT));
        echo json_encode(['message' => 'Produit ajouté', 'stock' => $newStock]);
        break;

    case 'PUT':
        parse_str($_SERVER['QUERY_STRING'], $params);
        foreach ($stocks as &$stock) {
            if ($stock['id'] == $params['id']) {
                $stock['product_name'] = $input['product_name'];
                $stock['quantity'] = $input['quantity'];
                break;
            }
        }
        file_put_contents($filename, json_encode($stocks, JSON_PRETTY_PRINT));
        echo json_encode(['message' => 'Produit mis à jour']);
        break;

    case 'DELETE':
        parse_str($_SERVER['QUERY_STRING'], $params);
        $stocks = array_filter($stocks, fn($s) => $s['id'] != $params['id']);
        file_put_contents($filename, json_encode(array_values($stocks), JSON_PRETTY_PRINT));
        echo json_encode(['message' => 'Produit supprimé']);
        break;
}
