<?php

namespace report_sphorphanedfiles;

use html_writer;

class HTML
{
    public static function createImage($url)
    {
        return html_writer::tag(
            'div',
            html_writer::empty_tag(
                'img',
                [
                    'height' => '100px',
                    'src' => $url
                ]
            ),
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

    public static function createIconForInstance($instance, $page)
    {
        return html_writer::empty_tag(
            'img',
            [
                'src' => $page->theme->image_url('icon', $instance->modname)->out(),
                'style' => 'width: 20px; height: 20px; margin-right: 4px;',
                'class' => 'iconlarge activityicon'
            ]
        );
    }
}
