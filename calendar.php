<link rel="Stylesheet" href="/calendar.css">

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