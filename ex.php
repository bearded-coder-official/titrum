<?php

$filename = 'titrum_' . $_GET['id'] . '.html';

$file = tempnam("tmp", "zip");
$zip = new ZipArchive();
$zip->open($file, ZipArchive::OVERWRITE);
$zip->addFile('files/' . $filename, 'index.html');
$zip->addFile('js/titrum.js', 'js/titrum.js');
$zip->close();

header('Content-Type: application/zip');
header('Content-Length: ' . filesize($file));
header('Content-Disposition: attachment; filename="titrum.zip"');
readfile($file);
unlink($file);