<?php if (!empty($article->errors)): ?>
    <ul>
        <?php foreach ($article->errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h1>Edit Article</h1>

<form method="post" id="form"> 
    <div>
        <label for="title"> Article Title</label>
        <input type="text" name="title" id="title" placeholder="Article Title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : (isset($article->title) ? htmlspecialchars($article->title) : ''); ?>">
    </div><br><br>

    <div>
        <label for="content"> Article Content</label>
        <textarea name="content" id="content" placeholder="Article Content"><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : (isset($article->content) ? htmlspecialchars($article->content) : ''); ?></textarea>
    </div><br><br>

    <div>
        <label for="published_at"> Published At</label>
        <input type="date" name="published_at" id="published_at" value="<?php echo isset($_POST['published_at']) ? htmlspecialchars($_POST['published_at']) : (isset($article->published_at) ? htmlspecialchars($article->published_at) : ''); ?>">
        <small>Leave blank to keep the article unpublished. If you set a future date, the article will remain unpublished until that date.</small>
    </div><br><br>

<fieldset>
    <legend>Categories</legend>
    <?php foreach ($categories as $category) : ?>
        <div>
            <input type="checkbox" name="category[]" value="<?= $category['id']; ?>" id="category<?= $category['id']; ?>"
            <?php if (in_array($category['id'], $category_ids)) : ?>
                checked
            <?php endif; ?>
            <label for="category<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></label>
        </div>
    <?php endforeach; ?>
</fieldset>

    <button type="submit">Update</button>
</form>