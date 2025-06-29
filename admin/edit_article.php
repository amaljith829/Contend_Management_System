<?php
require '../includes/autoLoader.php';


$conn = require '../includes/db.php';


if (isset($_GET['id'])) {
    
    $article = Article::getById($conn, $_GET['id']);
    
    if (!$article) {
        die("Article not found");
    }
    
} else {
    die("id not set");
}

$category_ids = array_column($article->getCategories($conn, $article->id), 'id');
$categories = Category::getAll($conn);


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $article->title = $_POST['title'];
    $article->content = $_POST['content'];
    $article->published_at = $_POST['published_at'];
    $category_ids = $_POST['category'] ?? [];
    
       if ($article->update($conn)) {
        $article->setCategories($conn, $category_ids);
        Url::redirect("/admin/article.php?id={$article->id}");
       } 
    }
?>

<?php require '../includes/header.php'; ?>

<?php if($article) : ?>
    <?php require '../admin/includes/article_form.php'; ?>
<?php else : ?>
    <p>Article not found</p>
<?php endif; ?>

<?php require '../includes/footer.php'; ?>