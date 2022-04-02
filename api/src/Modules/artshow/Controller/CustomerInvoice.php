<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/*
    phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
namespace App\Modules\artshow\Controller;

class CustomerInvoiceDocument extends \TCPDF
{

    private $data;

    private $font;

    private $config;


    public function SetData($data, $font, $config)
    {
        $this->data = $data;
        $this->font = $font;
        $this->config = $config;

    }


    public function Header()
    {
        $this->SetFont($this->font, '', 5);
        $this->Cell(0, 0, 'Invoice #: '.$this->data['invoice'], 0, true, 'R', 0, '', 0, false, 'C', 'C');
        // Set font
        $this->SetFont($this->font, 'B', 20);
        // Title
        $this->Cell(0, 15, '', 0, true, 'C', 0, '', 0, false, 'C', 'C');
        $this->Cell(0, 15, 'Customer Invoice', 0, true, 'C', 0, '', 0, false, 'C', 'C');
        if (array_key_exists('Artshow_LinkBuyers', $this->config) && boolval($this->config['Artshow_LinkBuyers'])) {
            $data = $this->data['customer'][0]['first_name'].' '.$this->data['customer'][0]['last_name'];
        } else {
            $data = $this->data['customer'][0]['identifier'];
        }
        $this->Cell(0, 15, $data, 0, false, 'C', 0, '', 0, false, 'C', 'C');

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


    /* end CustomerInvoiceDocument */
}

class CustomerInvoice
{

    private $font = 'times';

    private $fontPt = 9;

    private $paperSize = 'LETTER';

    private $paperOrientation = 'PORTRAIT';

    private $margins = 10;

    private $config;

    private $data;


    private function generateArtistName($artist)
    {
        if ($artist['company_name_on_sheet'] == '1') {
            return $artist['company_name'];
        }
        return $artist['member']['first_name'].' '.$artist['member']['last_name'];

    }


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
        $pdf = new CustomerInvoiceDocument($this->paperOrientation, 'mm', $this->paperSize, true, 'UTF-8', false);
        $pdf->SetData($this->data, $this->font, $this->config);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Customer Invoice');
        $pdf->SetAuthor('Con-In-A-Box');
        $pdf->SetSubject('ArtShow Customer Invoice');
        $pdf->SetKeywords('CIAB, PDF, Artshow, customer, report');

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


    public function buildInvoice()
    {
        $pdf = $this->initPDF();

        $margins = $pdf->getMargins();
        $drawableWidth = $pdf->getPageWidth() - ($margins['left'] + $margins['right']);

        $pdf->SetFont($this->font, 'B', $this->fontPt + 5);
        $pdf->Cell(0, 20, $this->config['Artshow_DisplayArtName'], 0, true, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);

        $art_net = 0;
        foreach ($this->data['art_sales'] as $sale) {
            $w = $drawableWidth / 3;
            $pdf->Cell($w, 0, 'Title: \''.$sale['piece']['name'].'\' By '.$this->generateArtistName($sale['piece']['artist']), 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w, 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $w = (2 * $drawableWidth / 3) / 4;
            $pdf->Cell($w, 0, $sale['price_type'], 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $p = number_format((float)$sale['price'], 2, '.', '');
            $pdf->Cell($w, 0, 'price: $'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
            $art_net += $sale['price'];
        }

        if (!empty($this->data['art_sales'])) {
            $w = ($drawableWidth / 2) / 3;
            $pdf->Cell($drawableWidth - $w, 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $p = number_format((float)$art_net, 2, '.', '');
            $pdf->SetFont($this->font, 'B', $this->fontPt);
            $pdf->Cell($w, 0, 'Art Total:  $'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->SetFont($this->font, '', $this->fontPt);
        }

        $pdf->SetFont($this->font, 'B', $this->fontPt + 5);
        $pdf->Cell(0, 20, $this->config['Artshow_PrintArtName'], 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);

        $print_net = 0;
        foreach ($this->data['print_sale'] as $sale) {
            $w = $drawableWidth / 3;
            $pdf->Cell($w, 0, 'Title: \''.$sale['piece']['name'].'\' By '.$this->generateArtistName($sale['piece']['artist']), 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w, 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $w = (2 * $drawableWidth / 3) / 4;
            $pdf->Cell($w, 0, $this->config['Artshow_PrintArtName'].' Sale', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $p = number_format((float)$sale['price'], 2, '.', '');
            $pdf->Cell($w, 0, 'price: $'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
            $print_net += $sale['price'];
        }
        if (!empty($this->data['print'])) {
            $w = ($drawableWidth / 2) / 3;
            $pdf->Cell($drawableWidth - $w, 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $p = number_format((float)$print_net, 2, '.', '');
            $pdf->SetFont($this->font, 'B', $this->fontPt);
            $pdf->Cell($w, 0, $this->config['Artshow_PrintArtName'].' Total:  $'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->SetFont($this->font, '', $this->fontPt);
        }

        $pdf->SetFont($this->font, 'B', $this->fontPt + 5);
        $pdf->Cell(0, 20, 'Sub Total', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);
        $w = ($drawableWidth / 2) / 3;
        $pdf->Cell($drawableWidth - $w, 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
        $p = number_format((float)($print_net + $art_net), 2, '.', '');
        $pdf->SetFont($this->font, 'B', $this->fontPt);
        $pdf->Cell($w, 0, 'Sub Total:  $'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);

        $pdf->SetFont($this->font, 'B', $this->fontPt + 5);
        $pdf->Cell(0, 20, 'Payment Received', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);
        $payment_total = 0;
        foreach ($this->data['payment'] as $payment) {
            $w = $drawableWidth / 3;
            $pdf->Cell($w, 0, $payment['date'], 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(2 * $w, 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $w = (2 * $drawableWidth / 3) / 4;
            $pdf->SetX($drawableWidth - $w);
            $p = number_format((float)$payment['amount'], 2, '.', '');
            $pdf->Cell($w, 0, $payment['payment_type'].' $'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
            $payment_total += $payment['amount'];
        }

        $pdf->Cell($drawableWidth, 0, '', '', 1, 'L', 0, '', 0, false, 'T', 'M');

        $w = ($drawableWidth / 2) / 3;
        $pdf->Cell($drawableWidth - $w, 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
        $p = number_format((float)$payment_total, 2, '.', '');
        $pdf->SetFont($this->font, 'B', $this->fontPt);
        $pdf->Cell($w, 0, 'Payment Total:  $'.$p, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);

        $balance = number_format((float)($print_net + $art_net - $payment_total), 2, '.', '');
        $w = ($drawableWidth / 2) / 3;
        $pdf->Cell($drawableWidth - $w, 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, 'B', $this->fontPt);
        $pdf->Cell($w, 0, 'Please Pay:  $'.$balance, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);

        return $pdf->Output('', 'S');

    }


    /* end CustomerInvoice */
}
