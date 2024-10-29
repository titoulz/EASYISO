<?php
require_once '../vendor/autoload.php';
Use Dotenv\Dotenv;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Utilisation de la clé API dans une variable PHP
$apiKey = $_ENV['API_KEY_EXAMPLE'];
