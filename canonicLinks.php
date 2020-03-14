<?php

function getCanonicLink($text, $all = false) {
	$matches = [];
	if(preg_match_all('/(\w+\.\w+\S+)/', $text, $matches)) {
		if($all) {
			return $matches[0];
		}	
		else {
			return $matches[0][0];
		}
	}
	else {
		return false;
	}
}

$test1 = 'verynyasha поделился(-ась) с вами публикацией @diyvideosx. Посмотрите ее в https://www.instagram.com/p/BMwa6WchRJu/?r=3147170348';
$test2 = 'И просто instagram.com/durov';
$test3 = 'И просто instagram.com/durov и ещё https://www.instagram.com/p/BMwa6WchRJu/?r=3147170348 фвф';

print_r(getCanonicLink($test1));
echo '<br>';
print_r(getCanonicLink($test2));
echo '<br>';
print_r(getCanonicLink($test3, true));