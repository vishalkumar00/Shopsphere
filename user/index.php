<?php
session_start();
include 'navbar.php';
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
</main>

<?php include 'footer.php'; ?>
