<?php

namespace App\CpanelApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class ApiHandler
{
    /**
     * @var array
     */
    protected $apiCredentials = [
        'user' => 'root',
        'token' => 'TOKEN'
    ];

    /**
     * @var string
     */
    protected $accountsListSearchUrl = 'https://rekrutacja.modulesgarden-demo.com:2087/json-api/listaccts?api.version=1';

    /**
     * @var string
     */
    protected $packagesListSearchUrl = 'https://rekrutacja.modulesgarden-demo.com:2087/json-api/listpkgs?api.version=1&want=all';

    /**
     * @var string
     */
    protected $createAccountUrl = 'https://rekrutacja.modulesgarden-demo.com:2087/json-api/createacct?api.version=1';

    /**
     * @var string
     */
    protected $removeAccountUrl = 'https://rekrutacja.modulesgarden-demo.com:2087/json-api/removeacct';

    /**
     * @var string
     */
    protected $modifyUserUrl = 'https://rekrutacja.modulesgarden-demo.com:2087/json-api/modifyacct?api.version=1';

    /**
     * @var string
     */
    protected $modifyUserPackageUrl = 'https://rekrutacja.modulesgarden-demo.com:2087/json-api/changepackage?api.version=1';

    /**
     * @var Client $client
     */
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function createAccount(User $user): array
    {
        $username = $user->getUsername();
        $domain = $user->getDomain();
        $email = $user->getEmail();
        $plan = $user->getPlan();

        try {
            $response = $this->client->post(
                $this->createAccountUrl .
                '&username=' . $username .
                '&domain=' . $domain .
                '&contactemail=' . $email .
                '&plan=' . $plan,
                [
                    'headers' => [
                        'Authorization' => 'whm ' . $this->apiCredentials['user'] . ':' . $this->apiCredentials['token']
                    ]
                ]
            );
        } catch (RequestException $e) {
            return [
                'message' => $e->getMessage()
            ];
        } catch (GuzzleException $e) {
            return [
                'message' => $e->getMessage()
            ];
        }
        $jsonDecoded = json_decode($response->getStatusCode(), true);

        if ($jsonDecoded === 200) {
            return [
                'message' => 'account has been added'
            ];
        } else {
            return [
                'message' => 'something went wrong'
            ];
        }
    }

    public function getPackagesList(): array
    {
        try {
            $response = $this->client->get(
                $this->packagesListSearchUrl,
                [
                    'headers' => [
                        'Authorization' => 'whm ' . $this->apiCredentials['user'] . ':' . $this->apiCredentials['token']
                    ]
                ]
            );
        } catch (RequestException $e) {
            return [
                'message' => $e->getMessage()
            ];
        } catch (GuzzleException $e) {
            return [
                'message' => $e->getMessage()
            ];
        }

        $jsonDecoded = json_decode($response->getBody(), true);
        $pkgData = $jsonDecoded['data']['pkg'];
        $pkgList = [];

        foreach ($pkgData as $row) {
            if ($row['name']) {
                array_push($pkgList, $row['name']);
            }
        }

        return $pkgList;
    }

    public function fetchAccountsByUserName(User $user): array
    {
        $username = $user->getUsername();

        try {
            $response = $this->client->post(
                $this->accountsListSearchUrl .
                '&search=' . $username .
                '&searchtype=user',
                [
                    'headers' => [
                        'Authorization' => 'whm ' . $this->apiCredentials['user'] . ':' . $this->apiCredentials['token']
                    ]
                ]
            );
        } catch (RequestException $e) {
            return [
                'message' => $e->getMessage()
            ];
        } catch (GuzzleException $e) {
            return [
                'message' => $e->getMessage()
            ];
        }

        $userList = json_decode($response->getBody(), true);

        return $userList['data']['acct'];
    }

    public function deleteAccount(User $user): array
    {
        $username = $user->getUsername();

        try {
            $response = $this->client->post(
                $this->removeAccountUrl .
                '?user=' . $username,
                [
                    'headers' => [
                        'Authorization' => 'whm ' . $this->apiCredentials['user'] . ':' . $this->apiCredentials['token']
                    ]
                ]
            );
        } catch (RequestException $e) {
            return [
                'message' => $e->getMessage()
            ];
        } catch (GuzzleException $e) {
            return [
                'message' => $e->getMessage()
            ];
        }

        $statusCode = json_decode($response->getStatusCode());

        if ($statusCode === 200) {
            return [
                'message' => 'Account has been removed'
            ];
        } else {
            return [
                'message' => 'Something went wrong'
            ];
        }
    }

    public function editUser(User $oldUser, User $newUser): array
    {
        $oldUsername = $oldUser->getUsername();

        $newUsername = $newUser->getUsername();
        $domain = $newUser->getDomain();
        $email = $newUser->getEmail();
        $plan = $newUser->getPlan();

        try {
            $userResponse = $this->client->post(
                $this->modifyUserUrl .
                '&user=' . $oldUsername .
                '&contactemail=' . $email .
                '&DNS=' . $domain .
                '&newuser=' . $newUsername ,
                [
                    'headers' => [
                        'Authorization' => 'whm ' . $this->apiCredentials['user'] . ':' . $this->apiCredentials['token']
                    ]
                ]
            );
            $pkgResponse = $this->client->post(
                $this->modifyUserPackageUrl .
                '&user=' . $newUsername .
                '&pkg=' . $plan,
                [
                    'headers' => [
                        'Authorization' => 'whm ' . $this->apiCredentials['user'] . ':' . $this->apiCredentials['token']
                    ]
                ]
            );
        } catch (RequestException $e) {
            return [
                'message' => $e->getMessage()
            ];
        } catch (GuzzleException $e) {
            return [
                'message' => $e->getMessage()
            ];
        }

        $accountStatusCode = json_decode($userResponse->getStatusCode());
        $pkgStatusCode = json_decode($pkgResponse->getStatusCode());

        if ($accountStatusCode === 200 && $pkgStatusCode === 200) {
            return [
                'message' => 'Account has been modified'
            ];
        } else {
            return [
                'message' => 'Something went wrong'
            ];
        }
    }
}