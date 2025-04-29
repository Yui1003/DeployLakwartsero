<?php
// Include header
include 'includes/header.php';

// Get featured destinations and packages
$featuredDestinations = getFeaturedDestinations($conn);
$featuredPackages = getFeaturedPackages($conn);
?>

<!-- Hero Section -->
<section class="hero" style="background-image: url('https://images.unsplash.com/photo-1494676051766-7a7454d53904?q=80&w=2073&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');">
    <div class="container">
        <div class="hero-content" data-aos="fade-up">
            <h1 class="hero-title">Discover the Philippines' Beauty</h1>
            <p class="hero-subtitle">Explore breathtaking destinations and create unforgettable memories</p>
            
            <div class="hero-search-form">
                <form action="destinations.php" method="get">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="destinationInput" name="search" placeholder="Where do you want to go?">
                                <label for="destinationInput">Where do you want to go?</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="dateInput" name="date" placeholder="dd/mm/yyyy">
                                <label for="dateInput">When?</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 h-100 search-btn">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Featured Destinations -->
<section class="destinations">
    <div class="container">
        <div class="section-title text-center" data-aos="fade-up">
            <h2>Featured Destinations</h2>
            <p>Explore our handpicked selection of the Philippines' most amazing places</p>
        </div>
        
        <div class="row">
            <?php 
            
            foreach ($featuredDestinations as $destination): ?>
            <div class="col-md-6 col-lg-3">
                <div class="destination-card" data-aos="fade-up">
                    <img src="<?php echo !empty($destination['image_url']) ? $destination['image_url'] : 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&q=80'; ?>" alt="<?php echo $destination['name']; ?>" class="destination-img">
                    <div class="destination-card-overlay">
                        <h3><?php echo $destination['name']; ?></h3>
                        <div class="location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo $destination['region']; ?></span>
                        </div>
                    </div>
                    <div class="destination-card-content">
                        <p><?php echo substr($destination['description'], 0, 100); ?>...</p>
                        <a href="destinations.php?id=<?php echo $destination['id']; ?>" class="btn btn-primary">Explore</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="destinations.php" class="btn btn-outline-primary">View All Destinations</a>
        </div>
    </div>
</section>

<!-- Featured Packages -->
<section class="featured-packages">
    <div class="container">
        <div class="section-title text-center" data-aos="fade-up">
            <h2>Featured Tour Packages</h2>
            <p>Hand-crafted tour packages for unforgettable experiences</p>
        </div>
        
        <div class="row">
            <?php foreach ($featuredPackages as $package): ?>
            <div class="col-md-6 col-lg-4">
                <div class="package-card" data-aos="fade-up">
                    <img src="<?php echo !empty($package['image_url']) ? $package['image_url'] : 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&q=80'; ?>" alt="<?php echo $package['title']; ?>" class="package-img">
                    <div class="package-card-content">
                        <h3><?php echo $package['title']; ?></h3>
                        <div class="package-meta">
                            <div><i class="fas fa-map-marker-alt"></i> <?php echo $package['destination_name']; ?></div>
                            <div><i class="fas fa-clock"></i> <?php echo $package['duration']; ?> days</div>
                        </div>
                        <div class="package-price">
                            <?php echo formatCurrency($package['price']); ?> <span>/ per person</span>
                        </div>
                        <a href="package-details.php?id=<?php echo $package['id']; ?>" class="btn btn-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="destinations.php" class="btn btn-outline-primary">View All Packages</a>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta">
    <div class="container text-center">
        <h2 data-aos="fade-up">Ready for Your Next Adventure?</h2>
        <p data-aos="fade-up" data-aos-delay="100">Join thousands of satisfied travelers who have experienced the Philippines with us. Book your trip today and create memories that last a lifetime.</p>
        <a href="destinations.php" class="btn btn-light" data-aos="fade-up" data-aos-delay="200">Start Exploring</a>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>