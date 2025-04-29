
<?php
require_once 'includes/auth.php';
require_once 'includes/db_connect.php';

// Require operator or admin access
if (!isOperator() && !isAdmin()) {
    header("Location: login.php");
    exit;
}

// Get package ID
$packageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// First check if there are any bookings for this package
$checkBookings = "SELECT COUNT(*) as booking_count FROM bookings WHERE package_id = ?";
$stmt = $conn->prepare($checkBookings);
$stmt->bind_param("i", $packageId);
$stmt->execute();
$result = $stmt->get_result();
$bookingCount = $result->fetch_assoc()['booking_count'];

if ($bookingCount > 0) {
    $redirectUrl = isAdmin() ? "admin-dashboard.php" : "operator-dashboard.php";
    header("Location: " . $redirectUrl . "?error=has_bookings");
    exit;
}

// Verify package exists and user has permission
$sql = "SELECT created_by FROM packages WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $packageId);
$stmt->execute();
$result = $stmt->get_result();
$package = $result->fetch_assoc();

if (!$package) {
    $redirectUrl = isAdmin() ? "admin-dashboard.php" : "operator-dashboard.php";
    header("Location: " . $redirectUrl);
    exit;
}

// Check if user has permission to delete (admin can delete any, operator only their own)
if (!isAdmin() && $package['created_by'] != $_SESSION['user_id']) {
    header("Location: operator-dashboard.php");
    exit;
}

// Delete package
$sql = "DELETE FROM packages WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $packageId);

if ($stmt->execute()) {
    $redirectUrl = isAdmin() ? "admin-dashboard.php" : "operator-dashboard.php";
    header("Location: $redirectUrl?deleted=1");
} else {
    $redirectUrl = isAdmin() ? "admin-dashboard.php" : "operator-dashboard.php";
    header("Location: " . $redirectUrl . "?error=1");
}
exit;
?>
