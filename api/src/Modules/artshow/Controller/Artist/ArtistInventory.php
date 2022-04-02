<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/*
    phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
namespace App\Modules\artshow\Controller\Artist;

class ArtistInventoryDocument extends \TCPDF
{

    private $data;

    private $font;


    public function SetData($data, $font)
    {
        $this->data = $data;
        $this->font = $font;

    }


    private function generateArtistName()
    {
        if ($this->data['artist']['company_name_on_sheet'] == '1') {
            return $this->data['artist']['company_name'];
        }
        return $this->data['member']['first_name'].' '.$this->data['member']['last_name'];

    }


    public function Header()
    {
        /*
        $image_file = K_PATH_IMAGES.'logo_example.jpg';
        $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
         */
        // Set font
        $this->SetFont($this->font, 'B', 20);
        // Title
        $this->Cell(0, 15, '', 0, true, 'C', 0, '', 0, false, 'C', 'C');
        $this->Cell(0, 15, 'Artist Inventory', 0, true, 'C', 0, '', 0, false, 'C', 'C');
        $this->Cell(0, 15, $this->generateArtistName(), 0, false, 'C', 0, '', 0, false, 'C', 'C');

    }


    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-10);
        // Set font
        $this->SetFont($this->font, 'I', 8);
        // Page number
        $this->Cell(0, 0, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, true, 'C', 0, '', 0, false, 'C', 'C');
        $this->SetFont('helvetica', 'I', 6);
        $this->Cell(0, 10, 'InventoryGenerated '.date('l jS \of F Y h:i:s A'), 0, false, 'R', 0, '', 0, false, 'C', 'C');

    }


    /* end ArtistInventoryDocument */
}

class ArtistInventory
{

    private $font = 'times';

    private $fontPt = 9;

    private $paperSize = 'LETTER';

    private $paperOrientation = 'PORTRAIT';

    private $margins = 10;

    private $config;

    private $data;


    public function __construct($config, $data)
    {
        $this->config = $config;
        $this->data = $data;

        if (array_key_exists('Artshow_InventoryFont', $config)) {
            $this->font = strval($config['Artshow_InventoryFont']);
        }

        if (array_key_exists('Artshow_InventoryFontSize', $config)) {
            $this->fontPt = intval($config['Artshow_InventoryFontSize']);
        }

        if (array_key_exists('Artshow_InventoryPaperSize', $config)) {
            $this->paperSize = strval($config['Artshow_InventoryPaperSize']);
        }

        if (array_key_exists('Artshow_InventoryPaperOrientation', $config)) {
            $this->paperOrientation = strval($config['Artshow_InventoryPaperOrientation']);
        }

        if (array_key_exists('Artshow_InventoryMargins', $config)) {
            $this->margins = intval($config['Artshow_InventoryMargins']);
        }

    }


    private function initPDF() : \TCPDF
    {
        $pdf = new ArtistInventoryDocument($this->paperOrientation, 'mm', $this->paperSize, true, 'UTF-8', false);
        $pdf->SetData($this->data, $this->font);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Con-In-A-Box');
        $pdf->SetTitle('Artist Inventory');
        $pdf->SetSubject('ArtShow Artist Inventory');
        $pdf->SetKeywords('CIAB, PDF, Artshow, artist, report');

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins($this->margins, $this->margins + 20, $this->margins, $this->margins);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->SetFont($this->font, '', $this->fontPt);
        $pdf->AddPage();
        return $pdf;

    }


    public function buildInventory()
    {
        $pdf = $this->initPDF();

        $margins = $pdf->getMargins();
        $drawableWidth = $pdf->getPageWidth() - ($margins['left'] + $margins['right']);

        $pdf->SetFont($this->font, 'B', $this->fontPt + 5);
        $pdf->Cell(0, 20, $this->config['Artshow_DisplayArtName'], 0, true, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);

        $count = 0;
        foreach ($this->config['pricetype'] as $price) {
            if ($price == 'configuration') {
                continue;
            }
            if (intval($price['artist_set']) == 1) {
                $count ++;
            }
        }

        $lineWidth = $drawableWidth - 30;
        $pdf->Cell(10, 0, 'Checked In', 'B', 0, '', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($lineWidth, 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(10, 0, 'Picked Up', 'B', 1, '', 0, '', 0, false, 'T', 'M');
        foreach ($this->data['art'] as $art) {
            $printed = false;
            foreach ($this->data['art_sales'] as $sale) {
                if ($sale['piece'] == $art['id']) {
                    $x = $pdf->GetX();
                    $pdf->Cell(10, 0, '', '', 0, '', 0, '', 0, false, 'T', 'M');
                    $x2 = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->Rect($x2 + (($x - $x2) / 2) - 1.5, $y, 3, 3, 'F', array(), array(90, 90, 90));

                    $w = $lineWidth / 3;
                    $pdf->Cell($w, 0, 'Title: '.$art['name'], 'BL', 0, 'L', 0, '', 0, false, 'T', 'M');

                    $w = (2 * $lineWidth / 3) / ($count + 1);

                    $pdf->Cell($w, 0, 'Medium: '.$art['medium'], 'BR', 0, 'L', 0, '', 0, false, 'T', 'M');

                    $pdf->Cell($w, 0, ' ', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
                    $pdf->Cell($w, 0, 'Sold: $'.number_format(intval($sale['price'])), 'B', 0, 'L', 0, '', 0, false, 'T', 'M');

                    for ($j = 0; $j < $count - 2; $j++) {
                        $pdf->Cell($w, 0, ' ', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
                    }

                    $x = $pdf->GetX();
                    $pdf->Cell(10, 0, '', 'L', 0, '', 0, '', 0, false, 'T', 'M');
                    $x2 = $pdf->GetX();
                    $pdf->Rect($x2 + (($x - $x2) / 2) - 1.5, $y, 3, 3, 'F', array(), array(90, 90, 90));
                    $pdf->Cell(0.1, 0, '', '', 1, 'L', 0, '', 0, false, 'T', 'M');

                    $printed = true;
                }
            }
            if (!$printed) {
                $x = $pdf->GetX();
                $pdf->Cell(10, 0, '', '', 0, 'L', 0, '', 0, false, 'T', 'M');
                $x2 = $pdf->GetX();
                $y = $pdf->GetY() + ($pdf->getCellHeight($this->fontPt) / 2) - 5;
                if (!empty($art['location'])) {
                    $pdf->Rect($x2 + (($x - $x2) / 2) - 1.5, $y, 3, 3, 'F', array(), array(90, 90, 90));
                } else {
                    $pdf->Rect($x2 + (($x - $x2) / 2) - 1.5, $y, 3, 3);
                }

                $w = $lineWidth / 3;
                $pdf->Cell($w, 0, 'Title: '.$art['name'], 'BL', 0, 'L', 0, '', 0, false, 'T', 'M');

                $w = (2 * $lineWidth / 3) / ($count + 1);

                $pdf->Cell($w, 0, 'Medium: '.$art['medium'], 'BR', 0, 'L', 0, '', 0, false, 'T', 'M');

                foreach ($this->config['pricetype'] as $price) {
                    if ($price == 'configuration') {
                        continue;
                    }
                    if (intval($price['artist_set']) == 1) {
                        $p = number_format(intval($art[$price['price']]), 2, '.', '');
                        $pdf->Cell($w, 0, $price['price'].':'.$p, 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
                    }
                }
                $x = $pdf->GetX();
                $pdf->Cell(10, 0, '', 'L', 0, 'L', 0, '', 0, false, 'T', 'M');
                $x2 = $pdf->GetX();
                $y = $pdf->GetY() + ($pdf->getCellHeight($this->fontPt) / 2) - 5;
                $pdf->Rect($x2 + (($x - $x2) / 2) - 1.5, $y, 3, 3);
                $pdf->Cell(0.1, 0, '', '', 1, 'L', 0, '', 0, false, 'T', 'M');
            }
        }

        $pdf->SetFont($this->font, 'B', $this->fontPt + 5);
        $pdf->Cell(0, 20, $this->config['Artshow_PrintArtName'], 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);

        $pdf->Cell(10, 0, '# Checked In', 'B', 0, '', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($lineWidth, 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(10, 0, '# Picked Up', 'B', 1, '', 0, '', 0, false, 'T', 'M');
        foreach ($this->data['print'] as $art) {
            $pdf->Cell(10, 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');

            $w = $lineWidth / 2;
            $pdf->Cell($w, 0, 'Title: '.$art['name'], 'LB', 0, 'L', 0, '', 0, false, 'T', 'M');
            $w = ($lineWidth / 2) / 4;
            $left = intval($art['quantity']) - intval($art['sold']);
            $pdf->Cell($w, 0, 'Count: '.$art['quantity'], 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w, 0, 'Sold: '.$art['sold'], 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w, 0, 'Remaining: '.$left, 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $p = number_format((float)$art['price'], 2, '.', '');
            $pdf->Cell($w, 0, 'Price: $'.$p, 'B', 0, 'L', 0, '', 0, false, 'T', 'M');

            $pdf->Cell(10, 0, '', 'LB', 1, 'L', 0, '', 0, false, 'T', 'M');
        }

        return $pdf->Output('', 'S');

    }


    /* end ArtistInventory */
}
