<?php

/** @noinspection PhpUnhandledExceptionInspection */

switch (explode('?', $_SERVER['REQUEST_URI'], 2)[0]) {
    case '/throw/an/error':
        throw new Exception('Foo');
    default:
        echo '<h1>PHP Apache</h1>';
};
