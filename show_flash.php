<?php
require __DIR__ . '/app/Core/helpers.php';
$sid = trim(file_get_contents(__DIR__.'/sid.txt'));
session_id($sid);
session_start();
var_dump($_SESSION['flash'] ?? null);
