<?php

/*
 * This file is part of the Autodiscover.xml
 * 
 * Copyright (c) 2019 Radosław Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AutodiscoverXml\Provider;


class UserProvider implements UserProviderInterface
{
    /**
     * @var UserProviderInterface[]
     */
    private $providers;

    /**
     * UserProvider constructor.
     * @param iterable $providers
     */
    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @inheritdoc
     */
    public function getUserName(string $email)
    {
        foreach ($this->providers as $provider) {
            $username = $provider->getUserName($email);
            if (null !== $username) {
                return $username;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getDisplayName(string $email)
    {
        foreach ($this->providers as $provider) {
            $username = $provider->getDisplayName($email);
            if (null !== $username) {
                return $username;
            }
        }

        return null;
    }
}
