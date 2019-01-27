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


use AutodiscoverXml\Services\Provider;
use AutodiscoverXml\Services\ServerFactory;

class ServiceProvider
{
    /**
     * @var UserProviderInterface[]
     */
    private $imaps;
    private $smtps;
    private $pop3s;
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
     * @param $providerName
     * @param $providerDomain
     * @param $providerShortName
     */
    public function __construct(ServerFactory $serverFactory, $imaps, $pop3s, $smtps,
                                $providerName, $providerDomain, $providerShortName = null)
    {
        $this->imaps = $imaps;
        $this->pop3s = $pop3s;
        $this->smtps = $smtps;
        $this->serverFactory = $serverFactory;
        $this->providerName = $providerName;
        $this->providerDomain = $providerDomain;
        $this->providerShortName = $providerShortName;
    }

    public function getProvider()
    {
        return new Provider($this->providerDomain, $this->providerName, $this->providerShortName);
    }

    public function getImap()
    {
        return $this->createServers($this->imaps);
    }

    public function getSmtp()
    {
        return $this->createServers($this->smtps);
    }

    public function getPop3()
    {
        return $this->createServers($this->pop3s);
    }

    private function createServers($urls)
    {
        $servers = array();
        foreach($urls as $url) {
            array_push($servers, $this->serverFactory->fromUrl($url));
        }

        return $servers;
    }
}
