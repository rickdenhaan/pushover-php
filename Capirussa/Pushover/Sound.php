<?php
namespace Capirussa\Pushover;

/**
 * Pushover Sound class is simply a wrapper around constants defining the available sounds
 *
 * @package Capirussa\Pushover
 */
class Sound
{
    /**
     * Does not send a sound directive, so that what ever sound the user has selected as the default on his device is used
     *
     */
    const USER_DEFAULT = '';

    /**
     * Plays the default Pushover sound
     *
     */
    const PUSHOVER = 'pushover';

    /**
     * Plays the Bike sound
     *
     */
    const BIKE = 'bike';

    /**
     * Plays the Bugle sound
     *
     */
    const BUGLE = 'bugle';

    /**
     * Plays the Cash Register sound
     *
     */
    const CASH_REGISTER = 'cashregister';

    /**
     * Plays the Classical sound
     *
     */
    const CLASSICAL = 'classical';

    /**
     * Plays the Cosmic sound
     *
     */
    const COSMIC = 'cosmic';

    /**
     * Plays the Falling sound
     *
     */
    const FALLING = 'falling';

    /**
     * Plays the Gamelan sound
     *
     */
    const GAMELAN = 'gamelan';

    /**
     * Plays the Incoming sound
     *
     */
    const INCOMING = 'incoming';

    /**
     * Plays the Intermission sound
     *
     */
    const INTERMISSION = 'intermission';

    /**
     * Plays the Magic sound
     *
     */
    const MAGIC = 'magic';

    /**
     * Plays the Mechanical sound
     *
     */
    const MECHANICAL = 'mechanical';

    /**
     * Plays the Piano Bar sound
     *
     */
    const PIANO_BAR = 'pianobar';

    /**
     * Plays the Siren sound
     *
     */
    const SIREN = 'siren';

    /**
     * Plays the Space Alarm sound
     *
     */
    const SPACE_ALARM = 'spacealarm';

    /**
     * Plays the Tug Boat sound
     *
     */
    const TUG_BOAT = 'tugboat';

    /**
     * Plays the Alien Alarm (long) sound
     *
     */
    const ALIEN_ALARM = 'alien';

    /**
     * Plays the Climb (long) sound
     *
     */
    const CLIMB = 'climb';

    /**
     * Plays the Persistent (long) sound
     *
     */
    const PERSISTENT = 'persistent';

    /**
     * Plays the Pushover Echo (long) sound
     *
     */
    const PUSHOVER_ECHO = 'echo';

    /**
     * Plays the Up Down (long) sound
     *
     */
    const UP_DOWN = 'updown';

    /**
     * Delivers the message silently
     *
     */
    const NONE = 'none';

    /**
     * You're not supposed to instantiate this class, but if you do anyway, this will be the configured sound
     *
     * @var string
     */
    protected $sound = self::USER_DEFAULT;

    /**
     * You're not supposed to instantiate this class, but it is supported if you do anyway
     *
     * @param string $sound Sound to use
     * @throws \InvalidArgumentException if an invalid sound was given
     */
    public function __construct($sound = self::USER_DEFAULT)
    {
        if (function_exists('unittest_log')) unittest_log('Sound::__construct()');
        // validate the sound by checking whether it is defined as a constant in this class
        if (function_exists('unittest_log')) unittest_log('Validating sound');
        if (!self::isValidSound($sound)) {
            if (function_exists('unittest_log')) unittest_log('Sound is not valid, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid sound \'%2$s\' given',
                    __METHOD__,
                    $sound
                )
            );
        }

        // set the sound
        if (function_exists('unittest_log')) unittest_log('Sound appears to be OK, setting it');
        $this->sound = $sound;
    }

    /**
     * Uses reflection to determine whether a given sound is valid
     *
     * @param string $sound
     * @return bool
     */
    public static function isValidSound($sound)
    {
        if (function_exists('unittest_log')) unittest_log('Sound::isValidSound()');
        // validate the sound by checking whether it is defined as a constant in this class
        if (function_exists('unittest_log')) unittest_log('Getting reflection class');
        $reflectionClass = new \ReflectionClass(get_class());
        if (function_exists('unittest_log')) unittest_log('Getting constants');
        $definedConstants = $reflectionClass->getConstants();

        $soundIsValid = false;
        foreach ($definedConstants as $constantValue) {
            if ($constantValue == $sound) {
                if (function_exists('unittest_log')) unittest_log('Found ' . $sound . ' in constants, sound is valid');
                $soundIsValid = true;
                break;
            }
        }

        if (function_exists('unittest_log')) unittest_log('Returning that sound is' . (!$soundIsValid ? ' not' : '') . ' valid');
        return $soundIsValid;
    }

    /**
     * Return a string representation of the selected sound
     *
     * @return string
     */
    public function __toString()
    {
        if (function_exists('unittest_log')) unittest_log('Sound::__toString()');
        if (function_exists('unittest_log')) unittest_log('Returning ' . $this->sound);
        return $this->sound;
    }
}
