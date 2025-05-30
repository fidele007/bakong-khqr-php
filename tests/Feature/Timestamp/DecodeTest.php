<?php

declare(strict_types=1);

namespace KHQR\Tests\Feature\Timestamp;

use KHQR\BakongKHQR;
use PHPUnit\Framework\TestCase;

class DecodeTest extends TestCase
{
    private array $testData = [
        [
            'statement' => 'Decode Test 1',
            'data' => '00020101021229460015john_smith@devb0111855122334550208Dev Bank52045999530384054031005802KH5910John Smith6010PHNOM PENH62670106#123450211855122334550311Coffee Shop0709Cashier_10810Buy coffee64280002km0108ចន ស្មីន0206ភ្នំពញ993400131726821915797011317268220357976304B73E',
            'result' => [
                'merchantID',
                'unionPayMerchant',
            ],
        ],
        [
            'statement' => 'Decode Test 2',
            'data' => '00020101021230350009khqr@devb01061234560208Dev Bank52045999530384054031005802KH5910John Smith6010PHNOM PENH62670106#123450211855122334550311Coffee Shop0709Cashier_10810Buy coffee64280002km0108ចន ស្មីន0206ភ្នំពញ993400131726822962612011317268230826126304A560',
            'result' => [
                'accountInformation',
                'unionPayMerchant',
            ],
        ],
    ];

    public function test_decode(): void
    {
        foreach ($this->testData as $data) {
            $decoded = BakongKHQR::decode($data['data']);
            $nullFilteredResult = array_keys(array_filter($decoded->data, fn ($value) => is_null($value)));
            $this->assertEquals($data['result'], (array) $nullFilteredResult, $data['statement']);
        }
    }
}
