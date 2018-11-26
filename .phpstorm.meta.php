<?php

namespace PHPSTORM_META {

    $STATIC_METHOD_TYPES = [
        \PHPUnit\Framework\TestCase::createMock('')  => [
            "" == "@",
        ],
        \Zend\ServiceManager\ServiceManager::get('') => [
            "" == "@",
        ],
    ];
}
