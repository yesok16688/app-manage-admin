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

            $method = $this->ask('decrpyt[d] or encrypt[e]?', 'e');
            $rsaUtil = new RsaUtil();
            if(empty($data)) {
                $this->error('nothing to do');
            } else if($method == 'e') {
                $privateKey  = openssl_pkey_get_private(file_get_contents('C:\data\www\app-manage\rsa\private.key.pem'));
                $result = $rsaUtil->encrypt($data, $privateKey);
                $this->info('here is the result:');
                $this->line($result);
            } else if ($method == 'd') {
                $publicKey = openssl_pkey_get_public(file_get_contents('C:\data\www\app-manage\rsa\public.key.pem'));
                $this->info('the decrypt result is:');
                $this->line($rsaUtil->decrypt($data, $publicKey));
            }

            if ($this->confirm('Do you wish to exit?', false)) {
                return;
            }
        }
    }
}
