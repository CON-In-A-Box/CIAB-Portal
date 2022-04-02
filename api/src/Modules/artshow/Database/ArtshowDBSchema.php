<?php

/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Database;

class ArtshowDBSchema extends \App\Core\ModuleDBSchema
{

    private $tables = [
        'Artshow_Configuration' => [
            'Field' => 'VARCHAR(255) NOT NULL PRIMARY KEY',
            'Value' => 'VARCHAR(255) NOT NULL'
        ],
        'Artshow_PieceType' => [
            'PieceType' => 'VARCHAR(255) NOT NULL PRIMARY KEY'
        ],
        'Artshow_PaymentType' => [
            'PaymentType' => 'VARCHAR(255) NOT NULL PRIMARY KEY'
        ],
        'Artshow_ReturnMethod' => [
            'ReturnMethod' => 'VARCHAR(255) NOT NULL PRIMARY KEY'
        ],
        'Artshow_PriceType' => [
            'PriceType' => 'VARCHAR(255) NOT NULL PRIMARY KEY',
            'Position' => 'INT UNSIGNED NOT NULL UNIQUE AUTO_INCREMENT',
            'SetPrice' => 'BOOLEAN NOT NULL',
            'Fixed' => 'BOOLEAN'
        ],
        'Artshow_Artist' => [
            'ArtistID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'AccountID' => 'INT UNSIGNED NOT NULL UNIQUE',
            'CompanyName' => 'VARCHAR(255)',
            'CompanyNameOnSheet' => 'BOOLEAN DEFAULT 0',
            'CompanyNameOnPayment' => 'BOOLEAN DEFAULT 0',
            'Website' => 'VARCHAR(255)',
            'Notes' => 'VARCHAR(255)',
            'Professional' => 'BOOLEAN NOT NULL DEFAULT 0',
            'Inactive' => 'BOOLEAN NOT NULL DEFAULT 0',
            'GuestOfHonor' => 'BOOLEAN NOT NULL DEFAULT 0'
        ],
        'Artshow_Agent' => [
            'ArtistID' => 'INT UNSIGNED NOT NULL',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'AccountID' => 'INT UNSIGNED NOT NULL',
        ],
        'Artshow_Buyer' => [
            'BuyerID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'AccountID' => 'INT UNSIGNED UNIQUE',
            'Identifier' => 'VARCHAR(255) UNIQUE'
        ],
        'Artshow_Registration' => [
            'ArtistID' => 'INT UNSIGNED NOT NULL',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'MailIn' => 'BOOLEAN NOT NULL',
            'ReturnMethod' => 'VARCHAR(255)',
            'InsuranceAmount' => 'INT UNSIGNED',
            'InitialPayment' => 'INT UNSIGNED DEFAULT 0',
            'PaymentType' => 'VARCHAR(255)',
            'CheckNumber' => 'INT UNSIGNED',
            'Notes' => 'VARCHAR(255)',
            'ReturnLabels' => 'VARCHAR(255)'
        ],
        'Artshow_RegistrationQuestion' => [
            'QuestionID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'BooleanQuestion' => 'BOOLEAN DEFAULT 0',
            'Text' => 'VARCHAR(255) NOT NULL'
        ],
        'Artshow_RegistrationAnswer' => [
            'ArtistID' => 'INT UNSIGNED NOT NULL',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'QuestionID' => 'INT UNSIGNED NOT NULL',
            'Answer' => 'VARCHAR(255) NOT NULL'
        ],
        'Artshow_DisplayArt' => [
            'PieceID' => 'INT UNSIGNED NOT NULL',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'ArtistID' => 'INT UNSIGNED NOT NULL',
            'Name' => 'VARCHAR(255) NOT NULL',
            'Medium' => 'VARCHAR(255)',
            'PieceType' => 'VARCHAR(255) NOT NULL',
            'Edition' => 'VARCHAR(255)',
            'NFS' => 'BOOLEAN DEFAULT FALSE',
            'Charity' => 'BOOLEAN DEFAULT FALSE',
            'NonTax' => 'BOOLEAN DEFAULT FALSE',
            'Notes' => 'VARCHAR(255)',
            'Location' => 'VARCHAR(255)',
            'TagPrintCount' => 'INT UNSIGNED DEFAULT 0',
            'inAuction' => 'BOOLEAN DEFAULT FALSE',
            'Status' => 'VARCHAR(255)'
        ],
        'Artshow_DisplayArtPrice' => [
            'PieceID' => 'INT UNSIGNED NOT NULL',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'PriceType' => 'VARCHAR(255) NOT NULL',
            'Price' => 'INT NOT NULL'
        ],
        'Artshow_PrintShopArt' => [
            'PieceID' => 'INT UNSIGNED NOT NULL',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'ArtistID' => 'INT UNSIGNED NOT NULL',
            'Name' => 'VARCHAR(255) NOT NULL',
            'PieceType' => 'VARCHAR(255) NOT NULL',
            'Quantity' => 'INT UNSIGNED NOT NULL',
            'Price' => 'INT UNSIGNED NOT NULL',
            'Charity' => 'BOOLEAN DEFAULT FALSE',
            'NonTax' => 'BOOLEAN DEFAULT FALSE',
            'Notes' => 'VARCHAR(255)',
            'Location' => 'VARCHAR(255)',
            'TagPrintCount' => 'INT UNSIGNED DEFAULT 0',
            'Status' => 'VARCHAR(255)'
        ],
        'Artshow_Art_Sale' => [
            'SaleID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'PieceID' => 'INT UNSIGNED NOT NULL',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'BuyerID' => 'INT UNSIGNED NOT NULL',
            'PriceType' => 'VARCHAR(255) NOT NULL',
            'Price' => 'INT UNSIGNED NOT NULL'
        ],
        'Artshow_Print_Sale' => [
            'SaleID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'PieceID' => 'INT UNSIGNED NOT NULL',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'BuyerID' => 'INT UNSIGNED NOT NULL',
            'PriceType' => 'VARCHAR(255)',
            'Price' => 'INT UNSIGNED NOT NULL'
        ],
        'Artshow_Buyer_Payment' => [
            'PaymentID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'Date' => 'DATETIME NOT NULL DEFAULT NOW()',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'BuyerID' => 'INT UNSIGNED NOT NULL',
            'PaymentType' => 'VARCHAR(255) NOT NULL',
            'Amount' => 'INT NOT NULL',
            'Notes' => 'VARCHAR(255)',
        ],
        'Artshow_Buyer_Invoice' => [
            'InvoiceID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'BuyerID' => 'INT UNSIGNED NOT NULL',
            'Ready' => 'BOOLEAN NOT NULL DEFAULT 0',
            'PickedUp' => 'BOOLEAN NOT NULL DEFAULT 0',
            'Paid' => 'BOOLEAN NOT NULL DEFAULT 0',
            'InvoiceGenerated' => 'DATETIME NOT NULL DEFAULT NOW()',
            'Notes' => 'VARCHAR(255)',
        ],
        'Artshow_Artist_Invoice' => [
            'InvoiceID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'ArtistID' => 'INT UNSIGNED NOT NULL',
            'InvoiceGenerated' => 'DATETIME NOT NULL DEFAULT NOW()',
            'Accepted' => 'BOOLEAN NOT NULL DEFAULT FALSE',
            'Paid' => 'INT NOT NULL DEFAULT 0',
            'Notes' => 'VARCHAR(255)',
        ],
        'Artshow_Artist_Distribution' => [
            'DistributionID' => 'INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'EventID' => 'INT UNSIGNED NOT NULL',
            'ArtistID' => 'INT UNSIGNED NOT NULL',
            'Amount' => 'FLOAT(10,2) NOT NULL',
            'CheckNumber' => 'INT NOT NULL',
            'Date' => 'DATETIME NOT NULL DEFAULT NOW()'
        ]

    ];

    private $foreignKeys = [
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
        'Artshow_Art_Sale' => [
            'PieceID' => 'Artshow_DisplayArt (PieceID) ON DELETE RESTRICT ON UPDATE CASCADE',
            'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
            'PriceType' => 'Artshow_PriceType (PriceType) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'Artshow_Print_Sale' => [
            'PieceID' => 'Artshow_PrintShopArt (PieceID) ON DELETE RESTRICT ON UPDATE CASCADE',
            'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'Artshow_Buyer_Payment' => [
            'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
            'PaymentType' => 'Artshow_PaymentType(PaymentType) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'Artshow_Buyer_Invoice' => [
            'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'Artshow_Artist_Invoice' => [
            'ArtistID' => 'Artshow_Artist (ArtistID) ON DELETE RESTRICT ON UPDATE CASCADE',
            'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ],
        'Artshow_Artist_Distribution' => [
            'EventID' => 'Events (EventID) ON DELETE RESTRICT ON UPDATE CASCADE',
            'ArtistID' => 'Artshow_Artist (ArtistID) ON DELETE RESTRICT ON UPDATE CASCADE',
        ]
    ];

    private $primaryKeys = [
        'Artshow_Agent' => ['ArtistID', 'EventID'],
        'Artshow_Registration' => ['ArtistID', 'EventID'],
        'Artshow_DisplayArt' => ['PieceID', 'EventID'],
        'Artshow_DisplayArtPrice' => ['PieceID', 'EventID', 'PriceType'],
        'Artshow_PrintShopArt' => ['PieceID', 'EventID'],
        'Artshow_RegistrationAnswer' => ['EventID', 'ArtistID', 'QuestionID'],

    ];

    private $seed = [
        'Artshow_PaymentType' => [
            [
                'index' => ['PaymentType' => 'Cash'],
                'data' => []
            ],
            [
                'index' => ['PaymentType' => 'Check'],
                'data' => []
            ],
            [
                'index' => ['PaymentType' => 'Credit Card'],
                'data' => []
            ]
        ],
        'Artshow_PieceType' => [
            [
                'index' => ['PieceType' => 'Normal'],
                'data' => []
            ],
            [
                'index' => ['PieceType' => 'Framed'],
                'data' => []
            ],
            [
                'index' => ['PieceType' => '3D'],
                'data' => []
            ],
            [
                'index' => ['PieceType' => 'Jewelry'],
                'data' => []
            ],
            [
                'index' => ['PieceType' => 'Sculpture'],
                'data' => []
            ],
            [
                'index' => ['PieceType' => 'Huge'],
                'data' => []
            ],
        ],
        'Artshow_PriceType' => [
            [
                'index' => ['PriceType' => 'Auction'],
                'data' => ['SetPrice' => false]
            ],
            [
                'index' => ['PriceType' => 'Min Bid'],
                'data' => ['SetPrice' => true]
            ],
            [
                'index' => ['PriceType' => 'Quick Sale'],
                'data' => ['SetPrice' => true]
            ],
            [
                'index' => ['PriceType' => 'Sunday Sale'],
                'data' => ['SetPrice' => true]
            ],
        ],
        'Artshow_RegistrationQuestion' => [
            [
                'index' => ['Text' => 'The artist or agent hereby refuses release of publishing right with any sales'],
                'data' => ['BooleanQuestion' => 1]
            ],
            [
                'index' => ['Text' => 'Release of all publishing rights negotiable upon contract with the Artist.'],
                'data' => ['BooleanQuestion' => 1]
            ],
            [
                'index' => ['Text' => 'What type of space would you prefer?'],
                'data' => ['BooleanQuestion' => 0]
            ],
            [
                'index' => ['Text' => 'How can Art Show patrons contact you?'],
                'data' => ['BooleanQuestion' => 0]
            ],
        ],
        'Artshow_ReturnMethod' => [
            [
                'index' => ['ReturnMethod' => 'UPS Ground'],
                'data' => []
            ],
            [
                'index' => ['ReturnMethod' => 'USPS Priority Mail'],
                'data' => []
            ],
            [
                'index' => ['ReturnMethod' => 'Fed Ex'],
                'data' => []
            ],
            [
                'index' => ['ReturnMethod' => 'Other'],
                'data' => []
            ],
        ],
        'ConfigurationField' => [
            [
                'index' => ['Field' => 'Artshow_ArtAuction'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'boolean',
                    'InitialValue' => 1,
                    'Description' => 'There is an art auction.'
                ]
            ],
            [
                'index' => ['Field' => 'Artshow_BidTagBarcode'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'boolean',
                    'InitialValue' => '1',
                    'Description' => 'Do bid tags have a barcode?'
                ]
            ],
            [
                'index' => ['Field' => 'Artshow_BidTag2DBarcode'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'boolean',
                    'InitialValue' => '1',
                    'Description' => 'Do bid tags have a 2D barcode.?'
                ]
            ],
            [
                'index' => ['Field' => 'Artshow_BidTagFont'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'text',
                    'InitialValue' => 'times',
                    'Description' => 'Font on the bid tags.'
                ]
            ],
            [
                'index' => ['Field' => 'Artshow_BidTagFontSize'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => '9',
                    'Description' => 'Font size on the bid tags.'
                ]
            ],
            [
                'index' => ['Field' => 'Artshow_BidTagMargins'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => '5',
                    'Description' => 'Margins around the edge of the bid tag sheet.'
                ]
            ],
            [
                'index' => ['Field' => 'Artshow_BidTagPaperOrientation'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'select',
                    'InitialValue' => 'LANDSCAPE',
                    'Description' => 'Paper orientation for printing bid tags.'
                ]
            ],
            [
                'index' => ['Field' => 'Artshow_BidTagPaperSize'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'select',
                    'InitialValue' => 'LETTER',
                    'Description' => 'Paper size for printing bid tags.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_BidTagTitle'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'text',
                    'InitialValue' => '',
                    'Description' => 'Title on each bid tag.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_BidTagInfo1'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'text',
                    'InitialValue' => '',
                    'Description' => 'Info 1 block on each bid tag.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_BidTagInfo2'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'text',
                    'InitialValue' => '',
                    'Description' => 'Info 2 block on each bid tag.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_BidTagsPerRow'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 3,
                    'Description' => 'Number of bid tags per printed row.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_BidsUntilAuction'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 3,
                    'Description' => 'How many bids until a piece goes to auction.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_DefaultType'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'select',
                    'InitialValue' => 'Normal',
                    'Description' => 'Default type when entering types.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_DisplayComission'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 10,
                    'Description' => 'Commission for display art in percent.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_DisplayLimit'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 20,
                    'Description' => 'Limit to number of display pieces.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_HangingFee'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'text',
                    'InitialValue' => 1,
                    'Description' => 'Display art hanging fee.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_NFSHangingFee'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'text',
                    'InitialValue' => 2,
                    'Description' => 'Not For Sale display art hanging fee.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_PrintShop'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'boolean',
                    'InitialValue' => 1,
                    'Description' => 'There an art print shop.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_PrintShopComission'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 10,
                    'Description' => 'Commission for print shop art in percent.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_SelfRegistration'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'boolean',
                    'InitialValue' => 1,
                    'Description' => 'Artists can register art themselves online.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_SelfRegistrationClose'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 3,
                    'Description' => 'Days before the event that artists can add and modify registration and items.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_InvoiceFont'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'text',
                    'InitialValue' => 'times',
                    'Description' => 'Font on the artist invoice.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_InvoiceFontSize'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 9,
                    'Description' => 'Font size on the artist invoice.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_InvoicePaperSize'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'select',
                    'InitialValue' => 'LETTER',
                    'Description' => 'Paper size for printing artist invoices.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_InvoicePaperOrientation'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'select',
                    'InitialValue' => 'LANDSCAPE',
                    'Description' => 'Paper orientation for printing artist invoices.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_InvoiceMargins'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 5,
                    'Description' => 'Margins around the edge of the printed artist invoice.'
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_DisplayArtName'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'text',
                    'InitialValue' => 'Hung Art',
                    'Description' => 'The term used for the unique display art that is placed in the show for bidding. i.e. "Hung Art", "Display Art", "Gallery", etc....',
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_PrintArtName'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'text',
                    'InitialValue' => 'Print Shop',
                    'Description' => 'The term used for the art that has multiple copies and is avaliable for direct sale. i.e. "Print Shop", "Art Market",  etc...',
                ]
            ],
            [
                'index'  => ['Field' => 'Artshow_InventoryFont'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'text',
                    'InitialValue' => 'times',
                    'Description' => 'Font on the artist inventory.'
                    ]
            ],
            [
                'index'  => ['Field' => 'Artshow_InventoryFontSize'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 9,
                    'Description' => 'Font size on the artist inventory.'
                    ]
            ],
            [
                'index'  => ['Field' => 'Artshow_InventoryPaperSize'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'select',
                    'InitialValue' => 'LETTER',
                    'Description' => 'Paper size for printing artist inventorys.'
                    ]
            ],
            [
                'index'  => ['Field' => 'Artshow_InventoryPaperOrientation'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'select',
                    'InitialValue' => 'LANDSCAPE',
                    'Description' => 'Paper orientation for printing artist inventorys.'
                    ]
            ],
            [
                'index'  => ['Field' => 'Artshow_InventoryMargins'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 5,
                    'Description' => 'Margins around the edge of the printed artist inventory.'
                    ]
            ],
            [
                'index'  => ['Field' => 'Artshow_MailInAllowed'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'boolean',
                    'InitialValue' => 1,
                    'Description' => 'Mail in art is allowed.'
                    ]
            ],
            [
                'index'  => ['Field' => 'Artshow_LinkBuyers'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'boolean',
                    'InitialValue' => 1,
                    'Description' => 'Buyers are all in located in the members database.'
                    ]
            ],
            [
                'index'  => ['Field' => 'Artshow_ComanyName_Charlimit'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 255,
                    'Description' => 'Character limit for company names.'
                    ]
            ],
            [
                'index'  => ['Field' => 'Artshow_Website_Charlimit'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 255,
                    'Description' => 'Character limit for company websites.'
                    ]
            ],
            [
                'index'  => ['Field' => 'Artshow_PieceName_Charlimit'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 255,
                    'Description' => 'Character limit for art piece names.'
                    ]
            ],
            [
                'index'  => ['Field' => 'Artshow_PieceMedium_Charlimit'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 255,
                    'Description' => 'Character limit for art piece mediums.'
                    ]
            ],
            [
                'index'  => ['Field' => 'Artshow_PieceEdition_Charlimit'],
                'data' => [
                    'TargetTable' => 'Artshow_Configuration',
                    'Type' => 'integer',
                    'InitialValue' => 255,
                    'Description' => 'Character limit for art piece editions.'
                    ]
            ],
        ],
        'ConfigurationOption' => [
            [
                'index' => ['Field' => 'Artshow_BidTagPaperOrientation'],
                'data' => ['Name' => 'LANDSCAPE'],
            ],
            [
                'index' => ['Field' => 'Artshow_BidTagPaperOrientation'],
                'data' => ['Name' => 'PORTRAIT'],
            ],
            [
                'index' => ['Field' => 'Artshow_BidTagPaperSize'],
                'data' => ['Name' => 'LETTER'],
            ],
            [
                'index' => ['Field' => 'Artshow_BidTagPaperSize'],
                'data' => ['Name' => 'A4'],
            ],
            [
                'index' => ['Field' => 'Artshow_BidTagPaperSize'],
                'data' => ['Name' => 'LEGAL'],
            ],
            [
                'index' => ['Field' => 'Artshow_InvoicePaperOrientation'],
                'data' => ['Name' => 'LANDSCAPE'],
            ],
            [
                'index' => ['Field' => 'Artshow_InvoicePaperOrientation'],
                'data' => ['Name' => 'PORTRAIT'],
            ],
            [
                'index' => ['Field' => 'Artshow_InvoicePaperSize'],
                'data' => ['Name' => 'LETTER'],
            ],
            [
                'index' => ['Field' => 'Artshow_InvoicePaperSize'],
                'data' => ['Name' => 'A4'],
            ],
            [
                'index' => ['Field' => 'Artshow_InvoicePaperSize'],
                'data' => ['Name' => 'LEGAL'],
            ],
            [
                'index' => ['Field' => 'Artshow_DefaultType'],
                'data' => ['Name' => 'Normal'],
            ],
            [
                'index' => ['Field' => 'Artshow_DefaultType'],
                'data' => ['Name' => 'Framed'],
            ],
            [
                'index' => ['Field' => 'Artshow_DefaultType'],
                'data' => ['Name' => '3D'],
            ],
            [
                'index' => ['Field' => 'Artshow_DefaultType'],
                'data' => ['Name' => 'Jewelry'],
            ],
            [
                'index' => ['Field' => 'Artshow_DefaultType'],
                'data' => ['Name' => 'Sculpture'],
            ],
            [
                'index' => ['Field' => 'Artshow_DefaultType'],
                'data' => ['Name' => 'Huge'],
            ],
            [
                'index' => ['Field' => 'Artshow_InventoryPaperOrientation'],
                'data' => ['Name' => 'LANDSCAPE'],
            ],
            [
                'index' => ['Field' => 'Artshow_InventoryPaperOrientation'],
                'data' => ['Name' => 'PORTRAIT'],
            ],
            [
                'index' => ['Field' => 'Artshow_InventoryPaperSize'],
                'data' => ['Name' => 'LETTER'],
            ],
            [
                'index' => ['Field' => 'Artshow_InventoryPaperSize'],
                'data' => ['Name' => 'A4'],
            ],
            [
                'index' => ['Field' => 'Artshow_InventoryPaperSize'],
                'data' => ['Name' => 'LEGAL'],
            ],
        ],


    ];


    public function __construct($database)
    {
        parent::__construct('artshow', $database, $this->tables, $this->foreignKeys, $this->primaryKeys, null, $this->seed);

    }


    /* end */
}
