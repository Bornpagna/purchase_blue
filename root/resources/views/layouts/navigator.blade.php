<!-- BEGIN SIDEBAR -->
<div class="page-sidebar navbar-collapse collapse">
	<!-- BEGIN SIDEBAR MENU -->
	<ul class="<?php if(getSetting()->page_header=="fixed"){echo "page-header-fixed";}?> <?php if(getSetting()->sidebar_style=="light"){echo "page-sidebar-menu-light";}?> <?php if(getSetting()->sidebar_menu=="hover"){echo "page-sidebar-menu-hover-submenu";}?> <?php if(getSetting()->sidebar_mode=="fixed"){echo "page-sidebar-menu-fixed";}else{echo "page-sidebar-menu-default";}?> page-sidebar-menu  <?php if(Session::get('sidebar')){ echo " page-sidebar-menu-closed "; } ?>" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
		
		
		<li class="nav-item start <?php if(Request::is('/')){echo "active open";} ?>">
			<a href="{{ url('/') }}" class="nav-link">
				<i class="fa fa-dashboard"></i>
				<span class="title">{{ trans('lang.dashboard') }}</span>
				<span class="selected"></span>
			</a>
		</li>
	@if(hasRole('setup_option'))
		<li class="nav-item <?php if(
			Request::is('usageFormula') ||
			Request::is('zone') || 
			Request::is('block') ||
			Request::is('building') ||  
			Request::is('street') || 
			Request::is('working_type') ||
			Request::is('constr') || 
			Request::is('warehouse')){echo "active open";} ?>">
			<a  class="nav-link nav-toggle">
				<i class="icon-wrench"></i>
				<span class="title">{{ trans('lang.setup_option') }}</span>
				<span class="selected"></span>
				<span class="arrow <?php if(
					Request::is('usageFormula') ||
					Request::is('zone') || 
					Request::is('block') || 
					Request::is('building') || 
					Request::is('street') || 
					Request::is('working_type') || 
					Request::is('constr') || 
					Request::is('warehouse')){echo "open";}?>"></span>
			</a>
			<ul class="sub-menu">
			@if(hasRole('zone'))
				@if(getSetting()->allow_zone==1)
				<li class="nav-item <?php if(Request::is('zone')){echo "active open";} ?>">
					<a href="{{url('zone')}}" class="nav-link ">
						<i class="fa fa-th-large"></i>
						<span class="title">{{ trans('lang.zone') }}</span>
						<?php if(Request::is('zone/')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
				@endif
			@endif
			@if(hasRole('block'))
				@if(getSetting()->allow_block==1)
				<li class="nav-item <?php if(Request::is('block')){echo "active open";} ?>">
					<a href="{{url('block')}}" class="nav-link ">
						<i class="fa fa-square"></i>
						<span class="title">{{ trans('lang.block') }}</span>
						<?php if(Request::is('block')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
				@endif
			@endif
			@if(hasRole('building'))
				@if(getSetting()->allow_block==1)
				<li class="nav-item <?php if(Request::is('building')){echo "active open";} ?>">
					<a href="{{url('building')}}" class="nav-link ">
						<i class="fa fa-square"></i>
						<span class="title">{{ trans('lang.building') }}</span>
						<?php if(Request::is('block')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
				@endif
			@endif
			@if(hasRole('street'))
				<li class="nav-item <?php if(Request::is('street')){echo "active open";} ?>">
					<a href="{{url('street')}}" class="nav-link ">
						<i class="fa fa-road"></i>
						<span class="title">{{ trans('lang.street') }}</span>
						<?php if(Request::is('street')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(hasRole('working_type'))
				<li class="nav-item <?php if(Request::is('working_type')){echo "active open";} ?>">
					<a href="{{url('working_type')}}" class="nav-link ">
						<i class="fa fa-road"></i>
						<span class="title">{{ trans('lang.working_type') }}</span>
						<?php if(Request::is('working_type')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(hasRole('constructor'))
				<li class="nav-item <?php if(Request::is('constr')){echo "active open";} ?>">
					<a href="{{url('constr')}}" class="nav-link ">
						<i class="fa fa-user"></i>
						<span class="title">{{ trans('lang.constructor') }}</span>
						<?php if(Request::is('constr')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(hasRole('warehouse'))
				<li class="nav-item <?php if(Request::is('warehouse')){echo "active open";} ?>">
					<a href="{{url('warehouse')}}" class="nav-link ">
						<i class="fa fa-building"></i>
						<span class="title">{{ trans('lang.warehouse') }}</span>
						<?php if(Request::is('warehouse')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(true)
				<li class="nav-item <?php if(Request::is('usageFormula')){echo "active open";} ?>">
					<a href="{{url('usageFormula')}}" class="nav-link ">
						<i class="fa fa-building"></i>
						<span class="title">{{ trans('lang.usage_formula') }}</span>
						<?php if(Request::is('usageFormula')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			</ul>
		</li>
	@endif
	
	@if(hasRole('item_info'))
		<li class="nav-item <?php if(Request::is('item_type') || Request::is('items') || Request::is('unit') || Request::is('supplier') || Request::is('supitems')){echo "active open";} ?>">
			<a  class="nav-link nav-toggle">
				<i class="fa fa-barcode"></i>
				<span class="title">{{ trans('lang.item_info') }}</span>
				<span class="selected"></span>
				<span class="arrow <?php if(Request::is('item_type') || Request::is('items') || Request::is('unit') || Request::is('supplier') || Request::is('supitems')){echo "open";}?>"></span>
			</a>
			<ul class="sub-menu">
			@if(hasRole('item_type'))
				<li class="nav-item <?php if(Request::is('item_type')){echo "active open";} ?>">
					<a href="{{url('item_type')}}" class="nav-link ">
						<i class="fa fa-pie-chart"></i>
						<span class="title">{{ trans('lang.item_type') }}</span>
						<?php if(Request::is('item_type')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif	
			@if(hasRole('unit'))
				<li class="nav-item <?php if(Request::is('unit')){echo "active open";} ?>">
					<a href="{{url('unit')}}" class="nav-link ">
						<i class="fa fa-refresh"></i>
						<span class="title">{{ trans('lang.unit_convert') }}</span>
						<?php if(Request::is('unit')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(hasRole('item'))
				<li class="nav-item <?php if(Request::is('items')){echo "active open";} ?>">
					<a href="{{url('items')}}" class="nav-link ">
						<i class="fa fa-tags"></i>
						<span class="title">{{ trans('lang.items') }}</span>
						<?php if(Request::is('items')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(hasRole('supplier'))
				<li class="nav-item <?php if(Request::is('supplier')){echo "active open";} ?>">
					<a href="{{url('supplier')}}" class="nav-link ">
						<i class="fa fa-user"></i>
						<span class="title">{{ trans('lang.supplier') }}</span>
						<?php if(Request::is('supplier')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(hasRole('supplier_item'))
				<li class="nav-item <?php if(Request::is('supitems')){echo "active open";} ?>">
					<a href="{{url('supitems')}}" class="nav-link ">
						<i class="fa fa-eyedropper"></i>
						<span class="title">{{ trans('lang.supplier_items') }}</span>
						<?php if(Request::is('supitems')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			</ul>
		</li>
	@endif
	
	@if(hasRole('house_info'))
		<li class="nav-item <?php if(Request::is('boqs') || Request::is('boqs/*') || Request::is('house') || Request::is('housetype')){echo "active open";} ?>">
			<a  class="nav-link nav-toggle">
				<i class="icon-home"></i>
				<span class="title">{{ trans('lang.house_info') }}</span>
				<span class="selected"></span>
				<span class="arrow <?php if(Request::is('boqs') || Request::is('house') || Request::is('housetype')){echo "open";}?>"></span>
			</a>
			<ul class="sub-menu">
			@if(hasRole('house_type'))
				<li class="nav-item <?php if(Request::is('housetype')){echo "active open";} ?>">
					<a href="{{url('housetype')}}" class="nav-link ">
						<i class="fa fa-building-o"></i>
						<span class="title">{{ trans('lang.house_type') }}</span>
						<?php if(Request::is('housetype')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(hasRole('house'))
				<li class="nav-item <?php if(Request::is('house')){echo "active open";} ?>">
					<a href="{{url('house')}}" class="nav-link ">
						<i class="fa fa-home"></i>
						<span class="title">{{ trans('lang.house') }}</span>
						<?php if(Request::is('house')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(hasRole('boq'))
				<li class="nav-item <?php if(Request::is('boqs') || Request::is('boqs/*')){echo "active open";} ?>">
					<a href="{{url('boqs')}}" class="nav-link ">
						<i class="fa fa-bitcoin"></i>
						<span class="title">{{ trans('lang.boq') }}</span>
						<?php if(Request::is('house')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			</ul>
		</li>
	@endif

	@if(hasRole('purchase'))
		<li class="nav-item <?php if(Request::is('purch') || Request::is('purch/*')){echo "active open";} ?> ">
			<a  class="nav-link nav-toggle">
				<i class="icon-basket"></i>
				<span class="title">{{ trans('lang.purchase') }}</span>
				<span class="selected"></span>
				<span class="arrow <?php if(Request::is('purch')){echo "open";}?>"></span>
			</a>
			<ul class="sub-menu">
			@if(hasRole('purchase_request'))
				<li class="nav-item  <?php if(Request::is('purch/request') || Request::is('purch/request/*')){echo "active open";} ?>">
					<a href="{{url('purch/request')}}" class="nav-link ">
						<i class="fa fa-registered"></i>
						<span class="title">{{ trans('lang.request') }}</span>
						<?php if(Request::is('purch/request') || Request::is('purch/request/*')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(hasRole('purchase_order'))
				<li class="nav-item  <?php if(Request::is('purch/order') || Request::is('purch/order/*')){echo "active open";} ?>">
					<a href="{{url('purch/order')}}" class="nav-link ">
						<i class="fa fa-shopping-cart"></i>
						<span class="title">{{ trans('lang.order') }}</span>
						<?php if(Request::is('purch/order') || Request::is('purch/order/*')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			</ul>
		</li>
	@endif
	
	@if(hasRole('approve'))
		<li class="nav-item <?php if(Request::is('approve') || Request::is('approve/*')){echo "active open";} ?> ">
			<a href="" class="nav-link nav-toggle">
				<i class="fa fa-check-square-o"></i>
				<span class="title">{{ trans('lang.approval') }}</span>
				<span class="selected"></span>
				<span class="arrow <?php if(Request::is('approve') || Request::is('approve/*')){echo "open";} ?>"></span>
			</a>
			<ul class="sub-menu">
			@if(hasRole('approve_request'))
				<li class="nav-item  <?php if(Request::is('approve/request')){echo "active open";} ?>">
					<a href="{{url('approve/request')}}" class="nav-link ">
						<i class="fa fa-registered"></i>
						<span class="title">{{ trans('lang.request') }}</span>
						<?php if(Request::is('approve/request')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(hasRole('approve_order'))
				<li class="nav-item  <?php if(Request::is('approve/order')){echo "active open";} ?>">
					<a href="{{url('approve/order')}}" class="nav-link ">
						<i class="fa fa-shopping-cart"></i>
						<span class="title">{{ trans('lang.order') }}</span>
						<?php if(Request::is('approve/order')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			</ul>
		</li>
	@endif
	
	@if(hasRole('inventory'))
		<li class="nav-item <?php if(Request::is('stock') || Request::is('stock/*')){echo "active open";} ?>">
			<a href="" class="nav-link nav-toggle">
				<i class="fa fa-university"></i>
				<span class="title">{{ trans('lang.inventory') }}</span>
				<span class="selected"></span>
				<span class="arrow <?php if(Request::is('stock') || Request::is('stock/*')){echo "open";}?>"></span>
			</a>
			<ul class="sub-menu">
			@if(hasRole('stock_entry'))
				<li class="nav-item <?php if(Request::is('stock/entry') || Request::is('stock/entry/*')){echo "active open";} ?>">
					<a href="{{url('stock/entry')}}" class="nav-link ">
						<i class="fa fa-sign-in"></i>
						<span class="title">{{ trans('lang.stock_entry') }}</span>
						<?php if(Request::is('stock/entry') || Request::is('stock/entry/*')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(hasRole('stock_import'))
				<li class="nav-item <?php if(Request::is('stock/import')){echo "active open";} ?>">
					<a href="{{url('stock/import')}}" class="nav-link ">
						<i class="fa fa-upload"></i>
						<span class="title">{{ trans('lang.stock_import') }}</span>
						<?php if(Request::is('stock/import')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(hasRole('stock_balance'))
				<li class="nav-item <?php if(Request::is('stock/balance') || Request::is('stock/balance/*')){echo "active open";} ?>">
					<a href="{{url('stock/balance')}}" class="nav-link ">
						<i class="fa fa-balance-scale"></i>
						<span class="title">{{ trans('lang.stock_balance') }}</span>
						<?php if(Request::is('stock/balance') || Request::is('stock/balance/*')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(hasRole('stock_adjust'))
				<li class="nav-item <?php if(Request::is('stock/adjust') || Request::is('stock/adjust/*')){echo "active open";} ?>">
					<a href="{{url('stock/adjust')}}" class="nav-link ">
						<i class="fa fa-adjust"></i>
						<span class="title">{{ trans('lang.adjustment') }}</span>
						<?php if(Request::is('stock/adjust') || Request::is('stock/adjust/*')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(hasRole('stock_move'))
				<li class="nav-item <?php if(Request::is('stock/move') || Request::is('stock/move/*')){echo "active open";} ?>">
					<a href="{{url('stock/move')}}" class="nav-link ">
						<i class="fa fa-arrows"></i>
						<span class="title">{{ trans('lang.movement') }}</span>
						<?php if(Request::is('stock/move') || Request::is('stock/move/*')){echo '<span class="selected"></span>';} ?>
					</a>
				</li>
			@endif
			@if(hasRole('delivery'))
				<li class="nav-item start <?php if(Request::is('stock/deliv') || Request::is('stock/deliv/*') || Request::is('stock/redeliv') || Request::is('stock/redeliv/*')){echo "active open";} ?>">
					<a  class="nav-link nav-toggle">
						<i class="fa fa-truck"></i>
						<span class="title">{{ trans('lang.delivery') }}</span>
						<span class="arrow <?php if(Request::is('stock/deliv') || Request::is('stock/deliv/*') || Request::is('stock/redeliv') || Request::is('stock/redeliv/*')){echo "open";} ?>"></span>
					</a>
					<ul class="sub-menu">
					@if(hasRole('delivery_entry'))	
						<li class="nav-item <?php if(Request::is('stock/deliv') || Request::is('stock/deliv/*')){echo "active open";} ?>">
							<a href="{{url('stock/deliv')}}" class="nav-link ">
								<i class="fa fa-sign-in"></i>
								<span class="title">{{ trans('lang.entry') }}</span>
								<?php if(Request::is('stock/deliv') || Request::is('stock/deliv/*')){echo '<span class="selected"></span>';} ?>
							</a>
						</li>
					@endif
					@if(hasRole('delivery_return'))
						<li class="nav-item <?php if(Request::is('stock/redeliv') || Request::is('stock/redeliv/*')){echo "active open";} ?>">
							<a href="{{url('stock/redeliv')}}" class="nav-link ">
								<i class="fa fa-exchange"></i>
								<span class="title">{{ trans('lang.return') }}</span>
								<?php if(Request::is('stock/redeliv') || Request::is('stock/redeliv/*')){echo '<span class="selected"></span>';} ?>
							</a>
						</li>
					@endif
					</ul>
				</li>
			@endif
			@if(hasRole('usage'))
				<li class="nav-item start <?php if(Request::is('stock/reuse_single') || Request::is('stock/reuse_single/*') || Request::is('stock/use_single') || Request::is('stock/use_single/*') || Request::is('stock/use') || Request::is('stock/use/*') || Request::is('stock/reuse') || Request::is('stock/reuse/*')){echo "active open";} ?>">
					<a  class="nav-link nav-toggle">
						<i class="fa fa-legal"></i>
						<span class="title">{{ trans('lang.usage') }}</span>
						<span class="arrow <?php if(Request::is('stock/reuse_single') || Request::is('stock/reuse_single/*') || Request::is('stock/use_single') || Request::is('stock/use_single/*') || Request::is('stock/use') || Request::is('stock/use/*') || Request::is('stock/reuse') || Request::is('stock/reuse/*')){echo "open";} ?>"></span>
					</a>
					<ul class="sub-menu">
					@if(hasRole('usage_entry'))
						<li class="nav-item <?php if(Request::is('stock/use') || Request::is('stock/use/*')){echo "active open";} ?>">
							<a href="{{url('stock/use')}}" class="nav-link ">
								<i class="fa fa-sign-in"></i>
								<span class="title">{{ trans('lang.entry') }}</span>
								<?php if(Request::is('stock/use') || Request::is('stock/use/*')){echo '<span class="selected"></span>';} ?>
							</a>
						</li>
					@endif
					@if(hasRole('usage_entry'))
						<li class="nav-item <?php if(Request::is('stock/use_single') || Request::is('stock/use_single/*')){echo "active open";} ?>">
							<a href="{{url('stock/use_single')}}" class="nav-link ">
								<i class="fa fa-sign-in"></i>
								<span class="title">{{ trans('lang.entry_single') }}</span>
								<?php if(Request::is('stock/use_single') || Request::is('stock/use_single/*')){echo '<span class="selected"></span>';} ?>
							</a>
						</li>
					@endif
					@if(hasRole('usage_entry'))
					<!-- Usage with Policy -->
					<li class="nav-item <?php if(Request::is('stock/use/policy') || Request::is('stock/use/policy/*')){echo "active open";} ?>">
						<a href="{{url('stock/use/policy')}}" class="nav-link ">
							<i class="fa fa-sign-in"></i>
							<span class="title">{{ trans('lang.usage_with_policy') }}</span>
							<?php if(Request::is('stock/use/policy') || Request::is('stock/use/policy/*')){echo '<span class="selected"></span>';} ?>
						</a>
					</li>
					<!-- End Usage with Policy -->
					@endif
					<!--@if(hasRole('usage_return'))
						<li class="nav-item <?php if(Request::is('stock/reuse') || Request::is('stock/reuse/*')){echo "active open";} ?>">
							<a href="{{url('stock/reuse')}}" class="nav-link ">
								<i class="fa fa-exchange"></i>
								<span class="title">{{ trans('lang.return') }}</span>
								<?php if(Request::is('stock/reuse') || Request::is('stock/reuse/*')){echo '<span class="selected"></span>';} ?>
							</a>
						</li>
					@endif
					@if(hasRole('usage_return'))
						<li class="nav-item <?php if(Request::is('stock/reuse_single') || Request::is('stock/reuse_single/*')){echo "active open";} ?>">
							<a href="{{url('stock/reuse_single')}}" class="nav-link ">
								<i class="fa fa-exchange"></i>
								<span class="title">{{ trans('lang.return_single') }}</span>
								<?php if(Request::is('stock/reuse_single') || Request::is('stock/reuse_single/*')){echo '<span class="selected"></span>';} ?>
							</a>
						</li>
					@endif-->
					</ul>
				</li>
			@endif
			</ul>
		</li>
	@endif	
	
	@if(hasRole('report'))
		<li class="nav-item <?php if(Request::is('report/*')){echo "active open";} ?> ">
			<a  class="nav-link nav-toggle">
				<i class="icon-bar-chart"></i>
				<span class="title">{{ trans('lang.report') }}</span>
				<span class="selected"></span>
				<span class="arrow <?php if(Request::is('report')){echo "open";}?>"></span>
			</a>
			<ul class="sub-menu">
				@if(hasRole('report_boq'))
					<li class="nav-item start <?php if(Request::is('report/sub_boq') 
							|| Request::is('report/boqTreeView') 
							|| Request::is('report/boq_detail') 
							|| Request::is('report/boq/reportRemainingBOQ')
							|| Request::is('report/boq/reportRemainingBOQTotal')
						){echo "active open";} ?>">
						<a class="nav-link nav-toggle">
							<i class="fa fa-bitcoin"></i>
							<span class="title">{{ trans('lang.boq') }}</span>
							<span class="arrow <?php if(Request::is('report/sub_boq') 
								|| Request::is('report/boqTreeView') 
								|| Request::is('report/boq_detail') 
								|| Request::is('report/boq/reportRemainingBOQ')
								|| Request::is('report/boq/reportRemainingBOQTotal')
							){echo "open";} ?>"></span>
						</a>
						<ul class="sub-menu">
							@if(hasRole('report_boq_detail'))
								<li class="nav-item <?php if(Request::is('report/boqTreeView')){echo "active open";} ?>">
									<a href="{{url('report/boqTreeView')}}" class="nav-link ">
										<i class="fa fa-building-o"></i>
										<span class="title">{{ trans('lang.tree_view') }}</span>
										<span class="<?php if(Request::is('report/boq_detail')){echo "selected";} ?>"></span>
									</a>
								</li> 
							@endif
							@if(hasRole('report_boq_detail'))
								<li class="nav-item <?php if(Request::is('report/boq_detail')){echo "active open";} ?>">
									<a href="{{url('report/boq_detail')}}" class="nav-link ">
										<i class="fa fa-building-o"></i>
										<span class="title">{{ trans('rep.boq_detail') }}</span>
										<span class="<?php if(Request::is('report/boq_detail')){echo "selected";} ?>"></span>
									</a>
								</li> 
							@endif
							@if(false)
								<li class="nav-item <?php if(Request::is('report/sub_boq')){echo "active open";} ?>">
									<a href="{{url('report/sub_boq')}}" class="nav-link ">
										<i class="fa fa-home"></i>
										<span class="title">{{ trans('rep.sub_boq') }}</span>
										<span class="<?php if(Request::is('report/sub_boq')){echo "selected";} ?>"></span>
									</a>
								</li>
							@endif
							<!-- Report Remaining BOQ(Each House) -->
							<li class="nav-item <?php if(Request::is('report/boq/reportRemainingBOQ')){echo "active open";} ?>">
								<a href="{{url('report/boq/reportRemainingBOQ')}}" class="nav-link ">
									<i class="fa fa-home"></i>
									<span class="title">{{ trans('lang.report_remaining_boq') }}</span>
									<span class="<?php if(Request::is('report/boq/reportRemainingBOQ')){echo "selected";} ?>"></span>
								</a>
							</li>
							<!-- Report Remaining BOQ(Total) -->
							<li class="nav-item <?php if(Request::is('report/boq/reportRemainingBOQTotal')){echo "active open";} ?>">
								<a href="{{url('report/boq/reportRemainingBOQTotal')}}" class="nav-link ">
									<i class="fa fa-home"></i>
									<span class="title">{{ trans('lang.report_remaining_boq_total') }}</span>
									<span class="<?php if(Request::is('report/boq/reportRemainingBOQTotal')){echo "selected";} ?>"></span>
								</a>
							</li>
						</ul>
					</li>
				@endif
				@if(hasRole('report_usage'))
					<li class="nav-item start <?php if(Request::is('report/usage') 
						|| Request::is('report/usage-house')
						|| Request::is('report/usage/compareBOQWithUsage')
						|| Request::is('report/usage-costing')
						|| Request::is('report/usage/return')
					){echo "active open";} ?>">
						<a  class="nav-link nav-toggle">
							<i class="fa fa-legal"></i>
							<span class="title">{{ trans('lang.usage') }}</span>
							<span class="arrow <?php if(
								Request::is('report/usage') 
								|| Request::is('report/usage-house') 
								|| Request::is('report/usage/compareBOQWithUsage')
								|| Request::is('report/usage-costing')
								|| Request::is('report/usage/return')
							){echo "open";} ?>"></span>
						</a>
						<ul class="sub-menu">
							@if(hasRole('report_usage_entry'))
								<li class="nav-item <?php if(Request::is('report/usage-house')){echo "active open";} ?>">
									<a href="{{url('report/usage-house')}}" class="nav-link ">
										<i class="fa fa-building-o"></i>
										<span class="title">{{ trans('rep.usage_house') }}</span>
										<span class="<?php if(Request::is('report/usage-house')){echo "selected";} ?>"></span>
									</a>
								</li> 
							@endif
							@if(hasRole('report_usage_entry'))
								<li class="nav-item <?php if(Request::is('report/usage')){echo "active open";} ?>">
									<a href="{{url('report/usage')}}" class="nav-link ">
										<i class="fa fa-building-o"></i>
										<span class="title">{{ trans('rep.usage') }}</span>
										<span class="<?php if(Request::is('report/usage')){echo "selected";} ?>"></span>
									</a>
								</li> 
							@endif							
							@if(hasRole('report_usage_entry'))
								<li class="nav-item <?php if(Request::is('report/usage/compareBOQWithUsage')){echo "active open";} ?>">
									<a href="{{url('report/usage/compareBOQWithUsage')}}" class="nav-link ">
										<i class="fa fa-building-o"></i>
										<span class="title">{{ trans('lang.report_usage_with_boq') }}</span>
										<span class="<?php if(Request::is('report/usage/compareBOQWithUsage')){echo "selected";} ?>"></span>
									</a>
								</li> 
							@endif
							@if(hasRole('report_usage_entry') && getSetting()->is_costing==1)
								<li class="nav-item <?php if(Request::is('report/usage-costing')){echo "active open";} ?>">
									<a href="{{url('report/usage-costing')}}" class="nav-link ">
										<i class="fa fa-dollar"></i>
										<span class="title">{{ trans('rep.report_usage_costing') }}</span>
										<span class="<?php if(Request::is('report/usage-costing')){echo "selected";} ?>"></span>
									</a>
								</li> 
							@endif
							<!--@if(hasRole('report_return_usage'))
								<li class="nav-item <?php if(Request::is('report/usage/return')){echo "active open";} ?>">
									<a href="{{url('report/usage/return')}}" class="nav-link ">
										<i class="fa fa-home"></i>
										<span class="title">{{ trans('rep.return_usage') }}</span>
										<span class="<?php if(Request::is('report/usage/return')){echo "selected";} ?>"></span>
									</a>
								</li>
							@endif-->
						</ul>
					</li>
				@endif
				@if(hasRole('report_purchase'))
					<li class="nav-item start <?php if(Request::is('report/purchase_items') || Request::is('report/purchase/*') || Request::is('report/delivery') || Request::is('report/return') || Request::is('report/delivery_details')){echo "active open";} ?>">
						<a  class="nav-link nav-toggle">
							<i class="icon-basket"></i>
							<span class="title">{{ trans('lang.purchase') }}</span>
							<span class="arrow <?php if(Request::is('report/purchase_items') || Request::is('report/purchase/*') || Request::is('report/delivery') || Request::is('report/return') || Request::is('report/delivery_details')){echo "open";} ?>"></span>
						</a>
						<ul class="sub-menu">
							@if(hasRole('report_purchase_request'))
								<li class="nav-item <?php if(Request::is('report/purchase-detail') || Request::is('report/purchase/request/*') || Request::is('report/purchase-detail')){echo "active open";} ?>">
									<a  class="nav-link nav-toggle">
										<i class="fa fa-registered"></i>
										<span class="title">{{ trans('lang.request') }}</span>
										<span class="arrow <?php if(Request::is('report/purchase/request') || Request::is('report/purchase/request/*') || Request::is('report/purchase-detail')){echo "open";} ?>"></span>
									</a>
									<ul class="sub-menu">
										@if(hasRole('report_purchase_request_1'))
											<li class="nav-item <?php if(Request::is('report/purchase-detail')){echo "active open";} ?>">
												<a href="{{url('report/purchase-detail')}}" class="nav-link ">
													<i class="fa fa-file-pdf-o"></i>
													<span class="title">{{ trans('rep.report_purchase_request_detail') }}</span>
													<span class="<?php if(Request::is('report/purchase-detail')){echo "selected";} ?>"></span>
												</a>
											</li>
										@endif
										@if(hasRole('report_purchase_request_1'))
											<li class="nav-item <?php if(Request::is('report/purchase/request/request_1')){echo "active open";} ?>">
												<a href="{{url('report/purchase/request/request_1')}}" class="nav-link ">
													<i class="fa fa-file-pdf-o"></i>
													<span class="title">{{ trans('rep.request_1') }}</span>
													<span class="<?php if(Request::is('report/purchase/request/request_1')){echo "selected";} ?>"></span>
												</a>
											</li>
										@endif
										@if(hasRole('report_purchase_request_2'))
											<li class="nav-item <?php if(Request::is('report/purchase-request-and-order')){echo "active open";} ?>">
												<a href="{{url('report/purchase-request-and-order')}}" class="nav-link ">
													<i class="fa fa-file-pdf-o"></i>
													<span class="title">{{ trans('rep.report_purchase_and_order') }}</span>
													<span class="<?php if(Request::is('report/purchase-request-and-order')){echo "selected";} ?>"></span>
												</a>
											</li>
										@endif
										@if(hasRole('report_purchase_request_3'))
											<li class="nav-item <?php if(Request::is('report/purchase-request-and-order-delivery')){echo "active open";} ?>">
												<a href="{{url('report/purchase-request-and-order-delivery')}}" class="nav-link ">
													<i class="fa fa-file-pdf-o"></i>
													<span class="title">{{ trans('rep.report_purchase_and_order_delivery') }}</span>
													<span class="<?php if(Request::is('report/purchase-request-and-order-delivery')){echo "selected";} ?>"></span>
												</a>
											</li>
										@endif
									</ul>
								</li>
							@endif
							@if(hasRole('report_purchase_order'))
								<li class="nav-item <?php if(Request::is('report/purchase/order') || Request::is('report/purchase/order/*')){echo "active open";} ?>">
									<a class="nav-link nav-toggle">
										<i class="fa fa-file-pdf-o"></i>
										<span class="title">{{ trans('lang.order') }}</span>
										<span class="arrow <?php if(Request::is('report/purchase/order') || Request::is('report/purchase/order/*')){echo "open";} ?>"></span>
									</a>
									<ul class="sub-menu">
										@if(hasRole('report_purchase_order_1'))
											<li class="nav-item <?php if(Request::is('report/purchase/order')){echo "active open";} ?>">
												<a href="{{url('report/purchase/order')}}" class="nav-link ">
													<i class="fa fa-file-pdf-o"></i>
													<span class="title">{{ trans('rep.order') }}</span>
													<span class="<?php if(Request::is('report/purchase/order')){echo "selected";} ?>"></span>
												</a>
											</li> 
										@endif
										@if(hasRole('report_purchase_order_2'))
											<li class="nav-item <?php if(Request::is('report/purchase/order/order_1')){echo "active open";} ?>">
												<a href="{{url('report/purchase/order/order_1')}}" class="nav-link ">
													<i class="fa fa-home"></i>
													<span class="title">{{ trans('rep.order_1') }}</span>
													<span class="<?php if(Request::is('report/purchase/order/order_1')){echo "selected";} ?>"></span>
												</a>
											</li>
										@endif
										@if(hasRole('report_purchase_order_3'))
											<li class="nav-item <?php if(Request::is('report/purchase/order/order_2')){echo "active open";} ?>">
												<a href="{{url('report/purchase/order/order_2')}}" class="nav-link ">
													<i class="fa fa-home"></i>
													<span class="title">{{ trans('rep.order_2') }}</span>
													<span class="<?php if(Request::is('report/purchase/order/order_2')){echo "selected";} ?>"></span>
												</a>
											</li>
										@endif
									</ul>
								</li>
							@endif
							@if(hasRole('report_delivery'))
								<li class="nav-item <?php if(Request::is('report/delivery') || Request::is('report/return') || Request::is('report/delivery_details')){echo "active open";} ?>">
									<a class="nav-link nav-toggle">
										<i class="fa fa-truck"></i>
										<span class="title">{{ trans('lang.delivery') }}</span>
										<span class="arrow <?php if(Request::is('report/delivery_details') || Request::is('report/delivery') || Request::is('report/return') || Request::is('report/delivery_details')){echo "open";} ?>"></span>
									</a>
									<ul class="sub-menu">
										@if(hasRole('report_delivery_item'))
											<li class="nav-item <?php if(Request::is('report/delivery')){echo "active open";} ?>">
												<a href="{{url('report/delivery')}}" class="nav-link ">
													<i class="fa fa-area-chart"></i>
													<span class="title">{{ trans('rep.delivery') }}</span>
													<span class="<?php if(Request::is('report/delivery')){echo "selected";} ?>"></span>
												</a>
											</li> 
										@endif
										@if(hasRole('report_delivery_item'))
											<li class="nav-item <?php if(Request::is('report/delivery_details')){echo "active open";} ?>">
												<a href="{{url('report/delivery_details')}}" class="nav-link ">
													<i class="fa fa-area-chart"></i>
													<span class="title">{{ trans('lang.delivery_details') }}</span>
													<span class="<?php if(Request::is('report/delivery_details')){echo "selected";} ?>"></span>
												</a>
											</li> 
										@endif
										@if(hasRole('report_return_delivery_item'))
											<li class="nav-item <?php if(Request::is('report/return')){echo "active open";} ?>">
												<a href="{{url('report/return')}}" class="nav-link ">
													<i class="fa fa-exchange"></i>
													<span class="title">{{ trans('rep.return') }}</span>
													<span class="<?php if(Request::is('report/return')){echo "selected";} ?>"></span>
												</a>
											</li>
										@endif
										@if(hasRole('report_delivery_item'))
											<li class="nav-item <?php if(Request::is('report/delivery-with-return')){echo "active open";} ?>">
												<a href="{{url('report/delivery-with-return')}}" class="nav-link ">
													<i class="fa fa-truck"></i>
													<span class="title">{{ trans('rep.delivery_with_return') }}</span>
													<span class="<?php if(Request::is('report/delivery-with-return')){echo "selected";} ?>"></span>
												</a>
											</li>
										@endif
									</ul>
								</li>
							@endif
							<li class="nav-item <?php if(Request::is('report/purchase_items')){echo "active open";} ?>">
								<a href="{{url('report/purchase_items')}}" class="nav-link ">
									<i class="fa fa-area-chart"></i>
									<span class="title">{{ trans('lang.purchase_items') }}</span>
									<span class="<?php if(Request::is('report/purchase_items')){echo "selected";} ?>"></span>
								</a>
							</li>
						</ul>
					</li>
				@endif
				@if(hasRole('report_stock'))
				<li class="nav-item start <?php if(Request::is('report/stock_details') 
					|| Request::is('report/stock_balance') 
					|| Request::is('report/all_stock_transaction')
					|| Request::is('report/inventory/inventoryValuationDetail')
					|| Request::is('report/inventory/inventoryValuationDetailSubDataTable')
					|| Request::is('report/inventory/inventoryValuationSummary')
				){echo "active open";} ?>">
						<a class="nav-link nav-toggle">
							<i class="fa fa-university"></i>
							<span class="title">{{ trans('lang.stock') }}</span>
							<span class="arrow <?php if(Request::is('report/stock_details') 
							|| Request::is('report/stock_balance') 
							|| Request::is('report/all_stock_transaction')
							|| Request::is('report/inventory/inventoryValuationDetail')
							|| Request::is('report/inventory/inventoryValuationDetailSubDataTable')
							|| Request::is('report/inventory/inventoryValuationSummary')
						){echo "open";} ?>"></span>
						</a>
						<ul class="sub-menu">
							@if(hasRole('report_stock_balance'))
								<li class="nav-item <?php if(Request::is('report/stock_balance')){echo "active open";} ?>">
									<a href="{{url('report/stock_balance')}}" class="nav-link ">
										<i class="fa fa-balance-scale"></i>
										<span class="title">{{ trans('rep.stock_balance') }}</span>
										<span class="<?php if(Request::is('report/stock_balance')){echo "selected";} ?>"></span>
									</a>
								</li> 
							@endif
							@if(hasRole('report_stock_balance'))
								<li class="nav-item <?php if(Request::is('report/stock_details')){echo "active open";} ?>">
									<a href="{{url('report/stock_details')}}" class="nav-link ">
										<i class="fa fa-area-chart"></i>
										<span class="title">{{ trans('lang.stock_details') }}</span>
										<span class="<?php if(Request::is('report/stock_details')){echo "selected";} ?>"></span>
									</a>
								</li> 
							@endif
							@if(hasRole('report_all_stock'))
								<li class="nav-item <?php if(Request::is('report/all_stock_transaction')){echo "active open";} ?>">
									<a href="{{url('report/all_stock_transaction')}}" class="nav-link ">
										<i class="fa fa-bar-chart"></i>
										<span class="title">{{ trans('rep.all_stock_transaction') }}</span>
										<span class="<?php if(Request::is('report/all_stock_transaction')){echo "selected";} ?>"></span>
									</a>
								</li>
							@endif
							<!-- Inventory Valuation Detail -->
							@if(true)
								<li class="nav-item <?php if(Request::is('report/inventory/inventoryValuationDetail')){echo "active open";} ?>">
									<a href="{{url('report/inventory/inventoryValuationDetail')}}" class="nav-link ">
										<i class="fa fa-bar-chart"></i>
										<span class="title">{{ trans('lang.inventory_valuation_detail') }}</span>
										<span class="<?php if(Request::is('report/inventory/inventoryValuationDetail')){echo "selected";} ?>"></span>
									</a>
								</li>
							@endif
							<!-- Inventory Valuation Summary -->
							@if(true)
								<li class="nav-item <?php if(Request::is('report/inventory/inventoryValuationSummary')){echo "active open";} ?>">
									<a href="{{url('report/inventory/inventoryValuationSummary')}}" class="nav-link ">
										<i class="fa fa-bar-chart"></i>
										<span class="title">{{ trans('lang.inventory_valuation_summary') }}</span>
										<span class="<?php if(Request::is('report/inventory/inventoryValuationSummary')){echo "selected";} ?>"></span>
									</a>
								</li>
							@endif
						</ul>
					</li>
				@endif
			</ul>
		</li>
	@endif	
	
	@if(hasRole('user_control'))
		<li class="nav-item <?php if(Request::is('user') || Request::is('depart') || Request::is('group') || Request::is('permis') || Request::is('role')){echo "active open";} ?>">
			<a  class="nav-link nav-toggle">
				<i class="icon-user"></i>
				<span class="title">{{ trans('lang.user_control') }}</span>
				<span class="selected"></span>
				<span class="arrow <?php if(Request::is('user') || Request::is('depart') || Request::is('group') || Request::is('permis') || Request::is('role')){echo "open";}?>"></span>
			</a>
			<ul class="sub-menu">
			@if(hasRole('department'))
				<li class="nav-item <?php if(Request::is('depart')){echo "active open";} ?>">
					<a href="{{ url('depart') }}" class="nav-link ">
						<i class="fa fa-sitemap"></i>
						<span class="title">{{ trans('lang.department') }}</span>
						<span class="<?php if(Request::is('depart')){echo "selected";} ?>"></span>
					</a>
				</li>
			@endif
			@if(hasRole('user'))
				<li class="nav-item <?php if(Request::is('user')){echo "active open";} ?>">
					<a href="{{ url('user') }}" class="nav-link ">
						<i class="icon-user"></i>
						<span class="title">{{ trans('lang.user_info') }}</span>
						<span class="<?php if(Request::is('user')){echo "selected";} ?>"></span>
					</a>
				</li>
			@endif
			@if(hasRole('user_group'))
				<li class="nav-item  <?php if(Request::is('group')){echo "active open";} ?>">
					<a href="{{url('group')}}" class="nav-link ">
						<i class="icon-users"></i>
						<span class="title">{{ trans('lang.user_group') }}</span>
						<span class="<?php if(Request::is('group')){echo "selected";} ?>"></span>
					</a>
				</li>
			@endif
			@if(hasRole('role'))
				<li class="nav-item <?php if(Request::is('role')){echo "active open";} ?> ">
					<a href="{{url('role')}}" class="nav-link ">
						<i class="fa fa-exclamation-triangle"></i>
						<span class="title">{{ trans('lang.role') }}</span>
						<span class="<?php if(Request::is('role')){echo "selected";} ?>"></span>
					</a>
				</li>
			@endif
			</ul>
		</li>
	@endif	
	
	@if(hasRole('system'))
		<li class="nav-item <?php if(Request::is('setting') || Request::is('trash') || Request::is('trash/*') || Request::is('activity') || Request::is('backup')){echo "active open";} ?>">
			<a  class="nav-link nav-toggle">
				<i class="icon-settings"></i>
				<span class="title">{{ trans('lang.system') }}</span>
				<span class="selected"></span>
				<span class="arrow <?php if(Request::is('setting') || Request::is('period') || Request::is('trash') || Request::is('activity') || Request::is('backup')){echo "open";} ?>"></span>
			</a>
			<ul class="sub-menu">
			@if(hasRole('setting'))
				<li class="nav-item <?php if(Request::is('setting')){echo "active open";} ?>">
					<a href="{{url('setting')}}" class="nav-link ">
						<i class="fa fa-cogs"></i>
						<span class="title">{{trans('lang.setting')}}</span>
						<span class="<?php if(Request::is('setting')){echo "selected";} ?>"></span>
					</a>
				</li>
			@endif
			@if(hasRole('setting'))
				<li class="nav-item <?php if(Request::is('format')){echo "active open";} ?>">
					<a href="{{url('format/index')}}" class="nav-link ">
						<i class="fa fa-file-o"></i>
						<span class="title">{{trans('lang.format_info')}}</span>
						<span class="<?php if(Request::is('format')){echo "selected";} ?>"></span>
					</a>
				</li>
			@endif
			@if(hasRole('user_log'))
				<li class="nav-item <?php if(Request::is('activity')){echo "active open";} ?>">
					<a href="{{url('activity')}}" class="nav-link ">
						<i class="fa fa-expeditedssl"></i>
						<span class="title">{{trans('lang.user_activity')}}</span>
						<span class="<?php if(Request::is('activity')){echo "selected";} ?>"></span>
					</a>
				</li>
			@endif
			@if(hasRole('backup'))
				<li class="nav-item <?php if(Request::is('backup')){echo "active open";} ?>">
					<a href="{{url('backup')}}" class="nav-link ">
						<i class="fa fa-database"></i>
						<span class="title">{{trans('lang.backup')}}</span>
						<span class="<?php if(Request::is('backup')){echo "selected";} ?>"></span>
					</a>
				</li>
			@endif
			@if(hasRole('trash'))
				<li class="nav-item <?php if(Request::is('trash') || Request::is('trash/*')){echo "active open";} ?>">
					<a  class="nav-link nav-toggle">
						<i class="fa fa-trash-o"></i>
						<span class="title">{{trans('lang.trash')}}</span>
						<span class="arrow <?php if(Request::is('trash') || Request::is('trash/*')){echo "open";} ?>"></span>
					</a>
					<ul class="sub-menu">
					@if(hasRole('trash_stock_entry'))
						<li class="nav-item <?php if(Request::is('trash/entry')){echo "active open";} ?>">
							<a href="{{url('trash/entry')}}" class="nav-link ">
								<i class="fa fa-sign-in"></i>
								<span class="title">{{trans('lang.stock_entry')}}</span>
								<span class="<?php if(Request::is('trash/entry')){echo "selected";} ?>"></span>
							</a>
						</li>
					@endif
					@if(hasRole('trash_stock_import'))
						<li class="nav-item <?php if(Request::is('trash/import')){echo "active open";} ?>">
							<a href="{{url('trash/import')}}" class="nav-link ">
								<i class="fa fa-upload"></i>
								<span class="title">{{trans('lang.stock_import')}}</span>
								<span class="<?php if(Request::is('trash/import')){echo "selected";} ?>"></span>
							</a>
						</li>
					@endif
					@if(hasRole('trash_stock_adjust'))
						<li class="nav-item <?php if(Request::is('trash/adjust')){echo "active open";} ?>">
							<a href="{{url('trash/adjust')}}" class="nav-link ">
								<i class="fa fa-adjust"></i>
								<span class="title">{{trans('lang.adjustment')}}</span>
								<span class="<?php if(Request::is('trash/adjust')){echo "selected";} ?>"></span>
							</a>
						</li>
					@endif
					@if(hasRole('trash_stock_move'))
						<li class="nav-item <?php if(Request::is('trash/move')){echo "active open";} ?>">
							<a href="{{url('trash/move')}}" class="nav-link ">
								<i class="fa fa-arrows"></i>
								<span class="title">{{trans('lang.movement')}}</span>
								<span class="<?php if(Request::is('trash/move')){echo "selected";} ?>"></span>
							</a>
						</li>
					@endif
					@if(hasRole('trash_stock_delivery'))
						<li class="nav-item <?php if(Request::is('trash/delivery')){echo "active open";} ?>">
							<a href="{{url('trash/delivery')}}" class="nav-link ">
								<i class="fa fa-truck"></i>
								<span class="title">{{trans('lang.delivery')}}</span>
								<span class="<?php if(Request::is('trash/delivery')){echo "selected";} ?>"></span>
							</a>
						</li>
					@endif
					@if(hasRole('trash_stock_return_delivery'))
						<li class="nav-item <?php if(Request::is('trash/redelivery')){echo "active open";} ?>">
							<a href="{{url('trash/redelivery')}}" class="nav-link ">
								<i class="fa fa-exchange"></i>
								<span class="title">{{trans('lang.return_delivery')}}</span>
								<span class="<?php if(Request::is('trash/redelivery')){echo "selected";} ?>"></span>
							</a>
						</li>
					@endif
					@if(hasRole('trash_stock_usage'))
						<li class="nav-item <?php if(Request::is('trash/usage')){echo "active open";} ?>">
							<a href="{{url('trash/usage')}}" class="nav-link ">
								<i class="fa fa-legal"></i>
								<span class="title">{{trans('lang.usage')}}</span>
								<span class="<?php if(Request::is('trash/usage')){echo "selected";} ?>"></span>
							</a>
						</li>
					@endif
					@if(hasRole('trash_stock_return_usage'))
						<li class="nav-item <?php if(Request::is('trash/reusage')){echo "active open";} ?>">
							<a href="{{url('trash/reusage')}}" class="nav-link ">
								<i class="fa fa-exchange"></i>
								<span class="title">{{trans('lang.return_usage')}}</span>
								<span class="<?php if(Request::is('trash/reusage')){echo "selected";} ?>"></span>
							</a>
						</li>
					@endif
					@if(hasRole('trash_request'))
						<li class="nav-item <?php if(Request::is('trash/request')){echo "active open";} ?>">
							<a href="{{url('trash/request')}}" class="nav-link ">
								<i class="fa fa-registered"></i>
								<span class="title">{{trans('lang.request')}}</span>
								<span class="<?php if(Request::is('trash/request')){echo "selected";} ?>"></span>
							</a>
						</li>
					@endif
					@if(hasRole('trash_order'))
						<li class="nav-item <?php if(Request::is('trash/order')){echo "active open";} ?>">
							<a href="{{url('trash/order')}}" class="nav-link ">
								<i class="icon-basket"></i>
								<span class="title">{{trans('lang.order')}}</span>
								<span class="<?php if(Request::is('trash/order')){echo "selected";} ?>"></span>
							</a>
						</li>
					@endif
					</ul>
				</li>
			@endif
			</ul>
		</li>
	@endif	
	</ul>
	<!-- END SIDEBAR MENU -->
</div>
<!-- END SIDEBAR -->