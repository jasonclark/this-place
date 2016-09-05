<?php
//Testing the location APIs
//worldcat api http://oclc.org/developer/documentation/worldcat-search-api/using-api
//http://openlibrary.org/developers
//flickr.com api docs at http://www.flickr.com/services/api/
//geonames api docs at http://www.geonames.org/export/web-services.html
//youtube api docs at https://developers.google.com/youtube/

//assign value for title of page
$pageTitle = 're: This Place - Location Matters';
$subTitle = 'MSU Library';
//declare filename for additional stylesheet variable - default is "none"
$customCSS = 'global.css';
//create an array with filepaths for multiple page scripts - default is meta/scripts/global.js
$customScript[0] = './meta/scripts/global.js';

//functions to convert Atom feed date format 2008-11-03T21:30:06Z to "X seconds/minutes/hours/days ago"
//dependencies: used in youtube api routine to convert published dates to hours/minutes ago

function time_since($your_timestamp) {
    $unix_timestamp = strtotime($your_timestamp);
    $seconds = time() - $unix_timestamp;
    $minutes = 0;
    $hours = 0;
    $days = 0;
    $weeks = 0;
    $months = 0;
    $years = 0;
    if ( $seconds == 0 ) $seconds = 1;
    if ( $seconds> 60 ) {
        $minutes =  $seconds/60;
    } else {
        return add_s($seconds,'second');
    }

    if ( $minutes >= 60 ) {
        $hours = $minutes/60;
    } else {
        return add_s($minutes,'minute');
    }

    if ( $hours >= 24) {
        $days = $hours/24;
    } else {
        return add_s($hours,'hour');
    }

    if ( $days >= 7 ) {
        $weeks = $days/7;
    } else {
        return add_s($days,'day');
    }

    if ( $weeks >= 4 ) {
        $months = $weeks/4;
    } else {
        return add_s($weeks,'week');
    }

    if ( $months>= 12 ) {
        $years = $months/12;
        return add_s($years,'year');
    } else {
        return add_s($months,'month');
    }

}

function add_s($num,$word) {
    $num = floor($num);
    if ( $num == 1 ) {
        return $num.' '.$word.' ago';
    } else {
        return $num.' '.$word.'s ago';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title><?php echo($pageTitle); ?> : Montana State University Libraries</title>
<meta name="description" content="Search results page for This-Place, location-based app that suggests items of interest from local context"/>
<link rel="alternate" type="application/rss+xml" title="MSU Libraries: Tools" href="http://feeds.feedburner.com/msulibrarySpotlightTools" />
<?php
if ($customCSS != 'none') {
?>
<link href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/meta/styles/<?php echo $customCSS; ?>" rel="stylesheet"/>
<?php
}
?>
</head>
<body class="<?php if(!isset($_GET['view'])) { echo 'default'; } else { echo $_GET['view']; } ?>">
<header>
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
<main>
  <ul id="nav">
    <li><a href="#read">Read (worldcat)</a></li>
    <li><a href="#see">See (flickr)</a></li>
    <li><a href="#learn">Learn (wikipedia)</a></li>
    <li><a href="#watch">Watch (youtube)</a></li>
    <li><a href="#weather">Weather (forecast.io)</a></li>
    <li><a href="#street">Street (google street view)</a></li>
    <!--<li><a href="#hear">Hear (soundcloud)</a></li>-->
    <!--<li><a href="#talk">Talk (twitter)</a></li>-->
    <li><a title="convert this page to a PDF" href="http://pdfcrowd.com/url_to_pdf/?footer_text=source%20%u%20page%20%p%20of%20%n">Snapshot (pdf)</a></li>
  </ul>
<?php
//set default value for query to Worldcat Search API
$q = isset($_REQUEST['q']) ? trim(htmlentities(strip_tags($_REQUEST['q']))) : 'montana';
//set default value for location to Worldcat Search API
$location = isset($_REQUEST['loc']) ? strip_tags((int)$_REQUEST['loc']) : '59715';
//set default number of results to return from Worldcat Search API
$count = isset($_REQUEST['count']) ? strip_tags((int)$_REQUEST['count']) : '5';
//set default number for starting record number to return from Worldcat Search API
$start = isset($_REQUEST['start']) ? strip_tags((int)$_REQUEST['start']) : '1';

//set base url for our opensearch request to Worldcat Search API
$base = 'http://worldcat.org/webservices/catalog/search/worldcat/opensearch?';
//$base = 'http://www.worldcat.org/webservices/catalog/search/sru?query=srw.kw+all+"'.trim(strip_tags(urlencode($topic))).'"+and+srw.su+all+"'.trim(strip_tags(urlencode($topic))).'"+and+srw.yr+<%3D+"'.date('Y').'"&amp;';
//$base = 'http://www.worldcat.org/webservices/catalog/search/sru?query=srw.su+all+"'.trim(strip_tags(urlencode($q))).'"&amp;';

$params = array(
  'wskey' => 'ADD-API-KEY-HERE', //worldcat API key
  'servicelevel' => 'full', //worldcat api service level
  'format' => 'atom', //type of format to output
  'cformat' => 'mla', //type of citation format to output
  'start' => $start, //record result number to start from
  'count' => $count, //number of results to return
  'q' => trim(strip_tags($q)), //query to search
  //'version' => '1.1', //worldcat api version
  //'operation' => 'searchRetrieve', //worldcat api operation
  //'recordSchema' => 'info:srw/schema/1/dc', //type of record schema to output
  //'maximumRecords' => $count, //number of results to return
  //'startRecord' => $start, //record result number to start from
  //'recordPacking' => 'xml', //type of format to output
  //'sortKeys' => 'Date,,0', //sort parameters - this is date descending; other options: relevance, Author, Title, Date, Score, LibraryCount
  //'resultSetTTL' => '300', //default set results
  //'query' => 'srw.kw+all+'.$q.'+and+srw.su+all+'.$q.',
  //all possible options are documented at http://worldcat.org/devnet/wiki/SearchAPIDetails
);

//echo $base.http_build_query($params);
//build request, encode entities (using http_build_query), and send to Worldcat Search API
$request = simplexml_load_file($base.http_build_query($params));

//echo $base.http_build_query($params);

//prepare opensearch namespace for parsing
$opensearch = $request->children('http://a9.com/-/spec/opensearch/1.1/');

$subtitle = $request->subtitle;
$totalResults = $opensearch->totalResults;

if ($totalResults > 0):

?>
<a name="read"></a>
<h2 class="subHeading">READ (from Worldcat)<br />
<?php echo $count; ?> out of a possible <?php echo $totalResults; ?> matches for your query <strong>"<?php echo urldecode($q); ?>"</strong></h2>

<ol>
<?php
//parse returned data elements from api call and display as html

foreach ($request->entry as $entry) {
	//prepare dublin core namespace for parsing
	$dc = $entry->children('http://purl.org/dc/elements/1.1/');
	//prepare oclc namespace for parsing
	$oclc = $entry->children('http://purl.org/oclc/terms/');
	$title = htmlentities($entry->title);
	$creator = htmlentities($entry->author->name);
	$cite = htmlentities($entry->content);
	$url = $entry->id;
	$oclcNumber = $oclc->recordIdentifier;
		//get isbn
		if ($dc->identifier != '') {
			$isbn = explode(":", $dc->identifier[0]);
			$isbn = $isbn[2];
		} else {
			$isbn = 'not available';
			$thumbnail = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/meta/img/thumbnail-default.gif';
		}

		if ($isbn != 'not available') {
			$remoteImageUrl = 'https://syndetics.com/index.aspx?isbn='.$isbn.'/sc.jpg';
			//$remoteImageUrl = 'http://covers.openlibrary.org/b/isbn/'.$isbn.'-S.jpg';
			list($width, $height) = getimagesize($remoteImageUrl);
			//echo $width;
			if ($width > 30){
				//thumbnail available
				$thumbnail = $remoteImageUrl;
			}else{
    			//set default thumbnail
				$thumbnail = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/meta/img/thumbnail-default.gif';
			}
		}

?>
	<li>
	<img src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>"/>
        <p><a title="<?php echo title; ?>" href="<?php echo $url; ?>"><?php echo $title; ?></a></p>
        <p>
	<?php echo html_entity_decode($cite); ?>
	ISBN: <?php echo $isbn; ?><br />
        OCLC #: <?php echo $oclcNumber; ?>
        </p>
	</li>

<?php
}//close foreach loop
?>
</ol>
<?php
else: //show no results message
?>
<h2 class="subHeading">No Worldcat.org results for <strong><?php urldecode($q); ?></strong>.</h2>
<p class="control"><a href="<?php echo htmlentities(strip_tags(basename(__FILE__))); ?>" class="refresh">Reset the page</a></p>
<?php
endif;
?>

<?php
//set base url for our opensearch request to Worldcat Search API
$base = 'https://api.flickr.com/services/rest/?';

$params = array(
  'method' => 'flickr.photos.search', //flickr api request method
  'api_key' => 'ADD-API-KEY-HERE', //flickr API key
  'extras' => 'url_sq,url_m,url_z,url_l', //type of image format to output
  'page' => $start, //record result number to start from
  'per_page' => $count, //number of results to return
  'text' => trim(strip_tags($q)), //query to search
  //'version' => '1.1', //flickr api version
  //'secret' => '977f80b7e4bbc7c6', //flickr api secret for this app
  //'format' => 'atom', //type of format to output
  //http://www.flickr.com/services/api/
);

//echo $base.http_build_query($params);
//build request, encode entities (using http_build_query), and send to Worldcat Search API
$request = simplexml_load_file($base.http_build_query($params));

//echo $base.http_build_query($params);

$totalResults = $request->photos[total];

if ($totalResults > 0):
?>
<a name="see"></a>
<h2 class="subHeading">SEE (from Flickr)<br />
<?php echo $count; ?> out of a possible <?php echo $totalResults; ?> matches for your query <strong>"<?php echo urldecode($q); ?>"</strong></h2>
<ol>

<?php
//parse returned data elements from api call and display as html
foreach ($request->photos->photo as $entry) {
	$title = $entry['title'];
	$thumbnail = $entry['url_sq'];
	$id = $entry['id'];
	$image = $entry['url_z'];
?>

	<li>
	<img src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>"/>
        <p><a title="<?php echo title; ?>" href="<?php echo $image; ?>"><?php echo $title; ?></a></p>
        <p>id: <?php echo $id; ?></p>
	</li>

<?php
}//close foreach loop
?>
</ol>

<?php
else: //show no results message
?>
<h2 class="subHeading">No Flickr.com results for <strong><?php urldecode($q); ?></strong>.</h2>
<p class="control"><a href="<?php echo htmlentities(strip_tags(basename(__FILE__))); ?>" class="refresh">Reset the page</a></p>
<?php
endif;
?>

<?php
//set default value for latitude to geonames API
$lat = isset($_REQUEST['lat']) ? htmlentities(strip_tags($_REQUEST['lat'])) : '45.68346';
//set default value for latitude to geonames API
$lng = isset($_REQUEST['lng']) ? htmlentities(strip_tags($_REQUEST['lng'])) : '-111.050499';
//set default value for geonames user, enables use of geonames web service - http://www.geonames.org/manageaccount
$user = isset($_GET['user']) ? htmlentities(strip_tags($_GET['user'])) : 'ADD-GEONAMES-USER-HERE';
//set default search radius for geonames API
$radius = isset($_GET['radius']) ? $_GET['radius'] : '15';

//set base url for request to geonames wikipedia API
$base = 'http://api.geonames.org/findNearbyWikipedia?';

$params = array(
  'lat' => $lat, //latitude setting
  'lng' => $lng, //longitude setting
  'username'=> $user, //geonames username
  'maxRows' => $count, //number of results to return
  'radius' => $radius, //number of results to return
  //'version' => '1.1', //api version
  //geonames api docs at http://www.geonames.org/export/ws-overview.html
);

//build request, encode entities (using http_build_query), and send to audioboo.fm API
$request = simplexml_load_file($base.http_build_query($params));

//echo $base.http_build_query($params);

if (!isset($request->geonames->status)):
?>
<a name="learn"></a>
<h2 class="subHeading">Learn (from Geonames Wikipedia API)<br />
<?php echo $count; ?> of the Wikipedia articles matching your query <strong>"<?php echo urldecode($q); ?> (<?php echo $radius; ?> mile radius)"</strong></h2>
<ol>

<?php
//parse returned data elements from api call and display as html
foreach ($request->entry as $entry) {
	$title = $entry->title;
	$description = $entry->summary;
	$url = $entry->wikipediaUrl;
	$type = strlen($entry->feature) > 2 ? $entry->feature : 'not available';
	$lat = $entry->lat;
	$lng = $entry->lng;
	$distance = $entry->distance;
?>

	<li>
        <p><a title="<?php echo title; ?>" href="<?php echo $url; ?>"><?php echo $title; ?></a></p>
        <p><?php echo $description; ?></p>
        <p>location: <?php echo $lat; ?>, <?php echo $lng; ?></p>
	<p><img alt="<?php echo title; ?>" src="https://maps.google.com/maps/api/staticmap?center=<?php echo $lat; ?>,<?php echo $lng; ?>&zoom=13&size=320x320&markers=color:blue|<?php echo $lat; ?>,<?php echo $lng; ?>&mobile=true&sensor=false" /></p>
        <p>type: <?php echo $type; ?></p>
        <p>* About <?php echo $distance; ?> miles from you</p>
	</li>

<?php
}//close foreach loop
?>
</ol>

<?php
else: //show no results message
?>
<h2 class="subHeading">No geonames wikipedia results for <strong><?php urldecode($q); ?></strong>.</h2>
<p class="control"><a href="<?php echo htmlentities(strip_tags(basename(__FILE__))); ?>" class="refresh">Reset the page</a></p>
<?php
endif;
?>

<?php
//set default value for API version
$version = isset($_GET['v']) ? strip_tags((int)$_GET['v']) : 'v3';
//set default value for latitude to youtube API
$lat = isset($_REQUEST['lat']) ? strip_tags(floatval($_REQUEST['lat'])) : '45.68346';
//set default value for latitude to youtube API
$lng = isset($_REQUEST['lng']) ? strip_tags(floatval($_REQUEST['lng'])) : '-111.050499';
//set default search radius for youtube API
$radius = isset($_GET['radius']) ? $_GET['radius'] : '15mi';
//set default resource type for youtube API (video,channel,playlist)
$type = isset($_GET['type']) ? $_GET['type'] : 'video';

//set base url for our opensearch request to youtube.com data API
$base = 'https://www.googleapis.com/youtube/'.$version.'/search?';

$params = array(
  'key' => 'ADD-API-KEY-HERE', // ADD YOUR_YOUTUBE_API_KEY_HERE
  'part' => 'id,snippet', //part parameter specifies search resource properties that the API response will include
  'location' => "$lat,$lng", //latitude setting,longitude setting
  'locationRadius' => $radius,//location-radius setting
  'type' => $type, //default resource type
  'start-index' => $start, //record result number to start from
  'max-results' => $count, //number of results to return
  'q' => $q, //query to search
  //youtube.com api docs at https://developers.google.com/youtube/v3
);

echo $base.http_build_query($params);

//call API and get data
$request = file_get_contents($base.http_build_query($params));

if ($request === FALSE) {
  //API call failed, display message to user
  echo '<p><strong>It looks like we can\'t communicate with the API at the moment.</strong></p>'."\n";
  exit();
}

//create json object(s) out of response from API; set to "true" to turn response into an array
//$result = json_decode($request,true);
$result = json_decode($request);

//get values in json data for number of search results returned
$totalResults = $result->pageInfo->totalResults;

if ($totalResults > 0):
?>
<a name="watch"></a>
<h2 class="subHeading">Watch (from youtube.com)<br />
<?php echo $count; ?> out of a possible <?php echo $totalResults; ?> matches for your query <strong>"<?php echo urldecode($q); ?>"</strong></h2>
<ol>
<?php
//parse returned data elements from api call and display as html
foreach ($result->items as $item) {
	$title = htmlentities($item->snippet->title);
	$id = $item->id->videoId;
	//get published date
	$timestamp = strtotime($item->snippet->publishedAt);
	$uploaded = date('M j, Y', $timestamp);
	$image = $item->snippet->thumbnails->default->url;
	//get video description
	$description = isset($item->snippet->description) ? $item->snippet->description : 'No description available.';
	$channel = $item->snippet->channelTitle;

?>
	<li>
	<img src="<?php echo $image; ?>" alt="<?php echo $title; ?>"/>
        <p><a title="<?php echo title; ?>" href="https://www.youtube.com/watch?v=<?php echo $id; ?>"><?php echo $title; ?></a></p>
        <p><?php echo $description; ?></p>
        <p>date: <?php echo $uploaded; ?></p>
        <p>channel: <?php echo $channel; ?></p>
        <p>id: <?php echo $id; ?></p>
        <p>url: https://www.youtube.com/watch?v=<?php echo $id; ?></p>
        <p>
	<iframe itemprop="video" width="480" height="360"
		src="https://www.youtube.com/embed/<?php echo $id; ?>?hd=1&modestbranding=1&version=3&autohide=1&showinfo=0&rel=0"
		frameborder="0"
		allowfullscreen>
	</iframe>
        </p>
	</li>

<?php
}//close foreach loop
?>
</ol>

<?php
else: //show no results message
?>
<h2 class="subHeading">No youtube.com results for <strong><?php urldecode($q); ?></strong>.</h2>
<p class="control"><a href="<?php echo htmlentities(strip_tags(basename(__FILE__))); ?>" class="refresh">Reset the page</a></p>
<?php
endif;
?>

<?php
//forecast.io api docs at https://developer.forecast.io/docs/v2

//ADD YOUR_FORECAST.IO_API_KEY_HERE
$key = 'ADD-API-KEY-HERE';
//set default value for latitude to youtube API
$lat = isset($_REQUEST['lat']) ? strip_tags(floatval($_REQUEST['lat'])) : '45.68346';
//set default value for latitude to youtube API
$lng = isset($_REQUEST['lng']) ? strip_tags(floatval($_REQUEST['lng'])) : '-111.050499';

//set base url for our opensearch request to youtube.com data API
$apiURL = 'https://api.forecast.io/forecast/'.$key.'/'.$lat.','.$lng.'?exclude=minutely,hourly';

//for testing purposes show actual request to API - REMOVE when finished
//echo $apiURL;

//call API and get data
$request = file_get_contents($apiURL);

if ($request === FALSE) {
  //API call failed, display message to user
  echo '<p><strong>It looks like we can\'t communicate with the API at the moment.</strong></p>'."\n";
  exit();
}

//create json object(s) out of response from API; set to "true" to turn response into an array
//$result = json_decode($request,true);
$result = json_decode($request);

if (strlen($result->latitude) > 2):
?>
<a name="weather"></a>
<a name="weather"></a>
<h2 class="subHeading">Weather (from forecast.io)<br />
Current Weather for your query <strong>"<?php echo urldecode($q); ?>"</strong></h2>
<dl>
<?php
//dependency on skycons.js to make animated weather icons appear - https://github.com/darkskyapp/skycons

//parse returned data elements from api call and display as html
$summary = $result->currently->summary;
$icon = $result->currently->icon;
$temperature = $result->currently->temperature;
$wind = $result->currently->windSpeed;
$pressure = $result->currently->pressure;
$precipProbability = $result->currently->precipProbability;
$cloudCover = $result->currently->cloudCover;
?>
	<dt>Current Conditions:</dt>
	<dd><figure><canvas id="<?php echo $icon; ?>" width="64" height="64"></canvas></figure></dd>
        <dd>summary: <?php echo $summary; ?></dd>
	<dd>icon shorthand: <?php echo $icon; ?></dd>
	<dd>temperature: <?php echo $temperature; ?></dd>
	<dd>wind: <?php echo $wind; ?></dd>
	<dd>pressure: <?php echo $pressure; ?></dd>
	<dd>chance of precip: <?php echo $precipProbability; ?></dd>
	<dd>cloud cover: <?php echo $cloudCover; ?></dd>
<?php
//parse returned data elements from api call and display as html
$summary = $result->daily->summary;
$icon = $result->daily->icon;
$daySummary = $result->daily->data[0]->summary;
$dayIcon = $result->daily->data[0]->icon;
$highTemperature = $result->daily->data[0]->temperatureMax;
$lowTemperature = $result->daily->data[0]->temperatureMin;
$wind = $result->daily->data[0]->windSpeed;
$pressure = $result->daily->data[0]->pressure;
$precipProbability = $result->daily->data[0]->precipProbability;
$cloudCover = $result->daily->data[0]->cloudCover;
?>
	<dt>Tomorrow Conditions:</dt>
	<dd><figure><canvas id="<?php echo $dayIcon; ?>" width="64" height="64"></canvas></figure></dd>
        <dd>tomorrow summary: <?php echo $daySummary; ?></dd>
        <dd>icon shorthand: <?php echo $dayIcon; ?></dd>
	<dd>high temperature: <?php echo $highTemperature; ?></dd>
	<dd>low temperature: <?php echo $lowTemperature; ?></dd>
	<dd>wind: <?php echo $wind; ?></dd>
	<dd>pressure: <?php echo $pressure; ?></dd>
	<dd>chance of precip: <?php echo $precipProbability; ?></dd>
	<dd>cloud cover: <?php echo $cloudCover; ?></dd>
	<dt>Weekly Conditions:</dt>
	<dd><figure><canvas id="<?php echo $icon; ?>" width="64" height="64"></canvas></figure></dd>
        <dd>summary: <?php echo $summary; ?></dd>
<?php
//parse returned data elements from api call and display as html
//$alert = $result->alerts->title[0];
//foreach ($result->alerts as $item) {
//alerts are possible, need to finish this code
?>
    <!--<dt>Alerts:</dt>
    <dd>alert: <?php //echo $description; ?></dd>-->
<?php
//}//close foreach loop
?>
<script src="./meta/scripts/skycons.js"></script>
<script>
      var icons = new Skycons(),
          list  = [
            "clear-day", "clear-night", "partly-cloudy-day",
            "partly-cloudy-night", "cloudy", "rain", "sleet", "snow", "wind",
            "fog"
          ],
          i;

      for(i = list.length; i--; )
        icons.set(list[i], list[i]);

      icons.play();
</script>
</dl>
<?php
else: //show no results message
?>
<h2 class="subHeading">No forecast.io results for <strong><?php urldecode($q); ?></strong>.</h2>
<p class="control"><a href="<?php echo htmlentities(strip_tags(basename(__FILE__))); ?>" class="refresh">Reset the page</a></p>
<?php
endif;
?>

<?php
//set default key for API use - https://developers.google.com/maps/documentation/streetview/#introduction
$key = isset($_GET['key']) ? htmlentities(strip_tags($_GET['key'])) : null;
//$version = isset($_GET['v']) ? strip_tags((int)$_GET['v']) : 'v3';
//set default value for latitude to Google Street View Image API
$lat = isset($_REQUEST['lat']) ? strip_tags(floatval($_REQUEST['lat'])) : '45.68346';
//set default value for latitude to Google Street View Image API
$lng = isset($_REQUEST['lng']) ? strip_tags(floatval($_REQUEST['lng'])) : '-111.050499';

//set base url for our request to Google Street View Image API
$base = 'https://maps.googleapis.com/maps/api/streetview?';

$params = array(
  'key' => 'ADD-API-KEY-HERE', // ADD YOUR_GOOGLE_MAPS_API_KEY_HERE
  'size' => '600x300', //set image size
  'sensor' => 'true', //set whether or not the request came from a device using a location sensor
  'location' => "$lat,$lng", //latitude setting,longitude setting
  //'location-radius' => '100km',//location-radius setting
  //Google Street View Image API docs at https://developers.google.com/maps/documentation/streetview/
);

//echo $base.http_build_query($params);
$imageURL = $base.http_build_query($params);

if (getimagesize($imageURL) !== false) :
?>
<a name="street"></a>
<h2 class="subHeading">Street View (from Google Street View Image API)<br />
Current image for your query <strong>"<?php echo urldecode($q); ?>"</strong></h2>
<figure><img alt="street view image for <?php echo urldecode($q); ?>" src="<?php echo $imageURL; ?>" /></figure>

<?php
else: //show no results message
?>
<h2 class="subHeading">No Google Street View results for <strong><?php echo urldecode($q); ?></strong>.</h2>
<p class="control"><a href="<?php echo htmlentities(strip_tags(basename(__FILE__))); ?>" class="refresh">Reset the page</a></p>
<?php
endif;
?>

<!--
<a name="hear"></a>
<h2 class="subHeading">Hear (from SoundCloud)<br />
<ol>
<li>
<audio id="player" controls>-->
  <!--<source src="" />--> <!-- chrome/safari plays this, Firefox ignores -->
  <!-- <source src="audio.ogg" /> Firefox will play this -->
  <!--<p>Your browser cannot play this audio</p>  IE-->
<!--</audio>
</li>
</ol>-->

<?php
/* TURNED off Twitter API until oauth for API version 2 is added
//http://search.twitter.com/search.atom?geocode=45.6603844,-111.0373621,30mi&page=1&rpp=5

//set default value for API version
//$version = isset($_GET['v']) ? $_GET['v'] : '2';
//set default format for API version
$format = isset($_GET['form']) ? $_GET['form'] : 'atom';
//set default value for latitude to twitter search API
$lat = isset($_REQUEST['lat']) ? $_REQUEST['lat'] : '45.68346';
//set default value for latitude to twitter search API
$lng = isset($_REQUEST['lng']) ? $_REQUEST['lng'] : '-111.050499';
//set default search radius for API version
$radius = isset($_GET['radius']) ? $_GET['radius'] : '30mi';

//set base url for our opensearch request to youtube.com data API
$base = 'http://search.twitter.com/search.'.$format.'?';

$params = array(
  //'v' => $version, //api version
  'geocode' => "$lat,$lng,$radius", //latitude setting,longitude setting
  'page' => $start, //record result number to start from
  'rpp' => $count, //number of results to return
  //'q' => trim(strip_tags($q)), //query to search
  //twitter.com search api docs at http://dev.twitter.com/doc/get/search
);

//build request, encode entities (using http_build_query), and send to audioboo.fm API
$request = simplexml_load_file($base.http_build_query($params));

//echo $base.http_build_query($params);

//prepare opensearch namespace for parsing
$opensearch = $request->children('http://a9.com/-/spec/opensearch/1.1/');

$itemsPerPage = $opensearch->itemsPerPage;

if ($itemsPerPage > 0):
?>
<a name="talk"></a>
<h2 class="subHeading">Talk (from twitter.com)<br />
<?php echo $count; ?> of the latest tweets matching your query <strong>"<?php echo urldecode($q); ?> (<?php echo $radius; ?> radius)"</strong></h2>
<ol>

<?php
//parse returned data elements from api call and display as html
foreach ($request->entry as $entry) {
	$title = $entry->title;
	$creator = $entry->author->name;
	$content = html_entity_decode($entry->content);
	//prepare opensearch namespace for parsing
	$google = $entry->children('http://base.google.com/ns/1.0');
	$location = $google->location;
	$id = $entry->id;
	$published = $entry->published;
	$tweet = $entry->link[0]->attributes()->href;
	$thumbnail = $entry->link[1]->attributes()->href;
*/
?>
<!--
	<li>
	<img src="<?php //echo $thumbnail; ?>" alt="<?php //echo $creator; ?>"/>
        <p><?php //echo $creator; ?></p>
        <p><?php //echo $content; ?></p>
        <p><?php //echo time_since($published); ?></p>
        <p>location: <?php //echo $location; ?></p>
        <p>original tweet: <?php //echo $tweet; ?></p>
        <p>id: <?php //echo $id; ?></p>
	</li>
-->
<?php
//}//close foreach loop
?>
<!--</ol>-->

<?php
//else: //show no results message
?>
<!--<h2 class="subHeading">No twitter.com results for <strong><?php //echo urldecode($q); ?></strong>.</h2>
<p class="control"><a href="<?php //echo htmlentities(strip_tags(basename(__FILE__))); ?>" class="refresh">Reset the page</a></p>-->
<?php
//endif;
?>
</main>
</div><!-- end div main -->
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
