<?php

namespace App\Services;

/**
 * Classe UserService
 * 
 * Classe responsável por buscar no banco de dados 
 * as informações relativas aos usuários.
 * 
 * Recebe como parâmetro uma instância do banco.
 *
 * Exemplos de uso:
 *   $service = new UserService($db);
 *
 * @author Paulo <contato@juniorherval.com.br>
 * @package App\Services
 */
class UserService
{

  /**
   * @var PDO
   */
  private $db;


  /**
   * Construtor da classe
   * 
   * @param PDO $db Instância do banco de dados.
   */
  public function __construct($db)
  {
    $this->db = $db;
  }

  /**
   * Método getAll
   * 
   * Responsável por buscar todos os usuários.
   * 
   * @return array
   */
  public function getAll(): array
  {
    try {
      $query = 'SELECT id, name FROM user ORDER BY name ASC';
      $stmt = $this->db->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      error_log($e->getMessage());
      throw new \Exception('Erro ao recuperar dados.');
    }
  }

  /**
   * Método findByIdOrName
   * 
   * Responsável por buscar as informações de um usuário 
   * no bancos de dados.
   * 
   * @param $id ID do usuário.
   * 
   * @return array
   */
  public function findById(int $id): array
  {
    try {

      $query = 'SELECT id, name FROM user WHERE id=:id';

      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':id', $id);
      $stmt->execute();
      return $stmt->fetch(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      error_log($e->getMessage());
      throw new \Exception('Erro ao recuperar dados.');
    }
  }


  /**
   * Método getPersonalRecords
   * 
   * Responsável por buscar os recordes registrados para um usuário.
   * 
   * @param $id ID do usuário.
   * 
   * @return array
   */
  public function getPersonalRecords(int $id): array
  {
    try {
      $stmt = $this->db->prepare('SELECT id, movement_id, value, date FROM personal_record WHERE user_id=:id ORDER BY value DESC');
      $stmt->bindParam(':id', $id);
      $stmt->execute();
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      error_log($e->getMessage());
      throw new \Exception('Erro ao recuperar dados.');
    }
  }
}
