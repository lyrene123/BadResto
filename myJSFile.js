function init(){
    getLocation();
}

function getLocation() {
    if (navigator.geolocation) {  //geolocation is supported by browser
	     //provide two callbacks, for success or error
        navigator.geolocation.getCurrentPosition(getPosition, getError);
    } else {   //geolocation is not supported by browser
        treatError();
    }
}

function getPosition(position) {
    //set coordinates in hidden fields and submit
    if (position.coords.accuracy > 10000) { 
        //location is not accurate with 10 km, large error is possible on desktops
        treatError();
    } else {
        document.forms['hiddenForm'].elements['latitude'].value = position.coords.latitude;
        document.forms['hiddenForm'].elements['longitude'].value = position.coords.longitude;
        
        //submit form with geolocation info 
        document.forms['hiddenForm'].submit(); 
    } 
}

function getError(error) {   //geolocation was not successful
    
    switch(error.code) {
        case error.PERMISSION_DENIED:
            document.forms['hiddenForm'].elements['error'].value = '2';
            break;
        case error.POSITION_UNAVAILABLE:
            document.forms['hiddenForm'].elements['error'].value = '3';
            break;
        case error.TIMEOUT:
            document.forms['hiddenForm'].elements['error'].value = '4';
            break;
        case error.UNKNOWN_ERROR:
            document.forms['hiddenForm'].elements['error'].value = '5';
            break;
        default:
            document.forms['hiddenForm'].elements['error'].value = '1';
    }
}

function treatError() {   //geolocation was not successful
    document.forms['hiddenForm'].elements['error'].value = '1';
	
   //submit form with error 
   document.forms['hiddenForm'].submit(); 
}
window.onload = init;
