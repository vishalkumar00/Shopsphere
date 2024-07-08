<?php
session_start();
include '../database/conn.php'; 

$selected_categories = isset($_POST['categories']) ? $_POST['categories'] : [];
$selected_prices = isset($_POST['price']) ? $_POST['price'] : [];

$whereClauses = [];
$params = [];

// Build the SQL query based on the selected filters
if (!empty($selected_categories) && !in_array('all', $selected_categories)) {
    $placeholders = implode(',', array_fill(0, count($selected_categories), '?'));
    $whereClauses[] = "p.category_id IN ($placeholders)";
    $params = array_merge($params, $selected_categories);
}

if (!empty($selected_prices)) {
    $priceConditions = [];
    foreach ($selected_prices as $priceRange) {
        if ($priceRange === '1001') {
            $priceConditions[] = "pv.price >= 1001";
        } else {
            list($min, $max) = explode('-', $priceRange);
            $priceConditions[] = "pv.price BETWEEN ? AND ?";
            $params[] = $min;
            $params[] = $max;
        }
    }
    $whereClauses[] = '(' . implode(' OR ', $priceConditions) . ')';
}

$whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$sql = "SELECT p.product_id, p.product_name, MIN(pv.price) AS price, pv.product_image
        FROM products p
        JOIN product_variants pv ON p.product_id = pv.product_id
        $whereSql
        GROUP BY p.product_id, p.product_name, pv.product_image";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param(str_repeat('i', count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[$row['product_id']] = $row;
        $products[$row['product_id']]['variants'] = [];
        $products[$row['product_id']]['colors'] = [];
    }
}

$sql_variants = "SELECT pv.product_id, pv.variant_id, pv.color_id, c.color_name, c.color_code, pv.product_image
                 FROM product_variants pv
                 JOIN colors c ON pv.color_id = c.color_id";
$result_variants = $conn->query($sql_variants);

if ($result_variants->num_rows > 0) {
    while ($row = $result_variants->fetch_assoc()) {
        if (isset($products[$row['product_id']])) {
            $products[$row['product_id']]['variants'][] = $row;
            if ($row['color_id'] !== null) {
                if ($row['color_id'] === 'Multicolor') {
                    $products[$row['product_id']]['colors']['multicolor'] = [
                        'color_name' => 'Multicolor',
                        'color_code' => '../img/multicolor.png'
                    ];
                } elseif ($row['color_id'] !== 'None') {
                    $products[$row['product_id']]['colors'][$row['color_id']] = [
                        'color_name' => $row['color_name'],
                        'color_code' => $row['color_code'],
                        'product_image' => $row['product_image']
                    ];
                }
            }
        }
    }
}

echo json_encode(['products' => $products]);
?>
