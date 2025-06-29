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

    try {
        // Check for upload errors first
        switch($_FILES['file']['error']) {
            case UPLOAD_ERR_OK: 
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('Missing file');
            case UPLOAD_ERR_INI_SIZE:
                throw new Exception('File is too large');
            default:
                throw new Exception('An error occurred');
        }

        // Validate file type
        $mime_types = ['image/gif', 'image/png', 'image/jpeg'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['file']['tmp_name']);
        finfo_close($finfo);
        
        if(!in_array($mime_type, $mime_types)) {
            throw new Exception('Invalid file type. Only GIF, PNG, and JPEG are allowed.');
        }

        // Process filename
        $pathinfo = pathinfo($_FILES['file']['name']);
        $base = $pathinfo['filename'];
        $base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);
        $base = mb_substr($base, 0, 200);
        
        // Handle duplicate filenames
        $extension = strtolower($pathinfo['extension']);
        $filename = $base . '.' . $extension;
        $destination = "../uploads/$filename";
        
        $counter = 1;
        while (file_exists($destination)) {
            $filename = $base . '_' . $counter . '.' . $extension;
            $destination = "../uploads/$filename";
            $counter++;
        }

        // Ensure uploads directory exists
        if (!is_dir("../uploads")) {
            mkdir("../uploads", 0755, true);
        }

        // Move uploaded file
        if(move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {

            $previous_image = $article->image_file;

           if($article->setImageFile($conn, $filename)) {
            if($previous_image) {
                unlink("../uploads/$previous_image");
            }
            Url::redirect("/admin/edit_article_image.php?id={$article->id}");
           }
        } else {
            throw new Exception('Failed to move uploaded file');
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<?php require '../includes/header.php'; ?>
<h2>Edit Image</h2>

<?php if ($article->image_file) : ?>
                    <img src="/uploads/<?= ($article->image_file); ?>" alt="Image for <?= ($article->title); ?>">
                    <a class="delete" href="/admin/delete_article_image.php?id=<?= ($article->id); ?>">Delete</a>
                <?php endif; ?>

<?php if (isset($error)) : ?>
    <p><?= $error; ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <div>
        <label for="file">Image file</label>
        <input type="file" name="file" id="file">
    </div>
    <button>Upload</button>
</form>

<?php require '../includes/footer.php'; ?>