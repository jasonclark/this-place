<?php
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
<style type="text/css" media="screen, projection, handheld">
<!-- @import url("./meta/styles/default.css"); -->
<!--
<?php if ($customCSS != 'none') {
	echo '@import url("'.dirname($_SERVER['PHP_SELF']).'/meta/styles/'.$customCSS.'");'."\n";
}
?>
-->
</style>
<?php
if ($customScript) {
  $counted = count($customScript);
  for ($i = 0; $i < $counted; $i++) {
   echo '<script type="text/javascript" src="'.$customScript[$i].'"></script>'."\n";
  }
}
?>
</head>
<body class="<?php if(!isset($_GET['view'])) { echo 'code'; } else { echo $_GET['view']; } ?>">
<h1><?php echo $pageTitle; ?><span>: <?php echo $subTitle; ?></span><small>(working code and proof of concepts)</small></h1>
<div class="container">
    <ul id="tabs">
        <li id="tab1"><a href="./index.php">Demo App</a></li>
        <li id="tab2"><a href="./what.php">What is this?</a></li>
        <li id="tab3"><a href="./code.php">View Code</a></li>
    </ul><!-- end tabs unordered list -->
	<div class="main">
		<h2 class="mainHeading">CODE: local stuff search app using Open Library API for thumbnails, Google Ajax Search API for Geolocation, and Worldcat Basic Search API for Item Results</h2>
		<p><a href="index.txt" title="code for geolocation and search form">Geolocation and search form (Javascript and HTML)</a></p>
		<p><a href="search.txt" title="code for search form and php">Search logic (PHP)</a></p>
		<!--<p><a href="amazon-api-class.txt" title="code for thumbnails">Amazon API Library for Thumbnails (PHP)</a></p>-->
	</div><!-- end div main -->
</div><!-- end container div -->
</body>
</html>
