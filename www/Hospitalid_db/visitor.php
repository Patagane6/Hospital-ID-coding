<?php require_once __DIR__ . '/Includes/database.php'; ?>

<?php

$add_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_visitor'])) {
    $full_name = $conn->real_escape_string(trim($_POST['full_name'] ?? ''));

    // sanitize contact number: keep digits only and validate
    $contact_number_raw = $_POST['contact_number'] ?? '';
    $contact_number_digits = preg_replace('/\D+/', '', $contact_number_raw);
    if ($contact_number_digits === '') {
        $add_error = 'Contact number must contain digits only.';
    }
    $contact_number = $conn->real_escape_string($contact_number_digits);

    // allowed ID types
    $allowed_ids = [
        'Passport',
        "Driver's License",
        'Voter ID',
        'SSS/GSIS ID',
        'PhilHealth ID',
        'Senior Citizen ID',
        'Student ID',
        'Other'
    ];
    $valid_id_raw = $_POST['valid_id'] ?? '';
    if (!in_array($valid_id_raw, $allowed_ids, true)) {
        $add_error = $add_error ?: 'Invalid ID type selected.';
    }
    $valid_id = $conn->real_escape_string($valid_id_raw);

    $number_of_visitors = intval($_POST['number_of_visitors'] ?? 0);

    if ($add_error === '') {
        $sql = "INSERT INTO visitor (full_name, contact_number, valid_id, number_of_visitors) 
                VALUES ('$full_name', '$contact_number', '$valid_id', $number_of_visitors)";
        if ($conn->query($sql) === TRUE) {
            header('Location: visitor.php?added=1');
            exit;
        } else {
            $add_error = $conn->error;
        }
    }
}

// Handle delete action early so we can redirect before HTML is sent
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM visitor WHERE visitor_id = $id");
    header('Location: visitor.php?deleted=1');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visitors</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    Visitor Records
</header>

<div class="container">
    <h2>Add Visitor</h2>
    <form method="POST" action="">
        <label for="full_name">Full Name</label>
        <input type="text" name="full_name" id="full_name" placeholder="Please Input Full Name" required>

               <label for="contact_number">Contact Number</label>
        <input type="text" name="contact_number" id="contact_number" placeholder="Please Input Contact Number" inputmode="numeric" pattern="\d+" required>

        <label for="valid_id">Type of ID</label>
        <select name="valid_id" id="valid_id" required>
            <option value="">-- Select ID Type --</option>
            <option>Passport</option>
            <option>Driver's License</option>
            <option>Voter ID</option>
            <option>SSS/GSIS ID</option>
            <option>PhilHealth ID</option>
            <option>Senior Citizen ID</option>
            <option>Student ID</option>
            <option>Other</option>
        </select>

        <label for="number_of_visitors">Number of Visitors</label>
        <input type="number" name="number_of_visitors" id="number_of_visitors" min="1" placeholder="Please Input Number of Visitors" required>

        <button type="submit" name="add_visitor">Add Visitor</button>
    </form>

     <?php
    // show flash messages (replace the old inline insert/echo block here)
    if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
        echo "<div id='flash-msg' style='color:red;'>Visitor record deleted.</div>";
    }
    if (isset($_GET['added']) && $_GET['added'] == '1') {
        echo "<div id='flash-msg' style='color:green;'>New visitor added successfully!</div>";
    }
    if (!empty($add_error)) {
        echo "<div id='flash-msg' style='color:red;'>Error adding visitor: " . htmlspecialchars($add_error) . "</div>";
    }
    ?>

    <script>
    // hide the message after 3 seconds and remove query string to avoid repeat
    setTimeout(function(){
        var el = document.getElementById('flash-msg');
        if (el) {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(function(){ el.remove(); }, 500);
        }
        if (window.history && window.history.replaceState) {
            var url = window.location.protocol + '//' + window.location.host + window.location.pathname;
            window.history.replaceState({}, document.title, url);
        }
    }, 3000);
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var cn = document.getElementById('contact_number');
        if (cn) {
            // remove non-digits on input (handles typing and paste)
            cn.addEventListener('input', function(){ this.value = this.value.replace(/\D/g,''); });

            // optionally block most non-numeric key presses while allowing navigation/editing
            cn.addEventListener('keydown', function(e){
                var allowed = [8,9,13,27,37,38,39,40,46]; // backspace, tab, enter, esc, arrows, delete
                if (allowed.indexOf(e.keyCode) !== -1) return;
                if (e.ctrlKey || e.metaKey) return; // allow Ctrl/Cmd shortcuts
                // digits (top row) and numpad
                if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105)) return;
                e.preventDefault();
            });
        }
    });
    </script>

    <h2>Visitor List</h2> 

    <?php
    // Query all visitors
    $sql = "SELECT * FROM visitor";
    $result = $conn->query($sql);

      if ($result && $result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>Visitor ID</th>
                <th>Full Name</th>
                <th>Contact Number</th>
                <th>Valid ID</th>
                <th>Number of Visitors</th>
                <th>Action</th>
              </tr>";
        while ($row = $result->fetch_assoc()) {
            $vid = htmlspecialchars($row['visitor_id']);
            $vname = htmlspecialchars($row['full_name']);
            $vcontact = htmlspecialchars($row['contact_number']);
            $valid = htmlspecialchars($row['valid_id']);
            $num_visitors = htmlspecialchars($row['number_of_visitors']);
            $delete_id = urlencode($row['visitor_id']);
            echo "<tr>
                    <td>{$vid}</td>
                    <td>{$vname}</td>
                    <td>{$vcontact}</td>
                    <td>{$valid}</td>
                    <td>{$num_visitors}</td>
                    <td><a href='visitor.php?delete={$delete_id}' onclick=\"return confirm('Are you sure you want to delete this visitor?');\">Delete</a></td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No records found.</p>";
    }

    $conn->close();
    ?>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> Hospital Visitor System
</footer>

</body>
</html>
