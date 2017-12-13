document.querySelector('#is_it_snowing_get_location').addEventListener("click", function(e) {
	
	navigator.geolocation.getCurrentPosition( function( location ) {
		
		document.querySelector('#weather_lat').value = location.coords.latitude;
		document.querySelector('#weather_lng').value = location.coords.longitude;
		
		
	}, function() {
		alert(isItSnowingI18N.failedToGetLocation);
	}, { enableHighAccuracy: true } );

});