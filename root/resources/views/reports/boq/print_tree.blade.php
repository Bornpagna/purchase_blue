@extends('layouts.app')
@section('stylesheet')
	<link href="{{ asset('assets/global/plugins/jstree/dist/themes/default/style.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/jquery-nestable/jquery.nestable.css') }}" rel="stylesheet" />
	<style>
		td.details-control {
            background: url("{{url("assets/upload/temps/details_open.png")}}") no-repeat center center !important;
            cursor: pointer !important;
        }
        tr.shown td.details-control {
            background: url("{{url("assets/upload/temps/details_close.png")}}") no-repeat center center !important;
        }
        .branch a{
            cursor: pointer;
            
        }
        .boq-items:hover{
            text-decoration: none;
        }
        .dd3-item > button{
            margin-left:0px !important;
        }

        .tree, .tree ul {
            margin:0;
            padding:0;
            list-style:none
        }
        .tree ul {
            margin-left:1em;
            position:relative
        }
        .tree ul ul {
            margin-left:0;
        }
        .tree ul:before {
            content:"";
            display:block;
            width:0;
            position:absolute;
            top:0;
            bottom:0;
            left:0;
            border-left:1px solid
        }
        .tree li {
            margin:0;
            padding:3px;
            line-height:2em;
            color:#369;
            font-weight:700;
            position:relative
        }
        .tree ul li:before {
            content:"";
            display:block;
            width:10px;
            height:0;
            border-top:1px solid;
            margin-top:-1px;
            position:absolute;
            top:1em;
            left:0
        }
        .tree ul li:last-child:before {
            background:#fff;
            height:auto;
            top:1em;
            bottom:0
        }
        .indicator {
            margin-right:5px;
        }
        .tree li a {
            font-size: 13px;
            text-decoration: none;
            color:#369;
        }
        .tree li button, .tree li button:active, .tree li button:focus {
            text-decoration: none;
            color:#369;
            border:none;
            background:transparent;
            margin:0px 0px 0px 0px;
            padding:0px 0px 0px 0px;
            outline: 0;
        }
        h1.report-title{
            font-size:15px !important;
        }
	</style>
@endsection

@section('content')
<div class="invoice">
    @include('reports.header')
    <div class="invoice-items">
        <div class="div-table">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th width="10%" class="all">{{ trans('lang.working_type') }}</th>
                        <th width="10%" class="all">{{ trans('lang.house_type') }}</th>
                        <th width="10%" class="all">{{ trans('lang.house_no') }}</th>
                        <th width="10%" class="all">{{ trans('lang.item_type') }}</th>
                        <th width="10%" class="all">{{ trans('lang.item_code') }}</th>
                        <th width="10%" class="all">{{ trans('lang.item_name') }}</th>
                        <th width="10%" class="all">{{ trans('rep.boq_qty') }}</th>
                        <th width="10%" class="all">{{ trans('rep.add_qty') }}</th>
                        <th width="10%" class="all">{{ trans('lang.units') }}</th>
                        <th width="10%" class="all">{{ trans('lang.price') }}</th>
                        <!-- <th width="10%" class="all">{{ trans('lang.total') }}</th> -->
                    </tr>
                </thead>
                <tbody class="invoice-table-tbody">
                    @foreach($house as $rows)
                        <tr>
                            <td>{{$rows->working_type}}</td>
                            <td>{{$rows->house_type}}</td>
                            <td>{{$rows->house_no}}</td>
                            <td>{{$rows->item_type}}</td>
                            <td>{{$rows->code}}</td>
                            <td>{{$rows->name}}</td>
                            <td>{{$rows->qty_std}}</td>
                            <td>{{$rows->qty_add}}</td>
                            <td>{{$rows->unit}}</td>
                            <td>$ {{$rows->cost}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @include('reports.footer')
</div>
<!-- Modal Varian -->
@endsection()
@section('javascript')
<script type="text/javascript">		
    
    var table = $('#my-table').DataTable();
	$(function(){
		$("#btnBack, #btnCancel").on("click",function(){
			var rounte = $(this).attr('rounte');
			window.location.href=rounte;
		});
        onPrint();
	});
    function onPrint(){        
        $('.invoice').css('display','block');
        var strInvioce=$('.invoice').html();
        var styleInvoice = $('.style-invoice').html();
        var popupWin = window.open('', '_self', 'width=1000,height=800');
        var printInvoice = '<html>';
            printInvoice += '<head>';
            printInvoice += '<title></title>';
            printInvoice += styleInvoice;
            printInvoice += '</head>';
            printInvoice += '<body>';
            printInvoice += strInvioce;
            printInvoice += '</body>';
            printInvoice += '</html>';
        popupWin.document.open();
        popupWin.document.write(printInvoice);
        popupWin.print();            
    }
</script>
@endsection()