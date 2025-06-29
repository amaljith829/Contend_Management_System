<?php

require '../includes/autoLoader.php';

Auth::requireLogin();
$conn = require '../includes/db.php';

$paginator = new Paginator($_GET['page'] ?? 1, 3, Article::getTotal($conn, false));

$articles = Article::getPage($conn, $paginator->limit, $paginator->offset, false);

?>
<?php require "../includes/header.php"; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Admin Dashboard</h2>
    <a href="/admin/new_article.php" class="btn btn-success">
        Add New Article
    </a>
</div>

<?php if(empty($articles)) : ?>
    <div class="alert alert-info">
        No articles found. Create your first article to get started!
    </div>
<?php else : ?>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Article Management</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Published Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article) : ?>
                            <tr>
                                <td>
                                    <a href="/admin/article.php?id=<?= $article['id']; ?>" 
                                       class="text-decoration-none">
                                        <?= htmlspecialchars($article["title"]); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if (empty($article['published_at'])): ?>
                                        <span class="badge bg-danger">Unpublished</span>
                                    <?php else: ?>
                                        <?php 
                                        $dateTime = new DateTime($article['published_at']);
                                        $now = new DateTime();
                                        if ($dateTime > $now): ?>
                                            <span class="badge bg-warning text-dark">Scheduled</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Published</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($article['published_at'])): ?>
                                        <?php 
                                        $dateTime = new DateTime($article['published_at']);
                                        echo $dateTime->format("j F Y, g:i a");
                                        ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not published</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/admin/article.php?id=<?= $article['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                        <a href="/admin/edit_article.php?id=<?= $article['id']; ?>" 
                                           class="btn btn-sm btn-outline-warning">
                                            Edit
                                        </a>
                                        <?php if (empty($article['published_at'])): ?>
                                            <button class="btn btn-sm btn-outline-success publish" 
                                                    data-id="<?= $article['id']; ?>">
                                                Publish
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <?php require "../includes/pagination.php"; ?>
    </div>
<?php endif; ?>

<?php require "../includes/footer.php"; ?>