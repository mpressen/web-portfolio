<?php

require_once("view/header/header.php");

if (isset($_GET['error']) && $_GET['error'] == 1)
	echo "<br><div class='message'>Invalid comment</div>"; 
?>

<div id="fb-root"></div>
  <script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.8";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
  </script>

<?
echo "<div class='commentpage'>";

 // picture side
if ($picture['title'])
	echo "<h3 class='title_pic'>".$picture['title'] ."</h3>";
else
	echo "<h3 class='title_pic' style='visibility:hidden;'>(Name)</h3>";
echo "<img class='picture_comment' src='pictures/".$picture['picture_id']."'>";
?>

<div class="fb-share-button" data-href="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/Webcam.svg/768px-Webcam.svg.png" data-layout="button" data-size="large" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fupload.wikimedia.org%2Fwikipedia%2Fcommons%2Fthumb%2Fd%2Fd0%2FWebcam.svg%2F768px-Webcam.svg.png&amp;src=sdkpreparse">Partager</a></div>

<?
// comment side
echo "</div>";
for ($i = 0; $i < count($all_comments); $i++)
{
	echo "<div class='commentpage'>";
	echo "<span style='color:black;'>".$all_comments[$i]['comments']."</span>";
	echo "<br>";
	echo "<span style='font-style:italic;font-size:12px;'>written by ".$logins[$i]." at ".$all_comments[$i]['timestamp']."</span>";
	echo "</div>";
}

if ($user_id)
{
	echo '<div class="commentpage">';
	echo '<form id="comment" method="post" action=controller/index/commentpic2.php?user_id='.$user_id.'&picture_id='.$picture_id.'>';
	echo $formKey->outputKey();
	echo '<br>';
    echo '<textarea id="styled" name="comment_message" placeholder="New comment (500char max)" required autofocus></textarea>';
	echo '<br>';
    echo '<input id="post_comment" type="submit" name="comment_submit" value="Post comment !">';
	echo '</form>';
	echo '</div>';
}
else
{
	echo '<br><div class="message">';
	echo 'You need to be signed in to comment';
	echo '</div>';
}
echo "</div>";
require_once("view/footer.php");
?>
