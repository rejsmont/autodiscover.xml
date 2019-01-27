<?php

/*
 * This file is part of the XXX.
 * 
 * Copyright (c) 2019 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Email;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\EmailParser;
use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Validation\RFCValidation;

class EmailFactory
{
    private $validator;
    private $parser;

    public function __construct()
    {
        $this->validator = new EmailValidator();
        $this->parser = new EmailParser(new EmailLexer());
    }

    /**
     * @param $email
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
