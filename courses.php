<?php

use GuzzleHttp\Client;

$client = new Client();
$response = $client->request('GET', 'https://cursos.alura.com.br/app/catalog');

$html = (string) $response->getBody();
