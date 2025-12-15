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
        <label for="name">Name (User ID):</label>
        <input type="name" name="name" placeholder="Jennifer">
    </div>

    <div>
        <label for="arrival">Arrival:</label>
        <input type="date" name="arrival" class="form-input" min="2026-01-01" max="2026-01-31">
    </div>

    <div>
        <label for="departure">Departure:</label>
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
        <div class="mb-2">
            <p class="font-semibold capitalize features">cozy</p>
            <label class="block ml-2">
                <input class="mr-2" type="checkbox" name="features[]" value="13">
                bad book (Economy, $2)
            </label>
            <label class="block ml-2">
                <input class="mr-2" type="checkbox" name="features[]" value="14">
                good book (Basic, $5)
            </label>
        </div>
        <label class="block ml-2">
            <input class="mr-2" type="checkbox" name="features[]" value="13">
            bad book (Economy, $2)
        </label>
        <label class="block ml-2">
            <input class="mr-2" type="checkbox" name="features[]" value="14">
            good book (Basic, $5)
        </label>
    </div>

    <div>
        <label for="transferCode">Transfer code:</label>
        <a href="https://www.yrgopelag.se/centralbank"
            class="code-button">Create transfer code</a>
        <input type="transferCode" name="transferCode" placeholder="****-****-****-****">
    </div>

    <button type="submit">Submit</button>
</form>

<?php
require __DIR__ . '/footer.php';
?>