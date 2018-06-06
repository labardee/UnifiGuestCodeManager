<?php

include dirname(__FILE__) . "/phpapi/class.unifi.php";

$config = dirname(__FILE__) . '/config.php';
$datastore =  dirname(__FILE__) . '/data.store';

if ( file_exists($config)) {
	include $config;
}
else {
	die("Config file does not exist");
}

$file = fopen($datastore, 'r');
$raw = fread($file, filesize($datastore));
$site = json_decode($raw, true);
fclose($file);

if (empty($site)) {
	$site = array();
}


$unifi = new unifiapi($controlleruser, $controllerpassword, $controllerurl, '', $controllerversion);

$unifi->login();
$result = $unifi->list_sites();

foreach ($site as $name => $loc) {
	$found = 0;
	foreach ($result as $r) {
		if ($r->desc == $name) {
			$found++;
		}
	}
	if ($found == 0) {
		unset($site[$name]);
	}
}

foreach ($result as $r) {
	if ( !isset($site[$r->desc]) ) {
		$site[$r->desc] = array();
	}
	$site[$r->desc]['name'] = $r->name;
}

$today = date('Y-m-d');

$note = "Today's wifi voucher for " . $today;

foreach ($site as $id => $data) {

	$unifi->site = $data['name'];

	if ( isset($data['code']) && !empty($data['code']) ) {
		$vouchers = $unifi->stat_voucher();
		foreach ($vouchers as $voucher) {
			if ( $data['code'] == $voucher->code ) {
				$unifi->revoke_voucher($voucher->_id);
			}
		}
	}

	$r = $unifi->create_voucher(730, 1, 0, $note);
	$site[$id]['code'] = $r[0];

}


$file = fopen($datastore, 'w');
$output = json_encode($site);
fwrite($file, $output);
fclose($file);

