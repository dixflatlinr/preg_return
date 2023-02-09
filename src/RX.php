<?php

namespace DF\App\Helper;

class RX
{
    const PREG_MATCH = 1;
    const PREG_MATCH_ALL = 2;

    /**
     *
     * Works like preg_match, but returns the matching elements or a subset of it
     *
     * $indexesToReturn governs what is returned and how. Examples:
     *
     * pregReturn('~(a)(?<namedindex>b)~is','ab', '') => [0 => "ab", 1 => "a", "namedindex" => "b", 2 => "b"]
     *
     * pregReturn('~(a)(?<namedindex>b)~is','ab', 0) => ab
     *
     * pregReturn('~(a)(?<namedindex>b)~is','ab', 1) => a
     *
     * pregReturn('~(a)(?<namedindex>b)~is','ab', 'namedindex') => b
     *
     * pregReturn('~(a)(?<namedindex>b)~is','ab', [1,'namedindex']) => [1 => 'a', 'namedindex' => b]
     *
     * @param string $pattern The pattern to search for, as a string
     * @param string $subject The input string
     * @param int|string|array $indexesToReturn Value(s) to return from the matches array.
     *
     * Specified as int|string: Return the given index/named index
     *
     * Specified as array: Return the given index/named index values as-is in an array
     * @param int $flags Check preg_match docs => {@see preg_match}
     * @param int $offset Can be used to specify the alternate place from which to start the search (in bytes).
     * @return array|mixed|null
     */
    static function pregReturn(string $pattern, string $subject, $indexesToReturn = null, int $flags = 0, int $offset = 0)
    {
        return self::_preg(self::PREG_MATCH, ...func_get_args());
    }

    /**
     * Desc @ {@see pregReturn}
     */
    static function pregReturnAll(string $pattern, string $subject, $indexesToReturn = null, int $flags = 0, int $offset = 0)
    {
        return self::_preg(self::PREG_MATCH_ALL, ...func_get_args());
    }

    /**
     * Modifies the $subject in place and returns the matching elements or a subset of it
     *
     * Rest is identical to: {@see pregReturn}
     *
     */
    static function pregReturnReplace(string $pattern, string $replacement, string &$subject, $indexesToReturn = null, int $flags = 0, int $offset = 0)
    {
        $ret = self::_preg(self::PREG_MATCH, $pattern, $subject, $indexesToReturn, $flags, $offset);
        $subject = preg_replace($pattern, $replacement, $subject);

        return $ret;
    }

    static function _preg($type = self::PREG_MATCH, string $pattern, string $subject, $indexesToReturn = null, int $flags = 0, int $offset = 0)
    {
        if ($type == self::PREG_MATCH)
            $ret = preg_match($pattern, $subject, $matches, $flags, $offset);
        elseif ($type == self::PREG_MATCH_ALL)
            $ret = preg_match_all($pattern, $subject, $matches, $flags, $offset);
        else
            throw new \InvalidArgumentException('Invalid $type specified! Should be: self::PREG_MATCH|PREG_MATCH_ALL');

        //Null returns the actual preg_match return value
        if ($indexesToReturn === null)
            return $ret;

        //If no index specified be it an array or a(n) int|string, return the whole results array
        if ( is_array($indexesToReturn) && empty($indexesToReturn) || $indexesToReturn === '')
            return $matches;

        //Integers and strings return a specific match
        if ( is_int($indexesToReturn) || is_string($indexesToReturn) )
            return $matches[$indexesToReturn] ?? null;

        if (! is_array($indexesToReturn) )
            throw new \InvalidArgumentException(__FUNCTION__ . ': Must specify an int|str|array!', E_USER_ERROR);

        //Array indexes return a subset of the matches array
        $out = [];
        foreach($indexesToReturn as $index)
        {
            $out[$index] = $matches[$index] ?? null;
        }

        return $out;
    }
}
