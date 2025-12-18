<?php

require __DIR__ . '/header.php';

?>

<div class="carousel-container" id="carousel">
    <!-- Slides kommer att genereras av JavaScript -->
</div>

<article class="about-container">
    <p>Infinity hotel is located on the beautiful Isla Syntax. </p>
</article>

<section class="features-container">
    <p>Features:</p>
    <ul>
        <li>Ping-pong table</li>
        <li>Pool</li>
        <li>Casino</li>
        <li>Good book</li>
    </ul>
</section>

<article class="booking-dates">
    <div class="check-in-container">
        <p>Check-in date:</p>
        <div class="check-in-date">
            <input type="date" name="check-in" class="check-in" min="2026-01-01" max="2026-01-31">
        </div>
    </div>

    <img src="images/Arrow 1.png">

    <div class="check-out-container">
        <p>Check-out date:</p>
        <div class="check-out-date">
            <p>Monday, jan 10</p>
            <img src="images/calendar-icon.png">
        </div>
    </div>
</article>

<article class="room-container">
    <div>
        <img src="images/room3.png">
    </div>

    <div>
        <p>Economy</p>
        <p>2c/night</p>
    </div>
    <div class="calendar-container">
        <h2>January 2026</h2>
        <section class="calendar">
            <?php
            for ($i = 1; $i <= 31; $i++) :
            ?>
                <div class="day"><?= $i; ?></div>
            <?php endfor; ?>
        </section>
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
    <div class="calendar-container">
        <h2>January 2026</h2>
        <section class="calendar">
            <?php
            for ($i = 1; $i <= 31; $i++) :
            ?>
                <div class="day"><?= $i; ?></div>
            <?php endfor; ?>
        </section>
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
    <div class="calendar-container">
        <h2>January 2026</h2>
        <section class="calendar">
            <?php
            for ($i = 1; $i <= 31; $i++) :
            ?>
                <div class="day"><?= $i; ?></div>
            <?php endfor; ?>
        </section>
    </div>
</article>

<?php
require __DIR__ . '/footer.php';
?>

<script src="carousel.js"></script>