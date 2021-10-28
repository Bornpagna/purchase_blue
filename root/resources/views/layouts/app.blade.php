<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{getSetting()->app_name}} | {{$title}}</title>
	<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<link href="{{ asset('assets/kh-font/google.font.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/morris/morris.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
	<link href="{{ asset('assets/global/plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/icheck/skins/all.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/css/components-square.min.css') }}" rel="stylesheet" id="style_components" type="text/css" />
	<link href="{{ asset('assets/global/css/plugins.min.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ asset('assets/jquery-confirm/css/jquery-confirm.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ asset('assets/layouts/layout/css/layout.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/layouts/layout/css/themes/darkblue.min.css') }}" rel="stylesheet" type="text/css" id="style_color" />
	<link href="{{ asset('assets/layouts/layout/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/flags/flags.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/fancybox/source/jquery.fancybox.css') }}" rel="stylesheet"  type="text/css" media="screen" />
	<link href="{{ asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{asset('assets/global/plugins/bootstrap-toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
	<link type="text/css" rel="stylesheet" href="{{asset('assets/loading/waitMe.css')}}">
	<link rel="shortcut icon" href="{{url('assets/upload/temps/app_icon.png')}}" />
	<style type="text/css">
		.page-content {background-color: #F3F3F3 !important;}
		@font-face {
		   font-family: myKhHanuman;
		   src: url('{{ asset('assets/kh-font/Hanuman.woff') }}');
		}
		@font-face {
		   font-family: myKhFreehand;
		   src: url('{{ asset('assets/kh-font/Kh-Freehand.woff') }}');
		}
		@font-face {
		   font-family: myKhBattambang;
		   src: url('{{ asset('assets/kh-font/KhmerOSbattambang.woff') }}');
		}
		@font-face {
		   font-family: myKhMetal;
		   src: url('{{ asset('assets/kh-font/Kh-Metal-Chrieng.woff') }}');
		}
		body,h1,h2,h3,h4,h5,h6,a{
			font-family:"Open Sans",sans-serif,myKhBattambang!important;
		}
		.modal-header, .modal-footer{
			background-color: <?php echo getSetting()->modal_header_color ?>;
		}
		.modal-title {
			color: <?php echo getSetting()->modal_title_color ?>;
		}
		.help-block{
			font-size:12px;
		}
		.select2-container .select2-selection--single {
            box-sizing: border-box;
            cursor: pointer;
            display: block;
            height: 34px !important;
            user-select: none;
            -webkit-user-select: none;
        }
        .select2-container--classic .select2-selection--single .select2-selection__arrow {
            background-color: #ddd;
            border: none;
            border-left: 1px solid #aaa;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
            height: 32px !important;
            position: absolute;
            top: 1px;
            right: 1px;
            width: 20px;
            background-image: -webkit-linear-gradient(top, #eeeeee 50%, #cccccc 100%);
            background-image: -o-linear-gradient(top, #eeeeee 50%, #cccccc 100%);
            background-image: linear-gradient(to bottom, #eeeeee 50%, #cccccc 100%);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#eeeeee', endColorstr='#cccccc', GradientType=0);
        }
		.select2-container--classic .select2-selection--single .select2-selection__rendered {
            color: #444 !important;
            line-height: 31px !important;
        }
        .select2-search__field{
        	width: 100% !important;
        }
		.select2-results__group{
			padding: 6px 6px !important;
			font-family:"Open Sans",sans-serif,myKhBattambang !important;
			color: #000 !important;
		}
		.select2-results__options--nested > .select2-results__option{
			padding: 6px 16px !important;
		}
	</style>
	@yield('stylesheet')
	</head>
	<!-- END HEAD -->
</head>
<body class="<?php if(getSetting()->page_header=="fixed"){echo "page-header-fixed";}?> <?php if(getSetting()->sidebar_position=="right"){echo "page-sidebar-reversed";}?> <?php if(getSetting()->page_footer=="fixed"){echo "page-footer-fixed";}?> <?php if(getSetting()->sidebar_mode=="fixed"){echo "page-sidebar-fixed";}?> {{$background}} page-content-white page-sidebar-closed-hide-logo <?php if(getSetting()->theme_layout=="boxed"){echo "page-boxed";}?> <?php if(Session::get('sidebar')){echo "page-sidebar-closed";}?>">
    <!-- BEGIN HEADER -->
    @include('layouts.header')
    <!-- END HEADER -->
    <!-- BEGIN HEADER & CONTENT DIVIDER -->
    <div class="clearfix"> </div>
    <!-- END HEADER & CONTENT DIVIDER -->
	@if(getSetting()->theme_layout=="boxed")
	<div class="container">
	@endif
	<!-- BEGIN CONTAINER -->
    <div class="page-container">
		<div class="page-sidebar-wrapper">
        @if(Session::get('project') != NULL)
			<!-- BEGIN SIDEBAR -->
			@include('layouts.navigator')
			<!-- BEGIN SIDEBAR -->
		@endif
		</div>
    <!-- END CONTAINER -->
	<!-- BEGIN CONTENT -->
		<div class="page-content-wrapper">
			<!-- BEGIN CONTENT BODY -->
			<div class="page-content">
				<!-- BEGIN PAGE BAR -->
				<div class="page-bar">
					<ul class="page-breadcrumb">
					<?php if(count($link)>0): ?>
						<?php
							foreach($link as $row){
								echo "<li>";
								if(isset($row['url'])){
									echo '<a href="'.$row['url'].'">'.$row['caption'].'</a>'.
										 '<i class="fa fa-circle"></i>';
								}else{
									echo '<span>'.$row['caption'].'</span>';
								}
								echo "</li>";
							}
						?>
					<?php endif; ?>
					</ul>
					<div class="page-toolbar"><!-- dashboard-report-range -->
						<div id="reportrange" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
							<i class="icon-calendar"></i>&nbsp;
							<span class="thin uppercase hidden-xs"></span>&nbsp;
							<i class="fa fa-angle-down"></i>
						</div>
					</div>
				</div>
				<!-- END PAGE BAR -->
				<!-- BEGIN PAGE TITLE-->
				<br/>
				<!-- END PAGE TITLE-->
				<!-- END PAGE HEADER-->
				@yield('content')
			</div>
			<!-- END CONTENT BODY -->
		</div>
		<!-- END CONTENT -->
    </div>
	<!-- BEGIN FOOTER -->
	@include('layouts.footer')
    <!-- END FOOTER -->
	@if(getSetting()->theme_layout=="boxed")
		<div class="scroll-to-top">
			<i class="icon-arrow-up"></i>
		</div>
	</div>
	@endif
	<script src="{{ asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/format-date/date.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/js.cookie.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/jquery.blockui.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/moment.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/morris/morris.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/counterup/jquery.waypoints.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/counterup/jquery.counterup.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/scripts/app.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/pages/scripts/dashboard.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/pages/scripts/table-datatables-responsive.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/jquery-confirm/js/jquery-confirm.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/icheck/icheck.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/bootstrap-markdown/js/bootstrap-markdown.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.js')}}" type="text/javascript"></script>
	<script src="{{ asset('assets/pages/scripts/ui-modals.min.js')}}" type="text/javascript"></script>
	<script src="{{ asset('assets/upload-img/bootstrap-fileupload.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.'.(config('app.locale')!='kh' ? 'en-GB' : 'kh').'.min.js') }}"></script>
	<script src="{{ asset('assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/fancybox/source/jquery.fancybox.pack.js')}}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/global/plugins/bootstrap-toastr/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('assets/loading/waitMe.js')}}"></script>
	<script src="https://github.com/wasikuss/select2-multi-checkboxes/tree/select2-3.5.x"></script>
	<script type="text/javascript">
		window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
	
		var path_asset="{{ url('') }}/assets/";
		var path_base="{{ url('') }}";
		App.setAssetsPath(path_asset);
		App.setBaseUrl(path_base);
		
		$(function(){
			@if(getSetting()->sidebar_position == "right")
				$('#frontend-link').tooltip('destroy').tooltip({
					placement: 'right'
				});
			@else
				$('#frontend-link').tooltip('destroy').tooltip({
					placement: 'left'
				});
			@endif
		});

		showLoading = function(){
			$('body').waitMe({
				effect : 'ios',
				text : "{{trans('lang.loading')}}",
			});
		}

		stopLoading = function(){
			$('body').waitMe('hide');
		}
		
		function getExtension(file) {
			return file.split('.').pop();
		}

		
		function formatDate(str){
			var date = new Date(str)
			var form = '{{getSetting()->format_date}}';
			
			/* format for day */
			if(form.indexOf("dd") >= 0){
				form = form.replace('dd', lineNo(date.getDate(), 2));
			}
			/* format for month */
			if(form.indexOf("mm") >= 0){
				form = form.replace('mm', lineNo((date.getMonth() + 1), 2));
			}
			/* format for year */
			if(form.indexOf("yyyy") >= 0){
				form = form.replace('yyyy', date.getFullYear());
			}
			return form;
		}
		
		function chkDublicateCode(obj, code, code_old = null){
			var isValid = true;
			if (obj) {
				if (code_old) {
					$.each(obj.filter(a => a.code==$(code).val() && a.code!=$(code_old).val()),function(key,val){
						if (val) {
							$(code).css('border','1px solid #e43a45');
							$(code).next('.help-block').html("{{trans('lang.field_unique')}}");
							isValid = false;
						}else{
							$(code).css('border','1px solid #c2cad8');
							$(code).next('.help-block').empty();
						}
					});
				}else{
					$.each(obj.filter(a => a.code==$(code).val()),function(key,val){
						if (val) {
							$(code).css('border','1px solid #e43a45');
							$(code).next('.help-block').html("{{trans('lang.field_unique')}}");
							isValid = false;
						}else{
							$(code).css('border','1px solid #c2cad8');
							$(code).next('.help-block').empty();
						}
					});
				}
			}
			return isValid;
		}
		
		function chkDublicateName(obj, name, name_old = null){
			var isValid = true;
			if (obj) {
				if (name_old) {
					$.each(obj.filter(a => a.name==$(name).val() && a.name!=$(name_old).val()),function(key,val){
						if (val){
							$(name).css('border','1px solid #e43a45');
							$(name).next('.help-block').html("{{trans('lang.field_unique')}}");
							isValid = false;
						}else{
							$(name).css('border','1px solid #c2cad8');
							$(name).next('.help-block').empty();
						}
					});
				}else{
					$.each(obj.filter(a => a.name==$(name).val()),function(key,val){
						if (val) {
							$(name).css('border','1px solid #e43a45');
							$(name).next('.help-block').html("{{trans('lang.field_unique')}}");
							isValid = false;
						}else{
							$(name).css('border','1px solid #c2cad8');
							$(name).next('.help-block').empty();
						}
					});
				}
			}
			return isValid;
		}

		function chkDublicateRef(obj, code){
			var isValid = true;
			if (obj) {
				$.each(obj.filter(a => a.ref_no==$(code).val()),function(key,val){
					if (val) {
						$(code).css('border','1px solid #e43a45');
						$(code).next('.help-block').html("{{trans('lang.field_unique')}}");
						isValid = false;
					}else{
						$(code).css('border','1px solid #c2cad8');
						$(code).next('.help-block').empty();
					}
				});
			}
			return isValid;
		}

		function chkReference(obj, ref){
			var isValid = true;
			if (obj) {
				$.each(obj.filter(a => a.reference==$(ref).val()),function(key,val){
					if (val) {
						$(ref).css('border','1px solid #e43a45');
						$(ref).next('.help-block').html("{{trans('lang.field_unique')}}");
						isValid = false;
					}else{
						$(ref).css('border','1px solid #c2cad8');
						$(ref).next('.help-block').empty();
					}
				});
			}
			return isValid;
		}
		
		function chkValid(arr){
			var isValid = true;
			if(arr.length > 0){
				$.each(arr,function(key,value){
					$(value).each(function(k,v){
						var len = $(this).attr('length');
						var val = $(this).val();
						// console.log(this);
						if(val == null || val =='' || typeof val == undefined){
							$(this).css('border','1px solid #e43a45');
							$(this).next().find('.select2-selection').css('border','1px solid #e43a45');
							$(this).next('.help-block').html("{{trans('lang.field_required')}}");
							$(this).next().next('.help-block').html("{{trans('lang.field_required')}}");
							$(this).parent(".md-editor").next('.help-block').html("{{trans('lang.field_required')}}");
							isValid = false;
						}else if(typeof len != undefined && len!='' && parseInt(val.length) > parseInt(len)){
							$(this).css('border','1px solid #e43a45');
							$(this).next().find('.select2-selection').css('border','1px solid #e43a45');
							$(this).next('.help-block').html("{{trans('lang.max_length')}} " + $(this).attr('length'));
							$(this).next().next('.help-block').html("{{trans('lang.field_required')}}");
							$(this).parent(".md-editor").next('.help-block').html("{{trans('lang.max_length')}} " + $(this).attr('length'));
							isValid = false;
						}else{
							$(this).css('border','1px solid #c2cad8');
							$(this).next().find('.select2-selection').css('border','1px solid #c2cad8');
							$(this).next().next().find('.select2-selection').css('border','1px solid #c2cad8');
							$(this).next().next('.help-block').empty();
							$(this).parent(".md-editor").next('.help-block').empty();
							$(this).next('.help-block').empty();
						}
					});
				});
			}
			
			return isValid;
		}
		
		// function onUploadExcel(){
        //     var isValid = true;
        //     var file = $('#excel').val();
		// 	console.log(file);
        //     if (file==null || file=='' || file==undefined) {
        //         isValid = false;
        //         $('.excel').attr('style','border : 1px solid #e43a45 !important;');
        //         $('.error-excel').html("{{trans('lang.doc_required')}}");
        //     }else{
        //         var exe = file.split('.').pop();
        //         if (exe.toUpperCase()!='CSV' && exe.toUpperCase()!='XLS' && exe.toUpperCase()!='XLSX') {
        //             isValid = false;
        //             $('.excel').attr('style','border : 1px solid #e43a45 !important;');
        //             $('.error-excel').html("{{trans('lang.excel_mimes')}}");
        //         }else{
        //             $('.excel').attr('style','border : 1px solid #c2cad8 !important;');
        //             $('.error-excel').html("");
        //         }
        //     }
        //     return isValid;
        // }
		
		function getLanguage(val){
			var locale = val;
			var _token = $("input[name=_token]").val();
			$.ajax({
				url: "{{ asset('/language') }}",
				type: 'POST',
				data: {'locale': locale, '_token': _token},
				datatype: 'json',
				success: function(data){},
				error: function(data){},
				beforeSend: function(){},
				complete: function(data){
					window.location.reload(true);
				}
			});
		}
		
		function onUploadExcel(){
            var isValid = true;
            var file = $('#excel').val();
			console.log(file);
            if (file==null || file=='' || file==undefined) {
                isValid = false;
                $('.excel').attr('style','border : 1px solid #e43a45 !important;');
                $('.error-excel').html("{{trans('lang.doc_required')}}");
            }else{
                var exe = file.split('.').pop();
                if (exe.toUpperCase()!='CSV' && exe.toUpperCase()!='XLS' && exe.toUpperCase()!='XLSX') {
                    isValid = false;
                    $('.excel').attr('style','border : 1px solid #e43a45 !important;');
                    $('.error-excel').html("{{trans('lang.excel_mimes')}}");
                }else{
                    $('.excel').attr('style','border : 1px solid #c2cad8 !important;');
                    $('.error-excel').html("");
                }
            }
            return isValid;
        }
		function onUploadExcelRevise($name){
            var isValid = true;
            var file = $('#excel_'+$name).val();
            if (file==null || file=='' || file==undefined) {
                isValid = false;
                $('.excel').attr('style','border : 1px solid #e43a45 !important;');
                $('.error-excel').html("{{trans('lang.doc_required')}}");
            }else{
                var exe = file.split('.').pop();
                if (exe.toUpperCase()!='CSV' && exe.toUpperCase()!='XLS' && exe.toUpperCase()!='XLSX') {
                    isValid = false;
                    $('.excel').attr('style','border : 1px solid #e43a45 !important;');
                    $('.error-excel').html("{{trans('lang.excel_mimes')}}");
                }else{
                    $('.excel').attr('style','border : 1px solid #c2cad8 !important;');
                    $('.error-excel').html("");
                }
            }
            return isValid;
        }
		function setThemeStyle(field,value){
			var _token = $("input[name=_token]").val();
			$.ajax({
				url: "{{ url('/theme') }}",
				type: 'POST',
				data: {
					'_token': _token,
					'field'	: field,
					'value'	: value,
				},
				datatype: 'json',
				success: function(data){}
			});
		}
		
		function setSidebar(){
			var _token = $("input[name=_token]").val();
			$.ajax({
				url: "{{ url('/sidebar') }}",
				type: 'POST',
				data: {'_token': _token},
				datatype: 'json',
				success: function(data){
					console.log(data);
				}
			});
		}
		
		function formatDollar(total) {
			var neg = false;
			if(total < 0) {
				neg = true;
				total = Math.abs(total);
			}
			return (neg ? "-$" : '$') + parseFloat(total, 10).toFixed('{{getSetting()->round_dollar}}').replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
		}
		
		function formatNumber(total) {
			var neg = false;
			if(total < 0) {
				neg = true;
				total = Math.abs(total);
			}
			return (neg ? "-" : '') + parseFloat(total, 10).toFixed('{{getSetting()->round_number}}').replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
		}
		
		function lineNo(str, max) {
			str = str.toString();
			return str.length < max ? lineNo("0" + str, max) : str;
		}
		
		function convertQuot(argument) {
			return argument.replace(/&quot;/g, '\"');
		}

		function validateEmail(email) {
			var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return re.test(email);
		}

		function isNumbers(text){
			if (/^[0-9]{1,10}$/.test(+text)){
			  return true;
			}else{
			  return false;
			}
		}
	
		function arraySearch(arr,val) {
			for (var i=0; i<arr.length; i++)
				if (arr[i] === val)                    
					return i;
		}
		
		function isNullToZero(val){
			if(val==null || val=='' || typeof val ==undefined){
				return 0;
			}
			return parseFloat(val.replace("$","").replace(",",""));
		}
		
		function isNullToString(val){
			if(val==null || val=='' || typeof val ==undefined){
				return "";
			}
			return val.replace(/\"/g,"");
		}

		$.fn.select2.defaults.set('theme','classic');
        $(".my-select2").select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:true});
		$('textarea').attr('data-provide',"markdown");
		
		$('#reportrange').daterangepicker({
			"ranges": {
				'{{trans("lang.ranges.Today")}}': [moment(), moment()],
				'{{trans("lang.ranges.Yesterday")}}': [moment().subtract('days', 1), moment().subtract('days', 1)],
				'{{trans("lang.ranges.Last_7_Days")}}': [moment().subtract('days', 6), moment()],
				'{{trans("lang.ranges.Last_30_Days")}}': [moment().subtract('days', 29), moment()],
				'{{trans("lang.ranges.This_Month")}}': [moment().startOf('month'), moment().endOf('month')],
                '{{trans("lang.ranges.Last_Month")}}': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
                '{{trans("lang.ranges.This_Year")}}': [moment().startOf('year'), moment().endOf('year')],
				'{{trans("lang.ranges.Last_Year")}}': [moment().subtract('year', 1).startOf('year'), moment().subtract('year', 1).endOf('year')],
			},
			"locale": {
				"format": "MM/DD/YYYY",
				"separator": " - ",
				"applyLabel": "{{trans('lang.locale.applyLabel')}}",
				"cancelLabel": "{{trans('lang.locale.cancelLabel')}}",
				"fromLabel": "{{trans('lang.locale.fromLabel')}}",
				"toLabel": "{{trans('lang.locale.toLabel')}}",
				"customRangeLabel": "{{trans('lang.locale.customRangeLabel')}}",
				"daysOfWeek": [
					"{{trans('lang.locale.daysOfWeek.su')}}",
					"{{trans('lang.locale.daysOfWeek.mo')}}",
					"{{trans('lang.locale.daysOfWeek.tu')}}",
					"{{trans('lang.locale.daysOfWeek.we')}}",
					"{{trans('lang.locale.daysOfWeek.th')}}",
					"{{trans('lang.locale.daysOfWeek.fr')}}",
					"{{trans('lang.locale.daysOfWeek.sa')}}"
				],
				"monthNames": [
					"{{trans('lang.locale.monthNames.January')}}",
					"{{trans('lang.locale.monthNames.February')}}",
					"{{trans('lang.locale.monthNames.March')}}",
					"{{trans('lang.locale.monthNames.April')}}",
					"{{trans('lang.locale.monthNames.May')}}",
					"{{trans('lang.locale.monthNames.June')}}",
					"{{trans('lang.locale.monthNames.July')}}",
					"{{trans('lang.locale.monthNames.August')}}",
					"{{trans('lang.locale.monthNames.September')}}",
					"{{trans('lang.locale.monthNames.October')}}",
					"{{trans('lang.locale.monthNames.November')}}",
					"{{trans('lang.locale.monthNames.December')}}"
				],
				"firstDay": 1
			},
			// "startDate": "11/08/2015",
			// "endDate": "11/14/2015",
			opens: (App.isRTL() ? 'right' : 'left'),
		}, function(start, end, label) {
			$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
			$('#start_date').val(start.format('YYYY-MM-DD'));
			$('#end_date').val(end.format('YYYY-MM-DD'));
			$('#form-filter-date').submit();
		});
		
		var start_date = $("#start_date").val();
		var end_date = $("#end_date").val();
		if(start_date=='' || start_date==null){
			$('#start_date').val(moment().subtract('days', 29).format('YYYY-MM-DD'));
			start_date = moment().subtract('days', 29).format('MMMM D, YYYY');
		}else{
			var date =  Date.parse(start_date);
			start_date = date.toString('MMMM d, yyyy');
		}
		if(end_date=='' || end_date==null){
			$('#end_date').val(moment().format('YYYY-MM-DD'));
			end_date = moment().format('MMMM D, YYYY');
		}else{
			var date  = Date.parse(end_date);
			end_date = date.toString('MMMM d, yyyy');
		}
		$('#reportrange span').html(start_date + ' - ' + end_date);
		$('#reportrange').show();

		$('#report_date').daterangepicker({
			"ranges": {
				'{{trans("lang.ranges.Today")}}': [moment(), moment()],
				'{{trans("lang.ranges.Yesterday")}}': [moment().subtract('days', 1), moment().subtract('days', 1)],
				'{{trans("lang.ranges.Last_7_Days")}}': [moment().subtract('days', 6), moment()],
				'{{trans("lang.ranges.Last_30_Days")}}': [moment().subtract('days', 29), moment()],
				'{{trans("lang.ranges.This_Month")}}': [moment().startOf('month'), moment().endOf('month')],
                '{{trans("lang.ranges.Last_Month")}}': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
                '{{trans("lang.ranges.This_Year")}}': [moment().startOf('year'), moment().endOf('year')],
				'{{trans("lang.ranges.Last_Year")}}': [moment().subtract('year', 1).startOf('year'), moment().subtract('year', 1).endOf('year')],
			},
			"locale": {
				"format": "MM/DD/YYYY",
				"separator": " - ",
				"applyLabel": "{{trans('lang.locale.applyLabel')}}",
				"cancelLabel": "{{trans('lang.locale.cancelLabel')}}",
				"fromLabel": "{{trans('lang.locale.fromLabel')}}",
				"toLabel": "{{trans('lang.locale.toLabel')}}",
				"customRangeLabel": "{{trans('lang.locale.customRangeLabel')}}",
				"daysOfWeek": [
					"{{trans('lang.locale.daysOfWeek.su')}}",
					"{{trans('lang.locale.daysOfWeek.mo')}}",
					"{{trans('lang.locale.daysOfWeek.tu')}}",
					"{{trans('lang.locale.daysOfWeek.we')}}",
					"{{trans('lang.locale.daysOfWeek.th')}}",
					"{{trans('lang.locale.daysOfWeek.fr')}}",
					"{{trans('lang.locale.daysOfWeek.sa')}}"
				],
				"monthNames": [
					"{{trans('lang.locale.monthNames.January')}}",
					"{{trans('lang.locale.monthNames.February')}}",
					"{{trans('lang.locale.monthNames.March')}}",
					"{{trans('lang.locale.monthNames.April')}}",
					"{{trans('lang.locale.monthNames.May')}}",
					"{{trans('lang.locale.monthNames.June')}}",
					"{{trans('lang.locale.monthNames.July')}}",
					"{{trans('lang.locale.monthNames.August')}}",
					"{{trans('lang.locale.monthNames.September')}}",
					"{{trans('lang.locale.monthNames.October')}}",
					"{{trans('lang.locale.monthNames.November')}}",
					"{{trans('lang.locale.monthNames.December')}}"
				],
				"firstDay": 1
			},
			// "startDate": "11/08/2015",
			// "endDate": "11/14/2015",
			opens: (App.isRTL() ? 'right' : 'left'),
		}, function(start, end, label) {
			$('#report_date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
			$('#start_date').val(start.format('YYYY-MM-DD'));
			$('#end_date').val(end.format('YYYY-MM-DD'));
			$('#form-filter-date').submit();
		});

		$('#report_date span').html(start_date + ' - ' + end_date);
		$('#report_date').show();
	
		

    @if (hasRole('approve') || hasRole('inventory'))
		function makeNotificationToast(color,title,msg) {
			var toastOptions = $.getJSON({url:'{{url("assets/lang/notification.options.json")}}',type:'GET',async:false}).responseJSON;
			toastr.options = toastOptions;
			toastr[color](msg,title);
        }
	
		function comparer(otherArray){
			return function(current){
				return otherArray.filter(function(other){
					return other.toastId == current.toastId
				}).length == 0;
			}
		}
	
		$(function(){
			var shortCutFunction = 'info';
			setInterval(function(){
				$.ajax({
					url:'{{url("alert/notification")}}',
					type:'GET',
					dataType:'json',
					success:function(dataAlert){
						var notification_lists = $('.notification_lists');
						var strList = '';
						$('.header_notification_counter').html(dataAlert.length);
						$('.header_notification_counter_span').html(dataAlert.length);
						if (dataAlert.length > 0) {
							if (localStorage['notification_toast_{{Auth::user()->id}}_Z']!=undefined) {
								$.each(dataAlert.filter(comparer(JSON.parse(localStorage['notification_toast_{{Auth::user()->id}}_Z']))),function(key,val){
									makeNotificationToast(shortCutFunction,val.ref_no,val.alert);
								});
								localStorage['notification_toast_{{Auth::user()->id}}_Z'] = JSON.stringify(dataAlert);
							}else{
								$.each(dataAlert,function(key,val){
									makeNotificationToast(shortCutFunction,val.ref_no,val.alert);
								});
								localStorage['notification_toast_{{Auth::user()->id}}_Z'] = JSON.stringify(dataAlert);
							}
							$.each(dataAlert,function(key,val){								
								strList += '<li>';
                                strList += '    <a href="'+val.url+'">';
                                if (val.day_ago==0) {
									strList += '        <span class="time">{{trans('lang.ranges.Today')}}</span>';
                                }else{
									strList += '        <span class="time">'+val.day_ago+' {{trans('lang.day_ago')}}</span>';
								}
								strList += '        <span class="details">';
                                if (val.type=="PR") {
									strList += '            <span class="label label-sm label-icon label-warning">'
									strList += '                <i class="fa fa-bell-o"></i>';
								}else if(val.type=="PO"){
									strList += '            <span class="label label-sm label-icon label-success">';
									strList += '                <i class="icon-basket"></i>';
								}else {
									strList += '            <span class="label label-sm label-icon label-info">';
									strList += '                <i class="fa fa-balance-scale"></i>';
								}
                                strList += '            </span> '+val.type+' #'+val.ref_no+'</span>';
                                strList += '    </a>';
                                strList += '</li>';
							});
							notification_lists.html(strList);
						}
					}
				});
			}, 6000);
		});
    @endif

	</script>
	@yield('javascript')
</body>
</html>
