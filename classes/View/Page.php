<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace report_sphorphanedfiles\View;

use moodle_url;

defined('MOODLE_INTERNAL') || die();

class Page
{
    private $page;
    private $output;
    private $title;
    private $course;

    public function __construct($page, $course, $courseId, $output) {
        $this->page = $page;
        $this->output = $output;
        $this->course = $course;
        $this->title = get_string('pluginname', 'report_sphorphanedfiles');

        $page->set_url(new moodle_url('/report/sphorphanedfiles/index.php', ['id' => $courseId]));
        $page->set_title($this->getTitle());
        $page->set_heading($course->fullname);
        $page->set_pagelayout('incourse');
    }

    public function getOutput() {
        return $this->output;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getCourse() {
        return $this->course;
    }

    public function getCourseInfo() {
        return get_fast_modinfo($this->getCourse());
    }

    protected function getPage() {
        return $this->page;
    }

    public function getIconURL($instance) {
        return $this->getPage()->theme->image_url('icon', $instance->modname)->out();
    }
}
