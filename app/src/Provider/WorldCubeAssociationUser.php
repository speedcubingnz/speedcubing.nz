<?php

namespace App\Provider;

use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\GenericResourceOwner;

class WorldCubeAssociationUser extends GenericResourceOwner
{
    public function getId()
    {
        return $this->response['me'][$this->resourceOwnerId];
    }

    private function getField($key)
    {
        return isset($this->response['me'][$key]) ? $this->response['me'][$key] : null;
    }

    public function toArray()
    {
        return $this->response['me'];
    }
}
