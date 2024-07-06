<?php
session_start();
include 'navbar.php';

$sql_featured = "SELECT DISTINCT p.product_id, p.product_name, pv.price, pv.product_image 
        FROM products p 
        JOIN product_variants pv ON p.product_id = pv.product_id 
        GROUP BY p.product_id 
        ORDER BY RAND() 
        LIMIT 15";
$result_featured = $conn->query($sql_featured);

$featuredProducts = [];
if ($result_featured->num_rows > 0) {
    while ($row = $result_featured->fetch_assoc()) {
        $featuredProducts[] = $row;
    }
}

// Fetch categories with at least 6 distinct products
$sql_categories = "SELECT c.category_id, c.category_name
                   FROM categories c
                   JOIN products p ON c.category_id = p.category_id
                   GROUP BY c.category_id
                   HAVING COUNT(DISTINCT p.product_id) >= 6";
$result_categories = $conn->query($sql_categories);

$categoryCarousels = [];
if ($result_categories->num_rows > 0) {
    while ($row = $result_categories->fetch_assoc()) {
        $categoryId = $row['category_id'];
        $categoryName = $row['category_name'];

        // Fetch up to 6 distinct products for this category
        $sql_products = "SELECT p.product_id, p.product_name, MIN(pv.price) AS price, pv.product_image
                         FROM products p
                         JOIN product_variants pv ON p.product_id = pv.product_id
                         WHERE p.category_id = $categoryId
                         GROUP BY p.product_id
                         LIMIT 6";
        $result_products = $conn->query($sql_products);

        $categoryProducts = [];
        if ($result_products->num_rows > 0) {
            while ($product = $result_products->fetch_assoc()) {
                $categoryProducts[] = $product;
            }
        }

        // Add category and its products to the array
        $categoryCarousels[] = [
            'category_name' => $categoryName,
            'products' => $categoryProducts
        ];
    }
}

?>

<main>
    <div id="carouselExampleIndicators" class="carousel slide mb-3" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3" aria-label="Slide 4"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="../assets/img/carousel-1.jpg" class="d-block w-100 carousel-image" alt="Clothing">
                <div class="carousel-caption d-flex flex-column justify-content-center align-items-center text-center">
                    <h5 class="display-4">Explore the Latest Fashion Trends</h5>
                    <p class="lead">Discover a wide range of stylish clothing and accessories to suit every occasion.</p>
                    <a href="#" class="btn btn-primary btn-lg mt-3">SHOP NOW</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="../assets/img/carousel-2.jpg" class="d-block w-100 carousel-image" alt="Electronics">
                <div class="carousel-caption d-flex flex-column justify-content-center align-items-center text-center">
                    <h5 class="display-4">Upgrade Your Tech Gear</h5>
                    <p class="lead">Find the latest and greatest in electronics and gadgets to stay ahead of the curve.</p>
                    <a href="#" class="btn btn-primary btn-lg mt-3">SHOP NOW</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="../assets/img/carousel-3.jpg" class="d-block w-100 carousel-image" alt="Home & Decor">
                <div class="carousel-caption d-flex flex-column justify-content-center align-items-center text-center">
                    <h5 class="display-4">Transform Your Living Space</h5>
                    <p class="lead">Enhance your home with our exquisite collection of decor items and furniture.</p>
                    <a href="#" class="btn btn-primary btn-lg mt-3">SHOP NOW</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="../assets/img/carousel-4.jpg" class="d-block w-100 carousel-image" alt="Gardening">
                <div class="carousel-caption d-flex flex-column justify-content-center align-items-center text-center">
                    <h5 class="display-4">Bring Nature to Your Home</h5>
                    <p class="lead">Get everything you need for gardening and make your home a green sanctuary.</p>
                    <a href="#" class="btn btn-primary btn-lg mt-3">SHOP NOW</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="container-fluid my-5">
        <div class="row text-center gy-4">
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="border py-4 feature-cards">
                    <h4 class="mb-0 usr-features-h4 fw-bold"><i class="ri-check-fill"></i> Quality Products</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="border py-4 feature-cards">
                    <h4 class="mb-0 usr-features-h4 fw-bold"><i class="ri-truck-fill"></i> Free Shipping</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="border py-4 feature-cards">
                    <h4 class="mb-0 usr-features-h4 fw-bold"><i class="ri-arrow-left-right-fill"></i> 14-Day Return</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="border py-4 feature-cards">
                    <h4 class="mb-0 usr-features-h4 fw-bold"><i class="ri-phone-fill"></i> 24/7 Support</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid bg-white py-5">
        <h2 class="fw-bold text-center usr-feature-crousel-heading mb-3">Featured Products</h2>
        <div class="row">
            <div class="MultiCarousel" data-items="1,2,3,4" data-slide="1" id="MultiCarousel" data-interval="1000">
                <div class="MultiCarousel-inner">
                    <?php foreach ($featuredProducts as $featuredProduct) : ?>
                        <div class="item">
                            <a href="product_details.php?product_id=<?php echo $product['product_id']; ?>" class="product-link">
                                <div class="pad15 rounded-2">
                                    <img src="../uploads/<?php echo $featuredProduct['product_image']; ?>" class="product-img" alt="<?php echo $product['product_name']; ?>">
                                    <p class="product-title"><?php echo $featuredProduct['product_name']; ?></p>
                                    <p class="product-price fw-bold">$<?php echo $featuredProduct['price']; ?></p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="btn btn-primary leftLst"><</button>
                        <button class="btn btn-primary rightLst">></button>
            </div>
        </div>
    </div>

    <!-- Banner Section -->
    <div class="container-fluid banner-section" style="background-image: url('../assets/img/banner\ \(9\).jpg');">
        <div class="row align-items-center py-5">
            <div class="col-md-6 text-center text-md-start ps-3">
                <h2 class="banner-heading">Discover Our Exclusive Collection</h2>
                <p class="banner-text">Browse through our handpicked selection of top-quality products, perfect for any occasion.</p>
                <a href="#" class="btn btn-lg btn-primary">Shop Now</a>
            </div>
            <div class="col-md-6 text-center">
                <img src="../assets/img/banner-1.png" class="img-fluid banner-image" alt="Banner Product">
            </div>
        </div>
    </div>

    <?php foreach ($categoryCarousels as $categoryCarousel) : ?>
        <div class="container-fluid bg-white pt-5">
            <h2 class="fw-bold text-center usr-feature-crousel-heading mb-3"><?php echo $categoryCarousel['category_name']; ?></h2>
            <div class="row">
                <div class="MultiCarousel" data-items="1,2,3,4" data-slide="1" data-interval="1000">
                    <div class="MultiCarousel-inner">
                        <?php foreach ($categoryCarousel['products'] as $product) : ?>
                            <div class="item">
                                <a href="product_details.php?product_id=<?php echo $product['product_id']; ?>" class="product-link">
                                    <div class="pad15 rounded-2">
                                        <img src="../uploads/<?php echo $product['product_image']; ?>" class="product-img" alt="<?php echo $product['product_name']; ?>">
                                        <p class="product-title"><?php echo $product['product_name']; ?></p>
                                        <p class="product-price fw-bold">$<?php echo $product['price']; ?></p>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn btn-primary leftLst"><</button>
                    <button class="btn btn-primary rightLst">></button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="container-fluid bg-light py-5 my-5 my-sm-2">
        <div class="row text-center">
            <div class="col-12">
                <h2 class="fw-bold usr-newsletter-heading fw-bold">Stay Updated</h2>
                <p class="usr-newsletter-para fw-bold">Subscribe to our newsletter and stay updated on the latest products, offers, and news.</p>
                <form action="subscribe.php" method="POST" class="d-flex justify-content-center">
                    <div class="input-group w-50">
                        <input type="email" name="email" class="form-control rounded-0 sub-intput" placeholder="Enter your email" required>
                        <button type="submit" class="btn btn-primary rounded-0 sub-btn">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>