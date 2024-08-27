<?php

namespace App\Console\Commands;

use App\Models\SubRegion;
use App\Utils\CryptUtils\RsaUtil;
use Illuminate\Console\Command;

class Rsa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rsa';

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
        while (1) {
            $data = $this->ask('Please input your data:');
            if(empty($data)) {
                $this->error('nothing to do');
            } else {
                $privateKey  = openssl_pkey_get_private(file_get_contents('C:\data\www\app-manage\rsa\private.key.pem'));
                $publicKey = openssl_pkey_get_public(file_get_contents('C:\data\www\app-manage\rsa\public.key.pem'));
                $rsaUtil = new RsaUtil();
                $result = $rsaUtil->encrypt($data, $privateKey);
                $this->info('here is the result:');
                $this->line($result);

//        if ($this->confirm('Do you wish to decrypt?', false)) {
//            $this->info('the decrypt result is:');
//            $this->line($rsaUtil->decrypt($result, $publicKey));
//        }
            }

            if ($this->confirm('Do you wish to exit?', false)) {
                return;
            }
        }
    }
}
