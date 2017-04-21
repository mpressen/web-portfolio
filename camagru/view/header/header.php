<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no">
  <title>Camagru</title>
  <link rel="stylesheet" type="text/css" href="view/camagrustyle.css">
</head>
<body>
  <div id="site-container">
    <div id="site-pusher">
      <header id="header" role="banner">
        <div id="header-inner">
          <div id="header-logo">
            <img id="logo" src="camagru_pics/camagru_logo.png" alt="Camagru" width="50" height="50">
            <h1 id="title">Camagru</h1>
          </div>
          <a id="hamburger"><img id="label" src="camagru_pics/hamburger_icon.png" alt="Menu" width="50" height="50"></a>
        <nav id="hidden">        
            <ul>
              <li><a href="index.php">Gallery</a></li>
<?
	if ($user_id)
		echo '<li><a href="play.php">Take a picture !</a></li><li><a href="controller/connexion/logout.php">Sign out</a></li>';
	else
		echo '<li><a href="connexion.php">Connexion</a></li>';
?>
            </ul>
          </nav>
          <nav id="header-nav">
            <ul>
              <li><a href="index.php">Gallery</a></li>
<?
	if ($user_id)
		echo '<li><a href="play.php">Take a picture !</a></li><li><a href="controller/connexion/logout.php">Sign out</a></li>';
	else
		echo '<li><a href="connexion.php">Connexion</a></li>';
?>
            </ul>        
          </nav>
        </div>
      </header>
    </div>
  <script src="view/header/header.js"></script>