<?php

namespace report_sphorphanedfiles;

use html_writer;

class HTML
{
    public static function createImage($url)
    {
        return html_writer::tag(
            'div',
            html_writer::empty_tag('img', [
                'height' => '100px',
                'src' => $url
            ]),
            ['class' => 'courseimage']
        );
    }

    public static function createLinkInNewTab($url, $description)
    {
        return html_writer::tag(
            'div',
            html_writer::link($url, $description, ['target' => '_blank'])
        );        
    }
}
