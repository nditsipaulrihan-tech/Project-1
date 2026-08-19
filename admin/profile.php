<?php

session_start();
include 'admin.php';
include 'connection.php';
$username = $error = $password = $cpassword = '';

if(isset($_POST['update_password'])){
  $info = isset($_POST['info']) ? trim($_POST['info']) : '';
    $pass = isset($_POST['password']) ? $_POST['password'] : '';
    $cpass = isset($_POST['cpassword']) ? $_POST['cpassword'] : '';

    if(!empty($info)){
      
      $sql = "SELECT * FROM admin WHERE email='$info';";
      $result = mysqli_query($conn, $sql);
      
      if($result){
       if(!empty($pass)){
        if(strlen($pass)<6 || strlen($pass)>10){
        $error = "<center><span>Password must be between 6-10 characters</span></center>";
      } elseif (empty($cpass)){
        $error = "<center><span>Confirm Password</span></center>";
      } elseif ($cpass !== $pass){
        $error = "<center><span>Incorrect Password</span></center>";
      } else{
        $hpass = password_hash($pass, PASSWORD_BCRYPT);
        $sqli = "UPDATE admin SET password='$hpass' WHERE email='$info';";
        mysqli_close($conn);
        echo "<script>alert('Password updated successfully');</script>";
      }
       } else{
        $error = "<center><span>Input New Password</span></center>";
       }
      }else{
        $error = "<center><span>User Not Found</span></center>";
      }
      }else{
        $error = "<center><span>Email Required</span></center>";
      }}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/profile.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <title>Dash Board</title>
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
      <a href="./comments.php"><i class="fa-solid fa-comment-dots"></i> Comments</a>
      <a href="./createpost.php"><i class="fa-solid fa-pen-clip"></i> Create Post</a>
    </nav>

    <span>Settings & Security</span>
    <nav>
      <a href="#"><i class="fa-solid fa-user"></i> Profile</a>
      <a href="./logout.php"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
    </nav>

    </header>

    <main>

      <form action="" method="post">

      <h2>Update Password</h2>

      <?php echo $error;?>

        <div class="form-group">
          <label for=""><b>Email</b></label>
          <input type="text" name="info" class="input" placeholder="example@email.com" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>

        <div class="form-group">
          <label for=""><b>New Password</b></label>
          <input type="password" class="input" name="password" id="current_password" placeholder="Current Password" value="<?php echo htmlspecialchars($password); ?>" required>
          <label for=""><input type="checkbox" name="show_current" id="show"> Show Password</label>
        </div>

        <div class="form-group">
          <label for=""><b>Confirm Password</b></label>
          <input type="password" class="input" name="cpassword" id="new_password" placeholder="New Password" required>
          <label for=""><input type="checkbox" name="show_new" id="cshow"> Show Password</label>
        </div>
        
        <button type="submit" name="update_password">Update Password</button>
      </form>
    </main>
  </section>
  <footer>
    <small>&copy; 2026 - All rights reserved.</small>
  </footer>
  <script src="./js/main.js"></script>
  <script>
    function show(x,y) {
  x.addEventListener('click', ()=>{
    if(x.checked){
      y.type = "text"
    }else{
      y.type = "password"
    }
  })
  
}

let a = document.getElementById('show')
let b = document.getElementById('cshow')
let aa = document.querySelector("input[name='password']")
let bb = document.querySelector("input[name='cpassword']")

show(a, aa)
show(b, bb)

  </script>
</body>
</html>