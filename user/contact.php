<?php
session_start();
include 'navbar.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$full_name = '';
$email = '';

if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];

    // Query to get user details
    $stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $full_name = $row['first_name'] . ' ' . $row['last_name'];
        $email = $row['email'];
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if ($is_logged_in) {
        // Insert message with user_id if logged in
        $stmt = $conn->prepare("INSERT INTO contact_messages (user_id, full_name, email, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $name, $email, $message);
    } else {
        // Insert message without user_id if not logged in
        $stmt = $conn->prepare("INSERT INTO contact_messages (user_id, full_name, email, message) VALUES (NULL, ?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
    }

    $stmt->execute();
    $stmt->close();

    // Redirect or display a success message
    echo '<div class="alert alert-success text-center" role="alert">Your message has been sent successfully!</div>';
}
?>

<main class="container-fluid">
    <div class="row mb-4">
        <div class="col-lg-12 mx-auto py-5 text-center contact-header">
            <h1 class="fw-bold mb-3 contact-header-h1">Get in Touch</h1>
            <p class="lead fw-bold mb-4">Have any questions or need assistance? Feel free to reach out to us. We are here to help you!</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="card-title fw-bold mb-3 text-center">Send Us a Message</h4>
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($full_name); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2899.3961666097894!2d-80.40700802507149!3d43.38964836943864!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x882b9018e9e89adf%3A0x2043c24369ede07e!2sConestoga%20College%20Kitchener%20-%20Doon%20Campus!5e0!3m2!1sen!2sca!4v1723063311416!5m2!1sen!2sca" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>