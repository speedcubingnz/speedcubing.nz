<?php

namespace App\Provider;

use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\GenericResourceOwner;

class WorldCubeAssociationUser extends GenericResourceOwner
{
    protected $data;

    public function __construct(array $response)
    {
        if (isset($response['me'])) {
            $this->data = $response['me'];
        } else {
            $this->data = $response;
        }
    }

    public function getResourceOwner(AccessToken $token)
    {
        $response = $this->fetchResourceOwnerDetails($token);
        return $this->createResourceOwner($response->me, $token);
    }

    public function getEmail()
    {
        return $this->getField('email');
    }

    public function getName()
    {
        return $this->getField('name');
    }

    private function getField($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
}
