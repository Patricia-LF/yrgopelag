<?php
require __DIR__ . '/header.php';

if (isset($_POST['name'], $_POST['transferCode'])) {
    $name = $_POST['name'];
    $transferCode = $_POST['transferCode'];
}

?>

<form action="post.php" method="post">
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
        <select name="room" id="" class="form-input pr-12">
            <option value="1">Economy</option>
            <option value="2">Standard</option>
            <option value="3">Luxury</option>
        </select>
    </div>

    <div>
        <label for="features" class="features">Features</label>
        <div class="features-cont">
            <label class="feature">
                <input class="f" type="checkbox" name="features[]" value="13">
                bad book (Economy, $2)
            </label>
            <label class="feature">
                <input class="f" type="checkbox" name="features[]" value="14">
                good book (Basic, $5)
            </label>
        </div>
    </div>

    <div class="prize-total">
        <p>Total:</p>
        <p>20$</p> <!-- räkna ihop totalsumman -->
    </div>

    <div>
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

    <button type="submit" class="buy-button">Buy</button>
</form>

<?php
require __DIR__ . '/footer.php';
?>