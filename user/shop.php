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

// Fetch price ranges and their product counts
$price_ranges = [
    '0-50' => '0 AND 50',
    '51-100' => '51 AND 100',
    '101-200' => '101 AND 200',
    '201-500' => '201 AND 500',
    '501-1000' => '501 AND 1000',
    '1001' => '1001 AND 1000000'
];

$price_counts = [];
foreach ($price_ranges as $range => $condition) {
    $sql_price = "SELECT COUNT(*) AS product_count FROM product_variants WHERE price BETWEEN $condition";
    $result_price = $conn->query($sql_price);
    if ($result_price->num_rows > 0) {
        $row = $result_price->fetch_assoc();
        $price_counts[$range] = $row['product_count'];
    } else {
        $price_counts[$range] = 0;
    }
}

?>

<main class="container-fluid py-5">
    <div class="row">
        <!-- Sidebar for filters -->
        <aside class="col-lg-3 col-md-4 col-sm-12">
            <h4 class="sidebar-filter-title">Filter by:</h4>
            <form id="filterForm">
                <h5 class="mb-0 mt-3">Category</h5>
                <div class="usr-filter-list">
                    <label class="usr-filter-item rounded-0 list-group-item-2">
                        <input type="checkbox" name="categories[]" value="all" class="usr-filter-checkbox form-check-input-2" checked>
                        <span class="usr-filter-label">All Products</span>
                        <span class="usr-filter-count"></span>
                    </label>
                    <?php foreach ($categories as $category) : ?>
                        <label class="usr-filter-item rounded-0 list-group-item-2">
                            <input type="checkbox" name="categories[]" value="<?php echo $category['category_id']; ?>" class="usr-filter-checkbox form-check-input-2">
                            <span class="usr-filter-label"><?php echo $category['category_name']; ?></span>
                            <span class="usr-filter-count">(<?php echo $category['product_count']; ?>)</span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <h5 class="mb-0 mt-3">Price</h5>
                <div class="usr-filter-list">
                    <?php foreach ($price_ranges as $range => $condition) : ?>
                        <label class="usr-filter-item rounded-0 list-group-item-2">
                            <input type="checkbox" name="price[]" value="<?php echo $range; ?>" class="usr-filter-checkbox form-check-input-2">
                            <span class="usr-filter-label">
                                <?php
                                if ($range === '1001') {
                                    echo '$1001 & Above';
                                } else {
                                    $prices = explode('-', $range);
                                    echo '$' . $prices[0] . ' - $' . (isset($prices[1]) ? $prices[1] : '');
                                }
                                ?>
                            </span>
                            <span class="usr-filter-count">(<?php echo $price_counts[$range]; ?>)</span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </form>
        </aside>

        <!-- Main content for products -->
        <div class="col-lg-9 col-md-8 shop-pg-mrgn-tp">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold shop-pg-search-title" id="shopTitle">All Products</h2>
                <div class="d-flex">

                </div>
            </div>
            <div class="row shop-products-row" id="productsContainer">
                <!-- Products will be loaded here dynamically -->
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

<script>
    $(document).ready(function() {
        function loadProducts() {
            const searchQuery = $('#searchInput').val();
            $.ajax({
                url: 'filter_products.php',
                type: 'POST',
                data: $('#filterForm').serialize() + '&search=' + searchQuery,
                success: function(response) {
                    const data = JSON.parse(response);
                    const products = data.products;
                    const filters = data.filters;
                    let html = '';

                    $.each(products, function(productId, product) {
                        html += `
                        <div class="col-lg-4 col-md-6">
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
                                        <a href="product_details.php?product_id=${product.product_id}" class="rounded-0 usr-carosuel-btn btn btn-primary">View Details</a>
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

                    // Update the heading based on the selected filters and search query
                    let filterText = '';
                    if (filters.length > 0) {
                        filterText += filters.join(', ');
                    } else {
                        filterText += 'All Products';
                    }
                    if (searchQuery !== '') {
                        filterText += ' - Search: "' + searchQuery + '"';
                    }
                    $('#shopTitle').text(filterText);
                }
            });
        }

        // Function to handle checkbox changes
        $('#filterForm input[type="checkbox"]').change(function() {
            if ($(this).val() === 'all' && $(this).prop('checked')) {
                // If "All Products" is checked, uncheck all other checkboxes
                $('#filterForm input[type="checkbox"]:not([value="all"])').prop('checked', false);
            } else if ($(this).val() !== 'all' && $(this).prop('checked')) {
                // If another checkbox is checked, uncheck "All Products"
                $('#filterForm input[type="checkbox"][value="all"]').prop('checked', false);
            }
            loadProducts();
        });

        $('#searchButton').click(function(event) {
            event.preventDefault();
            loadProducts();
        });

        // Initial load
        loadProducts();
    });
</script>