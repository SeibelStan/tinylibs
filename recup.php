<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 300);

function removeDirectory($dir) {
	if(!is_dir($dir)) {
		unlink($dir);
		return;
	}
	$files = array_diff(
		scandir($dir), array('.', '..')
	);
	foreach ($files as $file) {
		is_dir("$dir/$file") ? removeDirectory("$dir/$file") : unlink("$dir/$file");
	}
	return rmdir($dir);
}

removeDirectory('a');
mkdir('a');

header('Content-type: text/plain');
echo date('H:i:s') . "\n";
for($i = 1; $i <= 505; $i++) {
    $dir = "recup_dir.$i";
    $files = @scandir($dir);
    if(!$files) {
        continue;
    }
    $copied = 0;
    foreach($files as $file) {
        if(count($files) == 2) {
            rmdir($dir);
        }
        $path = $dir . '/' . $file;
        $ext = pathinfo($path, $options = PATHINFO_EXTENSION);
        if(in_array($ext, ['jpg', 'jpeg'])) {
            $exif = @exif_read_data($path);
            if(!$exif || !@$exif['DateTime']) {
                continue;
            }
            $dt = preg_replace('/[ :]/', '-', $exif['DateTime']);
            $year = explode('-', $dt)[0];
            if($year < 2011 || $year > 2016) {
                continue;
            }
            $iw = $exif['COMPUTED']['Width'];
            $ih = $exif['COMPUTED']['Height'];
            $maxd = max($iw, $ih);
            $mind = min($iw, $ih);
            $yearDir = "a/$year";
            if(!file_exists($yearDir)) {
                mkdir($yearDir);
            }
            $cfname = "$yearDir/{$dt}_$maxd-$mind.jpg";
            copy($path, $cfname);
            $copied++;
        }
        else {
            @unlink($path);
        }
    }
    if($copied) {
        echo "$i ($copied)\n";
    }
    else {
        removeDirectory($dir);        
    }
}
echo date('H:i:s');