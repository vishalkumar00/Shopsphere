<?php
session_start();
include '../database/conn.php';

if (isset($_GET['id']) && isset($_GET['variant_id'])) {
    $productId = $_GET['id'];
    $variantId = $_GET['variant_id'];

    // Check if other variants exist for this product
    $checkVariantsSQL = "SELECT COUNT(*) AS num_variants FROM product_variants WHERE product_id = ?";
    $stmt = $conn->prepare($checkVariantsSQL);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $numVariants = $row['num_variants'];

    $stmt->close();

    if ($numVariants > 1) {
        // Delete only the variant
        $deleteVariantSQL = "DELETE FROM product_variants WHERE variant_id = ?";
        $stmt = $conn->prepare($deleteVariantSQL);
        $stmt->bind_param("i", $variantId);
        $stmt->execute();
        $stmt->close();
    } else {
        $deleteVariantsSQL = "DELETE FROM product_variants WHERE product_id = ?";
        $stmt = $conn->prepare($deleteVariantsSQL);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->close();

        // Delete the product and all variants
        $deleteProductSQL = "DELETE FROM products WHERE product_id = ?";
        $stmt = $conn->prepare($deleteProductSQL);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect with success message
    header("Location: slr_products.php?deleted=1");
    exit();
} else {
    echo "Product ID or Variant ID not provided.";
}
