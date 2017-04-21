<?php require_once("view/header/header.php"); ?>

<div class="container_connexion">
  <div id="form_change_pwd">
    <div class="sign" id="change_pwd">
      <a>Reboot password</a>
    </div>
    <form method="post" action="controller/connexion/change_pwd2.php">
	 <?php $formKey->outputKey(); ?>
    Login :&nbsp&nbsp<input type="text" name="change_login" size="20" required autofocus>
    <br><br>
    Mail :&nbsp&nbsp&nbsp&nbsp<input type="email" name="change_email" size="20" required>
    <br><br>
    <input type="submit" name="change_submit" value="OK">
    </form>
  </div>
</div>

<?
	 if (isset($_GET['value']))
	 {
		 if ($_GET['value'] == 1)
			 echo "<br><div class='message'>Invalid login format : only letters and white space allowed</div>";
		 else if ($_GET['value'] == 3)
			 echo "<br><div class='message'>We sent you an email to reset your password</div>";
		 else if ($_GET['value'] == 4)
			 echo "<br><div class='message'>Identification error</div>";
		 else if ($_GET['value'] == 2)
			 echo "<br><div class='message'>Invalid email format</div>";
	 }
	 
	 require_once "view/footer.php";
?>