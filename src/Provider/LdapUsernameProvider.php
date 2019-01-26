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

use App\Connection\LdapConnection;

/**
 * Class LdapUsernameProvider
 * @package App\Provider
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class LdapUsernameProvider implements UsernameProviderInterface
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
     * @param string $email
     * @return string|null
     */
    public function getUsername(string $email)
    {
        $results = $this->ldap->query($this->base, str_replace('%s', $email, $this->filter));

        if (count($results) != 1) {
            return null;
        }

        $entry = $results[0];
        $values = $entry->getAttribute($this->attribute);

        if (count($values) != 1) {
            return null;
        }

        return $values[0];
    }
}
