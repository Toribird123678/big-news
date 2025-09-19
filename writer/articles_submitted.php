<?php require_once 'classloader.php'; ?>

<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
}

if ($userObj->isAdmin()) {
  header("Location: ../admin/index.php");
}  
?>
<!doctype html>
  <html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <link rel="stylesheet" href="../styles/lesserafim.css">
    <style>
      body {
        font-family: "Arial";
      }
    </style>
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <?php  
          if (isset($_SESSION['message']) && isset($_SESSION['status'])) {

            if ($_SESSION['status'] == "200") {
              echo "<div class='alert alert-success'>".$_SESSION['message']."</div>";
            }

            else {
              echo "<div class='alert alert-danger'>".$_SESSION['message']."</div>"; 
            }
          }
          unset($_SESSION['message']);
          unset($_SESSION['status']);
          ?>
          <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
              <input type="text" class="form-control mt-4" name="title" placeholder="Input title here">
            </div>
            <div class="form-group">
              <textarea name="description" class="form-control mt-4" placeholder="Submit an article!"></textarea>
            </div>
            <div class="form-group">
              <label for="category">Category (Optional)</label>
              <select class="form-control" name="category_id" id="category">
                <option value="">Select a category...</option>
                <?php 
                $categories = $categoryObj->getCategories();
                foreach ($categories as $category): ?>
                  <option value="<?php echo $category['category_id']; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Optional image</label>
              <input type="file" name="image" accept="image/*" class="form-control-file">
            </div>
            <input type="submit" class="btn btn-primary form-control float-right mt-4 mb-4" name="insertArticleBtn">
          </form>

          <div class="display-4">Double click to edit article</div>
          <div class="mt-4">
            <h4>Pending edit requests for your articles</h4>
            <?php $requests = $articleObj->getPendingEditRequestsForAuthor($_SESSION['user_id']); ?>
            <?php if (count($requests) == 0) { ?>
              <p class="text-muted">No pending requests.</p>
            <?php } else { ?>
              <?php foreach ($requests as $req) { ?>
                <div class="card mb-2 p-3">
                  <div>
                    <strong><?php echo $req['requester_username']; ?></strong> requested to edit: <em><?php echo $req['title']; ?></em>
                  </div>
                  <form action="core/handleForms.php" method="POST" class="mt-2">
                    <input type="hidden" name="request_id" value="<?php echo $req['request_id']; ?>">
                    <button class="btn btn-sm btn-success" name="approveEditRequestBtn" value="1">Approve</button>
                    <button class="btn btn-sm btn-outline-danger" name="rejectEditRequestBtn" value="1">Reject</button>
                  </form>
                </div>
              <?php } ?>
            <?php } ?>
          </div>
          <?php $articles = $articleObj->getArticlesByUserID($_SESSION['user_id']); ?>
          <?php foreach ($articles as $article) { ?>
          <div class="card mt-4 shadow articleCard">
            <div class="card-body">
              <h1><?php echo $article['title']; ?></h1> 
              <?php if (!empty($article['category_name'])) { ?>
                <p><small class="bg-info text-white p-1">  
                  Category: <?php echo htmlspecialchars($article['category_name']); ?>
                </small></p>
              <?php } ?>
              <small><?php echo $article['username'] ?> - <?php echo $article['created_at']; ?> </small>
              <?php if ($article['is_active'] == 0) { ?>
                <p class="text-danger">Status: PENDING</p>
              <?php } ?>
              <?php if ($article['is_active'] == 1) { ?>
                <p class="text-success">Status: ACTIVE</p>
              <?php } ?>
              <?php if (!empty($article['image_path'])) { ?>
                <div class="mb-2">
                  <img src="<?php echo htmlspecialchars($article['image_path']); ?>" alt="article image" style="max-width:100%;height:auto;" />
                </div>
              <?php } ?>
              <p><?php echo $article['content']; ?> </p>
              <form class="deleteArticleForm">
                <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>" class="article_id">
                <input type="submit" class="btn btn-danger float-right mb-4 deleteArticleBtn" value="Delete">
              </form>
              <div class="updateArticleForm d-none">
                <h4>Edit the article</h4>
                <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                  <div class="form-group mt-4">
                    <input type="text" class="form-control" name="title" value="<?php echo $article['title']; ?>">
                  </div>
                  <div class="form-group">
                    <textarea name="description" id="" class="form-control"><?php echo $article['content']; ?></textarea>
                  </div>
                  <div class="form-group">
                    <label for="edit_category_<?php echo $article['article_id']; ?>">Category (Optional)</label>
                    <select class="form-control" name="category_id" id="edit_category_<?php echo $article['article_id']; ?>">
                      <option value="">Select a category...</option>
                      <?php 
                      $categories = $categoryObj->getCategories();
                      foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>" 
                                <?php echo ($article['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="mt-2">
                    <label>Replace image (optional)</label>
                    <input type="file" name="image" accept="image/*" class="form-control-file">
                  </div>
                  <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                  <input type="submit" class="btn btn-primary float-right mt-4" name="editArticleBtn">
                </form>
              </div>
            </div>
          </div>  
          <?php } ?> 
        </div>
      </div>
    </div>
    <script>
      $('.articleCard').on('dblclick', function (event) {
        var updateArticleForm = $(this).find('.updateArticleForm');
        updateArticleForm.toggleClass('d-none');
      });

      $('.deleteArticleForm').on('submit', function (event) {
        event.preventDefault();
        var formData = {
          article_id: $(this).find('.article_id').val(),
          deleteArticleBtn: 1
        }
        if (confirm("Are you sure you want to delete this article?")) {
          $.ajax({
            type:"POST",
            url: "core/handleForms.php",
            data:formData,
            success: function (data) {
              if (data) {
                location.reload();
              }
              else{
                alert("Deletion failed");
              }
            }
          })
        }
      })
    </script>
  </body>
</html>