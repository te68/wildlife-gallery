<?php

define("MAX_FILE_SIZE", 1000000);

$photo_uploaded = False;

// submit new record form variables
$title = NULL;
$source = NULL;
$source_link = NULL;
$description = NULL;

// submit new record form sticky variables
$sticky_title = '';
$sticky_source = '';
$sticky_source_link = '';
$sticky_description = '';

// submit new record form feedback classes
$image_feedback = 'hidden';
$image_too_big = 'hidden';
$title_feedback = 'hidden';
$source_feedback = 'hidden';

if (is_user_logged_in()){
  // when user submits form
  if (isset($_POST["submit-photo"])) {
    // retrieve values
    $upload = $_FILES['upload'];
    $title = trim($_POST['title']); // untrusted
    $source = trim($_POST['source']); // untrusted
    $source_link = trim($_POST['source-link']); // untrusted
    $description = trim($_POST['description']); //untrusted

    $form_valid = True;

    // if no errors in upload, retrieve filename and extension
    if ($upload['error'] == UPLOAD_ERR_INI_SIZE or $upload['error'] == UPLOAD_ERR_FORM_SIZE){
      $image_too_big = '';
      $form_valid = False;
    } else if ($upload['error'] == UPLOAD_ERR_OK){
      $filename = basename($upload['name']);
      $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    } else {
      $form_valid = False;
      $image_feedback = '';
    }

    if (empty($title)) {
      $form_valid = False;
      $title_feedback = '';
    }

    if (empty($source)) {
      $form_valid = False;
      $source_feedback = '';
    }

    if ($form_valid) {
      $db->beginTransaction();

      // insert upload into DB
      $insert_success = exec_sql_query($db,
        "INSERT INTO photos (title, description, extension, citation_link, citation_text, user_id) VALUES (:title, :description, :extension, :citation_link, :citation_text, :user_id)",
        array(':title' => $title, ':description' => $description, ':extension' => $extension, ':citation_link' => $source_link, ':citation_text' => $source, ':user_id' => $current_user['id'] )
      );

      if ($insert_success) {
        $last_id = $db->lastInsertId('id');
        $new_filename = 'public/uploads/photos/' . $last_id . '.' . $extension;
        move_uploaded_file($upload["tmp_name"],  $new_filename);
        $photo_uploaded = True;
      }

      $db->commit();

    } else {
      $sticky_title = $title;
      $sticky_source = $source;
      $sticky_source_link = $source_link;
      $sticky_description = $description;
    }
  }
}

?>

<div class="blur-background">
  <a href="/">Click anywhere</a>
</div>
<div id="new-photo-window">
  <div class="close-window-container">
    <div class="icon-container">
        <a href="/">x</a>
    </div>
  </div>
  <?php if (!$photo_uploaded){ ?>
    <h1>Submit Photo</h1>
    <form id="photo-form" action="/?window=new-photo" enctype="multipart/form-data" method="post" novalidate>
      <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />

      <div id="form-note-container">
          <span id="note">*</span>
          <span>required field</span>
      </div>

      <div class="input-container">
        <label for="upload">Image file:</label>
        <input accept=".jpg, .png, .svg" type="file" id="upload" name="upload" />
        <span class="star">*</span>
      </div>
      <p class="<?php echo $image_feedback; ?> feedback">Please upload an image.</p>
      <p class="<?php echo $image_too_big; ?> feedback">Please upload an image smaller than 1 MB.</p>

      <div class="input-container">
        <label for="title">Title:</label>
        <input class="text-input" id="title" type="text" name="title" value="<?php echo htmlspecialchars($sticky_title); ?> ">
        <span class="star">*</span>
      </div>
      <p class="<?php echo $title_feedback; ?> feedback">Please enter a title.</p>

      <div class="input-container">
        <label for="source">Source:</label>
        <input class="text-input" id="source" type="text" name="source" value="<?php echo htmlspecialchars($sticky_source); ?> ">
        <span class="star">*</span>
      </div>
      <p class="<?php echo $source_feedback; ?> feedback">Please enter the source.</p>

      <div class="input-container">
        <label for="source-link">Source link:</label>
        <input class="text-input" id="source-link" type="text" name="source-link" value="<?php echo htmlspecialchars($sticky_source_link); ?> ">
      </div>

      <div id="description-container" class="input-container">
        <label id="description-label" for="description-box">Description:</label>
        <textarea id="description-box" name="description" maxlength="1000" rows="7" cols="70"><?php echo htmlspecialchars($sticky_description);?></textarea>
      </div>

      <div id="submit-container">
          <input id="submit-button" type="submit" name="submit-photo" value="Submit">
      </div>

    </form>
  <?php } else { ?>
    <div id="photo-upload-message">
      <h2>Your photo has succesfully been uploaded.</h2>
    </div>
    <div id="upload-success-container">
        <a href="/?window=new-photo">Upload another</a>
        <a href="/?photo=<?php echo htmlspecialchars($last_id) ?>">Add tags</a>
    </div>
<?php  } ?>

</div>
