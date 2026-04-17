<?php

require "includes/auth.php";

require "includes/connect.php";

require "includes/adminHeader.php";

$errors = [];

$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and sanitize form values
    $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS));

    $imagePath = null;

    if ($title === '') {
        $errors[] = "Title is required.";
    }

    if(isset($_FILES['user_image']) && $_FILES['user_image']['error'] !== UPLOAD_ERR_NO_FILE) {

        if($_FILES['user_image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "There was a problem uploading your file!";
        }
        else {
            //only allow a few file types
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

            //detect the real MIME type of file
            $detectedType = mime_content_type($_FILES['user_image']['tmp_name']);

            if(!in_array($detectedType, $allowedTypes, true)) {

                $errors[] = "Only JPG, PNG and WebP allowed!";

            }
            else {
                //build file name and move it to where we want it to go
                //get the file extension
                $extension = pathinfo($_FILES['user_image']['title'], PATHINFO_EXTENSION);

                //creat a unique filename so uploaded files don't overwrite
                $safeFilename = uniqid('user_', true) . '.' . strtolower($extension);

                //build the full server path where file will be stored
                $destination = __DIR__ . '/uploads/' . $safeFilename;

                if(move_uploaded_file($_FILES['user_image']['tmp_name'], $destination)) {

                    //save the relative path to the database
                    $imagePath = 'uploads/' . $safeFilename;

                }
                else {
                    $errors[] = "Image upload failed!";
                }
            }
        }

    }

    if (empty($errors)) {
        $sql = "INSERT INTO photos (title, image_path)
                VALUES (:title, :image_path)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':image_path', $imagePath);
        $stmt->execute();

        $success = "User photo added successfully!";
    }
}
?>

<main class="container mt-4">
    <h1>Add Photo</h1>

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
        </div>
    <?php endif; ?>
    <!--enctype="multipart/form-data" required for uploads, will not send properly if not included -->
    <form method="post" enctype="multipart/form-data" class="mt-3">
        <label for="title" class="form-label">Title</label>
        <input
            type="text"
            id="title"
            name="title"
            class="form-control mb-3"
            required
        >



        <label for="user_image" class="form-label">Photo</label>
        <input
            type="file"
            id="user_image"
            name="user_image"
            class="form-control mb-4"
            accept=".jpg,.jpeg,.png,.webp"
        >

        <button type="submit" class="btn btn-primary">Add Photo</button>
    </form>
</main>
</body>

<?php require "includes/footer.php"; ?>
</html>