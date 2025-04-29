
<?php
require_once 'includes/auth.php';
require_once 'includes/db_connect.php';

header('Content-Type: application/json');

if (isset($_GET['package_id'])) {
    $packageId = (int)$_GET['package_id'];
    
    $sql = "SELECT COUNT(*) as booking_count FROM bookings WHERE package_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $packageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo json_encode(['has_bookings' => $row['booking_count'] > 0]);
    exit;
}

if (isset($_GET['destination_id'])) {
    $destinationId = (int)$_GET['destination_id'];
    
    // Check for packages
    $sqlPackages = "SELECT COUNT(*) as package_count FROM packages WHERE destination_id = ?";
    $stmt = $conn->prepare($sqlPackages);
    $stmt->bind_param("i", $destinationId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $hasPackages = $row['package_count'] > 0;
    
    $response = ['has_packages' => $hasPackages];
    
    // If check_all parameter is present, also check for bookings
    if (isset($_GET['check_all']) && $hasPackages) {
        $sqlBookings = "SELECT COUNT(*) as booking_count 
                       FROM bookings b 
                       JOIN packages p ON b.package_id = p.id 
                       WHERE p.destination_id = ?";
        $stmt = $conn->prepare($sqlBookings);
        $stmt->bind_param("i", $destinationId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $response['has_bookings'] = $row['booking_count'] > 0;
    }
    
    echo json_encode($response);
    exit;
}

echo json_encode(['error' => 'Invalid request']);
?>
