<?php

declare(strict_types=1);

namespace KHQR\Tests\Feature\Mcc;

use KHQR\BakongKHQR;
use KHQR\Exceptions\KHQRException;
use KHQR\Models\MerchantInfo;
use PHPUnit\Framework\TestCase;

class GenerateMerchantTest extends TestCase
{
	/** @var array<mixed> */
    private array $accountInfo = [
		'bakongAccountID' => 'jonhsmith@nbcq',
		'merchantName' => 'Jonh Smith',
		'merchantCity' => 'Phnom Penh',
		'merchantId' => '123456',
		'acquiringBank' => 'Dev Bank',
	];

	public function test_positive_cases(): void {
		/** @var array<mixed> */
		$positiveCases = [
			[
				'statement' => 'Feature MCC: Success Generate Merchant KHQR Test 1',
				'data' => [
					'required' => $this->accountInfo,
					'optional' => [
						'merchantCategoryCode' => '0000',
					],
				],
				'result' => '00020101021130400014jonhsmith@nbcq01061234560208Dev Bank5204000053031165802KH5910Jonh Smith6010Phnom Penh',
			],
			[
				'statement' => 'Feature MCC: Success Generate Merchant KHQR Test 2',
				'data' => [
					'required' => $this->accountInfo,
					'optional' => [
						'merchantCategoryCode' => '1234',
					],
				],
				'result' => '00020101021130400014jonhsmith@nbcq01061234560208Dev Bank5204123453031165802KH5910Jonh Smith6010Phnom Penh',
			],
		];

		foreach ($positiveCases as $data) {
			$requiredData = $data['data']['required'];
            $optionalData = $data['data']['optional'];

            $merchantInfo = MerchantInfo::withOptionalArray(
                $requiredData['bakongAccountID'],
                $requiredData['merchantName'],
                $requiredData['merchantCity'],
				$requiredData['merchantId'],
				$requiredData['acquiringBank'],
                $optionalData
            );

            $khqrData = BakongKHQR::generateMerchant($merchantInfo);
			$sliceIndex = isset($optionalData['amount']) ? -46 : -8;
            $this->assertEquals($data['result'], mb_substr($khqrData->data['qr'], 0, $sliceIndex, 'UTF-8'), $data['statement']);
		}
	}

	public function test_negative_cases(): void {
		/** @var array<mixed> */
		$negativeCases = [
			[
				'statement' => 'Feature MCC: Failed Generate Merchant KHQR Test 1',
				'data' => [
					'required' => $this->accountInfo,
					'optional' => [
						'merchantCategoryCode' => '1A2B',
					],
				],
				'result' => [
					'error' => KHQRException::INVALID_MERCHANT_CATEGORY_CODE,
				],
			],
			[
				'statement' => 'Feature MCC: Failed Generate Merchant KHQR Test 2',
				'data' => [
					'required' => $this->accountInfo,
					'optional' => [
						'merchantCategoryCode' => '-100',
					],
				],
				'result' => [
					'error' => KHQRException::INVALID_MERCHANT_CATEGORY_CODE,
				],
			],
		];

		foreach ($negativeCases as $data) {
			$requiredData = $data['data']['required'];
            $optionalData = $data['data']['optional'];

            $merchantInfo = MerchantInfo::withOptionalArray(
                $requiredData['bakongAccountID'],
                $requiredData['merchantName'],
                $requiredData['merchantCity'],
				$requiredData['merchantId'],
				$requiredData['acquiringBank'],
                $optionalData
            );

            try {
                BakongKHQR::generateMerchant($merchantInfo);
            } catch (KHQRException $e) {
                $expectedErrorCode = KHQRException::getError($data['result']['error'])[0];
                $this->assertEquals($expectedErrorCode, $e->getCode(), $data['statement']);
            }
		}
	}
}