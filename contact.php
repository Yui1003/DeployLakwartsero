<?php
// Include header
include 'includes/header.php';

// Process contact form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $subject = sanitizeInput($_POST['subject']);
    $messageText = sanitizeInput($_POST['message']);
    
    // Get user ID if logged in
    $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
    
    // Insert message into database
    $sql = "INSERT INTO messages (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $userId, $name, $email, $subject, $messageText);
    
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">
            <h4><i class="fas fa-check-circle"></i> Message Sent!</h4>
            <p>Thank you for contacting us. Our team will get back to you as soon as possible.</p>
        </div>';
    } else {
        $message = '<div class="alert alert-danger">
            <h4><i class="fas fa-times-circle"></i> Message Failed</h4>
            <p>We encountered an error while sending your message. Please try again later or contact us directly.</p>
        </div>';
    }
}
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('https://images.unsplash.com/uploads/1413222992504f1b734a6/1928e537?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');">
    <div class="container">
        <h1 data-aos="fade-up">Contact Us</h1>
        <p data-aos="fade-up" data-aos-delay="100">Get in touch with our travel experts</p>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <?php echo $message; ?>
        
        <div class="row">
            <div class="col-lg-5 order-lg-2" data-aos="fade-left">
                <div class="contact-info">
                    <div class="contact-info-header">
                        <h2>Contact Information</h2>
                        <p>Our team is ready to assist you with any inquiries</p>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-info-text">
                            <h4>Office Address</h4>
                            <p>CV38+C99, Rosario, Cavite</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="contact-info-text">
                            <h4>Phone Numbers</h4>
                            <p>+63 909 437 6677 (Office)</p>
                            <p>+63 999 791 4791 (Mobile)</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-info-text">
                            <h4>Email Address</h4>
                            <p>rc.jomarie.avecilla@cvsu.edu.ph</p>
                            <p>rc.maurice.montano@cvsu.edu.ph</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-info-text">
                            <h4>Office Hours</h4>
                            <p>Monday to Friday: 9:00 AM - 6:00 PM</p>
                            <p>Saturday: 9:00 AM - 1:00 PM</p>
                            <p>Sunday: Closed</p>
                        </div>
                    </div>
                    
                    <div class="contact-social">
                        <h4>Connect With Us</h4>
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7 order-lg-1" data-aos="fade-right">
                <div class="contact-form-card">
                    <h2>Send Us a Message</h2>
                    <p>Have questions about our packages or destinations? Fill out the form below and our travel experts will get back to you soon.</p>
                    
                    <form method="post" action="contact.php" class="contact-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required
                                        value="<?php echo isLoggedIn() ? $_SESSION['full_name'] : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="privacy" required>
                            <label class="form-check-label" for="privacy">I agree to the <a href="#">Privacy Policy</a> and consent to having my data processed.</label>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Google Map -->
<section class="map-section">
    <div class="container-fluid p-0">
        <div class="map-container" data-aos="fade-up">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d965.8600891203168!2d120.86597383833493!3d14.403690711804149!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTTCsDI0JzEzLjMiTiAxMjDCsDUxJzU3LjUiRQ!5e0!3m2!1sen!2sph!4v1689517562067!5m2!1sen!2sph" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <div class="section-title text-center" data-aos="fade-up">
            <h2>Frequently Asked Questions</h2>
            <p>Find answers to common questions about our services</p>
        </div>
        
        <div class="accordion" id="faqAccordion" data-aos="fade-up">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        How do I book a tour package?
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Booking a tour package is easy! Simply browse our destinations, select a package that interests you, and click the "Book Now" button. You'll need to create an account or log in, select your travel dates and number of travelers, and complete the payment process. You'll receive a confirmation email with all the details of your booking.
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        What payment methods do you accept?
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        We accept various payment methods including credit/debit cards (Visa/Landbank) and popular e-wallet - GCash. All payments are processed securely through our trusted payment gateway partners.
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Can I customize a tour package?
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes, we offer customization options for our tour packages. If you'd like to modify an existing package or create a completely custom itinerary, please contact our travel experts through this contact form or call our office. We'll be happy to help you design the perfect trip based on your preferences and budget.
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        What is your cancellation policy?
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Our cancellation policy varies depending on the package and timing of cancellation. Generally, cancellations made 30 days or more before the travel date are eligible for a full refund minus a small processing fee. Cancellations made 15-29 days before travel receive a 75% refund, 7-14 days receive a 50% refund, and cancellations less than 7 days before travel are not eligible for refunds. Please refer to the specific terms and conditions of your booking for exact details.
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFive">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        Do I need travel insurance?
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        While travel insurance is not mandatory, we strongly recommend it for all travelers. A good travel insurance policy can protect you from unexpected events such as trip cancellations, medical emergencies, lost luggage, and more. We can recommend trusted travel insurance providers or you can arrange your own coverage before your trip.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional CSS for the Contact page -->
<style>
.contact-section {
    padding: 100px 0;
}

.contact-info {
    background-color: var(--primary-color);
    color: white;
    border-radius: var(--border-radius);
    padding: 40px;
    height: 100%;
}

.contact-info-header {
    margin-bottom: 30px;
}

.contact-info-header h2 {
    margin-bottom: 10px;
}

.contact-info-item {
    display: flex;
    margin-bottom: 30px;
}

.contact-info-icon {
    width: 50px;
    height: 50px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 1.2rem;
}

.contact-info-text h4 {
    margin-bottom: 5px;
    font-size: 1.1rem;
}

.contact-info-text p {
    margin-bottom: 5px;
    opacity: 0.8;
}

.contact-social {
    margin-top: 40px;
}

.contact-social h4 {
    margin-bottom: 15px;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-link {
    width: 40px;
    height: 40px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: var(--transition);
}

.social-link:hover {
    background-color: white;
    color: var(--primary-color);
}

.contact-form-card {
    background-color: white;
    border-radius: var(--border-radius);
    padding: 40px;
    box-shadow: var(--box-shadow);
    height: 100%;
}

.contact-form-card h2 {
    margin-bottom: 10px;
}

.contact-form-card p {
    margin-bottom: 30px;
    color: #6c757d;
}

.map-section {
    padding-bottom: 100px;
}

.map-container {
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--box-shadow);
}

.faq-section {
    padding: 100px 0;
    background-color: var(--light-bg);
}

.accordion-item {
    margin-bottom: 15px;
    border-radius: var(--border-radius) !important;
    overflow: hidden;
    border: 1px solid var(--border-color);
}

.accordion-button {
    font-weight: 600;
    padding: 20px;
}

.accordion-button:not(.collapsed) {
    background-color: var(--primary-color);
    color: white;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: var(--border-color);
}

.accordion-button::after {
    background-size: 16px;
}

.accordion-body {
    padding: 20px;
}
</style>

<?php
// Include footer
include 'includes/footer.php';
?>