<?php

$html = $_POST['html'];
$html = str_replace('&lt;', '<', $html);
$html = str_replace('&gt;', '>', $html);
$html = str_replace('\\', '', $html);

$tmpfname = uniqid('titrum_') . '.html';

$handle = fopen('files/' . $tmpfname, 'w');
fwrite($handle, $html);
fclose($handle);

$tmpfname = str_replace('titrum_', '', $tmpfname);
$tmpfname = str_replace('.html', '', $tmpfname);
echo $tmpfname;