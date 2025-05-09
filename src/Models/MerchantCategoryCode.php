<?php

declare(strict_types=1);

namespace KHQR\Models;

use KHQR\Exceptions\KHQRException;
use KHQR\Helpers\EMV;

class MerchantCategoryCode extends TagLengthString
{
    public function __construct(string $tag, ?string $value)
    {
        if ($value === '' || $value == null) {
            throw new KHQRException(KHQRException::MERCHANT_CATEGORY_TAG_REQUIRED);
        } else if (strlen($value) > EMV::INVALID_LENGTH_MERCHANT_CATEGORY_CODE) {
            throw new KHQRException(KHQRException::MERCHANT_CODE_LENGTH_INVALID);
        } else if (!preg_match('/^\d+$/', $value)) {
            throw new KHQRException(KHQRException::INVALID_MERCHANT_CATEGORY_CODE);
        } else {
            $mcc = intval($value);
            if ($mcc < 0 || $mcc > 9999) {
                throw new KHQRException(KHQRException::INVALID_MERCHANT_CATEGORY_CODE);
            }
        }
        parent::__construct($tag, $value);
    }
}
