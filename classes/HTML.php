<?php

namespace report_sphorphanedfiles;

use html_writer;
use moodle_url;

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

    public static function createSectionHeading($sectionInfo, $course, $sectionCounter)
    {
        $courseInfo = get_fast_modinfo($course);
        $formatsectionname = '';

        if (get_string_manager()->string_exists('sectionname', 'format_' . $course->format)) {
            $formatsectionname = get_string('sectionname', 'format_' . $course->format);
        }

        $url = (new moodle_url('/course/view.php', array('id' => $courseInfo->courseid))) . '#section-' . $sectionCounter;

        $sectionname = $sectionInfo->name;
        $anzuzeigenderText = '';
        if (is_null($sectionname) || $sectionname === '') {
            $anzuzeigenderText = $formatsectionname . ' ' . $sectionCounter;
        } else {
            $anzuzeigenderText = $sectionname;
        }

        $linktext = html_writer::link($url, $anzuzeigenderText);
        $linktext2 = html_writer::link($url, '📑', ['target' => '_blank']);

        return html_writer::tag('h3', '(' . $sectionCounter . ') ' . $linktext . ' ' .  $linktext2, ['class' => 'orphandfilesh3']);
    }
}
