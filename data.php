<?php
require __DIR__ . '/header.php';

// Hämta rum från databasen
$statement = $database->query("SELECT id, type, price FROM rooms ORDER BY price");
$rooms = $statement->fetchAll(PDO::FETCH_ASSOC);

// Hämta features grupperade per kategori och tier
$statement = $database->query("SELECT id, activity, tier, name, price FROM features WHERE is_active = 1 ORDER BY activity, price");
$features = $statement->fetchAll(PDO::FETCH_ASSOC);

// Gruppera features per aktivitet
$featuresByActivity = [];
foreach ($features as $feature) {
    $featuresByActivity[$feature['activity']][] = $feature;
}

// Skapa prisdata för JavaScript
$roomPrices = [];
foreach ($rooms as $room) {
    $roomPrices[$room['id']] = $room['price'];
}

$featurePrices = [];
foreach ($features as $feature) {
    $featurePrices[$feature['id']] = $feature['price'];
}
?>

<form action="post.php" method="post" id="bookingForm">
    <h2>Book a room</h2>

    <div>
        <label for="name" class="user">Name:</label>
        <input type="text" name="name" id="name" placeholder="Jennifer" required>
    </div>

    <div>
        <label for="arrival" class="arrival">Arrival:</label>
        <input type="date" name="arrival" id="arrival" class="form-input" min="2026-01-01" max="2026-01-31" required>
    </div>

    <div>
        <label for="departure" class="departure">Departure:</label>
        <input type="date" name="departure" id="departure" class="form-input" min="2026-01-01" max="2026-01-31" required>
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

    <!--  <div>
        <label for="features" class="features">Features</label>
        <div class="features-cont">
            <label class="feature">
                <input class="f" type="checkbox" name="features[]" value="13">
                Pool (Water, Economy, $2)
            </label>
            <label class="feature">
                <input class="f" type="checkbox" name="features[]" value="14">
                Breakfast buffet (Hotel-specific, Economy, $2)
            </label>
            <label class="feature">
                <input class="f" type="checkbox" name="features[]" value="15">
                Bowling (Hotel-specific, Basic, $5)
            </label>
            <label class="feature">
                <input class="f" type="checkbox" name="features[]" value="16">
                Spa access (Hotel-specific, Premium, $10)
            </label>
            <label class="feature">
                <input class="f" type="checkbox" name="features[]" value="17">
                Private beach (Hotel-specific, Superior, $17)
            </label>
        </div>
    </div> -->

    <div class="price-total">
        <p>Nights: <span id="nightsCount">0</span></p>
        <p>Room cost: $<span id="roomCost">0</span></p>
        <p>Features cost: $<span id="featuresCost">0</span></p>
        <p><strong>Total: $<span id="totalCost">0</span></strong></p>
    </div>

    <!-- <div class="price-total">
        <p>Total:</p>
        <p>20$</p> räkna ihop totalsumman
    </div> -->

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

    <!--  <div>
        <p>Create transfercode</p>
        <label for="username" class="username">Username</label>
        <input type="name" name="name" placeholder="Jennifer">

        <label for="api_key" class="api-key">API_KEY</label>
        <input type="password" name="password">

        <label for="amount" class="amount">Amount</label>
        <input type="number" name="amount">

        <a href="https://www.yrgopelag.se/centralbank"
            class="code-button">Create transfercode</a>
    </div>

    <div>
        <label for="transferCode" class="tr-code">Transfer code:</label>
        <div class="code-container">
            <a href="https://www.yrgopelag.se/centralbank"
                class="code-button">Create transfercode</a>
            <input type="transferCode" name="transferCode" placeholder="****-****-****-****">
        </div>
    </div>

    <button type="submit" class="buy-button">Buy</button> -->
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
        document.getElementById('nightsCount').textContent = nights;
        document.getElementById('roomCost').textContent = roomCost;
        document.getElementById('featuresCost').textContent = featuresCost;
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

<style>
    .feature-category {
        margin-bottom: 20px;
        padding: 15px;
        background: #f5f5f5;
        border-radius: 8px;
    }

    .feature-category h4 {
        margin: 0 0 10px 0;
        color: #333;
        text-transform: capitalize;
    }

    .feature {
        display: block;
        padding: 8px;
        margin: 5px 0;
        background: white;
        border-radius: 4px;
    }

    .feature:hover {
        background: #e8f4f8;
    }

    .price {
        color: #0066cc;
        font-weight: bold;
    }

    #createStatus {
        display: inline-block;
        margin-left: 10px;
        font-size: 14px;
    }

    #createStatus.success {
        color: green;
    }

    #createStatus.error {
        color: red;
    }

    .success-message {
        color: green;
        font-weight: bold;
        padding: 10px;
        background: #e8f5e9;
        border-radius: 4px;
        margin: 10px 0;
    }
</style>

<?php
require __DIR__ . '/footer.php';
?>