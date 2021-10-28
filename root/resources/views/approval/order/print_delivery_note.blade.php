<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <title>{{$title}}</title>
        <!-- CUSTOMIZE STYLE -->
        <style type="text/css">
            @media print{@page {size: portrait}}
            .paper {
                display: block;
                border: 0px solid #CCC;
                margin: 10px auto;
                padding: 0.1in 0 0 0.1in;
                page-break-after: always;
            }
            .portrait {
                size: portrait;
                width: 8.25in !important;
                height: 11.6in !important;
            }
            .landscape {
                size: landscape;
                width: 11.6in !important;
                height: 8.25in !important;
            }
            table {width: 100%;}
            th,td {font-size: 9px;}
            table td {border: #9e9e9e 1px solid;}
            table th {border: <?= getSetting()->report_header_color; ?>  1px solid;}
            table td,th {padding: 2px;}
            table thead th {
                background-color: <?= getSetting()->report_header_color; ?> !important;
                color: white !important;
            }
            .signature{
                height: 50px !important;
                background-color: white;
                background-position: center;
                background-repeat: no-repeat;
                background-size: contain;
            }
            .clear-LB {
                border-left: white 1px solid !important;
                border-bottom: white 1px solid !important;
            }
            .clear-B {
                border-bottom: white 1px solid !important;
            }
            .clear-all {border: white 0px solid !important;}
            .clear {display: block;padding-bottom: 10px !important;}
            .default-color {color: <?= getSetting()->report_header_color; ?> !important;}
            .default-background-color {background-color: <?= getSetting()->report_header_color; ?> !important;color: white !important;}
            .upper-text {text-transform: uppercase;}
            .size-9 {font-size: 9px !important;}
            table {border-collapse: collapse;}
            .empty-box {background-color: #03a9f473 !important;}
            .white-space {
                padding: 3px !important;
                background-color: white !important;
                color: black !important;
                border-left: white 1px solid !important;
                border-right: white 1px solid !important;
                border-bottom: white 1px solid !important;
            }
            .center {text-align: center !important;}
            .left {text-align: left !important;}
            .right {text-align: right !important;}
            .pad-left-3 {padding-left: 3px !important;}
            .pad-right-3 {padding-right: 3px !important;}
            .pad-2 {padding: 2px !important;}
            .col-3,.col-4,.col-6 {
                position: relative;
                min-height: 1px;
                float: left;
            }
            .col-12 {width: 100% !important;}
            .col-11 {width: 91.666666% !important;}
            .col-10 {width: 83.333333% !important;}
            .col-9 {width: 75% !important;}
            .col-8 {width: 66.666666% !important;}
            .col-7 {width: 58.333333% !important;}
            .col-6 {width: 50% !important;}
            .col-5 {width: 41.666666% !important;}
            .col-4 {width: 33.333333% !important;}
            .col-3 {width: 25% !important;}
            .col-2 {width: 16.666666% !important;}
            .col-1 {width: 8.333333% !important;}
            
            .row:after, .row:before {
                display: table;
                content: " ";
            }
            h3{text-align: center;line-height: 10px;}
            h4{text-align: center;line-height: 2px;}
            h5{text-align: center;line-height: 1px;font-weight: bold;}
            h6{text-align: left;line-height: 2px;}
            .row{display: flex;}
            .horizontal-line{
                margin-top: 2px;
                margin-bottom: 2px;
                border-top: 2px solid <?= getSetting()->report_header_color; ?>;
            }
            .report-header {}
            .report-info {}
            .report-body {margin-bottom: 5px;}
            .title {font-weight: bold;display: block;}
            .center {text-align: center;}
            .info-box{
                display: flex;
                padding: 1px;
                font-size: 12px;
                font-weight: bold;
            }
            .box {
                border: 1px solid #cecece;
                margin: 5px;
                border-radius: 5px;
                font-size: 10px;
            }
            .sq-box {
                border: 1px solid #cecece;
                font-size: 10px;
            }
            .note-box {
                height: 120px !important;
                padding: 3px !important;
            }
            .rounded-box {
                border-radius: 10px !important;
                border: 2px solid #cecece;
                padding: 10px;
                font-size: x-small;
            }
            .white {color: white !important;}
            .primary {color: <?= getSetting()->report_header_color; ?> !important; }
            .bg-white {background-color: white !important;}
            .bg-primary {background-color: <?= getSetting()->report_header_color; ?> !important; }
            .bg-box {
                padding: 2px !important; 
                border-top: 1px solid <?= getSetting()->report_header_color; ?> !important;
                border-left: 1px solid <?= getSetting()->report_header_color; ?> !important;
                border-right: 1px solid <?= getSetting()->report_header_color; ?> !important;
            }
            .print-footer {
                position: fixed;
                bottom: 0;
                font-size: 8px !important;
                right: 0;
                color: grey !important;
                page-break-before: always;
            }
        </style>
    </head>
    <!-- CUSTOMIZE STYLE -->
    <body class="paper portrait">
        <!-- Header -->
        <div class="report-header">
            <div class="col-12">
                <div class="row">
                    <!-- LOGO -->
                    <div class="col-6">
                        <div class="left">
                            <img src="{{asset('assets/upload/temps/app_icon.png')}}" width="100" />
                        </div>
                    </div>
                    <!-- NAME -->
                    <div class="col-6">
                        <h3 class="default-color right">{{getSetting()->report_header}}</h3>
                        <h5 class="right">{{getSetting()->company_name}}</h5>
                        <h5 class="right">{{getSetting()->company_address}}</h5>
                        <h5 class="right">{{getSetting()->company_tel}}</h5>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="horizontal-line"></div>
            </div>
        </div>
        <!-- Info -->
        <div class="report-info">
            <div class="col-12">
                <span class="title default-color upper-text center">{{$title}}</span>
            </div>
        </div>
        <div class="clear"></div>
        <div class="report-body">
            <!-- Order info -->
            <div class="row">
                <!-- Supplier -->
                @if($supplier)
                <div class="col-6">
                    <div class="rounded-box" style="margin-right: 2px;">
                        <div class="col-12 left"><strong> {{trans('lang.supplier')}}</strong></div>
                        <!-- Company Name -->
                        <div class="col-3 left">- {{trans('lang.name')}}</div>
                        <div class="col-9 left">: <strong>{{$supplier->name}}</strong></div>
                        <!-- Cheque Name -->
                        <div class="col-3 left">- {{trans('lang.cheque_name')}}</div>
                        <div class="col-9 left">: <strong>{{$supplier->desc}}</strong></div>
                        <!-- Phone -->
                        <div class="col-3 left">- {{trans('lang.tel')}}</div>
                        <div class="col-9 left">: <strong>{{$supplier->tel}}</strong></div>
                        <!-- Email -->
                        <div class="col-3 left">- {{trans('lang.email')}}</div>
                        <div class="col-9 left">: <strong>{{$supplier->email}}</strong></div>
                        <!-- Address -->
                        <div class="col-3 left">- {{trans('lang.address')}}</div>
                        <div class="col-9 left">: <strong>{{$supplier->address}}</strong></div>
                    </div>
                </div>
                @endif
                <!-- Order -->
                @if($order)
                <div class="col-6">
                    <div class="rounded-box" style="margin-left: 2px;">
                        <div class="col-12 left"><strong> {{trans('lang.information')}}</strong></div>
                        <!-- Company Name -->
                        <div class="col-3 left">- {{trans('lang.project')}}</div>
                        <div class="col-9 left">: <strong>{{$order->project}}</strong></div>
                        <!-- Cheque Name -->
                        <div class="col-3 left">- {{trans('lang.date')}}</div>
                        <div class="col-9 left">: <strong>{{date('d/m/Y',strtotime($order->trans_date))}}</strong></div>
                        <!-- Phone -->
                        <div class="col-3 left">- {{trans('lang.po_number')}}</div>
                        <div class="col-9 left">: <strong>{{$order->ref_no}}</strong></div>
                        <!-- Email -->
                        <div class="col-3 left">- {{trans('lang.pr_number')}}</div>
                        <div class="col-9 left">: <strong>{{$order->pr_no}}</strong></div>
                        <!-- Address -->
                        <div class="col-3 left">- {{trans('lang.department')}}</div>
                        <div class="col-9 left">: <strong>{{$order->department}}</strong></div>
                    </div>
                </div>
                @endif
            </div>
            <span class="clear"></span>
            <!-- List items -->
            <div class="row">
                @if($orderItems)
                <table>
                    <thead>
                        <tr>
                            <th width="3%" class="all">{{trans("lang.line_no")}}</th>
                            <th width="44%" class="all">{{trans("lang.product_code_name")}}</th>
                            <th width="7%" class="all">{{trans("lang.qty")}}</th>
                            <th width="7%" class="all">{{trans("lang.units")}}</th>
                            {{-- <th width="6%" class="all">{{trans("lang.price")}}</th>
                            <th width="8%" class="all">{{trans("lang.discount")}}(%)</th>
                            <th width="8%" class="all">{{trans("lang.discount")}}($)</th>
                            <th width="7%" class="all">{{trans("lang.total")}}</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                    <?php $index = 0; ?>
                    @foreach($orderItems as $row)
                        <?php $index++; ?>
                        <tr>
                            <td class="center">{{$index}}</td>
                            <!-- Item Code -->
                            <td>{{$row->item_name}} <strong>({{$row->item_code}})</strong></td>
                            <!-- Qty -->
                            <td class="right">{{formatQty($row->qty)}}</td>
                            <!-- Unit -->
                            <td class="center">{{$row->unit}}</td>
                            <!-- Price -->
                            {{-- <td class="right">{{formatDollars($row->price)}}</td>
                            <!-- Discount(%) -->
                            <td class="right">{{$row->disc_perc}}</td>
                            <!-- Discount($) -->
                            <td class="right">{{formatDollars($row->disc_usd)}}</td>
                            <!-- Total -->
                            <td class="right">{{formatDollars($row->total)}}</td> --}}
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @endif
            </div>
            <div class="clear"></div>
            <!-- Note & Prepare & Term Pay & Total -->
            <div class="col-12">
                <div class="row">
                    <!-- Note -->
                    <div class="col-12">
                        <div class="sq-box note-box">
                            <label class="upper-text bold">{{trans('lang.note')}} : </label>
                            <div class="size-9"> ** {{trans('lang.term_of_payment')}} : {{$order->term_pay}}</div>
                            <div class="size-9">{{$order->note}}</div>
                        </div>
                    </div>
                    <!-- Prepare & Term Pay -->
                    {{-- <div class="col-3">
                        <div style="display: grid;font-size: 10px;">
                            <span class="center">{{trans('lang.request_by')}}</span>
                            <div class="clear"></div>
                            @if(isset($order->signature))
                            <div class="center">
                            <div class="signature" style="background-image:url({{asset('assets/upload/picture/signature')}}/{{$order->signature}});"></div>
                            </div>
                            @else
                            <div class="clear"></div>
                            <div class="clear"></div>
                            <div class="clear"></div>
                            <div class="clear"></div>
                            <div class="clear"></div>
                            @endif
                            <span class="center">{{$order->position}}  <strong>{{$order->user_request}}</strong></span>
                            <span class="center">{{trans('lang.date')}} : {{date('d/m/Y',strtotime($order->trans_date))}}</span>
                        </div>
                    </div> --}}
                    <!-- Total -->
                    {{-- <div class="col-2">
                        <!-- Subtotal -->
                        <div class="col-12">
                            <div class="row">
                                <div class="col-6 right upper-text size-9 primary pad-2 bold">{{trans('lang.subtotal')}}</div>
                                <div class="col-6 right size-9 white bg-box bg-primary">{{formatDollars(isset($order->sub_total) ? $order->sub_total : 0)}}</div>
                            </div>
                        </div>
                        <!-- Deposit -->
                        <div class="col-12">
                            <div class="row">
                                <div class="col-6 right upper-text size-9 primary pad-2 bold">{{trans('lang.deposit')}}</div>
                                <div class="col-6 right size-9 black bg-box bg-white">{{formatDollars(isset($order->deposit) ? $order->deposit : 0)}}</div>
                            </div>
                        </div>
                        <!-- Discount -->
                        <div class="col-12">
                            <div class="row">
                                <div class="col-6 right upper-text size-9 primary pad-2 bold">{{trans('lang.discount')}}</div>
                                <div class="col-6 right size-9 black bg-box bg-white">{{formatDollars(isset($order->discount) ? $order->discount : 0)}}</div>
                            </div>
                        </div>
                        <!-- VAT Tax -->
                        <div class="col-12">
                            <div class="row">
                                <div class="col-6 right upper-text size-9 primary pad-2 bold">{{trans('lang.vat_tax')}}</div>
                                <div class="col-6 right size-9 black bg-box bg-white">{{formatDollars(isset($order->tax) ? $order->tax : 0)}}</div>
                            </div>
                        </div>
                        <!-- Shipping -->
                        <div class="col-12">
                            <div class="row">
                                <div class="col-6 right upper-text size-9 primary pad-2 bold">{{trans('lang.shipping')}}</div>
                                <div class="col-6 right size-9 black bg-box bg-white">{{formatDollars(isset($order->fee_charge) ? $order->fee_charge : 0)}}</div>
                            </div>
                        </div>
                        <!-- Total -->
                        <div class="col-12">
                            <div class="row">
                                <div class="col-6 right upper-text size-9 primary pad-2 bold">{{trans('lang.total')}}</div>
                                <div class="col-6 right size-9 white bg-box bg-primary">{{formatDollars(isset($order->grand_total) ? $order->grand_total : 0)}}</div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
            <!-- Signature -->
            <div class="clear"></div>
            <div class="col-12">
                <div class="row">
                    <!-- Prepared By -->
                    @if($order)
                    <div class="col-3">
                        <div style="display: grid;font-size: 10px;">
                            <span class="center">{{trans('lang.prepared_by')}}</span>
                            <div class="clear"></div>
                            @if(isset($order->ordered_signature))
                            <div class="center">
                            <div class="signature" style="background-image:url({{asset('assets/upload/picture/signature')}}/{{$order->ordered_signature}});"></div>
                            </div>
                            @else
                            <div class="clear"></div>
                            <div class="clear"></div>
                            <div class="clear"></div>
                            <div class="clear"></div>
                            <div class="clear"></div>
                            @endif
                            <span class="center">{{$order->order_position}}  <strong>{{$order->ordered_user}}</strong></span>
                            <span class="center">{{trans('lang.date')}} : {{date('d/m/Y',strtotime($order->trans_date))}}</span>
                        </div>
                    </div>
                    @endif
                    @if($orderApproved)
                        @foreach($orderApproved as $approveMan)
                        <!-- Engineer -->
                        <div class="col-3">
                            <div style="display: grid;font-size: 10px;">
                                <span class="center">{{trans('lang.approved_by')}}</span>
                                <div class="clear"></div>
                                @if(isset($approveMan->signature))
                                <div class="center">
                                    <div class="signature" style="background-image:url({{asset('assets/upload/picture/signature')}}/{{$approveMan->signature}});"></div>
                                </div>
                                @else
                                <div class="clear"></div>
                                <div class="clear"></div>
                                <div class="clear"></div>
                                <div class="clear"></div>
                                <div class="clear"></div>
                                @endif
                                <span class="center">{{$approveMan->position}}  <strong>{{$approveMan->approved_people}}</strong></span>
                                <span class="center">{{trans('lang.date')}} : {{date('d/m/Y',strtotime($approveMan->approved_date))}}</span>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <div class="print-footer">
            <label>{{trans('lang.document_num')}} : {{$order->ref_no}} | {{trans('lang.page')}} : 1</label>
        </div>
    </body>
    <script>
        onload = function(){
            window.print();
        }
    </script>
</html>