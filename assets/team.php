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
/* ===== Base Reset & Variables ===== */
:root {
  --bg: #0b1220;
  --panel: #111a2b;
  --muted: #c7d2fe;
  --text: #e5e7eb;
  --accent: #7c3aed;
  --accent-2: #22d3ee;
  --border: #2a3550;
  --success: #22c55e;
  --danger: #ef4444;
  --radius: 16px;
  --shadow: 0 10px 25px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.04);
}

* { box-sizing: border-box; }
html, body { margin:0; padding:0; }
body {
  font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji';
  line-height: 1.6;
  color: var(--text);
  background: radial-gradient(1200px 800px at 15% -10%, rgba(124,58,237,.25), transparent 60%),
              radial-gradient(1000px 700px at 110% 20%, rgba(34,211,238,.2), transparent 60%),
              var(--bg);
  min-height: 100vh;
  padding: 40px 16px;
  display: grid;
  place-items: start center;
}

/* ===== Page Shell ===== */
main, form {
  width: 100%;
  max-width: 980px;
}

.card {
  background: linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.01));
  backdrop-filter: blur(8px);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 24px;
}

h1, h2, h3 {
  line-height: 1.2;
  margin: 0 0 12px;
}
h2 {
  font-size: clamp(1.4rem, 1.2rem + 1vw, 2rem);
  background: linear-gradient(90deg, var(--accent), var(--accent-2));
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  letter-spacing: .2px;
  margin-bottom: 20px;
}

/* ===== Controls ===== */
label { display: inline-flex; align-items: center; gap: 10px; }
input[type="text"], input[type="tel"], input[type="number"], select {
  width: 100%;
  appearance: none;
  background: #0e1627;
  color: var(--text);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 10px 12px;
  outline: none;
  transition: border-color .2s, box-shadow .2s;
}
input:focus, select:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(124,58,237,.25);
}

input[type="radio"], input[type="checkbox"] {
  width: 18px; height: 18px;
  accent-color: var(--accent);
}

/* ===== Layout Helpers ===== */
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 14px;
}
.row { display: flex; gap: 12px; flex-wrap: wrap; }
.hidden { display: none; }

/* ===== Event List ===== */
.event-list {
  margin: 16px 0;
}
.event-list > label,
.event-list > div,
.event-list > .option {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  border: 1px solid var(--border);
  border-radius: 12px;
  background: #0f182a;
  transition: transform .12s ease, border-color .2s ease, background .2s ease;
  cursor: pointer;
}
.event-list > label:hover,
.event-list > div:hover,
.event-list > .option:hover {
  transform: translateY(-1px);
  border-color: rgba(124,58,237,.6);
  background: #111c30;
}

/* ===== Participant Box ===== */
.participant-box, .member, .panel {
  border: 1px solid var(--border);
  border-radius: 14px;
  background: #0e1729;
  padding: 16px;
  margin: 12px 0;
  box-shadow: var(--shadow);
}
.participant-box h4 { margin: 0 0 10px; font-weight: 600; }
.participant-box .row > label { flex: 1 1 240px; }

/* ===== Buttons ===== */
button, .btn {
  appearance: none;
  border: none;
  border-radius: 14px;
  padding: 12px 16px;
  font-weight: 600;
  cursor: pointer;
  background: linear-gradient(90deg, var(--accent), var(--accent-2));
  color: white;
  box-shadow: var(--shadow);
  transition: transform .12s ease, filter .15s ease, opacity .2s ease;
}
button:hover { transform: translateY(-1px); filter: brightness(1.05); }
button:active { transform: translateY(0); filter: brightness(.96); }
button[disabled] { opacity: .6; cursor: not-allowed; }

/* Secondary Button */
.btn-secondary {
  background: #15213a;
  color: var(--text);
  border: 1px solid var(--border);
}

/* ===== Info/Status ===== */
#amountInfo {
  margin-top: 12px;
  font-size: .95rem;
  opacity: .9;
}
.success { color: var(--success); }
.error { color: var(--danger); }

/* ===== Form Sections ===== */
.section {
  margin: 20px 0;
}
.section > .title {
  font-size: 1.1rem;
  margin-bottom: 8px;
  opacity: .9;
}

/* ===== Responsive ===== */
@media (max-width: 560px) {
  body { padding: 24px 12px; }
  .row { gap: 8px; }
  button, .btn { width: 100%; }
}
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
