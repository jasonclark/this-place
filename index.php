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

//assign value for title of page, limit to 60-70 characters - for use in title and og:title tags
$pageTitle = 're: This Place - Location Matters';
$subTitle = 'MSU Library';
//assign value for description of page, limit to 155 characters - for use in meta description and og:description tags
$pageDescription = 'This-Place is a location-based app that suggests items of interest from local context.';
//get file last modified date for use in Schema.org date properties
$pageLastModified = date ('c', getlastmod());

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
<meta name="description" content="<?php echo $pageDescription; ?>"/>
<meta property="og:title" content="<?php echo $pageTitle; ?>"/>
<meta property="og:description" content="<?php echo $pageDescription; ?>"/>
<meta property="og:image" content="<?php echo $protocol.$server.$path; ?>/meta/img/clark-share-default.png"/>
<meta property="og:url" content="<?php echo $protocol.$server.$path; ?>/"/>
<meta property="og:type" content="website"/>
<meta name="twitter:creator" property="og:site_name" content="@jaclark"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:site" content="http://www.jasonclark.info"/>
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
<body class="<?php if ($fileNameOnly == 'index' || $fileNameOnly == 'search') { echo 'default'; } else { echo $fileNameOnly; } ?>">
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
<script>
function initMap() {
  var map = new google.maps.Map(document.getElementById('map-canvas'), {
    center: {lat: 41.850033, lng: -87.6500523},
    disableDefaultUI: true,
    zoom: 6
  });

  // try HTML5 geolocation.
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var pos = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };

      var s = document.querySelector('#status');
      if (s.className == 'success') {
        // not sure why we're hitting this twice in FF, I think it's to do with a cached result coming back
        return;
      }
      s.innerHTML = "found you!";
      s.className = 'success';

      var infoWindow = new google.maps.InfoWindow({
        map: map
      });
      infoWindow.setPosition(pos);
      infoWindow.setContent('You are here.');
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
      handleLocationError(true, infoWindow, map.getCenter());
    });
  } else {
    // browser doesn't support Geolocation
    handleLocationError(false, infoWindow, map.getCenter());
  }
}

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
  infoWindow.setPosition(pos);
  infoWindow.setContent(browserHasGeolocation ?
    'Error: The Geolocation service failed.' :
    'Error: Your browser doesn\'t support geolocation.');
}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=ADD-YOUR-API-KEY-HERE&callback=initMap"></script>
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
