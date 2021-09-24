<?php

require_once(__DIR__ . '/../../config.php');

use report_sphorphanedfiles\Files\FileInfoList;

echo '<html>';
echo '<body>';

if (FileInfoList::isSufficientForConstruction($_POST))
 {
    $myList = new FileInfoList($_POST);

    if (!$myList->isEmpty()) {
        echo "<h1>Die nachfolgenden Dateien sind für das Löschen ausgewählt</h1>";

        echo $myList->toHTML();

        echo '<b>Der Aufruf $myList->delete(...) würde die obigen Dateien löschen</b>';
    } else {
        echo "<b>Keine Objekte ausgewählt!</b>";
    }
} else {
    echo "<b>FATAL: Keine FileInfo Objekte vorhanden!</b>";
}

echo '</body>';
echo '</html>';
