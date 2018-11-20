<?php 
    
    require __DIR__ . '/../vendor/autoload.php';
    
    use Embryo\Log\StreamLogger;

    $logPath = __DIR__.DIRECTORY_SEPARATOR.'logs';
    $logger  = new StreamLogger($logPath);
    $logger->info('This is info log');