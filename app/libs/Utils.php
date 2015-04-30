<?php

namespace App\Libs;


use Nette\Object;

class Utils extends Object
{
    public static function safeExplodeByComma(&$data)
    {
        $data = trim($data, ' ,');
        $data = explode(',', $data);
        array_walk($data, function (&$value, $key) {
            $value = trim($value, ' ,');
        });
        $data = array_values(array_unique(array_filter($data)));
    }

    public static function texy($text)
    {
        $t = self::getTexy()->process($text);

        return $t;
    }

    /**
     * @return \Texy
     */
    private static function getTexy()
    {
        $texy = new \Texy();
        $texy->allowed['block'] = FALSE;
        $texy->allowed['blockquote'] = FALSE;
        $texy->allowed['figure'] = FALSE;
        $texy->allowed['heading/underlined'] = FALSE;
        $texy->allowed['heading/surrounded'] = FALSE;
        $texy->allowed['horizline'] = FALSE;
        $texy->allowed['html/tag'] = FALSE;
        $texy->allowed['html/comment'] = FALSE;
        $texy->allowed['image'] = FALSE;
        $texy->allowed['image/definition'] = FALSE;
        $texy->allowed['link/definition'] = FALSE;
        $texy->allowed['link/reference'] = FALSE;
        $texy->allowed['link/url'] = FALSE;
        $texy->allowed['link/email'] = FALSE;
        $texy->allowed['list'] = FALSE;
        $texy->allowed['list/definition'] = FALSE;
        $texy->allowed['phrase/strong'] = FALSE;
        $texy->allowed['script'] = FALSE;

        return $texy;
    }

} 