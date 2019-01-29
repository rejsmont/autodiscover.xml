<?php

/*
 * This file is part of the Autodiscover.xml
 * 
 * Copyright (c) 2019 Radosław Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AutodiscoverXml\Email;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\EmailParser;
use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Validation\RFCValidation;


/**
 * Class EmailFactory
 * @package AutodiscoverXml\Email
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class EmailFactory
{
    private $validator;
    private $parser;

    /**
     * EmailFactory constructor.
     */
    public function __construct()
    {
        $this->validator = new EmailValidator();
        $this->parser = new EmailParser(new EmailLexer());
    }

    /**
     * Create Email class from string
     *
     * @param  string $email
     * @return Email|null
     */
    public function fromString($email)
    {
        if ($this->validator->isValid($email, new RFCValidation())) {
            $parts = $this->parser->parse($email);
            return new Email($parts['local'], $parts['domain']);
        } else {
            return null;
        }
    }
}
