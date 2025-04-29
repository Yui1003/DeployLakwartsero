<?php
// Include header
include 'includes/header.php';

// Get package ID from URL
$packageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get package details
$package = null;
if ($packageId > 0) {
    $package = getPackageById($conn, $packageId);
}

// Redirect if package not found
if (!$package) {
    header("Location: destinations.php");
    exit;
}

// Get package reviews
$reviews = getPackageReviews($conn, $packageId);

// Calculate average rating
$averageRating = 0;
$totalRatings = count($reviews);
if ($totalRatings > 0) {
    $ratingSum = 0;
    foreach ($reviews as $review) {
        $ratingSum += $review['rating'];
    }
    $averageRating = $ratingSum / $totalRatings;
}

// Handle booking submission
$bookingMessage = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking_submit'])) {
    // Check if user is logged in
    if (!isLoggedIn()) {
        header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }

    // Get form data
    $userId = $_SESSION['user_id'];
    $travelDate = sanitizeInput($_POST['travel_date']);
    $numTravelers = (int)$_POST['num_travelers'];
    $specialRequests = sanitizeInput($_POST['special_requests']);
    $paymentMethod = sanitizeInput($_POST['payment_method']);
    $totalPrice = $package['price'] * $numTravelers;

    // Generate confirmation number
    $confirmationNumber = generateConfirmationNumber();

    // Insert booking with payment details
    $sql = "INSERT INTO bookings (user_id, package_id, booking_date, travel_date, num_travelers, total_price, confirmation_number, special_requests, payment_method, status) 
            VALUES (?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisiisss", $userId, $packageId, $travelDate, $numTravelers, $totalPrice, $confirmationNumber, $specialRequests, $paymentMethod);

    // Execute the statement
    if ($stmt->execute()) {
        $bookingId = $stmt->insert_id; // Get the last inserted booking ID

        // Handle file upload based on payment method
        $payment_proof = '';
        $upload_dir = 'uploads/receipts/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Handle GCash payment proof upload
        if ($paymentMethod === 'gcash' && isset($_FILES['gcashProofOfPayment']) && $_FILES['gcashProofOfPayment']['error'] == 0) {
            $file_extension = pathinfo($_FILES['gcashProofOfPayment']['name'], PATHINFO_EXTENSION);
            $file_name = 'gcash_receipt_' . uniqid() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['gcashProofOfPayment']['tmp_name'], $file_path)) {
                $payment_proof = $file_path;
            } else {
                $bookingMessage = '<div class="alert alert-danger">GCash proof of payment upload failed.</div>';
            }
        }

        // Handle Bank Transfer payment proof upload
        if ($paymentMethod === 'bank_transfer' && isset($_FILES['bankProofOfPayment']) && $_FILES['bankProofOfPayment']['error'] == 0) {
            $file_extension = pathinfo($_FILES['bankProofOfPayment']['name'], PATHINFO_EXTENSION);
            $file_name = 'bank_receipt_' . uniqid() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['bankProofOfPayment']['tmp_name'], $file_path)) {
                $payment_proof = $file_path;
            } else {
                $bookingMessage = '<div class="alert alert-danger">Bank proof of payment upload failed.</div>';
            }
        }

        // Update SQL query to include payment_proof if it was uploaded
        if ($payment_proof) {
            $updateSql = "UPDATE bookings SET payment_proof = ? WHERE id = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("si", $payment_proof, $bookingId);
            $stmt->execute();
        }

        // Set success message
        $bookingMessage = '<div class="alert alert-success">Booking Successful!</div>';
    } else {
        // Log the error for debugging
        error_log("Booking failed: " . $stmt->error);
        $bookingMessage = '<div class="alert alert-danger">Booking Failed. Please try again.</div>';
    }
}
?>

<!-- Page Content -->
<section class="package-hero" style="background-image: url('<?php echo $package['image_url']; ?>');">
    <div class="container">
        <div class="package-hero-content" data-aos="fade-up">
            <h1><?php echo $package['title']; ?></h1>
            <div class="package-location">
                <i class="fas fa-map-marker-alt"></i> <?php echo $package['destination_name']; ?>, <?php echo $package['country']; ?>
            </div>
            <?php if ($totalRatings > 0): ?>
            <div class="package-rating">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <?php if($i <= floor($averageRating)): ?>
                        <i class="fas fa-star"></i>
                    <?php elseif($i - 0.5 <= $averageRating): ?>
                        <i class="fas fa-star-half-alt"></i>
                    <?php else: ?>
                        <i class="far fa-star"></i>
                    <?php endif; ?>
                <?php endfor; ?>
                <span><?php echo number_format($averageRating, 1); ?> (<?php echo $totalRatings; ?> reviews)</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="package-detail">
    <div class="container">
        <?php echo $bookingMessage; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="package-overview" data-aos="fade-up">
                    <h2>Overview</h2>
                    <p><?php echo $package['description']; ?></p>
                    <div class="package-meta-large">
                        <div class="package-meta-item">
                            <i class="fas fa-clock"></i>
                            <span><?php echo $package['duration']; ?> days</span>
                        </div>
                        <div class="package-meta-item">
                            <i class="fas fa-users"></i>
                            <span>Max <?php echo $package['max_travelers']; ?> travelers</span>
                        </div>
                        <?php if ($package['discount_percent'] > 0): ?>
                        <div class="package-meta-item">
                            <i class="fas fa-tag"></i>
                            <span><?php echo $package['discount_percent']; ?>% discount</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="package-section" data-aos="fade-up">
                    <h2><i class="fas fa-route"></i> Itinerary</h2>
                    <?php echo formatItinerary($package['itinerary']); ?>
                </div>

                <div class="package-section" data-aos="fade-up">
                    <div class="row">
                        <div class="col-md-6">
                            <h2><i class="fas fa-check-circle"></i> Inclusions</h2>
                            <?php echo formatListItems($package['inclusions']); ?>
                        </div>
                        <div class="col-md-6">
                            <h2><i class="fas fa-times-circle"></i> Exclusions</h2>
                            <?php echo formatListItems($package['exclusions']); ?>
                        </div>
                    </div>
                </div>

                <?php if (count($reviews) > 0): ?>
                <div class="package-section" data-aos="fade-up">
                    <h2><i class="fas fa-star"></i> Reviews</h2>
                    <?php foreach($reviews as $review): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <div class="review-user">
                                <div class="review-avatar">
                                    <img src="<?php echo !empty($review['profile_img']) ? $review['profile_img'] : 'https://ui-avatars.com/api/?name=' . urlencode($review['full_name']) . '&background=3078c6&color=fff'; ?>" alt="<?php echo $review['full_name']; ?>">
                                </div>
                                <div>
                                    <h4><?php echo $review['full_name']; ?></h4>
                                    <div class="review-date"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></div>
                                </div>
                            </div>
                            <div class="review-rating">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <i class="<?php echo $i <= $review['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="review-content">
                            <p><?php echo $review['comment']; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <div class="package-sidebar">
                    <div class="package-sidebar-header">
                        <h3>Book This Package</h3>
                    </div>
                    <div class="package-sidebar-body">
                        <div class="package-price-large">
                            <?php echo formatCurrency($package['price']); ?> <span>/ per person</span>
                        </div>

                        <form method="post" action="package-details.php?id=<?php echo $packageId; ?>" id="bookingForm" enctype="multipart/form-data" accept-charset="UTF-8">
                            <div class="mb-3">
                                <label for="travelDate" class="form-label">Travel Date</label>
                                <input type="date" class="form-control" id="travelDate" name="travel_date" required>
                            </div>

                            <div class="mb-3">
                                <label for="numTravelers" class="form-label">Number of Travelers</label>
                                <select class="form-select" id="numTravelers" name="num_travelers" required>
                                    <?php for($i = 1; $i <= min(10, $package['max_travelers']); $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="paymentMethod" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="gcash">GCash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>

                            <div id="gcashDetails" style="display:none;" class="mb-3 payment-instructions">
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-info-circle"></i> GCash Payment Instructions:</h5>
                                    <ol>
                                        <li>Send payment to GCash number: <strong>09997914791</strong></li>
                                        <li>Amount to send: <span class="payment-amount">PHP 0.00</span></li>
                                        <li>Take a screenshot of your payment confirmation</li>
                                    </ol>
                                </div>

                                <label for="gcashProofOfPayment" class="form-label">Proof of Payment *</label>
                                <input type="file" class="form-control" id="gcashProofOfPayment" name="gcashProofOfPayment" accept="image/*,.pdf" required>
                                <div class="form-text">Upload a screenshot or photo of your payment confirmation</div>
                            </div>

                            <div id="bankDetails" style="display:none;" class="mb-3 payment-instructions">
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-info-circle"></i> Bank Transfer Instructions:</h5>
                                    <ol>
                                        <li>Transfer to:
                                            <ul>
                                                <li>Bank: Visa/Landbank</li>
                                                <li>Account Name: Lakwartsero</li>
                                                <li>Account Number: 2717063355</li>
                                            </ul>
                                        </li>
                                        <li>Amount to transfer: <span class="payment-amount">PHP 0.00</span></li>
                                        <li>Take a screenshot of your payment confirmation</li>
                                    </ol>
                                </div>

                                <label for="bankProofOfPayment" class="form-label">Proof of Payment *</label>
                                <input type="file" class="form-control" id="bankProofOfPayment" name="bankProofOfPayment" accept="image/*" required>
                                <div class="form-text">Upload a screenshot or photo of your payment confirmation</div>
                            </div>

                            <div class="mb-3">
                                <label for="specialRequests" class="form-label">Special Requests (Optional)</label>
                                <textarea class="form-control" id="specialRequests" name="special_requests" rows="3"></textarea>
                            </div>

                            <div class="price-calculation mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Price per person:</span>
                                    <span id="pricePerPerson" data-price="<?php echo $package['price']; ?>"><?php echo formatCurrency($package['price']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between total-row">
                                    <span><strong>Total Price:</strong></span>
                                    <span id="totalPrice" class="total-price"><?php echo formatCurrency($package['price']); ?></span>
                                </div>
                                <input type="hidden" name="total_price_input" id="totalPriceInput" value="<?php echo $package['price']; ?>">
                            </div>

                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const numTravelersSelect = document.getElementById('numTravelers');
                                const pricePerPerson = parseFloat(document.getElementById('pricePerPerson').dataset.price);
                                const totalPriceDisplay = document.getElementById('totalPrice');
                                const totalPriceInput = document.getElementById('totalPriceInput');
                                const gcashAmount = document.querySelector('.payment-amount');
                                const bankAmount = document.querySelector('.payment-amount');
                                
                                function updatePrice() {
                                    const numTravelers = parseInt(numTravelersSelect.value);
                                    const totalPrice = pricePerPerson * numTravelers;
                                    totalPriceDisplay.textContent = '₱' + totalPrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                    totalPriceInput.value = totalPrice;
                                    if(gcashAmount) gcashAmount.textContent = 'PHP ' + totalPrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                    if(bankAmount) bankAmount.textContent = 'PHP ' + totalPrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                                
                                numTravelersSelect.addEventListener('change', updatePrice);
                                updatePrice(); // Initialize price on load
                            });
                            </script>

                            <?php if (isLoggedIn()): ?>
                                <?php if ($package['status'] === 'active'): ?>
                                <div class="d-grid">
                                    <button type="submit" name="booking_submit" class="btn btn-primary">Book Now</button>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-warning">
                                    This package is currently <?php echo $package['status'] === 'sold-out' ? 'sold out' : 'not available for booking'; ?>.
                                </div>
                                <?php endif; ?>
                            <?php else: ?>
                            <div class="login-prompt">
                                <p>Please <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">login</a> to book this package.</p>
                                <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-outline-primary w-100">Login to Book</a>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="package-sidebar mt-4">
                    <div class="package-sidebar-header">
                        <h3>Need Help?</h3>
                    </div>
                    <div class="package-sidebar-body">
                        <p>If you have questions about this tour or need help with booking, contact our travel experts:</p>
                        <div class="contact-info">
                            <div><i class="fas fa-phone-alt"></i> +63999-791-4791</div>
                            <div><i class="fas fa-envelope"></i> j.jomarie1435@gmail.com</div>
                        </div>
                        <div class="d-grid mt-3">
                            <a href="contact.php" class="btn btn-outline-primary">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('paymentMethod');
        const gcashDetails = document.getElementById('gcashDetails');
        const bankDetails = document.getElementById('bankDetails');
        const numTravelersSelect = document.getElementById('numTravelers');
        const pricePerPerson = parseFloat(document.getElementById('pricePerPerson').dataset.price);
        const form = document.getElementById('bookingForm');
        
        function updateAmounts() {
            const numTravelers = parseInt(numTravelersSelect.value);
            const totalAmount = pricePerPerson * numTravelers;
            const formattedAmount = 'PHP ' + totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            document.querySelectorAll('.payment-amount').forEach(el => {
                el.textContent = formattedAmount;
            });
        }

        paymentMethodSelect.addEventListener('change', function() {
            if (this.value === 'gcash') {
                gcashDetails.style.display = 'block';
                bankDetails.style.display = 'none';
                document.getElementById('gcashProofOfPayment').required = true;
                document.getElementById('bankProofOfPayment').required = false;
            } else if (this.value === 'bank_transfer') {
                bankDetails.style.display = 'block';
                gcashDetails.style.display = 'none';
                document.getElementById('bankProofOfPayment').required = true;
                document.getElementById('gcashProofOfPayment').required = false;
            } else {
                gcashDetails.style.display = 'none';
                bankDetails.style.display = 'none';
            }
        });

        numTravelersSelect.addEventListener('change', updateAmounts);
        updateAmounts(); // Initial update
    });
</script>
