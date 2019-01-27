<?php

/*
 * This file is part of the XXX.
 * 
 * Copyright (c) 2019 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
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

    public function __construct($base, $filter, $attribute, LdapConnection $ldap)
    {
        $this->base = $base;
        $this->filter = $filter;
        $this->attribute = $attribute;
        $this->ldap = $ldap;
    }

    /**
     * @param string $domain
     * @return bool
     */
    public function verifyDomain(string $domain): bool
    {
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
