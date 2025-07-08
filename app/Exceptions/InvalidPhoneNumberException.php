<?php

namespace App\Exceptions;

use Exception;

class InvalidPhoneNumberException extends Exception
{
    public function __construct(string $phone, string $extractedDigits = '')
    {
        $message = $this->getFormattedMessage($phone, $extractedDigits);
        parent::__construct($message);
    }

    private function getFormattedMessage(string $phone, string $extractedDigits): string
    {
        $cleanDigits = preg_replace('/\D/', '', $phone);
        $length = strlen($cleanDigits);

        if ($length < 9) {
            return "Phone number is too short. Please enter a valid Kenyan phone number (e.g., 0712345678).";
        }

        if ($length > 12) {
            return "Phone number is too long. Please enter a valid Kenyan phone number (e.g., 0712345678).";
        }

        if ($length === 10 && !str_starts_with($cleanDigits, '07')) {
            return "Invalid phone number format. Kenyan numbers should start with 07 (e.g., 0712345678).";
        }

        if ($length === 9 && !str_starts_with($cleanDigits, '7')) {
            return "Invalid phone number format. Please include the leading 0 (e.g., 0712345678).";
        }

        if ($length === 12 && !str_starts_with($cleanDigits, '2547')) {
            return "Invalid international format. Use either 0712345678 or +254712345678.";
        }

        return "Invalid phone number format. Please enter a valid Kenyan phone number (e.g., 0712345678 or +254712345678).";
    }
}
