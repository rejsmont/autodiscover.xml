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

use AutodiscoverXml\Provider\DomainProviderInterface;

class DomainProvider implements DomainProviderInterface
{
    /**
     * @var DomainProviderInterface[]
     */
    private $providers;

    /**
     * DomainProvider constructor.
     * @param iterable $providers
     */
    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @inheritdoc
     */
    public function verifyDomain(string $domain): bool
    {
        foreach ($this->providers as $provider) {
            if ($provider->verifyDomain($domain)) {
                return true;
            }
        }

        return false;
    }
}
