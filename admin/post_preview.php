<?php
session_start();
include 'connection.php';

// Check if post ID is provided
if (empty($_GET['id'])) {
    header("Location: blog_post.php");
    exit();
}

$post_id = intval($_GET['id']);

// HANDLE DELETE POST REQUEST
if (isset($_POST['confirm_delete'])) {
    $del_id = intval($_POST['post_id']);

    // 1. Fetch image filename to delete physical file from server
    $stmt_img = mysqli_prepare($conn, "SELECT img_url FROM post WHERE id = ?");
    mysqli_stmt_bind_param($stmt_img, "i", $del_id);
    mysqli_stmt_execute($stmt_img);
    $res_img = mysqli_stmt_get_result($stmt_img);

    if ($row_img = mysqli_fetch_assoc($res_img)) {
        $img_file = 'uploads/' . $row_img['img_url'];
        if (file_exists($img_file) && !empty($row_img['img_url'])) {
            @unlink($img_file);
        }
    }
    mysqli_stmt_close($stmt_img);

    // 2. Delete related comments
    $stmt_com = mysqli_prepare($conn, "DELETE FROM comments WHERE post_id = ?");
    mysqli_stmt_bind_param($stmt_com, "i", $del_id);
    mysqli_stmt_execute($stmt_com);
    mysqli_stmt_close($stmt_com);

    // 3. Delete post record from DB
    $stmt_del = mysqli_prepare($conn, "DELETE FROM post WHERE id = ?");
    mysqli_stmt_bind_param($stmt_del, "i", $del_id);
    if (mysqli_stmt_execute($stmt_del)) {
        mysqli_stmt_close($stmt_del);
        mysqli_close($conn);
        header("Location: blog_post.php");
        exit();
    }
}

// FETCH POST DETAILS
$stmt = mysqli_prepare($conn, "SELECT * FROM post WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $post_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($post = mysqli_fetch_assoc($result)) {
    $title = htmlspecialchars($post['title']);
    $desc = htmlspecialchars($post['post_desc']);
    $p_i = htmlspecialchars($post['paragraph_i']);
    $p_ii = !empty($post['paragraph_ii']) ? htmlspecialchars($post['paragraph_ii']) : '';
    $p_iii = !empty($post['paragraph_iii']) ? htmlspecialchars($post['paragraph_iii']) : '';
    $p_note = !empty($post['post_note']) ? htmlspecialchars($post['post_note']) : '';
    $time = htmlspecialchars($post['uploaded']);

    $img_raw = $post['img_url'];
    $img_path = (strpos($img_raw, 'uploads/') === 0) ? $img_raw : 'uploads/' . $img_raw;
} else {
    header("Location: blog_post.php");
    exit();
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/post.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <title>Preview Post - <?php echo $title; ?></title>
  <style>
    .preview-card {
      background: white;
      border-radius: 0.8rem;
      padding: 24px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      width: 100%;
      max-width: 900px;
      margin: 0 auto;
    }
    .preview-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 10px;
    }
    .back-btn {
      color: #3182ce;
      text-decoration: none;
      font-weight: bold;
    }
    .preview-img {
      width: 100%;
      max-height: 380px;
      object-fit: cover;
      border-radius: 0.6rem;
      margin: 15px 0;
    }
    .action-bar {
      margin-top: 25px;
      padding-top: 15px;
      border-top: 1px solid #e2e8f0;
      display: flex;
      gap: 10px;
      align-items: center;
    }
    .del-btn, .pub-btn {
      background: #e53e3e;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 0.5rem;
      font-weight: bold;
      cursor: pointer;
    }
    .del-confirm-box {
      display: none;
      gap: 10px;
      align-items: center;
      background: #fed7d7;
      padding: 10px 15px;
      border-radius: 0.5rem;
    }
    .del-confirm-box.active { display: flex; }
    .btn-yes { background: #e53e3e; color: white; border: none; padding: 8px 16px; border-radius: 0.4rem; cursor: pointer; font-weight: bold; }
    .btn-cancel { background: #718096; color: white; border: none; padding: 8px 16px; border-radius: 0.4rem; cursor: pointer; font-weight: bold; }
  </style>
</head>
<body>
  <i class="fa-solid fa-bars" id="bars"></i>
  <section>

    <header class="nav">
      <div class="logo"><img src="./img/logom.png" alt="Logo"></div>
      <span>Dash Board</span>
      <nav>
        <a href="./blog_post.php" class="active"><i class="fa-solid fa-newspaper"></i> Blog Post</a>
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
      <div class="preview-card">
        <div class="preview-header">
          <a href="./blog_post.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Posts</a>
          <span class="pph" style="background:#2ecc71; color:white; padding:4px 12px; border-radius:1rem;">Published</span>
        </div>

        <h2><?php echo $title; ?></h2>
        <small style="color:#718096;"><i class="fa-regular fa-clock"></i> Published on: <?php echo $time; ?></small>

        <img src="<?php echo htmlspecialchars($img_path); ?>" alt="Post Image" class="preview-img" onerror="this.src='./img/logom.png'">

        <p style="font-size:1.1rem; color:#4a5568; margin-bottom:15px;"><strong>Description:</strong> <?php echo $desc; ?></p>
        <hr style="border:0; border-top:1px solid #edf2f7; margin:15px 0;">

        <div class="content-body">
          <p style="margin-bottom:12px; line-height:1.6;"><?php echo $p_i; ?></p>
          <?php if (!empty($p_ii)): ?><p style="margin-bottom:12px; line-height:1.6;"><?php echo $p_ii; ?></p><?php endif; ?>
          <?php if (!empty($p_iii)): ?><p style="margin-bottom:12px; line-height:1.6;"><?php echo $p_iii; ?></p><?php endif; ?>
          
          <?php if (!empty($p_note)): ?>
            <blockquote style="border-left:5px solid #d6a777; background:#fdf6ed; padding:12px; border-radius:0.4rem; margin-top:15px; font-style:italic;">
              <?php echo $p_note; ?>
            </blockquote>
          <?php endif; ?>
        </div>

        <!-- ACTION BAR WITH DELETE CONFIRMATION -->
        <div class="action-bar">
          <a href="edit_post.php?id=<?php echo $post_id; ?>" class="pub-btn" style="text-decoration:none; background:#3182ce;"><i class="fa-solid fa-pen-to-square"></i> Edit Post</a>
          <button type="button" class="del-btn" id="triggerDel"><i class="fa-solid fa-trash"></i> Delete Post</button>

          
          <div class="del-confirm-box" id="delBox">
            <span style="color:#9b2c2c; font-weight:bold;">Permanently delete this post?</span>
            <form action="" method="post" style="display:inline;">
              <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
              <button type="submit" name="confirm_delete" class="btn-yes">Yes, Delete</button>
            </form>
            <button type="button" class="btn-cancel" id="cancelDel">Cancel</button>
          </div>
        </div>

      </div>
    </main>

  </section>

  <footer>
    <small>&copy; 2026 - All rights reserved.</small>
  </footer>

  <script src="./js/main.js"></script>
  <script>
    const triggerDel = document.getElementById('triggerDel');
    const delBox = document.getElementById('delBox');
    const cancelDel = document.getElementById('cancelDel');

    if (triggerDel && delBox && cancelDel) {
      triggerDel.addEventListener('click', () => {
        delBox.classList.add('active');
        triggerDel.style.display = 'none';
      });

      cancelDel.addEventListener('click', () => {
        delBox.classList.remove('active');
        triggerDel.style.display = 'inline-block';
      });
    }
  </script>
</body>
</html>
<?php mysqli_close($conn); ?>