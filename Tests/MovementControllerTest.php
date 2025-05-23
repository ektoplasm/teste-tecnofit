<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class MovementControllerTest extends PHPUnit\Framework\TestCase
{
  public function testShouldReturnDeadliftMovementInfo(): void
  {
    $curl = curl_init('http://127.0.0.1:8080/movements/search');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
      'id' => 1
    ]));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl, CURLOPT_HTTPHEADER, ['content-type: application/json']);

    $response = curl_exec($curl);

    $jsonResponse = json_decode($response, true);

    curl_close($curl);

    $this->assertJson($response);
    $this->assertEquals('Deadlift', $jsonResponse['movement_name']);
  }

  public function testShouldReturn404Error(): void
  {
    $curl = curl_init('http://127.0.0.1:8080/movements/search');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
      'id' => 1,
      'name' => 'bench'
    ]));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl, CURLOPT_HTTPHEADER, ['content-type: application/json']);

    curl_exec($curl);

    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    $this->assertEquals($httpCode, 404);
  }

  public function testCorrectRankingOrder(): void
  {
    $curl = curl_init('http://127.0.0.1:8080/movements/search');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
      'id' => 1,
      'name' => 'deadlift'
    ]));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl, CURLOPT_HTTPHEADER, ['content-type: application/json']);

    curl_exec($curl);

    $response = curl_exec($curl);

    $jsonResponse = json_decode($response, true);

    curl_close($curl);

    $this->assertJson($response);
    $this->assertEquals('1', $jsonResponse['users'][0]['position']);
    $this->assertEquals('3', $jsonResponse['users'][2]['position']);
  }
}
