<?php declare(strict_types=1);

namespace Butler\PhpHelpers;

class StringUtil
{
    public const AMBIGUOUS_CHARACTERS_REGEX = '~[+/=oO0iIlL1]~';

    /**
     * Checks if a string is valid typical UTF-8. Treats things like NUL bytes as invalid, even if it might be allowed
     * in UTF-8 standard. Use for general user generated content to keep things sane.
     */
    public static function isTypicalUtf8(string $str): bool
    {
        if (!self::isRawUtf8($str)) {
            return false;
        }

        // Hex. char checks. What do these do?
        // UTF-8 encoded strings treat characters < 128 as the raw ASCII character. In other words, multi-byte
        // UTF characters will never consist of something < 128 in it. Thus, we can check for these non-printable
        // ASCII chars and reject it if we are looking only for sane UTF-8 strings.

        // Chars 00 to 08, hex
        if (preg_match('/[\x00-\x08]/', $str)) {
            return false;
        }

        // x09 - Tab \t
        // x0A - Line Feed

        // Chars 0B to 0C, hex
        if (preg_match('/[\x0B-\x0C]/', $str)) {
            return false;
        }

        // x0D - Carriage Return

        // Chars 0E to 1F, hex
        if (preg_match('/[\x0E-\x1F]/', $str)) {
            return false;
        }

        return true;
    }

    /**
     * Checks if a string is valid UTF-8, but no extra checks. Generally you should use isTypicalUtf8.
     */
    public static function isRawUtf8(string $str): bool
    {
        return mb_check_encoding($str, 'UTF-8');
    }

    /**
     * Can be used for secure keys and one time codes, etc.
     *
     * @param int $length Length of random string
     */
    public static function getRandomString(int $length): string
    {
        $str = '';
        do {
            $rand = base64_encode(random_bytes($length * 3));
            $rand = preg_replace(self::AMBIGUOUS_CHARACTERS_REGEX, '', $rand);
            $str .= $rand;
        } while (strlen($str) < $length);

        // Truncate result
        return substr($str, 0, $length);
    }

    /**
     * Convert a string for use in a URL, such as "Hello, world!" => 'hello-world'
     * @param string $str Input string
     * @param array $options Options
     * @return string
     */
    public static function slugify(string $str, array $options = []): string
    {
        static $char_map = null;

        if ($char_map === null) {
            $char_map = [
                // Latin
                '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'AE', '??' => 'C',
                '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'I', '??' => 'I', '??' => 'I', '??' => 'I',
                '??' => 'D', '??' => 'N', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O',
                '??' => 'O', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'Y', '??' => 'TH',
                '??' => 'ss',
                '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'ae', '??' => 'c',
                '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'i', '??' => 'i', '??' => 'i', '??' => 'i',
                '??' => 'd', '??' => 'n', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o',
                '??' => 'o', '??' => 'u', '??' => 'u', '??' => 'u', '??' => 'u', '??' => 'u', '??' => 'y', '??' => 'th',
                '??' => 'y',

                // Latin symbols
                '??' => '(c)',

                // Greek
                '??' => 'A', '??' => 'B', '??' => 'G', '??' => 'D', '??' => 'E', '??' => 'Z', '??' => 'H', '??' => '8',
                '??' => 'I', '??' => 'K', '??' => 'L', '??' => 'M', '??' => 'N', '??' => '3', '??' => 'O', '??' => 'P',
                '??' => 'R', '??' => 'S', '??' => 'T', '??' => 'Y', '??' => 'F', '??' => 'X', '??' => 'PS', '??' => 'W',
                '??' => 'A', '??' => 'E', '??' => 'I', '??' => 'O', '??' => 'Y', '??' => 'H', '??' => 'W', '??' => 'I',
                '??' => 'Y',
                '??' => 'a', '??' => 'b', '??' => 'g', '??' => 'd', '??' => 'e', '??' => 'z', '??' => 'h', '??' => '8',
                '??' => 'i', '??' => 'k', '??' => 'l', '??' => 'm', '??' => 'n', '??' => '3', '??' => 'o', '??' => 'p',
                '??' => 'r', '??' => 's', '??' => 't', '??' => 'y', '??' => 'f', '??' => 'x', '??' => 'ps', '??' => 'w',
                '??' => 'a', '??' => 'e', '??' => 'i', '??' => 'o', '??' => 'y', '??' => 'h', '??' => 'w', '??' => 's',
                '??' => 'i', '??' => 'y', '??' => 'y', '??' => 'i',

                // Turkish
                '??' => 'S', '??' => 'I', /* '??' => 'U', '??' => 'O', */ '??' => 'G',
                '??' => 's', '??' => 'i', /* '??' => 'c', '??' => 'u', '??' => 'o', */ '??' => 'g',

                // Russian
                '??' => 'A', '??' => 'B', '??' => 'V', '??' => 'G', '??' => 'D', '??' => 'E', '??' => 'Yo', '??' => 'Zh',
                '??' => 'Z', '??' => 'I', '??' => 'J', '??' => 'K', '??' => 'L', '??' => 'M', '??' => 'N', '??' => 'O',
                '??' => 'P', '??' => 'R', '??' => 'S', '??' => 'T', '??' => 'U', '??' => 'F', '??' => 'H', '??' => 'C',
                '??' => 'Ch', '??' => 'Sh', '??' => 'Sh', '??' => '', '??' => 'Y', '??' => '', '??' => 'E', '??' => 'Yu',
                '??' => 'Ya',
                '??' => 'a', '??' => 'b', '??' => 'v', '??' => 'g', '??' => 'd', '??' => 'e', '??' => 'yo', '??' => 'zh',
                '??' => 'z', '??' => 'i', '??' => 'j', '??' => 'k', '??' => 'l', '??' => 'm', '??' => 'n', '??' => 'o',
                '??' => 'p', '??' => 'r', '??' => 's', '??' => 't', '??' => 'u', '??' => 'f', '??' => 'h', '??' => 'c',
                '??' => 'ch', '??' => 'sh', '??' => 'sh', '??' => '', '??' => 'y', '??' => '', '??' => 'e', '??' => 'yu',
                '??' => 'ya',

                // Ukrainian
                '??' => 'Ye', '??' => 'I', '??' => 'Yi', '??' => 'G',
                '??' => 'ye', '??' => 'i', '??' => 'yi', '??' => 'g',

                // Czech
                '??' => 'C', '??' => 'D', '??' => 'E', '??' => 'N', '??' => 'R', '??' => 'S', '??' => 'T', '??' => 'U',
                '??' => 'Z',
                '??' => 'c', '??' => 'd', '??' => 'e', '??' => 'n', '??' => 'r', '??' => 's', '??' => 't', '??' => 'u',
                '??' => 'z',

                // Polish
                '??' => 'A', '??' => 'C', '??' => 'e', '??' => 'L', '??' => 'N', '??' => 'S', '??' => 'Z',
                '??' => 'Z',
                '??' => 'a', '??' => 'c', '??' => 'e', '??' => 'l', '??' => 'n', /* '??' => 'o', */ '??' => 's', '??' => 'z',
                '??' => 'z',

                // Latvian
                '??' => 'A', /* '??' => 'C', */ '??' => 'E', '??' => 'G', '??' => 'i', '??' => 'k', '??' => 'L', '??' => 'N',
                /* '??' => 'S', */ '??' => 'u', /* '??' => 'Z', */
                '??' => 'a', /* '??' => 'c', */ '??' => 'e', '??' => 'g', '??' => 'i', '??' => 'k', '??' => 'l', '??' => 'n',
                /* '??' => 's', */ '??' => 'u', /* '??' => 'z' */
            ];
        }
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = (string) mb_convert_encoding($str, 'UTF-8', 'UTF-8');

        $defaults = array(
            'delimiter' => '-',
            'limit' => null,
            'lowercase' => true,
            'replacements' => [],
            'transliterate' => true,
        );

        // Merge options
        $options = array_merge($defaults, $options);

        // Transliterate characters to ASCII
        if ($options['transliterate']) {
            $str = str_replace(array_keys($char_map), $char_map, $str);
        }

        // Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

        // Make custom replacements
        if ($options['replacements']) {
            $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
        }

        // Remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

        // Truncate slug to max. characters
        if ($options['limit'] > 0) {
            $str = mb_substr($str, 0, ($options['limit'] ?: mb_strlen($str, 'UTF-8')), 'UTF-8');
        }

        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);

        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }

    /**
     * Convert string to proper UTF-8 for safe keeping. Possibly removes chars to make it valid.
     * Leaves weird ASCII chars intact!
     * @param string $str
     * @return string
     */
    public static function toUtf8(string $str): string
    {
        if (self::isRawUtf8($str)) {
            return $str;
        }
        $orig = ini_set('mbstring.substitute_character', "none");
        try {
            return mb_convert_encoding($str, 'UTF-8', 'UTF-8');
        } finally {
            ini_set('mbstring.substitute_character', $orig);
        }
    }

    /**
     * Determine if an email address is valid, using a fairly loose check. Be sure to trim whitespace prior to calling.
     * Basically checks for one @ symbol, then one dot symbol, and at least 2 chars after the dot.
     * @param string $email
     * @return bool
     */
    public static function isValidEmail(string $email): bool
    {
        if (!self::isTypicalUtf8($email)) {
            return false;
        }
        return (bool) preg_match('/^[^\s@]+@[^\s@.]+\.[^\s@]{2,}$/', $email);
    }
}
