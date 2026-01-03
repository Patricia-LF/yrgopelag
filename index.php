<?php

require __DIR__ . '/header.php';

$database = new PDO('sqlite:yrgopelag.db');

// Hämta rum
$statement = $database->query("SELECT id, type, price FROM rooms ORDER BY price");
$rooms = $statement->fetchAll(PDO::FETCH_ASSOC);

// Hämta features
$statement = $database->query("SELECT id, activity, tier, name, price FROM features WHERE is_active = 1 ORDER BY activity, price");
$features = $statement->fetchAll(PDO::FETCH_ASSOC);

// Gruppera features
$featuresByActivity = [];
foreach ($features as $feature) {
    $featuresByActivity[$feature['activity']][] = $feature;
}

// Skapa prisdata för JavaScript
$roomPrices = [];
foreach ($rooms as $room) {
    $roomPrices[$room['id']] = (int)$room['price'];
}

$featurePrices = [];
foreach ($features as $feature) {
    $featurePrices[$feature['id']] = (int)$feature['price'];
}

?>

<div class="carousel-container" id="carousel">
    <!-- Slides kommer att genereras av JavaScript -->
</div>

<div class="nav-bar">
    <a href="#about-container" class="navbar-button">About</a>
    <a href="#rooms" class="navbar-button">Our rooms</a>
    <a href="booking-page.php" class="navbar-button">Book</a>
    <a href="#features-container" class="navbar-button">Features</a>
    <div class="image-container">
        <a href="javascript:void(0)" onclick="openImageOverlay()" class="navbar-button">Images</a>
    </div>
</div>

<div class="about-rooms-container">
    <div class="about-features-container">
        <article class="about-container" id="about-container">
            <h1>Infinity hotel</h1>
            <div>⭐⭐⭐⭐</div>
            <p class="welcome"> Welcome to Infinity Hotel, your exclusive retreat on the stunning island Isla Syntax. Nestled between swaying palm trees and crystal-clear turquoise waters, our hotel offers the perfect blend of tranquility and adventure. Here, personalized service is offered along with customizable experiences to create your perfect island getaway.
            </p>

            <div class="hotel-list">
                <ul>
                    <li>✓ 4-STAR HOTEL</li>
                    <li>✓ THREE ROOM CATEGORIES</li>
                    <li>✓ BEACHFRONT LOCATION</li>
                    <li>✓ COMPLIMENTARY FACILITIES</li>
                    <li>✓ OPTIONAL ISLAND EXPERIENCES</li>
                    <li>✓ ECO-CERTIFIED</li>
                </ul>
            </div>

            <div class="perfect-stay">
                <h3>Choose Your Perfect Stay</h3>
                <p>At Infinity Hotel, we believe in giving you the freedom to design your perfect vacation. We offer three carefully designed room categories to suit every traveler:</p>

                <ul>
                    <li><strong>Economy Room – </strong>Comfortable and budget-friendly with our signature minimalistic décor</li>
                    <li><strong>Standard Room – </strong>Enhanced space and amenities for added comfort</li>
                    <li><strong>Luxury Room – </strong>Spacious elegance with premium touches and breathtaking ocean views</li>
                </ul>

                <p>
                    Each room features timeless design with ultimate comfort, providing the perfect setting for relaxation after island adventures or a peaceful romantic escape.
                </p>
            </div>

            <div class="complimentary">
                <h3>Complimentary Hotel Facilities</h3>
                <p>
                    All guests have access to our beachfront restaurant serving fresh seafood and international cuisine, our tropical beach bar with sunset views, a fully equipped gym, and convenient parking.
                </p>
            </div>

            <div class="enhance">
                <h3>Enhance Your Island Experience</h3>
                <p>Customize your stay with our optional features:</p>

                <ul>
                    <li><strong>Breakfast Buffet – </strong>Start your day with fresh tropical fruits, local specialties, and international favorites</li>
                    <li><strong>Pool Access – </strong>Relax by our stunning infinity pool overlooking the ocean</li>
                    <li><strong>Private Beach – </strong>Exclusive access to pristine white sand and turquoise waters</li>
                    <li><strong>Spa & Wellness – </strong>Rejuvenating therapies inspired by island traditions</li>
                    <li><strong>Island Adventures – </strong>Guided jungle hikes, snorkeling excursions, sunset boat tours, and diving trips to coral reefs</li>
                    <li><strong>Water Sports – </strong>Kayaking, paddleboarding, and jet skiing</li>
                </ul>
            </div>

            <div class="island-escape">
                <h3>Your Island Escape</h3>
                <p>Whether you're seeking a romantic getaway, family adventure, or peaceful retreat, Infinity Hotel invites you to create your ideal vacation on beautiful Isla Syntax. Book your room and add the extras that matter most to you.
                </p>
            </div>

            <div class="amenities-section">
                <h2>AMENITIES</h2>
                <div class="amenities-grid">
                    <div class="amenity-item">
                        <img src="images/wine.png" alt="Bar icon">
                        <span>BAR</span>
                    </div>
                    <div class="amenity-item">
                        <img src="images/weight.webp" alt="Gym icon">
                        <span>GYM</span>
                    </div>
                    <div class="amenity-item">
                        <img src="images/car.png" alt="Parking icon">
                        <span>PARKING</span>
                    </div>
                    <div class="amenity-item">
                        <img src="images/restaurant (1).png" alt="Restaurant icon">
                        <span>RESTAURANT</span>
                    </div>
                </div>
            </div>

            <div class="location-section">
                <h2>LOCATION</h2>
                <div class="location-grid">
                    <div class="location-item">
                        <img src="images/beach-icon.png" alt="Beach icon">
                        <span>BEACH 50M</span>
                    </div>
                    <div class="location-item">
                        <img src="images/city-icon.png" alt="City icon">
                        <span>CITY CENTER 1KM</span>
                    </div>
                    <div class="location-item">
                        <img src="images/shopping-icon.jpg" alt="Shopping icon">
                        <span>SHOPPING 1KM</span>
                    </div>
                </div>
            </div>

            <div class="room-features-section">
                <h2>IN YOUR ROOM</h2>
                <div class="room-features-grid">
                    <div class="room-feature-item">
                        <img src="images/coffee-icon.png" alt="Coffee icon">
                        <span>COFFEE MAKER</span>
                    </div>
                    <div class="room-feature-item">
                        <img src="images/hairdryer-icon.png" alt="Hair dryer icon">
                        <span>HAIR DRYER</span>
                    </div>
                    <div class="room-feature-item">
                        <img src="images/iron-icon.png" alt="Iron icon">
                        <span>IRONING BOARD</span>
                    </div>
                </div>
            </div>
        </article>

        <div id="image-overlay" class="image-overlay">
            <img src="images/close-icon.png" alt="Close" class="overlay-close-icon" onclick="closeImageOverlay()">
            <div id="image-carousel" class="image-carousel-container"></div>
        </div>

    </div>

    <section class="rooms" id="rooms">
        <h2>Our rooms</h2>
        <article class="room-container">
            <div class="room-about">
                <img src="images/room3.png">
            </div>
            <div class="room-info">
                <div>
                    <p class="room-type1">Economy</p>
                    <p class="room-descripiton">The economy room includes a</p>
                    <p class="room-price">$4/night</p>
                </div>

                <a href="booking-page.php" class="room-button">Book</a>
            </div>
        </article>

        <article class="room-container">
            <div class="room-about">
                <img src="images/Standard.png">
            </div>

            <div class="room-info">
                <div>
                    <p class="room-type1">Standard</p>
                    <p class="room-descripiton">Our standard room include a queen size bed,
                    <p class="room-price">$7/night</p>
                </div>

                <a href="booking-page.php" class="room-button">Book</a>
            </div>
        </article>

        <article class="room-container">
            <div class="room-about">
                <img src="images/Gemini_Generated_Image_chpf31chpf31chpf(1).png">
            </div>
            <div class="room-info">
                <div>
                    <p class="room-type1">Luxury</p>
                    <p class="room-descripiton">Our Luxury suite includes a king size bed, a private pool and free access to all features in the economy tier </p>
                    <p class="room-price">$10/night</p>
                </div>

                <a href="booking-page.php" class="room-button">Book</a>
            </div>
        </article>

        <section class="features-img-container">
            <article class="features-container" id="features-container">
                <p>Features:</p>
                <?php foreach ($featuresByActivity as $activity => $activityFeatures): ?>
                    <div class="feature-category">
                        <h4><?= ucfirst(str_replace('-', ' ', htmlspecialchars($activity))) ?></h4>
                        <ul>
                            <?php foreach ($activityFeatures as $feature): ?>
                                <li><?= ucfirst(htmlspecialchars($feature['name'])) ?>:
                                    <?= ucfirst(htmlspecialchars($feature['tier'])) ?>,
                                    <span class="price">($<?= $feature['price'] ?>)</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </article>
        </section>
    </section>
</div>

<?php
require __DIR__ . '/footer.php';
?>

<script src="carousel.js"></script>
<script src="image-overlay.js"></script>