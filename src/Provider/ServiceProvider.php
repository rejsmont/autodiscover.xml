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

use AutodiscoverXml\Services\Provider;
use AutodiscoverXml\Services\ServerFactory;


/**
 * Class ServiceProvider
 * @package AutodiscoverXml\Provider
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ServiceProvider
{
    /**
     * @var UserProviderInterface[]
     */
    private $imaps;
    private $smtps;
    private $pop3s;
    private $activesync;
    private $serverFactory;
    private $providerName;
    private $providerDomain;
    private $providerShortName;

    /**
     * ServiceProvider constructor.
     * @param ServerFactory $serverFactory
     * @param $imaps
     * @param $pop3s
     * @param $smtps
     * @param $activesync
     * @param $providerName
     * @param $providerDomain
     * @param $providerShortName
     */
    public function __construct(ServerFactory $serverFactory, $imaps, $pop3s, $smtps, $activesync,
                                $providerName, $providerDomain, $providerShortName = null)
    {
        $this->imaps = $imaps;
        $this->pop3s = $pop3s;
        $this->smtps = $smtps;
        $this->activesync = $activesync;
        $this->serverFactory = $serverFactory;
        $this->providerName = $providerName;
        $this->providerDomain = $providerDomain;
        $this->providerShortName = $providerShortName;
    }

    /**
     * Get provider data
     *
     * @return Provider
     */
    public function getProvider()
    {
        return new Provider($this->providerDomain, $this->providerName, $this->providerShortName);
    }

    /**
     * Get IMAP servers
     *
     * @return array
     */
    public function getImap()
    {
        return $this->createServers($this->imaps);
    }

    /**
     * Get SMTP servers
     *
     * @return array
     */
    public function getSmtp()
    {
        return $this->createServers($this->smtps);
    }

    /**
     * Get POP3 servers
     *
     * @return array
     */
    public function getPop3()
    {
        return $this->createServers($this->pop3s);
    }

    /**
     * Get ActiveSync url
     *
     * @return string
     */
    public function getActiveSync()
    {
        return $this->activesync;
    }

    /**
     * Create servers from the url array
     *
     * @param $urls
     * @return array
     */
    private function createServers($urls)
    {
        $servers = array();
        foreach($urls as $url) {
            array_push($servers, $this->serverFactory->fromUrl($url));
        }

        return $servers;
    }
}
