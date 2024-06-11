<?php

include 'header.php';
include 'sidebar.php';
include '../database/conn.php'; 

$message = "";
$alertClass = "";
$categoryName = "";
$commissionRate = "";
$categoryId = "";

// Handle edit action before rendering the form
if (isset($_GET['edit'])) {
  $editId = $conn->real_escape_string(trim($_GET['edit']));
  $sql = "SELECT category_name, commission_rate FROM categories WHERE category_id='$editId'";
  $editResult = $conn->query($sql);
  if ($editResult->num_rows > 0) {
    $editRow = $editResult->fetch_assoc();
    $categoryName = $editRow['category_name'];
    $commissionRate = $editRow['commission_rate'];
    $categoryId = $editId;
  }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $categoryName = $conn->real_escape_string(trim($_POST['categoryName']));
  $commissionRate = $conn->real_escape_string(trim($_POST['commissionRate']));
  $categoryId = isset($_POST['categoryId']) ? $conn->real_escape_string(trim($_POST['categoryId'])) : '';

  if (empty($categoryName) || empty($commissionRate)) {
    $message = "Please fill in all fields.";
    $alertClass = "alert-danger";
  } elseif (!is_numeric($commissionRate) || $commissionRate < 1 || $commissionRate > 50) {
    $message = "Commission rate must be a number between 1 and 50.";
    $alertClass = "alert-danger";
  } else {
    // Check for duplicate category name
    $checkSql = "SELECT category_id FROM categories WHERE category_name='$categoryName'" . (!empty($categoryId) ? " AND category_id != '$categoryId'" : "");
    $checkResult = $conn->query($checkSql);

    if ($checkResult->num_rows > 0) {
      $message = "Category name already exists.";
      $alertClass = "alert-danger";
    } else {
      if (!empty($categoryId)) {
        // Update existing category
        $sql = "UPDATE categories SET category_name='$categoryName', commission_rate='$commissionRate' WHERE category_id='$categoryId'";
        if ($conn->query($sql) === TRUE) {
          $message = "Category updated successfully";
          $alertClass = "alert-success";
          // Clear the input fields
          $categoryName = "";
          $commissionRate = "";
          $categoryId = "";
        } else {
          $message = "Error: " . $sql . "<br>" . $conn->error;
          $alertClass = "alert-danger";
        }
      } else {
        // Add new category
        $sql = "INSERT INTO categories (category_name, commission_rate) VALUES ('$categoryName', '$commissionRate')";
        if ($conn->query($sql) === TRUE) {
          $message = "New category added successfully";
          $alertClass = "alert-success";
          // Clear the input fields
          $categoryName = "";
          $commissionRate = "";
        } else {
          $message = "Error: " . $sql . "<br>" . $conn->error;
          $alertClass = "alert-danger";
        }
      }
    }
  }
}

// Handle delete action
if (isset($_GET['delete'])) {
  $deleteId = $conn->real_escape_string(trim($_GET['delete']));
  $sql = "SELECT category_name FROM categories WHERE category_id='$deleteId'";
  $deleteResult = $conn->query($sql);
  if ($deleteResult->num_rows > 0) {
    $deleteRow = $deleteResult->fetch_assoc();
    $deleteCategoryName = $deleteRow['category_name'];
    
    // Check if products exist under this category
    $checkProductSql = "SELECT product_id FROM products WHERE category_id='$deleteId'";
    $checkProductResult = $conn->query($checkProductSql);

    if ($checkProductResult->num_rows > 0) {
      $message = "The category '$deleteCategoryName' cannot be removed because products exist under this category.";
      $alertClass = "alert-danger";
    } else {
      // Delete the category
      $sql = "DELETE FROM categories WHERE category_id='$deleteId'";
      if ($conn->query($sql) === TRUE) {
        $message = "Category '$deleteCategoryName' deleted successfully";
        $alertClass = "alert-warning";
      } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
        $alertClass = "alert-danger";
      }
    }
  }
}

// Fetch categories
$sql = "SELECT category_id, category_name, commission_rate FROM categories";
$result = $conn->query($sql);
?>

<main id="main-admin" class="main-admin">

<div class="pagetitle">
  <h1>Categories</h1>
</div>

<?php if (!empty($message)) : ?>
  <div class="alert <?php echo $alertClass; ?>" role="alert">
    <?php echo $message; ?>
  </div>
<?php endif; ?>

<div class="row">
  <div class="col-lg-6">
    <div class="card category-card">
      <div class="card-body">
        <h5 class="card-title category-card-title"><?php echo !empty($categoryId) ? 'Edit Category' : 'Add New Category'; ?></h5>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <div class="row mb-3">
            <label for="categoryName" class="col-sm-5 col-form-label category-label">Category Name</label>
            <div class="col-sm-7">
              <input type="text" class="form-control category-input" name="categoryName" id="categoryName" value="<?php echo htmlspecialchars($categoryName); ?>">
            </div>
          </div>
          <div class="row mb-3">
            <label for="commissionRate" class="col-sm-5 col-form-label category-label">Commission Rate (%)</label>
            <div class="col-sm-7">
              <input type="number" class="form-control category-input" name="commissionRate" id="commissionRate" value="<?php echo htmlspecialchars($commissionRate); ?>">
            </div>
          </div>
          <?php if (!empty($categoryId)) : ?>
            <input type="hidden" name="categoryId" value="<?php echo $categoryId; ?>">
          <?php endif; ?>
          <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary category-btn"><?php echo !empty($categoryId) ? 'Update Category' : 'Add Category'; ?></button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <div class="col-lg-6">
    <div class="card category-card">
      <div class="card-body">
        <h5 class="card-title category-card-title">Existing Categories</h5>
        <div class="table-responsive">
          <table class="table category-table table-borderless">
            <thead>
              <tr>
                <th scope="col">Category Name</th>
                <th scope="col">Commission Rate</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0) : ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                  <tr>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['commission_rate']) . '%'; ?></td>
                    <td>
                      <a href="ad_category.php?edit=<?php echo $row['category_id']; ?>" class="btn btn-sm btn-warning category-edit-btn"><i class="bx bx-edit-alt"></i></a>
                      <a href="ad_category.php?delete=<?php echo $row['category_id']; ?>" class="btn btn-sm btn-danger category-delete-btn"><i class="bx bx-trash"></i></a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else : ?>
                <tr>
                  <td colspan="3">No categories found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

</main>

<?php include 'footer.php'; ?>
