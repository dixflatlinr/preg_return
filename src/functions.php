<?php

use DF\App\Helper\RX;

if (!function_exists('preg_return'))
{
    /**
     * Desc: {@see RX::pregReturn}
     */
    function preg_return(string $pattern, string $subject, $indexesToReturn = null, int $flags = 0, int $offset = 0)
    {
        return RX::pregReturn(...func_get_args());
    }
}

if (!function_exists('preg_return_all'))
{
    /**
     * Desc: {@see RX::pregReturnAll}
     */
    function preg_return_all(string $pattern, string $subject, $indexesToReturn = null, int $flags = 0, int $offset = 0)
    {
        return RX::pregReturnAll(...func_get_args());
    }
}

if (!function_exists('preg_return_replace'))
{
    /**
     * Desc: {@see RX::pregReturnReplace}
     */
    function preg_return_replace(string|array $pattern, string|array $replacement, string|array &$subject, $indexesToReturn = null, int $flags = 0, int $offset = 0)
    {
        return RX::pregReturnReplace($pattern, $replacement, $subject, $indexesToReturn, $flags, $offset);
    }
}
