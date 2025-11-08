<?php

require_once 'bibli_bookshop.php';
session_start();
sessionExit();

// Redirection vers la page précédente, sinon vers index.php
$destination = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) 
    ? $_SERVER['HTTP_REFERER'] 
    : '../index.php';

header("Location: $destination");
exit();
