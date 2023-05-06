<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $data->invoice_id }}</title>
</head>

<style>
    .clearfix:after {
        content: "";
        display: table};
        clear: both;
    }

    a {
        color: #5D6975;
        text-decoration: underline;
    }

    body {
        position: relative;
        width: 21cm;
        height: 29.7cm;
        margin: 0 auto;
        color: {{ $colors['text_color_default'] }};
        background: {{ $colors['foreground_color'] }};
        font-family: Arial, sans-serif;
    
        font-family: Arial;
    }

    header {
        padding: 10px 0;
        margin-bottom: 30px;
    }

    .logo {
        margin-bottom: 10px;
    }

    .logo .img {
        width: 70px;
        border-radius: 50%;
    }

    /*------------*/

    .hr {
        width: 100%;
        height: 2px;
        background-color: {{ $colors['hr_color'] }};
    }

    .br-1 {
        width: 100%;
        height: 2px;
    }

    .br-2 {
        width: 100%;
        height: 4px;
    }

    .br-3 {
        width: 100%;
        height: 6px;
    }

    .br-4 {
        width: 100%;
        height: 10px;
    }

    .br-5 {
        width: 100%;
        height: 15px;
    }

    /*------------*/

    .text-sm-1 {
        font-size: 5px;
    }

    .text-sm-2 {
        font-size: 7px;
    }

    .text-sm-3 {
        font-size: 9px;
    }

    .text-sm-4 {
        font-size: 10px;
    }

    .text-bold {
        font-weight: bold;
    }

    /*------------*/

    .infotable {
        width: 100%;
        margin-bottom: 10px;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .datatable {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
    }

    .datatable th,
    .datatable td {
        text-align: right;
        border: 1px solid {{ $colors['datatable_border_color'] }};
    }

    .datatable th {
        padding: 5px 20px;
        background-color: {{ $colors['datatable_th_background_color'] }};
        color: {{ $colors['datatable_th_text_color'] }};
        white-space: nowrap;
        font-weight: bold;
    }

    .datatable .itcode,
    .datatable .desc {
        text-align: left;
        padding: 6px;
    }

    .datatable .itcode {
        font-weight: bold;
    }

    .datatable td.itcode,
    .datatable td.desc {
        vertical-align: top;
        font-family: typewriter;
        font-size: 1em;
    }

    .datatable td.unit,
    .datatable td.qty,
    .datatable td.disc,
    .datatable td.total {
        padding: 6px;
        font-family: typewriter;
        font-size: 1em;
    }

    .datatable .itcode {
        background-color: {{ $colors['datatable_td_background_color_light'] }};
    }

    .datatable td.unit,
    .datatable td.qty,
    .datatable td.disc {
        background-color: {{ $colors['datatable_td_background_color_light'] }};
        font-weight: bold;
    }

    .datatable td.total {
        background-color: {{ $colors['datatable_td_background_color_dark'] }};
        font-weight: bold;
    }
    .datatable td.desc {
        background-color: {{ $colors['datatable_td_background_color_dark'] }};
    }

    .datatable td {
        padding: 4px;
        text-align: right;
    }

</style>

<body>

    <header class="clearfix">

        <table class="infotable">
            <tr>
                <td>
                    <img style="width: 70px; border-radius: 50%;" src="data:image/jpeg;base64,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">
                </td>
                <td>
                    <h2>&nbsp;CTEC ELECTRICAL</h2>
                </td>
                <td style="width: 10%;"></td>
                <td>
                    <span class="text-sm-4 text-bold">Akuressa, 84400 Matara, Sri Lanka</span>
                    <div class="br-2"></div>
                    <span class="text-sm-4">Tel: (94) 71-0564364</span><br>
                    <span class="text-sm-4">Tel: (94) 77-0564364</span><br>
                    <span class="text-sm-4">Tel: (94) 75-0564364</span>
                </td>
                <td>
                    <span class="text-sm-4">ctechakuressa@gmail.com</span><br>
                    <span class="text-sm-4">info@ctec.lk</span>
                    <div class="br-2"></div>
                    <span class="text-sm-4 text-bold">www.ctec.lk</span>
                </td>
            </tr>
        </table>

        <div class="hr"></div>

        <table class="infotable">
            <tr>
            <td style="width: 50%;">
                <span>Invoice to:</span>
                <div class="br-2"></div>
                <span style="font-size: 30px;">{{ $data->customer()->name }}</span>
                <div class="br-2"></div>
                <span>{{ $data->customer()->address }}</span>
                <div class="br-1"></div>
                <span class="text-bold">{{ $data->customer()->email }}</span>
                <div style="display:{{ is_null($data->title) ? 'none' : 'block'  }};">
                    <p>&nbsp;</p>
                    <span class="text-bold">{{ $data->title }}</span>
                </div>
            </td>
            <td style="width:20%"></td>
            <td style="float: left;">
                <div style="width: 100%; text-align: right;">
                    <span style="font-size: 90px; letter-spacing: 20px;">INVOICE</span>
                    <div style="float: right;">
                        <table class="datatable">
                            <thead>
                                <tr>
                                    <th>Total Due</th>
                                    <th>Due Date</th>
                                    <th>Due Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="text-bold" style="font-size: 18px;">Rs.{{ $data->sale_data()->grand_total() }}</span></td>
                                    <td><span class="text-bold" style="font-size: 15px;">{{ date('Y-m-d', strtotime($data->updated_at)) }}</span></td>
                                    <td><span class="text-bold" style="font-size: 15px;">{{ date('g:i A', strtotime($data->updated_at)) }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </td>
            </tr>
        </table>

        <div class="hr"></div>

    </header>

    <main>

        <table class="datatable">
            <thead>
                <tr style="padding: 10px;">
                    <th style="padding: 8px; text-align: center;">ITCODE</th>
                    <th style="padding: 8px; text-align: center;">DESCRIPTION</th>
                    <th style="padding: 8px; text-align: center;">UNIT PRICE</th>
                    <th style="padding: 8px; text-align: center;">QTY</th>
                    <th style="padding: 8px; text-align: center;">TOTAL</th>
                </tr>
            </thead>
            <tbody>';

            @foreach ($data->sale_data()->get() as $record)
            <tr>
                <td class="itcode">{{ $record->itemcode }}</td>
                <td class="desc">{{ $record->description }}</td>
                <td class="unit">{{ $record->unitprice }}</td>
                <td class="qty">{{ $record->qty }}</td>
                <td class="total">{{ $record->unitprice * $record->qty }}</td>
            </tr>
            @endforeach
        
            </tbody>
        </table>

        <table class="infotable">
            <tr>
                <td>
                    <img style="width: 158px;" src="data:image/jpeg;base64,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">
                </td>
                <td style="width: 100%;">
                    <table class="infotable">
                        <tr>
                            <td style="padding: 10px; text-align: right; background-color: {{ $colors['foreground_color_light'] }};">
                                <div>
                                    <table>
                                        <tr>
                                            <td style="color: {{ $colors['text_color_light'] }}; font-size: 20px;">SUB TOTAL:</td>
                                            <td style="color: {{ $colors['text_color_light'] }}; font-size: 20px;"><strong>&nbsp;&nbsp;Rs.{{ number_format((float)$data->sale_data()->sub_total(), 2, '.', '') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="color: {{ $colors['text_color_light'] }}; font-size: 20px;">TOTAL DISCOUNT:</td>
                                            <td style="color: {{ $colors['text_color_light'] }}; font-size: 20px;"><strong>&nbsp;&nbsp;Rs.{{ number_format((float)$data->sale_data()->total_discount(), 2, '.', '') }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr><td></td></tr>
                        <tr>
                            <td style="padding: 10px; text-align: right; background-color: {{ $colors['foreground_color_dark'] }};">
                                <div>
                                    <span style="color: {{ $colors['text_color_light'] }}; font-size: 25px;">TOTAL:<strong>&nbsp;&nbsp;Rs.{{ number_format((float)$data->sale_data()->grand_total(), 2, '.', '') }}</strong></span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="hr"></div>

    </main>

    <footer>
    
        <table class="infotable" style="border-collapse: separate; border-spacing: 15px;">
            <thead>
                <tr style="padding: 10px;">
                    <th style="font-size: 12px; color: {{ $colors['text_color_light'] }}; background-color: {{ $colors['foreground_color_dark'] }}; padding: 4px; text-align: left;">CTEC Electrical Installation</th>
                    <th style="font-size: 12px; color: {{ $colors['text_color_light'] }}; background-color: {{ $colors['foreground_color_dark'] }}; padding: 4px; text-align: left;">CTEC Electrical Maintenance</th>
                    <th style="font-size: 12px; color: {{ $colors['text_color_light'] }}; background-color: {{ $colors['foreground_color_dark'] }}; padding: 4px; text-align: left;">Electrical Electronic Items Sales</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <ul>
                            <li style="font-size: 10px;">Industrial wiring</li>
                            <li style="font-size: 10px;">Domestic wiring</li>
                            <li style="font-size: 10px;">Panel board fabrication</li>
                        </ul>
                    </td>
                    <td>
                        <ul>
                            <li style="font-size: 10px;">Tea factory machine</li>
                            <li style="font-size: 10px;">Metal crusher</li>
                            <li style="font-size: 10px;">Polythene machine</li>
                        </ul>
                    </td>
                    <td>
                        <ul>
                            <li style="font-size: 10px;">PLC VFD Proximity</li>
                            <li style="font-size: 10px;">Limit switch Temperature control</li>
                            <li style="font-size: 10px;">Contactor Relay etc...</li>
                        </ul>
                    </td>
                </tr>
            <tbody>
        </table>

        <div class="hr"></div>

        <div class="br-4"></div>
    
        <table class="infotable">
            <tr>
                <td style="vertical-align: top;">
                    <span style="font-size: 8px; color: '.text_color_dark_l2.';">Document was created on a computer and is valid without the signature and seal.</span>
                </td>
                <td style="text-align: right;">
                    <img src="data:image/png;base64,{{ $barcode }}">
                    <br>
                    <center>
                        <span style="font-family: typewriter; letter-spacing: 5px;">{{ $data->invoice_id }}</span>
                    </center>
                </td>
            </tr>
        </table>

    </footer>

</body>

</html>