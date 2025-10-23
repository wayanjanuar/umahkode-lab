<?php
// file_view_vuln.php
require 'db.php';
session_start();
$owner = $_SESSION['user_id'] ?? 0;
$file_id = intval($_GET['id'] ?? 0);

$q = "SELECT id, owner_id, filename FROM files WHERE id=$file_id";
$r = $conn->query($q);
$row = $r->fetch_assoc();

if(!$row) { http_response_code(404); echo "Not found"; exit; }

// VULNERABILITY: no check that current user == owner -> IDOR
header('Content-Type: application/octet-stream');
readfile('/var/www/uploads/'.$row['filename']);
