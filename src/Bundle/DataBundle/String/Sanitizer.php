<?php
/**
 * File Sanitizer.php
 */
namespace Nerdery\Xbox\Bundle\DataBundle\String;

/**
 * Class Sanitizer
 *
 * Performs simple modifications on strings
 *
 * This class exists in case the simple modifications become more complex, it
 * will be easier to make modifications that get applied everywhere they're needed.
 *
 * @author Nate Brunette <n@tebru.net>
 * @package Nerdery\Xbox\Bundle\DataBundle\String
 */
class Sanitizer
{
    /**
     * Make string safe for storing in a database
     *
     * @param string $string
     *
     * @return string
     */
    public function sanitizeForDatabase($string)
    {
        return addslashes($string);
    }

    /**
     * Remove all html to protect against XSS attacks
     *
     * @param string $string
     *
     * @return string
     */
    public function removeHtml($string)
    {
        return strip_tags($string);
    }

    /**
     * Reverse the operations performed after retrieving from database
     *
     * @param string $string
     *
     * @return string
     */
    public function reverseSanitizeForDatabase($string)
    {
        return stripslashes($string);
    }

    /**
     * Do simple string cleanup
     *
     * Currently this only trims the string
     *
     * @param string $string
     *
     * @return string
     */
    public function cleanup($string)
    {
        return trim($string);
    }
}