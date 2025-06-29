<?php
require "../includes/autoLoader.php";

$conn = require "../includes/db.php";

if (isset($_GET['id'])) {
    $article = Article::getWithCategories($conn, $_GET['id'], true);
} else {
    $article = null;
}

?>
<?php require "../includes/header.php"; ?>
<?php if ($article) : ?>
    <article>
        <h2><?= htmlspecialchars($article[0]['title'] ?? ''); ?></h2>
        <?php if (!empty($article[0]['category_name'])) : ?>
            <p>Categories:
                <?php foreach ($article as $a) : ?>
                    <?= htmlspecialchars($a['category_name'] ?? ''); ?>
                <?php endforeach; ?>
            </p>
        <?php endif; ?>
        <?php if (!empty($article[0]['image_file'])) : ?>
            <img src="/uploads/<?= htmlspecialchars($article[0]['image_file'] ?? ''); ?>" alt="Image for <?= htmlspecialchars($article[0]['title'] ?? ''); ?>">
        <?php endif; ?>
        <p><?= htmlspecialchars($article[0]['content'] ?? ''); ?></p>

        <a href="/admin/edit_article.php?id=<?= htmlspecialchars($article[0]['id'] ?? ''); ?>" style="background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;">Edit Article</a>
        <a class="delete" href="/admin/delete_article.php?id=<?= htmlspecialchars($article[0]['id'] ?? ''); ?>" style="background: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;">Delete Article</a>
        <a href="/admin/edit_article_image.php?id=<?= htmlspecialchars($article[0]['id'] ?? ''); ?>" style="background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;">Edit Image</a>
    </article>
<?php else : ?>
    <p>No articles found</p>
<?php endif; ?>

<?php require "../includes/footer.php"; ?>