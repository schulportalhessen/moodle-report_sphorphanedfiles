<?php

require_once(__DIR__ . '/../../config.php');

use report_sphorphanedfiles\View\OrphanedView;

/* Assign global variables to local (parameter) variables.
 * At the moment, this approach is used for documentation
 * purposes.
 * 
 * FIXME: Move to method call. 
 */
$courseId = required_param('id', PARAM_INT);
$page = $PAGE;
$output = $OUTPUT;
$user = $USER;
$db = $DB;

$orphanedViewInstance = new OrphanedView($db, $courseId, $page, $output, $user);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orphanedViewInstance->deleteOrphanedFile();
}

$orphanedViewInstance->init();
