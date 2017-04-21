(function() {

		var frameradio	= document.getElementsByName("frame"),
		frame_selected	= document.querySelector('#frame_selected'),

		streaming_on	= document.querySelector('#streaming_on'),
		flipvideo		= document.querySelector('#video_flip'),
		parent_video	= document.querySelector('#parent_video'),
		img_1			= document.querySelector('#img_1'),
		img_2	        = document.querySelector('#img_2'),
		img_3		    = document.querySelector('#img_3'),
		parent_start	= document.querySelector('#parent_start'),
		startbutton		= document.querySelector('#startbutton'),

		picture_taken	= document.querySelector('#picture_taken'),
		parent_photo	= document.querySelector('#parent_photo'),
		flipphoto		= document.querySelector('#photo_flip'),
		flip_or_not     = document.querySelector('#flip_or_not'),
		photo			= document.querySelector('#photo'),
		parent_save		= document.querySelector('#parent_save'),
		savebutton		= document.querySelector('#savebutton'),

		my_pictures		= document.querySelector('#my_pictures'),
		namebutton		= document.getElementsByClassName("name"),
		publishbutton	= document.getElementsByClassName("publish"),
		suppressbutton	= document.getElementsByClassName("suppress"),
		flipbutton		= document.getElementsByClassName("flipbutton"),
		page			= document.querySelector('#page').innerHTML,
		pageobj			= document.querySelector('#page'),

		hidden_index	= document.getElementsByClassName("hidden_index")[0],
		hidden_current	= document.querySelector('#hidden_current'),
		hidden_next		= document.querySelector('#hidden_next'),
		index			= document.querySelector('#index'),
		indexes			= document.getElementsByClassName("indexes"),

		upload			= document.querySelector('#upload');

	var shutter			= new Audio();
	shutter.autoplay = false;
	shutter.src = navigator.userAgent.match(/Firefox/) ? './camagru_pics/shutter.ogg' : './camagru_pics/shutter.mp3';
	
	img_1.setAttribute('style', 'display:block;');
	parent_start.style.visibility = 'visible';
	video_flip.style.visibility = 'visible';

	function select_frame(elem) {
		frame_selected.innerHTML = elem.value;
		if (elem.value == 1)
		{
			img_2.setAttribute('style', 'display:none;');
			img_3.setAttribute('style', 'display:none;');
			img_1.setAttribute('style', 'display:block;');
		}
		else if (elem.value == 2)
		{
			img_1.setAttribute('style', 'display:none;');
			img_3.setAttribute('style', 'display:none;');
			img_2.setAttribute('style', 'display:block;');
		}
		else if (elem.value == 3)
		{
			img_1.setAttribute('style', 'display:none;');
			img_2.setAttribute('style', 'display:none;');
			img_3.setAttribute('style', 'display:block;');
		}
	}

	for (var i = 0; i < frameradio.length; i++)
	{
		frameradio[i].addEventListener('click', function(ev) {
				select_frame(this);
			}, false);
	}

	function takepicture() {
		var tmpcanvas = document.createElement("canvas");
		tmpcanvas.width = 640; 
		tmpcanvas.height = 480; 
		tmpcanvas.getContext('2d').drawImage(upload, 0, 0, 640, 480);
		var data = tmpcanvas.toDataURL('image/png');
		var pic = "base64data=" + encodeURIComponent(data);
		var merging = new XMLHttpRequest();
		merging.onreadystatechange = function(){
			if (merging.readyState == 4 && (merging.status == 200 || merging.status == 0))
			{
				shutter.play();
				picture_taken.style.visibility = 'visible';
				photo.setAttribute('src', 'data:image/png;base64,' + merging.responseText);
			}
		};
		merging.open("POST",'ajax/mergepic.php', true);
		merging.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		merging.send(pic + "&frame=" + frame_selected.innerHTML);
	}

	function savepicture()
	{
		var data = photo.src;
		var pic = "base64data=" + encodeURIComponent(data);
		var saving = new XMLHttpRequest();

		saving.onreadystatechange = function(){
			if (saving.readyState == 4 && (saving.status == 200 || saving.status == 0))
			{
				if (page == 1)
				{ 
					//creer card et integration dans le DOM
					var newcard = document.createElement("div");
					newcard.setAttribute('class', 'card');
					
					var newtitle = document.createElement("h3");
					newtitle.setAttribute('class', 'title_pic');
					newtitle.setAttribute('style', 'visibility:hidden');
					newtitle.innerHTML = "(Name)";
					newcard.appendChild(newtitle);
					
					var newpic = document.createElement("img");
					newpic.classList.add('picture_gallery');
					if (flip_or_not.innerHTML == 1)
						newpic.classList.add('flip');
					newpic.setAttribute('src', data);
					newcard.appendChild(newpic);
					
					var newspace = document.createElement("br");
					newcard.appendChild(newspace);
					
					var newnamebutton = document.createElement("button");
					newnamebutton.setAttribute('class', 'name');
					newnamebutton.setAttribute('name', 'name' + saving.responseText);
					newnamebutton.innerHTML = "Name it !";
					newcard.appendChild(newnamebutton);
					
					var newpublishbutton = document.createElement("button");
					newpublishbutton.setAttribute('class', 'publish');
					newpublishbutton.setAttribute('name', 'publish' + saving.responseText);
					newpublishbutton.innerHTML = "Unpublish";
					newcard.appendChild(newpublishbutton);
					
					var newsuppressbutton = document.createElement("button");
					newsuppressbutton.setAttribute('class', 'suppress');
					newsuppressbutton.setAttribute('name', 'suppress' + saving.responseText);
					newsuppressbutton.innerHTML = "Suppress";
					newcard.appendChild(newsuppressbutton);

					var newflipbutton = document.createElement("button");
					newflipbutton.setAttribute('class', 'flipbutton');
					newflipbutton.setAttribute('name', 'flip' + saving.responseText);
					newflipbutton.innerHTML = "Flip";
					newcard.appendChild(newflipbutton);
					
					var newspan = document.createElement("span");
					newspan.setAttribute('style', 'display:none;');
					newspan.innerHTML = saving.responseText;
					newcard.appendChild(newspan);
					my_pictures.insertBefore(newcard, my_pictures.firstChild);
					
					my_pictures.removeChild(pageobj);
					my_pictures.insertBefore(pageobj, my_pictures.firstChild);
					
					
					var childList = my_pictures.children[6];
					if (childList)
					{
						my_pictures.removeChild(childList);
						if (hidden_index)
							hidden_index.style.display = 'block';
						if (hidden_current)
						hidden_current.style.display = 'inline-block';
						if (hidden_next)
						hidden_next.style.display = 'inline-block';
						if (index)
						index.style.display = 'block';
						if (indexes)
						indexes[0].style.display = 'inline-block';
						if (indexes)
						indexes[1].style.display = 'inline-block';
					}

					newnamebutton.addEventListener('click', function(ev) {
							namepicture(this);
							ev.preventDefault();
						}, false);
					
					newpublishbutton.addEventListener('click', function(ev) {
							publishpicture(this);
							ev.preventDefault();
						}, false);
					
					newsuppressbutton.addEventListener('click', function(ev) {
							suppresspicture(this);
							ev.preventDefault();
						}, false);

					newflipbutton.addEventListener('click', function(ev) {
							flippicture(this);
							ev.preventDefault();
						}, false);
				}
				else
					document.location.href="play.php";
			}
		};

		saving.open("POST",'ajax/savepic.php', true);
		saving.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		saving.send(pic + '&flip=' + flip_or_not.innerHTML);
	}

	function namepicture(elem)
	{
		var button_name = elem.name;
		var element = document.getElementsByName(button_name)[0];
		var card = element.parentElement;
		var title  = card.firstChild;
		var name = prompt("Please enter a name for your picture", "");
		if (name !== null && name !== "")
		{
			var picture = card.lastChild;
			var renaming = new XMLHttpRequest();
			renaming.onreadystatechange = function()
			{
				if (renaming.readyState == 4 && (renaming.status == 200 || renaming.status == 0))
				{
					title.setAttribute("style", "visibility:visible;");
					title.innerHTML = name;
				}
			};
			renaming.open("POST",'ajax/renamepic.php', true);
			renaming.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			renaming.send("name=" + name + "&picture_id=" + picture.innerHTML);
		}
	}

	for (var i = 0; i < namebutton.length; i++)
	{
		namebutton[i].addEventListener('click', function(ev) {
				namepicture(this);
				ev.preventDefault();
			}, false);
	}

	function publishpicture(elem2)
	{
		var button_name2 = elem2.name;
		var element2 = document.getElementsByName(button_name2)[0];
		var card2 = element2.parentElement;
		var picture2 = card2.lastChild;
		var publishing = new XMLHttpRequest();
		publishing.onreadystatechange = function()
		{
			if (publishing.readyState == 4 && (publishing.status == 200 || publishing.status == 0))
			{
				if (element2.innerHTML === "Unpublish")
				{
					card2.classList.add("unpublished");
					element2.innerHTML = "Publish";
				}
				else
				{
					card2.classList.remove("unpublished");
					element2.innerHTML = "Unpublish";

				}
			}
		};
		publishing.open("POST",'ajax/publishpic.php', true);
		publishing.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		publishing.send("picture_id=" + picture2.innerHTML);
	}

	for (var j = 0; j < publishbutton.length; j++)
	{
		publishbutton[j].addEventListener('click', function(ev) {
				publishpicture(this);
				ev.preventDefault();
			}, false);
	}

	function suppresspicture(elem3)
	{
		if (confirm("Are you sure you want to delete this picture ?"))
		{
			var button_name3 = elem3.name;
			var element3 = document.getElementsByName(button_name3)[0];
			var card3 = element3.parentElement;
			var picture3 = card3.lastChild;
			var last_card = my_pictures.lastElementChild;
			var last_pic_id = last_card.lastChild.innerHTML;
			var suppressing = new XMLHttpRequest();
			suppressing.onreadystatechange = function()
				{
					if (suppressing.readyState == 4 && (suppressing.status == 200 || suppressing.status == 0))
					{
						my_pictures.removeChild(card3);
						var new_pic = suppressing.responseText.split(",");
						if (new_pic[0])
						{
							//creer card et integration dans le DOM
							var newcard = document.createElement("div");
							newcard.setAttribute('class', 'card');
							
							var newtitle = document.createElement("h3");
							newtitle.setAttribute('class', 'title_pic');
							if (new_pic[2])
								newtitle.innerHTML = new_pic[2];
							else
							{
								newtitle.setAttribute('style', 'visibility:hidden');
								newtitle.innerHTML = "(Name)";
							}
							newcard.appendChild(newtitle);
							
							var newpic = document.createElement("img");
							newpic.classList.add('picture_gallery');
							if (new_pic[4] == 1)
								newpic.classList.add('flip');
							newpic.setAttribute('src', 'pictures/' + new_pic[0]);
							newcard.appendChild(newpic);
							
							var newspace = document.createElement("br");
							newcard.appendChild(newspace);
							
							var newnamebutton = document.createElement("button");
							newnamebutton.setAttribute('class', 'name');
							newnamebutton.setAttribute('name', 'name' + new_pic[0]);
							newnamebutton.innerHTML = "Name it !";
							newcard.appendChild(newnamebutton);
							
							var newpublishbutton = document.createElement("button");
							newpublishbutton.setAttribute('class', 'publish');
							newpublishbutton.setAttribute('name', 'publish' + new_pic[0]);
							newpublishbutton.innerHTML = "Unpublish";
							newcard.appendChild(newpublishbutton);
							
							var newsuppressbutton = document.createElement("button");
							newsuppressbutton.setAttribute('class', 'suppress');
							newsuppressbutton.setAttribute('name', 'suppress' + new_pic[0]);
							newsuppressbutton.innerHTML = "Suppress";
							newcard.appendChild(newsuppressbutton);

							var newflipbutton = document.createElement("button");
							newflipbutton.setAttribute('class', 'flipbutton');
							newflipbutton.setAttribute('name', 'flip' + suppressing.responseText);
							newflipbutton.innerHTML = "Flip";
							newcard.appendChild(newflipbutton);
							
							var newspan = document.createElement("span");
							newspan.setAttribute('style', 'display:none;');
							newspan.innerHTML = new_pic[0];
							newcard.appendChild(newspan);
							my_pictures.appendChild(newcard);
							
							//rajout des events_listeners
							newnamebutton.addEventListener('click', function(ev) {
									namepicture(this);
									ev.preventDefault();
								}, false);
							
							newpublishbutton.addEventListener('click', function(ev) {
									publishpicture(this);
									ev.preventDefault();
								}, false);
							
							newsuppressbutton.addEventListener('click', function(ev) {
									suppresspicture(this);
									ev.preventDefault();
								}, false);

							newflipbutton.addEventListener('click', function(ev) {
									flippicture(this);
									ev.preventDefault();
								}, false);
							
							if (new_pic[5] != 1)
							{
								if (hidden_index)
									hidden_index.style.display = 'none';
								else if (index)
									index.style.display = 'none';
								if (hidden_current && hidden_next)
								{
									hidden_current.style.display = 'none';
									hidden_next.style.display = 'none';
								}
								else if (indexes)
								{
									indexes[0].style.display = 'none';
									indexes[1].style.display = 'none';
								}
							}
						}
						else if (page != 1 & my_pictures.children.length == 1)
						{
								var num = page - 1;
								document.location.href="play.php?page=" + num.toString();
						}
					}
				};
			suppressing.open("POST",'ajax/suppresspic.php', true);
			suppressing.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			suppressing.send("picture_id=" + picture3.innerHTML +"&last_pic_id=" + last_pic_id);
		}
	}

	for (var k = 0; k < suppressbutton.length; k++)
	{
		suppressbutton[k].addEventListener('click', function(ev) {
				suppresspicture(this);
				ev.preventDefault();
			}, false);
	}

	function flippicture(elem4)
	{
			var button_name4 = elem4.name;
			var element4 = document.getElementsByName(button_name4)[0];
			var card4 = element4.parentElement;
			var picture4 = card4.lastChild;
			var pictureNode = card4.childNodes;
			var flipping = new XMLHttpRequest();
			flipping.onreadystatechange = function()
				{
					if (flipping.readyState == 4 && (flipping.status == 200 || flipping.status == 0))
						pictureNode[1].classList.toggle("flip");
				};
			flipping.open("POST",'ajax/flippic.php', true);
			flipping.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			flipping.send("picture_id=" + picture4.innerHTML);
	}

	for (var e = 0; e < flipbutton.length; e++)
	{
		flipbutton[e].addEventListener('click', function(ev) {
				flippicture(this);
				ev.preventDefault();
			}, false);
	}

	flipvideo.addEventListener('click', function(ev) {
			upload.classList.toggle("flip");
			photo.classList.toggle("flip");
			img_1.classList.toggle("flip");
			img_2.classList.toggle("flip");
			img_3.classList.toggle("flip");
			if (photo.classList.contains("flip"))
                flip_or_not.innerHTML = 1;
			else
				flip_or_not.innerHTML = 0;
			ev.preventDefault();
		}, false);

	flipphoto.addEventListener('click', function(ev) {
			photo.classList.toggle("flip");
			if (photo.classList.contains("flip"))
                flip_or_not.innerHTML = 1;
			else
				flip_or_not.innerHTML = 0;
			ev.preventDefault();
		}, false);

	startbutton.addEventListener('click', function(ev) {
			takepicture();
			ev.preventDefault();
		}, false);

	savebutton.addEventListener('click', function(ev){
			savepicture();
			ev.preventDefault();
		}, false);

})();