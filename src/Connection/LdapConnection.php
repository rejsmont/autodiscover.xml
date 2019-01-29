<?php

/*
 * This file is part of the Autodiscover.xml
 * 
 * Copyright (c) 2019 Radosław Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AutodiscoverXml\Connection;

use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Ldap;


/**
 * Class LdapConnection
 * @package AutodiscoverXml\Connection
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class LdapConnection
{
    private $server;
    private $dn;
    private $password;
    private $bound;
    private $ldap;


    /**
     * LdapConnection constructor.
     * @param string $url  LDAP server URL
     */
    public function __construct($url = 'ldap://localhost')
    {
        $this->parseUrl($url);
        $this->bound = false;
        $this->ldap = Ldap::create('ext_ldap', ['connection_string' => $this->server]);
    }

    /**
     * Query the LDAP server
     *
     * @param $base   string  LDAP base
     * @param $filter string  LDAP filter
     * @return Entry[]
     */
    public function query(string $base, string $filter) {
        if (! $this->bound) {
            $this->ldap->bind($this->dn, $this->password);
        }
        $query = $this->ldap->query($base, $filter);

        return $query->execute()->toArray();
    }

    /**
     * Parse LDAP server URL
     *
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
