<?php

return [
    'currency' => 'MGA',

    /*
    |--------------------------------------------------------------------------
    | Méthodes de paiement — Golden Shop Mdg
    |--------------------------------------------------------------------------
    | enabled      : visible au checkout
    | requires_phone : numéro Mobile Money obligatoire (034 / 032 / 033)
    | merchant_number : numéro marchand affiché au client
    | instructions : consignes après sélection / confirmation
    */
    'methods' => [
        'mvola' => [
            'name' => 'MVola',
            'description' => 'Paiement Mobile Money Telma',
            'enabled' => true,
            'requires_phone' => true,
            'merchant_number' => '+261 34 66 234 95',
            'merchant_name' => 'Golden Shop Mdg',
            'instructions' => 'Envoyez le montant exact au numéro MVola ci-dessous. Indiquez votre numéro de commande en référence.',
            'prefixes' => ['034', '038'],
        ],
        'orange_money' => [
            'name' => 'Orange Money',
            'description' => 'Paiement Mobile Money Orange',
            'enabled' => true,
            'requires_phone' => true,
            'merchant_number' => '032 12 345 67',
            'merchant_name' => 'Golden Shop Mdg',
            'instructions' => 'Effectuez un transfert Orange Money vers le numéro indiqué. Conservez le reçu de transaction.',
            'prefixes' => ['032', '037'],
        ],
        'airtel_money' => [
            'name' => 'Airtel Money',
            'description' => 'Paiement Mobile Money Airtel',
            'enabled' => true,
            'requires_phone' => true,
            'merchant_number' => '033 12 345 67',
            'merchant_name' => 'Golden Shop Mdg',
            'instructions' => 'Transférez le montant total via Airtel Money. Mentionnez votre numéro de commande.',
            'prefixes' => ['033'],
        ],
        'cash_on_delivery' => [
            'name' => 'Paiement à la livraison',
            'description' => 'Espèces à la réception de votre commande',
            'enabled' => true,
            'requires_phone' => false,
            'merchant_number' => null,
            'merchant_name' => null,
            'instructions' => 'Préparez le montant exact en espèces. Le livreur ne dispose pas toujours de monnaie.',
            'prefixes' => [],
        ],
        'bank_transfer' => [
            'name' => 'Virement bancaire',
            'description' => 'Transfert vers notre compte bancaire',
            'enabled' => true,
            'requires_phone' => false,
            'merchant_number' => null,
            'merchant_name' => 'Golden Shop Mdg',
            'bank_name' => 'BNI Madagascar',
            'account_number' => '0000 0000 0000 0000 0000',
            'account_holder' => 'Golden Shop Mdg SARL',
            'instructions' => 'Effectuez le virement et envoyez-nous la preuve par email ou WhatsApp avec votre numéro de commande.',
            'prefixes' => [],
        ],
        'card' => [
            'name' => 'Carte bancaire',
            'description' => 'Visa, Mastercard — bientôt disponible',
            'enabled' => false,
            'requires_phone' => false,
            'merchant_number' => null,
            'merchant_name' => null,
            'instructions' => null,
            'prefixes' => [],
        ],
    ],
];
