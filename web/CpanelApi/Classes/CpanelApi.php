<?php

namespace App\CpanelApi;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\TemplateWrapper;

class CpanelApi
{
    /**
     * @var TemplateWrapper $template
     */
    protected $template;

    /**
     * @var ApiHandler $apiHandler
     */
    protected $apiHandler;

    /**
     * CpanelApi constructor.
     * @param Environment $twig
     * @param string $URI
     */
    public function __construct($twig, $URI)
    {
        $this->apiHandler = new ApiHandler();
        $this->loadTemplate($twig, $URI);
    }

    public function renderDefaultPage()
    {
        return $this->template->render();
    }

    public function renderSearchList()
    {
        $data = [];

        if (!empty($_POST)) {
            $username = $_POST['username'];
            $data = $this->apiHandler->fetchAccountsByUserNameFromApi($username);
        }

        return $this->template->render([
            'data' => $data
        ]);
    }

    public function renderCreateAccount()
    {
        $data = [];

        if (!empty($_POST)) {
            $user = $_POST;
            $this->apiHandler->createAccount($user);

            return $this->template->render();
        } else {
            $data = $this->apiHandler->getPackagesList();
            return $this->template->render([
                    'data' => $data
                ]
            );
        }
    }

    /**
     * @param Environment $twig
     * @param string $URI
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function loadTemplate($twig, $URI): void
    {
        switch ($URI) {
            case '/lists':
                $this->template = $twig->load('search.html.twig');
                break;
            case '/add':
                $this->template = $twig->load('add.html.twig');
                break;
            default:
                $this->template = $twig->load('index.html.twig');
                break;
        }
    }


}