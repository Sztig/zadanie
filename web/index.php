<?php

use App\CpanelApi\CpanelApi;

require __DIR__ . '/../vendor/autoload.php';

$loader = new Twig\Loader\FilesystemLoader('CpanelApi/Resources/View');
$twig = new Twig\Environment($loader);

$CpanelApi = new CpanelApi($twig);

$uri = $_SERVER['REQUEST_URI'];


switch (true) {
    case $uri === '/lists':
        echo $CpanelApi->renderSearchList();
        break;
    case $uri === '/add':
        echo $CpanelApi->renderCreateAccount();
        break;
    case (strpos($uri, '/delete')) !== false :
        echo $CpanelApi->renderDeleteAccount();
        break;
    case (strpos($uri, '/edit')) !== false :
        echo $CpanelApi->renderEditUser();
        break;
    default:
        echo $CpanelApi->renderDefaultPage();
        break;
}




