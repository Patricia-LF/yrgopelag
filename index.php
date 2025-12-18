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

<article class="about-container">
    <p>Infinity hotel is located on the beautiful Isla Syntax. </p>
</article>

<article class="features-container">
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

<section class="rooms">
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

<?php
require __DIR__ . '/footer.php';
?>

<script src="carousel.js"></script>