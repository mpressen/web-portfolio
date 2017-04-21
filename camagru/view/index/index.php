<?php
require_once("view/header/header.php");

if (count($all_pictures) && $page == 1)
{
?>

<form action="index.php">
  <?php $formKey->outputKey(); ?>
  <input type="submit" value="show" style="cursor:pointer">
  <select name="display">
    <option value="5">5</option>
    <option value="10" selected>10</option>
    <option value="20">20</option>
    <option value="all">All</option>
  </select>
</form>

<?
}

echo '<div id="maingallery">';
echo   "<span id='page' style='display:none;'>".$page."</span>";

foreach ($all_pictures as $picture)
{
//if picture(s)
    echo "<div class='card'>";
	
    echo "<span style='display:none;'>".$user_id."</span>";
	//title
    if ($picture['title'])
        echo "<h3 class='title_pic'>".$picture['title'] ."</h3>";
    else
        echo "<h3 class='title_pic' style='visibility:hidden;'>(Name)</h3>";
	//picture
    echo "<img class='picture_gallery2";
    if ($picture['flip'] == 1)
        echo " flip";
    echo "' src='pictures/".$picture['picture_id']."'>";	
    echo "<br>";
	//count likes and button	
    $count_likes = count_likes($picture['picture_id']);
    $already_liked = if_liked_by_user($picture['picture_id'], $user_id);
    echo "<button ";
    if ($already_liked)
        echo "class='likes already_liked' title='Unlike !' ";
    else if ($user_id)
        echo "class='likes' title='Like !' ";
    else
        echo "class='likes' title='Sign in to like !' ";
    if ($count_likes > 1)
        echo "name='likes".$picture['picture_id']."'>".$count_likes." likes</button>";
    else
        echo "name='likes".$picture['picture_id']."'>".$count_likes." like</button>";
	//count comments and button
    $count_comments = count_comments($picture['picture_id']);
    $already_commented = if_commented_by_user($picture['picture_id'], $user_id);
    echo "<button ";
    if ($already_commented)
        echo "class='comments already_commented' title='See comments !' ";
    else if ($count_comments > 0 || !$user_id)
        echo "class='comments' title='See comments !' ";
    else
        echo "class='comments' title='Be the first to comment !' ";
    if ($count_comments > 1)
        echo "name='comments".$picture['picture_id']."'>".$count_comments." comments</button>";
    else
		echo "name='comments".$picture['picture_id']."'>".$count_comments." comment</button>";
    echo "<span style='display:none;'>".$picture['picture_id']."</span>";

    echo "</div>";
}

if (!count($all_pictures))
{
// if no picture
    echo "<div class='message'>";
    echo "There are currently no pictures in Camagru";
    echo '</div>';
}

echo "</div>";

echo "<div id='separator'></div>";

//index
echo "<div id='index'";
if ($pictures_published_count <= $display)
    echo " class='hidden_index'";
echo ">";

if ($page > 2)
    echo "<a class='indexes' href='index.php?page=1&display=".$display."'>First</a>";
if ($page > 1)
    echo "<a class='indexes' href='index.php?page=".($page - 1)."&display=".$display."'><< previous</a>";
if ($pictures_published_count >$pics_to_display)
    echo "<a class='indexes'>".$page."</a>";
else
    echo "<a class='indexes' id='hidden_current'>".$page."</a>";
if ($max_index != $page)
{
    echo "<a class='indexes' href='index.php?page=".($page + 1)."&display=".$display."'>next >></a>";
    if ($max_index != $page + 1)
        echo "<a class='indexes' style='float:right;' href='index.php?page=".$max_index."&display=".$display."'>Last</a>";
}
else
    echo "<a class='indexes' id='hidden_next' href='index.php?page=".($page + 1)."&display=".$display."'>next >></a>";

echo "</div>";
?>
<script src="view/index/index.js"></script>
	<? require_once( "view/footer.php"); ?>