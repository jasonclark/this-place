<?php
//Testing the W3c geolocation API and Worldcat Search API
//http://oclc.org/developer/documentation/worldcat-search-api/using-api
//assign value for title of page
$pageTitle = 're: This Place - Location Matters';
$subTitle = 'MSU Libraries';
//declare filename for additional stylesheet variable - default is "none"
$customCSS = 'global.css';
//create an array with filepaths for multiple page scripts - default is meta/scripts/global.js
$customScript[0] = './meta/scripts/global.js';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title><?php echo($pageTitle); ?> : Montana State University Libraries</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="alternate" type="application/rss+xml" title="MSU Libraries: Tools" href="http://feeds.feedburner.com/msulibrarySpotlightTools" />
<?php
if ($customCSS != 'none') {
echo '<link href="'.dirname($_SERVER['PHP_SELF']).'/meta/styles/'.$customCSS.'" media="screen" rel="stylesheet" type="text/css"/>'."\n";
}
?>
</head>
<body class="<?php if(!isset($_GET['view'])) { echo 'default'; } else { echo $_GET['view']; } ?>">
<h1><?php echo $pageTitle; ?><span>: <?php echo $subTitle; ?></span><small>(working code and proof of concepts)</small></h1>
<div class="container">
    <ul id="tabs">
        <li id="tab1"><a href="./index.php">Demo App</a></li>
        <li id="tab2"><a href="./what.php">What is this?</a></li>
        <li id="tab3"><a href="./code.php">View Code</a></li>
    </ul><!-- end tabs unordered list -->
	<div class="main">
  <h2>Getting your location: <span id="status">checking...</span></h2>
  <div id="cantfindyou"></div>
  <div id="map-canvas" style="width:500px;height:300px;"></div>
  <p>Do you want us to use the location of <strong><span id="loc"></span></strong> to find local interest items for you?</p>
	<form id="searchBox" method="get" action="./search.php">
	<fieldset>
	<label for="q">Search</label>
	<input type="hidden" name="lat" id="lat" value="" />
	<input type="hidden" name="lng" id="lng" value="" />
	<input type="text" maxlength="200" name="q" id="q" tabindex="1" value="" />
	<button type="submit" class="button">Search</button>
	</fieldset>
	</form>
	</div><!-- end div main -->
</div><!-- end container div -->
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
<script>

var map;
var geocoder;

function initialize() {
  var mapOptions = {
    zoom: 6,
		disableDefaultUI: true
  };
  map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);

  //try HTML5 geolocation
  if(navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var pos = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);

      var s = document.querySelector('#status');
				if (s.className == 'success') {
					// not sure why we're hitting this twice in FF, I think it's to do with a cached result coming back
					return;
				}
				s.innerHTML = "found you!";
				s.className = 'success';

      var infowindow = new google.maps.InfoWindow({
        map: map,
        position: pos,
        content: 'You are here.'
      });

      map.setCenter(pos);

      geocoder = new google.maps.Geocoder();
      var lat = position.coords.latitude;
			var lng = position.coords.longitude;
			document.getElementById("lat").value = lat;
			document.getElementById("lng").value = lng;
	    if (geocoder) {
	    	geocoder.geocode({'latLng': pos}, function(results, status) {
	      	if (status == google.maps.GeocoderStatus.OK) {
	        	if (results[1]) {
		        	$("loc").innerHTML = results[1].formatted_address;
							document.getElementById("q").value = results[1].formatted_address.toLowerCase();
	          }
	        }
	      });
	    }
}, function() {
		handleNoGeolocation(true);
	});
	} else {
		//browser doesn't support Geolocation
		handleNoGeolocation(false);
  }
}

function handleNoGeolocation(errorFlag) {
  if (errorFlag) {
    var content = 'Error: The Geolocation service failed.';
    document.getElementById("cantfindyou").innerHTML = "Hmmm... I don't know. Good hiding!";
  } else {
    var content = 'Error: Your browser doesn\'t support geolocation.';
    document.getElementById("cantfindyou").innerHTML = "Hmmm... I don't know. Good hiding!";
  }

  var options = {
    map: map,
    position: new google.maps.LatLng(60, 105),
    content: content
  };

  var infowindow = new google.maps.InfoWindow(options);
  map.setCenter(options.position);
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>
<?php
if ($customScript) {
  $counted = count($customScript);
  for ($i = 0; $i < $counted; $i++) {
   echo '<script type="text/javascript" src="'.$customScript[$i].'" defer></script>'."\n";
  }
}
?>
</body>
</html>
