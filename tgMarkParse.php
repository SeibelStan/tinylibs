<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-type: text/plain');

function tgMarkParse($data) {
    $data = json_decode($data);
    $entities = @$data->message->entities ?: [];

    $text = $data->message->text;
    $chars = preg_split('//u', $text);

    $tags = [
        'bold' => ['<b>', '</b>'],
        'italic' => ['<i>', '</i>'],
        'pre' => ['<pre>', '</pre>'],
        'text_link' => ['<a href="DOOMURL">', '</a>']
    ];

    $result = '';

    foreach ($chars as $i => $char) {
        foreach ($entities as $ent) {
            if ($i == $ent->offset + 1) {
                $result .= $tags[$ent->type][0];
                if ($ent->type == 'text_link') {
                    $result = preg_replace('/DOOMURL/', $ent->url, $result);
                }
            }
            if ($i == $ent->offset + $ent->length + 1) {
                $result .= $tags[$ent->type][1];
            }
        }
        $result .= $char;
    }

    return $result;
}

$data = '
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
}';

echo tgMarkParse($data);