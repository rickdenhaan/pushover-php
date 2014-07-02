<?php
/**
 * Prepares a simple autoloader for the Capirussa\Pushover namespace
 */

// handle autoloading
spl_autoload_register(
    function ($className) {
        if ($className === 'MockClient') {
            require_once(realpath(dirname(__FILE__) . '/Capirussa/Pushover/MockClient.php'));
            return true;
        }

        if (preg_match('/^Capirussa\\\\Pushover/', $className)) {
            $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
            if (file_exists($filePath)) {
                require_once(realpath(dirname(__FILE__) . '/../' . $filePath));
                return true;
            }
        }
        return false;
    }
);

