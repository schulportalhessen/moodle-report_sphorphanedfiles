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
    // wrong courseId;
    die();
}
// Only show index.php for logged in users
require_login($courseId);

// More access rules
$isactive = get_config('report_sphorphanedfiles', 'isactive');
$isactiveforadmin = get_config('report_sphorphanedfiles', 'isactiveforadmin');
$coursecontext = \context_course::instance($courseId);
$hascapability = has_capability('report/sphorphanedfiles:view',$coursecontext);

// Check all accessrules
if ( ($isactive || $isactiveforadmin) && $hascapability ) {
    $orphanedViewInstance = new OrphanedView($DB, $courseId, $PAGE, $OUTPUT,$USER);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $orphanedViewInstance->deleteOrphanedFile();
    }
    $orphanedViewInstance->init($isactive, $isactiveforadmin);
} else {
    $msg = '';
    $msg = get_string('accessruleviolationmessage', 'report_sphorphanedfiles');
    echo $msg;
}

