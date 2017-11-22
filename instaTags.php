<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

function instaTagSearch($tag, $page_info = false, $offset = 100) {
    $tag = mb_strtolower($tag);
    $offset += 1;
    $url = "https://www.instagram.com/explore/tags/$tag/?__a=1";
    if($page_info && $page_info->has_next_page) {
        $variables = [
            'tag_name' => $tag,
            'after' => $page_info->end_cursor,
            'first' => $offset
        ];
        $url = "https://www.instagram.com/graphql/query/?query_id=17875800862117404&variables=" . urlencode(json_encode($variables));
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => 1
    ]);
    $rawResult = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($rawResult);

    if($page_info) {
        $source = $result->data->hashtag->edge_hashtag_to_media;
        $page_info = $source->page_info;
        $source = $source->edges;
    }
    else {    
        $source = array_merge(
            $result->tag->top_posts->nodes,
            $result->tag->media->nodes
        );
        $page_info = $result->tag->media->page_info;        
    }
    
    $units = [];
    $ids = [];
    foreach($source as &$unit) {
        if(isset($unit->node)) {
            $unit = $unit->node;
            $units[] = (object) [
                'code'        => $unit->shortcode,
                'display_src' => $unit->display_url,
                'caption'     => @$unit->text,
                'is_video'    => $unit->is_video ? 1 : 0,
                'id'          => $unit->id
            ];
        }
        else {
            $units[] = (object) [
                'code'        => $unit->code,
                'display_src' => $unit->display_src,
                'caption'     => @$unit->caption,
                'is_video'    => $unit->is_video ? 1 : 0,
                'id'          => $unit->id
            ];
        }
        $ids[] = $unit->id;
    }
    if(!$units) {
        print_r($rawResult);
        return 0;
    }
    return (object) [
        'page_info' => $page_info,        
        'tag'       => $tag,
        'offset'    => $offset,
        'units'     => $units,        
    ];
}

function getVideoUrl($code) {
    $ch = curl_init("https://www.instagram.com/p/$code/?__a=1");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => 1
    ]);
    $rawResult = curl_exec($ch);
    curl_close($ch);
    return json_decode($rawResult)->graphql->shortcode_media->video_url;
}

//header('Content-type: text/plain');

$tag = @$_GET['tag'] ?: 'Зож';
$page_info = @$_GET['page_info'] ? json_decode($_GET['page_info']) : '';
$offset = @$_GET['offset'] ?: 100;
$test1 = instaTagSearch($tag, $page_info, $offset);
//$test2 = instaTagSearch($tag, $test1->page_info, $test1->offset); // пример конструктора следующей страницы
//print_r($test1);
//echo getVideoUrl('BbzdvjBha2f'); // пример запроса mp4 от видео, пока не используется
?>

<?php foreach($test1->units as $unit) : ?>
    <img src="<?= $unit->display_src ?>" width="64" title="<?= $unit->code ?>">
<?php endforeach; ?>
<p><a href="?tag=<?= $tag ?>&offset=<?= $test1->offset ?>&page_info=<?= urlencode(json_encode($test1->page_info)) ?>">дальше</a>