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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    

            $previous_image = $article->image_file;

           if($article->setImageFile($conn, null)) {
            if($previous_image) {
                unlink("../uploads/$previous_image");
            }
            Url::redirect("/admin/edit_article_image.php?id={$article->id}");
           }
        }
?>

<?php require '../includes/header.php'; ?>
<h2>Delete Image</h2>

<?php if ($article->image_file) : ?>
                    <img src="/uploads/<?= ($article->image_file); ?>" alt="Image for <?= ($article->title); ?>">
                <?php endif; ?>

<form method="post">
    <p>Are you sure?</p>
    <button>Delete</button>
    <a href="/admin/edit_article_image.php?id=<?= ($article->id); ?>">Cancel</a>
</form>

<?php require '../includes/footer.php'; ?>