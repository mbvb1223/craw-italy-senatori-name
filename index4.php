<?php
require './crawler.php';
require __DIR__ . '/simple_html_dom.php';

use Symfony\Component\DomCrawler\Crawler;

function getBODByTime()
{
    for ($i = 2018; $i <= 2020; $i++) {
        $link = "http://www.consob.it/web/consob-and-its-activities/search-listed-companies?viewId=ricerca_quotate&viewres=1&search=1&&resultmethod=socquotmr&queryid=main.emittenti.societa_quotate.form_avanzato&maxres=500&subject=ors&startdate=$i-12-01&enddate=$i-12-31";
        getBOD($link, $i);
    }
}

function getBOD($link, $year)
{
    try {
        $i = 0;
        while ($i !== 'stop') {
            $newlink = $link . "&firstres=$i";
            $crawler = getCrawlerError($newlink);

            $isExitingData = $crawler
                ->filter('.consobResult li .div80 a:first-child')->getNode(0);

            if (!$isExitingData) {
                return;
            }

            $crawler
                ->filter('.consobResult li .div80 a:first-child')
                ->each(function (Crawler $node, $i) use ($year) {
                    var_dump($i);
                    var_dump($node->text());

                    $listBod = getListBodName($node->attr('href'));
                    $companyName = str_replace('- Board Members','', $node->text());

                    $data = [];
                    if (!$listBod) {
                        $listBod = [['', '']];
                    }

                    foreach ($listBod as $key => $item) {
                        if ($key != 0) {
                            $companyName = '';
                        }
                        $data[] = [$companyName, $item[0], $item[1]];
                    }

                    writeData("bod-$year.csv", $data);

                });

            $i += 50;
        }
    } catch (Exception $e) {
        echo "(^_^ " . $e->getCode() . " ^_^)";
    }
}

function getListBodName($link)
{
    $link = str_replace("javascript:liferayLinkHook('", '', $link);
    $link = str_replace("');", '', $link);

    $link = "http://www.consob.it/web/consob-and-its-activities/listed-companies" . $link;

    $crawler = getCrawlerError2($link);

    $bodName = [];
    $crawler
        ->filter('tr')
        ->each(function (Crawler $node, $i) use (&$bodName) {
            if ($i === 0 || $i === 1) {
                return;
            }

            $name = $node->filter('td')->first();
            $position = $node->filter('td')->eq(1);

            $bodName[] = [$name->text(), $position->text()];
        });

    return $bodName;
}

getBODByTime();