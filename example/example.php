<?php

require __DIR__ . '/../src/bootstrap.php';

$config = (new PhpIni\Reader)->parse(file_get_contents(__DIR__ . '/example.ini'));

echo '<pre>';
print_r($config);