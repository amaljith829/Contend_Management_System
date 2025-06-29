<?php
require "./includes/autoLoader.php";

$conn = require "./includes/db.php";



if (isset($_GET['id'])) {
    $article = Article::getWithCategories($conn, $_GET['id']);
} else {
    $article = null;
}


?>
<?php require "./includes/header.php"; ?>

<?php if ($article) : ?>
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Article</li>
        </ol>
    </nav>

    <div class="single-article">
        <h2><?= htmlspecialchars($article[0]['title']); ?></h2>
        
        <div class="article-meta">
            <time datetime="<?= $article[0]['published_at']; ?>">
                <?php
                $dateTime = new DateTime($article[0]['published_at']);
                echo $dateTime->format("j F Y");
                ?>
            </time>
        </div>

        <?php if($article[0]['category_name']) : ?>
            <div class="article-categories">
                Categories: 
                <?php foreach($article as $a) : ?>
                    <?= htmlspecialchars($a['category_name']); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($article[0]['image_file']) : ?>
            <img src="/uploads/<?= htmlspecialchars($article[0]['image_file']); ?>" 
                 alt="Image for <?= htmlspecialchars($article[0]['title']); ?>"
                 class="article-image">
        <?php endif; ?>
        
        <div class="article-content">
            <p><?= nl2br(htmlspecialchars($article[0]['content'])); ?></p>
        </div>
        
        <div class="text-center mt-4">
            <a href="/index.php" class="btn btn-primary">
                Back to Articles
            </a>
        </div>
    </div>
<?php else : ?>
    <div class="alert alert-warning">
        <strong>Article Not Found</strong><br>
        The article you're looking for doesn't exist or has been removed.
    </div>
    
    <div class="text-center">
        <a href="/index.php" class="btn btn-primary">
            Go to Homepage
        </a>
    </div>
<?php endif; ?>

<?php require "./includes/footer.php"; ?>