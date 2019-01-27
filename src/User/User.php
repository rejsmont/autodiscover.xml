<?php

/*
 * This file is part of the XXX.
 * 
 * Copyright (c) 2019 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\User;


use App\Email\Email;

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
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }
}
