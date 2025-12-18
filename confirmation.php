<?php
require __DIR__ . '/header.php';

$bookingId = $_GET['booking_id'] ?? null;

if (!$bookingId) {
    header('Location: index.php');
    exit;
}

// Hämta bokningsdetaljer
$statement = $database->prepare("
    SELECT 
        b.*,
        r.type as room_type,
        r.price as room_price,
        u.name as guest_name
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    JOIN users u ON b.user_id = u.id
    WHERE b.id = ?
");
$statement->execute([$bookingId]);
$booking = $statement->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    echo "<p>Booking not found.</p>";
    require __DIR__ . '/footer.php';
    exit;
}

// Hämta features för bokningen
$statement = $database->prepare("
    SELECT f.name, f.price, f.activity, f.tier
    FROM booking_features bf
    JOIN features f ON bf.feature_id = f.id
    WHERE bf.booking_id = ?
");
$statement->execute([$bookingId]);
$features = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="confirmation-container">
    <div class="confirmation-header">
        <h1>✓ Booking Confirmed!</h1>
        <p class="booking-number">Booking #<?= htmlspecialchars($bookingId) ?></p>
    </div>

    <div class="confirmation-details">
        <h2>Booking Details</h2>

        <div class="detail-section">
            <h3>Guest Information</h3>
            <p><strong>Name:</strong> <?= htmlspecialchars($booking['guest_name']) ?></p>
        </div>

        <div class="detail-section">
            <h3>Stay Details</h3>
            <p><strong>Check-in:</strong> <?= date('F j, Y', strtotime($booking['arrival'])) ?></p>
            <p><strong>Check-out:</strong> <?= date('F j, Y', strtotime($booking['departure'])) ?></p>
            <p><strong>Number of nights:</strong> <?= $booking['nights'] ?></p>
        </div>

        <div class="detail-section">
            <h3>Room</h3>
            <p><strong><?= ucfirst(htmlspecialchars($booking['room_type'])) ?> Room</strong></p>
            <p>$<?= $booking['room_price'] ?> per night × <?= $booking['nights'] ?> nights = $<?= $booking['room_price'] * $booking['nights'] ?></p>
        </div>

        <?php if (!empty($features)): ?>
            <div class="detail-section">
                <h3>Selected Features</h3>
                <ul class="features-list">
                    <?php
                    $featuresTotal = 0;
                    foreach ($features as $feature):
                        $featuresTotal += $feature['price'];
                    ?>
                        <li>
                            <span class="feature-name">
                                <?= htmlspecialchars($feature['name']) ?>
                                <span class="feature-tier">(<?= ucfirst(htmlspecialchars($feature['tier'])) ?>)</span>
                            </span>
                            <span class="feature-price">$<?= $feature['price'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p class="features-subtotal"><strong>Features total: $<?= $featuresTotal ?></strong></p>
            </div>
        <?php endif; ?>

        <div class="detail-section total-section">
            <h3>Total Payment</h3>
            <p class="total-amount">$<?= $booking['total_price'] ?></p>
            <p class="payment-status">✓ Payment received and confirmed</p>
        </div>
    </div>

    <div class="confirmation-actions">
        <a href="index.php" class="btn btn-primary">Make Another Booking</a>
        <button onclick="window.print()" class="btn btn-secondary">Print Confirmation</button>
    </div>
</div>

<?php
require __DIR__ . '/footer.php';
?>