<?php

namespace JSMin;

/**
 * @package JSMin
 * @copyright 2015 Hans-Jürgen Tappe <hjtappe@users.noreply.github.com>
 * @license http://opensource.org/licenses/mit-license.php MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * The Software shall be used for Good, not Evil.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
class EncodingDetector
{
    /**
     * Default Target Encoding
     */
    const DEFAULT_ENCODING = 'UTF-8';

    /**
     * Debug output switch
     */
    const DEBUG = false;


    /**
     * Decode input file content to UTF-8, taking BOM into account.
     *
     * @param string $input Input string
     * @throws EncodingException
     */
    public static function decode($input, $targetEncoding = NULL)
    {
        // Check for the target encoding.
        if (is_null($targetEncoding)) {
            $targetEncoding = self::get_encoding();
        }

        // Remove the utf-8 BOM to save transfer bytes.
        // Otherwise, line breaks before the 2nd comment are kept and
        // lots of zero bytes stay, leading to additional waste and
        // parsing
        // exceptions.
        $first2 = substr($input, 0, 2);
        $first3 = substr($input, 0, 3);
        $first4 = substr($input, 0, 4);

        // Unicode BOM is U+FEFF, but after encoded, it will look
        // like this.
        if ($first3 == chr(0xEF).chr(0xBB).chr(0xBF)) {
            $encoding = 'UTF-8';
            $input = substr($input, 3);
        } elseif ($first4 == chr(0x00).chr(0x00).chr(0xFE).chr(0xFF)) {
            $encoding = 'UTF-32BE';
            $input = substr($input, 4);
        } elseif ($first4 == chr(0xFF).chr(0xFE).chr(0x00).chr(0x00)) {
            $encoding = 'UTF-32LE';
            $input = substr($input, 4);
        } elseif ($first2 == chr(0xFE).chr(0xFF)) {
            $encoding = 'UTF-16BE';
            $input = substr($input, 2);
        } elseif ($first2 == chr(0xFF).chr(0xFE)) {
            $encoding = 'UTF-16LE';
            $input = substr($input, 2);
        } else {
            $encoding = 'UTF-8';
            // No BOM
        }

        // Convert only convertible files.
        if (strtoupper($encoding) != strtoupper($targetEncoding)) {
            if (self::check_mbstring()) {
                $input = mb_convert_encoding($input, $targetEncoding, $encoding);
            } else {
                $result = iconv(
                        $in_charset = $encoding,
                        $out_charset = $targetEncoding,
                        $input);
                if (false === $result)
                {
                    throw new EncodingException('Input string could not be converted.');
                } else {
                    $input = $result;
                }
            }
        }

        return $input;
    }

    /**
     * Check the availability of mbstring.
     * @returns boolean
     */
    private static function check_mbstring()
    {
        if (function_exists('mb_strlen') &&
                ((int)ini_get('mbstring.func_overload') & 2)) {
            self::DEBUG && error_log(__CLASS__.'::'.__FUNCTION__.': true');
            return true;
        } else {
            self::DEBUG && error_log(__CLASS__.'::'.__FUNCTION__.': false');
            return false;
        }
    }

    /**
     * Get the target encoding.
     * @returns string 
     */
    private static function get_encoding()
    {
        $iniValue = ini_get('default_encoding');
        if (!is_null($iniValue) && $iniValue != '') {
            self::DEBUG && error_log(__CLASS__.'::'.__FUNCTION__.': '.$iniValue.' (INI VALUE)');
            return $iniValue;
        } else {
            self::DEBUG && error_log(__CLASS__.'::'.__FUNCTION__.': '.self::DEFAULT_ENCODING.' (DEFAULT)');
            return self::DEFAULT_ENCODING;
        }
    }
}
