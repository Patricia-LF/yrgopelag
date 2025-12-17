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

<style>
    .confirmation-container {
        max-width: 800px;
        margin: 40px auto;
        padding: 20px;
    }

    .confirmation-header {
        text-align: center;
        padding: 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        margin-bottom: 30px;
    }

    .confirmation-header h1 {
        margin: 0 0 10px 0;
        font-size: 2.5em;
    }

    .booking-number {
        font-size: 1.2em;
        opacity: 0.9;
    }

    .confirmation-details {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .detail-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e0e0e0;
    }

    .detail-section:last-child {
        border-bottom: none;
    }

    .detail-section h3 {
        color: #667eea;
        margin-bottom: 15px;
    }

    .detail-section p {
        margin: 8px 0;
        line-height: 1.6;
    }

    .features-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .features-list li {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        background: #f8f9fa;
        margin-bottom: 8px;
        border-radius: 5px;
    }

    .feature-tier {
        color: #666;
        font-size: 0.9em;
    }

    .feature-price {
        font-weight: bold;
        color: #667eea;
    }

    .features-subtotal {
        text-align: right;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #ddd;
    }

    .total-section {
        background: #f0f4ff;
        padding: 20px;
        border-radius: 8px;
        border: none;
    }

    .total-amount {
        font-size: 2em;
        color: #667eea;
        font-weight: bold;
        margin: 10px 0;
    }

    .payment-status {
        color: #28a745;
        font-weight: bold;
    }

    .confirmation-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 30px;
        border: none;
        border-radius: 5px;
        font-size: 1em;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #5568d3;
    }

    .btn-secondary {
        background: white;
        color: #667eea;
        border: 2px solid #667eea;
    }

    .btn-secondary:hover {
        background: #f0f4ff;
    }

    @media print {
        .confirmation-actions {
            display: none;
        }
    }
</style>

<?php
require __DIR__ . '/footer.php';
?>