<?php
// Подключение библиотеки
use GuzzleHttp\Client;
use Telegram\Api;

class TelegramBot
{
protected $token;
protected $updateId;

public function __construct($token) {
  $this->token = $token;
}

// Функция собирает URL
protected function query($method, $params = []) {
  $url = "https://api.telegram.org/bot";
  $url .= $this->token;
  $url .= "/" . $method;
  if (!empty($params))
    $url .= "?" . http_build_query($params);

  $client = new Client([
    'base_uri' => $url
  ]);

  $result = $client->request('GET');

  return json_decode($result->getBody());
}

// Получаем обновления
public function getUpdates(){
  $response = $this->query('getUpdates', [
    'offset' => $this->updateId + 1
  ]);

  if (!empty($response->result))
    $this->updateId = $response->result[count($response->result) -1]->update_id;

  return $response->result;
}

}
