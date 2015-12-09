<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace Jose\Test;

use Jose\Algorithm\ContentEncryption\A128CBCHS256;
use Jose\Algorithm\ContentEncryption\A128GCM;
use Jose\Algorithm\ContentEncryption\A192CBCHS384;
use Jose\Algorithm\ContentEncryption\A192GCM;
use Jose\Algorithm\ContentEncryption\A256CBCHS512;
use Jose\Algorithm\ContentEncryption\A256GCM;
use Jose\Algorithm\KeyEncryption\A128GCMKW;
use Jose\Algorithm\KeyEncryption\A128KW;
use Jose\Algorithm\KeyEncryption\A192GCMKW;
use Jose\Algorithm\KeyEncryption\A192KW;
use Jose\Algorithm\KeyEncryption\A256GCMKW;
use Jose\Algorithm\KeyEncryption\A256KW;
use Jose\Algorithm\KeyEncryption\Dir;
use Jose\Algorithm\KeyEncryption\ECDHES;
use Jose\Algorithm\KeyEncryption\ECDHESA128KW;
use Jose\Algorithm\KeyEncryption\ECDHESA192KW;
use Jose\Algorithm\KeyEncryption\ECDHESA256KW;
use Jose\Algorithm\KeyEncryption\PBES2HS256A128KW;
use Jose\Algorithm\KeyEncryption\PBES2HS384A192KW;
use Jose\Algorithm\KeyEncryption\PBES2HS512A256KW;
use Jose\Algorithm\KeyEncryption\RSA15;
use Jose\Algorithm\KeyEncryption\RSAOAEP;
use Jose\Algorithm\KeyEncryption\RSAOAEP256;
use Jose\Algorithm\JWAManager;
use Jose\Algorithm\Signature\ES256;
use Jose\Algorithm\Signature\ES384;
use Jose\Algorithm\Signature\ES512;
use Jose\Algorithm\Signature\HS256;
use Jose\Algorithm\Signature\HS384;
use Jose\Algorithm\Signature\HS512;
use Jose\Algorithm\Signature\None;
use Jose\Algorithm\Signature\PS256;
use Jose\Algorithm\Signature\PS384;
use Jose\Algorithm\Signature\PS512;
use Jose\Algorithm\Signature\RS256;
use Jose\Algorithm\Signature\RS384;
use Jose\Algorithm\Signature\RS512;
use Jose\Checker\AudienceChecker;
use Jose\Checker\CheckerManager;
use Jose\Checker\CriticalChecker;
use Jose\Checker\ExpirationChecker;
use Jose\Checker\IssuedAtChecker;
use Jose\Checker\NotBeforeChecker;
use Jose\Compression\CompressionManager;
use Jose\Compression\Deflate;
use Jose\Compression\GZip;
use Jose\Compression\ZLib;
use Jose\Encrypter;
use Jose\Finder\JWKFinder;
use Jose\Finder\X5CFinder;
use Jose\Finder\JWKFinderManager;
use Jose\Loader;
use Jose\Payload\JWKConverter;
use Jose\Payload\JWKSetConverter;
use Jose\Payload\PayloadConverterManager;
use Jose\Signer;
use Jose\Test\Stub\AlgorithmFinder;
use Jose\Test\Stub\APVFinder;
use Jose\Test\Stub\IssuerChecker;
use Jose\Test\Stub\KIDFinder;
use Jose\Test\Stub\SubjectChecker;

/**
 * Class TestCase.
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Loader
     */
    protected function getLoader()
    {
        $loader = new Loader(
            $this->getJWAManager(),
            $this->getJWKFinderManager(),
            $this->getPayloadConverterManager(),
            $this->getCompressionManager(),
            $this->getCheckerManager()
        );

        return $loader;
    }

    /**
     * @return Signer
     */
    protected function getSigner()
    {
        $signer = new Signer(
            $this->getJWAManager(),
            $this->getPayloadConverterManager()
        );

        return $signer;
    }

    /**
     * @return Encrypter
     */
    protected function getEncrypter()
    {
        $encrypter = new Encrypter(
            $this->getJWAManager(),
            $this->getPayloadConverterManager(),
            $this->getCompressionManager()
        );

        return $encrypter;
    }

    /**
     * @return \Jose\Checker\CheckerManagerInterface
     */
    protected function getCheckerManager()
    {
        $checker_manager = new CheckerManager();

        $checker_manager->addChecker(new AudienceChecker('My service'));
        $checker_manager->addChecker(new CriticalChecker());
        $checker_manager->addChecker(new ExpirationChecker());
        $checker_manager->addChecker(new NotBeforeChecker());
        $checker_manager->addChecker(new IssuedAtChecker());
        $checker_manager->addChecker(new IssuerChecker());
        $checker_manager->addChecker(new SubjectChecker());

        return $checker_manager;
    }

    /**
     * @return \Jose\Payload\PayloadConverterManagerInterface
     */
    protected function getPayloadConverterManager()
    {
        $payload_converter_manager = new PayloadConverterManager();
        $payload_converter_manager->addConverter(new JWKConverter());
        $payload_converter_manager->addConverter(new JWKSetConverter());

        return $payload_converter_manager;
    }

    /**
     * @return \Jose\Compression\CompressionManager
     */
    protected function getCompressionManager()
    {
        $compression_manager = new CompressionManager();
        $compression_manager->addCompressionAlgorithm(new Deflate());
        $compression_manager->addCompressionAlgorithm(new GZip());
        $compression_manager->addCompressionAlgorithm(new ZLib());

        return $compression_manager;
    }

    /**
     * @return \Jose\Finder\JWKFinderManagerInterface
     */
    protected function getJWKFinderManager()
    {
        $jwk_finder_manager = new JWKFinderManager();

        $jwk_finder_manager->addJWKFinder(new JWKFinder());
        $jwk_finder_manager->addJWKFinder(new X5CFinder());
        $jwk_finder_manager->addJWKFinder(new APVFinder());
        $jwk_finder_manager->addJWKFinder(new KIDFinder());
        $jwk_finder_manager->addJWKFinder(new AlgorithmFinder());

        return $jwk_finder_manager;
    }

    /**
     * @return \Jose\Algorithm\JWAManager
     */
    protected function getJWAManager()
    {
        $key_manager = new JWAManager();
        $key_manager->addAlgorithm(new HS256());
        $key_manager->addAlgorithm(new HS384());
        $key_manager->addAlgorithm(new HS512());
        $key_manager->addAlgorithm(new RS256());
        $key_manager->addAlgorithm(new RS384());
        $key_manager->addAlgorithm(new RS512());
        $key_manager->addAlgorithm(new PS256());
        $key_manager->addAlgorithm(new PS384());
        $key_manager->addAlgorithm(new PS512());
        $key_manager->addAlgorithm(new None());
        $key_manager->addAlgorithm(new ES256());
        $key_manager->addAlgorithm(new ES384());
        $key_manager->addAlgorithm(new ES512());

        $key_manager->addAlgorithm(new A128CBCHS256());
        $key_manager->addAlgorithm(new A192CBCHS384());
        $key_manager->addAlgorithm(new A256CBCHS512());

        $key_manager->addAlgorithm(new A128KW());
        $key_manager->addAlgorithm(new A192KW());
        $key_manager->addAlgorithm(new A256KW());
        $key_manager->addAlgorithm(new Dir());
        $key_manager->addAlgorithm(new ECDHES());
        $key_manager->addAlgorithm(new ECDHESA128KW());
        $key_manager->addAlgorithm(new ECDHESA192KW());
        $key_manager->addAlgorithm(new ECDHESA256KW());
        $key_manager->addAlgorithm(new PBES2HS256A128KW());
        $key_manager->addAlgorithm(new PBES2HS384A192KW());
        $key_manager->addAlgorithm(new PBES2HS512A256KW());
        $key_manager->addAlgorithm(new RSA15());
        $key_manager->addAlgorithm(new RSAOAEP());
        $key_manager->addAlgorithm(new RSAOAEP256());

        if ($this->isCryptoExtensionAvailable()) {
            $key_manager->addAlgorithm(new A128GCM());
            $key_manager->addAlgorithm(new A192GCM());
            $key_manager->addAlgorithm(new A256GCM());
            $key_manager->addAlgorithm(new A128GCMKW());
            $key_manager->addAlgorithm(new A192GCMKW());
            $key_manager->addAlgorithm(new A256GCMKW());
        }

        return $key_manager;
    }

    /**
     * @return bool
     */
    private function isCryptoExtensionAvailable()
    {
        return class_exists('\Crypto\Cipher');
    }
}
