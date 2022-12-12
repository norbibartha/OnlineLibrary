<?php

use Controller\BookController;

require 'autoload.php';

header('Content-Type: application/json');

$params = $_GET;

$bookController = new BookController();
echo $bookController->getBooks($params);
