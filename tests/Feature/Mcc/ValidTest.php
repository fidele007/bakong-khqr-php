<?php

declare(strict_types=1);

namespace KHQR\Tests\Feature\Mcc;

use KHQR\BakongKHQR;
use PHPUnit\Framework\TestCase;

class ValidTest extends TestCase
{
    /** @var array<array<string, string|int>> */
    private array $negativeCases = [
		[
			'statement' => 'Feature MCC: Verify Invalid Merchant Category Code 1',
			'data' => '00020101021129180014jonhsmith@nbcq52041A2B53031165802KH5910Jonh Smith6010Phnom Penh6304F7FD',
			'errorCode' => 51,
		],
		[
			'statement' => 'Feature MCC: Verify Invalid Merchant Category Code 2',
			'data' => '00020101021129180014jonhsmith@nbcq5204-10053031165802KH5910Jonh Smith6010Phnom Penh6304038A',
			'errorCode' => 51,
		],
	];

    public function test_data_from_failed_decode(): void
    {
        foreach ($this->negativeCases as $data) {
            $crcValidation = BakongKHQR::verify((string) $data['data']);
            $this->assertEquals(false, $crcValidation->isValid, $data['statement']);
        }
    }
}
