<?php

declare(strict_types=1);

ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/header.php';

// Hotellägarinfo - centralbank )
define('HOTEL_OWNER_USER', 'Patricia ');
define('HOTEL_OWNER_API_KEY', $_ENV['API_KEY']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Validera input
$name = trim($_POST['name'] ?? '');
$arrival = $_POST['arrival'] ?? '';
$departure = $_POST['departure'] ?? '';
$roomId = $_POST['room'] ?? '';
$features = $_POST['features'] ?? [];
$transferCode = trim($_POST['transferCode'] ?? '');

$errors = [];

if (empty($name)) {
    $errors[] = "Please enter your name";
}

if (empty($arrival) || empty($departure)) {
    $errors[] = "Arrival and departure dates are required";
}

if (empty($roomId)) {
    $errors[] = "Please select a room";
}

if (empty($transferCode)) {
    $errors[] = "Transfer code is required";
}

// Beräkna antal nätter
$arrivalDate = new DateTime($arrival);
$departureDate = new DateTime($departure);
$interval = $arrivalDate->diff($departureDate);
$nights = $interval->days;

if ($nights < 1) {
    $errors[] = "Departure must be after arrival";
}

// Kontrollera att rummet är tillgängligt
$statement = $database->prepare("
    SELECT COUNT(*) as count 
    FROM bookings 
    WHERE room_id = ? 
    AND status = 'confirmed'
    AND (
        (arrival <= ? AND departure > ?) OR
        (arrival < ? AND departure >= ?) OR
        (arrival >= ? AND departure <= ?)
    )
");

$statement->execute([$roomId, $arrival, $arrival, $departure, $departure, $arrival, $departure]);
$result = $statement->fetch(PDO::FETCH_ASSOC);

if ($result['count'] > 0) {
    $errors[] = "Room is not available for selected dates";
}

// Beräkna totalpris
$statement = $database->prepare("SELECT price FROM rooms WHERE id = ?");
$statement->execute([$roomId]);
$room = $statement->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    $errors[] = "Invalid room selection";
}

$totalPrice = $room['price'] * $nights;

// Hämta valda features och deras namn
$selectedFeatures = [];
if (!empty($features)) {
    $placeholders = str_repeat('?,', count($features) - 1) . '?';
    $statement = $database->prepare("SELECT id, name, price, activity, tier FROM features WHERE id IN ($placeholders)");
    $statement->execute($features);
    $selectedFeatures = $statement->fetchAll(PDO::FETCH_ASSOC);

    foreach ($selectedFeatures as $feature) {
        $totalPrice += $feature['price'];
    }
}

// Deposit (konsumera transferCode och ta emot betalningen)
if (empty($errors)) {
    $depositData = json_encode([
        'user' => HOTEL_OWNER_USER,
        'transferCode' => $transferCode
    ]);

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => $depositData,
            'ignore_errors' => true,
            'timeout' => 10
        ]
    ]);

    $depositResponse = @file_get_contents('https://www.yrgopelag.se/centralbank/deposit', false, $context);

    if ($depositResponse === false) {
        $error = error_get_last();
        error_log("Deposit failed: " . print_r($error, true));
        error_log("Sent data: " . $depositData);
        $errors[] = "Payment processing failed - could not connect to payment service";
    } else {
        error_log("Deposit response: " . $depositResponse);
        $depositResult = json_decode($depositResponse, true);

        if (!$depositResult || !isset($depositResult['status']) || $depositResult['status'] !== 'success') {
            $errors[] = "Payment failed: " . ($depositResult['error'] ?? 'Invalid transfer code or insufficient funds');
        }
    }
}

// Steg 3: Om betalningen lyckades, skapa bokningen
if (empty($errors)) {
    try {
        $database->beginTransaction();

        // Skapa eller hämta user
        $statement = $database->prepare("SELECT id FROM users WHERE name = ? LIMIT 1");
        $statement->execute([$name]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $statement = $database->prepare("INSERT INTO users (name) VALUES (?)");
            $statement->execute([$name]);
            $userId = $database->lastInsertId();
        } else {
            $userId = $user['id'];
        }

        // Skapa bokning
        $statement = $database->prepare("
            INSERT INTO bookings (room_id, user_id, arrival, departure, nights, total_price, status)
            VALUES (?, ?, ?, ?, ?, ?, 'confirmed')
        ");
        $statement->execute([$roomId, $userId, $arrival, $departure, $nights, $totalPrice]);
        $bookingId = $database->lastInsertId();

        // Lägg till features
        if (!empty($features)) {
            $statement = $database->prepare("INSERT INTO booking_features (booking_id, feature_id) VALUES (?, ?)");
            foreach ($features as $featureId) {
                $statement->execute([$bookingId, $featureId]);
            }
        }

        $database->commit();

        // Steg 4: Skicka kvitto till centralbanken (för analytics/points)
        // Hämta star rating från databasen
        $statement = $database->query("SELECT value FROM settings WHERE key = 'star_rating'");
        $starRating = $statement->fetch()['value'] ?? '5';

        // Formatera features enligt nya formatet
        $featuresUsed = [];
        foreach ($selectedFeatures as $feature) {
            $featuresUsed[] = [
                'activity' => $feature['activity'],
                'tier' => $feature['tier']
            ];
        }

        $receiptData = json_encode([
            'user' => HOTEL_OWNER_USER,
            'api_key' => HOTEL_OWNER_API_KEY,
            'guest_name' => $name,
            'arrival_date' => $arrival,
            'departure_date' => $departure,
            'features_used' => $featuresUsed,
            'star_rating' => (int)$starRating
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $receiptData,
                'ignore_errors' => true
            ]
        ]);

        // Skicka kvitto (men fortsätt även om det misslyckas)
        @file_get_contents('https://www.yrgopelag.se/centralbank/receipt', false, $context);

        // Omdirigera till bekräftelsesida
        header("Location: confirmation.php?booking_id=" . $bookingId);
        exit;
    } catch (Exception $e) {
        if ($database->inTransaction()) {
            $database->rollBack();
        }
        $errors[] = "Database error: " . $e->getMessage();
    }
}

// Visa fel
if (!empty($errors)) { ?>
    <div class='errors'>
        <h2>Booking Failed</h2>
        <ul>
            <?php foreach ($errors as $error) { ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php } ?>
        </ul>
        <a href='booking-page.php' class='back-link'>Go back to booking form</a>
    </div>
<?php }

require __DIR__ . '/footer.php';
