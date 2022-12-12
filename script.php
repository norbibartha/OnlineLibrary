<?php

require 'autoload.php';

use Controller\FileProcessor;

if (!isset($argv[1])) {
    throw new Exception('You need to pass the path as command line argument!');
}

$path = $argv[1];

$fileProcessor = new FileProcessor();
$fileProcessor->processFiles($path);