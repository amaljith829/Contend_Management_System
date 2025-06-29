<?php
require './includes/autoLoader.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = require './includes/db.php';
    if (User::authenticate($conn, $_POST['username'], $_POST['password'])) {
        Auth::login();
    Url::redirect('/index.php');
    } else {
        $error = "Invalid username or password";
    }
}
?>
<?php require "./includes/header.php"; ?>

<div class="login-container">
    <div class="login-form">
        <h1>Login</h1>
        
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" 
                       class="form-control" 
                       id="username" 
                       name="username" 
                       placeholder="Enter your username" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" 
                       class="form-control" 
                       id="password" 
                       name="password" 
                       placeholder="Enter your password" 
                       required>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    Login
                </button>
            </div>
        </form>
        
        <div class="text-center mt-3">
            <a href="/index.php" class="text-decoration-none">
                Back to Homepage
            </a>
        </div>
    </div>
</div>

<?php require "./includes/footer.php"; ?>