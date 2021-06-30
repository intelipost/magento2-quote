<?php
namespace Intelipost\Quote\Logger;

use Monolog\Logger;

/**
 * Class Handler
 * @package Intelipost\Quote\Logger
 */
class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/intelipost.log';
}
