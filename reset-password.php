
<?php
include 'includes/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['verify_credentials'])) {
        $username = sanitizeInput($_POST['username']);
        $full_name = sanitizeInput($_POST['full_name']);
        
        // Check if username and full name match
        $sql = "SELECT id FROM users WHERE username = ? AND full_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $full_name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['reset_user_id'] = $user['id'];
            $message = '<div class="alert alert-success">Account verified. Please set your new password.</div>';
        } else {
            $message = '<div class="alert alert-danger">Incorrect credentials. Please check your username and full name.</div>';
        }
    } elseif (isset($_POST['update_password'])) {
        if (isset($_SESSION['reset_user_id'])) {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            
            if ($password === $confirm_password) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $hashedPassword, $_SESSION['reset_user_id']);
                
                if ($stmt->execute()) {
                    unset($_SESSION['reset_user_id']);
                    $message = '<div class="alert alert-success">Password updated successfully. You can now login.</div>';
                    $message .= '<div class="text-center mt-3"><a href="login.php" class="btn btn-primary">Go to Login</a></div>';
                } else {
                    $message = '<div class="alert alert-danger">Error updating password.</div>';
                }
            } else {
                $message = '<div class="alert alert-danger">Passwords do not match.</div>';
            }
        }
    }
}
?>

<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="auth-card">
                    <div class="auth-form" data-aos="fade-up">
                        <h2>Reset Password</h2>
                        <?php echo $message; ?>
                        
                        <?php if (!isset($_SESSION['reset_user_id'])): ?>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="full_name" required>
                            </div>
                            <button type="submit" name="verify_credentials" class="btn btn-primary w-100">Verify Account</button>
                        </form>
                        <?php else: ?>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                            <button type="submit" name="update_password" class="btn btn-primary w-100">Update Password</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
