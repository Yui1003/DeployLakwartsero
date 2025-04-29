<?php
// Include header
include 'includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Check if form is submitted
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $fullName = sanitizeInput($_POST['full_name']);
    $phone = sanitizeInput($_POST['phone']);
    $role = 'traveler'; // Default role set to traveler
    $address = ''; // Default empty address

    // Validate phone number format
    if (!preg_match('/^[0-9+\-\s()]*$/', $phone)) {
        $message = '<div class="alert alert-danger">Phone number can only contain numbers, spaces, and the following characters: + - ( )</div>';
    }
    // Validate passwords match
    else if ($password !== $confirmPassword) {
        $message = '<div class="alert alert-danger">Passwords do not match. Please try again.</div>';
    } else {
        // Register user with all required parameters
        $result = registerUser($conn, $username, $email, $password, $fullName, $phone, $address, $role);

        if ($result['success']) {
            // Redirect to dashboard based on role
            if ($_SESSION['role'] === 'admin') {
                header("Location: admin-dashboard.php");
            } elseif ($_SESSION['role'] === 'operator') {
                header("Location: operator-dashboard.php");
            } else {
                header("Location: traveler-dashboard.php");
            }
            exit;
        } else {
            $message = '<div class="alert alert-danger">' . $result['message'] . '</div>';
        }
    }
}
?>

<!-- Registration Form -->
<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="auth-card">
                    <div class="row g-0">
                        <div class="col-md-6 d-none d-md-block">
                            <div class="auth-image h-100" style="background-image: url('https://images.unsplash.com/photo-1464746133101-a2c3f88e0dd9');"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="auth-form" data-aos="fade-left">
                                <h2>Create an Account</h2>

                                <?php echo $message; ?>

                                <form method="post" action="register.php">
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Juan Dela Cruz" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="juandelacruz123" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="juan.delacruz@example.com" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" pattern="[0-9+\-\s()]*" placeholder="+63 999 999 9999" title="Phone number can only contain numbers, spaces, and the following characters: + - ( )" required>
                                    </div>

                                    <input type="hidden" name="role" value="traveler">

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                                            <span class="input-group-text">
                                                <i class="fas fa-eye password-toggle" role="button"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>
                                            <span class="input-group-text">
                                                <i class="fas fa-eye password-toggle-confirm" role="button"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                        <label class="form-check-label" for="terms">I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms of Service</a> and <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a></label>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Register</button>
                                    </div>
                                </form>

                                <div class="auth-footer">
                                    <p>Already have an account? <a href="login.php">Login</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    const passwordToggle = document.querySelector('.password-toggle');
    if (passwordToggle) {
        passwordToggle.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    // Confirm password visibility toggle
    const passwordToggleConfirm = document.querySelector('.password-toggle-confirm');
    if (passwordToggleConfirm) {
        passwordToggleConfirm.addEventListener('click', function() {
            const confirmPasswordInput = document.getElementById('confirm_password');
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);

            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>