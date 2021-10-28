<?php

/*///////////////////Report//////////////////*/
Route::group(['prefix'=>'report'],function(){
	Route::group(['prefix'=>'purchase'],function(){
		Route::group(['prefix'=>'request'],function(){
			Route::match(['get','post'],'','ReportController@requests')->middleware('checkRole:report_purchase_request_1');
			Route::get('/generate_requests','ReportController@generate_requests');
			Route::match(['get','post'],'request_1','ReportController@request_1')->middleware('checkRole:report_purchase_request_2');
			Route::get('/generate_request_1','ReportController@generate_request_1');
			Route::match(['get','post'],'request_2','ReportController@request_2')->middleware('checkRole:report_purchase_request_3');
			Route::get('/generate_request_2','ReportController@generate_request_2');
		});
		Route::group(['prefix'=>'order'],function(){
			Route::match(['get','post'],'','ReportController@orders')->middleware('checkRole:report_purchase_order_1');
			Route::get('/generate_orders','ReportController@generate_orders');
			Route::match(['get','post'],'order_1','ReportController@order_1')->middleware('checkRole:report_purchase_order_2');
			Route::get('/generate_order_1','ReportController@generate_order_1');
			Route::match(['get','post'],'order_2','ReportController@order_2')->middleware('checkRole:report_purchase_order_3');
			Route::get('/generate_order_2','ReportController@generate_order_2');
		});
	});
});
/////////////////// Tree View //////////////////////
Route::match(['get','post'],'report/boqTreeView','ReportController@boqTreeView');
Route::get('report/boqTreeView/getBoq','ReportController@getBoq');
Route::get('report/boqTreeView/getBoqexport','ReportController@getBoqexport'); 
Route::get('report/boqTreeView/getBoqprint','ReportController@getBoqprint'); 
/////////////////// End Tree View //////////////////
/*/////////////////Request Print//////////////////*/
Route::get('/purch/request/print/{id}','ReportController@print_request')->middleware('checkRole:purchase_request_print');
Route::get('/purch/order/print/{id}','ReportController@print_order')->middleware('checkRole:purchase_order_print');
Route::match(['get','post'],'/report/delivery','ReportController@delivery')->middleware('checkRole:report_delivery_item');
Route::get('/report/generate_delivery','ReportController@generate_delivery');
Route::match(['get','post'],'/report/return','ReportController@returns')->middleware('checkRole:report_return_delivery_item');
Route::get('/report/generate_return','ReportController@generate_return');
Route::match(['get','post'],'/report/usage','ReportController@usage')->middleware('checkRole:report_usage_entry');
Route::get('/report/generate_usage','ReportController@generate_usage');
Route::get('/report/getHouse','ReportController@getHouse');
Route::match(['get','post'],'/report/usage/return','ReportController@return_usage')->middleware('checkRole:report_return_usage');
Route::get('/report/generate_return_usage','ReportController@generate_return_usage');
Route::match(['get','post'],'/report/sub_boq','ReportController@sub_boq')->middleware('checkRole:report_sub_boq');
Route::get('/report/generate_sub_boq','ReportController@generate_sub_boq');
Route::match(['get','post'],'/report/boq_detail','ReportController@boq_detail')->middleware('checkRole:report_boq_detail');
Route::get('/report/generate_boq_detail','ReportController@generate_boq_detail');
/*Purchase Report Usage by House */
Route::match(['get','post'],'report/usage-house','ReportController@usageHouse'); 
Route::get('/report/generate_usageHouse','ReportController@generate_usageHouse');
Route::get('/report/generate_usageItem/{house_id}','ReportController@generate_usageItem');
Route::get('/report/generate_usageHouseDetail/{house_id}/{team_id}','ReportController@generate_usageHouseDetail');
/*End Purchase Report Usage by House */
Route::match(['get','post'],'/report/stock_balance','ReportController@stock_balance')->middleware('checkRole:report_stock_balance');
Route::match(['get','post'], 'report/stock_balance/get_data','ReportController@stock_balance_get_date');

Route::match(['get','post'],'/report/all_stock_transaction','ReportController@all_stock_transaction')->middleware('checkRole:report_all_stock');

Route::get('report/stock_details','ReportController@stock_details')->middleware('checkRole:report_stock_balance');
Route::match(['get','post'], 'report/stock_details/get_data','ReportController@stock_details_get_date');

Route::get('report/purchase_items','ReportController@purchase_items')->middleware('checkRole:report_purchase');
Route::match(['get','post'], 'report/purchase_items/get_data','ReportController@purchase_items_get_date');

Route::get('report/delivery_details','ReportController@delivery_details')->middleware('checkRole:report_delivery_item');
Route::match(['get','post'], 'report/delivery_details/get_data','ReportController@delivery_details_get_date');

/*Delivery with Return*/
Route::match(['get','post'],'report/delivery-with-return','ReportController@view_delivery_with_return');
Route::get('report/delivery-with-return-data','ReportController@delivery_with_return');
Route::get('report/get_po_code','ReportController@get_po_code');
/*Purchase Detail*/
Route::match(['get','post'],'report/purchase-detail','ReportController@view_report_purchase_request_detail');
Route::get('report/purchase-detail-data','ReportController@report_purchase_request_detail');
/*Purchase Request and Order*/
Route::match(['get','post'],'report/purchase-request-and-order','ReportController@view_report_purchase_and_order');
Route::get('report/purchase-request-and-order-data','ReportController@report_purchase_and_order');
/*Purchase Request and Order*/
Route::match(['get','post'],'report/purchase-request-and-order-delivery','ReportController@view_report_purchase_and_order_delivery');
Route::get('report/purchase-request-and-order-delivery-data','ReportController@report_purchase_and_order_delivery');

Route::get('/report/generate_stock_balance','ReportController@generate_stock_balance');
Route::get('/report/generate_all_stock_transaction','ReportController@generate_all_stock_transaction');
Route::get('/report/stock_balance_detail/{item_id}','ReportController@stock_balance_detail');
/*Purchase Request and Order*/
Route::match(['get','post'],'report/usage-costing','ReportController@view_report_usage_costing');
Route::get('report/usage-costing-data','ReportController@generate_usage_costing');
Route::get('report/usage/subUsageCosting/{usageID}/{houseID}/{itemID}','ReportController@subUsageCosting');
Route::get('report/usage/printUsageCosting','ReportController@printUsageCosting');
// Compare BOQ with Usage
Route::match(['get','post'],'report/usage/compareBOQWithUsage','ReportController@reportCompareBOQWithUsage');
Route::get('report/usage/generate/compareBOQWithUsage','ReportController@generateCompareBOQWithUsage');
Route::get('report/usage/print/compareBOQWithUsage','ReportController@printCompareBOQWithUsage');
// Remaining BOQ(Each House)
Route::match(['get','post'],'report/boq/reportRemainingBOQ','ReportController@reportRemainingBOQ');
Route::get('report/boq/generateRemainingBOQ','ReportController@generateRemainingBOQ');
Route::get('report/boq/printRemainingBOQ','ReportController@printRemainingBOQ');
// Remaining BOQ(Total)
Route::match(['get','post'],'report/boq/reportRemainingBOQTotal','ReportController@reportRemainingBOQTotal');
Route::get('report/boq/generateRemainingBOQTotal','ReportController@generateRemainingBOQTotal');
Route::get('report/boq/printRemainingBOQTotal','ReportController@printRemainingBOQTotal');
// Inventory Valuation Detail
Route::match(['get','post'],'report/inventory/inventoryValuationDetail','ReportController@inventoryValuationDetail');
Route::get('report/inventory/generateInventoryValuationDetail','ReportController@generateInventoryValuationDetail');
Route::get('report/inventory/printInventoryValuationDetail','ReportController@printInventoryValuationDetail');
// Inventory Valuation Detail Sub DataTable
Route::match(['get','post'],'report/inventory/inventoryValuationDetailSubDataTable/{ItemID?}','ReportController@inventoryValuationDetailSubDataTable');
Route::get('report/inventory/generateInventoryValuationDetailSubDataTable/{ItemID?}','ReportController@generateInventoryValuationDetailSubDataTable');
Route::get('report/inventory/printInventoryValuationDetailSubDataTable/{ItemID?}','ReportController@printInventoryValuationDetailSubDataTable');
// Inventory Valuation Summary
Route::match(['get','post'],'report/inventory/inventoryValuationSummary','ReportController@inventoryValuationSummary');
Route::get('report/inventory/generateInventoryValuationSummary','ReportController@generateInventoryValuationSummary');
Route::get('report/inventory/printInventoryValuationSummary','ReportController@printInventoryValuationSummary');