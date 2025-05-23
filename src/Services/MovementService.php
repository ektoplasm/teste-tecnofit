<?php

namespace App\Services;

/**
 * Classe MovementService
 * 
 * Classe responsável por buscar no banco de dados 
 * as informações relativas aos movimentos.
 * 
 * Recebe como parâmetro uma instância do banco.
 *
 * Exemplos de uso:
 *   $service = new MovementService($db);
 *
 * @author Paulo <contato@juniorherval.com.br>
 * @package App\Services
 */
class MovementService
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
   * Método findByIdOrName
   * 
   * Responsável por buscar as informações de um movimento 
   * no bancos de dados.
   * 
   * @param $params Parâmetros da pesquisa.
   * 
   * @return array|bool
   */
  public function findByIdOrName(array $params): array|bool
  {
    // Query base
    $query = 'SELECT id, name FROM movement WHERE ';

    // Se houver o parâmetro "id", adiciona na query
    if (array_key_exists('id', $params) && is_numeric($params['id']) && $params['id'] > 0) {
      $queryParams[] = 'id = :id';
      $params[':id'] = $params['id'];
    }

    // Se houver o parâmetro "name", adiciona na query
    if (array_key_exists('name', $params) && strlen($params['name']) > 1) {
      $queryParams[] = 'name LIKE :name';
      $params[':name'] = '%' . $params['name'] . '%';
    }

    // Se não houver nenhum parâmetro definido para a busca, retorna vazio.
    if (empty($queryParams))
      return [];

    try {
      $query .= implode(' AND ', $queryParams);
      $query .= ' ORDER BY id';

      $stmt = $this->db->prepare($query);
      foreach ($params as $key => &$value) {
        $stmt->bindParam($key, $value);
      }
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
   * Responsável por buscar os recordes registrados para um movimento.
   * 
   * @param $id ID do movimento.
   * 
   * @return array
   */
  public function getPersonalRecords(int $id): array
  {
    try {
      $stmt = $this->db->prepare('SELECT id, user_id, value, date FROM personal_record WHERE movement_id=:id ORDER BY value DESC');
      $stmt->bindParam(':id', $id);
      $stmt->execute();

      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      error_log($e->getMessage());
      throw new \Exception('Erro ao recuperar dados.');
    }
  }
}
