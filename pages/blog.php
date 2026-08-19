<?php 
session_start();
include '../admin/connection.php';

$sql = "SELECT * FROM post ORDER BY id DESC;";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/style6 copy.css">
  <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <title>Blog</title>
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
        <li>
          <select onchange="goToPage(this)">
            <option value="book">Books</option>
            <option value="diary.html">The Diary</option>
            <option value="clarity.html">Clarity</option>
          </select>
        </li>
        <li><a href="#">Blog</a></li>
        <li><a href="./services.html">Services</a></li>
        <li><a href="./free.html">Free Resources</a></li>
        <li><a href="./contact.html">Contact</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <div class="main">    
      <?php
      if ($result && mysqli_num_rows($result) > 0) {
          while ($a = mysqli_fetch_assoc($result)) {
              $img_path = (strpos($a['img_url'], 'uploads/') === 0) ? $a['img_url'] : '../admin/uploads/' . $a['img_url'];
              
              echo "
              <div class='post'>
                <h3>" . htmlspecialchars($a['title']) . "</h3>
                <div class='etc'>
                  <div class='img'>
                    <img src='" . htmlspecialchars($img_path) . "' alt='Post Image' onerror=\"this.src='../img/logom.png'\">
                  </div>
                  <div class='bloga'>
                    <p class='desc'>" . htmlspecialchars($a['post_desc']) . "</p>
                    <a href='./blogpost.php?post_id={$a['id']}'>Read Article <i class='fa-solid fa-arrow-right'></i></a>
                  </div>
                </div>
              </div>";
          }
      } else {
          echo "<p style='background: white; padding: 20px; border-radius: 0.8rem; text-align: center; grid-column: 1 / -1;'>No articles published yet.</p>";
      }

      if ($result) mysqli_free_result($result);
      mysqli_close($conn);
      ?>
    </div>
  </main>

  <footer>
    <div class="footer">
      <div class="links">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="../index.html">Home</a></li>
          <li><a href="./about.html">About</a></li>
          <li>
            <select onchange="goToPage(this)">
              <option value="book">Books</option>
              <option value="./diary.html">The Diary</option>
              <option value="./clarity.html">Clarity</option>
            </select>
          </li>
          <li><a href="#">Blog</a></li>
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
    <hr style="margin: 15px 0 10px 0; border: 0; border-top: 1px solid rgba(255,255,255,0.3);">
    <small>&copy; 2026 - All rights reserved.</small>
  </footer>
  
  <script src="../js/script.js"></script>
</body>
</html>