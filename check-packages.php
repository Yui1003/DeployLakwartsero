
<?php
require_once 'includes/auth.php';
require_once 'includes/db_connect.php';

header('Content-Type: application/json');

if (isset($_GET['destination_id'])) {
    $destinationId = (int)$_GET['destination_id'];
    
    $sql = "SELECT COUNT(*) as package_count FROM packages WHERE destination_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $destinationId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo json_encode(['package_count' => $row['package_count']]);
} else {
    echo json_encode(['error' => 'No destination ID provided']);
}
?>
