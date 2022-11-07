<?php

namespace SlickPay\Transfer;

use Illuminate\Support\Facades\Validator;

/**
 * Transfer
 *
 * @author     Slick-Pay <contact@slick-pay.com>
 */
class Transfer
{
    /**
     * Calculate transfer commission
     *
     * @param  float $amount  Request params
     * @return array
     */
    public static function calculateCommission(float $amount): array
    {
        $public_key = config('transfer.public_key', null);

        if (empty($public_key)) return [
            'success'  => 0,
            'error'    => 1,
            'messages' => [
                __("You have to set a public key, from your config file.")
            ],
        ];

        if (!is_numeric($amount) || $amount <= 100) return [
            'success'  => 0,
            'error'    => 1,
            'messages' => [
                __("The amount must be a valid number.")
            ],
        ];

        try {

            $cURL = curl_init();

            $domain_name = config('transfer.sandbox', true)
                ? "dev.transfer.slick-pay.com"
                : "transfer.slick-pay.com";

            curl_setopt($cURL, CURLOPT_URL, "https://{$domain_name}/api/user/transfer/commission");
            curl_setopt($cURL, CURLOPT_POSTFIELDS, [
                'amount' => $amount
            ]);
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
            'response' => [
                'amount' => $result['amount'],
            ]
        ];
    }

    /**
     * Initiate a new payment
     *
     * @param  array $params  Request params
     * @return array
     */
    public static function createPayment(array $params): array
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
            'transfer_id'   => 'nullable|numeric|min:1',
            'amount'        => 'required|numeric|min:100',
            'receiver_uuid' => 'required_without:rib|string',
            'rib'           => 'nullable|numeric|digits:20',
            'phone'         => 'nullable',
            'email'         => 'nullable|email',
            'title'         => 'required_with:rib|string|min:2|max:225',
            'fname'         => 'required_with:rib|string|min:2|max:225',
            'lname'         => 'required_with:rib|string|min:2|max:225',
            'address'       => 'required_with:rib|string|min:5|max:225',
            'returnUrl'     => 'nullable|url',
            'type'          => 'required|in:external,internal'
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

            curl_setopt($cURL, CURLOPT_URL, "https://{$domain_name}/api/user/transfer");
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
            'response' => [
                'transferId'  => $result['transfer_id'],
                'redirectUrl' => $result['url'],
            ]
        ];
    }

    /**
     * Check a payment status with it transfer_id
     *
     * @param  integer $transfer_id  The payment transfer_id provided as a return of the initiate function
     * @param  string  $rib          The merchant bank account ID
     * @return array
     */
    public static function paymentStatus(int $transfer_id): array
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

            curl_setopt($cURL, CURLOPT_URL, "https://{$domain_name}/api/user/transfer/{$transfer_id}");
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

            if (!empty($result['msg']) && $result['msg'] == 'draft') return [
                'success' => 1,
                'error'   => 0,
                'status'  => "draft",
            ];

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
            'status'   => "completed",
            'response' => [
                'date'         => $result['date'],
                'amount'       => $result['amount'],
                'orderId'      => $result['orderId'],
                'orderNumber'  => $result['orderNumber'],
                'approvalCode' => $result['approvalCode'],
                'pdf'          => $result['pdf'],
                'respCode'     => $result['respCode_desc'],
            ]
        ];
    }

    /**
     * Get user payment history
     *
     * @param  integer  $offset  Pagination offset
     * @return array
     */
    public static function paymentHistory(int $offset = 0): array
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

            curl_setopt($cURL, CURLOPT_URL, "https://{$domain_name}/api/user/transfer?offset={$offset}");
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