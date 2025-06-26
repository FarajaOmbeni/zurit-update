<?php

class Pesapal {
    public function __construct(
        private string $baseUrl    = 'https://cybqa.pesapal.com/pesapalv3',
        private ?string $token     = null,
    ) {
        if (config('pesapal.PESAPAL_ENVIRONMENT') === 'live') {
            $this->baseUrl  = 'https://pay.pesapal.com/v3';
        }
    }

    public function getAccessToken(){
        
    }
}