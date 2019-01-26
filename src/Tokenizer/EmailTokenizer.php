<?php

/*
 * This file is part of the XXX.
 * 
 * Copyright (c) 2019 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Tokenizer;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\EmailParser;
use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Validation\RFCValidation;

class EmailTokenizer
{
    private $validator;
    private $parser;

    public function __construct()
    {
        $this->validator = new EmailValidator();
        $this->parser = new EmailParser(new EmailLexer());
    }

    /**
     * Tokenize email to local and domain parts
     *
     * @param string $email
     * @return array
     */
    public function tokenize(string $email): array
    {
        if ($this->validator->isValid($email, new RFCValidation())) {
            return $this->parser->parse($email);
        } else {
            return null;
        }
    }

    public function getDomainPart(string $email): string
    {
        if ($this->validator->isValid($email, new RFCValidation())) {
            $parts = $this->parser->parse($email);
            return $parts['domain'];
        } else {
            return null;
        }
    }

    public function getLocalPart(string $email): string
    {
        if ($this->validator->isValid($email, new RFCValidation())) {
            $parts = $this->parser->parse($email);
            return $parts['local'];
        } else {
            return null;
        }
    }
}
