<?php
session_start();

include("connection.php");

$error = $info = $pass = '';

if (isset($_POST['send'])) {
  $info = isset($_POST['info']) ? trim($_POST['info']) : '';
  $pass = isset($_POST['passkey']) ? $_POST['passkey'] : '';

  if (!empty($info)) {

    $sql = "SELECT * FROM user_info WHERE email='$info';";
    $result = mysqli_query($conn, $sql);

    if($result){
      $rows = mysqli_fetch_assoc($result);

    if (!empty($rows)) {
      $row = $rows;

      if ($row) {

        if (password_verify($pass, $row['passkey'])) {
          $_SESSION['name'] = $row['name'];
          $_SESSION['email'] = $row['email'];


          if (!empty($error)) {
            $error = '';
          } else {
            mysqli_close($conn);
            header("Location: index.php");
            exit();
          }
        } else {
          $error = "<span>Incorrect Password</span>";
        }

      } else {
        $error = "<center><span>Incorrect Password</span></center>";
      }
    }else{$error = "<center><span>User Not Found</span></center>";}

    }
  } else {
    $error = "<center><span>User Not Found</span></center>";
  }
} else {
  $error = "<center><span>Email Required</span></center>";
}

?>






<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../css/login.css">
  <title>Log In</title>
</head>

<body>
  <!-- <?php // echo $_SESSION['update'];
        ?> -->
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

    <h2>Connection</h2>

    <?php echo $error; ?>

    <div class="formg">
      <label for=""><b>Email:</b></label>
      <input type="email" class="input" name="info" placeholder="example@gmail.com" value="<?php echo htmlspecialchars($info); ?>">
    </div>

    <div class="formg">
      <label for=""><b>Passkey:</b></label>
      <input type="password" class="input" name="passkey" placeholder="Enter Passkey" id="show" value="<?php echo htmlspecialchars($pass); ?>">
      <label for=""><input type="checkbox" name="" id="pshow">Show</label>
    </div>

    <button type="submit" name="send"><i class="fa-solid fa-user"></i> Log In</button><br><br>
  </form>

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

let b = document.getElementById('pshow')
let bb = document.querySelector("input[name='passkey']")

show(b, bb)
  </script>
</body>

</html>