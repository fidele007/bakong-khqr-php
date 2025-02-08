<?php

declare(strict_types=1);

namespace KHQR\Tests;

use KHQR\BakongKHQR;
use KHQR\Exceptions\KHQRException;
use PHPUnit\Framework\TestCase;

class DecodeTest extends TestCase
{

	protected $testData = [
		[
			'statement' => "Decode Test 1",
			'data' => "00020101021229190015john_smith@devb52045999530311654065000.05802KH5910jonh smith6010Phnom Penh62360109#INV-20030313Coffee Klaing0702#299170013161302797275763049ACF",
			'result' => [
				"merchantID",
				"accountInformation",
				"acquiringBank",
				"mobileNumber",
			],
		],
		[
			'statement' => "Decode Test 2",
			'data' => "00020101021230410015john_smith@devb01061234560208Dev Bank52045999530384054035.05802KH5916john smith actor6010Phnom Penh62130709Counter 299170013161343857579463048EF2",
			'result' => [
				"accountInformation",
				"billNumber",
				"mobileNumber",
				"storeLabel",
			],
		],
		[
			'statement' => "Decode Test 3",
			'data' => "00020101021230410015john_smith@devb01061234560208Dev Bank52045999530384054035.05802KH5916john smith actor6010Phnom Penh62280111Invoice#0690709Counter 29917001316134385758926304384E",
			'result' => ["accountInformation", "mobileNumber", "storeLabel"],
		],
		[
			'statement' => "Decode Test 4",
			'data' => "00020101021230410015john_smith@devb01061234560208Dev Bank52045999530384054035.05802KH5916john smith actor6010Phnom Penh62150111Invoice#0699917001316134385758926304552D",
			'result' => [
				"accountInformation",
				"billNumber",
				"mobileNumber",
				"storeLabel",
				"terminalLabel",
				"timeStamp",
			],
		],
		[
			'statement' => "Decode Test 5",
			'data' => "000201010211021641277800000000980416518352888000006030470016abaakhppxxx@abaa01153166007701102420204abaa5204224253038405802KH5910USD OUTLET6010PHNOM PENH62250509115B02750070827CE19809924001339CA026411FDA6803mmp63043870",
			'result' => [
				"accountInformation",
				"transactionAmount",
				"billNumber",
				"mobileNumber",
				"storeLabel",
			],
		],
		[
			'statement' => "Decode Test 6",
			'data' => "00020101021130470009khqr@aclb0111855124649170215ACLEDA Bank Plc5204599953038405802KH5907BUN MAO6010Phnom Penh6102126213020901050033164310002KM0107BUN MAO0210Phnom Penh6304E313",
			'result' => [
				"accountInformation",
				"transactionAmount",
				"mobileNumber",
				"storeLabel",
				"timeStamp",
			],
		],
		[
			'statement' => "Decode Test 7",
			'data' => "00020101021130640009khqr@aclb0111855124649060215ACLEDA Bank Plc051385500000019425204593153038405802KH5912KHQR TESTING6010Phnom Penh6102126213020901753577164360002KM0112KHQR TESTING0210Phnom Penh6304AC1C",
			'result' => [
				"accountInformation",
				"transactionAmount",
				"billNumber",
				"storeLabel",
				"timeStamp",
			],
		],
		[
			'statement' => "Decode Test 8",
			'data' => "00020101021229310014jonhsmith@nbcq0109012345678520459995303116540750000.05802KH5910Jonh Smith6009Siam Reap62550117INV-2021-07-658220211855123456780305BKK-1070601234599170013163039583850263041134",
			'result' => ["merchantID", "acquiringBank"],
		],
		[
			'statement' => "Decode Test 9",
			'data' => "EAB3",
			'result' => [
				"merchantType",
				"bakongAccountID",
				"accountInformation",
				"merchantID",
				"acquiringBank",
				"billNumber",
				"mobileNumber",
				"storeLabel",
				"terminalLabel",
				"payloadFormatIndicator",
				"pointofInitiationMethod",
				"merchantCategoryCode",
				"transactionCurrency",
				"transactionAmount",
				"countryCode",
				"merchantName",
				"merchantCity",
				"timestamp",
				"crc",
			],
		],
		[
			'statement' => "Decode Test 10",
			'data' => "00020101021230190015",
			'result' => [
				"merchantType",
				"bakongAccountID",
				"accountInformation",
				"merchantID",
				"acquiringBank",
				"billNumber",
				"mobileNumber",
				"storeLabel",
				"terminalLabel",
				"merchantCategoryCode",
				"transactionCurrency",
				"transactionAmount",
				"countryCode",
				"merchantName",
				"merchantCity",
				"timestamp",
				"crc",
			],
		],
		[
			'statement' => "Decode Test 11",
			'data' => "10263041234",
			'result' => [
				"merchantType",
				"bakongAccountID",
				"accountInformation",
				"merchantID",
				"acquiringBank",
				"billNumber",
				"mobileNumber",
				"storeLabel",
				"terminalLabel",
				"payloadFormatIndicator",
				"pointofInitiationMethod",
				"merchantCategoryCode",
				"transactionCurrency",
				"transactionAmount",
				"countryCode",
				"merchantName",
				"merchantCity",
				"timestamp",
				"crc",
			],
		],
		[
			'statement' => "Decode Test 12",
			'data' => "63041234",
			'result' => [
				"merchantType",
				"bakongAccountID",
				"accountInformation",
				"merchantID",
				"acquiringBank",
				"billNumber",
				"mobileNumber",
				"storeLabel",
				"terminalLabel",
				"payloadFormatIndicator",
				"pointofInitiationMethod",
				"merchantCategoryCode",
				"transactionCurrency",
				"transactionAmount",
				"countryCode",
				"merchantName",
				"merchantCity",
				"timestamp",
			],
		],
		[
			'statement' => "Decode Test 13",
			'data' => "00020101021230410015john_smith@devb01061234560208Dev Bank53038405204599954035.05802KH5916john smith actor6010Phnom Penh62150111Invoice#06999170013161343857589263040437",
			'result' => [
				"accountInformation",
				"mobileNumber",
				"storeLabel",
				"terminalLabel",
			],
		],
		[
			'statement' => "Decode Test 14",
			'data' => "00020101021252045999530384054035.05802KH5916john smith actor6010Phnom Penh62150111Invoice#06999170013161343857589263042494",
			'result' => [
				"merchantType",
				"bakongAccountID",
				"accountInformation",
				"merchantID",
				"acquiringBank",
				"mobileNumber",
				"storeLabel",
				"terminalLabel",
			],
		],
		[
			'statement' => "Decode Test 15",
			'data' => "000201010212301900151234567890123456304D807",
			'result' => [
				"bakongAccountID",
				"accountInformation",
				"merchantID",
				"acquiringBank",
				"billNumber",
				"mobileNumber",
				"storeLabel",
				"terminalLabel",
				"payloadFormatIndicator",
				"pointofInitiationMethod",
				"merchantCategoryCode",
				"transactionCurrency",
				"transactionAmount",
				"countryCode",
				"merchantName",
				"merchantCity",
				"timestamp",
				"crc",
			],
		],
		[
			'statement' => "Decode Test 16",
			'data' => "00020101021230440040123456789012345678901234567890123456789063041747",
			'result' => [
				"accountInformation",
				"merchantID",
				"acquiringBank",
				"billNumber",
				"mobileNumber",
				"storeLabel",
				"terminalLabel",
				"merchantCategoryCode",
				"transactionCurrency",
				"transactionAmount",
				"countryCode",
				"merchantName",
				"merchantCity",
				"timestamp",
			],
		],
		[
			'statement' => "Decode Test 17",
			'data' => "000301001021230190015john_smith@devb630493FD",
			'result' => [
				"accountInformation",
				"merchantID",
				"acquiringBank",
				"billNumber",
				"mobileNumber",
				"storeLabel",
				"terminalLabel",
				"merchantCategoryCode",
				"transactionCurrency",
				"transactionAmount",
				"countryCode",
				"merchantName",
				"merchantCity",
				"timestamp",
			],
		],
	];

	public function test_decode()
	{
		foreach ($this->testData as $data) {
			$decoded = BakongKHQR::decode($data['data']);
			print_r($decoded);
			break;
		}
	}
}
