<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Models\VouchersBank;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use AyubIRZ\PerfectMoneyAPI\PerfectMoneyAPI;
use Illuminate\Support\Facades\Log;

class VoucherBankArrangementJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 0;

    protected $numberOFVouchers =
        [
            1 => 10,
            2 => 10,
            3 => 5,
            4 => 5,
            5 => 7,
            6 => 5,
            7 => 2,
            8 => 2,
            9 => 2,
            10 => 4,
            20 => 4,
            16 => 1,
            17 => 1,
            18 => 1,
            25 => 1

        ];

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        try {
            $PM = new PerfectMoneyAPI(env('PM_ACCOUNT_ID'), env('PM_PASS'));
            foreach ($this->numberOFVouchers as $amount => $numberOFVoucher) {
                $getNewVoucherInDatabaseTable = VouchersBank::where('status', 'new')->where("amount", $amount)->count();

                $numberOfGenerate = $numberOFVoucher - $getNewVoucherInDatabaseTable;
                if ($numberOfGenerate > 0) {
                    for ($i = 0; $i < $numberOfGenerate; $i++) {
                        $PMeVoucher = $PM->createEV(env('PAYER_ACCOUNT'), $amount);
                        if (is_array($PMeVoucher) and isset($PMeVoucher['VOUCHER_NUM']) and isset($PMeVoucher['VOUCHER_CODE'])) {

                            VouchersBank::create(
                                [
                                    'serial' => $PMeVoucher['VOUCHER_NUM'],
                                    'code' => $PMeVoucher['VOUCHER_CODE'],
                                    'amount' => $amount,
                                    'status' => 'new',
                                    'description' => 'ایجاد ووچر به صورت اتوماتیک'
                                ]
                            );
                        }else{
                            Log::emergency(json_encode($PMeVoucher));

                        }

                    }
                }

            }
        } catch (\Exception $e) {
            Log::emergency(PHP_EOL.$e->getMessage().PHP_EOL);
            Ticket::create([
                'subject'=>'خرابی در پرفکت مانی',
                'user_id'=>1,
                'status'=>'closed'
            ]);
        }
    }
}
