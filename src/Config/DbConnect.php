<?php

namespace App\Config;

/**
 * Classe DbConnect
 * 
 * Responsável por conectar ao banco de dados.
 *
 * Exemplos de uso:
 *   $db = new DbConnect();
 *   $db->getConnection();
 * 
 * @author Paulo <contato@juniorherval.com.br>
 * @package App\Config
 */
class DbConnect
{

  /**
   * Instância da conexão com o banco de dados.
   * @var \PDO
   */
  private $dbConn = null;

  public function __construct()
  {
    //Dados de conexão com o banco de dados.
    // Caso não tenha as variáveis de ambiente, usa valores padrão
    $host = getenv('MYSQL_HOST') ?: 'localhost';
    $port = getenv('MYSQL_PORT') ?: '3306';
    $db   = getenv('MYSQL_DATABASE') ?: 'db';
    $user = getenv('MYSQL_USER') ?: 'root';
    $pass = getenv('MYSQL_PASSWORD') ?: '';

    try {
      // Cria a conexão com o banco de dados
      $this->dbConn = new \PDO(sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', $host, $port, $db), $user, $pass);
    } catch (\PDOException $e) {
      // Em caso de falha, loga a mensagem de erro e encerra o script
      error_log($e->getMessage());
      exit('Erro ao conectar com o banco de dados.');
    }
  }

  /**
   * Getter da instância da conexão
   * 
   * @return \PDO
   */
  public function getConnection(): \PDO
  {
    return $this->dbConn;
  }
}
