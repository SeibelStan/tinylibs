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