<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PragmaRX\Google2FA\Google2FA;

class twoface extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:two-face';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $google2fa = new Google2FA();
        $secretKey = $google2fa->generateSecretKey();
        $this->info('the google 2fa key is:');
        $this->line($secretKey);
    }
}
