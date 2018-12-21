<?php
// src/AppBundle/Security/MessageDigestPasswordEncoder.php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder as BaseMessageDigestPasswordEncoder;

class MessageDigestPasswordEncoder extends BaseMessageDigestPasswordEncoder
{
    private $algorithm;
    private $encodeHashAsBase64;
    private $iterations;

    public function __construct($algorithm = 'sha512', $encodeHashAsBase64 = true, $iterations = 5000)
    {
        $this->algorithm = $algorithm;
        $this->encodeHashAsBase64 = $encodeHashAsBase64;
        $this->iterations = $iterations;
    }

    protected function mergePasswordAndSalt($password, $salt)
    {
        return $password.$salt;
    }

    public function encodePassword($raw, $salt)
    {

        $salted = $this->mergePasswordAndSalt($raw, $salt);

        $digest = md5(sha1($salted));

        return $digest;
    }


}