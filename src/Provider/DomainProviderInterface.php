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
 * Interface DomainProviderInterface
 * @package AutodiscoverXml\Provider
 */
interface DomainProviderInterface
{
    /**
     * @param string $domain
     * @return bool
     */
    public function verifyDomain(string $domain): bool;
}
