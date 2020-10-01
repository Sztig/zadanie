<?php

use App\CpanelApi\CpanelApi;

require __DIR__ . '/../vendor/autoload.php';

$loader = new Twig\Loader\FilesystemLoader('CpanelApi/Resources/View');
$twig = new Twig\Environment($loader, [
    'debug' => true
]);
$twig->addExtension(new \Twig\Extension\DebugExtension());
$URI = $_SERVER['REQUEST_URI'];

$CpanelApi = new CpanelApi($twig, $URI);

switch ($URI) {
    case '/lists':
        echo $CpanelApi->renderSearchList();
        break;
    case '/add':
        echo $CpanelApi->renderCreateAccount();
        break;
    default:
        echo $CpanelApi->renderDefaultPage();
        break;
}




