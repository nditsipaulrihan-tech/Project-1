<?php
session_start();
include 'connection.php';

if (empty($_GET['id'])) {
    header("Location: blog_post.php");
    exit();
}

$post_id = intval($_GET['id']);
$error = $success = '';

// FETCH EXISTING DATA
$stmt_fetch = mysqli_prepare($conn, "SELECT * FROM post WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt_fetch, "i", $post_id);
mysqli_stmt_execute($stmt_fetch);
$res = mysqli_stmt_get_result($stmt_fetch);

if ($post = mysqli_fetch_assoc($res)) {
    $title = $post['title'];
    $desc = $post['post_desc'];
    $p_i = $post['paragraph_i'];
    $p_ii = $post['paragraph_ii'];
    $p_iii = $post['paragraph_iii'];
    $p_note = $post['post_note'];
    $old_img = $post['img_url'];
} else {
    header("Location: blog_post.php");
    exit();
}
mysqli_stmt_close($stmt_fetch);

// HANDLE FORM UPDATE
if (isset($_POST['update_post'])) {
    $new_title = mysqli_real_escape_string($conn, trim(stripslashes($_POST['title'])));
    $new_desc  = mysqli_real_escape_string($conn, trim(stripslashes($_POST['desc'])));
    $new_p_i   = mysqli_real_escape_string($conn, trim(stripslashes($_POST['paragraph_i'])));
    $new_p_ii  = !empty($_POST['paragraph_ii']) ? mysqli_real_escape_string($conn, trim(stripslashes($_POST['paragraph_ii']))) : '';
    $new_p_iii = !empty($_POST['paragraph_iii']) ? mysqli_real_escape_string($conn, trim(stripslashes($_POST['paragraph_iii']))) : '';
    $new_p_note = !empty($_POST['note']) ? mysqli_real_escape_string($conn, trim(stripslashes($_POST['note']))) : '';
    $move_to_draft = isset($_POST['as_draft']);

    if (empty($new_title) || empty($new_desc) || empty($new_p_i)) {
        $error = "Title, Description, and Main Paragraph are required.";
    } else {
        $new_img = $old_img;

        // Image upload handling
        if (!empty($_FILES['pic']['name'])) {
            $ext_allowed = ['png', 'jpeg', 'jpg'];
            $file = $_FILES['pic'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($file['size'] > 5242880) {
                $error = "Image size must be less than 5MB.";
            } elseif (!in_array($ext, $ext_allowed)) {
                $error = "Only JPG, JPEG, and PNG are allowed.";
            } else {
                $unique_name = uniqid('', true) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], 'uploads/' . $unique_name)) {
                    // Delete old file if exists
                    if (file_exists('uploads/' . $old_img) && !empty($old_img)) {
                        @unlink('uploads/' . $old_img);
                    }
                    $new_img = $unique_name;
                } else {
                    $error = "Failed to upload new image.";
                }
            }
        }

        if (empty($error)) {
            if ($move_to_draft) {
                // Move from 'post' to 'draft' table
                $stmt_ins = mysqli_prepare($conn, "INSERT INTO draft (title, post_desc, paragraph_i, paragraph_ii, paragraph_iii, post_note, img_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt_ins, "sssssss", $new_title, $new_desc, $new_p_i, $new_p_ii, $new_p_iii, $new_p_note, $new_img);
                
                if (mysqli_stmt_execute($stmt_ins)) {
                    $stmt_del = mysqli_prepare($conn, "DELETE FROM post WHERE id = ?");
                    mysqli_stmt_bind_param($stmt_del, "i", $post_id);
                    mysqli_stmt_execute($stmt_del);

                    header("Location: draft.php");
                    exit();
                }
            } else {
                // Regular update in 'post' table
                $stmt_up = mysqli_prepare($conn, "UPDATE post SET title=?, post_desc=?, paragraph_i=?, paragraph_ii=?, paragraph_iii=?, post_note=?, img_url=? WHERE id=?");
                mysqli_stmt_bind_param($stmt_up, "sssssssi", $new_title, $new_desc, $new_p_i, $new_p_ii, $new_p_iii, $new_p_note, $new_img, $post_id);
                
                if (mysqli_stmt_execute($stmt_up)) {
                    header("Location: post_preview.php?id=" . $post_id);
                    exit();
                } else {
                    $error = "Failed to update post in database.";
                }
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
  <link rel="stylesheet" href="./css/edit.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <title>Edit Post - <?php echo htmlspecialchars($title); ?></title>
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
      <div class="edit-card">
        <div class="edit-header">
          <a href="./post_preview.php?id=<?php echo $post_id; ?>" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Preview</a>
          <span class="badge-status badge-published">Editing Published Post</span>
        </div>

        <?php if (!empty($error)): ?>
          <div class="msg-box error-box"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label><b>Title</b></label>
            <input type="text" name="title" class="input" value="<?php echo htmlspecialchars($title); ?>" required>
          </div>

          <div class="form-group">
            <label><b>Description</b></label>
            <textarea name="desc" class="input" maxlength="130" required><?php echo htmlspecialchars($desc); ?></textarea>
          </div>

          <div class="form-group">
            <label><b>1st Paragraph (Main Content)</b></label>
            <textarea name="paragraph_i" class="input" rows="5" required><?php echo htmlspecialchars($p_i); ?></textarea>
          </div>

          <div class="form-group">
            <label><b>2nd Paragraph (Optional)</b></label>
            <textarea name="paragraph_ii" class="input" rows="4"><?php echo htmlspecialchars($p_ii); ?></textarea>
          </div>

          <div class="form-group">
            <label><b>3rd Paragraph (Optional)</b></label>
            <textarea name="paragraph_iii" class="input" rows="4"><?php echo htmlspecialchars($p_iii); ?></textarea>
          </div>

          <div class="form-group">
            <label><b>Note / Quote (Optional)</b></label>
            <textarea name="note" class="input" rows="3"><?php echo htmlspecialchars($p_note); ?></textarea>
          </div>

          <div class="form-group">
            <label><b>Current Image:</b></label>
            <div class="current-img-preview">
              <img src="uploads/<?php echo htmlspecialchars($old_img); ?>" alt="Current Image" onerror="this.src='./img/logom.png'">
            </div>
            <label style="margin-top:8px;"><b>Change Image (Optional):</b></label>
            <input type="file" name="pic" class="file-input">
          </div>

          <div class="form-group checkbox-group">
            <label><input type="checkbox" name="as_draft"> <b>Demote to Draft</b> (Move from published to drafts list)</label>
          </div>

          <button type="submit" name="update_post" class="save-btn"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
        </form>
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