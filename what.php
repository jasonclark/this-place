<?php
//Testing the W3c geolocation API and APIs below:
//http://oclc.org/developer/documentation/worldcat-search-api/using-api
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
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title><?php echo($pageTitle); ?> : Montana State University Libraries</title>
<meta name="description" content="About This-Place, location-based app that suggests items of interest from local context"/>
<link rel="alternate" type="application/rss+xml" title="MSU Libraries: Tools" href="http://feeds.feedburner.com/msulibrarySpotlightTools" />
<?php
if ($customCSS != 'none') {
?>
<link href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/meta/styles/<?php echo $customCSS; ?>" rel="stylesheet"/>
<?php
}
?>
</head>
<body class="<?php if(!isset($_GET['view'])) { echo 'what'; } else { echo $_GET['view']; } ?>">
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
<main role="main>
    <h2 class="mainHeading">re: This Place - Location Matters</h2>
    <p>This is a proof of concept app and teaching demo. I wanted to illustrate how a location-based browse/search app for Worldcat data could engage patrons. Built with PHP, JavaScript, HTML, Open Library API for thumbnails, Google Ajax Search API for Geolocation, and Worldcat Basic Search API.</p>
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
