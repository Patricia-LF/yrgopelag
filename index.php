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
            <p class="welcome"> Welcome to Infinity Hotel, your exclusive retreat on the stunning island of Isla Syntax. Nestled between swaying palm trees and crystal-clear turquoise waters, our hotel offers the perfect blend of tranquility and adventure.
            </p>

            <div class="perfect-stay">
                <h3>Choose Your Perfect Stay</h3>
                <p>We offer three carefully designed room categories to suit every traveler:</p>

                <ul>
                    <li><strong>Economy Room – </strong>Comfortable and budget-friendly with our signature minimalistic décor</li>
                    <li><strong>Standard Room – </strong>Enhanced space and amenities for added comfort</li>
                    <li><strong>Luxury Room – </strong>Spacious elegance with premium touches and breathtaking views</li>
                </ul>
            </div>

            <div class="enhance">
                <h3>Enhance Your Experience</h3>
                <p>Customize your stay with our optional extras:</p>

                <ul>
                    <li><strong>Breakfast Buffet – </strong>Start your day with fresh tropical fruits, local specialties, and international favorites</li>
                    <li><strong>Pool Access – </strong>Relax by our stunning infinity pool overlooking the ocean</li>
                    <li><strong>Private Beach – </strong>Exclusive access to pristine white sand and turquoise waters</li>
                    <li><strong>Spa Treatments – </strong>Rejuvenating therapies inspired by island traditions</li>
                </ul>
            </div>

            <div class="island-escape">
                <h3>Your Island Escape</h3>
                <p>Whether you're seeking a romantic getaway, family adventure, or peaceful retreat, Infinity Hotel invites you to create your ideal vacation on beautiful Isla Syntax. Book your room and add the extras that matter most to you.
                </p>
            </div>

            <div class="info">
                <h2>Info:</h2>
                <ul>
                    <li></li>
                    <li>Beach 50m</li>
                    <li>Shopping 1km</li>
                    <li>Coofee maker</li>
                    <li>Hair dryer</li>
                    <li>Ironing board</li>
                </ul>
            </div>
            <div class="amenities">
                <h2>Amenities:</h2>
                <ul>
                    <li><img src="images/restaurant (1).png">Restaurant</li>
                    <li><img src="images/weight.webp">Gym</li>
                    <li><img src="images/car.png">Parking</li>
                    <li><img src="images/wine.png">Bar</li>
                </ul>
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