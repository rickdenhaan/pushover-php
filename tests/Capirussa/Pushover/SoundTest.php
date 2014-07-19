<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pushover\Sound;

/**
 * Tests Capirussa\Pushover\Sound
 *
 */
class SoundTest extends PHPUnit_Framework_TestCase
{
    public function testConstructWithoutParameters()
    {
        $sound = new Sound();

        // this should have set the default sound of Sound::USER_DEFAULT

        $this->assertNotNull($this->getObjectAttribute($sound, 'sound'));
        $this->assertEquals(Sound::USER_DEFAULT, $this->getObjectAttribute($sound, 'sound'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid sound
     */
    public function testConstructWithInvalidSound()
    {
        new Sound('invalid sound');
    }

    public function testIsValidSound()
    {
        $validSoundsByConstant = array(
            Sound::USER_DEFAULT,
            Sound::PUSHOVER,
            Sound::BIKE,
            Sound::BUGLE,
            Sound::CASH_REGISTER,
            Sound::CLASSICAL,
            Sound::COSMIC,
            Sound::FALLING,
            Sound::GAMELAN,
            Sound::INCOMING,
            Sound::INTERMISSION,
            Sound::MAGIC,
            Sound::MECHANICAL,
            Sound::PIANO_BAR,
            Sound::SIREN,
            Sound::SPACE_ALARM,
            Sound::TUG_BOAT,
            Sound::ALIEN_ALARM,
            Sound::CLIMB,
            Sound::PERSISTENT,
            Sound::PUSHOVER_ECHO,
            Sound::UP_DOWN,
            Sound::NONE,
        );

        $validSoundsByString = array(
            '',
            'pushover',
            'bike',
            'bugle',
            'cashregister',
            'classical',
            'cosmic',
            'falling',
            'gamelan',
            'incoming',
            'intermission',
            'magic',
            'mechanical',
            'pianobar',
            'siren',
            'spacealarm',
            'tugboat',
            'alien',
            'climb',
            'persistent',
            'echo',
            'updown',
            'none',
        );

        foreach ($validSoundsByConstant as $sound) {
            $this->assertTrue(Sound::isValidSound($sound), $sound);
        }

        foreach ($validSoundsByString as $sound) {
            $this->assertTrue(Sound::isValidSound($sound), $sound);
        }

        for ($i=0; $i<1000; $i++) {
            $sound = '';
            for ($c=0; $c<mt_rand(1, 10); $c++) {
                $sound .= chr(mt_rand(0, 255));
            }

            if (!in_array($sound, $validSoundsByConstant) && !in_array($sound, $validSoundsByString)) {
                $this->assertFalse(Sound::isValidSound($sound), $sound);
            }
        }
    }

    public function testToString()
    {
        $sound = new Sound();

        $this->assertEquals(Sound::USER_DEFAULT, (string)$sound);

        $sound = new Sound(Sound::ALIEN_ALARM);

        $this->assertEquals(Sound::ALIEN_ALARM, (string)$sound);
    }
}