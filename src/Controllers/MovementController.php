<?php

namespace App\Controllers;

use App\Services\MovementService;
use App\Services\UserService;
use PDO;

/**
 * Classe MovementController
 * 
 * Classe responsável por gerenciar as requisições relativas aos movimentos.
 * 
 * Recebe como parâmetro uma instância do banco de dados, o método da 
 * requisição e o caminho informado, se houver corpo na requisição também
 * deve ser informado.
 *
 * Exemplos de uso:
 *   $controller = new MovementController($db, $requestMethod, $requestPath, $requestBody);
 *
 * @author Paulo <contato@juniorherval.com.br>
 * @package App\Controllers
 */
class MovementController
{
  /**
   * Método da requisição
   * @var string 
   */
  private $requestMethod;

  /**
   * Caminho da requisição
   * @var string 
   */
  private $requestPath;

  /**
   * Parâmetros da requisição
   * @var array 
   */
  private $params;

  /**
   * @var MovementService
   */
  private $service;

  /**
   * @var UserService
   */
  private $userService;

  /**
   * Construtor da classe
   * 
   * @param PDO $db Instância do banco de dados.
   * @param string $requestMethod Método da requisição.
   * @param string $requestPath Caminho da requisição.
   * @param array|null $params Parâmetros da requisição.
   */
  public function __construct(PDO $db, string $requestMethod, array $requestPath, ?array $params)
  {
    $this->requestMethod = $requestMethod;
    $this->requestPath = $requestPath;
    $this->params = $params ?? [];
    $this->service = new MovementService($db);
    $this->userService = new UserService($db);
  }

  /**
   * Método processRequest
   * 
   * Responsável por processar as requisições recebidas pelo controller.
   * 
   * @return void
   */
  public function processRequest(): void
  {
    // Se não houver path de finido, retorna 404.
    if (empty($this->requestPath)) {
      $response = $this->notFoundResponse();
    } else {

      //Define o método a ser requisitado de acordo com o path
      switch ($this->requestPath[0]) {
        case 'search':
          // Só aceitará requisições POST
          if ($this->requestMethod == 'POST') {
            $response = $this->findByIdOrName($this->params);
            break;
          }
        default:
          $response = $this->notFoundResponse();
          break;
      }
    }

    header($response['status_code']);
    header('Content-Type: application/json');

    if ($response['body']) {
      echo $response['body'];
    }
  }

  /**
   * Método findByIdOrName
   * 
   * Responsável por buscar as informações do movimento solicitado.
   * 
   * @param $params Parâmetros da pesquisa.
   * 
   * @return array
   */
  private function findByIdOrName($params): array
  {
    // Array que será devolvido com a resposta.
    $responseData = [];

    // Busca o movimento requisitado.
    $movementData = $this->service->findByIdOrName($params);
    if (! $movementData) {
      return $this->notFoundResponse();
    }

    $responseData['movement_name'] = $movementData['name'];
    $responseData['users'] = [];

    // Busca os PRs de todos os usuários de acordo com o movimento selecionado.
    $userData = $this->service->getPersonalRecords($movementData['id']);

    // Busca as informações do usuário e seleciona os PRs mais altos
    foreach ($userData as $value) {
      $user = $this->userService->findById($value['user_id']);
      if ($user) {
        if (!array_key_exists($value['user_id'], $responseData['users']) || $value['value'] > $responseData['users'][$value['user_id']]['personal_record'])
          $responseData['users'][$value['user_id']] = [
            'name' => $user['name'],
            'pr_date' => $value['date'],
            'personal_record' => $value['value'],
          ];
      }
    }

    // Ordena os usuários pelo valor do PR
    usort($responseData['users'], function ($a, $b) {
      if ($a['personal_record'] == $b['personal_record']) {
        return 0;
      }
      return ($a['personal_record'] < $b['personal_record']) ? 1 : -1;
    });

    // Rankeia os usuários
    $rankingPosition = 1;
    $lastValue = 0;
    foreach ($responseData['users'] as $key => $value) {
      if ($responseData['users'][$key]['personal_record'] < $lastValue) $rankingPosition++;
      $responseData['users'][$key]['position'] = $rankingPosition;
      $lastValue = $responseData['users'][$key]['personal_record'];
    }

    $response['status_code'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($responseData);

    return $response;
  }

  /**
   * Método notFoundResponse
   * 
   * Retorna uma resposta de erro 404.
   * 
   * @return array
   */
  private function notFoundResponse(): array
  {
    $response['status_code'] = 'HTTP/1.1 404 Not Found';
    $response['body'] = null;

    return $response;
  }
}
