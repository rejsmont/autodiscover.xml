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

/**
 * Interface UsernameProviderInterface
 * @package AutodiscoverXml\Provider
 */
interface UserProviderInterface
{
    /**
     * @param string $email
     * @return string
     */
    public function getUserName(string $email);

    /**
     * @param string $email
     * @return mixed
     */
    public function getDisplayName(string $email);

}
