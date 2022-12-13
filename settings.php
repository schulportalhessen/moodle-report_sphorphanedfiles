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

/**
 * Course list block settings
 *
 * @package    report_sphorphanedfiles
 * @copyright  Andreas Schenkel, Schulportal Hessen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configcheckbox(
        'report_sphorphanedfiles/isactive',
        get_string('isactive', 'report_sphorphanedfiles'),
        get_string('configisactive', 'report_sphorphanedfiles'),
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'report_sphorphanedfiles/isactiveforadmin',
        get_string('isactiveforadmin', 'report_sphorphanedfiles'),
        get_string('configisactiveforadmin', 'report_sphorphanedfiles'),
        0
    ));


    // Setting for IntroHandler - only use IntroHandler for Activities that have intro.
    $moodleactivitiescore = 'assign,choice,customcert,data,lti,feedback,forum,glossary,' .
        'h5pactivity,hotpot,lesson,quiz,scorm,survey,wiki,workshop';
    $configsetting = new  admin_setting_configtext(
        'report_sphorphanedfiles/handleractivitiescore',
        new lang_string('handleractivitiescore', 'report_sphorphanedfiles'),
        new lang_string('confighandleractivitiescore', 'report_sphorphanedfiles'),
        $moodleactivitiescore,
        true,
        120
    );
    $configsetting->set_force_ltr(true);
    $settings->add($configsetting);

    $moodleactivitiesplugins = 'bigbluebuttonbn,board,checklist,ratingallocate,geogebra,' .
        'hvp,mootyper,mindmap,pdfannotator,realtimequiz';
    $configsetting = new  admin_setting_configtext(
        'report_sphorphanedfiles/handleractivitiesplugin',
        new lang_string('handleractivitiesplugin', 'report_sphorphanedfiles'),
        new lang_string('confighandleractivitiesplugin', 'report_sphorphanedfiles'),
        $moodleactivitiesplugins,
        true,
        120
    );
    $configsetting->set_force_ltr(true);
    $settings->add($configsetting);

    // Do not add 'label' to this list.
    $moodlematerialscore = 'book,folder,imscp,url';
    $configsetting = new  admin_setting_configtext(
        'report_sphorphanedfiles/handlermaterialscore',
        new lang_string('handlermaterialscore', 'report_sphorphanedfiles'),
        new lang_string('confighandlermaterialscore', 'report_sphorphanedfiles'),
        $moodlematerialscore,
        true,
        120
    );
    $configsetting->set_force_ltr(true);
    $settings->add($configsetting);

    $moodlematerialsplugins = 'lightboxgallery,edusharing,unilabel';
    $configsetting = new  admin_setting_configtext(
        'report_sphorphanedfiles/handlermaterialsplugin',
        new lang_string('handlermaterialsplugin', 'report_sphorphanedfiles'),
        new lang_string('confighandlermaterialsplugin', 'report_sphorphanedfiles'),
        $moodlematerialsplugins,
        true,
        120
    );
    $configsetting->set_force_ltr(true);
    $settings->add($configsetting);

}
