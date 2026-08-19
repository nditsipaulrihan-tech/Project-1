<?php
session_start();
include 'admin.php';
include 'connection.php';

// Handle Search Query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $safe_search = mysqli_real_escape_string($conn, $search);
    // Search posts matching title or description
    $sql = "SELECT * FROM post WHERE title LIKE '%{$safe_search}%' OR post_desc LIKE '%{$safe_search}%' ORDER BY id DESC;";
} else {
    // Default fetch all posts (newest first)
    $sql = "SELECT * FROM post ORDER BY id DESC;";
}

$post = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/post.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <title>Dash Board - Blog Posts</title>
</head>
<body>
  <i class="fa-solid fa-bars" id="bars"></i>
  <section>

    <header class="nav">
      <div class="logo">
        <img src="./img/logom.png" alt="Logo">
      </div>

      <span>Dash Board</span>
      <nav>
        <a href="#"><i class="fa-solid fa-newspaper"></i> Blog Post</a>
        <a href="./draft.php"><i class="fa-solid fa-file-pen"></i> Draft</a>
        <a href="./comments.php"><i class="fa-solid fa-comment-dots"></i> Comments</a>
        <a href="./createpost.php"><i class="fa-solid fa-pen-clip"></i> Create Post</a>
      </nav>

      <span>Settings & Security</span>
      <nav>
        <a href="./profile.php"><i class="fa-solid fa-user"></i> Profile</a>
        <a href="./logout.php"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
      </nav>
    </header>

    <main>
      <!-- SEARCH CONTROL BAR -->
      <div class="top-bar">
        <form action="" method="GET" class="search-form">
          <div class="search-box">
            <input type="text" name="search" placeholder="Search by title or content..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
            <?php if (!empty($search)): ?>
              <a href="blog_post.php" class="reset-btn"><i class="fa-solid fa-rotate-left"></i> Reset</a>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <!-- POSTS GRID -->
      <div class="posts-grid">
        <?php 
        if ($post && mysqli_num_rows($post) > 0) {
            foreach ($post as $p) { 
                // Ensure image path is properly targeted
                $img_path = (strpos($p['img_url'], 'uploads/') === 0) ? $p['img_url'] : 'uploads/' . $p['img_url'];
                
                echo "
                <div class='post'>
                  <div class='post-img'>
                    <img src='" . htmlspecialchars($img_path) . "' alt='Post Image' onerror=\"this.src='./img/logom.png'\">
                  </div>
                  <h3>" . htmlspecialchars($p['title']) . "</h3>
                  <span class='pph'>Published</span>
                  <div class='bloga'>
                    <div class='desc'>
                      <p>" . htmlspecialchars($p['post_desc']) . "</p>
                      <small><i class='fa-regular fa-clock'></i> " . htmlspecialchars($p['uploaded']) . "</small>
                    </div>
                    <div class='btn'>
                      <a href='post_preview.php?id={$p['id']}'><button type='button'>View</button></a>
                    </div>
                  </div>
                </div>";
            }
        } else {
            echo "<div class='no-results'><i class='fa-solid fa-magnifying-glass'></i> No posts found matching your search.</div>";
        }
        ?>
      </div>
    </main>

  </section>

  <footer>
    <small>&copy; 2026 - All rights reserved.</small>
  </footer>

  <script src="./js/main.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>