<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Manual Payment Details
    |--------------------------------------------------------------------------
    |
    | Display details for manual payment methods (bank transfer / remittance).
    |
    */

    'banks' => [
        [
            'bank_name' => 'BDO',
            'account_name' => 'Mickhail Francisco',
            'account_number' => '0040-5037-1173',
            'logo_path' => 'images/banks/logos/bdo.svg',
            'qr_path' => 'images/banks/qr/bdo_qr.jpg',
        ],
        [
            'bank_name' => 'BPI',
            'account_name' => 'Mickhail Francisco',
            'account_number' => '4313-0376-01',
            'logo_path' => 'images/banks/logos/bpi.svg',
            'qr_path' => 'images/banks/qr/bpi_qr.jpeg',
        ],
        [
            'bank_name' => 'ChinaBank',
            'account_name' => 'Mickhail Francisco',
            'account_number' => '1209-0200-5418',
            'logo_path' => 'images/banks/logos/Chinabank_2024.svg',
            'qr_path' => 'images/banks/qr/chinabank_qr.jpg',
        ],
    ],

    'remittance' => [
        'receiver_name' => 'Marie Zharina M. Francisco',
        'contact_number' => '09973580654',
    ],
];
