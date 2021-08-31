<?php
namespace Intelipost\Quote\Logger;

/**
 * Class Logger
 * @package Intelipost\Quote\Logger
 */
class Logger extends \Monolog\Logger
{
      public function __construct(){
        parent::__construct('IntelipostQuote');
    }
}
