(function() {

	var form_show	= document.getElementsByClassName("sign"),
		messages	= document.getElementsByClassName("message");


	function show_form(id)
	{
		for (var i = 0; i < messages.length; i++)
			messages[i].style.display = 'none';

		var x		= document.getElementById(id),
			y		= document.getElementById('login'),
			z		= document.getElementById('account'),
			sign_in = document.getElementById('sign_in'),
			sign_up = document.getElementById('sign_up');
		
		if (x.style.display == 'block')
		{
			x.style.opacity = 0;
			setTimeout(function() {
					x.style.display = 'none';
					if (id == 'login')
						sign_up.style.display = 'block';
					else
						sign_in.style.display = 'block';
                }, 500);
		}
		else
		{
			x.style.display = 'block';
			setTimeout(function() {
					x.style.opacity = 1;
                }, 10);
			if (id == 'login')
				sign_up.style.display = 'none';
			else
				sign_in.style.display = 'none';
		}
	}
	
	form_show[0].addEventListener('click', function(ev) {
			show_form('login');
		}, false);
	form_show[1].addEventListener('click', function(ev) {
			show_form('account');
			}, false);
	
})();