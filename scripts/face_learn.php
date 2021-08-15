<?php
$learn_dir = '/home/user/.homeassistant/deepstack/learn';

$d = opendir($learn_dir);


while($face = readdir($d)) {
	if(!preg_match('/^([a-z]*)$/i', $face))
		continue;

	learn_face($face);
}

closedir($d);


function learn_face($name) {
	global $learn_dir;

	echo "Learning {$name}...\n";

	$baseDir = $learn_dir.'/'.$name;
	$postData = array('userid' => $name);

	$d = opendir($baseDir);
	$i = 1;

	while($img = readdir($d)) {
		if($img=='.' || $img=='..')
			continue;

		$postData['image'.$i] = new CURLFile($baseDir.'/'.$img);
		$i++;
	}

	closedir($d);

	$start = microtime(true);

	$ch = curl_init('http://localhost:5000/v1/vision/face/register');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);

	echo "Done in ".round(microtime(true)-$start, 3)."s, result: ".$result."\n";
	system('docker restart deepstack');
	sleep(1);
}
