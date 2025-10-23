<?php
// view_log_vuln.php
$log = $_GET['log'] ?? 'app.log';
$path = __DIR__.'/../../storage/logs/'.$log;
if(file_exists($path)) {
  echo "<pre>".file_get_contents($path)."</pre>";
} else {
  echo "No log";
}
