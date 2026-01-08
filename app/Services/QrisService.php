<?php

namespace App\Services;

class QrisService
{
    public function generateQris(string $staticQris, int $amount): string
    {
        // Keep internal spaces intact (e.g., merchant name), only trim edges.
        $source = trim($staticQris ?: (string) env('SHOP_STATIC_QRIS'));

        if ($source === '') {
            throw new \InvalidArgumentException('Static QRIS string is not configured.');
        }

        if ($amount <= 0) {
            throw new \InvalidArgumentException('QRIS amount must be greater than zero.');
        }

        $base = $this->stripCrc($source);
        $base = $this->stripTag($base, '54');

        $amountValue = $this->formatAmount($amount);
        $payload = $this->injectAmountTag($base, $amountValue);

        $crc = $this->calculateCrc($payload.'6304');

        return $payload.'6304'.$crc;
    }

    public function generateFromEnv(int $amount): string
    {
        $static = (string) (config('qris.static_qris') ?? env('SHOP_STATIC_QRIS'));

        return $this->generateQris($static, $amount);
    }

    protected function stripCrc(string $payload): string
    {
        if (preg_match('/6304[0-9A-Fa-f]{4}$/', $payload)) {
            return substr($payload, 0, -8);
        }

        return $payload;
    }

    protected function stripTag(string $payload, string $tag): string
    {
        $index = 0;
        $length = strlen($payload);

        while ($index + 4 <= $length) {
            $currentTag = substr($payload, $index, 2);
            $valueLength = (int) substr($payload, $index + 2, 2);
            $valueStart = $index + 4;
            $valueEnd = $valueStart + $valueLength;

            if ($valueEnd > $length) {
                break;
            }

            if ($currentTag === $tag) {
                return substr($payload, 0, $index).substr($payload, $valueEnd);
            }

            $index = $valueEnd;
        }

        return $payload;
    }

    protected function injectAmountTag(string $payload, string $amountValue): string
    {
        $tag = '54'.sprintf('%02d', strlen($amountValue)).$amountValue;

        $currencyPosition = $this->findTagPosition($payload, '53');
        if ($currencyPosition !== null) {
            return substr($payload, 0, $currencyPosition['end']).$tag.substr($payload, $currencyPosition['end']);
        }

        return $payload.$tag;
    }

    /**
     * @return array{start: int, end: int}|null
     */
    protected function findTagPosition(string $payload, string $tag): ?array
    {
        $index = 0;
        $length = strlen($payload);

        while ($index + 4 <= $length) {
            $currentTag = substr($payload, $index, 2);
            $valueLength = (int) substr($payload, $index + 2, 2);
            $valueStart = $index + 4;
            $valueEnd = $valueStart + $valueLength;

            if ($valueEnd > $length) {
                break;
            }

            if ($currentTag === $tag) {
                return [
                    'start' => $index,
                    'end' => $valueEnd,
                ];
            }

            $index = $valueEnd;
        }

        return null;
    }

    protected function formatAmount(int $amount): string
    {
        // EMVCo expects 2 fractional digits. Keep .00 for whole numbers.
        return number_format($amount, 2, '.', '');
    }

    protected function calculateCrc(string $data): string
    {
        $crc = $this->crc16Ccitt($data);

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    protected function crc16Ccitt(string $data): int
    {
        $crc = 0xFFFF;
        $polynomial = 0x1021;

        foreach (str_split($data) as $char) {
            $crc ^= (ord($char) << 8);

            for ($i = 0; $i < 8; $i++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return $crc & 0xFFFF;
    }
}
