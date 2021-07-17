<?php
// connect to db
include_once("includes/db.php");
$db = init_sqlite_db("db/site.sqlite", "db/init.sql");

// check login/logout params
include_once("includes/sessions.php");
$session_messages = array();
process_session_params($db, $session_messages);


$users = exec_sql_query($db, "SELECT * FROM users;")->fetchAll();
$tags = exec_sql_query($db, "SELECT * FROM tags;")->fetchAll();
$photos_tags = exec_sql_query($db, "SELECT * FROM photos_tags;")->fetchAll();

// render login pop up
$show_login_popup = $_GET['window'] == 'login';
// render photo details
$photo_detail_id = (int)$_GET['photo'];
$show_detail_popup = !empty($photo_detail_id);

// show new photo pop up
$show_new_photo_popup = $_GET['window'] == 'new-photo';

// render edit photo pop up
$edit_photo_id = (int)$_GET['edit-photo-id'];
$show_edit_popup = !empty($edit_photo_id);

// filter by tags
$tag_selected = $_GET['tag-selected'];
if (!empty($tag_selected) && $tag_selected != 1){
  $photos = exec_sql_query($db,
        "SELECT * FROM photos_tags INNER JOIN photos ON photos_tags.photo_id = photos.id WHERE photos_tags.tag_id = :tag_id;",
        array('tag_id' => $tag_selected)
    )->fetchAll();
} else {
  $photos = exec_sql_query($db, "SELECT * FROM photos;")->fetchAll();
}


 ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="../public/styles/home.css">
  <link rel="stylesheet" href="../public/styles/detail.css">
  <link rel="stylesheet" href="../public/styles/login.css">
  <link rel="stylesheet" href="../public/styles/new-photo.css">
  <link rel="stylesheet" href="../public/styles/edit-photo.css">
  <title>The Wildlife Gallery</title>
</head>

<body>

  <?php if ($show_login_popup){
    include("includes/login.php");
  } ?>

  <?php if ($show_detail_popup){
    include("includes/detail.php");
  }?>

  <?php if ($show_edit_popup){
    include("includes/edit-photo.php");
  }?>

  <?php if ($show_new_photo_popup){
    include("includes/new-photo.php");
  }?>

  <div id="login-button-container">
    <?php if (is_user_logged_in()){ ?>
      <a href="<?php echo logout_url(); ?>">Log out</a>
    <?php } else { ?>
      <a href="/?window=login">Log in</a>
    <?php } ?>
  </div>

  <h1>The Wildlife Gallery</h1>

  <?php if (is_user_logged_in()){ ?>
    <div id="add-photo-container">
      <div id="plus-sign-box">
          <a href="/?window=new-photo">+</a>
      </div>
    </div>
  <?php } ?>

  <div id="two-columns">
    <div id="tags-container">
    <h3>Tags</h3>
      <?php
      $tags = exec_sql_query($db, "SELECT * FROM tags;")->fetchAll();
      if (count($tags) > 0) {
      foreach ($tags as $tag) {
        if ($tag_selected == $tag["id"]){
          $tag_class = "selected";
        } else {
          $tag_class = "unselected";
        }
        ?>
        <a class="<?php echo $tag_class ?>" href="/?tag-selected=<?php echo $tag["id"] ?>"><?php echo htmlspecialchars($tag["name"])?></a>
      <?php } }  ?>
    </div>
    <div id="photos-container">
      <?php
      if (count($photos) > 0) {
      foreach ($photos as $photo) { ?>
        <figure>
          <a href="/?photo=<?php echo $photo['id']; ?>">
            <img src="../public/uploads/photos/<?php echo htmlspecialchars($photo["id"].".".$photo["extension"]) ?>" alt="<?php echo htmlspecialchars($photo["title"]) ?>">
          </a>
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
      <?php } }  ?>
    </div>
  </div>

</body>

</html>
