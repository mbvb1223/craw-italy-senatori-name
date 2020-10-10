<?php
require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

function getNames($number)
{
    $i = 0;
    while ($i !== 'stop') {
        $link = "https://storia.camera.it/deputati/faccette/all%7Cleg_repubblica:$number?da=$i#nav";
        $shouldContinue = getNameByPage($link, $number);

        $i += 20;
        if ($shouldContinue === false) {
            $i = "stop";
        }
    }
}

function getNameByPage($link, $number)
{
    try {
        $client = new Client();
        $response = $client->request('GET', $link);

        echo $response->getStatusCode(); // 200
        $crawler = new Crawler((string)$response->getBody());

        $numberPerPage = 0;
        $data = [];
        $crawler
            ->filter('.deputatiSx li a strong, .deputatiDx li a strong')
            ->each(function (Crawler $node, $i) use (&$data, &$numberPerPage, $number) {
                $numberPerPage++;
                $data[] = ["$number Legislatura", $node->text()];
            });

        $fp = fopen('Deputati.csv', 'a+');

        foreach ($data as $fields) {
            fputcsv($fp, $fields);
            echo ".";
        }

        fclose($fp);

        if ($numberPerPage < 20) {
            return false;
        }

    } catch (Exception $e) {
        echo "(^_^ " . $e->getCode() . " ^_^)";
    }
}

for ($i = 1; $i <= 18; $i++) {
    echo 'START_PAGE - ' . $page = $i . PHP_EOL;

    getNames($i);

    echo PHP_EOL;
    echo 'PAGE' . $page = sprintf('%02d', $i) . PHP_EOL;
    sleep(2);
}