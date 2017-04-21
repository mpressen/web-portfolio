$(document).ready(function(){
    if (navigator.geolocation){
        navigator.geolocation.getCurrentPosition(sendPosition);
    }
    else{
        console.log("navigator too old for geolaction by api geolocation.");
    }
});

function sendPosition( position )
{
    let str = positionToJson(position.coords);
    $("#latitude").val(position.coords.latitude);
    $("#longitude").val(position.coords.longitude);
}

function positionToJson( coords )
{
    let pos = {latitute: coords.latitude, longitude: coords.longitude, accuracy: coords.accuracy};
    return JSON.stringify(pos);
}