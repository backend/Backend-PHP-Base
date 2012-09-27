<?php
/**
 * File defining the String class
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Utilities
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Base\Utilities;
/**
 * Utility String class
 *
 * @category   Backend
 * @package    Base
 * @subpackage Utilities
 * @author     J Jurgens du Toit <jrgns@jrgns.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class String
{
    /**
     * The literal string.
     *
     * @var string
     */
    protected $string;

    /**
     * The class constructor.
     *
     * @param string $string The string we're operating on.
     */
    public function __construct($string)
    {
        $this->string = $string;
    }

    /**
     * Get the CamelCase version of the string
     *
     * @return string The transformed string
     */
    public function camelCase()
    {
        $this->string = str_replace(" ", "", ucwords(strtr($this->string, "_-.", "   ")));

        return $this;
    }

    /**
     * Get the uncameled version of a string, using the optionally specified seperator.
     *
     * @return string The transformed string.
     */
    public function unCamel($separator = '-')
    {
        $array = preg_split('/([A-Z][^A-Z]+)/', $this->string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        if (is_array($array)) {
            $array = array_map('trim', array_map('strtolower', $array));
            $this->string = implode($separator, $array);
        }

        return $this->string;
    }

    /**
     * Returns the plural form of a word.
     *
     * Code from http://www.eval.ca/articles/php-pluralize
     * Code from http://kuwamoto.org/2007/12/17/improved-pluralizing-in-php-actionscript-and-ror/
     *
     * @return string The plural form of the word.
     */
    public function pluralize()
    {
        $plural = array(
                    array( '/(quiz)$/i',               "$1zes"   ),
                    array( '/^(ox)$/i',                "$1en"    ),
                    array( '/([m|l])ouse$/i',          "$1ice"   ),
                    array( '/(matr|vert|ind)ix|ex$/i', "$1ices"  ),
                    array( '/(x|ch|ss|sh)$/i',         "$1es"    ),
                    array( '/([^aeiouy]|qu)y$/i',      "$1ies"   ),
                    array( '/([^aeiouy]|qu)ies$/i',    "$1y"     ),
                    array( '/(hive)$/i',               "$1s"     ),
                    array( '/(?:([^f])fe|([lr])f)$/i', "$1$2ves" ),
                    array( '/(shea|lea|loa|thie)f$/i', "$1ves"   ),
                    array( '/sis$/i',                  "ses"     ),
                    array( '/([ti])um$/i',             "$1a"     ),
                    array( '/(buffal|tomat|potat|ech|her|vet)o$/i', '$1oes'),
                    array( '/(bu)s$/i',                "$1ses"   ),
                    array( '/(alias|status)$/i',       "$1es"    ),
                    array( '/(octop|vir)us$/i',        "$1i"     ),
                    array( '/(ax|test)is$/i',          "$1es"    ),
                    array( '/s$/i',                    "s"       ),
                    array( '/$/',                      "s"       )
                );

        $irregular = array(
                        array( 'move',   'moves'    ),
                        array( 'sex',    'sexes'    ),
                        array( 'child',  'children' ),
                        array( 'man',    'men'      ),
                        array( 'person', 'people'   )
        );

        $uncountable = array(
                        'sheep',
                        'fish',
                        'series',
                        'species',
                        'money',
                        'rice',
                        'information',
                        'equipment',
                        'data',
                        'capital',
                        'access',
        );

        // save some time in the case that singular and plural are the same
        if (in_array(strtolower($this->string), $uncountable)) {
            return $this;;
        }

        // check for irregular singular forms
        foreach ($irregular as $noun) {
            if (strtolower($this->string) == $noun[0]) {
                $this->string = $noun[1];

                return $this;
            }
        }

        // check for matches using regular expressions
        foreach ($plural as $pattern) {
            if (preg_match($pattern[0], $this->string)) {
                $this->string = preg_replace($pattern[0], $pattern[1], $this->string);
                break;
            }
        }

        return $this;
    }

    /**
     * Returns the singular form of a word.
     *
     * Code from http://www.eval.ca/articles/php-pluralize
     * Code from http://kuwamoto.org/2007/12/17/improved-pluralizing-in-php-actionscript-and-ror/
     *
     * @return string The singular form of the word.
     * @todo Get a way to avoid the duplication between singularize and pluralize
     */
    public function singularize()
    {
        $singular = array(
                        array( '/(quiz)(zes)?$/i'          , "$1" ),
                        array( '/(matr)ices$/i'            , "$1ix" ),
                        array( '/(vert|ind)ices$/i'        , "$1ex" ),
                        array( '/^(ox)(en)?$/i'            , "$1" ),
                        array( '/(alias)(es)?$/i'          , "$1" ),
                        array( '/(octop|vir)i$/i'          , "$1us" ),
                        array( '/(cris|ax|test)es$/i'      , "$1is" ),
                        array( '/(shoe)(s)?$/i'            , "$1" ),
                        array( '/(o)(es)?$/i'              , "$1" ),
                        array( '/(bus)(es)?$/i'            , "$1" ),
                        array( '/([m|l])ice$/i'            , "$1ouse" ),
                        array( '/(x|ch|ss|sh|ms)(es)?$/i'  , "$1" ),
                        array( '/^(m)(ovies)?$/i'          , "$1ovie" ),
                        array( '/(s)eries$/i'              , "$1eries" ),
                        array( '/([^aeiouy]|qu)ies$/i'     , "$1y" ),
                        array( '/([lr])ves$/i'             , "$1f" ),
                        array( '/(tive)(s)?$/i'            , "$1" ),
                        array( '/(hive)(s)?$/i'            , "$1" ),
                        array( '/(li|wi|kni)ves$/i'        , "$1fe" ),
                        array( '/(shea|loa|lea|thie)ves$/i', "$1f" ),
                        array( '/(^analy)ses$/i'           , "$1sis" ),
                        array( '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i', "$1$2sis" ),
                        array( '/([ti])a$/i'               , "$1um" ),
                        array( '/(n)ews$/i'                , "$1ews" ),
                        array( '/(h|bl)ouses$/i'           , "$1ouse" ),
                        array( '/(corpse)(s)?$/i'          , "$1" ),
                        array( '/(us)(es)?$/i'             , "$1" ),
                        array( '/(dns)$/i'                 , "$1" ),
                        array( '/s$/i'                     , "" )
                    );

        $irregular = array(
                        array( 'move',   'moves'    ),
                        array( 'sex',    'sexes'    ),
                        array( 'child',  'children' ),
                        array( 'man',    'men'      ),
                        array( 'person', 'people'   )
        );

        $uncountable = array(
                        'sheep',
                        'fish',
                        'series',
                        'species',
                        'money',
                        'rice',
                        'information',
                        'equipment',
                        'data',
                        'capital',
                        'access',
        );

        // save some time in the case that singular and plural are the same
        if (in_array(strtolower($this->string), $uncountable)) {
            return $this;
        }

        // check for irregular singular forms
        foreach ($irregular as $noun) {
            if (strtolower($this->string) == $noun[1]) {
                $this->string = $noun[0];

                return $this;
            }
        }

        // check for matches using regular expressions
        foreach ($singular as $pattern) {
            if (preg_match($pattern[0], $this->string)) {
                $this->string = preg_replace($pattern[0], $pattern[1], $this->string);
                break;
            }
        }

        return $this;
    }

    /**
     * Coonvert the String object to a literal string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->string;
    }
}
