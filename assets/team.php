<?php
session_start();

// Example: assuming you already set these during login
// $_SESSION['login_user_id'], $_SESSION['login_user_name'], $_SESSION['login_user_phone']

// Make sure name and phone are always stored for payment page
if (!isset($_SESSION['user_name']) && isset($_SESSION['login_user_name'])) {
    $_SESSION['user_name'] = $_SESSION['login_user_name'];
}
if (!isset($_SESSION['user_phone']) && isset($_SESSION['login_user_phone'])) {
    $_SESSION['user_phone'] = $_SESSION['login_user_phone'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'] ?? 'individual';
    $events = $_POST['events'] ?? [];
    $names1 = $_POST['name1'] ?? [];
    $contacts1 = $_POST['contact1'] ?? [];
    $names2 = $_POST['name2'] ?? [];
    $contacts2 = $_POST['contact2'] ?? [];

    // Pricing rules
    $eventPrices = [
        "Tug of War" => 800,
        "Corporate Walk" => 1000
    ];
    $defaultPrice = 100;

    // Auto-upgrade to group if more than 3 events
    if ($type === "individual" && count($events) > 3) {
        $type = "group";
    }

    // Calculate amount
    if ($type === "group") {
        $amount = 1600;
    } else {
        $amount = 0;
        foreach ($events as $event) {
            $amount += $eventPrices[$event] ?? $defaultPrice;
        }
    }

    // Store in session for payment
    $_SESSION['registration'] = [
        'type' => $type,
        'events' => $events,
        'names1' => $names1,
        'contacts1' => $contacts1,
        'names2' => $names2,
        'contacts2' => $contacts2,
        'amount' => $amount
    ];

    // Redirect to payment
    header("Location: razorpay/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Registration</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { background: #fff; padding: 20px; border-radius: 10px; max-width: 800px; margin: auto; }
        .event-list { display: flex; flex-wrap: wrap; gap: 10px; }
        .event-item { padding: 10px; border: 1px solid #ccc; border-radius: 6px; background: #fafafa; }
        .participant-fields { margin: 10px 0; padding: 10px; border: 1px dashed #ccc; }
        .amount-box { margin-top: 20px; font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <h2>Register for Events</h2>
    <form method="POST" id="eventForm">
        <label><input type="radio" name="type" value="individual" checked> Individual</label>
        <label><input type="radio" name="type" value="group"> Group</label>

        <div class="event-list">
            <label class="event-item"><input type="checkbox" name="events[]" value="Singing"> Singing</label>
            <label class="event-item"><input type="checkbox" name="events[]" value="Dancing"> Dancing</label>
            <label class="event-item"><input type="checkbox" name="events[]" value="Drama"> Drama</label>
            <label class="event-item"><input type="checkbox" name="events[]" value="Tug of War"> Tug of War</label>
            <label class="event-item"><input type="checkbox" name="events[]" value="Corporate Walk"> Corporate Walk</label>
        </div>

        <div id="participantsContainer"></div>
        <div class="amount-box">Total Amount: ₹<span id="totalAmount">0</span></div>

        <button type="submit">Proceed to Payment</button>
    </form>
</div>

<script>
    const eventPrices = {
        "Singing": 100,
        "Dancing": 100,
        "Drama": 100,
        "Tug of War": 800,
        "Corporate Walk": 1000
    };
    const maxIndividualEvents = 3;

    function updateForm() {
        let selectedType = document.querySelector('input[name="type"]:checked').value;
        let selectedEvents = Array.from(document.querySelectorAll('input[name="events[]"]:checked')).map(e => e.value);

        if (selectedType === "individual" && selectedEvents.length > maxIndividualEvents) {
            document.querySelector('input[value="group"]').checked = true;
            selectedType = "group";
        }

        const container = document.getElementById('participantsContainer');
        container.innerHTML = '';

        selectedEvents.forEach(event => {
            let div = document.createElement('div');
            div.className = 'participant-fields';
            div.innerHTML = `
                <h4>${event}</h4>
                <input type="text" name="name1[]" placeholder="Participant 1 Name" required>
                <input type="text" name="contact1[]" placeholder="Participant 1 Contact" required>
                <input type="text" name="name2[]" placeholder="Participant 2 Name" required>
                <input type="text" name="contact2[]" placeholder="Participant 2 Contact" required>
            `;
            container.appendChild(div);
        });

        let amount = 0;
        if (selectedType === "group") {
            amount = 1600;
        } else {
            selectedEvents.forEach(event => {
                amount += eventPrices[event] || 0;
            });
        }
        document.getElementById('totalAmount').textContent = amount;
    }

    document.querySelectorAll('input[name="events[]"]').forEach(cb => cb.addEventListener('change', updateForm));
    document.querySelectorAll('input[name="type"]').forEach(radio => radio.addEventListener('change', updateForm));
</script>
</body>
</html>
