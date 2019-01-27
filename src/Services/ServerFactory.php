<?php

/*
 * This file is part of the XXX.
 * 
 * Copyright (c) 2019 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AutodiscoverXml\Services;


/**
 * Class ServerFactory
 * @package AutodiscoverXml\Services
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ServerFactory
{
    /**
     * @param $url
     * @return Server|null
     */
    public function fromUrl($url)
    {
        $components = parse_url($url);
        $scheme = $this->get($components['scheme'], null);
        $auth = $this->get($components['pass'], 'password-cleartext');
        $host = $this->get($components['host'], null);
        $port = $this->get($components['port'], null);
        $path = $this->get($components['path'], null);

        $ssl = false;
        switch($scheme) {
            case 'imaps':
                $ssl = true;
                $port = (null === $port) ? 993 : $port;
            case 'imap':
                $protocol = 'imap';
                $port = (null === $port) ? 143 : $port;
                break;
            case 'pop3s':
                $ssl = true;
                $port = (null === $port) ? 995 : $port;
            case 'pop3':
                $protocol = 'pop3';
                $port = (null === $port) ? 110 : $port;
                break;
            case 'smtps':
            case 'ssmtp':
                $ssl = true;
                $port = (null === $port) ? 465 : $port;
            case 'submission':
                $port = (null === $port) ? 587 : $port;
            case 'smtp':
                $protocol = 'smtp';
                $port = (null === $port) ? 25 : $port;
                break;
            default:
                $protocol = null;
                break;
        }
        $tls = (strpos($path, 'tls') !== false);
        $socket = $ssl ? 'SSL' : ($tls ? 'STARTTLS' : 'plain');

        switch($protocol) {
            case 'imap':
                $server = new Imap($host, $port, $socket, $auth);
                break;
            case 'smtp':
                $server = new Smtp($host, $port, $socket, $auth);
                break;
            case 'pop3':
                $server = new Pop3($host, $port, $socket, $auth);
                break;
            default:
                $server = null;
        }

        return $server;
    }

    /**
     * @param $var
     * @param null $default
     * @return null
     */
    private function get(&$var, $default=null) {
        return isset($var) ? $var : $default;
    }
}
