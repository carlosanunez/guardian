<?php namespace Elphie\Guardian;

class AccountNotActivatedException extends \RuntimeException {}
class AccountSuspendedException extends \RuntimeException {}
class UserNotFoundException extends \OutOfBoundsException {}
class UserNotLoginException extends \RuntimeException {}
class UserIsActivatedException extends \RuntimeException {}
class EmailAlreadyExistsException extends \RuntimeException {}
class EmailCannotBeEmptyException extends \RuntimeException {}
class NicknameAlreadyExistsException extends \RuntimeException {}
class GroupNotFoundException extends \OutOfBoundsException {}
class GroupAlreadyExistsException extends \RuntimeException {}