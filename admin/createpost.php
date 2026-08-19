<?php 
session_start();
include 'admin.php';
include "connection.php";

$title = $desc = $p_i = $p_ii = $p_iii = $p_note = $upload = $pic = $error = '';
$ext = ['png', 'jpeg', 'jpg'];
$size = 5242880; // 5MB

if (isset($_POST['send'])) {
    // 
    $title = mysqli_escape_string($conn, trim(stripslashes($_POST['title'])));
    $desc  = mysqli_escape_string($conn, trim(stripslashes($_POST['desc'])));
    $p_i   = mysqli_escape_string($conn, trim(stripslashes($_POST['paragraph_i'])));
    
    $p_ii  = !empty($_POST['paragraph_ii'])  ? mysqli_escape_string($conn, trim(stripslashes($_POST['paragraph_ii']))) : '';
    $p_iii = !empty($_POST['paragraph_iii']) ? mysqli_escape_string($conn, trim(stripslashes($_POST['paragraph_iii']))) : '';
    $p_note = !empty($_POST['note'])         ? mysqli_escape_string($conn, trim(stripslashes($_POST['note']))) : '';

    // Fix comparison vs assignment (Changed == to =)
    $upload = (isset($_POST['upload']) && $_POST['upload'] === 'publish') ? 'publish' : 'draft';

    // 2. VALIDATION FLOW
    if (empty($title)) { 
        $error = "<span style='color: red;'>Title Needed</span>";
    } elseif (strlen($title) > 100) {
        $error = "<span style='color: red;'>Title max 100 Char</span>";
    } elseif (empty($desc)) {
        $error = "<span style='color: red;'>Need Description</span>";
    } elseif (strlen($desc) > 130) {
        $error = "<span style='color: red;'>Description max 130 Char</span>";
    } elseif (empty($p_i)) {
        $error = "<span style='color: red;'>Main Paragraph Needed</span>";
    } elseif (empty($_FILES['pic']['name'])) {
        $error = "<span style='color: red;'>Upload Image</span>";
    } else {
        // 3. SECURE FILE UPLOAD
        $pic = $_FILES['pic'];
        $filename = $pic['name'];
        $filelocation = $pic['tmp_name'];
        $filesize = $pic['size'];
        
        $fileext = explode('.', $filename);
        $abc = strtolower(end($fileext)); // safe because $fileext is a defined variable

        if ($filesize > $size) {
            $error = "<span style='color: red;'>Image must be less than 5MB</span>";
        } elseif (!in_array($abc, $ext)) {
            $error = "<span style='color: red;'>You can only upload JPG, PNG or JPEG</span>";
        } else {
            // Save image & execute DB query
            $new = uniqid('', true) . '.' . $abc;
            $destination = 'uploads/' . $new;

            if (move_uploaded_file($filelocation, $destination)) {
                if ($upload === 'publish') {
                    $sql = "INSERT INTO post (title, post_desc, paragraph_i, paragraph_ii, paragraph_iii, post_note, img_url) 
                            VALUES ('$title', '$desc', '$p_i', '$p_ii', '$p_iii', '$p_note', '$new')";
                    mysqli_query($conn, $sql);
                    mysqli_close($conn);
                    header("Location: blog_post.php");
                    exit();
                } else {
                    // Fixed SQL structure & aligned column mismatches
                    $sql = "INSERT INTO draft (title, post_desc, paragraph_i, paragraph_ii, paragraph_iii, post_note, img_url) 
                            VALUES ('$title', '$desc', '$p_i', '$p_ii', '$p_iii', '$p_note', '$new')";
                    mysqli_query($conn, $sql);
                    mysqli_close($conn);
                    header("Location: draft.php");
                    exit();
                }
            } else {
                $error = "<span style='color: red;'>Image Upload Failed</span>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/create.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <title>Create Post</title>
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
        <a href="#"><i class="fa-solid fa-pen-clip"></i> Create Post</a>
      </nav>

      <span>Settings & Security</span>
      <nav>
        <a href="./profile.php"><i class="fa-solid fa-user"></i> Profile</a>
        <a href="./logout.php"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
      </nav>
    </header>

    <main>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" novalidate>
        <h2>Create Post</h2>
        
        <div style="text-align: center; margin-bottom: 10px;"><?php echo $error; ?></div>
        
        <div class="form-group">
          <label><b>Post Title</b></label>
          <input type="text" maxlength="100" class="input" name="title" value="<?php echo htmlspecialchars($title); ?>" placeholder="Title (max 100 Characters)" required>
        </div>

        <div class="form-group">
          <label><b>Post Description</b></label>
          <textarea style="resize:none;" name="desc" class="input" maxlength="130" placeholder="Description (max 130 Characters)" required><?php echo htmlspecialchars($desc); ?></textarea>
        </div>

        <div class="form-group">
          <label><b>Post Content</b></label>
          <textarea style="resize:none;" name="paragraph_i" class="input" placeholder="1st Paragraph" style="min-height: 100px;" required><?php echo htmlspecialchars($p_i); ?></textarea>
        </div>

        <div class="form-group">
          <label><b>Post Content</b> (Optional)</label>
          <textarea style="resize:none;" name="paragraph_ii" class="input" placeholder="2nd Paragraph" style="min-height: 80px;"><?php echo htmlspecialchars($p_ii); ?></textarea>
        </div>

        <div class="form-group">
          <label><b>Post Content</b> (Optional)</label>
          <textarea style="resize:none;" name="paragraph_iii" class="input" placeholder="3rd Paragraph" style="min-height: 80px;"><?php echo htmlspecialchars($p_iii); ?></textarea>
        </div>

        <div class="form-group">
          <label><b>Post Note</b> (Optional)</label>
          <textarea style="resize:none;" name="note" class="input" placeholder="Note" style="min-height: 60px;"><?php echo htmlspecialchars($p_note); ?></textarea>
        </div>

        <div class="form-group radio-container">
          <label><input type="radio" name="upload" value="publish" <?php if ($upload === 'publish') echo 'checked'; ?>> <b>Publish Now</b></label>
        </div>

        <div class="form-group">
          <label><b>Post Image</b></label><br>
          <input type="file" name="pic" class="file-input">
        </div>

        <button type="submit" name="send">
          <b><i class="fa-solid fa-arrow-up-from-bracket"></i> Upload Article</b>
        </button>
      </form>
    </main>

  </section>

  <footer>
    <small>&copy; 2026 - All rights reserved.</small>
  </footer>
  <script src="./js/main.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>