<?php

namespace App\Tests\src\Service\PhoneNumberService;

use App\Service\PhoneNumberService\PhoneNumberHelperService;
use PHPUnit\Framework\TestCase;

class PhoneNumberHelperServiceTest extends TestCase
{

    public function testGetDialCode(): void
    {
        foreach ($this->getNumbers() as $number => $code) {
            $phoneNumberHelperService = new PhoneNumberHelperService();
            $dialCode = $phoneNumberHelperService->getDialCode($number);
            $this->assertEquals($code, $dialCode);
        }
    }


    public function testGetCodeWithoutPrefix(): void
    {
        foreach ($this->getCodes() as $code => $codeWithoutPrefix) {
            $phoneNumberHelperService = new PhoneNumberHelperService();
            $dialCode = $phoneNumberHelperService->getCodeWithoutPrefix($code);
            $this->assertEquals($codeWithoutPrefix, $dialCode);
        }
    }

    public function getNumbers(): array
    {
        return [
            '+14085551234' => '+1',   // United States
            '+442081234567' => '+44',  // United Kingdom
            '+61234567890' => '+61',   // Australia
            '+491234567890' => '+49',  // Germany
            '00491234567890' => '0049',  // Germany 2
            '+420608127542' => '+420',  // Czech
            '+33123456789' => '+33',   // France
            '+81345678901' => '+81',   // Japan
            '+8612345678901' => '+86', // China
            '+74951234567' => '+7',   // Russia
            '+917012345678' => '+91',  // India
        ];
    }

    public function getCodes(): array
    {
        return [
            '+1' => '1',   // United States
            '+44' => '44',  // United Kingdom
            '+61' => '61',   // Australia
            '+49' => '49',  // Germany
            '0049' => '49',  // Germany 2
            '+420' => '420',  // Czech
            '+33' => '33',   // France
            '+81' => '81',   // Japan
            '+86' => '86', // China
            '+7' => '7',   // Russia
            '+91' => '91',  // India
        ];
    }

}