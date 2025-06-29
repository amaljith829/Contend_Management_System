<?php
require '../includes/autoLoader.php';

Auth::requireLogin();

$conn = require '../includes/db.php';

if (isset($_GET['id'])) {
    
    $article = Article::getById($conn, $_GET['id']);
    
    if (!$article) {
        die("Article not found");
    }
    
} else {
    die("id not set");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($article->delete($conn)) {
        Url::redirect("/admin/index.php");
    } else {
        $error = "Failed to delete article.";
    }
}
?>

<?php require '../includes/header.php'; ?>


<?php if ($article): ?>
    <h2>Delete Article</h2>
    <p>Are you sure you want to delete the article titled "<strong><?php echo htmlspecialchars($article->title); ?></strong>"?</p>
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post">
        <button type="submit" style="background: #dc3545; color: white; padding: 5px 10px; border: none; border-radius: 3px;">Delete</button>
    </form>
<?php else: ?>
    <p>Article not found</p>
<?php endif; ?>

<?php require '../includes/footer.php'; ?>
