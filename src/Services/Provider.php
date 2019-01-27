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


class Provider
{
    private $domain;
    private $name;
    private $shortName;

    public function __construct($domain, $name, $shortName = null)
    {
        $this->domain = $domain;
        $this->name = $name;
        $this->shortName = $shortName;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return mixed
     */
    public function getDomainRev()
    {
        $domainArray = explode('.', $this->domain);
        $domainRevArray = array_reverse($domainArray);

        return implode('.', $domainRevArray);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getShortName()
    {
        return $this->shortName;
    }
}
