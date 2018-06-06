
<head>


<script>
function catch_change() {
    var x = document.getElementById("site").value;
	window.location.href = '/?site=' + x; 
}
</script>

<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

</head>
<body style="font-family: 'Open Sans', sans-serif;">
<center>
<br>
<img src=/Logo.png style="width:400px"/>
<br>
<h1> Wireless Guest Portal </h1>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>


<div>

<?php

global $site;
$datastore =  dirname(__FILE__) . '/data.store';

$file = fopen($datastore, 'r');
$raw = fread($file, filesize($datastore));
$site = json_decode($raw, true);
fclose($file);

if (empty($site)) {
	die( "error, no data found");
}


if ( !isset($_GET['site']) || empty($_GET['site']) ) {		//no site parameter passed, do landing page
	landing_page();
}
elseif ( !isset($site[$_GET['site']]) ) {
	landing_page(true);			//site parameter is set but doesn't match a site we have a code for 
}
else {
	display_code($_GET['site']);	//do the needful
}


function landing_page($error = false) {
	global $site;
	echo 'Please Select a Site<br><br>';
	echo '<select id=site onchange="catch_change()" >';

	echo '<option value="">Site</option>';

	foreach ($site as $name => $data) {
		if ($name != 'Default') {
			echo '<option value="' . $name . '">' . $name . '</option>';
		}
	}

	echo '</select>';

	if ($error == true) {
		echo "<br><br><br><span style=\"color: red\"> You'll have to select a real site</span>";
	}
	
}

function display_code($site_name) {
	global $site;
	echo '<h2>';
	$clean_code = substr($site[$site_name]['code'],0, 5) . '-' . substr($site[$site_name]['code'],5);
	echo "Today's Code for  " . $site_name . " is: " . $clean_code;
	echo '</h2><br><br>';
	echo "<a href='/'> Back </a>";
}


//var_dump($site);
?>

</div>
</center>
