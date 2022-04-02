<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/*
    phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
namespace App\Modules\artshow\Controller;

class ArtistInvoiceDocument extends \TCPDF
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
        $this->SetFont($this->font, '', 5);
        $this->Cell(0, 0, 'Invoice #: '.$this->data['invoice'], 0, true, 'R', 0, '', 0, false, 'C', 'C');
        // Set font
        $this->SetFont($this->font, 'B', 20);
        // Title
        $this->Cell(0, 15, '', 0, true, 'C', 0, '', 0, false, 'C', 'C');
        $this->Cell(0, 15, 'Artist Invoice', 0, true, 'C', 0, '', 0, false, 'C', 'C');
        $this->Cell(0, 15, $this->generateArtistName(), 0, true, 'C', 0, '', 0, false, 'C', 'C');

        $this->SetFont($this->font, 'N', 9);
        $this->Cell(0, 9, $this->data['member']['address_line1'], 0, true, 'C', 0, '', 0, false, 'C', 'C');
        if ($this->data['member']['address_line2']) {
            $this->Cell(0, 9, $this->data['member']['address_line2'], 0, true, 'C', 0, '', 0, false, 'C', 'C');
        }
        $this->Cell(0, 9, $this->data['member']['city'].', '.$this->data['member']['state'].' '.$this->data['member']['zip_code'], 0, false, 'C', 0, '', 0, false, 'C', 'C');

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
        $this->Cell(0, 10, 'InvoiceGenerated '.date('l jS \of F Y h:i:s A'), 0, false, 'R', 0, '', 0, false, 'C', 'C');

    }


    /* end ArtistInvoiceDocument */
}

class ArtistInvoice
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

        if (array_key_exists('Artshow_InvoiceFont', $config)) {
            $this->font = strval($config['Artshow_InvoiceFont']);
        }

        if (array_key_exists('Artshow_InvoiceFontSize', $config)) {
            $this->fontPt = intval($config['Artshow_InvoiceFontSize']);
        }

        if (array_key_exists('Artshow_InvoicePaperSize', $config)) {
            $this->paperSize = strval($config['Artshow_InvoicePaperSize']);
        }

        if (array_key_exists('Artshow_InvoicePaperOrientation', $config)) {
            $this->paperOrientation = strval($config['Artshow_InvoicePaperOrientation']);
        }

        if (array_key_exists('Artshow_InvoiceMargins', $config)) {
            $this->margins = intval($config['Artshow_InvoiceMargins']);
        }

    }


    private function initPDF() : \TCPDF
    {
        $pdf = new ArtistInvoiceDocument($this->paperOrientation, 'mm', $this->paperSize, true, 'UTF-8', false);
        $pdf->SetData($this->data, $this->font);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Con-In-A-Box');
        $pdf->SetTitle('Artist Report');
        $pdf->SetSubject('ArtShow Artist Report');
        $pdf->SetKeywords('CIAB, PDF, Artshow, artist, report');

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins($this->margins, $this->margins + 30, $this->margins, $this->margins);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->SetFont($this->font, '', $this->fontPt);
        $pdf->AddPage();
        return $pdf;

    }


    public function buildInvoice()
    {
        $pdf = $this->initPDF();

        $margins = $pdf->getMargins();
        $drawableWidth = $pdf->getPageWidth() - ($margins['left'] + $margins['right']);

        $pdf->SetFont($this->font, 'B', $this->fontPt + 5);
        $pdf->Cell(0, 20, $this->config['Artshow_DisplayArtName'], 0, true, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);
        $com_rate = (intval($this->config['Artshow_DisplayComission']) / 100.0);
        $gross = 0;
        $art_net = 0;
        $commission = 0;
        foreach ($this->data['art'] as $art) {
            $printed = false;
            foreach ($this->data['art_sales'] as $sale) {
                if ($sale['piece'] == $art['id']) {
                    $pdf->SetFont($this->font, 'I', $this->fontPt);
                    $pdf->Cell(0, 0, 'Title: '.$art['name'], '', 1, 'L', 0, '', 0, false, 'T', 'M');

                    $c = $sale['price'] * $com_rate;
                    $w = $drawableWidth / 3;
                    $pdf->Cell($w, 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
                    $w = (2 * $drawableWidth / 3) / 4;
                    $pdf->Cell($w, 0, $sale['price_type'], 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
                    $p = number_format((float)$sale['price'], 2, '.', '');
                    $pdf->Cell($w, 0, 'price: $'.$p, 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
                    $p = number_format((float)$c, 2, '.', '');
                    $pdf->Cell($w, 0, 'commission: $'.$p, 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
                    $p = number_format((float)($sale['price'] - $c), 2, '.', '');
                    $pdf->Cell($w, 0, 'total: $'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
                    $gross += $sale['price'];
                    $art_net += ($sale['price'] - $c);
                    $commission += $c;
                    $printed = true;
                    $pdf->SetFont($this->font, '', $this->fontPt);
                    break;
                }
            }
            if (!$printed) {
                $pdf->Cell(0, 0, 'Title: '.$art['name'], 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
            }
        }
        if (!empty($this->data['art'])) {
            $x = $drawableWidth / 2;
            $w = ($drawableWidth / 2) / 3;
            $pdf->setX($margins['left'] + $x);
            $p = number_format((float)$gross, 2, '.', '');
            $pdf->Cell($w, 0, 'Total Sales $'.$p, 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $p = number_format((float)$commission, 2, '.', '');
            $pdf->Cell($w, 0, 'Commission $'.$p, 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $p = number_format((float)$art_net, 2, '.', '');
            $pdf->SetFont($this->font, 'B', $this->fontPt);
            $pdf->Cell($w, 0, 'Payable $'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->SetFont($this->font, '', $this->fontPt);
        }

        $pdf->SetFont($this->font, 'B', $this->fontPt + 5);
        $pdf->Cell(0, 20, $this->config['Artshow_PrintArtName'], 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);
        $gross = 0;
        $print_net = 0;
        $commission = 0;
        $com_rate = (intval($this->config['Artshow_PrintShopComission']) / 100.0);
        foreach ($this->data['print'] as $art) {
            $w = $drawableWidth / 2;
            $pdf->Cell($w, 0, 'Title: '.$art['name'], 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $w = $drawableWidth / 8;
            $amount = $art['price'] * $art['sold'];
            $pdf->Cell($w, 0, 'Count: '.$art['quantity'], 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $p = number_format((float)$art['price'], 2, '.', '');
            $pdf->Cell($w, 0, 'Price: $'.$p, 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w, 0, 'Sold: '.$art['sold'], 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $p = number_format((float)$amount, 2, '.', '');
            $pdf->Cell($w, 0, 'Total: $'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
            $gross += $amount;
            $c = $amount * $com_rate;
            $commission += $c;
            $print_net += ($amount - $c);
        }
        if (!empty($this->data['print'])) {
            $x = $drawableWidth / 2;
            $w = ($drawableWidth / 2) / 3;
            $pdf->setX($margins['left'] + $x);
            $p = number_format((float)$gross, 2, '.', '');
            $pdf->Cell($w, 0, 'Total Sales $'.$p, 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $p = number_format((float)$commission, 2, '.', '');
            $pdf->Cell($w, 0, 'Commission $'.$p, 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $p = number_format((float)$print_net, 2, '.', '');
            $pdf->SetFont($this->font, 'B', $this->fontPt);
            $pdf->Cell($w, 0, 'Payable $'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->SetFont($this->font, '', $this->fontPt);
        }

        $pdf->SetFont($this->font, 'B', $this->fontPt + 5);
        $pdf->Cell(0, 20, 'Total', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);
        $w = ($drawableWidth / 2) / 3;
        $pdf->Cell($drawableWidth - $w, 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
        $p = number_format((float)($print_net + $art_net), 2, '.', '');
        $pdf->SetFont($this->font, 'B', $this->fontPt);
        $pdf->Cell($w, 0, 'Payable $'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);

        /* Distributions */
        $pdf->SetFont($this->font, 'B', $this->fontPt + 5);
        $pdf->Cell(0, 20, 'Payments', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);
        $total = 0;
        foreach ($this->data['distributions'] as $payment) {
            $w = $drawableWidth / 2;
            $pdf->Cell($w, 0, $payment['date'], 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $w = $drawableWidth / 8;
            $pdf->Cell($w, 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w, 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w, 0, 'Check: '.$payment['check_number'], 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $p = number_format((float)$payment['amount'], 2, '.', '');
            $pdf->Cell($w, 0, 'Amount: $'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
            $total += $payment['amount'];
        }
        $pdf->Cell($drawableWidth, 0, '', '', 1, 'L', 0, '', 0, false, 'T', 'M');

        $w = $drawableWidth / 7;

        $pdf->Cell($drawableWidth - $w, 0, '', '', 0, 'L', 0, '', 0, false, 'T', 'M');
        $p = number_format((float)($print_net + $art_net), 2, '.', '');
        $pdf->Cell($w, 0, 'Sales $'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->Cell($drawableWidth - $w, 0, '', '', 0, 'L', 0, '', 0, false, 'T', 'M');
        $p = number_format((float)($total), 2, '.', '');
        $pdf->SetFont($this->font, 'I', $this->fontPt);
        $pdf->Cell($w, 0, 'Paid -$'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);

        $pdf->Cell($drawableWidth - $w, 0, '', '', 0, 'L', 0, '', 0, false, 'T', 'M');
        $p = number_format((float)($print_net + $art_net) - (float)($total), 2, '.', '');
        $pdf->SetFont($this->font, 'B', $this->fontPt);
        $pdf->Cell($w, 0, 'Balance $'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);

        $pdf->Cell($drawableWidth, 0, '', '', 1, 'L', 0, '', 0, false, 'T', 'M');

        return $pdf->Output('', 'S');

    }


    /* end ArtistInvoice */
}
