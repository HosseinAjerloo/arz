<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Models\VouchersBank;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use AyubIRZ\PerfectMoneyAPI\PerfectMoneyAPI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VoucherBankArrangementJob implements ShouldQueue,ShouldBeUnique
{
    use Queueable;

    public $timeout = 0;

    public function uniqueId()
    {
        return 'VoucherBankArrangement';
    }
    public $uniqueFor = 300;
    public $uniqueUntilFailure = true;

    protected $numberOFVouchers = [
        0.25 => 500,
        0.5  => 500,
        1.0  => 500,
        2.0  => 500,
        2.5  => 500,
        3.0  => 500,
        3.5  => 500,
        4.0  => 200,
        5.0  => 200,
        6.0  => 200,
        10.0 => 200,
    ];

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue('VoucherBankArrangementJob');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        try {
            foreach ($this->numberOFVouchers as $amount => $numberOFVoucher) {
                $getNewVoucherInDatabaseTable = VouchersBank::where('status', 'new')->where("amount", $amount)->count();

                $numberOfGenerate = $numberOFVoucher - $getNewVoucherInDatabaseTable;
                if ($numberOfGenerate > 0) {
                    for ($i = 0; $i < $numberOfGenerate; $i++) {
                        $token = 'USD-' . rand(1, 9) . Str::random(3) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4);
                        $token = strtoupper($token);
                        $voucher=VouchersBank::create([
                            'serial' => $token,
                            'code' => $token,
                            'amount' => $amount,
                            'status' => 'new',
                            'description' => 'ایجاد ووچر به صورت اتوماتیک'
                        ]);
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
