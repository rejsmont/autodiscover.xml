<?php

/*
 * This file is part of the XXX.
 * 
 * Copyright (c) 2019 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Provider;

use App\Provider\UsernameProviderInterface;

class UsernameProvider implements UsernameProviderInterface
{
    /**
     * @var UsernameProviderInterface[]
     */
    private $providers;

    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    public function getUsername(string $email)
    {
        foreach ($this->providers as $provider) {
            $username = $provider->getUsername($email);
            if (null !== $username) {
                return $username;
            }
        }

        return null;
    }
}
