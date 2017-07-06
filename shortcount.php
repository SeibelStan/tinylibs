<?php

function shortcount($count) {
	$l = strlen($count);
	$mode = '';
	
	if($l > 3) {
		$mode = 'k';
	}
	if($l > 6) {
		$mode = 'M';
	}
	if($l > 9) {
		$mode = 'G';
	}
	if($l > 12) {
		$mode = 'T';
	}
	if($l > 15) {
		$mode = 'P';
	}

	switch($mode) {
		case 'k': {
			$digs = round($count / 1000, 1);
			break;
		}
		case 'M': {
			$digs = round($count / 1000000, 1);
			break;
		}
		case 'G': {
			$digs = round($count / 1000000000, 1);
			break;
		}
		case 'T': {
			$digs = round($count / 1000000000000, 1);
			break;
		}
		case 'P': {
			$digs = round($count / 1000000000000000, 1);
			break;
		}
		default: {
			$digs = $count;
		}
	}
	return $digs . $mode;
}

echo shortcount(64400);
echo shortcount(5705400);
echo shortcount(6785705400);