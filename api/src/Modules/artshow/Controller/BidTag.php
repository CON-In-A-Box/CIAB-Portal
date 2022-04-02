<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/*
    phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
*/

namespace App\Modules\artshow\Controller;

class TagDocument extends \TCPDF
{


    public function Header()
    {
        // Get the current page break margin
        $bMargin = $this->getBreakMargin();

        // Get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;

        // Disable auto-page-break
        $this->SetAutoPageBreak(false, 0);

        // Define the path to the image that you want to use as watermark.
        $img_file = './your-watermark.jpg';

        // Render the image

        $this->SetAlpha(0.25);
        $this->SetFont('times', '', 112);

        $this->StartTransform();
        $this->Rotate(45, 0, 90);
        $this->Text(0, 90, 'DRAFT');
        $this->StopTransform();

        $this->StartTransform();
        $this->Rotate(45, 90, 90);
        $this->Text(90, 90, 'DRAFT');
        $this->StopTransform();

        $this->StartTransform();
        $this->Rotate(45, 180, 90);
        $this->Text(180, 90, 'DRAFT');
        $this->StopTransform();

        $this->StartTransform();
        $this->Rotate(45, 0, 190);
        $this->Text(0, 190, 'DRAFT');
        $this->StopTransform();

        $this->StartTransform();
        $this->Rotate(45, 90, 190);
        $this->Text(90, 190, 'DRAFT');
        $this->StopTransform();

        $this->StartTransform();
        $this->Rotate(45, 180, 190);
        $this->Text(180, 190, 'DRAFT');
        $this->StopTransform();



        $this->SetAlpha(1);
        // Restore the auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);

        // Set the starting point for the page content
        $this->setPageMark();

    }


    /* end TagDocument */
}

class BidTag
{

    private $font = 'times';

    private $fontPt = 9;

    private $paperSize = 'LETTER';

    private $paperOrientation = 'LANDSCAPE';

    private $tagsPerRow = 2;

    private $margins = 10;


    public function __construct($config)
    {
        $this->config = $config;

        if (array_key_exists('Artshow_BidTagFont', $config)) {
            $this->font = strval($config['Artshow_BidTagFont']);
        }

        if (array_key_exists('Artshow_BidTagFontSize', $config)) {
            $this->fontPt = intval($config['Artshow_BidTagFontSize']);
        }

        if (array_key_exists('Artshow_BidTagPaperSize', $config)) {
            $this->paperSize = strval($config['Artshow_BidTagPaperSize']);
        }

        if (array_key_exists('Artshow_BidTagPaperOrientation', $config)) {
            $this->paperOrientation = strval($config['Artshow_BidTagPaperOrientation']);
        }

        if (array_key_exists('Artshow_BidTagsPerRow', $config)) {
            $this->tagsPerRow = intval($config['Artshow_BidTagsPerRow']);
        }

        if (array_key_exists('Artshow_BidTagMargins', $config)) {
            $this->margins = intval($config['Artshow_BidTagMargins']);
        }

    }


    private function initPDF($draft) : \TCPDF
    {
        $pdf = new TagDocument($this->paperOrientation, 'mm', $this->paperSize);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Con-In-A-Box');
        $pdf->SetTitle('Bid Tags');
        $pdf->SetSubject('ArtShow Bid Tags');
        $pdf->SetKeywords('CIAB, PDF, Artshow, bid, tags, auction');
        if ($draft) {
            $pdf->setPrintHeader(true);
        } else {
            $pdf->setPrintHeader(false);
        }
        $pdf->setPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins($this->margins, $this->margins, $this->margins, $this->margins);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->AddPage();
        $pdf->SetFont($this->font, '', $this->fontPt);
        $pdf->setCellPaddings(1, 1, 1, 1);
        return $pdf;

    }


    private function tagBidRow($pdf, $x, $data, $width)
    {
        $pdf->MultiCell($width / 5, 5, '', 1, 'C', 0, 0, $x, '', true);
        $pdf->MultiCell((3 * $width) / 5, 5, '', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell($width / 5, 5, '', 1, 'C', 0, 1, '', '', true);

    }


    private function tagBidTitleRow($pdf, $x, $data, $width)
    {
        $pdf->MultiCell($width / 5, 5, 'Badge #', 1, 'C', 0, 0, $x, '', true);
        $pdf->MultiCell((3 * $width) / 5, 5, 'Name', 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell($width / 5, 5, 'Bid', 1, 'C', 0, 1, '', '', true);

    }


    private function tagInfoBlock($pdf, $info1, $info2, $x, $data, $width)
    {
        if ($info1 && !empty($info1)) {
            $pdf->MultiCell($width, 5, $info1, 1, 'C', 0, 1, $x, '', true);
        }
        if ($info2 && !empty($info2)) {
            $pdf->MultiCell($width, 5, $info2, 1, 'C', 0, 1, $x, '', true);
        }

    }


    private function tag2DBarcode($pdf, $x, $data, $nextline)
    {
        $style = array(
            'border' => 2,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        if ($nextline) {
            $align = 'N';
        } else {
            $align = 'T';
        }
        $pdf->write2DBarcode($data['2dUri'], 'QRCODE,L', $x, '', 18, 18, $style, $align);

    }


    private function tag1DBarcode($pdf, $x, $data, $width, $nextline)
    {
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => false,
            'cellfitalign' => false,
            'border' => true,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => false,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );

        if ($nextline) {
            $align = 'N';
        } else {
            $align = 'T';
        }
        $pdf->write1DBarcode($data['PieceID'].':'.$data['EventID'], 'C128', $x, '', $width, 18, 0.4, $style, $align);

    }


    private function tagPrices($pdf, $x, $prices, $width)
    {
        $start = true;
        $c = count($prices);
        foreach ($prices as $price) {
            if ($start) {
                $offset = $x;
            } else {
                $offset = '';
            }
            $pdf->MultiCell($width / $c, 5, $price['PriceType'], 'TRL', 'C', 0, 0, $offset, '', true);
            $start = false;
        }
        $pdf->Ln();
        $pdf->SetFont($this->font, 'B', $this->fontPt * 1.5);
        $start = true;
        foreach ($prices as $price) {
            if ($start) {
                $offset = $x;
            } else {
                $offset = '';
            }
            $pdf->MultiCell($width / $c, 5, $price['Price'], 'BRL', 'C', 0, 0, $offset, '', true);
            $start = false;
        }
        $pdf->SetFont($this->font, '', $this->fontPt);
        $pdf->Ln();

    }


    private function tagTitleBlock($pdf, $x, $title, $width, $newline)
    {
        $pdf->SetFont($this->font, 'B', $this->fontPt * 2);
        if (!$newline) {
            $height = 18;
            $pdf->MultiCell($width, $height, $title, 'TLRB', 'C', 0, $newline, $x, '', true, 0, false, true, $height, 'M');
        } else {
            $height = 5;
            $pdf->MultiCell($width, $height, $title, 'TLRB', 'C', 0, $newline, $x, '', true);
        }
        $pdf->SetFont($this->font, '', $this->fontPt);

    }


    private function tagHeaderBlock($pdf, $x, $data, $width)
    {
        $unit = $width / 8;

        $pdf->MultiCell(2 * $unit, 5, 'Title:', 'TLB', 'C', 0, 0, $x, '', true);
        $height = $pdf->getLastH();

        $pdf->SetFont($this->font, 'B', $this->fontPt);
        $pdf->MultiCell(5 * $unit, 5, $data['Name'], 'TB', 'C', 0, 0, '', '', true);

        $pdf->SetFont($this->font, '', $this->fontPt * 0.5);
        $pdf->MultiCell($unit, $height, '#'.$data['PieceID'], 'TRB', 'C', 0, 1, '', '', true, 0, false, true, $height, 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);

        $pdf->MultiCell(2 * $unit, 5, 'Artist:', 'TLB', 'C', 0, 0, $x, '', true);
        $pdf->MultiCell(5 * $unit, 5, $data['Artist'], 'TB', 'C', 0, 0, '', '', true);
        $pdf->SetFont($this->font, '', $this->fontPt * 0.5);
        $pdf->MultiCell($unit, $height, '#'.$data['ArtistID'], 'TRB', 'C', 0, 1, '', '', true, 0, false, true, $height, 'M');
        $pdf->SetFont($this->font, '', $this->fontPt);

        $pdf->MultiCell($width / 4, 5, 'Medium:', 'TBL', 'C', 0, 0, $x, '', true);
        $pdf->MultiCell((3 * $width) / 4, 5, $data['Medium'], 'RTB', 'C', 0, 1, '', '', true);

    }


    public static function build2DUri($request, $data)
    {
        $uri = $request->getUri();
        $path = $uri->getScheme()."://".$uri->getHost().":".$uri->getPort();
        $path .= '/index.php?Function=artshow/piece';
        $path .= '&pieceId='.$data['PieceID'].'&eventId='.$data['EventID'];
        return $path;

    }


    public function buildTags($dataset, $draft = true, $callback = null)
    {
        $pdf = $this->initPDF($draft);

        $margins = $pdf->getMargins();
        $drawableWidth = $pdf->getPageWidth() - ($margins['left'] + $margins['right']) - 20;
        $width = ($drawableWidth / $this->tagsPerRow);
        $height = $pdf->getPageHeight() - ($margins['top'] + $margins['bottom']);
        $pdf->setY($margins['top']);
        $count = count($dataset);

        $z = 0;
        do {
            $y = $pdf->GetY();
            if ($y * 2 > $height) {
                $pdf->AddPage();
                $pdf->setY($margins['top']);
                $y = $pdf->GetY();
                $z = 0;
            }
            for ($j = 0; $j < $this->tagsPerRow; $j++) {
                if ($count == 0) {
                    break;
                }
                $data = $dataset[$count - 1];

                $pdf->SetY($y + 10 * $z);
                $x = $margins['left'] + ($width * $j) + (10 * $j);
                if (array_key_exists('Artshow_BidTagTitle', $this->config) &&
                    !empty($this->config['Artshow_BidTagTitle'])) {
                    $title = $this->config['Artshow_BidTagTitle'];
                } else {
                    $title = 'ArtShow Bid Tag';
                }
                if (boolval($this->config['Artshow_BidTag2DBarcode'])) {
                    $this->tagTitleBlock($pdf, $x, $title, $width - 18, 0);
                    $this->tag2DBarcode($pdf, '', $data, true);
                } else {
                    $this->tagTitleBlock($pdf, $x, $title, $width, 1);
                }
                $this->tagHeaderBlock($pdf, $x, $data, $width);
                $this->tagPrices($pdf, $x, $data['prices'], $width);
                if (array_key_exists('Artshow_BidTagInfo1', $this->config) ||
                    array_key_exists('Artshow_BidTagInfo2', $this->config)) {
                    $info1 = $this->config['Artshow_BidTagInfo1'];
                    $info2 = $this->config['Artshow_BidTagInfo2'];
                    $this->tagInfoBlock($pdf, $info1, $info2, $x, $data, $width);
                }
                if (boolval($this->config['Artshow_BidTagBarcode'])) {
                    $this->tag1DBarcode($pdf, $x, $data, $width, true);
                }
                $this->tagBidTitleRow($pdf, $x, $data, $width);
                for ($i = 0; $i < intval($this->config['Artshow_BidsUntilAuction']); $i ++) {
                    $this->tagBidRow($pdf, $x, $data, $width);
                }
                $pdf->Line($x + $width + 5, 0, $x + $width + 5, $pdf->getPageHeight());
                $count--;
                if ($callback != null) {
                    call_user_func($callback, $count);
                }
            }

            $pdf->Line(0, $pdf->GetY() + 5, $pdf->getPageWidth(), $pdf->GetY() + 5);
            $z++;
        } while ($count > 0);

        return $pdf->Output('', 'S');

    }


    /* end BidTag */
}
