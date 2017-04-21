<?php

require_once("view/header/header.php");

if (isset($_GET['error']) && $_GET['error'] == 1)
    echo "<br><div class='message'>Wrong file uploaded. It must be a valid image</div>";

?>

<div id="camagru">
  <div id="select_layout" class="commentpage">Select layout</div>
  <div id="frames">
    <input type="radio" name="frame" value="1" checked><img src="camagru_pics/frame1.png">
    <input type="radio" name="frame" value="2"><img src="camagru_pics/frame2.png">
    <input type="radio" name="frame" value="3"><img src="camagru_pics/frame3.png">
    <span id="frame_selected" style="display:none;">1</span>
  </div>
  <div id="container">
    <div id="streaming_on">
      <div id="video_flip" class="center">
        <button id="flipvideo">Flip Webcam</button>
      </div>
      <div id="parent_video">
        <img id="img_1" src="camagru_pics/frame1.png">
        <img id="img_2" src="camagru_pics/frame2.png">
        <img id="img_3" src="camagru_pics/frame3.png">
<?
    if (!$image)
        echo "<video id='video'></video>";
    else
        echo "<img id='upload' src='".$image."'>";
?>
        <div class="inner">
         <video id="video2"></video>
        </div>
      </div>
      <div id="parent_start" class="center">
        <button id="startbutton">Take picture</button>
      </div>
    </div>
    <div id="picture_taken">
      <div id="photo_flip" class="center">
        <button id="flipphoto">Flip Preview</button>
        <span id="flip_or_not" style="display:none;">0</span>
      </div>
      <div id="parent_photo">
        <img id="photo">
      </div>
      <div id="parent_save" class="center">
        <button id="savebutton">Save picture</button>
      </div>
    </div>
  </div>
</div>
<div id='separator'></div>
<div class="card">
<form action="controller/play/uploader.php" method="post" enctype="multipart/form-data">
<?php $formKey->outputKey(); ?>
or&nbsp&nbsp<input type="file" name="fileToUpload" id="fileToUpload" required>
<input type="submit" value="Upload Image" name="submit">
</form>
</div>
<div id="usergallery">
  <div id="my_pictures">

<?
	if ($user_id)
	{
		echo "<span id='page' style='display:none;'>".$page."</span>";
		foreach ($all_my_pictures as $my_picture)
		{
// color card
			echo "<div class='card";
			if (!$my_picture['publish'])
				echo " unpublished";
			echo "'>";
// title pic
			if ($my_picture['title'] !== NULL)
				echo "<h3 class='title_pic'>".$my_picture['title'] ."</h3>";
            else
                echo "<h3 class='title_pic' style='visibility:hidden;'>(Name)</h3>";
// pic
			echo "<img class='picture_gallery";
			if ($my_picture['flip'] == 1)
				echo " flip";
			echo "' src='pictures/".$my_picture['picture_id']."'>";
			
			echo "<br>";
			
//name pic
            echo "<button class='name' name='name".$my_picture['picture_id']."'>Name it !</button>";
			
//(un)publish
            echo "<button class='publish' name='publish".$my_picture['picture_id']."'>";
            if ($my_picture['publish'])
                echo "Unpublish";
            else
                echo "Publish";
            echo "</button>";
			
// suppress
            echo "<button class='suppress' name='suppress".$my_picture['picture_id']."'>Suppress</button>";
			
// flip
            echo "<button class='flipbutton' name='flip".$my_picture['picture_id']."'>Flip</button>";
			
//simplification
            echo "<span style='display:none;'>".$my_picture['picture_id']."</span>";
			
            echo "</div>";
		}
	}

?>
  </div>
</div>
<div id='separator'></div>
<?
echo "<div id='index'";
if ($my_pictures_count <= 5)
    echo " class='hidden_index'";
echo ">";
if ($page > 2)
    echo "<a class='indexes' href='play.php?page=1'>First</a>";
if ($page > 1)
    echo "<a class='indexes' href='play.php?page=".($page - 1)."'><< previous</a>";
if ($my_pictures_count > 5)
    echo "<a class='indexes'>".$page."</a>";
else
    echo "<a class='indexes' id='hidden_current'>".$page."</a>";
if ($max_index != $page)
{
    echo "<a class='indexes' href='play.php?page=".($page + 1)."'>next >></a>";
    if ($max_index != $page + 1)
        echo "<a class='indexes' style='float:right;' href='play.php?page=".$max_index."'>Last</a>";
}
else
    echo "<a class='indexes' id='hidden_next' href='play.php?page=".($page + 1)."'>next >></a>";
echo "</div>";


if (!$image)
    echo "<script src='view/play/play.js'></script>";
else
    echo "<script src='view/play/play2.js'></script>";

require_once("view/footer.php");
?>