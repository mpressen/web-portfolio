<?php
require_once("view/header/header.php"); 
?>

<div class="container_connexion">
  <div id="form_signin">
    <div class="sign" id="sign_in">
	  <a>Sign in</a>
    </div>
	<form id='login' method="post" action="controller/connexion/login.php">
		<?php $formKey->outputKey(); ?>
		Login :&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="text" name="check_login" size="20" required>
		<br><br>
		Password :&nbsp&nbsp<input type="password" name="check_passwd" size="20" required>
		<br><br>
		<input type="submit" name="check_submit" value="OK">
		<br><br>
		<a id="forgotten_pwd" href="change_pwd.php">Forgotten password ?</a>
	</form>
  </div>
  <div id="form_signup">
    <div class="sign" id="sign_up">
	  <a>Sign up</a>
    </div>
	<form id="account" method="post" action="controller/connexion/create_user.php">
		<?php $formKey2->outputKey();?>
		Mail :&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="email" name="create_email" size="20" required>
		<br><br>
		Login :&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="text" name="create_login" size="20" title="Only letters and white space allowed"required>
		<br><br>
		Password :&nbsp&nbsp&nbsp<input type="password" pattern=".{5,10}" name="create_passwd" size="20" title="Between 5 and 10 characters, with at least one capital letter and one digit" required>
		<br><br>
		<input type="submit" name="create_submit" value="OK">
	</form>
  </div>
</div>
<?php
if (isset($_GET['name_err']) && $_GET['name_err'] == 1)
    echo "<br><div class='message'>Invalid login format : only letters and white space allowed</div>";
if (isset($_GET['error_email']) && $_GET['error_email'] == 1)
    echo "<br><div class='message'>This email address is already registered</div>";
if (isset($_GET['email_invalid']) && $_GET['email_invalid'] == 1)
    echo "<br><div class='message'>Invalid email format</div>";
if (isset($_GET['pwd_invalid']) && $_GET['pwd_invalid'] == 1)
    echo "<br><div class='message'>Invalid password format : between 5 and 10 characters, with at least one capital letter and one digit</div>";
if (isset($_GET['error_login']) && $_GET['error_login'] == 1)
    echo "<br><div class='message'>This login is already registered</div>";
if (isset($_GET['error_connect']) && $_GET['error_connect'] == 1)
    echo "<br><div class='message'>Identification error</div>";
if (isset($_GET['error_confirmation']) && $_GET['error_confirmation'] == 1)
    echo "<br><div class='message'>Check your mailbox to activate your account</div>";
if (isset($_GET['account_created']) && $_GET['account_created'] == 1)
    echo "<br><div class='message'>We sent you an email to activate your account</div>";

if (isset($_GET['verif_error']) && $_GET['verif_error'] == 1)
    echo "<br><div class='message'>Please do not hack</div>";
if (isset($_GET['confirmed']) && $_GET['confirmed'] == 1)
    echo "<br><div class='message'>Your account has just been activated</div>";
if (isset($_GET['pwd_changed']) && $_GET['pwd_changed'] == 1)
    echo "<br><div class='message'>Your password has been changed</div>";
if (isset($_GET['pwd_not_changed']) && $_GET['pwd_not_changed'] == 1)
    echo "<br><div class='message'>Invalid new password format : between 5 and 10 characters, with at least one capital letter and one digit</div>";
?>
<script src="view/connexion/connexion.js"></script>
<?php require_once "view/footer.php"; ?>