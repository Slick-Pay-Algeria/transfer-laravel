<?php

namespace SlickPay\Transfer;

use Illuminate\Support\Facades\Validator;

/**
 * BankAccount
 *
 * @author     Slick-Pay <contact@slick-pay.com>
 */
class BankAccount
{
    /**
     * Create a new user bank account
     *
     * @param  array $params  Request params
     * @return array
     */
    public static function create(array $params): array
    {
        $public_key = config('transfer.public_key', null);

        if (empty($public_key)) return [
            'success'  => 0,
            'error'    => 1,
            'messages' => [
                __("You have to set a public key, from your config file.")
            ],
        ];

        $validator = Validator::make($params, [
            'title'     => 'required|string',
            'fname'     => 'required|string|min:2|max:255',
            'lname'     => 'required|string|min:2|max:255',
            'rib'       => 'required|numeric|digits:20',
            'address'   => 'required|string|min:5|max:255',

        ]);

        if ($validator->fails()) return [
            'success'  => 0,
            'error'    => 1,
            'messages' => $validator->errors()->all(),
        ];

        try {

            $cURL = curl_init();

            $domain_name = config('transfer.sandbox', true)
                ? "dev.transfer.slick-pay.com"
                : "transfer.slick-pay.com";

            curl_setopt($cURL, CURLOPT_URL, "https://{$domain_name}/api/user/bank-account");
            curl_setopt($cURL, CURLOPT_POSTFIELDS, $params);
            curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURL, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($cURL, CURLOPT_TIMEOUT, 20);
            curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer {$public_key}",
            ));

            $result = curl_exec($cURL);

            $status = curl_getinfo($cURL, CURLINFO_HTTP_CODE);

            curl_close($cURL);

            $result = json_decode($result, true);

            if ($status < 200 || $status >= 300) return [
                'success'  => 0,
                'error'    => 1,
                'messages' => [
                    __("Error ! Please, try later")
                ],
            ];

            elseif (isset($result['errors']) && boolval($result['errors']) == true) return [
                'success'  => 0,
                'error'    => 1,
                'messages' => [
                    !empty($result['message']) ? $result['message'] : $result['msg']
                ],
            ];

        } catch (\Exception $e) {

            return [
                'success'  => 0,
                'error'    => 1,
                'messages' => [
                    $e->getMessage()
                ],
            ];
        }

        return [
            'success'  => 1,
            'error'    => 0,
            'response' => $result['data']
        ];
    }

    /**
     * Update a existing user bank account
     *
     * @param  string $uuid   User bank account
     * @param  array $params  Request params
     * @return array
     */
    public static function update(string $uuid, array $params): array
    {
        $public_key = config('transfer.public_key', null);

        if (empty($public_key)) return [
            'success'  => 0,
            'error'    => 1,
            'messages' => [
                __("You have to set a public key, from your config file.")
            ],
        ];

        $validator = Validator::make($params, [
            'title'     => 'required|string',
            'fname'     => 'required|string|min:2|max:255',
            'lname'     => 'required|string|min:2|max:255',
            'rib'       => 'required|numeric|digits:20',
            'address'   => 'required|string|min:5|max:255',

        ]);

        if ($validator->fails()) return [
            'success'  => 0,
            'error'    => 1,
            'messages' => $validator->errors()->all(),
        ];

        try {

            $cURL = curl_init();

            $domain_name = config('transfer.sandbox', true)
                ? "dev.transfer.slick-pay.com"
                : "transfer.slick-pay.com";

            curl_setopt($cURL, CURLOPT_URL, "https://{$domain_name}/api/user/bank-account/{$uuid}");
            curl_setopt($cURL, CURLOPT_POSTFIELDS, $params);
            curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURL, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($cURL, CURLOPT_TIMEOUT, 20);
            curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer {$public_key}",
            ));

            $result = curl_exec($cURL);

            $status = curl_getinfo($cURL, CURLINFO_HTTP_CODE);

            curl_close($cURL);

            $result = json_decode($result, true);

            if ($status < 200 || $status >= 300) return [
                'success'  => 0,
                'error'    => 1,
                'messages' => [
                    __("Error ! Please, try later")
                ],
            ];

            elseif (isset($result['errors']) && boolval($result['errors']) == true) return [
                'success'  => 0,
                'error'    => 1,
                'messages' => [
                    !empty($result['message']) ? $result['message'] : $result['msg']
                ],
            ];

        } catch (\Exception $e) {

            return [
                'success'  => 0,
                'error'    => 1,
                'messages' => [
                    $e->getMessage()
                ],
            ];
        }

        return [
            'success'  => 1,
            'error'    => 0,
            'response' => $result['data']
        ];
    }

    /**
     * Retreive user bank accounts list
     *
     * @param  integer $offset  Pagination offset
     * @return array
     */
    public static function list(int $offset = 0): array
    {
        $public_key = config('transfer.public_key', null);

        if (empty($public_key)) return [
            'success'  => 0,
            'error'    => 1,
            'messages' => [
                __("You have to set a public key, from your config file.")
            ],
        ];

        try {

            $cURL = curl_init();

            $domain_name = config('transfer.sandbox', true)
                ? "dev.transfer.slick-pay.com"
                : "transfer.slick-pay.com";

            curl_setopt($cURL, CURLOPT_URL, "https://{$domain_name}/api/user/bank-account?offset={$offset}");
            curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURL, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($cURL, CURLOPT_TIMEOUT, 20);
            curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer {$public_key}",
            ));

            $result = curl_exec($cURL);

            $status = curl_getinfo($cURL, CURLINFO_HTTP_CODE);

            curl_close($cURL);

            $result = json_decode($result, true);

            if ($status < 200 || $status >= 300) return [
                'success'  => 0,
                'error'    => 1,
                'messages' => [
                    __("Error ! Please, try later")
                ],
            ];

            elseif (!empty($result['errors'])) return [
                'success'  => 0,
                'error'    => 1,
                'messages' => [
                    !empty($result['message']) ? $result['message'] : $result['msg']
                ],
            ];

        } catch (\Exception $e) {

            return [
                'success' => 0,
                'error'   => 1,
                'messages' => [
                    $e->getMessage()
                ],
            ];
        }

        return [
            'success'  => 1,
            'error'    => 0,
            'response' => $result
        ];
    }
}