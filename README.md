# Bakong KHQR PHP

![tests badge](https://github.com/fidele007/bakong-khqr-php/actions/workflows/tests.yml/badge.svg)

This is a complete implementation of the [`bakong-khqr`](https://www.npmjs.com/package/bakong-khqr) npm module, including all the available API calls documented here: https://api-bakong.nbc.gov.kh/document.

> [!TIP]
> We're up-to-date with the bakong-khqr v1.0.20!

## Installation

```shell
composer require fidele007/bakong-khqr-php
```

## Usage

All available methods are exposed through the `BakongKHQR` class:

**Static methods (no token is required):**

- [`generateIndividual(IndividualInfo $individualInfo)`](#generate-khqr-for-an-individual)
- [`generateMerchant(MerchantInfo $merchantInfo)`](#generate-khqr-for-a-merchant)
- [`decode(string $khqrString)`](#decode-khqr)
- [`decodeNonKhqr(string $qr)`](#decode-non-khqr)
- [`verify(string $KHQRString)`](#verify-khqr)
- [`generateDeepLink(string $qr, ?SourceInfo $sourceInfo, bool $isTest = false)`](#api---generate-khqr-with-deep-link)
- [`generateDeepLinkWithUrl(string $url, string $qr, ?SourceInfo $sourceInfo)`](#api---generate-khqr-with-deep-link-by-providing-url)
- [`isExpiredToken(string $token)`](#api---checking-if-a-bakong-api-token-is-expired)
- [`renewToken(string $email, bool $isTest = false)`](#api---renewing-an-expired-bakong-api-token)
- [`checkBakongAccount(string $bakongID, bool $isTest = false)`](#api---check-bakong-account-existence)
- [`checkBakongAccountWithUrl(string $url, string $bakongID)`](#api---check-bakong-account-existence-by-providing-url)

**Non-static methods (token is required):**

- [`checkTransactionByMD5(string $md5, bool $isTest = false)`](#check-transaction-by-md5)
- [`checkTransactionByMD5List(array $md5Array, bool $isTest = false)`](#check-transaction-by-md5-list)
- [`checkTransactionByFullHash(string $fullHash, bool $isTest = false)`](#check-transaction-by-full-hash)
- [`checkTransactionByFullHashList(array $fullHashArrray, bool $isTest = false)`](#check-transaction-by-full-hash-list)
- [`checkTransactionByShortHash(string $shortHash, float $amount, string $currency, bool $isTest = false)`](#check-transaction-by-short-hash)
- [`checkTransactionByInstructionReference(string $ref, bool $isTest = false)`](#check-transaction-by-instruction-reference)
- [`checkTransactionByExternalReference(string $ref, bool $isTest = false)`](#check-transaction-by-external-reference)

### Generate KHQR for an individual

```php
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;

$individualInfo = new IndividualInfo(
    bakongAccountID: 'jonhsmith@nbcq',
    merchantName: 'Jonh Smith',
    merchantCity: 'PHNOM PENH',
    currency: KHQRData::CURRENCY_KHR,
    amount: 500,
    expirationTimestamp: strval(floor(microtime(true) * 1000) + 60 * 1000), // Expire in 1 minute
    merchantCategoryCode: "5999" // optional, default value is 5999
);

var_dump(BakongKHQR::generateIndividual($individualInfo));
```

> [!IMPORTANT]
> Starting from v1.1.0 (the v1.0.18 equivalent of the npm package) The `expirationTimestamp` parameter is required for dynamic KHQR, i.e. KHQR with a transaction amount. The expected format is a timestamp string in **milliseconds**.

Output:

```shell
object(KHQR\Models\KHQRResponse)#16 (2) {
  ["status"]=>
  array(3) {
    ["code"]=>
    int(0)
    ["errorCode"]=>
    NULL
    ["message"]=>
    NULL
  }
  ["data"]=>
  array(2) {
    ["qr"]=>
    string(136) "00020101021229180014jonhsmith@nbcq52045999530311654035005802KH5910Jonh Smith6010PHNOM PENH993400131742250594187011317422506541846304EA38"
    ["md5"]=>
    string(32) "838f6d998f6d700c42391aab5c5be555"
  }
}
```

### Generate KHQR for a merchant

```php
use KHQR\BakongKHQR;
use KHQR\Models\MerchantInfo;

$merchantInfo = new MerchantInfo(
    bakongAccountID: 'jonhsmith@nbcq',
    merchantName: 'Jonh Smith',
    merchantCity: 'Siem Reap',
    merchantID: '123456',
    acquiringBank: 'Dev Bank',
    mobileNumber: '85512345678',
    expirationTimestamp: strval(floor(microtime(true) * 1000) + (2 * 60 * 1000)), // Expire in 1 minute
    merchantCategoryCode: "5999" // optional, default value is 5999
);

var_dump(BakongKHQR::generateMerchant($merchantInfo));
```

Output:

```shell
object(KHQR\Models\KHQRResponse)#19 (2) {
  ["status"]=>
  array(3) {
    ["code"]=>
    int(0)
    ["errorCode"]=>
    NULL
    ["message"]=>
    NULL
  }
  ["data"]=>
  array(2) {
    ["qr"]=>
    string(152) "00020101021130400014jonhsmith@nbcq01061234560208Dev Bank5204599953031165802KH5910Jonh Smith6009Siem Reap621502118551234567863048C75"
    ["md5"]=>
    string(32) "3a1d545dfba97b39817bf43337e621ec"
  }
}
```

### Decode KHQR

```php
$result = BakongKHQR::decode('00020101021229190015john_smith@devb52045999530311654065000.05802KH5910jonh smith6010Phnom Penh62360109#INV-20030313Coffee Klaing0702#299170013161302797275763049ACF');

var_dump($result);
```

Output:

```shell
object(KHQR\Models\KHQRResponse)#19 (2) {
  ["status"]=>
  array(3) {
    ["code"]=>
    int(0)
    ["errorCode"]=>
    NULL
    ["message"]=>
    NULL
  }
  ["data"]=>
  array(24) {
    ["merchantType"]=>
    string(2) "29"
    ["bakongAccountID"]=>
    string(15) "john_smith@devb"
    ["accountInformation"]=>
    NULL
    ["merchantID"]=>
    NULL
    ["acquiringBank"]=>
    NULL
    ["billNumber"]=>
    string(9) "#INV-2003"
    ["mobileNumber"]=>
    NULL
    ["storeLabel"]=>
    string(13) "Coffee Klaing"
    ["terminalLabel"]=>
    string(2) "#2"
    ["purposeOfTransaction"]=>
    NULL
    ["languagePreference"]=>
    NULL
    ["merchantNameAlternateLanguage"]=>
    NULL
    ["merchantCityAlternateLanguage"]=>
    NULL
    ["payloadFormatIndicator"]=>
    string(2) "01"
    ["pointofInitiationMethod"]=>
    string(2) "12"
    ["unionPayMerchant"]=>
    NULL
    ["merchantCategoryCode"]=>
    string(4) "5999"
    ["transactionCurrency"]=>
    string(3) "116"
    ["transactionAmount"]=>
    string(6) "5000.0"
    ["countryCode"]=>
    string(2) "KH"
    ["merchantName"]=>
    string(10) "jonh smith"
    ["merchantCity"]=>
    string(10) "Phnom Penh"
    ["timestamp"]=>
    string(17) "00131613027972757"
    ["crc"]=>
    string(4) "9ACF"
  }
}
```

### Decode Non-KHQR

```php
$result = BakongKHQR::decodeNonKhqr('00020101021229190015john_smith@devb52045999530311654065000.05802KH5910jonh smith6010Phnom Penh62360109#INV-20030313Coffee Klaing0702#299170013161302797275763049ACF');

var_dump($result);
```

Output:

```shell
object(KHQR\Models\KHQRResponse)#2 (2) {
  ["status"]=>
  array(3) {
    ["code"]=>
    int(0)
    ["errorCode"]=>
    NULL
    ["message"]=>
    NULL
  }
  ["data"]=>
  array(12) {
    ["00"]=>
    string(2) "01"
    ["01"]=>
    string(2) "12"
    [29]=>
    array(1) {
      ["00"]=>
      string(15) "john_smith@devb"
    }
    [52]=>
    string(4) "5999"
    [53]=>
    string(3) "116"
    [54]=>
    string(6) "5000.0"
    [58]=>
    string(2) "KH"
    [59]=>
    string(10) "jonh smith"
    [60]=>
    string(10) "Phnom Penh"
    [62]=>
    array(3) {
      ["01"]=>
      string(9) "#INV-2003"
      ["03"]=>
      string(13) "Coffee Klaing"
      ["07"]=>
      string(2) "#2"
    }
    [99]=>
    array(1) {
      ["00"]=>
      string(13) "1613027972757"
    }
    [63]=>
    string(4) "9ACF"
  }
}
```

### Verify KHQR

```php
$result = BakongKHQR::verify('00020101021229180014jonhsmith@nbcq520459995303116540750000.05802KH5910Jonh Smith6010Phnom Penh62150211855123456789917001316257134678276304A96B');

var_dump($result);
```

Output:

```shell
object(KHQR\Models\CRCValidation)#16 (1) {
  ["isValid"]=>
  bool(true)
}
```

### API - Generate KHQR with Deep Link

```php
$sourceInfo = new SourceInfo(
    appIconUrl: 'https://bakong.nbc.gov.kh/images/logo.svg',
    appName: 'Bakong',
    appDeepLinkCallback: 'https://bakong.nbc.gov.kh'
);
$result = BakongKHQR::generateDeepLink('00020101021229190015john_smith@devb5204599953038405405100.05802KH5910John Smith6010Phnom Penh6304BF30', $sourceInfo);

var_dump($result);
```

Output:

```shell
object(KHQR\Models\KHQRResponse)#5 (2) {
  ["status"]=>
  array(3) {
    ["code"]=>
    int(0)
    ["errorCode"]=>
    NULL
    ["message"]=>
    NULL
  }
  ["data"]=>
  object(KHQR\Models\KHQRDeepLinkData)#17 (1) {
    ["shortLink"]=>
    string(42) "https://bakong.page.link/yhDhTSdPWschTBdb8"
  }
}
```

### API - Generate KHQR with Deep Link by Providing URL

Bakong API has two available URLs:

- Production URL: https://api-bakong.nbc.gov.kh
- SIT URL (for testing): https://sit-api-bakong.nbc.gov.kh

So the deep link API URLs can be different according to the environment you want to use:

- Production URL: https://api-bakong.nbc.gov.kh/v1/generate_deeplink_by_qr
- SIT URL (for testing): https://sit-api-bakong.nbc.gov.kh/v1/generate_deeplink_by_qr

This method can be used with any of the above URLs. For example:

```php
$sourceInfo = new SourceInfo(
    appIconUrl: 'https://bakong.nbc.gov.kh/images/logo.svg',
    appName: 'Bakong',
    appDeepLinkCallback: 'https://bakong.nbc.gov.kh'
);
$result = BakongKHQR::generateDeepLinkWithUrl('https://sit-api-bakong.nbc.gov.kh/v1/generate_deeplink_by_qr', '00020101021229190015john_smith@devb5204599953038405405100.05802KH5910John Smith6010Phnom Penh993400131742249567316011317422496273166304B418', $sourceInfo);

var_dump($result);
```

Output:

```shell
object(KHQR\Models\KHQRResponse)#8 (2) {
  ["status"]=>
  array(3) {
    ["code"]=>
    int(0)
    ["errorCode"]=>
    NULL
    ["message"]=>
    NULL
  }
  ["data"]=>
  object(KHQR\Models\KHQRDeepLinkData)#6 (1) {
    ["shortLink"]=>
    string(45) "https://bakongsit.page.link/wNA2pzfnMCZYVnJr6"
  }
}
```

### API - Check Bakong Account Existence

```php
$result = BakongKHQR::checkBakongAccount('dave@devb');

var_dump($result);
```

Output:

```shell
object(KHQR\Models\KHQRResponse)#16 (2) {
  ["status"]=>
  array(3) {
    ["code"]=>
    int(0)
    ["errorCode"]=>
    NULL
    ["message"]=>
    NULL
  }
  ["data"]=>
  array(1) {
    ["bakongAccountExists"]=>
    bool(false)
  }
}
```

### API - Check Bakong Account Existence by Providing URL

Just like the [Deep Link API](#api---generate-khqr-with-deep-link-by-providing-url), you can use any of the available Bakong API URLs to check for a Bakong account existence:

```php
$result = BakongKHQR::checkBakongAccountWithUrl('https://sit-api-bakong.nbc.gov.kh', 'dave@devb');

var_dump($result);
```

Output:

```shell
object(KHQR\Models\KHQRResponse)#16 (2) {
  ["status"]=>
  array(3) {
    ["code"]=>
    int(0)
    ["errorCode"]=>
    NULL
    ["message"]=>
    NULL
  }
  ["data"]=>
  array(1) {
    ["bakongAccountExists"]=>
    bool(false)
  }
}
```

### API - Checking if a Bakong API Token is Expired

```php
$result = BakongKHQR::isExpiredToken('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...');
```

Output:

```shell
bool(true)
```

### API - Renewing an expired Bakong API Token

If your token has expired, you will get a `KHQRException` when calling authorized Bakong API requests:

```shell
object(KHQR\Exceptions\KHQRException)#51 (7) {
  ["message":protected]=>
  string(57) "Unauthorized, not yet requested for token or code invalid"
  ["string":"Exception":private]=>
  string(0) ""
  ["code":protected]=>
  int(6)
  ...
}
```

You can renew your token with the `renewToken` method:

```php
$result = BakongKHQR::renewToken('john.smith@gmail.com');

var_dump($result);
```

Output:

```shell
array(4) {
  ["responseCode"]=>
  int(0)
  ["responseMessage"]=>
  string(21) "Token has been issued"
  ["errorCode"]=>
  NULL
  ["data"]=>
  array(1) {
    ["token"]=>
    string(172) "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

In case your email is not registered:

```shell
array(4) {
  ["responseCode"]=>
  int(1)
  ["responseMessage"]=>
  string(18) "Not registered yet"
  ["errorCode"]=>
  int(10)
  ["data"]=>
  NULL
}
```

### API - Check Transaction Status

A valid token is required to check transaction status. You can get one by registering on the Bakong website: https://api-bakong.nbc.gov.kh/register. At the moment of writing this README the token has to be renewed every 90 days. Then you can create a `BakongKHQR` instance with the token:

```php
$bakongKhqr = new BakongKHQR('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...');
```

#### Check Transaction by MD5

```php
$response = $bakongKhqr->checkTransactionByMD5('d60f3db96913029a2af979a1662c1e72');
```

#### Check Transaction by MD5 List

```php
$response = $bakongKhqr->checkTransactionByMD5List([
    '0dbe08d3829a8b6b59844e51aa38a4e2',
    '7b0e5c36486d7155eb3ee94997fe9bfb',
    'e12b3ecc4c066405ce05cd8cacab884c',
]);
```

#### Check Transaction by Full Hash

```php
$response = $bakongKhqr->checkTransactionByFullHash('dcd53430d3b3005d9cda36f1fe8dedc3714ccf18f886cf5d090d36fee67ef956');
```

#### Check Transaction by Full Hash List

```php
$response = $bakongKhqr->checkTransactionByFullHashList([
    'f0ae142842181535e678900bc5be1c3bd48d567ced77410a169fb672792968c8',
    'd3b42e35d618a42b7506a79564083e6e91d5383b63f8aa2cf2ca7e65d55ec858',
    '9036688e95cb3d1b621a9a989ebe64629d8c118654cfbc47f4d4991d72fc3b44',
]);
```

#### Check Transaction by Short Hash

```php
$response = $bakongKhqr->checkTransactionByShortHash('8465d722', 1.0, 'USD');
```

#### Check Transaction by Instruction Reference

```php
$response = $bakongKhqr->checkTransactionByInstructionReference('00001234');
```

#### Check Transaction by External Reference

```php
$response = $bakongKhqr->checkTransactionByExternalReference('DEV123456ZTH');
```

## Testing

To run the tests:

```shell
composer run test
```

## Static Code Analysis

To run static code analysis:

```shell
composer run stan
```

## Code Style

To run the code style fixer:

```shell
composer run pint
```

## Code Refactoring

```shell
composer run refactor
```

## Troubleshooting

### PHP curl does not work correctly on Windows

It may be due to the fact that your PHP configuration does not include a valid certificate file. This can be confirmed by disabling the SSL verification:

```php
// ignore the SSL certificate
curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER,false);
```

or by checking with `phpinfo()`:

```shell
curl.cainfo => no value => no value
```

If that's true, the certificate file can be downloaded from https://curl.se/ca/cacert.pem, and include in `php.ini` file:

```properties
curl.cainfo = "C:\Users\force\cacert.pem"
```

After that, restart your services or your terminal and retest.
