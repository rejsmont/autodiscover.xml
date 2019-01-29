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
class DoctrineUserProvider implements UserProviderInterface
{
    private $connection;
    private $query;
    private $userNameField;
    private $displayNameField;
    private $rows;

    /**
     * DoctrineUserProvider constructor.
     * @param Connection $connection
     * @param string $query
     * @param string $userNameField
     * @param string $displayNameField
     */
    public function __construct(Connection $connection, $query, $userNameField, $displayNameField)
    {
        $this->connection = $connection;
        $this->query = str_replace('%s', '?', $query);
        $this->userNameField = $userNameField;
        $this->displayNameField = $displayNameField;
        $this->rows = [];
    }

    /**
     * @inheritdoc
     */
    public function getUserName(string $email)
    {
        return $this->getField($email, $this->userNameField);
    }

    /**
     * @inheritdoc
     */
    public function getDisplayName(string $email)
    {
        return $this->getField($email, $this->displayNameField);
    }

    /**
     * Get row from the SQL database
     *
     * @param string $email
     * @return array
     */
    private function getRow(string $email)
    {
        if (null == $this->query) {
            return null;
        }

        if (! array_key_exists($email, $this->rows)) {
            $statement = $this->connection->prepare($this->query);
            $statement->bindValue(1, $email);
            $statement->execute();
            $results = $statement->fetchAll();
            if (count($results) != 1) {
                $this->rows[$email] = null;
            } else {
                $this->rows[$email] = $results[0];
            }
        }

        return $this->rows[$email];
    }

    /**
     * Get field from the SQL result array
     *
     * @param string $email  Email to find in SQL database
     * @param string $field  SQL field
     * @return string|null
     */
    private function getField(string $email, string $field)
    {
        $row = $this->getRow($email);
        if (null == $row) {
            return null;
        }

        $values = $row[$field];
        if (count($values) != 1) {
            return null;
        }

        return $values[0];
    }
}
