<?php require_once("view/header/header.php"); ?>

  <div class="container_connexion">
    <div id="form_change_pwd"
      <div class="sign" id="change_pwd">
        <a>Set your new password</a>
      </div>
	<form method="post" action=controller/connexion/verif.php?login=<? echo $login; ?>>
	    <?php $formKey->outputKey(); ?>
        Password:&nbsp&nbsp<input type="password" pattern=".{5,10}" name="create_passwd" size="20" title="Between 5 and 10 characters, with at least one capital letter and one digit" required autofocus>
        <br><br>
        <input type="submit" name="change_submit" value="OK">
      </form>
    </div>
  </div>
<? require_once ("view/footer.php"); ?>