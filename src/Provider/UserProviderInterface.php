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

/**
 * Interface UsernameProviderInterface
 * @package AutodiscoverXml\Provider
 */
interface UserProviderInterface
{
    /**
     * Get username
     *
     * @param string $email
     * @return string|null
     */
    public function getUserName(string $email);

    /**
     * Get display name
     *
     * @param string $email
     * @return string|null
     */
    public function getDisplayName(string $email);

}
