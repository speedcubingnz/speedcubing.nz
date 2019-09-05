<?php

namespace App\Provider;

use App\Provider\WorldCubeAssociationUser;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\GenericProvider;

class WorldCubeAssociation extends GenericProvider
{
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new WorldCubeAssociationUser($response);
    }
}
