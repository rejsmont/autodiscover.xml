<?php

/*
 * This file is part of the Autodiscover.xml
 * 
 * Copyright (c) 2019 Radosław Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AutodiscoverXml\User;


use AutodiscoverXml\Email\Email;

class User
{
    private $userName;
    private $displayName;
    private $email;
    private $domain;

    /**
     * User constructor.
     * @param $userName
     * @param $displayName
     * @param $email
     */
    public function __construct(Email $userName, $displayName, Email $email)
    {
        $this->userName = $userName;
        $this->displayName = $displayName;
        $this->email = $email;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Get display name
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Get email
     *
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
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
}
