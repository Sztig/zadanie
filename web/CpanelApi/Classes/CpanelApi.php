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
     * @var User $user
     */
    protected $user;

    /**
     * CpanelApi constructor.
     * @param Environment $twig
     * @param string $URI
     */
    public function __construct($twig)
    {
        $this->apiHandler = new ApiHandler();
        $this->loadTemplate($twig);
    }

    public function renderDefaultPage()
    {
        return $this->template->render();
    }

    public function renderSearchList()
    {
        if (!empty($_POST)) {
            $user = new User();
            $user->setUsername($_POST['username']);
            $data = $this->apiHandler->fetchAccountsByUserName($user);
        }

        if (isset($data['message'])) {
            return $this->template->render([
                'message' => $data
            ]);
        } else {
            return $this->template->render([
                'data' => $data
            ]);
        }
    }

    public function renderCreateAccount()
    {
        if (!empty($_POST)) {
            $errorArray = [];

            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
                $errorArray['emailError'] = 'Email is invalid!';
            }


            if (filter_var($_POST['domain'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                $errorArray['domainError'] = 'Domain is invalid!';
            }

            if (!empty($errorArray)) {
                $data = $this->apiHandler->getPackagesList();

                return $this->template->render([
                        'data' => $data,
                        'errors' => $errorArray
                    ]
                );
            } else {

                $user = new User();
                $user->setUsername($_POST['username']);
                $user->setDomain($_POST['domain']);
                $user->setEmail($_POST['email']);
                $user->setPlan($_POST['plan']);

                $this->apiHandler->createAccount($user);
                return $this->template->render();
            }
        } else {
            $data = $this->apiHandler->getPackagesList();

            return $this->template->render([
                    'data' => $data
                ]
            );
        }
    }

    public function renderDeleteAccount()
    {
        $uri = $_SERVER['REQUEST_URI'];

        if ($uri !== '/delete') {
            $username = str_replace('/delete/', "", $uri);
            $user = new User();
            $user->setUsername($username);

            $data = $this->apiHandler->deleteAccount($user);
            return $this->template->render([
                'data' => $data
            ]);
        } else {
            return $this->template->render([
                'data' => 'Something went wrong'
            ]);
        }
    }

    public function renderEditUser()
    {
        $uri = $_SERVER['REQUEST_URI'];

        if (!empty($_POST) && $uri !== '/edit') {
            $errorArray = [];

            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
                $errorArray['emailError'] = 'Email is invalid!';
            }


            if (filter_var($_POST['domain'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                $errorArray['domainError'] = 'Domain is invalid!';
            }

            if (!empty($errorArray)) {
                $pkgList = $this->apiHandler->getPackagesList();

                $username = str_replace('/delete/', "", $uri);
                $user = new User();
                $user->setUsername($username);

                return $this->template->render([
                    'pkgList' => $pkgList,
                    'oldUser' => $user,
                    'errors' => $errorArray
                ]);
            } else {
                $oldUser = new User();
                $oldUsername = str_replace('/edit/', "", $_POST['oldUsername']);
                $oldUser->setUsername($oldUsername);

                $newUser = new User();
                $newUser->setUsername($_POST['username']);
                $newUser->setEmail($_POST['email']);
                $newUser->setPlan($_POST['plan']);
                $newUser->setDomain($_POST['domain']);

                $data = $this->apiHandler->editUser($oldUser, $newUser);

                return $this->template->render([
                    'data' => $data
                ]);
            }
        } else if (empty($_POST)) {
            $pkgList = $this->apiHandler->getPackagesList();

            $username = str_replace('/delete/', "", $uri);
            $user = new User();
            $user->setUsername($username);

            return $this->template->render([
                'pkgList' => $pkgList,
                'oldUser' => $user
            ]);
        } else {
            return $this->template->render([
                'message' => 'something went wrong',
            ]);
        }


    }

    /**
     * @param Environment $twig
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function loadTemplate($twig): void
    {
        $uri = $_SERVER['REQUEST_URI'];

        switch (true) {
            case $uri === '/lists':
                $this->template = $twig->load('search.html.twig');
                break;
            case $uri === '/add':
                $this->template = $twig->load('add.html.twig');
                break;
            case (strpos($uri, '/delete')) !== false:
                $this->template = $twig->load('delete.html.twig');
                break;
            case (strpos($uri, '/edit')) !== false:
                $this->template = $twig->load('edit.html.twig');
                break;
            default:
                $this->template = $twig->load('index.html.twig');
                break;
        }
    }


}