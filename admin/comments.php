<?php
session_start();
include 'admin.php';
include 'connection.php';

$user = $comment = $id = '';

// Handle Delete Request
if (isset($_POST['yes'])) {
    $id = intval($_POST['id']);
    $abc = "DELETE FROM comments WHERE id = {$id}";
    
    if (mysqli_query($conn, $abc)) {
        // Redirect keeping current sorting selection active
        $sort_param = isset($_GET['sort']) ? "?sort=" . urlencode($_GET['sort']) : "";
        header("Location: " . $_SERVER['PHP_SELF'] . $sort_param);
        exit();
    }
}

// ==========================================
// DYNAMIC SORTING LOGIC (SQL WHITELIST SHIELD)
// ==========================================
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Allowed SQL mapping options
$sort_options = [
    'newest' => 'ORDER BY id DESC',
    'oldest' => 'ORDER BY id ASC',
    'post'   => 'ORDER BY post_id ASC, id DESC',
    'author' => 'ORDER BY username ASC'
];

// Determine SQL ORDER BY clause safely
$order_clause = isset($sort_options[$sort]) ? $sort_options[$sort] : $sort_options['newest'];

// Fetch comments with dynamic sorting
$sql = "SELECT * FROM comments {$order_clause};";
$com = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/com.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <title>Dash Board - Comments</title>
</head>
<body>
  <i class="fa-solid fa-bars" id="bars"></i>
  <section>

    <header class="nav">
      <div class="logo">
        <img src="./img/logom.png" alt="">
      </div>

      <span>Dash Board</span>
      <nav>
        <a href="./blog_post.php"><i class="fa-solid fa-newspaper"></i> Blog Post</a>
        <a href="./draft.php"><i class="fa-solid fa-file-pen"></i> Draft</a>
        <a href="#"><i class="fa-solid fa-comment-dots"></i> Comments</a>
        <a href="./createpost.php"><i class="fa-solid fa-pen-clip"></i> Create Post</a>
      </nav>

      <span>Settings & Security</span>
      <nav>
        <a href="./profile.php"><i class="fa-solid fa-user"></i> Profile</a>
        <a href="./logout.php"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
      </nav>
    </header>

    <main>
      <!-- DYNAMIC SORTING CONTROL BAR -->
      <div class="sort-bar">
        <form method="GET" action="" id="sortForm">
          <label for="sortSelect"><b><i class="fa-solid fa-sort"></i> Sort Comments:</b></label>
          <select name="sort" id="sortSelect" onchange="document.getElementById('sortForm').submit();">
            <option value="newest" <?php if ($sort === 'newest') echo 'selected'; ?>>Newest First</option>
            <option value="oldest" <?php if ($sort === 'oldest') echo 'selected'; ?>>Oldest First</option>
            <option value="post"   <?php if ($sort === 'post') echo 'selected'; ?>>Group by Post ID</option>
            <option value="author" <?php if ($sort === 'author') echo 'selected'; ?>>Author Name (A-Z)</option>
          </select>
        </form>
      </div>

      <?php 
      if ($com && mysqli_num_rows($com) > 0) {
          foreach ($com as $c) { 
              echo "<div class='com'>
                      <div>
                        <small>post_number {$c['post_id']}</small>
                      </div>
                      <div class='info'>
                        <h4>" . htmlspecialchars($c['username']) . "</h4>
                        <p>" . htmlspecialchars($c['p_com']) . "</p>
                      </div>
                      <div class='post'>
                        <button type='button' class='btn active'>Delete</button>
                        <div class='del'>
                          <button type='button' class='nope'>Cancel</button>
                          <form action='' method='post'>
                            <input type='hidden' name='id' value='{$c['id']}'>
                            <button name='yes' class='yep'>Yes</button>
                          </form>
                        </div>
                      </div>
                    </div>";
          }
      } else {
          echo "<p style='background: white; padding: 15px; border-radius: 0.6rem; text-align: center;'>No comments found.</p>";
      }
      ?> 
    </main>

  </section>

  <footer>
    <small>&copy; 2026 - All rights reserved.</small>
  </footer>

  <script src="./js/main.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Loop through each comment card (.com) individually
      document.querySelectorAll('.com').forEach(commentCard => {
        const delBtn = commentCard.querySelector('.btn');
        const delBox = commentCard.querySelector('.del');
        const cancelBtn = commentCard.querySelector('.nope');

        if (delBtn && delBox && cancelBtn) {
          delBtn.addEventListener('click', () => {
            delBox.classList.toggle('active');
            delBtn.classList.toggle('active');
          });

          cancelBtn.addEventListener('click', () => {
            delBox.classList.toggle('active');
            delBtn.classList.toggle('active');
          });
        }
      });
    });
  </script>
</body>
</html>
<?php mysqli_close($conn); ?>