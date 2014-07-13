<?php
/**
 * Prepares a simple autoloader for the Capirussa\Pushover namespace
 */

date_default_timezone_set('Europe/Amsterdam');

// handle autoloading
spl_autoload_register(
    function ($className) {
        if ($className === 'MockClient') {
            require_once(dirname(__FILE__) . '/Capirussa/Pushover/mock/MockClient.php');
            return true;
        }

        if (preg_match('/^Capirussa\\\\Pushover/', $className)) {
            $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
            if (file_exists($filePath)) {
                require_once(dirname(__FILE__) . '/../' . $filePath);
                return true;
            }
        }
        return false;
    }
);

/**
 * Outputs log entries for unittest debugging
 *
 * @param string $message
 */
function unittest_log($message)
{
    printf(
        "[%1\$s] %2\$s\n",
        date('H:i:s'),
        $message
    );
}