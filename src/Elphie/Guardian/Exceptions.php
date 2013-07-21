<?php namespace Elphie\Guardian;

/**
 * Larvel 4 user management package. Extending the Auth module.
 * 
 * @package  Elphie
 * @subpackage Guardian
 * @author  Ahmad Shah Hafizan Hamidin <[ahmadshahhafizan[at]gmail.com]>
 * @license  MIT
 */

class AccountNotActivatedException extends \RuntimeException {}
class AccountSuspendedException extends \RuntimeException {}
class UserNotFoundException extends \OutOfBoundsException {}
class UserNotLoginException extends \RuntimeException {}
class UserIsActivatedException extends \RuntimeException {}
class GroupNotFoundException extends \OutOfBoundsException {}
class GroupAlreadyExistsException extends \RuntimeException {}