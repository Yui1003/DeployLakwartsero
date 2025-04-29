
<?php
require_once 'includes/auth.php';
requireAdmin();
require_once 'includes/db_connect.php';

if (isset($_GET['id'])) {
    $destinationId = (int)$_GET['id'];
    
    // Check for associated packages
    $checkPackages = "SELECT COUNT(*) as package_count FROM packages WHERE destination_id = ?";
    $stmt = $conn->prepare($checkPackages);
    $stmt->bind_param("i", $destinationId);
    $stmt->execute();
    $result = $stmt->get_result();
    $packageCount = $result->fetch_assoc()['package_count'];
    
    // Check for booked packages
    if ($packageCount > 0) {
        $checkBookings = "SELECT COUNT(*) as booking_count FROM bookings b 
                         JOIN packages p ON b.package_id = p.id 
                         WHERE p.destination_id = ?";
        $stmt = $conn->prepare($checkBookings);
        $stmt->bind_param("i", $destinationId);
        $stmt->execute();
        $result = $stmt->get_result();
        $bookingCount = $result->fetch_assoc()['booking_count'];
        
        if ($bookingCount > 0) {
            $_SESSION['error_message'] = "Cannot delete this destination as it has booked packages.";
            header("Location: admin-dashboard.php");
            exit;
        }
    }
    
    $conn->begin_transaction();
    
    try {
        // Delete associated packages first
        $sqlPackages = "DELETE FROM packages WHERE destination_id = ?";
        $stmtPackages = $conn->prepare($sqlPackages);
        $stmtPackages->bind_param("i", $destinationId);
        $stmtPackages->execute();
        
        // Then delete attractions
        $sqlAttractions = "DELETE FROM attractions WHERE destination_id = ?";
        $stmtAttractions = $conn->prepare($sqlAttractions);
        $stmtAttractions->bind_param("i", $destinationId);
        $stmtAttractions->execute();
        
        // Finally delete the destination
        $sqlDestination = "DELETE FROM destinations WHERE id = ?";
        $stmtDestination = $conn->prepare($sqlDestination);
        $stmtDestination->bind_param("i", $destinationId);
        $stmtDestination->execute();
        
        $conn->commit();
        $_SESSION['success_message'] = "Destination deleted successfully.";
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_message'] = "Error deleting destination. Please try again.";
    }
    
    header("Location: admin-dashboard.php");
    exit;
}

header("Location: admin-dashboard.php");
exit;
?>
