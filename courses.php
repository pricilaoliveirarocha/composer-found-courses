<?php

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

require __DIR__ . '/vendor/autoload.php';

$client = new Client([
    'base_uri' => 'https://www.alura.com.br',
    'timeout' => 10,
]);

$response = $client->request('GET', '/cursos-online-programacao');
$crawler = new Crawler(
    (string) $response->getBody(),
    'https://www.alura.com.br/cursos-online-programacao',
);

$cursosBackEnd = [];

$crawler->filter('a[href*="/curso-online-"]')->each(
    function (Crawler $link) use (&$cursosBackEnd): void {
        $url = $link->link()->getUri();
        $rotulo = $link->attr('aria-label');
        $titulo = $rotulo !== null
            ? preg_replace('/^Abrir back-end\s+/i', '', trim($rotulo))
            : trim($link->text());

        if ($titulo !== '' && preg_match('/\bPHP\b/i', $titulo) === 1) {
            $cursosBackEnd[$url] = [
                'titulo' => $titulo,
                'link' => $url,
            ];
        }
    },
);

$cursosBackEnd = array_values($cursosBackEnd);

usort(
    $cursosBackEnd,
    fn(array $primeiro, array $segundo): int => strnatcasecmp(
        $primeiro['titulo'],
        $segundo['titulo'],
    ),
);

foreach ($cursosBackEnd as $curso) {
    echo $curso['titulo'] . PHP_EOL;
    echo ($curso['link'] ?? 'Link não encontrado') . PHP_EOL . PHP_EOL;
}

echo 'Total de cursos com PHP: ' . count($cursosBackEnd) . PHP_EOL;
