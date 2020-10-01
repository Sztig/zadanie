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
        'password' => 'password',
        'token' => 'token'
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
     * @var Client $client
     */
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function createAccount(array $user): array
    {
        try {
            $response = $this->client->post(
                $this->createAccountUrl .
                '&username=' . $user['username'] .
                '&domain=' . $user['domain'] .
                '&contactemail=' . $user['email'] .
                '&plan=' . $user['plan'],
                [
                    'headers' => [
                        'Authorization' => 'whm ' . $this->apiCredentials['user'] . ':' . $this->apiCredentials['token']
                    ]
                ]
            );
        } catch (RequestException $e) {
            return [
                'error' => $e->getMessage()
            ];
        } catch (GuzzleException $e) {
            return [
                'error' => $e->getMessage()
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
                'error' => $e->getMessage()
            ];
        } catch (GuzzleException $e) {
            return [
                'error' => $e->getMessage()
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

    public function fetchAccountsByUserNameFromApi(string $username): array
    {
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
                'error' => $e->getMessage()
            ];
        } catch (GuzzleException $e) {
            return [
                'error' => $e->getMessage()
            ];
        }

        $jsonDecoded = json_decode($response->getBody(), true);

        return $jsonDecoded['data']['acct'];
    }

}