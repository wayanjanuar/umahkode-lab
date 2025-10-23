<?php
// redirect_vuln.php
$next = $_GET['next'] ?? '/';
header("Location: $next");
exit;
