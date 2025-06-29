<?php

$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn = $db->getConn();
return $conn;

?>