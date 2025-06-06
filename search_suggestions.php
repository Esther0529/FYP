<?php
header('Content-Type: application/json');
require 'db.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$suggestions = [
    'products' => [],
    'categories' => [],
    'subcategories' => [],
    'brands' => []
];

if ($query !== '') {
    $like = '%' . $query . '%';
    // Products
    $stmt = mysqli_prepare($conn, "SELECT id, name FROM products WHERE name LIKE ? LIMIT 5");
    mysqli_stmt_bind_param($stmt, 's', $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions['products'][] = $row;
    }
    // Categories
    $stmt = mysqli_prepare($conn, "SELECT id, name FROM categories WHERE name LIKE ? LIMIT 5");
    mysqli_stmt_bind_param($stmt, 's', $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions['categories'][] = $row;
    }
    // Subcategories
    $stmt = mysqli_prepare($conn, "SELECT id, name FROM subcategories WHERE name LIKE ? LIMIT 5");
    mysqli_stmt_bind_param($stmt, 's', $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions['subcategories'][] = $row;
    }
    // Brands
    $stmt = mysqli_prepare($conn, "SELECT id, name FROM brands WHERE name LIKE ? LIMIT 5");
    mysqli_stmt_bind_param($stmt, 's', $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions['brands'][] = $row;
    }
}

echo json_encode($suggestions); 