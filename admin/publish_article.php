<?php
require "../includes/autoLoader.php";

Auth::requireLogin();

$conn = require "../includes/db.php";

// Use POST, not GET
if (!isset($_POST['id'])) {
    echo "No ID provided";
    exit;
}

$article = Article::getById($conn, $_POST['id']);

if (!$article) {
    echo "Article not found";
    exit;
}

$published_at = $article->publish($conn);

// Format the date/time as in your admin/index.php
$dateTime = new DateTime($published_at);
echo $dateTime->format("j F Y, g:i a");