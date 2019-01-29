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

use Doctrine\DBAL\Driver\Connection;


/**
 * Class DoctrineDomainProvider
 * @package AutodiscoverXml\Provider
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class DoctrineDomainProvider implements DomainProviderInterface
{
    private $connection;
    private $query;

    /**
     * DoctrineDomainProvider constructor.
     * @param Connection $connection
     * @param string $query
     */
    public function __construct(Connection $connection, string $query)
    {
        $this->connection = $connection;
        $this->query = str_replace('%s', '?', $query);
    }

    /**
     * @inheritdoc
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
