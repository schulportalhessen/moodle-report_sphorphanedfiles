<?php

echo '<html>';
echo '<body>';

$items = $_POST['selectedItems'];

if (!empty($items)) {
    echo "<h1>Die nachfolgenden Dateien sind für das Löschen ausgewählt</h1>";
    echo '<ul>';
    foreach ($items as $item)
        echo "<li>Zu löschen: " . $item . "</li>";
    echo '</ul>';
} else {
    echo "<b>Keine Objekte ausgewählt!</b>";
}
echo '</body>';
echo '</html>';
