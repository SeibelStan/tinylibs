function iptcMakeTag($rec, $tag, $value) {
    $length = strlen($value);
    $retval = chr(0x1C) . chr($rec) . chr($tag);

    if($length < 0x8000) {
        $retval .= chr($length >> 8) . chr($length & 0xFF);
    }
    else {
        $retval .= chr(0x80) . 
                   chr(0x04) . 
                   chr(($length >> 24) & 0xFF) . 
                   chr(($length >> 16) & 0xFF) . 
                   chr(($length >> 8) & 0xFF) . 
                   chr($length & 0xFF);
    }

    return $retval . $value;
}

// https://sno.phy.queensu.ca/~phil/exiftool/TagNames/IPTC.html
// 120 Caption-Abstract
function setTag($path, $tag, $value) {
    $data = iptcMakeTag(2, "$tag", $value);
    $content = iptcembed($data, $path);
    $fp = fopen($path, "wb");
    fwrite($fp, $content);
    return 1;
}

function getTag($path, $tag) {
    if (!preg_match('/jpe*g$/', $path)) {
        return false;
    }
    if (!getimagesize($path, $info)) {
        return false;
    }
    if(isset($info['APP13'])) {
        $iptc = iptcparse($info['APP13']);
    }
    return @$iptc ? $iptc["2#$tag"][0] : false;
}