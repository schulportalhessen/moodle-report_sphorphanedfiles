<?php
require_once(__DIR__ . '/../../config.php');
use report_sphorphanedfiles\View\OrphanedView;

/* Assign global variables to local (parameter) variables.
 * At the moment, this approach is used for documentation
 * purposes.
 * 
 * FIXME: Move to method call. 
 */

require_login();
$courseId = required_param('id', PARAM_INT);
require_login($courseId);


$page = $PAGE;
$output = $OUTPUT;
$user = $USER;
$db = $DB;

global $CFG;
echo "Hurz";

$isactive = $CFG->report_sphorphanedfiles_isactive;
$isactiveforadmin = $CFG->report_sphorphanedfiles_isactiveforadmin;
// $hascapability = has_capability('report/sphorphanedfiles:view',$context);

if ($isactive || $isactiveforadmin) {
    $orphanedViewInstance = new OrphanedView($db, $courseId, $page, $output, $user);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $orphanedViewInstance->deleteOrphanedFile();
    }
    $orphanedViewInstance->init($isactive, $isactiveforadmin);
} else {
    echo "Report is not activated or missing capability";
}

