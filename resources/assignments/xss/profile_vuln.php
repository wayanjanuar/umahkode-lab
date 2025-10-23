<?php
// profile_vuln.php
require 'db.php';
$id = $_GET['id'] ?? '1';
$q = "SELECT username, bio FROM users WHERE id=".(int)$id;
$r = $conn->query($q);
$row = $r->fetch_assoc();
?>
<!doctype html><html><body>
<h1>Profile: <?php echo $row['username']; ?></h1>
<p>Bio: <?php echo $row['bio']; ?></p> <!-- vulnerable to stored XSS if bio contains JS -->
</body></html>
