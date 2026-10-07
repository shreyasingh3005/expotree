<?php
$html = file_get_contents('http://localhost/expotree/event.php?id=1');
file_put_contents(__DIR__ . '/rendered_event.html', $html);
echo "Saved rendered HTML (" . strlen($html) . " bytes)\n";
