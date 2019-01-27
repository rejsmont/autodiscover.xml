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
use App\Email\EmailFactory;
use App\Provider\UserProvider;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\EmailParser;
use Egulias\EmailValidator\EmailLexer;

class UserFactory
{
    private $validator;
    private $parser;
    private $userProvider;
    private $emailFactory;

    public function __construct(UserProvider $userProvider, EmailFactory $emailFactory)
    {
        $this->validator = new EmailValidator();
        $this->parser = new EmailParser(new EmailLexer());
        $this->userProvider = $userProvider;
        $this->emailFactory = $emailFactory;
    }

    /**
     * @param $email
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
