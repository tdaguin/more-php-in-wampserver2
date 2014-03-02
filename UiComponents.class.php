<?php

/**
 * Description of cssComponents
 *
 * @author Thierry Daguin
 * @since 2014-03-01
 */
class UiComponents {

    public static function getBox($content, $style = '') {
        $html = '<div ';
        if ('' !== $style) {
            $html .= 'style="' . $style . '"';
        }
        $html .= '>' . $content . '</div>';
        return $html;
    }

    public static function getOkBox($content = 'OK') {
        return self::getBox($content, 'display: inline-block;'
                        . 'padding: 3px 4px;'
                        . 'font-size: 11px;'
                        . 'font-weight: bold;'
                        . 'line-height: 1;'
                        . 'color: #fff;'
                        . 'background-color: #569E3D;'
                        . 'border-radius: 2px;');
    }

    public static function getKoBox($content = 'KO') {
        return self::getBox($content, 'display: inline-block;'
                        . 'padding: 3px 4px;'
                        . 'font-size: 11px;'
                        . 'font-weight: bold;'
                        . 'line-height: 1;'
                        . 'color: #fff;'
                        . 'background-color: red;'
                        . 'border-radius: 2px;');
    }

    public static function getPhpReleaseBox($content) {
        return self::getBox($content, 'display: inline-block;'
                        . 'padding: 3px 4px;'
                        . 'font-size: 11px;'
                        . 'font-weight: bold;'
                        . 'line-height: 1;'
                        . 'color: #FFF;'
                        . 'background-color: #5B658A;'
                        . 'border-radius: 2px;');
    }

    public static function getInfoBox($content) {
        return self::getBox($content, 'display: block;'
                        . 'margin-left: 25px;'
                        . 'background-color: white;'
                        . 'border-radius: 5px;'
                        . 'border: 1px solid #a1a1a1;'
                        . 'padding: 5px; '
                        . 'width: auto;');
    }

//define('PHP_DIV_STYLE', ' style="display: block;margin-left: 25px;background-color: white;border-radius: 5px;border: 1px solid #a1a1a1;padding: 5px; width: 500px;">');
//define('OK_RESPONSE', '<span style="background-color:greenyellow">ok</span>');
//define('KO_RESPONSE', '<span style="background-color:red">not found</span>');
}
