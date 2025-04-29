<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Get current page for highlighting active menu item
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lakwartsero - Travel & Tourism Booking</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://i.imgur.com/OCfGVa2.png">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="https://i.imgur.com/OCfGVa2.png" alt="Lakwartsero Logo" style="height: 30px; margin-right: 5px;"> Lakwartsero
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'destinations.php') ? 'active' : ''; ?>" href="destinations.php">Destinations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'about.php') ? 'active' : ''; ?>" href="about.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <a href="admin-dashboard.php" class="btn btn-outline-light me-2">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                        <?php elseif (isOperator()): ?>
                            <a href="operator-dashboard.php" class="btn btn-outline-light me-2">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                        <?php else: ?>
                            <a href="traveler-dashboard.php" class="btn btn-outline-light me-2">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                        <?php endif; ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['full_name']; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <?php if (isAdmin()): ?>
                                    <li><a class="dropdown-item" href="admin-dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</a></li>
                                <?php elseif (isOperator()): ?>
                                    <li><a class="dropdown-item" href="operator-dashboard.php"><i class="fas fa-suitcase me-2"></i>Operator Dashboard</a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="traveler-dashboard.php"><i class="fas fa-briefcase me-2"></i>My Bookings</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-light me-2"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                        <a href="register.php" class="btn btn-light"><i class="fas fa-user-plus me-1"></i> Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content Container -->
    <main>