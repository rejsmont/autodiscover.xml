<?php

/*
 * This file is part of the XXX.
 * 
 * Copyright (c) 2019 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Connection;

use Symfony\Component\Ldap\Ldap;


class LdapConnection
{
    private $server;
    private $dn;
    private $password;
    private $bound;
    private $ldap;


    public function __construct($url)
    {
        $this->parseUrl($url);
        $this->bound = false;
        $this->ldap = Ldap::create('ext_ldap', ['connection_string' => $this->server]);
    }

    public function query($base, $filter) {
        if (! $this->bound) {
            $this->ldap->bind($this->dn, $this->password);
        }
        $query = $this->ldap->query($base, $filter);

        return $query->execute()->toArray();
    }

    /**
     * @param string $url
     */
    private function parseUrl(string $url) {
        $components = parse_url($url);
        $server = $components['scheme'] . '://' . $components['host'];
        if (array_key_exists('port', $components))
            $server .= ':' . $components['port'];
        $this->server = $server;
        $this->dn = urldecode($components['user']);
        $this->password = urldecode($components['pass']);
    }
}
