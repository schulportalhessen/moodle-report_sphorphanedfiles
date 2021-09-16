<?php
$items = $_POST['selectedItems'];

if(empty($items))
{
echo("Keine Objekte ausgewählt!");
}
else
{
foreach($items as $item)
{
echo "Zu löschen: " . $item;
}
}
?>