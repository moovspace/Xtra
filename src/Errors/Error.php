<?php
namespace Xtra\Error;
use \SplEnum;

class Error extends SplEnum
{
    const __default = self::EMPTY;

    // For Translate\Trans class
    const EMPTY = 'ERRORLOGIN_EMPTY'; // Empty login, pass
    const AUTH = 'ERRORLOGIN_AUTH'; // Error credentials
    const NOEXIST = 'ERRORLOGIN_NOEXIST'; // User not exists
}
?>
