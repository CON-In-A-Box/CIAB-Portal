<?php

/*.
    require_module 'standard';
.*/

namespace artshow;

$DB_tables = [
'Artshow_Configuration' => [
'Field' => 'VARCHAR(100) NOT NULL PRIMARY KEY',
'Value' => 'TEXT NOT NULL'
    ],
'Artshow_PieceType' => [
'PieceType' => 'VARCHAR(30) NOT NULL PRIMARY KEY'
    ],
'Artshow_PaymentType' => [
'PaymentType' => 'VARCHAR(30) NOT NULL PRIMARY KEY'
    ],
'Artshow_ReturnMethod' => [
'ReturnMethod' => 'VARCHAR(30) NOT NULL PRIMARY KEY'
],
'Artshow_PriceType' => [
'PriceType' => 'VARCHAR(30) NOT NULL PRIMARY KEY',
'Position' => 'INT UNSIGNED NOT NULL UNIQUE AUTO_INCREMENT',
'SetPrice' => 'BOOLEAN NOT NULL'
    ],
'Artshow_Artist' => [
'ArtistID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
'AccountID' => 'INT UNSIGNED NOT NULL UNIQUE',
'CompanyName' => 'VARCHAR(50)',
'CompanyNameOnSheet' => 'BOOLEAN DEFAULT 0',
'CompanyNameOnPayment' => 'BOOLEAN DEFAULT 0',
'Website' => 'VARCHAR(50)',
'Notes' => 'TEXT',
'Professional' => 'BOOLEAN NOT NULL DEFAULT 0',
'Inactive' => 'BOOLEAN NOT NULL DEFAULT 0',
'GuestOfHonor' => 'BOOLEAN NOT NULL DEFAULT 0'
    ],
'Artshow_Agent' => [
'ArtistID' => 'INT UNSIGNED NOT NULL',
'EventID' => 'INT UNSIGNED NOT NULL',
'AccountID' => 'INT UNSIGNED NOT NULL',
    ],
'Artshow_Registration' => [
'ArtistID' => 'INT UNSIGNED NOT NULL',
'EventID' => 'INT UNSIGNED NOT NULL',
'MailIn' => 'BOOLEAN NOT NULL',
'ReturnMethod' => 'VARCHAR(30)',
'InsuranceAmount' => 'INT UNSIGNED',
'InitialPayment' => 'INT UNSIGNED DEFAULT 0',
'PaymentType' => 'VARCHAR(30)',
'CheckNumber' => 'INT UNSIGNED',
'Notes' => 'TEXT',
'ReturnLabels' => 'VARCHAR(30)'
    ],
'Artshow_RegistrationQuestion' => [
'QuestionID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
'BooleanQuestion' => 'BOOLEAN DEFAULT 0',
'Text' => 'TEXT NOT NULL'
],
'Artshow_RegistrationAnswer' => [
'ArtistID' => 'INT UNSIGNED NOT NULL',
'EventID' => 'INT UNSIGNED NOT NULL',
'QuestionID' => 'INT UNSIGNED NOT NULL',
'Answer' => 'TEXT NOT NULL'
],
'Artshow_DisplayArt' => [
'PieceID' => 'INT UNSIGNED NOT NULL',
'EventID' => 'INT UNSIGNED NOT NULL',
'ArtistID' => 'INT UNSIGNED NOT NULL',
'Name' => 'TEXT NOT NULL',
'Medium' => 'TEXT',
'PieceType' => 'VARCHAR(30) NOT NULL',
'Edition' => 'TEXT',
'NFS' => 'BOOLEAN DEFAULT FALSE',
'Charity' => 'BOOLEAN DEFAULT FALSE',
'NonTax' => 'BOOLEAN DEFAULT FALSE',
'Notes' => 'TEXT',
'Location' => 'TEXT',
'TagPrintCount' => 'INT UNSIGNED DEFAULT 0',
'Status' => 'TEXT'
    ],
'Artshow_DisplayArtPrice' => [
'PieceID' => 'INT UNSIGNED NOT NULL',
'EventID' => 'INT UNSIGNED NOT NULL',
'PriceType' => 'VARCHAR(30) NOT NULL',
'Price' => 'INT NOT NULL'
    ],
'Artshow_PrintShopArt' => [
'PieceID' => 'INT UNSIGNED NOT NULL',
'EventID' => 'INT UNSIGNED NOT NULL',
'ArtistID' => 'INT UNSIGNED NOT NULL',
'Name' => 'TEXT NOT NULL',
'PieceType' => 'VARCHAR(30) NOT NULL',
'Quantity' => 'INT UNSIGNED NOT NULL',
'Price' => 'INT UNSIGNED NOT NULL',
'Charity' => 'BOOLEAN DEFAULT FALSE',
'NonTax' => 'BOOLEAN DEFAULT FALSE',
'Notes' => 'TEXT',
'Location' => 'TEXT',
'TagPrintCount' => 'INT UNSIGNED DEFAULT 0',
'Status' => 'TEXT'
    ],
'Artshow_Sale' => [
'PieceID' => 'INT UNSIGNED NOT NULL',
'EventID' => 'INT UNSIGNED NOT NULL',
'BuyerID' => 'INT UNSIGNED NOT NULL',
'PriceType' => 'VARCHAR(30) NOT NULL',
'Price' => 'INT UNSIGNED NOT NULL'
    ],
'Artshow_BidderInvoice' => [
'EventID' => 'INT UNSIGNED NOT NULL',
'BuyerID' => 'INT UNSIGNED NOT NULL',
'Ready' => 'BOOLEAN NOT NULL',
'PickedUp' => 'BOOLEAN NOT NULL',
'Paid' => 'BOOLEAN NOT NULL',
'PaymentType' => 'VARCHAR(30) NOT NULL',
'Amount' => 'INT NOT NULL',
    ],
'Artshow_ArtistPayment' => [
'EventID' => 'INT UNSIGNED NOT NULL',
'ArtistID' => 'INT UNSIGNED NOT NULL',
'Amount' => 'INT NOT NULL',
'CheckNumber' => 'INT NOT NULL',
'Date' => 'DATETIME NOT NULL'
    ]

];

$DB_foreignKeys = [
'Artshow_Artist' => [
'AccountID' => 'Members (AccountID) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],
'Artshow_Agent' => [
'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
'AccountID' => 'Members (AccountID) ON DELETE RESTRICT ON UPDATE CASCADE'
    ],
'Artshow_Registration' => [
'ArtistID' => 'Artshow_Artist (ArtistID) ON DELETE RESTRICT ON UPDATE CASCADE',
'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
'PaymentType' => 'Artshow_PaymentType(PaymentType) ON DELETE RESTRICT ON UPDATE CASCADE',
'ReturnMethod' => 'Artshow_ReturnMethod(ReturnMethod) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],
'Artshow_DisplayArt' => [
'ArtistID' => 'Artshow_Artist (ArtistID) ON DELETE RESTRICT ON UPDATE CASCADE',
'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
'PieceType' => 'Artshow_PieceType (PieceType) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],
'Artshow_PrintShopArt' => [
'ArtistID' => 'Artshow_Artist (ArtistID) ON DELETE RESTRICT ON UPDATE CASCADE',
'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
'PieceType' => 'Artshow_PieceType (PieceType) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],
'Artshow_DisplayArtPrice' => [
'PieceID' => 'Artshow_DisplayArt (PieceID) ON DELETE RESTRICT ON UPDATE CASCADE',
'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
'PriceType' => 'Artshow_PriceType (PriceType) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],
'Artshow_Sale' => [
'PieceID' => 'Artshow_DisplayArt (PieceID) ON DELETE RESTRICT ON UPDATE CASCADE',
'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
'BuyerID' => 'Members (AccountID) ON DELETE RESTRICT ON UPDATE CASCADE',
'PriceType' => 'Artshow_PriceType (PriceType) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],
'Artshow_BidderInvoice' => [
'BuyerID' => 'Members (AccountID) ON DELETE RESTRICT ON UPDATE CASCADE',
'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
'PaymentType' => 'Artshow_PaymentType(PaymentType) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],
'Artshow_ArtistPayment' => [
'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
'ArtistID' => 'Artshow_Artist (ArtistID) ON DELETE RESTRICT ON UPDATE CASCADE',
    ],

];

$DB_primaryKeys = [
'Artshow_Agent' => ['ArtistID', 'EventID'],
'Artshow_Registration' => ['ArtistID', 'EventID'],
'Artshow_DisplayArt' => ['PieceID', 'EventID'],
'Artshow_DisplayArtPrice' => ['PieceID', 'EventID', 'PriceType'],
'Artshow_PrintShopArt' => ['PieceID', 'EventID'],
'Artshow_Sale' => ['PieceID', 'EventID'],
'Artshow_BidderInvoice' => ['EventID', 'BuyerID'],
'Artshow_ArtistPayment' => ['EventID', 'ArtistID'],
'Artshow_RegistrationAnswer' => ['EventID', 'ArtistID', 'QuestionID'],

];

\DB::addTables($DB_tables);
\DB::addForeignKeys($DB_foreignKeys);
\DB::addPrimaryKeys($DB_primaryKeys);
