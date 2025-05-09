<?php

declare(strict_types=1);

namespace KHQR\Exceptions;

use Exception;

class KHQRException extends Exception
{
    // Define error constants
    public const BAKONG_ACCOUNT_ID_REQUIRED = 'BAKONG_ACCOUNT_ID_REQUIRED';

    public const MERCHANT_NAME_REQUIRED = 'MERCHANT_NAME_REQUIRED';

    public const BAKONG_ACCOUNT_ID_INVALID = 'BAKONG_ACCOUNT_ID_INVALID';

    public const TRANSACTION_AMOUNT_INVALID = 'TRANSACTION_AMOUNT_INVALID';

    public const MERCHANT_TYPE_REQUIRED = 'MERCHANT_TYPE_REQUIRED';

    public const BAKONG_ACCOUNT_ID_LENGTH_INVALID = 'BAKONG_ACCOUNT_ID_LENGTH_INVALID';

    public const MERCHANT_NAME_LENGTH_INVALID = 'MERCHANT_NAME_LENGTH_INVALID';

    public const KHQR_INVALID = 'KHQR_INVALID';

    public const CURRENCY_TYPE_REQUIRED = 'CURRENCY_TYPE_REQUIRED';

    public const BILL_NUMBER_LENGTH_INVALID = 'BILL_NUMBER_LENGTH_INVALID';

    public const STORE_LABEL_LENGTH_INVALID = 'STORE_LABEL_LENGTH_INVALID';

    public const TERMINAL_LABEL_LENGTH_INVALID = 'TERMINAL_LABEL_LENGTH_INVALID';

    public const CONNECTION_TIMEOUT = 'CONNECTION_TIMEOUT';

    public const INVALID_DEEP_LINK_SOURCE_INFO = 'INVALID_DEEP_LINK_SOURCE_INFO';

    public const INTERNAL_SERVER_ERROR = 'INTERNAL_SERVER_ERROR';

    public const PAYLOAD_FORMAT_INDICATOR_LENGTH_INVALID = 'PAYLOAD_FORMAT_INDICATOR_LENGTH_INVALID';

    public const POINT_INITIATION_LENGTH_INVALID = 'POINT_INITIATION_LENGTH_INVALID';

    public const MERCHANT_CODE_LENGTH_INVALID = 'MERCHANT_CODE_LENGTH_INVALID';

    public const TRANSACTION_CURRENCY_LENGTH_INVALID = 'TRANSACTION_CURRENCY_LENGTH_INVALID';

    public const COUNTRY_CODE_LENGTH_INVALID = 'COUNTRY_CODE_LENGTH_INVALID';

    public const MERCHANT_CITY_LENGTH_INVALID = 'MERCHANT_CITY_LENGTH_INVALID';

    public const CRC_LENGTH_INVALID = 'CRC_LENGTH_INVALID';

    public const PAYLOAD_FORMAT_INDICATOR_TAG_REQUIRED = 'PAYLOAD_FORMAT_INDICATOR_TAG_REQUIRED';

    public const CRC_TAG_REQUIRED = 'CRC_TAG_REQUIRED';

    public const MERCHANT_CATEGORY_TAG_REQUIRED = 'MERCHANT_CATEGORY_TAG_REQUIRED';

    public const COUNTRY_CODE_TAG_REQUIRED = 'COUNTRY_CODE_TAG_REQUIRED';

    public const MERCHANT_CITY_TAG_REQUIRED = 'MERCHANT_CITY_TAG_REQUIRED';

    public const UNSUPPORTED_CURRENCY = 'UNSUPPORTED_CURRENCY';

    public const INVALID_DEEP_LINK_URL = 'INVALID_DEEP_LINK_URL';

    public const MERCHANT_ID_REQUIRED = 'MERCHANT_ID_REQUIRED';

    public const ACQUIRING_BANK_REQUIRED = 'ACQUIRING_BANK_REQUIRED';

    public const MERCHANT_ID_LENGTH_INVALID = 'MERCHANT_ID_LENGTH_INVALID';

    public const ACQUIRING_BANK_LENGTH_INVALID = 'ACQUIRING_BANK_LENGTH_INVALID';

    public const MOBILE_NUMBER_LENGTH_INVALID = 'MOBILE_NUMBER_LENGTH_INVALID';

    public const ACCOUNT_INFORMATION_LENGTH_INVALID = 'ACCOUNT_INFORMATION_LENGTH_INVALID';

    public const TAG_NOT_IN_ORDER = 'TAG_NOT_IN_ORDER';

    public const LANGUAGE_PREFERENCE_REQUIRED = 'LANGUAGE_PREFERENCE_REQUIRED';

    public const LANGUAGE_PREFERENCE_LENGTH_INVALID = 'LANGUAGE_PREFERENCE_LENGTH_INVALID';

    public const MERCHANT_NAME_ALTERNATE_LANGUAGE_REQUIRED = 'MERCHANT_NAME_ALTERNATE_LANGUAGE_REQUIRED';

    public const MERCHANT_NAME_ALTERNATE_LANGUAGE_LENGTH_INVALID = 'MERCHANT_NAME_ALTERNATE_LANGUAGE_LENGTH_INVALID';

    public const MERCHANT_CITY_ALTERNATE_LANGUAGE_LENGTH_INVALID = 'MERCHANT_CITY_ALTERNATE_LANGUAGE_LENGTH_INVALID';

    public const PURPOSE_OF_TRANSACTION_LENGTH_INVALID = 'PURPOSE_OF_TRANSACTION_LENGTH_INVALID';

    public const UPI_ACCOUNT_INFORMATION_LENGTH_INVALID = 'UPI_ACCOUNT_INFORMATION_LENGTH_INVALID';

    public const UPI_ACCOUNT_INFORMATION_INVALID_CURRENCY = 'UPI_ACCOUNT_INFORMATION_INVALID_CURRENCY';

    public const EXPIRATION_TIMESTAMP_REQUIRED = 'EXPIRATION_TIMESTAMP_REQUIRED';

    public const KHQR_EXPIRED = 'KHQR_EXPIRED';

    public const INVALID_DYNAMIC_KHQR = 'INVALID_DYNAMIC_KHQR';

    public const POINT_OF_INITIATION_METHOD_INVALID = 'POINT_OF_INITIATION_METHOD_INVALID';

    public const EXPIRATION_TIMESTAMP_LENGTH_INVALID = 'EXPIRATION_TIMESTAMP_LENGTH_INVALID';

    public const EXPIRATION_TIMESTAMP_IN_THE_PAST = 'EXPIRATION_TIMESTAMP_IN_THE_PAST';

    public const INVALID_MERCHANT_CATEGORY_CODE = 'INVALID_MERCHANT_CATEGORY_CODE';

    // Error codes and messages
    public const ERRORS = [
        self::BAKONG_ACCOUNT_ID_REQUIRED => [1, 'Bakong Account ID cannot be null or empty'],
        self::MERCHANT_NAME_REQUIRED => [2, 'Merchant name cannot be null or empty'],
        self::BAKONG_ACCOUNT_ID_INVALID => [3, 'Bakong Account ID is invalid'],
        self::TRANSACTION_AMOUNT_INVALID => [4, 'Amount is invalid'],
        self::MERCHANT_TYPE_REQUIRED => [5, 'Merchant type cannot be null or empty'],
        self::BAKONG_ACCOUNT_ID_LENGTH_INVALID => [6, 'Bakong Account ID Length is Invalid'],
        self::MERCHANT_NAME_LENGTH_INVALID => [7, 'Merchant Name Length is invalid'],
        self::KHQR_INVALID => [8, 'KHQR provided is invalid'],
        self::CURRENCY_TYPE_REQUIRED => [9, 'Currency type cannot be null or empty'],
        self::BILL_NUMBER_LENGTH_INVALID => [10, 'Bill Name Length is invalid'],
        self::STORE_LABEL_LENGTH_INVALID => [11, 'Store Label Length is invalid'],
        self::TERMINAL_LABEL_LENGTH_INVALID => [12, 'Terminal Label Length is invalid'],
        self::CONNECTION_TIMEOUT => [13, 'Cannot reach Bakong Open API service. Please check internet connection'],
        self::INVALID_DEEP_LINK_SOURCE_INFO => [14, 'Source Info for Deep Link is invalid'],
        self::INTERNAL_SERVER_ERROR => [15, 'Internal server error'],
        self::PAYLOAD_FORMAT_INDICATOR_LENGTH_INVALID => [16, 'Payload Format indicator Length is invalid'],
        self::POINT_INITIATION_LENGTH_INVALID => [17, 'Point of initiation Length is invalid'],
        self::MERCHANT_CODE_LENGTH_INVALID => [18, 'Merchant code Length is invalid'],
        self::TRANSACTION_CURRENCY_LENGTH_INVALID => [19, 'Transaction currency Length is invalid'],
        self::COUNTRY_CODE_LENGTH_INVALID => [20, 'Country code Length is invalid'],
        self::MERCHANT_CITY_LENGTH_INVALID => [21, 'Merchant city Length is invalid'],
        self::CRC_LENGTH_INVALID => [22, 'CRC Length is invalid'],
        self::PAYLOAD_FORMAT_INDICATOR_TAG_REQUIRED => [23, 'Payload format indicator tag required'],
        self::CRC_TAG_REQUIRED => [24, 'CRC tag required'],
        self::MERCHANT_CATEGORY_TAG_REQUIRED => [25, 'Merchant category tag required'],
        self::COUNTRY_CODE_TAG_REQUIRED => [26, 'Country Code cannot be null or empty'],
        self::MERCHANT_CITY_TAG_REQUIRED => [27, 'Merchant City cannot be null or empty'],
        self::UNSUPPORTED_CURRENCY => [28, 'Unsupported currency'],
        self::INVALID_DEEP_LINK_URL => [29, 'Deep Link URL is not valid'],
        self::MERCHANT_ID_REQUIRED => [30, 'Merchant ID cannot be null or empty'],
        self::ACQUIRING_BANK_REQUIRED => [31, 'Acquiring Bank cannot be null or empty'],
        self::MERCHANT_ID_LENGTH_INVALID => [32, 'Merchant ID Length is invalid'],
        self::ACQUIRING_BANK_LENGTH_INVALID => [33, 'Acquiring Bank Length is invalid'],
        self::MOBILE_NUMBER_LENGTH_INVALID => [34, 'Mobile Number Length is invalid'],
        self::ACCOUNT_INFORMATION_LENGTH_INVALID => [35, 'Account Information Length is invalid'],
        self::TAG_NOT_IN_ORDER => [36, 'Tag is not in order'],
        self::LANGUAGE_PREFERENCE_REQUIRED => [37, 'Language Preference cannot be null or empty'],
        self::LANGUAGE_PREFERENCE_LENGTH_INVALID => [38, 'Language Preference Length is invalid'],
        self::MERCHANT_NAME_ALTERNATE_LANGUAGE_REQUIRED => [39, 'Merchant Name Alternate Language cannot be null or empty'],
        self::MERCHANT_NAME_ALTERNATE_LANGUAGE_LENGTH_INVALID => [40, 'Merchant Name Alternate Language Length is invalid'],
        self::MERCHANT_CITY_ALTERNATE_LANGUAGE_LENGTH_INVALID => [41, 'Merchant City Alternate Language Length is invalid'],
        self::PURPOSE_OF_TRANSACTION_LENGTH_INVALID => [42, 'Purpose of Transaction Length is invalid'],
        self::UPI_ACCOUNT_INFORMATION_LENGTH_INVALID => [43, 'Upi Account Information Length is invalid'],
        self::UPI_ACCOUNT_INFORMATION_INVALID_CURRENCY => [44, 'Upi Account Information Length does not accept USD'],
        self::EXPIRATION_TIMESTAMP_REQUIRED => [45, 'Expiration timestamp is required for dynamic KHQR'],
        self::KHQR_EXPIRED => [46, 'This dynamic KHQR has expired'],
        self::INVALID_DYNAMIC_KHQR => [47, 'This dynamic KHQR has invalid field transaction amount'],
        self::POINT_OF_INITIATION_METHOD_INVALID => [48, 'Point of Initiation Method is invalid'],
        self::EXPIRATION_TIMESTAMP_LENGTH_INVALID => [49, 'Expiration timestamp length is invalid'],
        self::EXPIRATION_TIMESTAMP_IN_THE_PAST => [50, 'Expiration timestamp is in the past'],
        self::INVALID_MERCHANT_CATEGORY_CODE => [51, 'Invalid merchant category code'],
    ];

    /**
     * Constructor to initialize exception with predefined error key.
     */
    public function __construct(string $key, ?int $code = 0)
    {
        if (! isset(self::ERRORS[$key])) {
            parent::__construct($key, $code ?? 0);
        } else {
            [$code, $message] = self::ERRORS[$key];
            parent::__construct($message, $code);
        }
    }

    /**
     * Get error details without throwing an exception.
     *
     * @return array<int|string>
     */
    public static function getError(string $key): array
    {
        return self::ERRORS[$key] ?? [0, 'Unknown error'];
    }
}
