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

<div class="row">
    <div class="col-md-12">
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="{{$icon}} font-dark"></i>
					<span class="caption-subject bold font-dark uppercase"> {{$title}}</span>
					<span class="caption-helper">{{$small_title}}</span>
				</div>				
			</div>
            <div class="portlet-body">
                <div class="row" style="margin:0px;">
                    <div class="portlet light bordered well col-md-2">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-social-dribbble font-purple-soft"></i>
                                <span class="caption-subject font-purple-soft bold uppercase"> Tree Boq </span>
                            </div>                                
                        </div>
                        <ul id="tree1">
                            {!! $li_html !!}                                    
                        </ul>
                    </div>                    
                    <div class="col-md-10">
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="icon-social-dribbble font-purple-soft"></i>
                                    <span class="caption-subject font-purple-soft bold uppercase"> BOQ & ITEM </span>
                                </div>
                                <div class="actions actionPD">
                                    <!-- <a title="{{trans('lang.print')}}" onclick="onPrint(this);" version="print" class="btn btn-circle btn-icon-only btn-default">
                                        <i class="fa fa-print"></i>
                                    </a>
                                    <a title="{{trans('lang.download')}}" onclick="onPrint(this);" version="excel"  class="btn btn-circle btn-icon-only btn-default">
                                        <i class="fa fa-file-excel-o"></i>
                                    </a> -->
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="div-table">
                                    <table class="table table-striped table-bordered table-hover dt-responsive" id="my-table">
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
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>
<!-- Modal Varian -->
@endsection()
@section('javascript')
<script type="text/javascript">		
    $.fn.extend({
        treed: function (o) {
        
        var openedClass = 'glyphicon-minus-sign';
        var closedClass = 'glyphicon-plus-sign';
        
        if (typeof o != 'undefined'){
            if (typeof o.openedClass != 'undefined'){
            openedClass = o.openedClass;
            }
            if (typeof o.closedClass != 'undefined'){
            closedClass = o.closedClass;
            }
        };      
            //initialize each of the top levels
            var tree = $(this);
            tree.addClass("tree");
            tree.find('li').has("ul").each(function () {
                var branch = $(this); //li with children ul
                branch.prepend("<i class='indicator glyphicon " + closedClass + "'></i>");
                branch.addClass('branch');
                branch.on('click', function (e) {             
                    if (this == e.target) {
                        var icon = $(this).children('i:first');
                        icon.toggleClass(openedClass + " " + closedClass);
                        $(this).children().children().toggle();
                    }
                })
                branch.children().children().toggle();
            });
            //fire event from the dynamically added icon
        tree.find('.branch .indicator').each(function(){
            $(this).on('click', function () {
                $(this).closest('li').click();
            });
        });
            //fire event to open branch if the li contains an anchor instead of text
            tree.find('.branch>a').each(function () {
                $(this).on('click', function (e) {
                    $(this).closest('li').click();
                    e.preventDefault();
                });
            });
            //fire event to open branch if the li contains a button instead of text
            tree.find('.branch>button').each(function () {
                $(this).on('click', function (e) {
                    $(this).closest('li').click();
                    e.preventDefault();
                });
            });
        }
    });
    //Initialization of treeviews

    $('#tree1').treed();

    $('#tree2').treed({openedClass:'glyphicon-folder-open', closedClass:'glyphicon-folder-close'});

    $('#tree3').treed({openedClass:'glyphicon-chevron-right', closedClass:'glyphicon-chevron-down'});
    var table = $('#my-table').DataTable();
	$(function(){
		$("#btnBack, #btnCancel").on("click",function(){
			var rounte = $(this).attr('rounte');
			window.location.href=rounte;
		});
        
	});
    function geBOQFromHouse (house_id){
        $('.actionPD').html();
        var str='<a title="{{trans('lang.print')}}" onclick="onPrint('+house_id+',1);" version="print" class="btn btn-circle btn-icon-only btn-default" style="margin-right: 5px;">';
                str+='<i class="fa fa-print"></i>';
            str+='</a>';
            str+='<a title="{{trans('lang.download')}}" onclick="onPrint('+house_id+',2);" version="excel"  class="btn btn-circle btn-icon-only btn-default">';
                str+='<i class="fa fa-file-excel-o"></i>';
            str+='</a>';
        $('.actionPD').html(str);
        table.clear().draw();
        $.get('{{url("report/boqTreeView/getBoq")}}?house_id='+house_id,function(val){        
            $.each(val,function(index,DataRow){               
                table.row.add([
                    DataRow.working_type,
                    DataRow.house_type,
                    DataRow.house_no,
                    DataRow.item_type,
                    DataRow.code,
                    DataRow.name,
                    DataRow.qty_std,
                    DataRow.qty_add,
                    DataRow.unit,
                    formatDollar(DataRow.cost),
                    // formatDollar((parseFloat(DataRow.qty_std+DataRow.qty_add)*parseFloat(DataRow.cost))),
                ]).draw();
            });
            // var strtb = '';
            // $.each(val,function(index,DataRow){
            //     strtb+='<tr>';
            //         strtb+='<td style="text-align: center;">'+DataRow.working_type+'</td>';
            //         strtb+='<td style="text-align: center;">'+DataRow.house_type+'</td>';
            //         strtb+='<td style="text-align: center;">'+DataRow.house_no+'</td>';
            //         strtb+='<td style="text-align: center;">'+DataRow.item_type+'</td>';
            //         strtb+='<td style="text-align: center;">'+DataRow.code+'</td>';
            //         strtb+='<td style="text-align: center;">'+DataRow.name+'</td>';
            //         strtb+='<td style="text-align: center;">'+DataRow.qty_std+'</td>';
            //         strtb+='<td style="text-align: center;">'+DataRow.qty_add+'</td>'; 
            //         strtb+='<td style="text-align: center;">'+DataRow.unit+'</td>'; 
            //         strtb+='<td style="text-align: center;">'+formatDollar(DataRow.cost)+'</td>'; 
            //     strtb+='</tr>';
            // });
            // $('.invoice-table-tbody').html();
            // $('.invoice-table-tbody').html(strtb);
        });
    }
    function onPrint(house_id,condition){        
        // $('.invoice').css('display','block');
        if(condition==1){
            // var strInvioce=$('.invoice').html();
            // var styleInvoice = $('.style-invoice').html();
            // var popupWin = window.open('', '_self', 'width=1000,height=800');
            // var printInvoice = '<html>';
            //     printInvoice += '<head>';
            //     printInvoice += '<title></title>';
            //     printInvoice += styleInvoice;
            //     printInvoice += '</head>';
            //     printInvoice += '<body>';
            //     printInvoice += strInvioce;
            //     printInvoice += '</body>';
            //     printInvoice += '</html>';
            // popupWin.document.open();
            // popupWin.document.write(printInvoice);
            // popupWin.print()
            window.open('{{url("report/boqTreeView/getBoqprint")}}?export=1&house_id='+house_id);
        }else{
            window.location.href ='{{url("report/boqTreeView/getBoqexport")}}?export=1&house_id='+house_id;
        }        
    }
    window.onafterprint = function(){
        window.close();
        $('.invoice').css('display','none');
    }
</script>
@endsection()