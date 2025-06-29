<?php
require './includes/autoLoader.php';
$conn = require './includes/db.php';

$paginator = new Paginator($_GET['page'] ?? 1, 3, Article::getTotal($conn, true));

$articles = Article::getPage($conn, $paginator->limit, $paginator->offset, true);


?>
<?php require "./includes/header.php"; ?>

<h1 class="mb-4">Latest Articles</h1>

<?php if(empty($articles)) : ?>
    <div class="alert alert-info">
        No articles found. Check back later for new content!
    </div>
<?php else : ?>
    <div class="row">
        <?php foreach ($articles as $article) : ?>
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="article-card">
                    <h2>
                        <a href="/article.php?id=<?= $article['id']; ?>"><?= htmlspecialchars($article["title"]); ?></a>
                    </h2>
                    
                    <div class="article-meta">
                        <time datetime="<?= $article['published_at']; ?>">
                            <?php
                            $dateTime = new DateTime($article['published_at']);
                            echo $dateTime->format("j F Y");
                            ?>
                        </time>
                    </div>

                    <?php if(!empty($article['category_names'])) : ?>
                        <div class="article-categories">
                            <?= htmlspecialchars(implode(", ", $article['category_names'])); ?>
                        </div>
                    <?php endif; ?>

                    <?php if($article['image_file']) : ?>
                        <img src="/uploads/<?= $article['image_file']; ?>" 
                             alt="Image for <?= $article['title']; ?>" 
                             class="article-image">
                    <?php endif; ?>

                    <p><?= htmlspecialchars(substr($article["content"], 0, 150)) . (strlen($article["content"]) > 150 ? '...' : ''); ?></p>
                    
                    <a href="/article.php?id=<?= $article['id']; ?>" class="btn btn-primary btn-sm">
                        Read More
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination-container">
        <?php require "./includes/pagination.php"; ?>
    </div>
<?php endif; ?>

<?php require "./includes/footer.php"; ?>