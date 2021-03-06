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
use Symfony\Component\Ldap\Entry;


/**
 * Class LdapUsernameProvider
 * @package AutodiscoverXml\Provider
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class LdapUserProvider implements UserProviderInterface
{
    /**
     * @var Entry[]
     */
    private $entries;
    private $base;
    private $filter;
    private $userNameAttribute;
    private $displayNameAttribute;
    private $ldap;

    /**
     * LdapUserProvider constructor.
     * @param LdapConnection $ldap
     * @param string $base
     * @param string $filter
     * @param string $userNameAttribute
     * @param string $displayNameAttribute
     */
    public function __construct(LdapConnection $ldap, $base, $filter, $userNameAttribute, $displayNameAttribute)
    {
        $this->ldap = $ldap;
        $this->base = $base;
        $this->filter = $filter;
        $this->userNameAttribute = $userNameAttribute;
        $this->displayNameAttribute = $displayNameAttribute;
        $this->entries = [];
    }

    /**
     * @inheritdoc
     */
    public function getUserName(string $email)
    {
        return $this->getAttribute($email, $this->userNameAttribute);
    }

    /**
     * @inheritdoc
     */
    public function getDisplayName(string $email)
    {
        return $this->getAttribute($email, $this->displayNameAttribute);
    }

    /**
     * Get LDAP entry identified by email
     *
     * @param string $email
     * @return Entry|null
     */
    private function getEntry(string $email)
    {
        // Check if this provider is configured
        if (null == $this->base) {
            return null;
        }

        if (! array_key_exists($email, $this->entries)) {
            $results = $this->ldap->query($this->base, str_replace('%s', $email, $this->filter));
            if (count($results) != 1) {
                $this->entries[$email] = null;
            } else {
                $this->entries[$email] = $results[0];
            }
        }

        return $this->entries[$email];
    }

    /**
     * Get LDAP attribute for user identified by email
     *
     * @param string $email      Email to find in LDAP database
     * @param string $attribute  Return this LDAP attribute
     * @return string|null
     */
    private function getAttribute(string $email, string $attribute)
    {
        $entry = $this->getEntry($email);
        if (null == $entry) {
            return null;
        }

        $values = $entry->getAttribute($attribute);
        if (count($values) != 1) {
            return null;
        }

        return $values[0];
    }
}
