<?php

function toHtml($content, $preheaderSkip = false) {
    $html = '';
    foreach($content as $row) {
       if(is_string($row)) {
            $html .= $row;
            continue;
        }

        $attrs = '';
        if(isset($row->attrs)) {
            foreach($row->attrs as $name => $value) {
                if($row->tag === 'img') {
                    $value = '//telegra.ph' . $value;
                }
                $attrs .= sprintf(' %s="%s"', $name, $value);
            }
        }

        $children = '';
        if(isset($row->children)) {
            $children .= toHtml($row->children);
        }

        $html .= sprintf('<%s%s>%s</%s>', $row->tag, $attrs, $children, $row->tag);
    }

    if($preheaderSkip) {
        $html = preg_replace('/^.+?(<h\d)/', '$1', $html);
    }
    return $html;
}

/*
[0] => path
[1] => url
[2] => title
[3] => description
[4] => author_name
[5] => content
[6] => views
*/
function getPost($url) {
    $path = preg_replace('/.+\/(.+?)$/', '$1', $url);
    $apiUrl = "https://api.telegra.ph/getPage/$path?return_content=true";
    $data = json_decode(file_get_contents($apiUrl))->result;
    $data->content = toHtml($data->content);
    return $data;
}

$url = @$_GET['url'] ?: 'https://telegra.ph/CHelovek-v-Islame-07-12';
$post = getPost($url);
?>

<h1><?= $post->title ?></h1>
<p><?= $post->author_name ?>
<?= $post->content ?>