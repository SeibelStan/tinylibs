<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-type: text/plain');

$map = @$_GET['map'] ?: trim("
    1111
    1001
    1010
    1001
");

$map = preg_replace('/\s+/', "\n", $map);
$map = explode("\n", $map);

$fig = @$_GET['fig'] ?: trim("
    110
    101
    110
");
$fig = preg_replace('/\s+/', "\n", $fig);
$fig = explode("\n", $fig);

$mw = strlen($map[0]);
$mh = count($map);

$fw = strlen($fig[0]);
$fh = count($fig);

print_r([$map, $fig]);
echo "\n\n";

$end = 1;

for ($mi = 0; $mi <= $mw - $fw; $mi++) {
    for ($mj = 0; $mj <= $mh - $fh; $mj++) {

        if (!$end) {
            continue;
        }

        $check = 1;

        for ($fi = 0; $fi < $fw; $fi++) {
            for ($fj = 0; $fj < $fh; $fj++) {
                $sum = (int) $map[$mj + $fj][$mi + $fi] + (int) $fig[$fj][$fi];
                if ($sum > 1)  {
                    $check = 0;
                }
            }
        }

        if ($check) {
            $end = 0;
        }
    }
}

echo "end: $end";