<?php

class CurrencyService
{
    public function convert($amount, $from, $to)
    {
        if (
            !is_scalar($amount)
            || is_bool($amount)
            || !is_numeric($amount)
            || !is_finite((float) $amount)
            || (float) $amount < 0
            || (float) $amount > 99999999.99
        ) {
            throw new InvalidArgumentException(
                'Amount must be between 0 and 99999999.99.'
            );
        }

        if (!is_string($from) || !is_string($to)) {
            throw new InvalidArgumentException(
                'Currency codes must be strings.'
            );
        }

        $from = strtoupper(trim($from));
        $to = strtoupper(trim($to));

        $allowedCurrencies = ['EUR', 'USD', 'GBP'];

        if (
            !in_array($from, $allowedCurrencies, true)
            || !in_array($to, $allowedCurrencies, true)
        ) {
            throw new InvalidArgumentException(
                'Supported currencies are EUR, USD and GBP.'
            );
        }

        if ($from === $to) {
            return [
                'amount' => (float) $amount,
                'from' => $from,
                'to' => $to,
                'rate' => 1,
                'converted_amount' => round((float) $amount, 2),
                'date' => null
            ];
        }

        try {
            $url = 'https://api.frankfurter.dev/v2/rate/'
                . strtolower($from)
                . '/'
                . strtolower($to);

            $curl = curl_init($url);

            if ($curl === false) {
                throw new RuntimeException(
                    'Unable to initialize the API request.'
                );
            }

            try {
                curl_setopt_array($curl, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CONNECTTIMEOUT => 3,
                    CURLOPT_TIMEOUT => 8,
                    CURLOPT_HTTPHEADER => [
                        'Accept: application/json'
                    ]
                ]);

                $response = curl_exec($curl);

                $status = curl_getinfo(
                    $curl,
                    CURLINFO_HTTP_CODE
                );

                if ($response === false) {
                    throw new RuntimeException(
                        'Currency request failed: ' . curl_error($curl)
                    );
                }

                if ($status !== 200) {
                    throw new RuntimeException(
                        'Currency API returned HTTP ' . $status
                    );
                }
            } finally {
                curl_close($curl);
            }

            $data = json_decode(
                $response,
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            if (
                !is_array($data)
                || !isset($data['rate'], $data['date'])
                || !is_numeric($data['rate'])
                || !is_finite((float) $data['rate'])
                || (float) $data['rate'] <= 0
                || !is_string($data['date'])
            ) {
                throw new RuntimeException(
                    'Currency API returned an invalid response.'
                );
            }

            return [
                'amount' => (float) $amount,
                'from' => $from,
                'to' => $to,
                'rate' => (float) $data['rate'],
                'converted_amount' => round(
                    (float) $amount * (float) $data['rate'],
                    2
                ),
                'date' => $data['date']
            ];

        } catch (Throwable $exception) {
            $this->logError($exception->getMessage());

            throw new RuntimeException(
                'Currency conversion is temporarily unavailable.'
            );
        }
    }

    private function logError($message)
    {
        $directory = __DIR__ . '/../../logs';

        if (!is_dir($directory)) {
            if (!@mkdir($directory, 0775, true) && !is_dir($directory)) {
                error_log('Unable to create the application log directory.');
                return;
            }
        }

        $message = str_replace(["\r", "\n"], ' ', $message);

        $line = '[' . date('Y-m-d H:i:s') . '] ERROR '
            . $message
            . PHP_EOL;

        if (
            @file_put_contents(
                $directory . '/error.log',
                $line,
                FILE_APPEND | LOCK_EX
            ) === false
        ) {
            error_log($line);
        }
    }
}