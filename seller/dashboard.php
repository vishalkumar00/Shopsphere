<?php
session_start();

include 'header.php';

include 'sidebar.php';

$sellerId = $_SESSION['seller_id'];

// Fetch products and their variants for the current seller from the database
$query = "SELECT 
                p.product_id, p.product_name, p.description, 
                p.length, p.width, p.height, p.weight,
                c.category_name, 
                v.variant_id, v.quantity, v.price, v.product_image,
                clr.color_name, 
                sz.size_name
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.category_id
              LEFT JOIN product_variants v ON p.product_id = v.product_id
              LEFT JOIN colors clr ON v.color_id = clr.color_id
              LEFT JOIN sizes sz ON v.size_id = sz.size_id
              WHERE p.seller_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $sellerId);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Fetch total number of variants for the current seller
$variantCountQuery = "SELECT COUNT(*) AS total_variants 
                          FROM product_variants v 
                          JOIN products p ON v.product_id = p.product_id 
                          WHERE p.seller_id = ?";
$variantStmt = $conn->prepare($variantCountQuery);
$variantStmt->bind_param("i", $sellerId);
$variantStmt->execute();
$variantResult = $variantStmt->get_result();
$variantCount = $variantResult->fetch_assoc()['total_variants'];
$variantStmt->close();

?>

<main id="main-admin" class="main-admin">

  <div class="pagetitle">
    <h1>Dashboard</h1>
  </div>

  <section class="section dashboard">
    <div class="row">

      <!-- Left side columns -->
      <div class="col-lg-8">
        <div class="row">

          <!-- Sales Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card sales-card">

              <div class="card-body">
                <h5 class="card-title">Orders</h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <!-- <i class="ri-box-3-line "></i>  -->
                    <i class="bi bi-box"></i>
                  </div>
                  <div class="ps-3">
                    <h6>145</h6>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- Revenue Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card revenue-card">

              <div class="card-body">
                <h5 class="card-title">Revenue</h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-currency-dollar"></i>
                  </div>
                  <div class="ps-3">
                    <h6>$3,264</h6>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- Products Card -->
          <div class="col-xxl-4 col-xl-12">

            <div class="card info-card customers-card">

              <div class="card-body">
                <h5 class="card-title">Products & Variants</h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-cart"></i>
                  </div>
                  <div class="ps-3">
                    <h6><?php echo htmlspecialchars($variantCount); ?></h6>
                  </div>
                </div>

              </div>
            </div>

          </div>

          <!-- Recent Sales -->
          <div class="col-12">
            <div class="card recent-sales overflow-auto">

              <div class="card-body">
                <h5 class="card-title">Recent Sales</h5>

                <table class="table table-borderless datatable">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Customer</th>
                      <th scope="col">Product</th>
                      <th scope="col">Price</th>
                      <th scope="col">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row">#2457</th>
                      <td>Brandon Jacob</td>
                      <td><a href="#" class="text-primary">At praesentium minu</a></td>
                      <td>$64</td>
                      <td><span class="badge bg-success">Approved</span></td>
                    </tr>
                    <tr>
                      <th scope="row">#2147</th>
                      <td>Bridie Kessler</td>
                      <td><a href="#" class="text-primary">Blanditiis dolor omnis similique</a></td>
                      <td>$47</td>
                      <td><span class="badge bg-warning">Pending</span></td>
                    </tr>
                    <tr>
                      <th scope="row">#2049</th>
                      <td>Ashleigh Langosh</td>
                      <td><a href="#" class="text-primary">At recusandae consectetur</a></td>
                      <td>$147</td>
                      <td><span class="badge bg-success">Approved</span></td>
                    </tr>
                    <tr>
                      <th scope="row">#2644</th>
                      <td>Angus Grady</td>
                      <td><a href="#" class="text-primar">Ut voluptatem id earum et</a></td>
                      <td>$67</td>
                      <td><span class="badge bg-danger">Rejected</span></td>
                    </tr>
                    <tr>
                      <th scope="row">#2644</th>
                      <td>Raheem Lehner</td>
                      <td><a href="#" class="text-primary">Sunt similique distinctio</a></td>
                      <td>$165</td>
                      <td><span class="badge bg-success">Approved</span></td>
                    </tr>
                  </tbody>
                </table>

              </div>

            </div>
          </div>

          <!-- Top Selling -->
          <div class="col-12">
            <div class="card top-selling overflow-auto">

              <div class="card-body pb-0">
                <h5 class="card-title">Top Selling</h5>

                <table class="table table-borderless">
                  <thead>
                    <tr>
                      <th scope="col">Preview</th>
                      <th scope="col">Product</th>
                      <th scope="col">Price</th>
                      <th scope="col">Sold</th>
                      <th scope="col">Revenue</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row"><a href="#"><img src="../assets/img/product-1.jpg" alt=""></a></th>
                      <td><a href="#" class="text-primary fw-bold">Ut inventore ipsa voluptas nulla</a></td>
                      <td>$64</td>
                      <td class="fw-bold">124</td>
                      <td>$5,828</td>
                    </tr>
                    <tr>
                      <th scope="row"><a href="#"><img src="../assets/img/product-1.jpg" alt=""></a></th>
                      <td><a href="#" class="text-primary fw-bold">Exercitationem similique doloremque</a></td>
                      <td>$46</td>
                      <td class="fw-bold">98</td>
                      <td>$4,508</td>
                    </tr>
                    <tr>
                      <th scope="row"><a href="#"><img src="../assets/img/product-1.jpg" alt=""></a></th>
                      <td><a href="#" class="text-primary fw-bold">Doloribus nisi exercitationem</a></td>
                      <td>$59</td>
                      <td class="fw-bold">74</td>
                      <td>$4,366</td>
                    </tr>
                    <tr>
                      <th scope="row"><a href="#"><img src="../assets/img/product-1.jpg" alt=""></a></th>
                      <td><a href="#" class="text-primary fw-bold">Officiis quaerat sint rerum error</a></td>
                      <td>$32</td>
                      <td class="fw-bold">63</td>
                      <td>$2,016</td>
                    </tr>
                    <tr>
                      <th scope="row"><a href="#"><img src="../assets/img/product-1.jpg" alt=""></a></th>
                      <td><a href="#" class="text-primary fw-bold">Sit unde debitis delectus repellendus</a></td>
                      <td>$79</td>
                      <td class="fw-bold">41</td>
                      <td>$3,239</td>
                    </tr>
                  </tbody>
                </table>

              </div>

            </div>
          </div>

        </div>
      </div>

      <!-- Right side columns -->
      <div class="col-lg-4">

        <!-- Announcement -->
        <div class="card">

          <div class="card-body">
            <h5 class="card-title">Announcements</h5>

            <div class="activity">

              <div class="activity-item d-flex">
                <div class="ann-mins"><span>32 min</span></div>
                <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                <div class="activity-content">
                  Quia quae rerum explicabo officiis beatae
                </div>
              </div>

              <div class="activity-item d-flex">
                <div class="ann-mins">56 min</div>
                <i class='bi bi-circle-fill activity-badge text-danger align-self-start'></i>
                <div class="activity-content">
                  Voluptatem blanditiis blanditiis eveniet
                </div>
              </div>

              <div class="activity-item d-flex">
                <div class="ann-mins">2 hrs</div>
                <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                <div class="activity-content">
                  Voluptates corrupti molestias voluptatem
                </div>
              </div>

              <div class="activity-item d-flex">
                <div class="ann-mins">1 day</div>
                <i class='bi bi-circle-fill activity-badge text-info align-self-start'></i>
                <div class="activity-content">
                  Tempore autem saepe occaecati voluptatem tempore
                </div>
              </div>

              <div class="activity-item d-flex">
                <div class="ann-mins">2 days</div>
                <i class='bi bi-circle-fill activity-badge text-warning align-self-start'></i>
                <div class="activity-content">
                  Est sit eum reiciendis exercitationem
                </div>
              </div>

              <div class="activity-item d-flex">
                <div class="ann-mins">4 weeks</div>
                <i class='bi bi-circle-fill activity-badge text-muted align-self-start'></i>
                <div class="activity-content">
                  Dicta dolorem harum nulla eius. Ut quidem quidem sit quas
                </div>
              </div>

            </div>

          </div>
        </div>

      </div>

    </div>
  </section>

</main>


<?php include 'footer.php'; ?>