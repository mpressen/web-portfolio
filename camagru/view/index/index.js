(function() {

	var likesbutton = document.getElementsByClassName("likes"),
		commentsbutton = document.getElementsByClassName("comments");
	
	function likepicture(elem)
	{
		
		var button_name = elem.name,
			element		= document.getElementsByName(button_name)[0],
			card		= element.parentElement,
			user		= card.firstChild,
			picture		= card.lastChild;
		
		if (user.innerHTML != '0')
		{
			var liking = new XMLHttpRequest();
			liking.onreadystatechange = function()
				{
					if (liking.readyState == 4 && (liking.status == 200 || liking.status == 0))
					{
						var likesnumber = element.innerHTML;
						likesnumber = parseInt(likesnumber.split(" likes")[0]);
						if (liking.responseText == 1)
						{
							if (likesnumber > 2)
								element.innerHTML = likesnumber - 1 + " likes";
							else
								element.innerHTML = likesnumber - 1 + " like";
							element.classList.remove("already_liked");
							element.setAttribute("title", "Like !");
						}
						else
						{
							if (likesnumber > 0)
								element.innerHTML = likesnumber + 1 + " likes";
							else
								element.innerHTML = likesnumber + 1 + " like";
							element.classList.add("already_liked");
							element.setAttribute("title", "Unlike !");
						}
					}
				};
			liking.open("POST",'ajax/likepic.php', true);
			liking.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			liking.send("user_id=" + user.innerHTML + "&picture_id=" + picture.innerHTML);
		}
	}
	
	for (var i = 0; i < likesbutton.length; i++)
	{
		likesbutton[i].addEventListener('click', function(ev) {
				likepicture(this);
				ev.preventDefault();
			}, false);
	}
	
	function commentpicture(elem)
	{
		var button_name = elem.name,
			element		= document.getElementsByName(button_name)[0],
			card		= element.parentElement,
			user		= card.firstChild,
			picture		= card.lastChild;
		
		document.location.href="commentpic.php?user_id=" + user.innerHTML + "&picture_id=" + picture.innerHTML;
	}
	
	for (var i = 0; i < commentsbutton.length; i++)
	{
		commentsbutton[i].addEventListener('click', function(ev) {
				commentpicture(this);
				ev.preventDefault();
			}, false);
	}
	
})();