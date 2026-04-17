<?php
require "includes/connect.php";

require "includes/header.php";

$errors = [];

$success = "";

// Check if the form was submitted using POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));

    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Server-side Validation

    if ($username === '') {
        $errors[] = "Username is required.";
    }

    if ($email === '') {
        $errors[] = "Email is required.";
    }

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email must be a valid email address.";
    }

    if ($password === '') {
        $errors[] = "Password is required.";
    }

    if ($confirmPassword === '') {
        $errors[] = "Please confirm your password.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }


    if (empty($errors)) {

        $sql = "SELECT id FROM users WHERE username = :username OR email = :email";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);

        $stmt->execute();

        if ($stmt->fetch()) {
            $errors[] = "That username or email is already in use.";
        }
    }

    // Insert the new user into the database

    if (empty($errors)) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password)
                VALUES (:username, :email, :password)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        $stmt->execute();

        $success = "Account created successfully. You can now log in.";
    }
}
?>

<main class="container mt-4">
    <h2>Register</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <h3>Please fix the following:</h3>
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success !== ""): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success); ?>
            <br>
            <a href="index.php" class="btn btn-sm btn-success mt-2">Go to Login</a>
        </div>
    <?php endif; ?>

    <!-- Registration form -->
    <form method="post" class="mt-3">

        <label for="username" class="form-label">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            class="form-control mb-3"
            value="<?= htmlspecialchars($username ?? ''); ?>"
            required
        >

        <label for="email" class="form-label">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            class="form-control mb-3"
            value="<?= htmlspecialchars($email ?? ''); ?>"
            required
        >

        <label for="password" class="form-label">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            class="form-control mb-3"
            required
        >

        <label for="confirm_password" class="form-label">Confirm Password</label>
        <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            class="form-control mb-4"
            required
        >

        <button type="submit" class="btn btn-primary">Create Account</button>

        <a href="login.php" class="btn btn-secondary">Back 2 Login</a>
    </form>
</main>


<?php
require "includes/footer.php";
?>
</body>
</html>