<?php
// shared_articles.php: Display articles with accepted edit requests
require_once 'admin/classes/Database.php';
$db = new Database();
// Get articles with at least one accepted edit request, showing original author and requester
$sql = "SELECT a.*, 
               author.username AS author_name, 
               requester.username AS requester_name
        FROM articles a
        JOIN edit_requests er ON a.article_id = er.article_id
        JOIN school_publication_users author ON a.author_id = author.user_id
        JOIN school_publication_users requester ON er.requester_id = requester.user_id
        WHERE er.status = 'accepted'";
$shared_articles = $db->executeQuery($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shared Articles</title>
    <link rel="stylesheet" href="styles/lesserafim.css">
</head>
<body>
    <h1>Shared Articles</h1>
    <?php foreach ($shared_articles as $article): ?>
        <div class="article-card">
            <h2><?= htmlspecialchars($article['title']) ?></h2>
            <?php if (!empty($article['image_path'])): ?>
                <img src="<?= 'writer/' . htmlspecialchars($article['image_path']) ?>" alt="Article Image" style="max-width:200px;">
            <?php else: ?>
                <img src="images/default.png" alt="Default Image" style="max-width:200px;">
            <?php endif; ?>
            <p><?= nl2br(htmlspecialchars($article['content'])) ?></p>
            <p><strong>Author:</strong> <?= htmlspecialchars($article['author_name']) ?></p>
            <p><strong>Shared by:</strong> <?= htmlspecialchars($article['requester_name']) ?></p>
        </div>
    <?php endforeach; ?>
</body>
</html>
