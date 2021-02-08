<?php

namespace store;

$DB_tables = [
    'Stores' => [
        'StoreID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'StoreSlug' => 'VARCHAR(16) NOT NULL UNIQUE',
        'Name' => 'VARCHAR(255) NOT NULL UNIQUE',
        'Description' => 'TEXT'
    ],
    'Products' => [
        'ProductID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'StoreID' => 'INT UNSIGNED NOT NULL',
        'ProductSlug' => 'VARCHAR(16) NOT NULL UNIQUE',
        'Name' => 'VARCHAR(255) NOT NULL UNIQUE',
        'Description' => 'TEXT',
        'UnitPriceCents' => 'INT NOT NULL DEFAULT 0',
        'PaymentSystemRef' => 'VARCHAR(255) UNIQUE'
    ],
    'Purchases' => [
      'PurchaseID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
      'StoreID' => 'INT UNSIGNED NOT NULL',
      'PaymentSystemRef' => 'VARCHAR(255) UNIQUE'
    ],
    'PurchaseLineItems' => [
        'PurchaseLineItemID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'PurchaseID' => 'INT UNSIGNED NOT NULL',
        'ProductID' => 'INT UNSIGNED NOT NULL',
        'PriceCents' => 'INT NOT NULL DEFAULT 0',
        'PaymentSystemRef' => 'VARCHAR(255) UNIQUE'
    ]
];

$DB_index = [
    'Stores' => [
        'idx_StoreSlug' => ['StoreSlug'],
    ],
    'Products' => [
        'idx_ProductSlug' => ['ProductSlug'],
        'idx_PaymentSystemRef_on_Products' => ['PaymentSystemRef'],
    ],
    'Purchases' => [
        'idx_PaymentSystemRef_on_Purchases' => ['PaymentSystemRef'],
    ],
    'PurchaseLineItems' => [
        'idx_PaymentSystemRef_on_PurchaseLineItems' => ['PaymentSystemRef']
    ]
];

$DB_foreignKeys = [
    'Products' => [
        'StoreID' => 'Stores (StoreID) ON DELETE RESTRICT ON UPDATE CASCADE'
    ],
    'Purchases' => [
        'StoreID' => 'Stores (StoreID) ON DELETE RESTRICT ON UPDATE CASCADE'
    ],
    'PurchaseLineItems' => [
        'PurchaseID' => 'Purchases (PurchaseID) ON DELETE RESTRICT ON UPDATE CASCADE',
        'ProductID' => 'Products (ProductID) ON DELETE RESTRICT ON UPDATE CASCADE',
    ]
];

$MODULE_TABLES = array_merge($MODULE_TABLES, $DB_tables);
$MODULE_FOREIGNKEYS = array_merge($MODULE_FOREIGNKEYS, $DB_foreignKeys);
$MODULE_INDEX = array_merge($MODULE_INDEX, $DB_index);
