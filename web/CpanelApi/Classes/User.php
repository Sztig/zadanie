<?php

namespace App\CpanelApi;

class User {

    /**
     * @var string $username
     */
    private $username;

    /**
     * @var int $id
     */
    private $id;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var string $plan
     */
    private $plan;

    /**
     * @var string $domain
     */
    private $domain;

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPlan(): string
    {
        return $this->plan;
    }

    /**
     * @param string $plan
     */
    public function setPlan(string $plan): void
    {
        $this->plan = $plan;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

}