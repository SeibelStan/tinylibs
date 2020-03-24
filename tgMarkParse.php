<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-type: text/plain');

function tgCheckTag($result, $mod, $i, $entities) {
    $tags = [
        'bold' => ['<b>', '</b>'],
        'italic' => ['<i>', '</i>'],
        'code' => ['<code>', '</code>'],
        'underline' => ['<u>', '</u>'],
        'strikethrough' => ['<s>', '</s>'],
        'pre' => ['<pre>', '</pre>'],
        'text_link' => ['<a href="DOOMURL">', '</a>']
    ];

    $hit = false;
    foreach ($entities[$mod] as $ent) {
        $offset = $ent->offset + 1;
        if ($mod) { $offset += $ent->length; }

        if ($i == $offset) {
            if (!$mod && $ent->type == 'text_link') {
                $result .= preg_replace('/DOOMURL/', $ent->url, $tags[$ent->type][$mod]);
            }
            else {
                $result .= $tags[$ent->type][$mod];
            }
            $hit = true;
        }
    }

    return [$result, $hit];
};

function tgMarkParse($data) {
    $entities = [
        $data->message->entities,
        array_reverse($data->message->entities)
    ];

    $text = $data->message->text;
    $chars = preg_split('//u', $text);

    $result = '';

    $mod = 0;
    foreach ($chars as $i => $char) {
        list($result, $hit) = tgCheckTag($result, $mod, $i, $entities);
        $mod = abs($mod - 1);

        list($result, $hit) = tgCheckTag($result, $mod, $i, $entities);
        if ($hit) { $mod = abs($mod - 1); }

        $result .= $char;
    }
    return $result;
}

$data = json_decode('
{
  "message": {
    "text": "Привет!\nЭто курсив\nЭто моноширный\nЭто ссылка",
    "entities": [
        {
            "offset": 0,
            "length": 7,
            "type": "bold"
        },
        {
            "offset": 8,
            "length": 10,
            "type": "italic"
        },
        {
            "offset": 34,
            "length": 10,
            "type": "text_link",
            "url": "https://google.ru/"
        }
    ]
  }
}');

echo tgMarkParse($data);