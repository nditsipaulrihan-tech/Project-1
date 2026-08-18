<?php 

session_start();
include '../admin/connection.php';
$user = $comment = $error = '';
if(empty($_GET['post_id'])){
  header("Location: blog.php");
  exit();
}
else{
  $id = $_GET['post_id'];
  $title = $_SESSION['title']; 
  $desc = $_SESSION['post_desc']; 
  $time = $_SESSION['uploaded']; 
  $p_i = $_SESSION['paragraph_i']; 
  $p_ii = "<p>{$_SESSION['paragraph_ii']}</p>"; 
  $p_iii = "<p>{$_SESSION['paragraph_iii']}</p>"; 
  $p_note = "<blockquote>{$_SESSION['post_note']}</blockquote>"; 
  $img = $_SESSION['img_url']; 

  $sql = "SELECT * FROM comments WHERE post_id={$id}";
  $com = mysqli_query($conn, $sql);
}

  if(isset($_POST['send'])){
      if(empty($_POST['comment']) || empty($_POST['info'])){
        $error ="<span style='color: red;'>Input user_name and Comment</span>";
        }else{
        $user = mysqli_escape_string($conn, htmlspecialchars($_POST['info']));
        $comm = mysqli_escape_string($conn, htmlspecialchars($_POST['comment']));
        $abc = "INSERT INTO comments(post_id, username, p_com) VALUES ({$id},'{$user}','{$comm}');";
        mysqli_query($conn, $abc);
        header("Location: blogpost.php?post_id={$id}");
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
  <title>Post Article</title>
</head>
<body>

  <header>
    <div class="logo">
      <img src="../img/logom.png" alt="">
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
   <section class="blog_post">
    <a href="./blog.php">&lt;Back</a>
    <h2><?php echo $title;?></h2>
    <div class="bpost">
      <div>
      <img src="<?php echo $img;?>" alt="">
    </div>
    <div>
      <p > Published on the <?php echo $time;?></p>
      <p class="desc"><?php echo $desc;?></p>
    </div>
    </div>

    <p><?php echo $p_i;?></p>

      <?php echo $p_ii;?>
      <?php echo $p_iii;?>
      <?php echo $p_note;?>
   </section>
   <section class="blog_com">
    <div class="comments">
      <h3>Comments</h3>
      <div class="com">
        <?php 
        if(mysqli_num_rows($com)>0){
          foreach ($com as $c){ 
          echo "<h5>{$c['username']}</h5>";
          echo "<p>{$c['p_com']}</p>";
        }}?> 
      </div>
    </div>
    <button type="button" id="come">Add Comment</button>
  </section>
    
    <form class="form" id="myForm" action="" method="post">
      
      <?php echo $error; ?>
      
      <div class="formg">
        <label for=""><b>user_name:</b></label>
        <input type="text" class="input" name="info" pattern="[a-z_]+" title="only lowercase letters and '_' are allowed" placeholder="user_name" value="<?php echo htmlspecialchars($user); ?>" required>
      </div>
      
      <div class="formg">
        <label for=""><b>Comment</b></label>
        <textarea name="comment" class="input" id="asd" placeholder="Enter your comment" required><?php echo htmlspecialchars($comment);?></textarea>
      </div>
      
      <button type="submit" name="send"><i class="fa-solid fa-comment"></i> Comment</button><br><br>
    </form>
    
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
    <hr>
    <small>&copy; 2026 - All rights reserved.</small>
  </footer>
  
  <script src="../js/script.js"></script>
  <!-- <script src="../admin/js/main.js"></script> -->
 <script>
    let com = document.getElementById("come");
  let form = document.getElementById("myForm")
    if(com){
      com.addEventListener("click", ()=>{
        form.classList.toggle("active");

      })
    } 

    document.querySelector('input[name="info"]').addEventListener('input', e=>{
      e.target.value = e.target.value.toLowerCase().replace(/[^a-z_]/g, '')
    })

    document.addEventListener("DOMContentLoaded", function(){
    function sizing() {
      this.style.height = 'auto';
      this.style.height = this.scrollHeight + 'px';
    }

    let input = document.querySelectorAll('#asd');

    input.forEach(text=>{
      sizing.call(text);
      text.addEventListener('input', sizing)
    })
 })
  </script> 
</body>
</html>