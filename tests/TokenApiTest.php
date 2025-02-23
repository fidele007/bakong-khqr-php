<?php

declare(strict_types=1);

namespace KHQR\Tests;

use KHQR\BakongKHQR;
use KHQR\Exceptions\KHQRException;
use PHPUnit\Framework\TestCase;

class TokenApiTest extends TestCase
{
    private const BACKOFF_FACTOR = 2;

    private const RETRY_ATTEMPTS = 3;

    public function test_renew_token_unregistered_email(): void
    {
        $response = BakongKHQR::renewToken('nonexistent-account@gmail.com');
        $this->assertEquals(10, $response['errorCode'], 'Unregistered email');
    }

    public function test_renew_token_registered_email(): void
    {
        for ($i = 0; $i < self::RETRY_ATTEMPTS; $i++) {
            try {
                $response = BakongKHQR::renewToken('fidele.fr@hotmail.com');
                $this->assertEquals($response['responseCode'], 0, 'Token has been issued');
                if (! isset($response['data']) || ! is_array($response['data']) || ! isset($response['data']['token'])) {
                    $this->fail('[test_renew_token_registered_email] Unexpected data structure: '.json_encode($response));
                }
                $this->assertNotEmpty($response['data']['token'], 'Renewed token string is not empty');
            } catch (KHQRException $e) {
                if ($e->getCode() === 503 || $e->getCode() === 504 || $e->getCode() === 13) {
                    // Unstable server or server cannot be reached; retry again 3 times
                    $waitTime = self::BACKOFF_FACTOR ** $i;
                    sleep($waitTime);

                    continue;
                }

                $this->fail('[test_renew_token_registered_email] Unexpected exception occurred: '.$e->getCode().' - '.$e->getMessage());
            }
        }
    }
}
