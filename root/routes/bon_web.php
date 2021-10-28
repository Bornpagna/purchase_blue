<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

///////////////////// route housetype ///////////////////////////
Route::group(['prefix' => 'housetype'], function() {
	Route::get('','HouseTypeController@index')->middleware('checkRole:house_type');
	Route::get('dt','HouseTypeController@getDt');
	Route::post('save','HouseTypeController@save')->middleware('checkRole:house_type_add');
	Route::post('edit/{id}','HouseTypeController@update')->middleware('checkRole:house_type_edit');
	Route::get('delete/{id}','HouseTypeController@destroy')->middleware('checkRole:house_type_delete');
	Route::get('excel/download','HouseTypeController@downloadExcel')->middleware('checkRole:house_type_download');
	Route::post('excel/upload','HouseTypeController@uploadExcel')->middleware('checkRole:house_type_upload');
	Route::post('boq/upload/{id}','HouseTypeController@uploadBoq')->middleware('checkRole:house_type_upload_boq');
	Route::post('boq/enter/{id}','HouseTypeController@enterBoq')->middleware('checkRole:house_type_enter_boq');
	// Import & Export BOQ
	Route::get('downloadSampleExcelBOQ/{houseTypeId}','HouseTypeController@downloadSampleExcelBOQ');
	Route::post('importSampleExcelBOQ/{houseTypeId}','HouseTypeController@importSampleExcelBOQ');
});

///////////////////// route house ///////////////////////////
Route::group(['prefix' => 'house'], function() {
	Route::get('','HouseController@index')->middleware('checkRole:house');
	Route::get('dt','HouseController@getDt');
	Route::post('save','HouseController@save')->middleware('checkRole:house_add');
	Route::post('edit/{id}','HouseController@update')->middleware('checkRole:house_edit');
	Route::get('delete/{id}','HouseController@destroy')->middleware('checkRole:house_delete');
	Route::get('excel/download','HouseController@downloadExcel')->middleware('checkRole:house_download');
	Route::post('excel/upload','HouseController@uploadExcel')->middleware('checkRole:house_upload');
	Route::post('excel/uploadUpdateHouse','HouseController@uploadExcelUpdateHouse')->middleware('checkRole:house_upload');
	Route::post('boq/upload/{id}','HouseController@uploadBoq')->middleware('checkRole:house_upload_boq');
	Route::post('boq/enter/{id}','HouseController@enterBoq')->middleware('checkRole:house_enter_boq');
	Route::get('subdt/{id}','HouseController@subDt');
	// Import & Export BOQ
	Route::get('downloadSampleExcelBOQ/{houseId}','HouseController@downloadSampleExcelBOQ');
	Route::post('importSampleExcelBOQ/{houseId}','HouseController@importSampleExcelBOQ');
});

///////////////////// route boq ///////////////////////////
Route::group(['prefix' => 'boqs'], function() {
	Route::get('','BoqController@index')->middleware('checkRole:boq');
	Route::get('dt','BoqController@getDt');
	Route::post('save','BoqController@save')->middleware('checkRole:boq_add');
	Route::get('view/{id}/{back}/{house_id}','BoqController@view')->middleware('checkRole:view_boq');
	/* Route::get('view/dt','BoqController@getDt'); */
	Route::post('edit/{id}','BoqController@update')->middleware('checkRole:boq_edit');
	Route::get('delete/{id}','BoqController@destroy')->middleware('checkRole:boq_delete');
	Route::get('sub/delete/{id}','BoqController@destroySub');
	Route::get('excel/download/{id}','BoqController@downloadExcel')->middleware('checkRole:boq_download');
	Route::get('excel/example','BoqController@downloadExample')->middleware('checkRole:boq_download_sample');	
	Route::post('excel/upload','BoqController@uploadExcel')->middleware('checkRole:boq_download_sample');
	Route::get('subdt/{id}','BoqController@subDt');
	Route::get('boqhousesdt/{id}','BoqController@boqHousesDt');
	Route::get('boqhouseworkingtypedt/{id}/{house_id}','BoqController@getHouseWorkingType');
	Route::get('boqworkingtypedt/{id}','BoqController@workingTypeDt');
	Route::get('boqitemworkingtypedt/{id}','BoqController@subWorkingTypeDt');
	Route::get('replicateboq/{id}','BoqController@replicateBoq')->middleware('checkRole:replicate_boq');
	Route::post('reviseboq/{id}','BoqController@reviseBoq')->middleware('checkRole:revise_boq');
	Route::get('replicateboqworkingtype/{boq_id}/{type_id}','BoqController@replicateBoqByWorkingType')->middleware('checkRole:replicate_boq_by_working_type');
	Route::post('reviseboqworkingtype/{boq_id}/{type_id}','BoqController@reviseBoqWorkingType')->middleware('checkRole:revise_boq_working_type');
	Route::get('getBoqItems/{boq_id}/{house_id}/{working_type_id?}','BoqController@getBoqHouseItem');
	Route::post('reviseBoqHouseView/{boq_id}/{house_id}','BoqController@reviseBoqHouseView');
	Route::post('reviseBoqHouse/{boq_id}/{house_id}/{working_type}','BoqController@reviseBoqHouse')->middleware('checkRole:revise_boq_house');
	Route::post('reviseBoqHouses/{boq_id}/{house_id}/{working_type}','BoqController@reviseBoqHouse')->middleware('checkRole:revise_boq_house');
	Route::get('viewBoq/{id}/{back}','BoqController@viewBoq')->middleware('checkRole:view_boq');
	Route::post('uploadBoqPreview','BoqController@uploadBoqPreview')->middleware('checkRole:boq_add');
	Route::get('boqhousesviewdt/{id}','BoqController@boqHousesViewDt');
	Route::post('assignHouse/{id}','BoqController@assignHouse');
	Route::get('downloadBoq/{id}','BoqController@downloadBoq');
	Route::get('reviseversion/{id}','BoqController@reviseVersion');
	Route::get('getreviseboqdt/{id}','BoqController@getReviseBoqDt');
	Route::get('getreviseboqhousedt/{id}','BoqController@reviseBoqHousesDt');
	Route::get('viewreviseboq/{id}/{back}','BoqController@viewReviseBoq');
	Route::get('reviseboqhousesviewdts/{id}','BoqController@reviseBoqHousesViewDt');
	Route::get('excel/downloadExampleById/{id}','BoqController@downloadExampleById');
	Route::post('reviseBoqHouseViewExcel/{boq_id}/{house_id}','BoqController@reviseBoqHouseViewExcel');
	Route::post('uploadBoqPreviewRevise','BoqController@uploadBoqPreviewRevise');
	Route::match(['get','post'],'updateBoqZone','BoqController@updateBoqZoneByHouse');
});

///////////////////// route zone ///////////////////////////
Route::group(['prefix' => 'zone'], function() {
	Route::get('','ZoneController@index')->middleware('checkRole:zone');
	Route::get('dt','ZoneController@getDt');
	Route::post('save','ZoneController@save')->middleware('checkRole:zone_add');
	Route::post('edit/{id}','ZoneController@update')->middleware('checkRole:zone_edit');
	Route::get('delete/{id}','ZoneController@destroy')->middleware('checkRole:zone_delete');
	Route::get('excel/download','ZoneController@downloadExcel')->middleware('checkRole:zone_download');
	Route::post('excel/upload','ZoneController@uploadExcel')->middleware('checkRole:zone_upload');
});

///////////////////// route block ///////////////////////////
Route::group(['prefix' => 'block'], function() {
	Route::get('','BlockController@index')->middleware('checkRole:block');
	Route::get('dt','BlockController@getDt');
	Route::post('save','BlockController@save')->middleware('checkRole:block_add');
	Route::post('edit/{id}','BlockController@update')->middleware('checkRole:block_edit');
	Route::get('delete/{id}','BlockController@destroy')->middleware('checkRole:block_delete');
	Route::get('excel/download','BlockController@downloadExcel')->middleware('checkRole:block_download');
	Route::post('excel/upload','BlockController@uploadExcel')->middleware('checkRole:block_upload');
});

///////////////////// route building ///////////////////////////
Route::group(['prefix' => 'building'], function() {
	Route::get('','BuildingController@index')->middleware('checkRole:building');
	Route::get('dt','BuildingController@getDt');
	Route::post('save','BuildingController@save')->middleware('checkRole:building_add');
	Route::post('edit/{id}','BuildingController@update')->middleware('checkRole:building_edit');
	Route::get('delete/{id}','BuildingController@destroy')->middleware('checkRole:building_delete');
	Route::get('excel/download','BuildingController@downloadExcel')->middleware('checkRole:building_download');
	Route::post('excel/upload','BuildingController@uploadExcel')->middleware('checkRole:building_upload');
});

///////////////////// route street ///////////////////////////
Route::group(['prefix' => 'street'], function() {
	Route::get('','StreetController@index')->middleware('checkRole:street');
	Route::get('dt','StreetController@getDt');
	Route::post('save','StreetController@save')->middleware('checkRole:street_add');
	Route::post('edit/{id}','StreetController@update')->middleware('checkRole:street_edit');
	Route::get('delete/{id}','StreetController@destroy')->middleware('checkRole:street_delete');
	Route::get('excel/download','StreetController@downloadExcel')->middleware('checkRole:street_download');
	Route::post('excel/upload','StreetController@uploadExcel')->middleware('checkRole:street_upload');
});

///////////////////// route Working Type ///////////////////////////
Route::group(['prefix' => 'working_type'], function() {
	Route::get('','WorkingTypeController@index')->middleware('checkRole:working_type');
	Route::get('dt','WorkingTypeController@getDt');
	Route::post('save','WorkingTypeController@save')->middleware('checkRole:working_type_add');
	Route::post('edit/{id}','WorkingTypeController@update')->middleware('checkRole:working_type_edit');
	Route::get('delete/{id}','WorkingTypeController@destroy')->middleware('checkRole:working_type_delete');
	Route::get('excel/download','WorkingTypeController@downloadExcel')->middleware('checkRole:working_type_download');
	Route::post('excel/upload','WorkingTypeController@uploadExcel')->middleware('checkRole:working_type_upload');
});

///////////////////// route item type ///////////////////////////
Route::group(['prefix' => 'item_type'], function() {
	Route::get('','ItemTypeController@index')->middleware('checkRole:item_type');
	Route::get('dt','ItemTypeController@getDt');
	Route::post('save','ItemTypeController@save')->middleware('checkRole:item_type_add');
	Route::post('edit/{id}','ItemTypeController@update')->middleware('checkRole:item_type_edit');
	Route::get('delete/{id}','ItemTypeController@destroy')->middleware('checkRole:item_type_delete');
	Route::get('excel/download','ItemTypeController@downloadExcel')->middleware('checkRole:item_type_download');
	Route::post('excel/upload','ItemTypeController@uploadExcel')->middleware('checkRole:item_type_upload');
});

///////////////////// route department ///////////////////////////
Route::group(['prefix' => 'depart'], function() {
	Route::get('','DepartmentController@index')->middleware('checkRole:department');
	Route::get('dt','DepartmentController@getDt');
	Route::post('save','DepartmentController@save')->middleware('checkRole:department_add');
	Route::post('edit/{id}','DepartmentController@update')->middleware('checkRole:department_edit');
	Route::get('delete/{id}','DepartmentController@destroy')->middleware('checkRole:department_delete');
	Route::get('excel/download','DepartmentController@downloadExcel')->middleware('checkRole:department_download');
	Route::post('excel/upload','DepartmentController@uploadExcel')->middleware('checkRole:department_upload');
});

///////////////////// route department ///////////////////////////
Route::group(['prefix' => 'group'], function() {
	Route::get('','GroupUserController@index')->middleware('checkRole:user_group');
	Route::get('dt','GroupUserController@getDt');
	Route::post('save','GroupUserController@save')->middleware('checkRole:user_group_add');
	Route::post('edit/{id}','GroupUserController@update')->middleware('checkRole:user_group_edit');
	Route::post('assign/{id}','GroupUserController@assign')->middleware('checkRole:user_group_assign');
	Route::get('delete/{id}','GroupUserController@destroy')->middleware('checkRole:user_group_delete');
	Route::get('excel/download','GroupUserController@downloadExcel')->middleware('checkRole:user_group_download');
	Route::post('excel/upload','GroupUserController@uploadExcel')->middleware('checkRole:user_group_upload');
});

///////////////////// route constructor ///////////////////////////
Route::group(['prefix' => 'constr'], function() {
	Route::get('','ConstructorController@index')->middleware('checkRole:constructor');
	Route::get('dt','ConstructorController@getDt');
	Route::post('save','ConstructorController@save')->middleware('checkRole:constructor_add');
	Route::post('edit/{id}','ConstructorController@update')->middleware('checkRole:constructor_edit');
	Route::get('delete/{id}','ConstructorController@destroy')->middleware('checkRole:constructor_delete');
	Route::get('excel/download','ConstructorController@downloadExcel')->middleware('checkRole:constructor_download');
	Route::post('excel/upload','ConstructorController@uploadExcel')->middleware('checkRole:constructor_upload');
});

///////////////////// route warehouse ///////////////////////////
Route::group(['prefix' => 'warehouse'], function() {
	Route::get('','WarehouseController@index')->middleware('checkRole:warehouse');
	Route::get('dt','WarehouseController@getDt');
	Route::post('save','WarehouseController@save')->middleware('checkRole:warehouse_add');
	Route::post('edit/{id}','WarehouseController@update')->middleware('checkRole:warehouse_edit');
	Route::get('delete/{id}','WarehouseController@destroy')->middleware('checkRole:warehouse_delete');
	Route::get('excel/download','WarehouseController@downloadExcel')->middleware('checkRole:warehouse_download');
	Route::post('excel/upload','WarehouseController@uploadExcel')->middleware('checkRole:warehouse_upload');
});

///////////////////// route items ///////////////////////////
Route::group(['prefix' => 'items'], function() {
	Route::get('','ItemsController@index')->middleware('checkRole:item');
	Route::get('dt','ItemsController@getDt');
	Route::post('save','ItemsController@save')->middleware('checkRole:item_add');
	Route::post('edit/{id}','ItemsController@update')->middleware('checkRole:item_edit');
	Route::get('delete/{id}','ItemsController@destroy')->middleware('checkRole:item_delete');
	Route::get('excel/download','ItemsController@downloadExcel')->middleware('checkRole:item_download');
	Route::post('excel/upload','ItemsController@uploadExcel')->middleware('checkRole:item_upload');
	Route::get('getProducts','ItemsController@getProducts');
	// Upload & Download Excel
	Route::get('downloadSampleUpdatePrice','ItemsController@sampleUpdatePrice');
	Route::post('uploadSampleUpdatePrice','ItemsController@updatePrice');
});

///////////////////// route unit convertion ///////////////////////////
Route::group(['prefix' => 'unit'], function() {
	Route::get('','UnitConvertController@index')->middleware('checkRole:unit');
	Route::get('dt','UnitConvertController@getDt');
	Route::post('save','UnitConvertController@save')->middleware('checkRole:unit_add');
	Route::post('edit/{id}','UnitConvertController@update')->middleware('checkRole:unit_edit');
	Route::get('delete/{id}','UnitConvertController@destroy')->middleware('checkRole:unit_delete');
	Route::get('excel/download','UnitConvertController@downloadExcel')->middleware('checkRole:unit_download');
	Route::post('excel/upload','UnitConvertController@uploadExcel')->middleware('checkRole:unit_upload');
});

///////////////////// route supplier ///////////////////////////
Route::group(['prefix' => 'supplier'], function() {
	Route::get('','SupplierController@index')->middleware('checkRole:supplier');
	Route::get('dt','SupplierController@getDt');
	Route::post('save','SupplierController@save')->middleware('checkRole:supplier_add');
	Route::post('edit/{id}','SupplierController@update')->middleware('checkRole:supplier_edit');
	Route::get('delete/{id}','SupplierController@destroy')->middleware('checkRole:supplier_delete');
	Route::get('excel/download','SupplierController@downloadExcel')->middleware('checkRole:supplier_download');
	Route::post('excel/upload','SupplierController@uploadExcel')->middleware('checkRole:supplier_upload');
});

///////////////////// route supplier items ///////////////////////////
Route::group(['prefix' => 'supitems'], function() {
	Route::get('','SupplierItemsController@index')->middleware('checkRole:supplier_item');
	Route::get('dt','SupplierItemsController@getDt');
	Route::post('save','SupplierItemsController@save')->middleware('checkRole:supplier_item_add');
	Route::post('edit/{id}','SupplierItemsController@update')->middleware('checkRole:supplier_item_edit');
	Route::get('delete/{id}','SupplierItemsController@destroy')->middleware('checkRole:supplier_item_delete');
	Route::get('excel/download','SupplierItemsController@downloadExcel')->middleware('checkRole:supplier_item_download');
	Route::post('excel/upload','SupplierItemsController@uploadExcel')->middleware('checkRole:supplier_item_upload');
});

///////////////////// route format invoice ///////////////////////////
Route::group(['prefix' => 'format'], function() {
	Route::get('index','FormatInvoiceController@index');
	Route::get('jsonFormatInvoice','FormatInvoiceController@getJsonFormatInvoice');
	Route::get('add','FormatInvoiceController@add');
	Route::get('edit/{id}','FormatInvoiceController@edit');
	Route::post('insert','FormatInvoiceController@insert');
	Route::post('update','FormatInvoiceController@update');
	Route::post('delete','FormatInvoiceController@onDelete');
});

///////////////////// route inventory ///////////////////////////
Route::group(['prefix' => 'stock'], function() {
	Route::group(['prefix' => 'entry'], function(){
		Route::get('','EntryController@index')->middleware('checkRole:stock_entry');
		Route::get('dt','EntryController@getDt');
		Route::get('add','EntryController@add')->middleware('checkRole:stock_entry_add');
		Route::get('edit/{id}','EntryController@edit')->middleware('checkRole:stock_entry_edit');
		Route::post('save','EntryController@save')->middleware('checkRole:stock_entry_add');
		Route::post('update/{id}','EntryController@update')->middleware('checkRole:stock_entry_edit');
		Route::get('delete/{id}','EntryController@destroy')->middleware('checkRole:stock_entry_delete');
		Route::get('subdt/{id}','EntryController@subDt');
		Route::get('GetDetail', 'EntryController@GetDetail');
	});
	
	Route::group(['prefix' => 'import'], function(){
		Route::get('','ImportController@index')->middleware('checkRole:stock_import');
		Route::get('dt','ImportController@getDt');
		Route::get('delete/{id}','ImportController@destroy')->middleware('checkRole:stock_import_delete');
		Route::get('subdt/{id}','ImportController@subDt');
		Route::get('excel/download/exaple','ImportController@downloadExample')->middleware('checkRole:stock_import_download_sample');
		Route::post('excel/upload','ImportController@uploadExcel')->middleware('checkRole:stock_import_upload');
	});
	
	Route::group(['prefix' => 'balance'], function(){
		Route::match(['get', 'post'],'','BalanceController@index')->middleware('checkRole:stock_balance');
		Route::match(['get', 'post'],'view/{item_id}/{warehouse_id}/{sd}/{ed}','BalanceController@view')->middleware('checkRole:stock_balance_view');
		Route::get('dt','BalanceController@getDt');
		Route::get('excel','BalanceController@downloadExcel');
		Route::get('subdt/{item_id}/{warehouse_id}','BalanceController@subDt');
		Route::get('show_alert/{item_id}/{warehouse_id}','BalanceController@showAlert')->middleware('checkRole:stock_balance');
		Route::get('close_alert/{item_id}/{warehouse_id}','BalanceController@closeAlert')->middleware('checkRole:stock_balance');
	});
	
	Route::group(['prefix' => 'adjust'], function(){
		Route::get('','AdjustController@index')->middleware('checkRole:stock_adjust');
		Route::get('dt','AdjustController@getDt');
		Route::get('add','AdjustController@add')->middleware('checkRole:stock_adjust_add');
		Route::get('edit/{id}','AdjustController@edit')->middleware('checkRole:stock_adjust_edit');
		Route::post('save','AdjustController@save')->middleware('checkRole:stock_adjust_add');
		Route::post('update/{id}','AdjustController@update')->middleware('checkRole:stock_adjust_edit');
		Route::get('delete/{id}','AdjustController@destroy')->middleware('checkRole:stock_adjust_delete');
		Route::get('subdt/{id}','AdjustController@subDt');
		Route::post('remoteItem', 'AdjustController@getItemStock');
		Route::get('GetDetail', 'AdjustController@GetDetail');
	});
	
	Route::group(['prefix' => 'move'], function(){
		Route::get('','MoveController@index')->middleware('checkRole:stock_move');
		Route::get('dt','MoveController@getDt');
		Route::get('add','MoveController@add')->middleware('checkRole:stock_move_add');
		Route::get('edit/{id}','MoveController@edit')->middleware('checkRole:stock_move_edit');
		Route::post('save','MoveController@save')->middleware('checkRole:stock_move_add');
		Route::post('update/{id}','MoveController@update')->middleware('checkRole:stock_move_edit');
		Route::get('delete/{id}','MoveController@destroy')->middleware('checkRole:stock_move_delete');
		Route::get('subdt/{id}','MoveController@subDt');
	});
	
	Route::group(['prefix' => 'use'], function(){
		Route::get('','UsageController@index')->middleware('checkRole:usage_entry');
		Route::get('dt','UsageController@getDt');
		Route::get('add','UsageController@add')->middleware('checkRole:usage_entry_add');
		Route::get('edit/{id}','UsageController@edit')->middleware('checkRole:usage_entry_edit');
		Route::post('save','UsageController@save')->middleware('checkRole:usage_entry_add');
		Route::post('update/{id}','UsageController@update')->middleware('checkRole:usage_entry_edit');
		Route::get('delete/{id}','UsageController@destroy')->middleware('checkRole:usage_entry_delete');
		Route::get('subdt/{id}','UsageController@subDt');
		Route::post('remoteItem', 'UsageController@getItemStocks');
		Route::post('remoteItemWithBuilding', 'UsageController@getItemStock');
		Route::get('ItemCost', 'UsageController@ItemCost');
		Route::get('GetItem', 'UsageController@GetItem');
		Route::get('GetHouse', 'UsageController@GetHouse');
		Route::get('GetStreet', 'UsageController@GetStreet');
		Route::get('GetDetail', 'UsageController@GetDetail');
		Route::get('GetRef', 'UsageController@GetRef');
		// Import & Export Excel
		Route::get('download_excel','UsageController@downloadExcel');
		Route::post('import_excel','UsageController@importExcel');
		Route::get('printUsage/{usageID}','UsageController@printUsage');
		///// Usage with Policy /////
		Route::get('policy','UsagePolicyController@index')->middleware('checkRole:usage_entry');
		Route::get('policy/add','UsagePolicyController@add')->middleware('checkRole:usage_entry_add');
		Route::get('policy/edit/{id}','UsagePolicyController@edit')->middleware('checkRole:usage_entry_edit');
		Route::post('policy/save','UsagePolicyController@save')->middleware('checkRole:usage_entry_add');
		Route::post('policy/update/{id}','UsagePolicyController@update')->middleware('checkRole:usage_entry_edit');
		///// Datatables /////
		Route::get('policy/usagePolicy','UsagePolicyController@usagePolicy');
		Route::get('policy/buildUnit/{itemId}','UsagePolicyController@buildUnit');
		Route::get('GetRef', 'UsageController@GetRef');
		Route::get('updateQtyRemain', 'UsageController@updateRemainQty');
	});

	Route::group(['prefix' => 'use_single'], function(){
		Route::get('','UsageSignleController@index')->middleware('checkRole:usage_entry');
		Route::get('dt','UsageSignleController@getDt');
		Route::get('add','UsageSignleController@add')->middleware('checkRole:usage_entry_add');
		Route::get('edit/{id}','UsageSignleController@edit')->middleware('checkRole:usage_entry_edit');
		Route::post('save','UsageSignleController@save')->middleware('checkRole:usage_entry_add');
		Route::post('update/{id}','UsageSignleController@update')->middleware('checkRole:usage_entry_edit');
		Route::get('delete/{id}','UsageSignleController@destroy')->middleware('checkRole:usage_entry_delete');
		Route::get('subdt/{id}','UsageSignleController@subDt');
		Route::post('remoteItem', 'UsageSignleController@getItemStock');
		// Usage with Policy
		Route::get('policy/add','UsageSignleController@addWithPolicy');
		Route::get('policy/edit/{id}','UsageSignleController@editWithPolicy');
		Route::post('policy/save','UsageSignleController@saveWithPolicy');
		Route::post('policy/update/{id}','UsageSignleController@updateWithPolicy');
	});
	
	Route::group(['prefix' => 'reuse'], function(){
		Route::get('','ReturnUsageController@index')->middleware('checkRole:usage_return');
		Route::get('dt','ReturnUsageController@getDt');
		Route::get('add','ReturnUsageController@add')->middleware('checkRole:usage_return_add');
		Route::get('edit/{id}','ReturnUsageController@edit')->middleware('checkRole:usage_return_edit');
		Route::post('save','ReturnUsageController@save')->middleware('checkRole:usage_return_add');
		Route::post('update/{id}','ReturnUsageController@update')->middleware('checkRole:usage_return_edit');
		Route::get('delete/{id}','ReturnUsageController@destroy')->middleware('checkRole:usage_return_delete');
		Route::get('subdt/{id}','ReturnUsageController@subDt');
		Route::post('remoteItem', 'ReturnUsageController@getItemStock');
		Route::get('GetDetail', 'ReturnUsageController@GetDetail');
	});

	Route::group(['prefix' => 'reuse_single'], function(){
		Route::get('','ReturnUsageSingleController@index')->middleware('checkRole:usage_return');
		Route::get('dt','ReturnUsageSingleController@getDt');
		Route::get('add','ReturnUsageSingleController@add')->middleware('checkRole:usage_return_add');
		Route::get('edit/{id}','ReturnUsageSingleController@edit')->middleware('checkRole:usage_return_edit');
		Route::post('save','ReturnUsageSingleController@save')->middleware('checkRole:usage_return_add');
		Route::post('update/{id}','ReturnUsageSingleController@update')->middleware('checkRole:usage_return_edit');
		Route::get('delete/{id}','ReturnUsageSingleController@destroy')->middleware('checkRole:usage_return_delete');
		Route::get('subdt/{id}','ReturnUsageSingleController@subDt');
		Route::post('remoteItem', 'ReturnUsageSingleController@getItemStock');
	});
	
	Route::group(['prefix' => 'deliv'], function(){
		Route::get('','DeliveryController@index')->middleware('checkRole:delivery_entry');
		Route::get('dt','DeliveryController@getDt');
		Route::get('add','DeliveryController@add')->middleware('checkRole:delivery_entry_add');
		Route::get('add2','DeliveryController@add2')->middleware('checkRole:delivery_entry_add');
		Route::get('edit/{id}','DeliveryController@edit')->middleware('checkRole:delivery_entry_edit');
		Route::get('make_delivery/{id}','DeliveryController@makeDelivery')->middleware('checkRole:delivery_entry_add');
		Route::post('save','DeliveryController@save')->middleware('checkRole:delivery_entry_add');
		Route::post('update/{id}','DeliveryController@update')->middleware('checkRole:delivery_entry_edit');
		Route::get('delete/{id}','DeliveryController@destroy')->middleware('checkRole:delivery_entry_delete');
		Route::get('subdt/{id}','DeliveryController@subDt');
		Route::get('GetItem','DeliveryController@GetItem');
		Route::get('GetUnit','DeliveryController@GetUnit');
		Route::post('remoteDelivery', 'DeliveryController@getDeliveryDetails');
		Route::get('GetRef', 'DeliveryController@GetRef');
		// Import & Export Excel
		Route::get('download_excel','DeliveryController@downloadExcel');
		Route::post('import_excel','DeliveryController@importExcel');
		// New UI
		Route::get('listOfOriginOrders/{orderID}','DeliveryController@listOfOriginOrders');
		Route::get('listOfDeliveriesByOrderID/{orderID}','DeliveryController@listOfDeliveriesByOrderID');
		Route::get('printDelivery/{deliveryID}','DeliveryController@printDelivery');
	});
	
	Route::group(['prefix' => 'redeliv'], function(){
		Route::get('','ReturnDeliveryController@index')->middleware('checkRole:delivery_return');
		Route::get('dt','ReturnDeliveryController@getDt');
		Route::get('add','ReturnDeliveryController@add')->middleware('checkRole:delivery_return_add');
		Route::get('edit/{id}','ReturnDeliveryController@edit')->middleware('checkRole:delivery_return_edit');
		Route::post('save','ReturnDeliveryController@save')->middleware('checkRole:delivery_return_add');
		Route::post('update/{id}','ReturnDeliveryController@update')->middleware('checkRole:delivery_return_edit');
		Route::get('delete/{id}','ReturnDeliveryController@destroy')->middleware('checkRole:delivery_return_delete');
		Route::get('subdt/{id}','ReturnDeliveryController@subDt');
		Route::get('GetItem','ReturnDeliveryController@GetItem');
		Route::post('remoteStock', 'ReturnDeliveryController@getItemStock');
	});
});

///////////////////// route purch ///////////////////////////
Route::group(['prefix' => 'purch'], function() {
	Route::group(['prefix' => 'request'], function(){
		Route::get('','RequestController@index')->middleware('checkRole:purchase_request');
		Route::get('dt','RequestController@getDt');
		Route::get('add/{cid?}','RequestController@add')->middleware('checkRole:purchase_request_add');
		Route::get('edit/{id}','RequestController@edit')->middleware('checkRole:purchase_request_edit');
		Route::post('save','RequestController@save')->middleware('checkRole:purchase_request_add');
		Route::post('update/{id}','RequestController@update')->middleware('checkRole:purchase_request_edit');
		Route::get('delete/{id}','RequestController@destroy')->middleware('checkRole:purchase_request_delete');
		Route::get('close/{id}','RequestController@close')->middleware('checkRole:purchase_request_clone');
		Route::get('view/{id}','RequestController@getStepApprove')->middleware('checkRole:purchase_request_view');
		Route::get('subdt/{id}','RequestController@subDt');
		Route::post('remoteItem', 'RequestController@getItemBOQ');
		Route::post('remotePR', 'RequestController@getRequestDetails');
		// get ref and detail
		Route::get('GetDetail','RequestController@GetDetail');
		Route::get('GetRef','RequestController@GetRef');
		// Import & Export Excel
		Route::get('download_excel','RequestController@downloadExcel');
		Route::post('import_excel','RequestController@importExcel');
	});
	
	Route::group(['prefix' => 'order'], function(){
		Route::get('','OrderController@index')->middleware('checkRole:purchase_order');
		Route::get('dt/{only_active?}/{delivery_part?}','OrderController@getDt');
		Route::get('add/{cid?}','OrderController@add')->middleware('checkRole:purchase_order_add');
		Route::get('addFromClose/{id}/{order_id?}','OrderController@addFromClose');
		Route::get('edit/{id}','OrderController@edit')->middleware('checkRole:purchase_order_edit');
		Route::get('makeOrder/{id}/{order_id?}','OrderController@makeOrder')->middleware('checkRole:purchase_make_order');
		Route::post('save','OrderController@save')->middleware('checkRole:purchase_order_add');
		Route::post('saveCloseOrder/{id}','OrderController@saveCloseOrder')->middleware('checkRole:purchase_order_add');
		Route::post('update/{id}','OrderController@update')->middleware('checkRole:purchase_order_edit');
		Route::get('delete/{id}','OrderController@destroy')->middleware('checkRole:purchase_order_delete');
		Route::get('close/{id}/{make_order?}','OrderController@close')->middleware('checkRole:purchase_order_clone');
		Route::get('view/{id}','OrderController@getStepApprove')->middleware('checkRole:purchase_order_view');
		Route::get('subdt/{id}','OrderController@subDt');
		Route::post('remotePO', 'OrderController@getOrderDetails');
		// get ref and detail
		Route::get('GetDetail','OrderController@GetDetail');
		Route::get('GetRef/{supplierID}','OrderController@GetRef');
		// Import & Export Excel
		Route::get('download_excel','OrderController@downloadExcel');
		Route::post('import_excel','OrderController@importExcel');
	});
});

///////////////////// route approval ///////////////////////////
Route::group(['prefix' => 'approve'], function() {
	Route::group(['prefix' => 'request'], function(){
		Route::get('','ApprovalRequestController@index')->middleware('checkRole:approve_request');
		Route::get('dt','ApprovalRequestController@getDt');
		Route::post('approve/{pr_id}/{role_id}','ApprovalRequestController@approve')->middleware('checkRole:approve_request_signature');
		Route::get('reject/{pr_id}/{role_id}','ApprovalRequestController@reject')->middleware('checkRole:approve_request_reject');
	});
	
	Route::group(['prefix' => 'order'], function(){
		Route::get('','ApprovalOrderController@index')->middleware('checkRole:approve_order');
		Route::get('dt','ApprovalOrderController@getDt');
		Route::post('approve/{po_id}/{role_id}','ApprovalOrderController@approve')->middleware('checkRole:approve_order_signature');
		Route::get('reject/{po_id}/{role_id}','ApprovalOrderController@reject')->middleware('checkRole:approve_reject');
	});
	
});

// USAGE FORMULA
Route::get('usageFormula','UsageFormulaController@index');
Route::post('usageFormula','UsageFormulaController@store');
Route::get('usageFormula/add','UsageFormulaController@add');
Route::get('usageFormula/edit/{id}','UsageFormulaController@edit');
Route::post('usageFormula/update/{id}','UsageFormulaController@update');
Route::delete('usageFormula/delete/{id}','UsageFormulaController@destroy');
Route::get('usageFormula/datatables','UsageFormulaController@datatables');
Route::get('usageFormula/subDatatables/{id}','UsageFormulaController@sub_datatables');
Route::get('usageFormula/downloadExcel','UsageFormulaController@downloadExcel');
Route::post('usageFormula/importExcel','UsageFormulaController@importExcel');
// All Repository
Route::get('repository/getHouses','RepositoryController@getHouses');
Route::get('repository/getSuppliers','RepositoryController@getSuppliers');
Route::get('repository/getWarehouses','RepositoryController@getWarehouses');
Route::get('repository/getEngineers','RepositoryController@getEngineers');
Route::get('repository/getSubcontractors','RepositoryController@getSubcontractors');
Route::get('repository/getHouseTypes','RepositoryController@getHouseTypes');
Route::get('repository/getHousesByHouseType/{houseType}','RepositoryController@getHousesByHouseType');
Route::get('repository/getProductTypes','RepositoryController@getProductTypes');
Route::get('repository/getProductsByProductType/{productType}','RepositoryController@getProductsByProductType');
Route::get('repository/getPurchaseOrderBySupplierID/{supplierID}','RepositoryController@getPurchaseOrderBySupplierID');
Route::get('repository/getOrderItemByOrderID/{orderID}','RepositoryController@getOrderItemByOrderID');
Route::get('repository/getUnitsByItemID/{itemID}','RepositoryController@getUnitsByItemID');
Route::get('repository/getHousesByZoneID/{zoneID}','RepositoryController@getHousesByZoneID');
Route::get('repository/getHousesByBlockID/{zoneID}','RepositoryController@getHousesByBlockID');
Route::get('repository/getHousesByStreetID/{zoneID}','RepositoryController@getHousesByStreetID');
Route::get('repository/getZones','RepositoryController@getZones');
Route::get('repository/getBlocks','RepositoryController@getBlocks');
Route::get('repository/getStreets','RepositoryController@getStreets');
Route::get('repository/getBuildings','RepositoryController@getBuildings');
Route::get('repository/getHouseTypesByZoneID/{zoneID}','RepositoryController@getHouseTypesByZoneID');
Route::get('repository/getHouseTypesByBlockID/{zoneID}','RepositoryController@getHouseTypesByBlockID');
Route::get('repository/getHouseTypesByStreetID/{zoneID}','RepositoryController@getHouseTypesByStreetID');
Route::get('repository/getOrderItemsByOrderID/{orderID}','RepositoryController@getOrderItemsByOrderID');
Route::get('repository/getBlocksByZoneID/{zoneID}','RepositoryController@getBlocksByZoneID');
Route::get('repository/getStreetsByZoneID/{zoneID}','RepositoryController@getStreetsByZoneID');
Route::get('repository/getStreetsByBlockID/{streetID}','RepositoryController@getStreetsByBlockID');
Route::get('repository/getHousesByAllTrigger','RepositoryController@getHousesByAllTrigger');
Route::get('repository/onRequestBOQToUsage','RepositoryController@onRequestBOQToUsage');
Route::get('repository/getUsersByDepartmentID/{departmentID}','RepositoryController@getUsersByDepartmentID');
Route::get('repository/getAssignedUserByRoleID/{roleID}','RepositoryController@getAssignedUserByRoleID');
Route::get('repository/getApprovalUsers','RepositoryController@getApprovalUsers');
Route::get('repository/fetchCurrentBOQ','RepositoryController@fetchCurrentBOQ');
Route::get('repository/checkStockQuantity','RepositoryController@checkStockQuantity');
Route::get('repository/getBoqItems','RepositoryController@getBoqItems');
Route::get('repository/houseNoBoq','RepositoryController@getHouseNoBoq');
Route::get('repository/getBlock','RepositoryController@getBlock');
Route::get('repository/getBuilding','RepositoryController@getBuilding');
Route::get('repository/getHouseType','RepositoryController@getHouseType');