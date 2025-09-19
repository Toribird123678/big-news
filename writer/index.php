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
      <div class="display-4 text-center">Hello there and welcome! <span class="text-success"><?php echo $_SESSION['username']; ?></span>. Here are all the articles</div>
      <div class="row justify-content-center">
        <div class="col-md-6">
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
          <?php $articles = $articleObj->getActiveArticles(); ?>
          <?php foreach ($articles as $article) { ?>
          <div class="card mt-4 shadow">
            <div class="card-body">
              <h1><?php echo $article['title']; ?></h1> 
              <?php if ($article['is_admin'] == 1) { ?>
                <p><small class="bg-primary text-white p-1">  
                  Message From Admin
                </small></p>
              <?php } ?>
              <?php if (!empty($article['category_name'])) { ?>
                <p><small class="bg-info text-white p-1">  
                  Category: <?php echo htmlspecialchars($article['category_name']); ?>
                </small></p>
              <?php } ?>
              <small><strong><?php echo $article['username'] ?></strong> - <?php echo $article['created_at']; ?> </small>
              <?php if (!empty($article['image_path'])) { ?>
                <div class="mb-2">
                  <img src="<?php echo htmlspecialchars($article['image_path']); ?>" alt="article image" style="max-width:100%;height:auto;" />
                </div>
              <?php } ?>
              <p><?php echo $article['content']; ?> </p>
              <?php if ((int)$article['author_id'] !== (int)$_SESSION['user_id']) { ?>
              <form action="core/handleForms.php" method="POST" class="mt-2">
                <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                <input type="submit" class="btn btn-outline-primary btn-sm" name="requestEditBtn" value="Request to Edit">
              </form>
              <?php } ?>
            </div>
          </div>  
          <?php } ?> 
        </div>
      </div>
    </div>
    <!-- Tiny YouTube background player (click to start due to autoplay policy) -->
    <div id="yt-bg-player" style="width:1px;height:1px;overflow:hidden;position:fixed;bottom:0;right:0;"></div>
    <button id="bgm-toggle" class="btn btn-sm btn-outline-primary" style="position:fixed;bottom:16px;left:16px;z-index:9999;">Play music</button>
    <script src="https://www.youtube.com/iframe_api"></script>
    <script>
      var ytPlayer;
      var YT_VIDEO_ID = '3geiZxnv1Y4';

      function onYouTubeIframeAPIReady() {
        ytPlayer = new YT.Player('yt-bg-player', {
          height: '1',
          width: '1',
          videoId: YT_VIDEO_ID,
          playerVars: {
            autoplay: 0,
            loop: 1,
            playlist: YT_VIDEO_ID,
            controls: 0,
            modestbranding: 1
          }
        });
      }

      var btn = document.getElementById('bgm-toggle');
      btn.addEventListener('click', function () {
        if (!ytPlayer || !ytPlayer.playVideo) return;
        if (btn.dataset.playing === '1') {
          ytPlayer.pauseVideo();
          btn.dataset.playing = '0';
          btn.textContent = 'Play music';
        } else {
          ytPlayer.setVolume(20);
          ytPlayer.playVideo();
          btn.dataset.playing = '1';
          btn.textContent = 'Pause music';
        }
      });
    </script>
  </body>
</html>