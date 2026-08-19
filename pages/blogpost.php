<?php 
session_start();
include '../admin/connection.php';

$user = $comment = $error = '';

if (empty($_GET['post_id'])) {
    header("Location: blog.php");
    exit();
} else {
    $id = intval($_GET['post_id']);

    // Fetch post details directly from DB using post_id (More reliable than relying solely on $_SESSION)
    $stmt_post = mysqli_prepare($conn, "SELECT * FROM post WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt_post, "i", $id);
    mysqli_stmt_execute($stmt_post);
    $result_post = mysqli_stmt_get_result($stmt_post);

    if ($post_data = mysqli_fetch_assoc($result_post)) {
        $title = htmlspecialchars($post_data['title']); 
        $desc = htmlspecialchars($post_data['post_desc']); 
        $time = htmlspecialchars($post_data['uploaded']); 
        $p_i = htmlspecialchars($post_data['paragraph_i']); 
        $p_ii = !empty($post_data['paragraph_ii']) ? "<p>" . htmlspecialchars($post_data['paragraph_ii']) . "</p>" : ""; 
        $p_iii = !empty($post_data['paragraph_iii']) ? "<p>" . htmlspecialchars($post_data['paragraph_iii']) . "</p>" : ""; 
        $p_note = !empty($post_data['post_note']) ? "<blockquote>" . htmlspecialchars($post_data['post_note']) . "</blockquote>" : ""; 
        
        $img_raw = $post_data['img_url'];
        $img = (strpos($img_raw, 'uploads/') === 0) ? $img_raw : '../admin/uploads/' . $img_raw;
    } else {
        // Fallback to session if direct fetch fails
        $title = isset($_SESSION['title']) ? $_SESSION['title'] : 'Post'; 
        $desc = isset($_SESSION['post_desc']) ? $_SESSION['post_desc'] : ''; 
        $time = isset($_SESSION['uploaded']) ? $_SESSION['uploaded'] : ''; 
        $p_i = isset($_SESSION['paragraph_i']) ? $_SESSION['paragraph_i'] : ''; 
        $p_ii = !empty($_SESSION['paragraph_ii']) ? "<p>{$_SESSION['paragraph_ii']}</p>" : ""; 
        $p_iii = !empty($_SESSION['paragraph_iii']) ? "<p>{$_SESSION['paragraph_iii']}</p>" : ""; 
        $p_note = !empty($_SESSION['post_note']) ? "<blockquote>{$_SESSION['post_note']}</blockquote>" : ""; 
        $img = isset($_SESSION['img_url']) ? $_SESSION['img_url'] : '../img/logom.png';
    }
    mysqli_stmt_close($stmt_post);

    // Fetch comments for this post
    $sql = "SELECT * FROM comments WHERE post_id = ?";
    $stmt_com = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt_com, "i", $id);
    mysqli_stmt_execute($stmt_com);
    $com = mysqli_stmt_get_result($stmt_com);
}

// Handle Comment Submission
if (isset($_POST['send'])) {
    if (empty($_POST['comment']) || empty($_POST['info'])) {
        $error = "<span style='color: red;'>Input username and comment</span>";
    } else {
        $user = mysqli_escape_string($conn, htmlspecialchars($_POST['info']));
        $comm = mysqli_escape_string($conn, htmlspecialchars($_POST['comment']));
        
        $insert_sql = "INSERT INTO comments (post_id, username, p_com) VALUES (?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($stmt_insert, "iss", $id, $user, $comm);
        mysqli_stmt_execute($stmt_insert);
        mysqli_stmt_close($stmt_insert);

        header("Location: blogpost.php?post_id=" . $id);
        exit();
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/style7.css">
  <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    @import url(../css/blogg.css);
  </style>
  <title><?php echo $title; ?></title>
</head>
<body>

  <header>
    <div class="logo">
      <img src="../img/logom.png" alt="Logo">
    </div>
    <i class="fa-solid fa-bars" id="bars"></i>
    <nav class="nav">
      <ul>
        <li><a href="../index.html">Home</a></li>
        <li><a href="./about.html">About</a></li>
        <li><select onchange="goToPage(this)">
          <option value="book">Books</option>
          <option value="diary.html">The Diary</option>
          <option value="clarity.html">Clarity</option>
        </select></li>
        <li><a href="./blog.php">Blog</a></li>
        <li><a href="./services.html">Services</a></li>
        <li><a href="./free.html">Free Resources</a></li>
        <li><a href="./contact.html">Contact</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <article class="blog_post">
      <a href="./blog.php"><i class="fa-solid fa-arrow-left"></i> Back to Blog</a>
      <h2><?php echo $title; ?></h2>
      
      <div class="bpost">
        <div class="img-container">
          <img src="<?php echo $img; ?>" alt="Post Image" onerror="this.src='../img/logom.png'">
        </div>
        <div class="post-meta-desc">
          <p class="meta-time"><i class="fa-regular fa-clock"></i> Published on <?php echo $time; ?></p>
          <p class="desc"><?php echo $desc; ?></p>
        </div>
      </div>

      <div class="post-content">
        <p><?php echo $p_i; ?></p>
        <?php echo $p_ii; ?>
        <?php echo $p_iii; ?>
        <?php echo $p_note; ?>
      </div>
    </article>

    <section class="blog_com">
      <div class="comments">
        <h3>Comments</h3>
        <div class="com-list">
          <?php 
          if ($com && mysqli_num_rows($com) > 0) {
              foreach ($com as $c) { 
                  echo "<div class='com'>";
                  echo "<h5>" . htmlspecialchars($c['username']) . "</h5>";
                  echo "<p>" . htmlspecialchars($c['p_com']) . "</p>";
                  echo "</div>";
              }
          } else {
              echo "<p style='color: #666; text-align: center; padding: 10px;'>No comments yet. Be the first to comment!</p>";
          }
          ?> 
        </div>
      </div>
      
      <div class="btn-container">
        <button type="button" id="come">Add Comment</button>
      </div>

      <form class="form" id="myForm" action="" method="post" style="display: none;">
        <h3>Leave a Comment</h3>
        <?php echo $error; ?>
        
        <div class="formg">
          <label><b>Username:</b></label>
          <input type="text" class="input" name="info" pattern="[a-z_]+" title="only lowercase letters and '_' are allowed" placeholder="username" value="<?php echo htmlspecialchars($user); ?>" required>
        </div>
        
        <div class="formg">
          <label><b>Comment:</b></label>
          <textarea name="comment" class="input" id="asd" placeholder="Enter your comment" required><?php echo htmlspecialchars($comment); ?></textarea>
        </div>
        
        <button type="submit" name="send"><i class="fa-solid fa-comment"></i> Post Comment</button>
      </form>
    </section>
  </main>
  
  <footer>
    <div class="footer">
      <div class="links">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="../index.html">Home</a></li>
          <li><a href="./about.html">About</a></li>
          <li><select onchange="goToPage(this)">
            <option value="book">Books</option>
            <option value="./diary.html">The Diary</option>
            <option value="./clarity.html">Clarity</option>
          </select></li>
          <li><a href="./blog.php">Blog</a></li>
          <li><a href="./services.html">Services</a></li>
          <li><a href="./contact.html">Contact</a></li>
        </ul>
      </div>
      <div>
        <h3>Follow Me</h3>
        <ul>
          <li><i class="fa-brands fa-facebook"></i><a href="https://www.facebook.com/share/1BYUnnJWPD/" target="_blank" rel="noopener noreferrer"> FaceBook</a></li>
          <li><i class="fa-brands fa-instagram"></i><a href="https://www.instagram.com/kum.doris.965?igsh=cjBhMDM0aHNlNDRz" target="_blank" rel="noopener noreferrer"> Instagram</a></li>
          <li><i class="fa-brands fa-linkedin"></i><a href="https://cm.linkedin.com/in/kum-doris-mbeuh-66868a223" target="_blank" rel="noopener noreferrer"> LinkedIn</a></li>
        </ul>
      </div>
      <div>
        <h3>Contact Me</h3>
        <ul>
          <li><i class="fa-solid fa-envelope"></i><a href="mailto:dorismbeuhkum@gmail.com"> Mail Me</a></li>
          <li><i class="fa-solid fa-phone"></i><a href="tel:+237651116915"> Call Me</a></li>
        </ul>
      </div>
    </div>
    <hr style="margin: 15px 0; border: 0; border-top: 1px solid rgba(255,255,255,0.3);">
    <small>&copy; 2026 - All rights reserved.</small>
  </footer>
  
  <script src="../js/script.js"></script>
  <script>
    // Toggle comment form visibility
    let comBtn = document.getElementById("come");
    let form = document.getElementById("myForm");
    
    if (comBtn && form) {
      comBtn.addEventListener("click", () => {
        if (form.style.display === "none" || form.style.display === "") {
          form.style.display = "block";
          comBtn.textContent = "Cancel";
        } else {
          form.style.display = "none";
          comBtn.textContent = "Add Comment";
        }
      });
    } 

    // Restrict username to lowercase & underscores
    const usernameInput = document.querySelector('input[name="info"]');
    if (usernameInput) {
      usernameInput.addEventListener('input', e => {
        e.target.value = e.target.value.toLowerCase().replace(/[^a-z_]/g, '');
      });
    }

    // Auto-resize textarea height
    document.addEventListener("DOMContentLoaded", function() {
      function sizing() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight + 2) + 'px';
      }

      let textareas = document.querySelectorAll('#asd');
      textareas.forEach(text => {
        sizing.call(text);
        text.addEventListener('input', sizing);
      });
    });
  </script> 
</body>
</html>