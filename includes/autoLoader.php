<?php
spl_autoload_register(function ($class) {
    require __DIR__ . "/../classes/{$class}.php";
});


session_start();

require_once __DIR__ . '/../config.php';

function errorHandler($level, $message, $file, $line) {

        throw new ErrorException($message, 0, $level, $file, $line);
    
}

function exceptionHandler($exception) {
    echo "An exception occurred: " . $exception->getMessage();
}

set_error_handler('errorHandler');
set_exception_handler('exceptionHandler');
