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
 * Class Provider
 * @package AutodiscoverXml\Services
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Provider
{
    private $domain;
    private $name;
    private $shortName;

    /**
     * Provider constructor.
     * @param string $domain
     * @param string $name
     * @param string $shortName
     */
    public function __construct($domain, $name, $shortName = null)
    {
        $this->domain = $domain;
        $this->name = $name;
        $this->shortName = $shortName;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Get domain in reverse notation
     *
     * @return string
     */
    public function getDomainRev()
    {
        $domainArray = explode('.', $this->domain);
        $domainRevArray = array_reverse($domainArray);

        return implode('.', $domainRevArray);
    }

    /**
     * Get provider name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get short name
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }
}
