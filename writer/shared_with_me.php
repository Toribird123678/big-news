<?php require_once 'classloader.php'; ?>

<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
}

if ($userObj->isAdmin()) {
  header("Location: ../admin/index.php");
}

// Fetch articles that have accepted edit requests for this user
$sharedArticles = $articleObj->getArticlesSharedWithUser($_SESSION['user_id']);
?>
<!doctype html>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <link rel="stylesheet" href="../styles/lesserafim.css">
    <style>
      body { font-family: "Arial"; }
    </style>
    <title>Shared With Me</title>
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <h2 class="mt-4">Articles shared with you</h2>

          <?php  
          if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
            if ($_SESSION['status'] == "200") {
              echo "<div class='alert alert-success'>".$_SESSION['message']."</div>";
            } else {
              echo "<div class='alert alert-danger'>".$_SESSION['message']."</div>"; 
            }
          }
          unset($_SESSION['message']);
          unset($_SESSION['status']);
          ?>

          <?php if (count($sharedArticles) === 0) { ?>
            <p class="text-muted mt-3">No shared articles yet.</p>
          <?php } ?>

          <?php foreach ($sharedArticles as $article) { ?>
            <div class="card mt-4 shadow">
              <div class="card-body">
                <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                <small><strong>Author: <?php echo htmlspecialchars($article['author_username']); ?></strong> - <?php echo $article['created_at']; ?></small>
                <?php if (!empty($article['image_path'])) { ?>
                  <div class="my-2">
                    <img src="<?php echo htmlspecialchars($article['image_path']); ?>" alt="article image" style="max-width:100%;height:auto;" />
                  </div>
                <?php } ?>
                <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>

                <div class="updateArticleForm mt-3">
                  <h5>Edit this article</h5>
                  <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                      <label>Title</label>
                      <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($article['title']); ?>">
                    </div>
                    <div class="form-group">
                      <label>Content</label>
                      <textarea name="description" class="form-control" rows="5"><?php echo htmlspecialchars($article['content']); ?></textarea>
                    </div>
                    <div class="form-group">
                      <label>Replace image (optional)</label>
                      <input type="file" name="image" accept="image/*" class="form-control-file">
                    </div>
                    <input type="hidden" name="article_id" value="<?php echo (int)$article['article_id']; ?>">
                    <input type="submit" class="btn btn-primary" name="editArticleBtn" value="Save Changes">
                  </form>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </body>
  </html>


