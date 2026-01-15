<?php include('includes/database.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Visitor System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    Hospital Visitor System
</header>

<div class="container">
    <h2>Welcome!</h2>
    <p>This is a simple system for managing hospital visitor records. 
       You can add, view, and manage visitors through the navigation below.</p>

    <h3>Navigation</h3>
    <div style="margin-top:20px;">
        <a href="visitor.php" class="btn-link">Manage Visitors</a>
    </div>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> Hospital Visitor System
</footer>

</body>
</html>
