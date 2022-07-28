<?php
require_once(__DIR__ . '/../../config.php');
use report_sphorphanedfiles\View\OrphanedView;

// Only show index.php for logged in users
require_login();

// Read the id of the course
$courseId = required_param('id', PARAM_INT);
try {
    // Look if the course exists
    $course = $DB->get_record('course', array('id' => $courseId), '*', MUST_EXIST);
} catch(Exception $e){
    $msg = '';
    $msg = get_string('invalidcourseidmessage', 'report_sphorphanedfiles');
    echo $msg;
    die();
}
// Only show index.php for logged in users
require_login($courseId);

// More access rules
$isactive = get_config('report_sphorphanedfiles', 'isactive');
$isactiveforadmin = get_config('report_sphorphanedfiles', 'isactiveforadmin');

$isUserAllowedToUseReport = ($isactive && has_capability('report/sphorphanedfiles:view', context_course::instance($courseId)));
$isUserAllowedToUseReport = $isUserAllowedToUseReport || ($isactiveforadmin && is_siteadmin());

if (!$isUserAllowedToUseReport){
    $msg = '';
    $msg = get_string('accessruleviolationmessage', 'report_sphorphanedfiles');
    echo $msg;
    die();
}

// No show report ore delete files
$orphanedViewInstance = new OrphanedView($DB, $courseId, $PAGE, $OUTPUT,$USER);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orphanedViewInstance->deleteOrphanedFile();
}
$orphanedViewInstance->init();


