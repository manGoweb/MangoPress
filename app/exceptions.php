<?php

namespace App;


/**
 *
 * `- Exception
 *    |- RuntimeException
 *    |  |- App\InvalidStateException
 *    |  |- App\DuplicateEntryException
 *    |  `- App\IOException
 *    |     |- App\FileNotFoundException
 *    |     `- App\DirectoryNotFoundException
 *    `- LogicException
 *       |- EmptyStackException
 *       |- InvalidArgumentException
 *       |  `- App\InvalidArgumentException
 *       |     `- App\ArgumentOutOfRangeException
 *       |- App\NotImplementedException
 *       |- App\ImplementationException
 *       |- App\NotSupportedException
 *       |  `- App\DeprecatedException
 *       `- App\StaticClassException
 *
 * @author Jan Tvrdík
 */

// === Runtime exceptions ======================================================

class InvalidStateException extends \RuntimeException { }

class DuplicateEntryException extends \RuntimeException { }

class IOException extends \RuntimeException { }

class FileNotFoundException extends IOException { }

class DirectoryNotFoundException extends IOException { }

// === Logic exceptions ========================================================

class EmptyStackException extends \LogicException {}

class InvalidArgumentException extends \InvalidArgumentException { }

class ArgumentOutOfRangeException extends InvalidArgumentException { }

class NotImplementedException extends \LogicException { }

class ImplementationException extends \LogicException { }

class NotSupportedException extends \LogicException { }

class DeprecatedException extends NotSupportedException { }

class StaticClassException extends \LogicException { }
