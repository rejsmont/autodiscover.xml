<?php

/*
 * This file is part of the XXX.
 * 
 * Copyright (c) 2019 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AutodiscoverXml\Email;


class Email
{
    private $localPart;
    private $domainPart;

    public function __construct(string $localPart, $domainPart)
    {
        $this->localPart = $localPart;
        $this->domainPart = $domainPart;
    }

    /**
     * @return string
     */
    public function getLocalPart()
    {
        return $this->localPart;
    }

    /**
     * @return string
     */
    public function getDomainPart()
    {
        return $this->domainPart;
    }

    public function __toString()
    {
        if (null !== $this->domainPart) {
            return $this->localPart . '@' . $this->domainPart;
        } else {
            return $this->localPart;
        }
    }
}
