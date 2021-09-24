<?php

namespace report_sphorphanedfiles\View;

use moodle_url;

class Page
{
    private $page;
    private $output;
    private $title;
    private $course;

    public function __construct($page, $course, $courseId, $output)
    {
        $this->page = $page;
        $this->output = $output;
        $this->course = $course;
        $this->title = get_string('pluginname', 'report_sphorphanedfiles');

        $page->set_url(new moodle_url('/report/sphorphanedfiles/index.php', ['id' => $courseId]));
        $page->set_title($this->getTitle());
        $page->set_heading($course->fullname);
        $page->set_pagelayout('incourse');
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getCourse()
    {
        return $this->course;
    }

    public function getCourseInfo()
    {
        return get_fast_modinfo($this->getCourse());
    }

    protected function getPage()
    {
        return $this->page;
    }

    public function getIconURL($instance)
    {
        return $this->getPage()->theme->image_url('icon', $instance->modname)->out();
    }
}
