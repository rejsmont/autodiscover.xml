<?php

/*
 * This file is part of the XXX.
 * 
 * Copyright (c) 2019 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Services;


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
     * @return mixed
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return mixed
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * @return mixed
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
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

    public function authSecure()
    {
        return ($this->auth == 'password-encrypted');
    }

    public function sslEnabled()
    {
        return ($this->socket == 'SSL');
    }

    public function tlsEnabled()
    {
        return ($this->socket == 'STARTTLS');
    }
}
