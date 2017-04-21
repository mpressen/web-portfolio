(function() {

	var hamburger	= document.querySelector('#hamburger'),
		hidden		= document.querySelector('#hidden'),
		label		= document.querySelector('#label'),
		shown		= 0,
		camagru		= document.querySelector('#header-logo');
	
	function show()
	{
		if (!shown)
		{
			hidden.setAttribute('style', 'display:block;');
			label.setAttribute('src', 'camagru_pics/cancel_icon.jpg');
			camagru.setAttribute('style', 'display:none;');
			shown = 1;
		}
		else
		{
			hidden.setAttribute('style', 'display:none;');
			label.setAttribute('src', 'camagru_pics/hamburger_icon.png');
			camagru.setAttribute('style', 'display:block;');
			shown = 0;
		}
	}
	
	hamburger.addEventListener('click', function(ev) {
			show();
			ev.preventDefault();
		}, false);

	camagru.addEventListener('click', function(ev) {
			document.location.href="index.php";
		}, false);
	
})();