<?php

define('APP_ROOT', __DIR__);

// Verifica se o arquivo de log existe, caso não exista, cria o arquivo
if (!file_exists(APP_ROOT . '/logs/error.log')) {
  touch(APP_ROOT . '/logs/error.log');
}

error_reporting(E_ALL); // Reporta todos os erros que acontecerem

ini_set('ignore_repeated_errors', TRUE); // Ignora erros repetidos
ini_set('display_errors', false); // Não exibe erros na tela
ini_set('log_errors', true); // Grava erros no log
ini_set('error_log', APP_ROOT . '/logs/error.log'); // Define o caminho do log de erros

// Faz o autoload das classes existentes
spl_autoload_register(function ($class) {
  $class = str_replace('\\', '/', $class);
  $class = str_replace('App/', '', $class);
  include  $class . '.php';
});
