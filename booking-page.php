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

// Hämta bokningar för kalendrarna
$statement = $database->query("
    SELECT room_id, arrival, departure 
    FROM bookings 
    WHERE status = 'confirmed'
    AND (
        (arrival >= '2026-01-01' AND arrival <= '2026-01-31') OR
        (departure >= '2026-01-01' AND departure <= '2026-01-31') OR
        (arrival <= '2026-01-01' AND departure >= '2026-01-31')
    )
");
$bookings = $statement->fetchAll(PDO::FETCH_ASSOC);

// Skapa array med bokade datum per rum
$bookedDates = [];
foreach ($bookings as $booking) {
    $roomId = $booking['room_id'];
    $start = new DateTime($booking['arrival']);
    $end = new DateTime($booking['departure']);

    while ($start < $end) {
        if ($start->format('Y-m') === '2026-01') {
            $day = (int)$start->format('d');
            $bookedDates[$roomId][$day] = true;
        }
        $start->modify('+1 day');
    }
}

function isWeekend($day)
{
    $date = new DateTime('2026-01-' . str_pad($day, 2, '0', STR_PAD_LEFT));
    return $date->format('N') >= 6;
}
?>

<!-- <link rel="stylesheet" href="/calendar.css"> -->

<div class="booking-layout">
    <form action="form.php" method="post">
        <h2>Book a room</h2>

        <div>
            <label for="name" class="user">Name (User ID):</label>
            <input type="text" name="name" id="name" placeholder="Enter your name" required>
        </div>

        <div>
            <label for="arrival" class="arrival">Arrival:</label>
            <input type="date" name="arrival" id="arrival" class="form-input"
                min="2026-01-01" max="2026-01-31" required>
        </div>

        <div>
            <label for="departure" class="departure">Departure:</label>
            <input type="date" name="departure" id="departure" class="form-input"
                min="2026-01-01" max="2026-01-31" required>
        </div>

        <div>
            <label for="room" class="room-type">Room</label>
            <select name="room" id="room" class="form-input pr-12" required>
                <option value="">Select room</option>
                <?php foreach ($rooms as $room): ?>
                    <option value="<?= $room['id'] ?>">
                        <?= ucfirst(htmlspecialchars($room['type'])) ?> - $<?= $room['price'] ?>/night
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="features">Features</label>
            <div class="features-cont">
                <?php foreach ($featuresByActivity as $activity => $activityFeatures): ?>
                    <div class="feature-category">
                        <h4><?= ucfirst(str_replace('-', ' ', htmlspecialchars($activity))) ?></h4>
                        <?php foreach ($activityFeatures as $feature): ?>
                            <label class="feature">
                                <input class="f feature-checkbox" type="checkbox" name="features[]"
                                    value="<?= $feature['id'] ?>">
                                <strong><?= ucfirst(htmlspecialchars($feature['name'])) ?>: </strong>
                                <?= ucfirst(htmlspecialchars($feature['tier'])) ?>,
                                <span class="price">($<?= $feature['price'] ?>)</span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="price-total">
            <p><strong>Total: $<span id="totalCost">0</span></strong></p>
        </div>

        <div class="transfer-section">
            <h3>Payment</h3>

            <div id="transferForm">
                <p>Create a transfer code to pay <strong>$<span id="paymentAmount">0</span></strong>:</p>

                <label for="username" class="username">Your Username (Centralbank)</label>
                <input type="text" id="username" placeholder="Enter your name" required>

                <label for="api_key" class="api-key">Your API Key</label>
                <input type="password" id="api_key" placeholder="Enter your API key" required>

                <button type="button" id="createTransferBtn" class="code-button">
                    Create Transfer Code
                </button>
                <span id="createStatus"></span>
            </div>

            <div id="transferResult" style="display: none;">
                <p class="success-message">✓ Transfer code created successfully!</p>
            </div>

            <label for="transferCode" class="tr-code">Transfer Code:</label>
            <input type="text" name="transferCode" id="transferCode"
                placeholder="Generate transfer code above" required readonly>
        </div>

        <button type="submit" class="buy-button" id="submitBtn" disabled>Complete Booking</button>
    </form>


    <!-- Höger sida: Kalendrar -->
    <div class="calendars-section">
        <h2 class="calendars-title">Room Availability</h2>

        <div class="calendars-wrapper">
            <?php foreach ($rooms as $room): ?>
                <div class="calendar-container">
                    <h2><?= ucfirst(htmlspecialchars($room['type'])) ?> Room</h2>
                    <h3>January 2026</h3>

                    <!-- Veckodagar header -->
                    <div class="weekday-header">
                        <div class="weekday">Mon</div>
                        <div class="weekday">Tue</div>
                        <div class="weekday">Wed</div>
                        <div class="weekday">Thu</div>
                        <div class="weekday">Fri</div>
                        <div class="weekday">Sat</div>
                        <div class="weekday">Sun</div>
                    </div>

                    <section class="calendar">
                        <?php
                        // Hitta vilken veckodag 1 januari 2026 är (1=måndag, 7=söndag)
                        $firstDay = new DateTime('2026-01-01');
                        $dayOfWeek = (int)$firstDay->format('N'); // 1-7

                        // Lägg till tomma rutor för dagar före 1:a
                        for ($i = 1; $i < $dayOfWeek; $i++) {
                            echo '<div class="day empty"></div>';
                        }

                        // Lägg till alla dagar i januari
                        for ($day = 1; $day <= 31; $day++) {
                            $classes = ['day'];

                            if (isWeekend($day)) {
                                $classes[] = 'weekend';
                            }

                            if (isset($bookedDates[$room['id']][$day])) {
                                $classes[] = 'booked';
                            }
                        ?>
                            <div class="<?= implode(' ', $classes) ?>"><?= $day ?></div>
                        <?php } ?>
                    </section>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="legend-container">
            <h3>Legend</h3>
            <div class="legend-items">
                <div class="legend-item">
                    <div class="legend-box available">A</div>
                    <span>Available</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box booked">B</div>
                    <span>Booked</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box weekend">W</div>
                    <span>Weekend</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Din befintliga JavaScript kod här...
    const roomPrices = <?= json_encode($roomPrices) ?>;
    const featurePrices = <?= json_encode($featurePrices) ?>;

    function calculateTotal() {
        const arrival = document.getElementById('arrival').value;
        const departure = document.getElementById('departure').value;
        const roomId = document.getElementById('room').value;
        const featureCheckboxes = document.querySelectorAll('.feature-checkbox:checked');

        let nights = 0;
        if (arrival && departure) {
            const arrivalDate = new Date(arrival);
            const departureDate = new Date(departure);
            const diffTime = departureDate - arrivalDate;
            nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (nights < 1) {
                nights = 0;
            }
        }

        const roomCost = (nights > 0 && roomId) ? roomPrices[roomId] * nights : 0;

        let featuresCost = 0;
        featureCheckboxes.forEach(checkbox => {
            featuresCost += featurePrices[checkbox.value];
        });

        const total = roomCost + featuresCost;
        document.getElementById('totalCost').textContent = total;
        document.getElementById('paymentAmount').textContent = total;

        return total;
    }

    document.getElementById('createTransferBtn').addEventListener('click', async function() {
        const username = document.getElementById('username').value.trim();
        const apiKey = document.getElementById('api_key').value.trim();
        const amount = calculateTotal();

        if (!username || !apiKey) {
            alert('Please enter your username and API key');
            return;
        }

        if (amount <= 0) {
            alert('Please select dates and room first');
            return;
        }

        const statusEl = document.getElementById('createStatus');
        statusEl.textContent = 'Creating transfer code...';
        statusEl.className = '';

        try {
            const response = await fetch('https://www.yrgopelag.se/centralbank/withdraw', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user: username,
                    api_key: apiKey,
                    amount: amount
                })
            });

            const data = await response.json();

            if (data.transferCode) {
                document.getElementById('transferCode').value = data.transferCode;
                document.getElementById('transferResult').style.display = 'block';
                document.getElementById('submitBtn').disabled = false;
                statusEl.textContent = '';
            } else {
                statusEl.textContent = 'Error: ' + (data.error || 'Unknown error');
                statusEl.className = 'error';
            }
        } catch (error) {
            statusEl.textContent = 'Error: ' + error.message;
            statusEl.className = 'error';
        }
    });

    document.getElementById('arrival').addEventListener('change', calculateTotal);
    document.getElementById('departure').addEventListener('change', calculateTotal);
    document.getElementById('room').addEventListener('change', calculateTotal);
    document.querySelectorAll('.feature-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotal);
    });

    calculateTotal();
</script>

<?php
require __DIR__ . '/footer.php';
?>