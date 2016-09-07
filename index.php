<?php
//Testing the W3c geolocation API and APIs below:
//http://oclc.org/developer/documentation/worldcat-search-api/using-api
//worldcat api http://oclc.org/developer/documentation/worldcat-search-api/using-api
//http://openlibrary.org/developers
//flickr.com api docs at http://www.flickr.com/services/api/
//geonames api docs at http://www.geonames.org/export/web-services.html
//youtube api docs at https://developers.google.com/youtube/

//get and set url protocol - for <link> rel=canonical and <body> class value for #tabs
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
//set and sanitize global variables for URL construction
$server = isset($_SERVER['SERVER_NAME']) ? htmlentities(strip_tags($_SERVER['SERVER_NAME'])) : null;
$path = isset($_SERVER['PHP_SELF']) ? htmlentities(strip_tags(dirname($_SERVER['PHP_SELF']))) : null;
$fileName = isset($_SERVER['SCRIPT_NAME']) ? htmlentities(strip_tags(basename($_SERVER['SCRIPT_NAME']))) : null;
$fileNameURI = isset($_SERVER['REQUEST_URI']) ? htmlentities(strip_tags($_SERVER['REQUEST_URI'])) : null;
$fileNameOnly = isset($_SERVER['SCRIPT_NAME']) ? substr($fileName, 0, strrpos($fileName, ".")) : null;
//$fileNameOnly = isset($_SERVER['PATH_INFO']) ? pathinfo($fileName, PATHINFO_FILENAME) : null;
$fileExtension = isset($_SERVER['PATH_INFO']) ? pathinfo($fileName, PATHINFO_EXTENSION) : null;

//assign value for title of page
$pageTitle = 're: This Place - Location Matters';
$subTitle = 'MSU Library';
//declare filename for additional stylesheet variable - default is "none"
$customCSS = 'global.css';
//create an array with filepaths for multiple page scripts - default is meta/scripts/global.js
$customScript[0] = './meta/scripts/global.js';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title><?php echo($pageTitle); ?> : Montana State University Libraries</title>
<meta name="description" content="This-Place is a location-based app that suggests items of interest from local context."/>
<link rel="alternate" type="application/rss+xml" title="MSU Libraries: Tools" href="http://feeds.feedburner.com/msulibrarySpotlightTools" />
<link rel="canonical" href="<?php echo $protocol.$server.$path.'/'.$fileName; ?>"/>
<?php
if ($customCSS != 'none') {
?>
<link href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/meta/styles/<?php echo $customCSS; ?>" rel="stylesheet"/>
<?php
}
?>
</head>
<body class="<?php if($fileNameOnly != 'index') { echo $fileNameOnly; } else { echo 'default'; } ?>">
<header role="banner">
<h1><?php echo $pageTitle; ?><span>: <?php echo $subTitle; ?></span></h1>
</header>
<nav>
    <ul id="tabs">
        <li id="tab1"><a href="./index.php">Demo App</a></li>
        <li id="tab2"><a href="./what.php">What is this?</a></li>
        <li id="tab3"><a href="./code.php">View Code</a></li>
    </ul><!-- end tabs unordered list -->
</nav>
<div class="main">
<main role="main">
  <p>Getting your location: <span id="status">checking...</span></p>
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
</main>
</div><!-- end div main -->
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
<script>
var map;
var geocoder;

function initialize() {
  var mapOptions = {
    zoom: 6,
    disableDefaultUI: true
  };
  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

  //try HTML5 geolocation
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

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
        geocoder.geocode({
          'latLng': pos
        }, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            if (results[1]) {
              document.getElementById("loc").innerHTML = results[1].formatted_address;
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
  var content;
  if (errorFlag) {
    content = 'Error: The Geolocation service failed.';
    document.getElementById("cantfindyou").innerHTML = "Hmmm... I don't know. Good hiding!";
  } else {
    content = 'Error: Your browser doesn\'t support geolocation.';
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
?>
<script type="text/javascript" src="<?php echo $customScript[$i]; ?>" defer></script>
<?php
  }
}
?>
</body>
</html>
