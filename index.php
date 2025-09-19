<?php require_once 'writer/classloader.php'; ?>
<!doctype html>
  <html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <link rel="stylesheet" href="styles/lesserafim.css">
    <style>
      body {
        font-family: "Arial";
      }
    </style>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark p-4" style="background-color: #355E3B;">
      <a class="navbar-brand" href="#">School Publication Homepage</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </nav>
    <div class="container-fluid">
      <div class="display-4 text-center">Hello there and welcome to the main homepage!</div>
      <div class="row">
        <div class="col-md-6">
          <div class="card shadow">
            <div class="card-body">
              <h1>Writer</h1>
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSm9MFmwWV5So4pMYHY5W4wZBo58z2IzfCc2w&s" class="img-fluid home-card-img">
              <p>Content writers create clear, engaging, and informative content that helps businesses communicate their services or products effectively, build brand authority, attract and retain customers, and drive web traffic and conversions.</p>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card shadow">
            <div class="card-body">
              <h1>Admin</h1>
              <img src="https://cdn.bhdw.net/im/le-sserafim-s-sakura-for-perfect-night-mv-shoot-wallpaper-123540_w635.webp" class="img-fluid home-card-img">
              <p>Admin writers play a key role in content team development. They are the highest-ranking editorial authority responsible for managing the entire editorial process, and aligning all published material with the publication’s overall vision and strategy. </p>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center mt-4">
        <a href="writer/login.php" class="btn btn-primary mr-2">Writer Login</a>
        <a href="admin/login.php" class="btn btn-secondary ml-2">Admin Login</a>
      </div>
      <div class="display-4 text-center mt-4">All articles are below!!</div>
      <div class="row justify-content-center">
        <div class="col-md-6">
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
              <small><strong><?php echo $article['username'] ?></strong> - <?php echo $article['created_at']; ?> </small>
              <?php if (!empty($article['image_path'])) { ?>
                <div class="mb-2">
                  <img src="<?php echo 'writer/' . htmlspecialchars($article['image_path']); ?>" alt="article image" style="max-width:100%;height:auto;" />
                </div>
              <?php } ?>
              <p><?php echo $article['content']; ?> </p>
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
      var YT_VIDEO_ID = 'rGD5U8u1Dk0'; // extracted from your YouTube URL

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