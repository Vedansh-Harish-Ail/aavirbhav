<?php
// db.php - Database Connection
$conn = new mysqli("localhost", "root", "", "aavirbhav");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $events = $_POST['events'];
    $names = $_POST['name'];
    $contacts = $_POST['contact'];

    // Calculate amount
    if ($type == "individual") {
        $amount = count($events) * 100;
    } else {
        $amount = 1500;
    }

    // Insert each participant
    for ($i = 0; $i < count($events); $i++) {
        $event = $events[$i];
        $pname = $names[$i];
        $pcontact = $contacts[$i];

        $stmt = $conn->prepare("INSERT INTO registrations (type, event, name, contact, amount) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $type, $event, $pname, $pcontact, $amount);
        $stmt->execute();
    }

    echo "<script>alert('Registration successful!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Registration</title>
    <style>
        body { font-family: Arial, sans-serif; padding:20px; }
        .hidden { display: none; margin-top:10px; }
        .event-list { margin: 10px 0; }
        .participant-box { border:1px solid #ccc; padding:10px; margin:10px 0; }
    </style>
    <script>
        function toggleEvents(type) {
            document.getElementById("eventsDiv").style.display = "block";
            document.getElementById("detailsDiv").innerHTML = "";
            document.getElementById("eventType").value = type;
            document.getElementById("amountInfo").innerText = 
                type === 'individual' ? "Each event costs ₹100." : "Fixed cost ₹1500 for all events.";
        }

        function showDetailsForm() {
            let selected = document.querySelectorAll("input[name='events[]']:checked");
            let detailsDiv = document.getElementById("detailsDiv");
            detailsDiv.innerHTML = ""; // reset

            if (selected.length === 0) {
                alert("Please select at least one event");
                return;
            }

            selected.forEach((checkbox, index) => {
                let div = document.createElement("div");
                div.className = "participant-box";
                div.innerHTML = `
                    <h4>${checkbox.value}</h4>
                    <label>Name: <input type="text" name="name[]" required></label><br><br>
                    <label>Contact: <input type="text" name="contact[]" required></label>
                `;
                detailsDiv.appendChild(div);
            });

            detailsDiv.style.display = "block";
            document.getElementById("submitBtn").style.display = "block";
        }
    </script>
</head>
<body>
    <h2>Event Registration</h2>

    <form method="POST">
        <label>
            <input type="radio" name="chooseType" onclick="toggleEvents('individual')"> Individual
        </label>
        <label>
            <input type="radio" name="chooseType" onclick="toggleEvents('group')"> Group
        </label>

        <div id="eventsDiv" class="hidden">
            <h3>Select Events</h3>
            <p id="amountInfo"></p>
            <div class="event-list">
                <?php 
                for ($i=1; $i<=10; $i++) {
                    echo "<label><input type='checkbox' name='events[]' value='Event $i'> Event $i</label><br>";
                }
                ?>
            </div>
            <button type="button" onclick="showDetailsForm()">Next</button>
        </div>

        <input type="hidden" id="eventType" name="type">

        <div id="detailsDiv" class="hidden"></div>

        <button type="submit" id="submitBtn" style="display:none;">Submit</button>
    </form>
</body>
</html>
