<?php

namespace SlickPay\Transfer;

use Illuminate\Support\ServiceProvider;

class TransferServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/transfer.php' => config_path('transfer.php'),
        ], 'transfer-config');
    }

    public function register()
    {
        //
    }
}