<?php
session_start();
include 'navbar.php';

$sql = "SELECT DISTINCT p.product_id, p.product_name, pv.price, pv.product_image 
        FROM products p 
        JOIN product_variants pv ON p.product_id = pv.product_id 
        GROUP BY p.product_id 
        ORDER BY RAND() 
        LIMIT 15";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<main>
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
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
                <div class="border py-4">
                    <h4 class="mb-0 usr-features-h4 fw-bold"><i class="ri-check-fill"></i> Quality Products</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="border py-4">
                    <h4 class="mb-0 usr-features-h4 fw-bold"><i class="ri-truck-fill"></i> Free Shipping</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="border py-4">
                    <h4 class="mb-0 usr-features-h4 fw-bold"><i class="ri-arrow-left-right-fill"></i> 14-Day Return</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="border py-4">
                    <h4 class="mb-0 usr-features-h4 fw-bold"><i class="ri-phone-fill"></i> 24/7 Support</h4>
                </div>
            </div>
        </div>
    </div>
    
    <div id="productCarousel" class="carousel slide carousel-pd" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $chunks = array_chunk($products, 5); // Split products into chunks of 5
            foreach ($chunks as $index => $chunk) {
                echo '<div class="carousel-item ' . ($index === 0 ? 'active' : '') . '">';
                echo '<div class="row justify-content-center">'; // Center align products
                foreach ($chunk as $product) {
                    echo '<div class="custom-col d-flex justify-content-center">';
                    echo '<div class="card home-pd-carouesel-random">';
                    echo '<img src="../uploads/' . $product['product_image'] . '" class="card-img-top product-img" alt="' . $product['product_name'] . '">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title home-pd-card-title">' . $product['product_name'] . '</h5>';
                    echo '<p class="card-text">$' . $product['price'] . '</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</main>

<?php include 'footer.php'; ?>