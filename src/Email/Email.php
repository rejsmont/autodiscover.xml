<?php

/*
 * This file is part of the Autodiscover.xml
 * 
 * Copyright (c) 2019 Radosław Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AutodiscoverXml\Email;


/**
 * Class Email
 * @package AutodiscoverXml\Email
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Email
{
    private $localPart;
    private $domainPart;

    /**
     * Email constructor.
     * @param string      $localPart
     * @param string|null $domainPart
     */
    public function __construct(string $localPart, $domainPart)
    {
        $this->localPart = $localPart;
        $this->domainPart = $domainPart;
    }

    /**
     * Get local (user) part of email
     *
     * @return string
     */
    public function getLocalPart()
    {
        return $this->localPart;
    }

    /**
     * Get domain part of email
     *
     * @return string
     */
    public function getDomainPart()
    {
        return $this->domainPart;
    }

    /**
     * Convert Email to string
     *
     * @return string
     */
    public function __toString()
    {
        if (null !== $this->domainPart) {
            return $this->localPart . '@' . $this->domainPart;
        } else {
            return $this->localPart;
        }
    }
}
