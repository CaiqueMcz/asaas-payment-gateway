<?php

namespace AsaasPaymentGateway\Helpers;

class CDate extends \DateTime
{
    public function __construct($time = 'now', $timezone = null)
    {
        parent::__construct($time, $timezone);
    }

    public static function from($time = 'now', $timezone = null): self
    {
        return new static($time, $timezone);
    }

    public function __toString()
    {
        return $this->getAsString();
    }

    public function getAsString(): string
    {
        return $this->format('Y-m-d');
    }
}
