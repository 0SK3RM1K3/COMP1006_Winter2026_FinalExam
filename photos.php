<?php
require "includes/auth.php";
require "includes/connect.php";
require "includes/adminHeader.php";

$sql = "SELECT * FROM photos ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$photos = $stmt->fetchALL(PDO::FETCH_ASSOC);

?>

<main class="container mt-4">
    <h1 class="mb-4">Our Photos</h1>
    <?php if (empty($photos)): ?>
        <p>No photos available yet.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($photos as $photo): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($photo['image_path'])): ?>
                            <img
                                src="<?= htmlspecialchars($photo['image_path']); ?>"
                                class="card-img-top"
                                alt="<?= htmlspecialchars($photo['title']); ?>">
                        <?php endif; ?>

                        <div class="card-body">
                            <h2 class="h5 card-title">
                                <?= htmlspecialchars($photo['title']); ?>
                            </h2>
                            
                            <a
                            class="btn btn-sm btn-danger mt-2"
                            href="delete.php?id=<?= urlencode($photo['id']); ?>"
                            onclick="return confirm('Are you sure you want to delete this photo?');">
                            Delete
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
</body>
<?php require "includes/footer.php"; ?>
</html>