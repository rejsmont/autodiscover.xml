<?php

/*
 * This file is part of the Autodiscover.xml
 * 
 * Copyright (c) 2019 Radosław Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AutodiscoverXml\Services;


/**
 * Class Server
 * @package AutodiscoverXml\Services
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Server
{
    private $hostname;
    private $port;
    private $socket;
    private $auth;

    /**
     * Imap constructor.
     * @param $hostname
     * @param $port
     * @param $socket
     * @param $auth
     */
    public function __construct($hostname, $port, $socket, $auth)
    {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->socket = $socket;
        $this->auth = $auth;
    }

    /**
     * Get hostname
     *
     * @return mixed
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Get port
     *
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Get socket type
     *
     * @return mixed
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * Get auth schema
     *
     * @return mixed
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Get auth schema for Apple devices
     *
     * @return mixed
     */
    public function getAuthApple()
    {
        switch($this->auth) {
            case 'password-cleartext':
                return 'EmailAuthPassword';
                break;
            case 'password-encrypted':
                return 'EmailAuthCRAMMD5';
                break;
            case 'NTLM':
                return 'EmailAuthNTLM';
                break;
            case 'none':
                return 'EmailAuthNone';
                break;
            default:
                return null;
        }
    }

    /**
     * Does server support secure authentication
     *
     * @return bool
     */
    public function authSecure()
    {
        return ($this->auth == 'password-encrypted');
    }

    /**
     * Is SSL enabled?
     *
     * @return bool
     */
    public function sslEnabled()
    {
        return ($this->socket == 'SSL');
    }

    /**
     * Is TLS enabled?
     *
     * @return bool
     */
    public function tlsEnabled()
    {
        return ($this->socket == 'STARTTLS');
    }
}
