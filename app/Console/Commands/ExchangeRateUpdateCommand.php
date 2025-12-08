<?php

namespace App\Console\Commands;

use App\Models\Doller;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ExchangeRateUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange-rate-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the exchange rate through the Nobitex network.';

    /**
     * Execute the console command.
     */
    public function handle()
    {


          $dollar = Doller::first();

        $response=\Illuminate\Support\Facades\Http::get('https://api.tetherland.com/currencies');
        $body=$response->json();
        if ($response->status()==200 and isset($body['status']) and $body['status']==200)
        {
            $price=$body['data']['currencies']['USDT']['price'];
            $dollar->update(['amount_to_rials' => $price."0"]);

        }

    }
}
