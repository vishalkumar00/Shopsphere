<?php
include 'header.php';
include 'sidebar.php';
include '../database/conn.php';

$title = '';
$message = '';
$announcement_id = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $message = $_POST['message'];
    $announcement_id = $_POST['announcement_id'];

    if ($announcement_id) {
        $query = "UPDATE announcements SET title = ?, message = ? WHERE announcement_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssi', $title, $message, $announcement_id);
    } else {
        $query = "INSERT INTO announcements (title, message) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $title, $message);
    }

    if ($stmt->execute()) {
        if (!$announcement_id) {
            $announcement_id = $stmt->insert_id;
            $notification_message = "New Announcement: " . $title;

            $sql_sellers = "SELECT seller_id FROM sellers";
            $result_sellers = $conn->query($sql_sellers);

            while ($seller = $result_sellers->fetch_assoc()) {
                $seller_id = $seller['seller_id'];
                $sql_insert_notification = "INSERT INTO notifications (seller_id, message, type) VALUES (?, ?, 'announcement')";
                $stmt_insert_notification = $conn->prepare($sql_insert_notification);
                $stmt_insert_notification->bind_param('is', $seller_id, $notification_message);
                $stmt_insert_notification->execute();
                $stmt_insert_notification->close();
            }
        }

        echo "Announcement " . ($announcement_id ? "updated" : "added") . " successfully.";
        $title = '';
        $message = '';
        $announcement_id = null;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

if (isset($_GET['delete'])) {
    $announcement_id = $_GET['delete'];

    $query = "DELETE FROM announcements WHERE announcement_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $announcement_id);

    if ($stmt->execute()) {
        echo "Announcement deleted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

if (isset($_GET['edit'])) {
    $announcement_id = $_GET['edit'];

    $query = "SELECT * FROM announcements WHERE announcement_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $announcement_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['title'];
        $message = $row['message'];
    }

    $stmt->close();
}

$query = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<main id="main-admin" class="main-admin">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h3 class="slr-product-page-title"><?php echo $announcement_id ? 'Edit' : 'Add'; ?> Announcement</h3>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title category-card-title"><?php echo $announcement_id ? 'Edit' : 'New'; ?> Announcement</h5>
                    <form action="ad_announcement.php" method="post">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
                        </div>
                        <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($announcement_id); ?>">
                        <button type="submit" class="btn btn-primary"><?php echo $announcement_id ? 'Update' : 'Add'; ?> Announcement</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mt-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title category-card-title">Current Announcements</h5>
                    <?php if ($result->num_rows > 0) : ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                        <td>
                                            <a href="ad_announcement.php?edit=<?php echo $row['announcement_id']; ?>" class="btn btn-warning btn-sm"><i class="bx bx-edit-alt"></i></a>
                                            <a href="ad_announcement.php?delete=<?php echo $row['announcement_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this announcement?');"><i class="bx bx-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p>No announcements found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$conn->close();
include 'footer.php';
?>