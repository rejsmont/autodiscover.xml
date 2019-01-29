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

use AutodiscoverXml\Connection\LdapConnection;

/**
 * Class LdapDomainProvider
 * @package AutodiscoverXml\Provider
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class LdapDomainProvider implements DomainProviderInterface
{
    private $base;
    private $filter;
    private $attribute;
    private $ldap;

    /**
     * LdapDomainProvider constructor.
     * @param string $base       LDAP base
     * @param string $filter     LDAP filter
     * @param string $attribute  LDAP domain attribute
     * @param LdapConnection $ldap
     */
    public function __construct($base, $filter, $attribute, LdapConnection $ldap)
    {
        $this->base = $base;
        $this->filter = $filter;
        $this->attribute = $attribute;
        $this->ldap = $ldap;
    }

    /**
     * @inheritdoc
     */
    public function verifyDomain(string $domain): bool
    {
        // Check if this provider is configured
        if (null == $this->base) {
            return false;
        }

        $verified = false;
        $results = $this->ldap->query($this->base, str_replace('%s', $domain, $this->filter));

        foreach ($results as $entry) {
            $values = $entry->getAttribute($this->attribute);
            if (null !== $values) {
                foreach ($values as $value) {
                    if ($value == $domain) {
                        $verified = true;
                        break 2;
                    }
                }
            }
        }

        return $verified;
    }
}
