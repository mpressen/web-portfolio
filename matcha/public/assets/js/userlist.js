$(document).ready(function() {
	var DoNotAttach = ['inputlocation', 'tags', 'username1', 'username2'];
	$('#userlist :input').each(function() {
		if ($( this ).attr('id') != undefined && jQuery.inArray($( this ).attr('id'), DoNotAttach) == -1)
		{
			$( this ).change(function() {
				$('#userlist').submit();
			});
		}
	});
	var autocomplete = new google.maps.places.Autocomplete(
        (document.getElementById('inputlocation')),
        { types: ['geocode'] });
	autocomplete.addListener('place_changed', function(){
		var place = autocomplete.getPlace();
		$("#longitude").val(null);
		$("#latitude").val(null);
		try {
			if (place.geometry.location)
			{
				$("#longitude").val(place.geometry.location.lng());
				$("#latitude").val(place.geometry.location.lat());
				document.getElementById('inputlocation').setCustomValidity("");
			}
		}
		catch (e){
			document.getElementById('inputlocation').setCustomValidity("Please select one from the list");
		}
	});
	$('#inputlocation').keyup( function()
	{
		$("#longitude").val(null);
		$("#latitude").val(null);
		if ($(this).val() != "")
			document.getElementById('inputlocation').setCustomValidity("Please select one from the list");
		else
			document.getElementById('inputlocation').setCustomValidity("");
	});
	$('#popularity').slider({
		tooltip: 'show',
		tooltip_split: 'true',
		scale: 'logarithmic'
	});
	$('#age').slider({
		tooltip: 'show',
		tooltip_split: 'true'
	});
	$('.popularity').each(function() {
		$(this).tooltip(
			{
				placement: "left",
				html: true,
				title: '<i class="glyphicon glyphicon-eye-open"></i>&nbsp;' + $(this).data('visit') + '&nbsp;&nbsp;<i class="glyphicon glyphicon-hand-right"></i>&nbsp;' + $(this).data('like') 
			});
	});
	morecomments();
});
function morecomments()
{
	$('#ulist .tag-editor').each( function( index , elt ){
		var nbtags = $( elt ).data('nbtags');
		if (nbtags > 1)
		{
			var parent_width = $( elt ).width(), cumulated_width = 0, index_elt = 0;
			$( elt ).find('div').each(function ( index , child_elt )
			{
				cumulated_width += $( child_elt ).outerWidth();
				if (cumulated_width > parent_width)
				{
					index_elt = Math.ceil(index / 2) ;
					return false;
				}
			});
			if (index_elt)
				morecomments_set(elt, (nbtags - index_elt));
			else
				$( elt ).next('span').hide();
		}
	});
}

function morecomments_set( elt , value )
{
	let sp = $( elt ).next('span');
	if (sp.length > 0)
	{
		sp.text('+' + value);
		sp.show();
	}
	else
		$( elt ).after('<span class="badge more-tags">+' + value + '</span>');
}

$( window ).resize( function(){ morecomments(); })