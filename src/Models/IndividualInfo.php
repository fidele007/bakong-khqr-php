<?php

declare(strict_types=1);

namespace KHQR\Models;

use KHQR\Exceptions\KHQRException;
use KHQR\Helpers\KHQRData;
use KHQR\Helpers\Utils;

class IndividualInfo
{
    // Required parameters
    public string $bakongAccountID;

    public string $merchantName;

    public string $merchantCity;

    public ?string $merchantID = null;

    // Optional parameters
    public ?string $acquiringBank;

    public ?string $accountInformation;

    public ?int $currency;

    public ?float $amount;

    public ?string $billNumber;

    public ?string $storeLabel;

    public ?string $terminalLabel;

    public ?string $mobileNumber;

    public ?string $purposeOfTransaction;

    public ?string $languagePreference;

    public ?string $merchantNameAlternateLanguage;

    public ?string $merchantCityAlternateLanguage;

    public ?string $upiMerchantAccount;

    public ?string $expirationTimestamp;

    public ?string $merchantCategoryCode;

    public function __construct(
        string $bakongAccountID,
        string $merchantName,
        string $merchantCity,
        ?string $acquiringBank = null,
        ?string $accountInformation = null,
        ?int $currency = null,
        float $amount = 0.0,
        ?string $billNumber = null,
        ?string $storeLabel = null,
        ?string $terminalLabel = null,
        ?string $mobileNumber = null,
        ?string $purposeOfTransaction = null,
        ?string $languagePreference = null,
        ?string $merchantNameAlternateLanguage = null,
        ?string $merchantCityAlternateLanguage = null,
        ?string $upiMerchantAccount = null,
        ?string $expirationTimestamp = null,
        ?string $merchantCategoryCode = null
    ) {
        if (Utils::isBlank($bakongAccountID)) {
            throw new KHQRException(KHQRException::BAKONG_ACCOUNT_ID_REQUIRED);
        }

        if (Utils::isBlank($merchantName)) {
            throw new KHQRException(KHQRException::MERCHANT_NAME_REQUIRED);
        }

        if (Utils::isBlank($merchantCity)) {
            throw new KHQRException(KHQRException::MERCHANT_CITY_TAG_REQUIRED);
        }

        $this->bakongAccountID = $bakongAccountID;
        $this->merchantName = $merchantName;
        $this->merchantCity = $merchantCity;
        // additional optional parameters
        $this->acquiringBank = $acquiringBank;
        $this->accountInformation = $accountInformation;
        $this->currency = $currency ?? KHQRData::CURRENCY_KHR;
        $this->amount = $amount;
        $this->billNumber = $billNumber;
        $this->storeLabel = $storeLabel;
        $this->terminalLabel = $terminalLabel;
        $this->mobileNumber = $mobileNumber;
        $this->purposeOfTransaction = $purposeOfTransaction;
        $this->languagePreference = $languagePreference;
        $this->merchantNameAlternateLanguage = $merchantNameAlternateLanguage;
        $this->merchantCityAlternateLanguage = $merchantCityAlternateLanguage;
        $this->upiMerchantAccount = $upiMerchantAccount;
        $this->expirationTimestamp = $expirationTimestamp;
        $this->merchantCategoryCode = $merchantCategoryCode;
    }

    /**
     * @param array{
     *     acquiringBank?: string|null,
     *     accountInformation?: string|null,
     *     currency?: int|null,
     *     amount?: float|null,
     *     billNumber?: string|null,
     *     storeLabel?: string|null,
     *     terminalLabel?: string|null,
     *     mobileNumber?: string|null,
     *     purposeOfTransaction?: string|null,
     *     languagePreference?: string|null,
     *     merchantNameAlternateLanguage?: string|null,
     *     merchantCityAlternateLanguage?: string|null,
     *     upiMerchantAccount?: string|null,
     *     expirationTimestamp?: string|null,
     *     merchantCategoryCode?: string|null
     * } $optionalData
     */
    public static function withOptionalArray(
        string $bakongAccountID,
        string $merchantName,
        string $merchantCity,
        array $optionalData
    ): self {
        return new self(
            bakongAccountID: $bakongAccountID,
            merchantName: $merchantName,
            merchantCity: $merchantCity,
            acquiringBank: $optionalData['acquiringBank'] ?? null,
            accountInformation: $optionalData['accountInformation'] ?? null,
            currency: $optionalData['currency'] ?? null,
            amount: $optionalData['amount'] ?? 0.0,
            billNumber: $optionalData['billNumber'] ?? null,
            storeLabel: $optionalData['storeLabel'] ?? null,
            terminalLabel: $optionalData['terminalLabel'] ?? null,
            mobileNumber: $optionalData['mobileNumber'] ?? null,
            purposeOfTransaction: $optionalData['purposeOfTransaction'] ?? null,
            languagePreference: $optionalData['languagePreference'] ?? null,
            merchantNameAlternateLanguage: $optionalData['merchantNameAlternateLanguage'] ?? null,
            merchantCityAlternateLanguage: $optionalData['merchantCityAlternateLanguage'] ?? null,
            upiMerchantAccount: $optionalData['upiMerchantAccount'] ?? null,
            expirationTimestamp: $optionalData['expirationTimestamp'] ?? null,
            merchantCategoryCode: $optionalData['merchantCategoryCode'] ?? null,
        );
    }
}
