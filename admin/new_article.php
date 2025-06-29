<?php
require '../includes/autoLoader.php';

Auth::requireLogin();

    
$article = new Article();
    $category_ids = [];
    $conn = require '../includes/db.php';

$categories = Category::getAll($conn);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $article = new Article();
  $article->title = $_POST['title'];
  $article->content = $_POST['content'];
  $article->published_at = $_POST['published_at'];

    $category_ids = $_POST['category'] ?? [];

     if ($article->create($conn)) {
        $article->setCategories($conn, $category_ids);
      Url::redirect("/admin/index.php");
     } 
  }
?>

<?php require '../includes/header.php'; ?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/admin/index.php">Admin</a></li>
        <li class="breadcrumb-item active" aria-current="page">New Article</li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Create New Article</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($article->errors)): ?>
                    <div class="alert alert-danger">
                        <h6 class="alert-heading">Please fix the following errors:</h6>
                        <ul class="mb-0">
                            <?php foreach ($article->errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="form-group">
                        <label for="title">Article Title</label>
                        <input type="text" 
                               class="form-control" 
                               name="title" 
                               id="title" 
                               placeholder="Enter article title" 
                               value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="content">Article Content</label>
                        <textarea class="form-control" 
                                  name="content" 
                                  id="content" 
                                  rows="10"
                                  placeholder="Write your article content here..." 
                                  required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="published_at">Publish Date</label>
                        <input type="datetime-local" 
                               class="form-control" 
                               name="published_at" 
                               id="published_at" 
                               value="<?php echo isset($_POST['published_at']) ? htmlspecialchars($_POST['published_at']) : ''; ?>">
                        <small class="form-text text-muted">
                            Leave empty to save as draft, or set a future date to schedule publication.
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Categories</label>
                        <div class="row">
                            <?php foreach ($categories as $category): ?>
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="category[]" 
                                               value="<?php echo $category['id']; ?>" 
                                               id="category_<?php echo $category['id']; ?>"
                                               <?php echo in_array($category['id'], $category_ids) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="category_<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/admin/index.php" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Create Article
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>