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
    $password = $_POST['password'];
    
    // Login user
    $result = loginUser($conn, $username, $password);
    
    if ($result['success']) {
        // Redirect to intended page or dashboard based on role
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';
        
        // Check user role and redirect accordingly
        if (isAdmin()) {
            header("Location: admin-dashboard.php");
            exit;
        } elseif (isOperator()) {
            header("Location: operator-dashboard.php");
            exit;
        } else {
            header("Location: traveler-dashboard.php");
            exit;
        }
        exit;
    } else {
        $message = '<div class="alert alert-danger">' . $result['message'] . '</div>';
    }
}
?>

<!-- Page Content -->
<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="auth-card">
                    <div class="row g-0">
                        <div class="col-md-6 d-none d-md-block">
                            <div class="auth-image h-100" style="background-image: url('https://images.unsplash.com/photo-1594661745200-810105bcf054');"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="auth-form" data-aos="fade-left">
                                <h2>Login to Your Account</h2>
                                
                                <?php echo $message; ?>
                                
                                <form method="post" action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username or Email</label>
                                        <input type="text" class="form-control" id="username" name="username" required autofocus>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" name="password" required>
                                            <span class="input-group-text">
                                                <i class="fas fa-eye password-toggle" role="button"></i>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">Remember me</label>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Login</button>
                                    </div>
                                </form>
                                
                                <div class="auth-footer">
                                    <p>Don't have an account? <a href="register.php">Register</a></p>
                                    <p><a href="reset-password.php">Forgot your password?</a></p>
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
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>