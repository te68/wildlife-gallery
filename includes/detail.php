<?php
// select photo to show details
$photo = exec_sql_query($db,
      "SELECT * FROM photos WHERE (id = :id);",
      array(':id' => $photo_detail_id)
  )->fetchAll()[0];
// select tags for the photo
$tags =  exec_sql_query($db,
      "SELECT tags.id, tags.name FROM tags INNER JOIN photos_tags ON tags.id = photos_tags.tag_id  WHERE (photos_tags.photo_id = :id);",
      array(':id' => $photo_detail_id)
  )->fetchAll();

$current_id = $current_user["id"];
// check if user is logged and this is the users photo
$edit_authorization = $current_id == $photo["user_id"] && is_user_logged_in();

$show_delete_tag_confirmation = False;
$tag1_feedback = 'hidden';

$photo_deleted = False;

if ($edit_authorization){

  // deleting photo
  $delete_entire_photo_id = $_GET['delete-photo-id'];
  if (!empty($delete_entire_photo_id)){
    $db->beginTransaction();
    // delete photos_tags of the photo
    exec_sql_query($db,
        "DELETE FROM photos_tags WHERE (photo_id = :photo_id);",
        array(':photo_id' => $delete_entire_photo_id)
    );
    // delete the photo itself
    exec_sql_query($db,
        "DELETE FROM photos WHERE (id = :photo_id);",
        array(':photo_id' => $delete_entire_photo_id)  );
    unlink('public/uploads/photos/'.$photo["id"].".".$photo["extension"] );
    $photo_deleted = True;
    // end transaction
    $db->commit();

  }

  // deleting tags
  $delete_tag_id = $_GET['delete-tag-id'];
  $delete_photo_id = $_GET['photo'];
  if (!empty($delete_tag_id) && !empty($delete_photo_id)) {
      exec_sql_query($db,
          "DELETE FROM photos_tags WHERE (photo_id = :photo_id AND tag_id = :tag_id);",
          array(':photo_id' => $delete_photo_id, ':tag_id' => $delete_tag_id)
      );
      $tags =  exec_sql_query($db,
            "SELECT tags.id, tags.name FROM tags INNER JOIN photos_tags ON tags.id = photos_tags.tag_id INNER JOIN photos ON photos_tags.photo_id = photos.id WHERE (photos.id = :id);",
            array(':id' => $photo_detail_id)
      )->fetchAll();
      $show_delete_tag_confirmation = True;
  }

  // creating new tags
  if (isset($_POST['submit_tags'])) {
    $tag1 = trim($_POST['tag1']); // untrusted
    $tag2 = trim($_POST['tag2']); // untrusted
    $tag3 = trim($_POST['tag3']); // untrusted

    $form_valid = True;

    // check if tags are new, if they are create new tags, if not create photo_tags only
    $db->beginTransaction();

    // check if inputs have been filled
    if (empty($tag1)) {
      $form_valid = False;
      $tag1_feedback = '';
    }

    if ($form_valid){

      $tags_array = [$tag1, $tag2, $tag3];
      foreach ($tags_array as $tag) {
        if (!empty($tag)){
          // look for tag with same name
          $does_tag_exist = exec_sql_query(
          $db,
          "SELECT * FROM tags WHERE (name = :tag_name);",
          array( ':tag_name' => $tag)
          )->fetchAll();
          // if the tag exists
          if (count($does_tag_exist) > 0){
              // check if photo_tags record exists
              $does_photos_tags_exist = exec_sql_query(
              $db,
              "SELECT * FROM photos_tags WHERE (photo_id = :photo_id AND tag_id = :tag_id);",
              array( ':photo_id' => $photo_detail_id, ':tag_id' => (int)$does_tag_exist[0]["id"])
              )->fetchAll();
              // if there is not a photos_tags, create one
              if (count($does_photos_tags_exist) == 0){
                $insert_success =  exec_sql_query($db,
                "INSERT INTO photos_tags (photo_id, tag_id) VALUES (:photo_id, :tag_id);",
                  array( ':photo_id' => $photo_detail_id, ':tag_id' => (int)$does_tag_exist[0]["id"]) );
                // if tag is inserted, show new tag in window
                if ($insert_success){
                  $tags =  exec_sql_query($db,
                        "SELECT tags.id, tags.name FROM tags INNER JOIN photos_tags ON tags.id = photos_tags.tag_id  WHERE (photos_tags.photo_id = :photo_id);",
                        array(':photo_id' => $photo_detail_id)
                    )->fetchAll();
                }
              }
              // if the tag does not exist
            } elseif (count($does_tag_exist) == 0) {
              $insert_tag_success = exec_sql_query($db,
               "INSERT INTO tags (name) VALUES (:tag);",
                array(':tag' => $tag) );
              if ($insert_tag_success){
                // find the new tag
                $new_tag = exec_sql_query( $db,
                "SELECT * FROM tags WHERE (name = :tag_name);",
                array( ':tag_name' => $tag)
                )->fetchAll()[0];
                // create photos_tags with new tag
                $insert_success =  exec_sql_query($db,
                "INSERT INTO photos_tags (photo_id, tag_id) VALUES (:photo_id, :tag_id);",
                  array( ':photo_id' => $photo_detail_id, ':tag_id' => (int)$new_tag["id"]) );
                // if tag is inserted, show new tag in window
                if ($insert_success){
                  $tags =  exec_sql_query($db,
                        "SELECT tags.id, tags.name FROM tags INNER JOIN photos_tags ON tags.id = photos_tags.tag_id  WHERE (photos_tags.photo_id = :photo_id);",
                        array(':photo_id' => $photo_detail_id)
                    )->fetchAll();
                }
              }
            }

        }
      }
      // end transaction
      $db->commit();

    } else {
      // set sticky values
      $sticky_tag1 = $tag1; // untrusted
      $sticky_tag2 = $tag2; // untrusted
      $sticky_tag3 = $tag3; // untrusted
      }
    }


}

?>


<div class="blur-background">
  <a href="/">Click anywhere</a>
</div>
<div id="detail-window">
  <div class="close-window-container">
    <div class="icon-container">
        <a href="/">x</a>
    </div>
  </div>
  <!-- show photo if it hasn't been deleted yet -->
  <?php if (!$photo_deleted){ ?>
    <?php if ($edit_authorization){ ?>
      <div id="edit-container">
        <a id="edit-link" href="/?<?php echo http_build_query(array('edit-photo-id' => $photo_detail_id)) ?>">
          <img id="edit-icon" src="../public/images/edit.png" alt="Edit Icon">
        </a>
      </div>

    <?php  } ?>
    <h1><?php echo htmlspecialchars($photo["title"]) ?></h1>
    <span id="description"><?php echo htmlspecialchars($photo["description"]) ?></span>
    <div id="photo-tags-container">
      <?php foreach ($tags as $tag) { ?>
        <?php if ($edit_authorization){ ?>
          <a href="/?<?php echo http_build_query(array('photo' => $photo_detail_id, 'delete-tag-id' => $tag['id'])) ?>">X</a>
        <?php } ?>
        <span><?php echo htmlspecialchars($tag["name"]) ?></span>
    <?php  } ?>
    </div>
    <?php if ($show_delete_tag_confirmation) { ?>
      <span id="remove-tag-confirmation">You succesfully removed the tag from this photo.</span>
    <?php } ?>
    <div id="photo-two-columns">
      <?php if ($edit_authorization){ ?>
        <div id="tag-form-container">
          <h3>Add Tags</h3>
          <form action="/?photo=<?php echo $photo_detail_id ?>" method="post">
            <label for="tag-input1">Tag #1</label>
            <input id="tag-input1" type="text" name="tag1" value="<?php echo htmlspecialchars($sticky_tag1) ?>">
            <span class="<?php echo $tag1_feedback ?>" id="tag-feedback">Enter a tag name</span>
            <label for="tag-input2">Tag #2 (optional)</label>
            <input id="tag-input2" type="text" name="tag2" value="<?php echo htmlspecialchars($sticky_tag1) ?>">
            <label for="tag-input3">Tag #3 (optional)</label>
            <input id="tag-input3" type="text" name="tag3" value="<?php echo htmlspecialchars($sticky_tag1) ?>">
            <input id="submit-tags" type="submit" name="submit_tags" value="Submit">
          </form>
        </div>
      <?php } ?>
      <figure id="detail-figure">
        <img src="../public/uploads/photos/<?php echo htmlspecialchars($photo["id"].".".$photo["extension"]) ?>" alt="<?php echo htmlspecialchars($photo["title"]) ?>">
        <figcaption>
          <?php if (empty($photo["citation_link"])){ ?>
            <cite>Source: <?php echo htmlspecialchars($photo["citation_text"]) ?></cite>
          <?php  } else { ?>
            <cite>
              <a target="_blank" href="<?php echo htmlspecialchars($photo["citation_link"]) ?>">
                <?php echo htmlspecialchars($photo["citation_text"]) ?>
              </a>
            </cite>
          <?php  } ?>
        </figcaption>
      </figure>
      <?php if ($edit_authorization){ ?>
        <div id="trash-container">
          <a href="/?<?php echo http_build_query(array('photo' => $photo_detail_id, 'delete-photo-id' => $photo_detail_id)) ?>">
            <!-- Source: https://www.flaticon.com/free-icon/delete_1214428?term=trash%20bin&page=1&position=2&page=1&position=2&related_id=1214428&origin=search -->
            <img id="trash-can" src="../public/images/delete.png" alt="Trash Icon">
          </a>
        </div>
    <?php  } ?>
    </div>
    <?php if ($edit_authorization){ ?>
      <cite id="trash-source">Source for edit and trash icons:
        <a target="_blank" href="https://www.flaticon.com/free-icon/delete_1214428?term=trash%20bin&page=1&position=2&page=1&position=2&related_id=1214428&origin=search">
             Kiranshastry
        </a>
      </cite>
    <?php  } ?>
  <?php } else{ ?>
    <div id="photo-delete-message">
      <h2>Your photo has succesfully been deleted.</h2>
    </div>
  <?php } ?>


</div>
