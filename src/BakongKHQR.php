<?php

declare(strict_types=1);

namespace KHQR;

use Exception;
use KHQR\Api\Account;
use KHQR\Api\DeepLink;
use KHQR\Api\Token;
use KHQR\Api\Transaction;
use KHQR\Config\Constants;
use KHQR\Exceptions\KHQRException;
use KHQR\Helpers\EMV;
use KHQR\Helpers\KHQRData;
use KHQR\Helpers\Utils;
use KHQR\Models\AdditionalData;
use KHQR\Models\CountryCode;
use KHQR\Models\CRCValidation;
use KHQR\Models\GlobalUniqueIdentifier;
use KHQR\Models\IndividualInfo;
use KHQR\Models\KHQRDeepLinkData;
use KHQR\Models\KHQRResponse;
use KHQR\Models\MerchantCategoryCode;
use KHQR\Models\MerchantCity;
use KHQR\Models\MerchantInfo;
use KHQR\Models\MerchantInformationLanguageTemplate;
use KHQR\Models\MerchantName;
use KHQR\Models\PayloadFormatIndicator;
use KHQR\Models\PointOfInitiationMethod;
use KHQR\Models\SourceInfo;
use KHQR\Models\Timestamp;
use KHQR\Models\TimestampData;
use KHQR\Models\TransactionAmount;
use KHQR\Models\TransactionCurrency;
use KHQR\Models\UnionpayMerchantAccount;

class BakongKHQR
{
    private string $token;

    public function __construct(string $token)
    {
        if (Utils::isBlank($token)) {
            throw new \InvalidArgumentException('Token cannot be blank');
        }

        $this->token = $token;
    }

    /**
     * @return array<string, mixed>
     */
    public function checkTransactionByMD5(string $md5, bool $isTest = false): array
    {
        return Transaction::checkTransactionByMD5($this->token, $md5, $isTest);
    }

    /**
     * @param  array<string>  $md5Array
     * @return array<string, mixed>
     */
    public function checkTransactionByMD5List(array $md5Array, bool $isTest = false): array
    {
        return Transaction::checkTransactionByMD5List($this->token, $md5Array, $isTest);
    }

    /**
     * @return array<string, mixed>
     */
    public function checkTransactionByFullHash(string $fullHash, bool $isTest = false): array
    {
        return Transaction::checkTransactionByFullHash($this->token, $fullHash, $isTest);
    }

    /**
     * @param  array<string>  $fullHashArrray
     * @return array<string, mixed>
     */
    public function checkTransactionByFullHashList(array $fullHashArrray, bool $isTest = false): array
    {
        return Transaction::checkTransactionByFullHashList($this->token, $fullHashArrray, $isTest);
    }

    /**
     * @return array<string, mixed>
     */
    public function checkTransactionByShortHash(string $shortHash, float $amount, string $currency, bool $isTest = false): array
    {
        return Transaction::checkTransactionByShortHash($this->token, $shortHash, $amount, $currency, $isTest);
    }

    /**
     * @return array<string, mixed>
     */
    public function checkTransactionByInstructionReference(string $ref, bool $isTest = false): array
    {
        return Transaction::checkTransactionByInstructionReference($this->token, $ref, $isTest);
    }

    /**
     * @return array<string, mixed>
     */
    public function checkTransactionByExternalReference(string $ref, bool $isTest = false): array
    {
        return Transaction::checkTransactionByExternalReference($this->token, $ref, $isTest);
    }

    /**
     * @return array<string, mixed>
     */
    public static function renewToken(string $email, bool $isTest = false): array
    {
        return Token::renewToken($email, $isTest);
    }

    public static function generateIndividual(IndividualInfo $individualInfo): \KHQR\Models\KHQRResponse
    {
        $khqr = self::generateKHQR($individualInfo, KHQRData::MERCHANT_TYPE_INDIVIDUAL);
        $result = [
            'qr' => $khqr,
            'md5' => md5($khqr),
        ];

        return new KHQRResponse($result, null);
    }

    public static function generateMerchant(MerchantInfo $merchantInfo): \KHQR\Models\KHQRResponse
    {
        $khqr = self::generateKHQR($merchantInfo, KHQRData::MERCHANT_TYPE_MERCHANT);
        $result = [
            'qr' => $khqr,
            'md5' => md5($khqr),
        ];

        return new KHQRResponse($result, null);
    }

    public static function decode(string $khqrString): \KHQR\Models\KHQRResponse
    {
        $decodedData = self::decodeKHQRString($khqrString);

        return new KHQRResponse($decodedData, null);
    }

    public static function verify(string $KHQRString): CRCValidation
    {
        $isCorrectFormCRC = Utils::checkCRCRegExp($KHQRString);
        if (! $isCorrectFormCRC) {
            return new CRCValidation(false);
        }

        $crc = mb_substr(string: $KHQRString, start: -4, encoding: 'UTF-8');
        $KHQRNoCrc = mb_substr($KHQRString, 0, -4, 'UTF-8');
        $validCRC = Utils::crc16($KHQRNoCrc) === strtoupper($crc);
        $isValidCRC = new CRCValidation($validCRC);

        try {
            if (! $isValidCRC->isValid || strlen($KHQRString) < EMV::INVALID_LENGTH_KHQR) {
                throw new KHQRException(KHQRException::KHQR_INVALID);
            }

            self::decodeKHQRValidation($KHQRString);

            return new CRCValidation(true);
        } catch (Exception $error) {
            // error_log($error->getMessage());
            // error_log($error->getTraceAsString());
            return new CRCValidation(false);
        }
    }

    public static function generateDeepLinkWithUrl(string $url, string $qr, ?SourceInfo $sourceInfo): KHQRResponse
    {
        // Check if URL is valid
        if (! DeepLink::isValidLink($url)) {
            throw new KHQRException(KHQRException::INVALID_DEEP_LINK_URL);
        }

        // Validate QR (CRC check)
        $isValidKHQR = self::verify($qr);
        if (! $isValidKHQR->isValid) {
            throw new KHQRException(KHQRException::KHQR_INVALID);
        }

        // Validate sourceInfo fields if provided
        if ($sourceInfo && ($sourceInfo->appIconUrl === null || $sourceInfo->appIconUrl === '' || $sourceInfo->appIconUrl === '0' ||
            ($sourceInfo->appName === null || $sourceInfo->appName === '' || $sourceInfo->appName === '0') ||
            ($sourceInfo->appDeepLinkCallback === null || $sourceInfo->appDeepLinkCallback === '' || $sourceInfo->appDeepLinkCallback === '0'))) {
            throw new KHQRException(KHQRException::INVALID_DEEP_LINK_SOURCE_INFO);
        }
        // Call API to generate deep link
        $data = DeepLink::callDeepLinkAPI($url, ['qr' => $qr, 'sourceInfo' => (array) $sourceInfo]);
        if (is_array($data) && isset($data['data'])) {
            $deepLinkData = new KHQRDeepLinkData($data['data']['shortLink']);

            return new KHQRResponse($deepLinkData, null);
        }

        return new KHQRResponse($data, null);
    }

    public static function generateDeepLink(string $qr, ?SourceInfo $sourceInfo, bool $isTest = false): KHQRResponse
    {
        $url = $isTest ? Constants::SIT_DEEPLINK_URL : Constants::DEEPLINK_URL;

        return self::generateDeepLinkWithUrl($url, $qr, $sourceInfo);
    }

    public static function checkBakongAccountWithUrl(string $url, string $bakongID): KHQRResponse
    {
        $accountExistResponse = Account::checkBakongAccountExistence($url, $bakongID);

        return new KHQRResponse($accountExistResponse, null);
    }

    public static function checkBakongAccount(string $bakongID, bool $isTest = false): \KHQR\Models\KHQRResponse
    {
        $url = $isTest ? Constants::SIT_ACCOUNT_URL : Constants::ACCOUNT_URL;

        return self::checkBakongAccountWithUrl($url, $bakongID);
    }

    /**
     * Decode helper function
     * This decode funcition has a flow of
     * 1. Slice the string as each KHQR tag and store into memory
     * 2. Check if the required field exist
     * 3. Check if the KHQR Code given is in order or not
     * 4. Get the value of each tag and if there is subtag repeat number 1
     *
     * @param  string  $khqrString  The KHQR string to decode.
     * @return array<string, mixed> An associative array containing the decoded KHQR string.
     */
    private static function decodeKHQRValidation(string $khqrString): array
    {
        $allField = array_map(fn ($el): string => $el['tag'], KHQRData::KHQRTag);
        $subtag = array_map(fn ($obj): string => $obj['tag'], array_filter(KHQRData::KHQRTag, fn ($el): bool => isset($el['sub']) && $el['sub']));
        $requiredField = array_map(fn ($el): string => $el['tag'], array_filter(KHQRData::KHQRTag, fn ($el): bool => $el['required'] == true));
        $subTagInput = KHQRData::KHQRSubtag['input'];
        $subTagCompare = KHQRData::KHQRSubtag['compare'];

        $tags = [];
        $merchantType = 'individual';
        $lastTag = '';

        while ($khqrString) {
            $sliceTagObject = Utils::cutString($khqrString);
            $tag = $sliceTagObject['tag'];
            $value = $sliceTagObject['value'];
            $slicedString = $sliceTagObject['slicedString'];

            if ($tag == $lastTag) {
                break;
            }

            $isMerchant = $tag == '30';

            if ($isMerchant) {
                $merchantType = 'merchant';
                $tag = '29';
            }

            if (in_array($tag, $allField)) {
                $tags[] = ['tag' => $tag, 'value' => $value];
                $requiredField = array_filter($requiredField, fn ($el): bool => $el != $tag);
            }

            $khqrString = $slicedString;
            $lastTag = $tag;
        }

        // Tag pointOfInitiationMethod 01, dynamic khqr 12
        if (array_filter($tags, fn ($item) => $item['tag'] === '01' && $item['value'] === '12')) {
            if (! array_filter($tags, fn ($item) => $item['tag'] === '54')) {
                throw new KHQRException(KHQRException::INVALID_DYNAMIC_KHQR);
            }
            if (! array_filter($tags, fn ($item) => $item['tag'] === '99')) {
                throw new KHQRException(KHQRException::EXPIRATION_TIMESTAMP_REQUIRED);
            }
        }

        // Check required timestamp for dynamic KHQR (tag amount 54, tag timestamp 99)
        if (array_filter($tags, fn ($item) => $item['tag'] === '54') && ! array_filter($tags, fn ($item) => $item['tag'] === '99')) {
            throw new KHQRException(KHQRException::EXPIRATION_TIMESTAMP_REQUIRED);
        }

        $requiredFieldNotExist = count($requiredField) != 0;
        if ($requiredFieldNotExist) {
            $requiredTag = current($requiredField);
            $missingInstance = ((array) Utils::findTag(KHQRData::KHQRTag, $requiredTag))['instance'];
            new $missingInstance($requiredTag, null);
        }

        $decodeValue = [
            'merchantType' => $merchantType,
        ];

        foreach (
            array_map(fn ($el): array => $el['data'], $subTagInput) as $obj
        ) {
            $decodeValue = array_merge($decodeValue, $obj);
        }

        $poi = null;
        foreach ($tags as $khqrTag) {
            $tag = $khqrTag['tag'];
            $khqr = current(array_filter(KHQRData::KHQRTag, fn ($el): bool => $el['tag'] == $tag));
            assert($khqr !== false);
            $value = $khqrTag['value'];
            $inputValue = $value;

            if ($tag === EMV::POINT_OF_INITIATION_METHOD) {
                $poi = $value;
            }

            if (in_array($tag, $subtag)) {
                $inputdata = (array) ((array) Utils::findTag($subTagInput, $tag))['data'];
                while ($value) {
                    $cutsubstring = Utils::cutString($value);
                    $tempSubtag = $cutsubstring['tag'];
                    $subtagValue = $cutsubstring['value'];
                    $slicedSubtag = $cutsubstring['slicedString'];

                    $nameSubtag = current(array_filter($subTagCompare, fn ($el): bool => $el['tag'] == $tag && $el['subTag'] == $tempSubtag));

                    if ($nameSubtag) {
                        $nameSubtag = $nameSubtag['name'];
                        $inputdata[$nameSubtag] = $subtagValue;
                        $inputValue = $inputdata;
                    }

                    $value = $slicedSubtag;
                }

                assert(is_array($inputValue));

                $tagClass = $khqr['instance'];
                assert(in_array($tagClass, [
                    AdditionalData::class,
                    MerchantInformationLanguageTemplate::class,
                    GlobalUniqueIdentifier::class,
                    Timestamp::class,
                ]));

                // Check if the tag value is valid
                if ($tagClass === Timestamp::class) {
                    $timestampData = new TimestampData($inputValue['creationTimestamp'], $inputValue['expirationTimestamp']);
                    new Timestamp($tag, $timestampData, $poi);
                } else {
                    new $tagClass($tag, $inputValue);
                }

                $decodeValue = array_merge($decodeValue, $inputValue);
            } else {
                assert($khqr['instance'] !== Timestamp::class);
                $instance = new $khqr['instance']($tag, $value);
                $decodeValue[$khqr['type']] = $instance->value;
            }
        }

        return $decodeValue;
    }

    /**
     * Decode helper function
     * This decode funcition has a flow of
     * 1. Slice the string as each KHQR tag and store into memory
     * 2. Check if the required field exist
     * 3. Check if the KHQR Code given is in order or not
     * 4. Get the value of each tag and if there is subtag repeat number 1
     *
     * @param  string  $khqrString  The KHQR string to decode.
     * @return array<string, mixed> An associative array containing the decoded KHQR string.
     */
    private static function decodeKHQRString(string $khqrString): array
    {
        $allField = array_map(fn ($el): string => $el['tag'], KHQRData::KHQRTag);
        $subtag = array_map(fn ($obj): string => $obj['tag'], array_filter(KHQRData::KHQRTag, fn ($el): bool => isset($el['sub']) && $el['sub']));
        $requiredField = array_map(fn ($el): string => $el['tag'], array_filter(KHQRData::KHQRTag, fn ($el): bool => $el['required'] == true));

        $subTagInput = KHQRData::KHQRSubtag['input'];
        $subTagCompare = KHQRData::KHQRSubtag['compare'];

        $tags = [];
        $merchantType = null;
        $lastTag = '';
        $isMerchantTag = false;

        while (strlen($khqrString) > 0) {
            $sliceTagObject = Utils::cutString($khqrString);
            $tag = $sliceTagObject['tag'];
            $value = $sliceTagObject['value'];
            $slicedString = $sliceTagObject['slicedString'];

            if ($tag == $lastTag) {
                break;
            }

            $isMerchant = $tag == '30';
            if ($isMerchant) {
                $merchantType = '30';
                $tag = '29';
                $isMerchantTag = true;
            } elseif ($tag == '29') {
                $merchantType = '29';
            }

            if (in_array($tag, $allField)) {
                $tags[$tag] = $value;
                $requiredField = array_filter($requiredField, fn ($el): bool => $el != $tag);
            }

            $khqrString = $slicedString;
            $lastTag = $tag;
        }

        $decodeValue = ['merchantType' => $merchantType];

        foreach ($subTagInput as $el) {
            $decodeValue = array_merge($decodeValue, $el['data']);
        }

        foreach (KHQRData::KHQRTag as $khqrTag) {
            $tag = $khqrTag['tag'];
            $khqr = current(array_filter(KHQRData::KHQRTag, fn ($el): bool => $el['tag'] === $tag));
            assert($khqr !== false);

            $value = $tags[$tag] ?? null;

            if (in_array($tag, $subtag)) {
                $inputValue = null;
                $inputdata = (array) ((array) Utils::findTag($subTagInput, $tag))['data'];
                while ($value) {
                    $cutsubstring = Utils::cutString($value);
                    $tempSubtag = $cutsubstring['tag'];
                    $subtagValue = $cutsubstring['value'];
                    $slicedSubtag = $cutsubstring['slicedString'];

                    $nameSubtag = current(array_filter($subTagCompare, fn ($el): bool => $el['tag'] === $tag && $el['subTag'] == $tempSubtag));

                    if ($nameSubtag) {
                        $nameSubtag = $nameSubtag['name'];
                        if ($isMerchantTag && $nameSubtag == 'accountInformation') {
                            $nameSubtag = 'merchantID';
                        }
                        $inputdata[$nameSubtag] = $subtagValue;
                        $inputValue = $inputdata;
                    }
                    $value = $slicedSubtag;
                }

                if (is_array($inputValue)) {
                    $decodeValue = array_merge($decodeValue, $inputValue);
                }
            } else {
                $decodeValue[$khqr['type']] = $value;
                // if ($tag === '99' && $value == null) {
                //     $decodeValue[$khqr['type']] = null;
                // }
            }
        }

        return $decodeValue;
    }

    private static function generateKHQR(MerchantInfo|IndividualInfo $info, string $type): string
    {
        if ($type === KHQRData::MERCHANT_TYPE_MERCHANT) {
            $merchantInfo = [
                'bakongAccountID' => $info->bakongAccountID,
                'merchantID' => $info->merchantID,
                'acquiringBank' => $info->acquiringBank,
                'isMerchant' => true,
            ];
        } else {
            $merchantInfo = [
                'bakongAccountID' => $info->bakongAccountID,
                'accountInformation' => $info->accountInformation,
                'acquiringBank' => $info->acquiringBank,
                'isMerchant' => false,
            ];
        }

        $additionalDataInformation = [
            'billNumber' => $info->billNumber,
            'mobileNumber' => $info->mobileNumber,
            'storeLabel' => $info->storeLabel,
            'terminalLabel' => $info->terminalLabel,
            'purposeOfTransaction' => $info->purposeOfTransaction,
        ];

        $languageInformation = [
            'languagePreference' => $info->languagePreference,
            'merchantNameAlternateLanguage' => $info->merchantNameAlternateLanguage,
            'merchantCityAlternateLanguage' => $info->merchantCityAlternateLanguage,
        ];
        $amount = $info->amount;
        $payloadFormatIndicator = new PayloadFormatIndicator(EMV::PAYLOAD_FORMAT_INDICATOR, EMV::DEFAULT_PAYLOAD_FORMAT_INDICATOR);
        $QRType = EMV::DYNAMIC_QR;
        if (! isset($amount) || $amount == 0) {
            $QRType = EMV::STATIC_QR;
        }
        $pointOfInitiationMethod = new PointOfInitiationMethod(EMV::POINT_OF_INITIATION_METHOD, $QRType);
        $upi = null;
        if ($info->upiMerchantAccount !== null && $info->upiMerchantAccount !== '' && $info->upiMerchantAccount !== '0') {
            $upi = new UnionpayMerchantAccount(EMV::UNIONPAY_MERCHANT_ACCOUNT, $info->upiMerchantAccount);
        }
        $KHQRType = ($type === KHQRData::MERCHANT_TYPE_MERCHANT) ? EMV::MERCHANT_ACCOUNT_INFORMATION_MERCHANT : EMV::MERCHANT_ACCOUNT_INFORMATION_INDIVIDUAL;
        $globalUniqueIdentifier = new GlobalUniqueIdentifier($KHQRType, $merchantInfo);
        $merchantCategoryCode = new MerchantCategoryCode(EMV::MERCHANT_CATEGORY_CODE,$info->merchantCategoryCode ?? EMV::DEFAULT_MERCHANT_CATEGORY_CODE);
        $currency = new TransactionCurrency(EMV::TRANSACTION_CURRENCY, $info->currency);
        if ($info->currency == KHQRData::CURRENCY_USD && $upi) {
            throw new KHQRException(KHQRException::UPI_ACCOUNT_INFORMATION_INVALID_CURRENCY);
        }
        $KHQRInstances = [
            $payloadFormatIndicator,
            $pointOfInitiationMethod,
            $upi ?: '',
            $globalUniqueIdentifier,
            $merchantCategoryCode,
            $currency,
        ];

        if (isset($amount) && $amount != 0) {
            if ($info->currency == KHQRData::CURRENCY_KHR) {
                if (floor($amount) !== $amount) {
                    throw new KHQRException(KHQRException::TRANSACTION_AMOUNT_INVALID);
                }

                $amountString = strval(round($amount)); // We use round() here as the npm package's Math.round()
                $KHQRInstances[] = new TransactionAmount(EMV::TRANSACTION_AMOUNT, $amountString);
            } else {
                $amountString = strval($amount);
                $hasDecimalPlaces = fmod($amount, 1) != 0;
                if ($hasDecimalPlaces) {
                    // Throw an error if the amount has more than 2 decimal places
                    $amountString = number_format($amount, 2, '.', '');
                    // In sync with JavaScript, substraction should be accurate up to 15 decimal places
                    $epsilon = 0.000000000000001;
                    if (abs($amount - floatval($amountString)) >= $epsilon) {
                        throw new KHQRException(KHQRException::TRANSACTION_AMOUNT_INVALID);
                    }
                }

                $KHQRInstances[] = new TransactionAmount(EMV::TRANSACTION_AMOUNT, $amountString);
            }
        }

        $countryCode = new CountryCode(EMV::COUNTRY_CODE, EMV::DEFAULT_COUNTRY_CODE);
        $KHQRInstances[] = $countryCode;
        $merchantName = new MerchantName(EMV::MERCHANT_NAME, $info->merchantName);
        $KHQRInstances[] = $merchantName;
        $merchantCity = new MerchantCity(EMV::MERCHANT_CITY, $info->merchantCity ?? EMV::DEFAULT_MERCHANT_CITY);
        $KHQRInstances[] = $merchantCity;
        if (array_filter($additionalDataInformation) !== []) {
            $additionalData = new AdditionalData(EMV::ADDITIONAL_DATA_TAG, $additionalDataInformation);
            $KHQRInstances[] = $additionalData;
        }
        if (array_filter($languageInformation) !== []) {
            $languageTemplate = new MerchantInformationLanguageTemplate(EMV::MERCHANT_INFORMATION_LANGUAGE_TEMPLATE, $languageInformation);
            $KHQRInstances[] = $languageTemplate;
        }

        if ($QRType === EMV::DYNAMIC_QR) {
            if (! isset($info->expirationTimestamp)) {
                throw new KHQRException(KHQRException::EXPIRATION_TIMESTAMP_REQUIRED);
            }

            $currentTimestampInMilliseconds = strval(floor(microtime(true) * 1000));
            $timestampData = new TimestampData($currentTimestampInMilliseconds, $info->expirationTimestamp);
            $timeStamp = new Timestamp(EMV::TIMESTAMP_TAG, $timestampData, $QRType);
            $KHQRInstances[] = $timeStamp;
        }

        $khqrNoCrc = '';
        foreach ($KHQRInstances as $instance) {
            $khqrNoCrc .= (string) $instance;
        }
        $khqr = $khqrNoCrc.EMV::CRC.EMV::CRC_LENGTH;

        return $khqr.Utils::crc16($khqr);
    }

    public static function isExpiredToken(string $token): bool
    {
        return Token::isExpiredToken($token);
    }

    public static function decodeNonKhqr(string $qr): \KHQR\Models\KHQRResponse
    {
        $firstLevelData = [];
        $finalData = [];
        $remaningQR = $qr;

        // first-level
        do {
            $result = Utils::extractTLV($remaningQR);
            $tag = (string) $result['tag'];
            $length = (int) $result['length'];
            $value = (string) $result['value'];
            $remainString = (string) $result['remainString'];

            if (! Utils::isValidTLV($tag, $length, $value)) {
                break;
            }
            $firstLevelData[$tag] = $value;
            $remaningQR = $remainString;
        } while (! empty($remaningQR));

        // second-level
        foreach ($firstLevelData as $tag => $value) {
            $remainingValue = $value;
            $finalData[$tag] = $remainingValue;

            $secondLevelData = [];
            $thirdLevelData = [];

            $tagInt = (int) $tag;
            if (! ($tagInt >= 26 && $tagInt <= 51) &&
                ! ($tagInt >= 80 && $tagInt <= 99) &&
                $tag !== 64 &&
                $tag !== 62) {
                continue;
            }

            // check if value has emv format
            if (strlen($remainingValue) >= 6) {
                do {
                    $result = Utils::extractTLV($remainingValue);
                    $subTag = (string) $result['tag'];
                    $length = (int) $result['length'];
                    $subValue = (string) $result['value'];
                    $remainString = (string) $result['remainString'];

                    if (! Utils::isValidTLV($subTag, $length, $subValue)) {
                        break;
                    }
                    $remainingValue = $remainString;

                    // check for main tag 62 and subtags in range 50-99
                    if ($tagInt === 62 && (int) $subTag >= 50 && (int) $subTag <= 99) {
                        $remainingValueL3 = $subValue;

                        // third-level
                        do {
                            $result = Utils::extractTLV($remainingValueL3);
                            $subTagL3 = (string) $result['tag'];
                            $length = (int) $result['length'];
                            $valueL3 = (string) $result['value'];
                            $remainString = (string) $result['remainString'];

                            if (! Utils::isValidTLV($subTagL3, $length, $valueL3)) {
                                break;
                            }
                            $thirdLevelData[$subTagL3] = $valueL3;
                            $remainingValueL3 = $remainString;
                        } while (! empty($remainingValueL3));
                    }

                    if (! empty($thirdLevelData)) {
                        $secondLevelData[$subTag] = $thirdLevelData;
                    } else {
                        $secondLevelData[$subTag] = $subValue;
                    }
                } while (! empty($remainingValue));

                if (! empty($secondLevelData)) {
                    $finalData[$tag] = $secondLevelData;
                }
            }
        }

        return new KHQRResponse($finalData, null);
    }
}
