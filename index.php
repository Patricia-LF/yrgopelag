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

<section>
    <?php foreach ($rooms as $room): ?>
        <article class="room-container">

            <div>
                <?= ucfirst($room['type']) ?> - $<?= $room['price'] ?>/night
            </div>
        </article>
    <?php endforeach; ?>
    <div>
        <img src="images/room3.png">
    </div>

    <article class="room-container">

        <div>
            <img src="images/room3.png">
        </div>
        <div>
            <p>Economy</p>
            <p>2c/night</p>
        </div>
    </article>

    <article class="room-container">
        <div>
            <img src="images/Standard.png">
        </div>

        <div>
            <p>Standard</p>
            <p>5c/night</p>
        </div>
    </article>

    <article class="room-container">
        <div>
            <img src="images/Gemini_Generated_Image_chpf31chpf31chpf(1).png">
        </div>

        <div class="room-info">
            <p class="room-type">Luxury</p>
            <p class="room-descripiton">Our Luxury suite includes a king size bed, a private pool and free access to all features in the economy tier </p>
            <p class="room-price">7c/night</p>
        </div>
    </article>
</section>

<?php
require __DIR__ . '/footer.php';
?>

<script src="carousel.js"></script>