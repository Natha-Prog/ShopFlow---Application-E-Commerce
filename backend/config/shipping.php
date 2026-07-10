<?php

return [
    'currency' => 'MGA',

    'shop' => [
        'latitude' => -18.8792,
        'longitude' => 47.5079,
        'name' => 'Golden Shop Mdg',
        'city' => 'Antananarivo',
    ],

    'delivery' => [
        'base_fee' => 5000,
        'per_km' => 500,
        'max_radius_km' => 25,
    ],

    'antananarivo_aliases' => [
        'antananarivo',
        'tana',
        'tananarive',
        'analamanga',
    ],

    'excluded_shipping_regions' => [
        'analamanga',
        'antananarivo',
    ],

    /*
    | Tarifs fixes expédition colis par région (hors Analamanga).
    | Clé = identifiant normalisé, name = libellé affiché.
    */
    'regions' => [
        'diana' => ['name' => 'Diana', 'fee' => 25000],
        'sava' => ['name' => 'Sava', 'fee' => 28000],
        'itasy' => ['name' => 'Itasy', 'fee' => 12000],
        'vakinankaratra' => ['name' => 'Vakinankaratra', 'fee' => 15000],
        'bongolava' => ['name' => 'Bongolava', 'fee' => 18000],
        'sofia' => ['name' => 'Sofia', 'fee' => 30000],
        'boeny' => ['name' => 'Boeny', 'fee' => 22000],
        'betsiboka' => ['name' => 'Betsiboka', 'fee' => 24000],
        'melaky' => ['name' => 'Melaky', 'fee' => 35000],
        'alaotra_mangoro' => ['name' => 'Alaotra-Mangoro', 'fee' => 20000],
        'atsinanana' => ['name' => 'Atsinanana', 'fee' => 18000],
        'analanjirofo' => ['name' => 'Analanjirofo', 'fee' => 22000],
        'amoron_i_mania' => ['name' => "Amoron'i Mania", 'fee' => 20000],
        'haute_matsiatra' => ['name' => 'Haute Matsiatra', 'fee' => 18000],
        'vatovavy' => ['name' => 'Vatovavy', 'fee' => 20000],
        'atsimo_atsinanana' => ['name' => 'Atsimo-Atsinanana', 'fee' => 22000],
        'ihorombe' => ['name' => 'Ihorombe', 'fee' => 25000],
        'menabe' => ['name' => 'Menabe', 'fee' => 28000],
        'atsimo_andrefana' => ['name' => 'Atsimo-Andrefana', 'fee' => 32000],
        'androy' => ['name' => 'Androy', 'fee' => 38000],
        'anosy' => ['name' => 'Anosy', 'fee' => 35000],
    ],
];
