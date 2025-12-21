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
    <a href="#images" class="navbar-button">Images</a>
</div>

<div class="about-rooms-container">
    <div class="about-features-container">
        <article class="about-container" id="about-container">
            <h1>Infinity hotel</h1>
            <div>⭐⭐⭐⭐</div>
            <p>Infinity hotel is located on the beautiful Island Isla Syntax. Here you can enjoy breathtaking views, turqoise water and fun explorations. With our minimalistic and cozy decor and extra features like breakfast buffet, spa access, pool and a private beach our hotel lets you relax to the fullest. It's the ultimate vacation. </p>
            <div class="info">
                <h2>Info:</h2>
                <ul>
                    <li>Reastaurant</li>
                    <li>Gym</li>
                    <li>Beach 50m</li>
                    <li>Shopping 1km</li>
                </ul>
            </div>
            <div class="amenities">
                <h2>Amenities:</h2>
                <ul>
                    <li>Shower</li>
                    <li>Coofee maker</li>
                    <li>Hair fan</li>
                    <li>Ironing board</li>
                </ul>
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

            <article class="images" id="images">
                <img src="images/unnamed (3).jpg" alt="spa">
                <img src="images/Gemini_Generated_Image_chpf31chpf31chpf(3).png" alt="spa">
                <img src="images/unnamed (4).jpg" alt="private beach">
                <img src="images/Beach22.jpg" alt="private beach">
                <img src="images/pool.jpg" alt="pool">
                <img src="images/unnamed (1).jpg" alt="breakfast buffet">
            </article>
        </section>
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
    </section>
</div>

<?php
require __DIR__ . '/footer.php';
?>

<script src="carousel.js"></script>