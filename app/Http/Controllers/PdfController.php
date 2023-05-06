<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Sale;
use Illuminate\Http\Request;
use \Mpdf\Mpdf as PDF;
use \Picqer\Barcode\BarcodeGeneratorPNG as BarcodeGeneratorPNG;

class PdfController extends Controller
{
    public function showInvoice(Request $request, string $id)
    {
        $type = null;
        $title = null;
        $data = null;

        switch (explode("-", $id)[0]) {
            case 'INV':
                $type = 'sale';
                $title = 'Sale Invoice';
                $data = Sale::where('invoice_id', $id)->first();
                break;
            case 'QUO':
                $type = 'quotation';
                $title = 'Quotation Invoice';
                $data = Quotation::where('invoice_id', $id)->first();
                break;
            default:
                $data = null;
                break;
        }

        if (is_null($data)) return $this->html_to_pdf('<h1>Not found!</h1>', 'not found', 'notfound.pdf');

        $html = view('pdf.invoices.'.$type, [
            'data' => $data,
            'barcode' => $this->generate_barcode_html($data->invoice_id),
            'colors' => $this->get_theme_colors($request)
        ])->render();

        return $this->html_to_pdf($html, $title.' '.$id, $id.'.pdf');
    }

    private function get_theme_colors($request) {
        $theme = $request->input('theme');

        switch ($theme) {
            case 'default':
                return config('pdfthemecolors.invoice.default');
            case 'ctec':
                return config('pdfthemecolors.invoice.ctec');
            default:
                return config('pdfthemecolors.invoice.default');
        }
    }

    private function generate_barcode_html($code) {
        $generator = new BarcodeGeneratorPNG(); 
        return base64_encode($generator->getBarcode($code, $generator::TYPE_CODE_128));
    }

    private function html_to_pdf($html, $title, $filename) {
        $mpdf= new PDF(config('pdf'));

        $this->add_custom_fonts_to_mpdf($mpdf);
    
        $mpdf->SetTitle($title);
        $mpdf->WriteHTML($html);
    
        return $mpdf->Output($filename, 'D');
    }

    private function add_custom_fonts_to_mpdf($mpdf)
    {
        $fontdata = [
            'typewriter' => [
                'R' => 'JMH Typewriter.ttf',
                'B' => 'JMH Typewriter-Bold.ttf'
            ]
        ];

        foreach ($fontdata as $f => $fs) {
            // add to fontdata array
            $mpdf->fontdata[$f] = $fs;
            // add to available fonts array
            foreach (['R', 'B', 'I', 'BI'] as $style) {
                if (isset($fs[$style]) && $fs[$style]) {
                    // warning: no suffix for regular style! hours wasted: 2
                    $mpdf->available_unifonts[] = $f . trim($style, 'R');
                }
            }
        }

        $mpdf->default_available_fonts = $mpdf->available_unifonts;
    }
}
