<?php
session_start();
include 'navbar.php';

// Fetch categories for filtering and their product counts
$sql_categories = "SELECT c.category_id, c.category_name, COUNT(p.product_id) AS product_count
                   FROM categories c
                   LEFT JOIN products p ON c.category_id = p.category_id
                   GROUP BY c.category_id, c.category_name";
$result_categories = $conn->query($sql_categories);

$categories = [];
if ($result_categories->num_rows > 0) {
    while ($row = $result_categories->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<main class="container-fluid py-5">
    <div class="row">
        <!-- Sidebar for filters -->
        <aside class="col-lg-3 col-md-4 col-sm-12">
            <h4>Filter by</h4>
            <form id="filterForm">
                <div class="usr-filter-list">
                    <label class="usr-filter-item list-group-item-2">
                        <input type="checkbox" name="categories[]" value="all" class="usr-filter-checkbox form-check-input-2" checked>
                        All Products
                    </label>
                    <?php foreach ($categories as $category) : ?>
                        <label class="usr-filter-item list-group-item-2">
                            <input type="checkbox" name="categories[]" value="<?php echo $category['category_id']; ?>" class="usr-filter-checkbox form-check-input-2">
                            <?php echo $category['category_name']; ?> (<?php echo $category['product_count']; ?>)
                        </label>
                    <?php endforeach; ?>
                </div>
            </form>
        </aside>
        
        <!-- Main content for products -->
        <div class="col-lg-9 col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold shop-pg-search-title">All Products</h2>
                <form id="searchForm" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search products">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            <div class="row shop-products-row" id="productsContainer">
                <!-- Products will be loaded here dynamically -->
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    function loadProducts() {
        $.ajax({
            url: 'filter_products.php',
            type: 'POST',
            data: $('#filterForm').serialize(),
            success: function(response) {
                const data = JSON.parse(response);
                const products = data.products;
                let html = '';

                $.each(products, function(productId, product) {
                    html += `
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card card-height d-flex flex-column">
                            <div class="product-image-container">
                                <img src="../uploads/${product.product_image}" class="card-img-top fixed-height-img" alt="${product.product_name}" data-original-src="../uploads/${product.product_image}">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title-2 mb-0 text-center">${product.product_name}</h5>
                    `;

                    if (Object.keys(product.colors).length > 1) {
                        html += '<div class="product-variants text-center my-2">';
                        $.each(product.colors, function(colorId, color) {
                            if (colorId === 'multicolor') {
                                html += `<div class="pd-color-circle pd-color-circle-multicolor" style="background-image: url('${color.color_code}');" data-variant-image="${color.color_code}"></div>`;
                            } else {
                                html += `<div class="rounded-circle pd-color-circle" style="background-color: ${color.color_code};" data-variant-image="../uploads/${color.product_image}"></div>`;
                            }
                        });
                        html += '</div>';
                    }

                    html += `
                                <div class="mt-auto text-center">
                                    <p class="card-text fw-bold usr-shop-pd-caed-price">$${product.price}</p>
                                    <a href="product_details.php?product_id=${product.product_id}" class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                });

                $('#productsContainer').html(html);

                // Add hover effect to change product image
                $('.pd-color-circle').hover(function() {
                    var variantImage = $(this).data('variant-image');
                    $(this).closest('.card').find('.card-img-top').attr('src', variantImage);
                }, function() {
                    var originalImage = $(this).closest('.card').find('.card-img-top').data('original-src');
                    $(this).closest('.card').find('.card-img-top').attr('src', originalImage);
                });
            }
        });
    }

    $('#filterForm input[type="checkbox"]').change(function() {
        loadProducts();
    });

    // Initial load
    loadProducts();
});
</script>
