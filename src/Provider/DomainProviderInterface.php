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

/**
 * Interface DomainProviderInterface
 * @package AutodiscoverXml\Provider
 */
interface DomainProviderInterface
{
    /**
     * Verify that the domain is served by the mail server
     *
     * @param string $domain
     * @return bool
     */
    public function verifyDomain(string $domain): bool;
}
