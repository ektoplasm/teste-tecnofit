<?php

namespace App;

include_once 'bootstrap.php';

use App\Controllers\MovementController;
use App\Config\DbConnect;

// Conectando ao banco de dados
$db = (new DbConnect())->getConnection();

// Separando o caminho da URL e o método da requisição
$requestPath = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) ?: [];
$requestMethod = htmlspecialchars($_SERVER['REQUEST_METHOD'], ENT_QUOTES, 'UTF-8');

// Obtendo o corpo da requisição e decodificando o JSON
$requestBody = json_decode(file_get_contents('php://input'), true);

// O único path disponível é o /movements
// Se o caminho não iniciar por /movements, retorna 404
if (count($requestPath) < 1 || $requestPath[1] !== 'movements') {
  header('HTTP/1.1 404 Not Found');
  exit;
}

// Remove os elementos vazios e o primeiro elemento do array, que é o path "movements"
$requestPath = array_slice(array_filter($requestPath, function ($value) {
  return !empty($value);
}), 1);

// Inicializa o controller com a instancia do banco de dados, o método da requisição e o caminho informado
$controller = new MovementController($db, $requestMethod, $requestPath, $requestBody);

// Processa a requisição
$controller->processRequest();
