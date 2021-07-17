<?php
// select photo to show details
$edit_photo = exec_sql_query($db,
      "SELECT * FROM photos WHERE (id = :id);",
      array(':id' => $edit_photo_id)
  )->fetchAll()[0];

$current_id = $current_user["id"];
// check if user is logged and this is the users photo
$edit_authorization = $current_id == $edit_photo["user_id"] && is_user_logged_in();

$photo_edited = False;

// edit photo variables
$title = NULL;
$source = NULL;
$source_link = NULL;
$description = NULL;

//edit photo sticky variables
$sticky_title = $edit_photo["title"];
$sticky_source = $edit_photo["citation_text"];
$sticky_source_link = $edit_photo["citation_link"];
$sticky_description = $edit_photo["description"];

// submit new record form feedback classes
$title_feedback = 'hidden';
$source_feedback = 'hidden';

if ($edit_authorization){
  // when user submits form
  if (isset($_POST["edit-photo"])) {
    // retrieve values
    $title = trim($_POST['title']); // untrusted
    $source = trim($_POST['source']); // untrusted
    $source_link = trim($_POST['source-link']); // untrusted
    $description = trim($_POST['description']); //untrusted

    $form_valid = True;

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

      // update photo
      $edit_success = exec_sql_query($db,
        "UPDATE photos SET title = :title, description = :description, citation_link = :citation_link, citation_text = :citation_text
        WHERE id = :edit_id",
        array(':edit_id' => $edit_photo["id"], ':title' => $title, ':description' => $description, ':citation_link' => $source_link, ':citation_text' => $source));

      $db->commit();

      $photo_edited = True;

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
<div id="edit-photo-window">
  <div class="close-window-container">
    <div class="icon-container">
        <a href="/">x</a>
    </div>
  </div>
  <?php if (!$photo_edited){ ?>
    <h1>Edit Photo Information</h1>
    <form id="edit-form" action="/?edit-photo-id=<?php echo htmlspecialchars($edit_photo["id"]) ?>" method="post" novalidate>

      <div id="edit-form-note-container">
          <span id="edit-note">*</span>
          <span>required field</span>
      </div>

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

      <div id="edit-description-container" class="input-container">
        <label id="edit-description-label" for="edit-description-box">Description:</label>
        <textarea id="edit-description-box" name="description" maxlength="1000" rows="7" cols="70"><?php echo htmlspecialchars($sticky_description);?></textarea>
      </div>

      <div id="edit-submit-container">
          <input id="edit-submit-button" type="submit" name="edit-photo" value="Submit">
      </div>

    </form>
  <?php } else { ?>
    <div id="edit-photo-upload-message">
      <h2>Your photo has successfully been edited.</h2>
    </div>
    <div id="edit-upload-success-container">
        <a href="/?edit-photo-id=<?php echo htmlspecialchars($edit_photo["id"]) ?>">Edit again</a>
        <a href="/?photo=<?php echo htmlspecialchars($edit_photo["id"]) ?>">Add tags</a>
    </div>
  <?php  } ?>

</div>
