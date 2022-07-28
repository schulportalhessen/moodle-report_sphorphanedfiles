<?php

namespace report_sphorphanedfiles;

use html_writer;
use moodle_url;

use report_sphorphanedfiles\View\Page;

defined('MOODLE_INTERNAL') || die();

class HTML
{
    public static function createImage(string $url): string
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

    public static function createLinkInNewTab(string $url, string $description): string
    {
        return html_writer::tag(
            'span',
            html_writer::link($url, $description, ['target' => '_blank'])
        );
    }

    public static function createIconForInstance($instance, Page $page): string
    {
        return html_writer::empty_tag(
            'img',
            [
                'src' => $page->getIconURL($instance),
                'style' => 'width: 20px; height: 20px; margin-right: 4px;',
                'class' => 'iconlarge activityicon'
            ]
        );
    }

    public static function createSectionHeading($sectionInfo, $course, $sectionCounter): string
    {
        $description = $sectionInfo->name;
        if (is_null($description) || $description === '') {
            $formatsectionname = get_string_manager()->string_exists('sectionname', 'format_' . $course->format) ? get_string('sectionname', 'format_' . $course->format) : '';

            $description = $formatsectionname . ' ' . $sectionCounter;
        }

        $courseInfo = get_fast_modinfo($course);

        $url = (new moodle_url('/course/view.php', ['id' => $courseInfo->courseid])) . '#section-' . $sectionCounter;

        $linktext = html_writer::link($url, $description);
        $linktext2 = HTML::createLinkInNewTab($url, '📑');

        return html_writer::tag('h3', '(' . $sectionCounter . ') ' . $linktext . ' ' .  $linktext2, ['class' => 'orphandfilesh3']);
    }

    public static function createSectionOverview(int $distance, string $head, string $body): string
    {
        return html_writer::tag(
            'div',
            $head . $body,
            ['class' => 'border shadow p-1']
        ) . str_repeat(html_writer::empty_tag('br'), $distance);
    }

    public static function createList(array $data, bool $ordered = false)
    {
        return html_writer::tag(
            $ordered ? 'ol' : 'ul',
            implode(
                array_map(function ($element) {
                    return html_writer::tag('li', $element);
                }, $data)
            )
        );
    }
}
