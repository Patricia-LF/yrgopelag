<?php

require __DIR__ . '/header.php';

$database = new PDO('sqlite:yrgopelag.db');

$statement = $database->query("SELECT id, type, price FROM rooms ORDER BY price");
$rooms = $statement->fetchAll(PDO::FETCH_ASSOC);

$statement = $database->query("SELECT id, activity, tier, name, price FROM features WHERE is_active = 1 ORDER BY activity, price");
$features = $statement->fetchAll(PDO::FETCH_ASSOC);

// Skapa prisdata för JavaScript
$roomPrices = [];
foreach ($rooms as $room) {
    $roomPrices[$room['id']] = (int)$room['price'];
}

$featurePrices = [];
foreach ($features as $feature) {
    $featurePrices[$feature['id']] = (int)$feature['price'];
}

// BOOKING-LOGIK - körs bara när formuläret skickas
if (isset($_POST['name'], $_POST['transferCode'])) {
    $name = trim($_POST['name']);
    $transferCode = trim($_POST['transferCode']);
    $roomId = $_POST['room_id'] ?? null;
    $arrival = $_POST['arrival'] ?? null;
    $departure = $_POST['departure'] ?? null;
    $selectedFeatures = $_POST['features'] ?? [];

    header('Location: confirmation.php?booking_id=' . $bookingId);
    exit;
}
?>

<form action="/form.php" method="post">
    <h2>Book a room</h2>
    <div>
        <label for="name" class="user">Name (User ID):</label>
        <input type="name" name="name" placeholder="Jennifer">
    </div>

    <div>
        <label for="arrival" class="arrival">Arrival:</label>
        <input type="date" name="arrival" class="form-input" min="2026-01-01" max="2026-01-31">
    </div>

    <div>
        <label for="departure" class="departure">Departure:</label>
        <input type="date" name="departure" class="form-input" min="2026-01-01" max="2026-01-31">
    </div>

    <div>
        <label for="room" class="room-type">Room</label>
        <select name="room" id="room" class="form-input pr-12" required>
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
                            <input class="f feature-checkbox" type="checkbox" name="features[]" value="<?= $feature['id'] ?>" data-feature-name="<?= htmlspecialchars($feature['name']) ?>">
                            <strong><?= ucfirst(htmlspecialchars($feature['tier'])) ?>:</strong>
                            <?= htmlspecialchars($feature['name']) ?>
                            <span class="price">($<?= $feature['price'] ?>)</span>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="price-total">
        <!-- <p>Nights: <span id="nightsCount">0</span></p>
        <p>Room cost: $<span id="roomCost">0</span></p>
        <p>Features cost: $<span id="featuresCost">0</span></p> -->
        <p><strong>Total: $<span id="totalCost">0</span></strong></p>
    </div>

    <div class="transfer-section">
        <h3>Payment</h3>

        <div id="transferForm">
            <p>Create a transfer code to pay for your booking:</p>

            <label for="username" class="username">Your Username (Centralbank)</label>
            <input type="text" id="username" placeholder="Your name" required>

            <label for="api_key" class="api-key">Your API Key</label>
            <input type="password" id="api_key" placeholder="Your API key" required>

            <button type="button" id="createTransferBtn" class="code-button">Create Transfer Code</button>
            <span id="createStatus"></span>
        </div>

        <div id="transferResult" style="display: none;">
            <p class="success-message">✓ Transfer code created successfully!</p>
        </div>

        <label for="transferCode" class="tr-code">Transfer Code:</label>
        <input type="text" name="transferCode" id="transferCode" placeholder="Generated after clicking button above" required readonly>
    </div>

    <button type="submit" class="buy-button" id="submitBtn" disabled>Complete Booking</button>
</form>

<script>
    // Prisdata från PHP
    const roomPrices = <?= json_encode($roomPrices) ?>;
    const featurePrices = <?= json_encode($featurePrices) ?>;

    // Beräkna totalpris
    function calculateTotal() {
        const arrival = document.getElementById('arrival').value;
        const departure = document.getElementById('departure').value;
        const roomId = document.getElementById('room').value;
        const featureCheckboxes = document.querySelectorAll('.feature-checkbox:checked');

        // Beräkna antal nätter
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

        // Beräkna rumskostnad
        const roomCost = nights > 0 ? roomPrices[roomId] * nights : 0;

        // Beräkna features-kostnad
        let featuresCost = 0;
        featureCheckboxes.forEach(checkbox => {
            featuresCost += featurePrices[checkbox.value];
        });

        // Totalt
        const total = roomCost + featuresCost;

        // Uppdatera UI
        /* document.getElementById('nightsCount').textContent = nights;
        document.getElementById('roomCost').textContent = roomCost;
        document.getElementById('featuresCost').textContent = featuresCost; */
        document.getElementById('totalCost').textContent = total;

        return total;
    }

    // Skapa transfer code via centralbanken (kund-sida: withdraw)
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

        try {
            // Använd withdraw endpoint för att skapa transferCode (kund betalar)
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
                statusEl.className = 'success';
            } else {
                statusEl.textContent = 'Error: ' + (data.error || 'Unknown error');
                statusEl.className = 'error';
            }
        } catch (error) {
            statusEl.textContent = 'Error: ' + error.message;
            statusEl.className = 'error';
        }
    });

    // Lyssna på ändringar
    document.getElementById('arrival').addEventListener('change', calculateTotal);
    document.getElementById('departure').addEventListener('change', calculateTotal);
    document.getElementById('room').addEventListener('change', calculateTotal);
    document.querySelectorAll('.feature-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotal);
    });

    // Initial beräkning
    calculateTotal();
</script>

<?php
require __DIR__ . '/footer.php';
?>