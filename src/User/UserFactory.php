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
use AutodiscoverXml\Email\EmailFactory;
use AutodiscoverXml\Provider\UserProvider;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\EmailParser;
use Egulias\EmailValidator\EmailLexer;


/**
 * Class UserFactory
 * @package AutodiscoverXml\User
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class UserFactory
{
    private $validator;
    private $parser;
    private $userProvider;
    private $emailFactory;

    /**
     * UserFactory constructor.
     * @param UserProvider $userProvider
     * @param EmailFactory $emailFactory
     */
    public function __construct(UserProvider $userProvider, EmailFactory $emailFactory)
    {
        $this->validator = new EmailValidator();
        $this->parser = new EmailParser(new EmailLexer());
        $this->userProvider = $userProvider;
        $this->emailFactory = $emailFactory;
    }

    /**
     * Create user from email
     *
     * @param string $email
     * @return User|null
     */
    public function fromString($email)
    {
        if (null === $email) {
            return null;
        }

        $userNameString = $this->userProvider->getUsername($email);
        if (null === $userNameString) {
            $userNameString = $email;
        }

        $displayName = $this->userProvider->getDisplayName($email);
        if (null === $displayName) {
            $displayName = $email;
        }

        $userName = $this->emailFactory->fromString($userNameString);
        if (null === $userName) {
            $userName = new Email($userNameString, null);
        }

        return new User($userName, $displayName, $email);
    }
}
