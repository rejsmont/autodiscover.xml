<?php

/*
 * This file is part of the XXX.
 * 
 * Copyright (c) 2019 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Provider;

use Doctrine\DBAL\Driver\Connection;


/**
 * Class DoctrineDomainProvider
 * @package App\Provider
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class DoctrineDomainProvider implements DomainProviderInterface
{
    private $connection;
    private $query;

    public function __construct(Connection $connection, string $query)
    {
        $this->connection = $connection;
        $this->query = str_replace('%s', '?', $query);
    }

    /**
     * @param string $domain
     * @return bool
     */
    public function verifyDomain(string $domain): bool
    {
        $statement = $this->connection->prepare($this->query);
        $statement->bindValue(1, $domain);
        $statement->execute();
        $result = $statement->fetchAll();

        return count($result) > 0;
    }
}
