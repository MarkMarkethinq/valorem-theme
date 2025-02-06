<?php
namespace FeedbackSupportTool\Middleware;

use BotMan\BotMan\Interfaces\Middleware\Received;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class MessageMiddleware implements Received
{
    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function received(IncomingMessage $message, $next, $bot)
    {
        $message->setText($this->message);
        return $next($message);
    }
} 