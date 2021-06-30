<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Model\Item;
use App\Model\Order;
use App\Model\OrderItem;
use App\Model\Boq;
use App\Model\BoqItem;
use App\Model\Usage;
use App\Model\UsageDetails;
use App\Model\SystemData;
use App\Model\Constructor;
use App\Model\House;
use App\Model\Warehouse;
use App\Model\Setting;
use App\Model\Supplier;
use App\Model\Unit;
use App\Model\UsageFormula;
use App\User;
use App\Model\UserAssignRole;
use App\Model\Stock;
use App\Model\Request as PurchaseRequest;

class RepositoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('CheckProject');
    }

	public function checkStockQuantity(Request $request){
		$prefix = DB::getTablePrefix();

		$originalDate = $request->input('trans_date');
		$tranDate = date("Y-m-d", strtotime($originalDate));
		$itemId = $request->input('item_id');
		$unit = $request->input('unit');

		$columns = [
			'stocks.*',
			DB::raw("(SELECT (CASE WHEN {$prefix}units.factor=NULL THEN 1 ELSE {$prefix}units.factor END) AS factor FROM {$prefix}units WHERE {$prefix}units.from_code={$unit} AND {$prefix}units.to_code={$prefix}items.unit_stock LIMIT 1) AS factor"),
		];

		$rawStockSQL = Stock::select($columns)
					->leftJoin('items','stocks.item_id','items.id')
					->whereRaw(DB::raw("{$prefix}stocks.item_id={$itemId}"))
					->whereRaw(DB::raw("{$prefix}stocks.trans_date >= '{$tranDate}'"))
					->whereRaw(DB::raw("{$prefix}stocks.delete=0"));

		$stepAColumns = [
			'StepA.*',
			DB::raw("(CASE WHEN StepA.factor < 1 THEN (StepA.qty / StepA.factor) ELSE (StepA.qty * StepA.factor) END) as stock_qty"),
		];

		$stepAQuery = DB::table(DB::raw("({$rawStockSQL}) AS StepA"))->select($stepAColumns)->toSql();

		$stepBColums = [
			'StepB.*',
			DB::raw("SUM(StepB.stock_qty) AS stock_qty"),
		];

		$stepBQuery = DB::table(DB::raw("({$stepAQuery}) AS StepB"))->select($stepBColums);

		$finalQuery = $stepBQuery->groupBy(['item_id']);
		
		$stocks = $finalQuery->get();

		return response()->json($stocks,200); 
	}

	public function fetchCurrentBOQ(Request $request){
		$prefix = DB::getTablePrefix();

		////// BOQ ITEM //////
		$columns = [
			'boq_items.boq_id',
			DB::raw("0 AS use_id"),
			DB::raw("0 AS stock_id"),
			'boq_items.house_id',
			'boq_items.item_id',
			'boq_items.unit',
			DB::raw("SUM({$prefix}boq_items.qty_std) AS qty_std"),
			DB::raw("SUM({$prefix}boq_items.qty_add) AS qty_add"),
			DB::raw("0 AS usage_qty"),
			DB::raw("0 AS stock_qty"),
			'items.code',
			'items.name',
			'items.unit_usage',
			'items.cat_id',
			DB::raw("(SELECT (CASE WHEN {$prefix}units.factor=NULL THEN 1 ELSE {$prefix}units.factor END) AS factor FROM {$prefix}units WHERE {$prefix}units.from_code={$prefix}boq_items.unit AND {$prefix}units.to_code={$prefix}items.unit_usage LIMIT 1) AS factor"),
			DB::raw("'BOQ' AS type")
		];

		$rawBoqs = BoqItem::select($columns)
				->leftJoin('items','boq_items.item_id','items.id');

		////// USAGE DETAIL //////
		$usageDetailColumns = [
			DB::raw("0 AS boq_id"),
			'usage_details.use_id',
			DB::raw("0 AS stock_id"),
			'usage_details.house_id',
			'usage_details.item_id',
			'usage_details.unit',
			DB::raw("0 AS qty_std"),
			DB::raw("0 AS qty_add"),
			DB::raw("SUM({$prefix}usage_details.qty) AS usage_qty"),
			DB::raw("0 AS stock_qty"),
			'items.code',
			'items.name',
			'items.unit_usage',
			'items.cat_id',
			DB::raw("(SELECT (CASE WHEN {$prefix}units.factor=NULL THEN 1 ELSE {$prefix}units.factor END) AS factor FROM {$prefix}units WHERE {$prefix}units.from_code={$prefix}usage_details.unit AND {$prefix}units.to_code={$prefix}items.unit_usage LIMIT 1) AS factor"),
			DB::raw("'USAGE' AS type")
		];
		
		$usageDetails = UsageDetails::select($usageDetailColumns)
						->leftJoin('items','usage_details.item_id','items.id')
						->whereRaw(DB::raw("{$prefix}usage_details.delete=0"));

		////// STOCK ///////
		$stockColumns = [
			DB::raw("0 AS boq_id"),
			DB::raw("0 AS use_id"),
			DB::raw("0 AS stock_id"),
			DB::raw("0 AS house_id"),
			'stocks.item_id',
			'stocks.unit',
			DB::raw("0 AS qty_std"),
			DB::raw("0 AS qty_add"),
			DB::raw("0 AS usage_qty"),
			DB::raw("SUM({$prefix}stocks.qty) AS stock_qty"),
			'items.code',
			'items.name',
			'items.unit_usage',
			'items.cat_id',
			DB::raw("(SELECT (CASE WHEN {$prefix}units.factor=NULL THEN 1 ELSE {$prefix}units.factor END) AS factor FROM {$prefix}units WHERE {$prefix}units.from_code={$prefix}stocks.unit AND {$prefix}units.to_code={$prefix}items.unit_usage LIMIT 1) AS factor"),
			DB::raw("'STOCK' AS type")
		];

		$rawStocks = Stock::select($stockColumns)
						->leftJoin('items','stocks.item_id','items.id')
						->whereRaw(DB::raw("{$prefix}stocks.delete=0"));

		if($zoneId = $request->input("zone_id")){
			if($houseIds  = House::where('zone_id',$zoneId)->pluck('id')){
				$houseIds = trim((string)$houseIds,'[]');
				$rawBoqs  = $rawBoqs->whereRaw(DB::raw("{$prefix}boq_items.house_id IN({$houseIds})"));
				$usageDetails = $usageDetails->whereRaw(DB::raw("{$prefix}usage_details.house_id IN({$houseIds})"));
			}
		}

		if($blockId = $request->input("block_id")){
			if($houseIds  = House::where('block_id',$blockId)->pluck('id')){
				$houseIds = trim((string)$houseIds,'[]');
				$rawBoqs  = $rawBoqs->whereRaw(DB::raw("{$prefix}boq_items.house_id IN({$houseIds})"));
				$usageDetails = $usageDetails->whereRaw(DB::raw("{$prefix}usage_details.house_id IN({$houseIds})"));
			}
		}

		if($streetId = $request->input("street_id")){
			if($houseIds  = House::where('street_id',$streetId)->pluck('id')){
				$houseIds = trim((string)$houseIds,'[]');
				$rawBoqs  = $rawBoqs->whereRaw(DB::raw("{$prefix}boq_items.house_id IN({$houseIds})"));
				$usageDetails = $usageDetails->whereRaw(DB::raw("{$prefix}usage_details.house_id IN({$houseIds})"));
			}
		}

		if($houseId = $request->input("house_id")){
			$rawBoqs = $rawBoqs->whereRaw(DB::raw("{$prefix}boq_items.house_id={$houseId}"));
			$usageDetails = $usageDetails->whereRaw(DB::raw("{$prefix}usage_details.house_id={$houseId}"));
		}

		if($itemId = $request->input('item_id')){
			$rawBoqs = $rawBoqs->whereRaw(DB::raw("{$prefix}boq_items.item_id={$itemId}"));
			$usageDetails = $usageDetails->whereRaw(DB::raw("{$prefix}usage_details.item_id={$itemId}"));
			$rawStocks = $rawStocks->whereRaw(DB::raw("{$prefix}stocks.item_id={$itemId}"));
		}

		if($originalDate = $request->input('trans_date')){
			$transDate = date("Y-m-d", strtotime($originalDate));
			$rawStocks = $rawStocks->whereRaw(DB::raw("{$prefix}stocks.trans_date <='{$transDate}'"));
		}

		$rawBoqs = $rawBoqs->groupBy(['house_id','item_id','unit'])->toSql();
		$usageDetails = $usageDetails->groupBy(['house_id','item_id','unit'])->toSql();
		$rawStocks = $rawStocks->groupBy(['item_id','unit'])->toSql();

		////// STOCK //////
		$onlyStockColumns = [
			'*',
			DB::raw("0 AS qty_std_x"),
			DB::raw("0 AS qty_add_x"),
			DB::raw("0 AS usage_qty_x"),
			DB::raw("(CASE WHEN STK.factor < 1 THEN (STK.stock_qty / STK.factor) ELSE (STK.stock_qty * STK.factor) END) as stock_qty_x"),
		];
		$onlyStocks = DB::table(DB::raw("({$rawStocks}) AS STK"))
					->select($onlyStockColumns);

		////// BOQ ITEM //////
		$onlyBoqItemColumns = [
			'*',
			DB::raw("(CASE WHEN OBI.factor < 1 THEN (OBI.qty_std / OBI.factor) ELSE (OBI.qty_std * OBI.factor) END) as qty_std_x"),
			DB::raw("(CASE WHEN OBI.factor < 1 THEN (OBI.qty_add / OBI.factor) ELSE (OBI.qty_add * OBI.factor) END) as qty_add_x"),
			DB::raw("0 AS usage_qty_x"),
			DB::raw("0 AS stock_qty_x"),
		];
		$onlyBoqItems = DB::table(DB::raw("({$rawBoqs}) AS OBI"))
						->select($onlyBoqItemColumns);
		////// USAGE DETAIL //////
		$onlyUsageDetailColumns = [
			'*',
			DB::raw("0 AS qty_std_x"),
			DB::raw("0 AS qty_add_x"),
			DB::raw("(CASE WHEN OUD.factor < 1 THEN (OUD.usage_qty / OUD.factor) ELSE (OUD.usage_qty * OUD.factor) END) as usage_qty_x"),
			DB::raw("0 AS stock_qty_x"),
		];
		$onlyUsageDetails = DB::table(DB::raw("({$usageDetails}) AS OUD"))
						->select($onlyUsageDetailColumns);

		// Combined BOQ Item with Usage Detail together
		// $onlyBoqItems as BoqItem Model
		// $onlyUsageDetails as UsageDetails Model
		$unionBoqItemWithUsageDetail = $onlyBoqItems->unionAll($onlyUsageDetails)
													->unionAll($onlyStocks)
													->toSql();

		$unionBoqItemWithUsageDetailColumns = [
			'type',
			'boq_id',
			'use_id',
			'house_id',
			'cat_id',
			'item_id',
			'code',
			'name',
			'factor',
			'unit',
			'unit_usage',
			DB::raw("SUM(BIWUD.qty_std) as qty_std"),
			DB::raw("SUM(BIWUD.qty_add) as qty_add"),
			DB::raw("SUM(BIWUD.usage_qty) as usage_qty"),
			DB::raw("SUM(BIWUD.stock_qty) as stock_qty"),
			DB::raw("SUM(BIWUD.qty_std_x) as qty_std_x"),
			DB::raw("SUM(BIWUD.qty_add_x) as qty_add_x"),
			DB::raw("SUM(BIWUD.usage_qty_x) as usage_qty_x"),
			DB::raw("SUM(BIWUD.stock_qty_x) as stock_qty_x"),
		];

		$unionBoqItemWithUsageDetail = DB::table(DB::raw("({$unionBoqItemWithUsageDetail}) AS BIWUD"))
										->select($unionBoqItemWithUsageDetailColumns);
		
		$boqs = $unionBoqItemWithUsageDetail->groupBy(['item_id']);
		
		$boqs = $boqs->get();

		return response()->json($boqs,200);
	}

	public function getApprovalUsers(Request $request){
		$users = User::where(['delete'=> 0, 'approval_user'=> 1])->get();
		return response()->json($users,200);
	}

	public function getAssignedUserByRoleID(Request $request,$roleID = 0){
		$userAssignedRoles = UserAssignRole::where('role_id',$roleID)->groupBy(['role_id','user_id'])->get();
		return response()->json($userAssignedRoles,200);
	}

	public function getUsersByDepartmentID(Request $request,$departmentID = 0){
		$users = User::where('delete',0);
		if($user1 = User::where('dep_id',$departmentID)->whereNotIn('id',[config('app.owner'),config('app.admin')])->where('delete',0)->pluck('id')){
			$users = $users->whereIn('id',$user1);
		}

		if($user2 = User::where('department2',$departmentID)->whereNotIn('id',[config('app.owner'),config('app.admin')])->where('delete',0)->pluck('id')){
			$users = $users->orWhereIn('id',$user2);
		}

		if($user3 = User::where('department3',$departmentID)->whereNotIn('id',[config('app.owner'),config('app.admin')])->where('delete',0)->pluck('id')){
			$users = $users->orWhereIn('id',$user3);
		}
		$users = $users->get();
		return response()->json($users,200);
	}

	public function onRequestBOQToUsage(Request $request){
		$columns  = [
			'boq_items.*',
		];
		$boqUsage = BoqItem::select($columns)
		->leftJoin('usage_details',function($join){
			$join->on('usage_details.house_id','boq_items.house_id')
				 ->on('usage_details.item_id','boq_items.item_id');
		});
		
		if($zoneId = $request->input('zoneId')){
			if($houseIds = House::where(['zone_id' => $zoneId])->pluck('id')){
				$boqUsage = $boqUsage->whereIn('boq_items.house_id',$houseIds);
			}
		}

		if($streetId = $request->input('streetId')){
			if($houseIds = House::where(['street_id' => $streetId])->pluck('id')){
				$boqUsage = $boqUsage->whereIn('boq_items.house_id',$houseIds);
			}
		}

		if($houseId = $request->input('houseId')){
			$boqUsage = $boqUsage->where('boq_items.house_id',$houseId);
		}

		if($itemId = $request->input('itemId')){
			$boqUsage = $boqUsage->where('boq_items.item_id',$itemId);
		}

		$boqUsage = $boqUsage->groupBy(['boq_items.house_id','boq_items.item_id'])->get();
		return response()->json($boqUsage,200);
	}

	public function getHousesByAllTrigger(Request $request){
		$prefix = DB::getTablePrefix();
		$houses = House::select([
			'*',
			DB::raw("(SELECT {$prefix}system_datas.name FROM {$prefix}system_datas WHERE {$prefix}system_datas.id = {$prefix}houses.zone_id) AS zone"),
			DB::raw("(SELECT {$prefix}system_datas.name FROM {$prefix}system_datas WHERE {$prefix}system_datas.id = {$prefix}houses.block_id) AS block"),
			DB::raw("(SELECT {$prefix}system_datas.name FROM {$prefix}system_datas WHERE {$prefix}system_datas.id = {$prefix}houses.street_id) AS street"),
			DB::raw("(SELECT {$prefix}system_datas.name FROM {$prefix}system_datas WHERE {$prefix}system_datas.id = {$prefix}houses.house_type) AS houseType"),
		]);

		if($zoneID = $request->input('zone_id')){
			$houses = $houses->where('zone_id',$zoneID);
		}

		if($blockID = $request->input('block_id')){
			$houses = $houses->where('block_id',$blockID);
		}

		if($streetID = $request->input('street_id')){
			$houses = $houses->where('street_id',$streetID);
		}

		if($houseType = $request->input('house_type')){
			$houses = $houses->where('house_type',$houseType);
		}

		$houses = $houses->get();

		return response()->json($houses,200); 
	}

	public function getBlocksByZoneID(Request $request,$zoneID){
		if($blockIds = House::where(['zone_id' => $zoneID])->pluck('block_id')){
			$blocks = SystemData::whereIn('id',$blockIds)->get();
		}else{
			$blocks = [];
		}
		return response()->json($blocks,200); 
	}

	public function getStreetsByZoneID(Request $request,$zoneID){
		if($streetIds = House::where(['zone_id' => $zoneID])->pluck('street_id')){
			$streets = SystemData::whereIn('id',$streetIds)->get();
		}else{
			$streets = [];
		}
		return response()->json($streets,200); 
	}

	public function getStreetsByBlockID(Request $request,$blockID){
		if($streetIds = House::where(['block_id' => $blockID])->pluck('street_id')){
			$streets = SystemData::whereIn('id',$streetIds)->get();
		}else{
			$streets = [];
		}
		return response()->json($streets,200);
	}

	public function getHouses(Request $request){
		$houses = House::get();
		return response()->json($houses,200); 
	}

	public function getHouseTypesByZoneID(Request $request,$zoneID = null){
		if(empty($zoneID)){
			$houseTypes = [];
		}else{
			if($houseTypeIds = House::where(['zone_id' => $zoneID])->pluck('house_type')){
				$houseTypes = SystemData::where('type','HT')->whereIn('id',$houseTypeIds)->get();
			}else{
				$houseTypes = [];
			}
		}
		return response()->json($houseTypes,200); 
	}

	public function getHouseTypesByBlockID(Request $request,$blockID = null){
		if(empty($blockID)){
			$houseTypes = [];
		}else{
			if($houseTypeIds = House::where(['block_id' => $blockID])->pluck('house_type')){
				$houseTypes = SystemData::where('type','HT')->whereIn('id',$houseTypeIds)->get();
			}else{
				$houseTypes = [];
			}
		}
		return response()->json($houseTypes,200);
	}

	public function getHouseTypesByStreetID(Request $request,$streetID = null){
		if(empty($streetID)){
			$houseTypes = [];
		}else{
			if($houseTypeIds = House::where(['street_id' => $streetID])->pluck('house_type')){
				$houseTypes = SystemData::where('type','HT')->whereIn('id',$houseTypeIds)->get();
			}else{
				$houseTypes = [];
			}
		}
		return response()->json($houseTypes,200);
	}

	public function getHousesByZoneID(Request $request,$zoneID = null){
		if(empty($zoneID)){
			$houses = [];
		}else{
			$houses = House::where(['zone_id' => $zoneID])->get();
		}
		return response()->json($houses,200); 
	}

	public function getHousesByBlockID(Request $request,$blockID = null){
		if(empty($blockID)){
			$houses = [];
		}else{
			$houses = House::where(['block_id' => $blockID])->get();
		}
		return response()->json($houses,200);
	}

	public function getHousesByStreetID(Request $request,$streetID = null){
		if(empty($streetID)){
			$houses = [];
		}else{
			$houses = House::where(['street_id' => $streetID])->get();
		}
		return response()->json($houses,200);
	}

	public function getZones(Request $request){
		$projectID = $request->session()->get('project');
		$zones = SystemData::select(['id','name'])->where(['type' => "ZN"])->get();
		return response()->json($zones,200);
	}

	public function getBlocks(Request $request){
		$projectID = $request->session()->get('project');
		$blocks = SystemData::select(['id','name'])->where(['type' => "BK"])->get();
		return response()->json($blocks,200);
	}

	public function getStreets(Request $request){
		$projectID = $request->session()->get('project');
		$streets = SystemData::select(['id','name'])->where(['type' => "ST"])->get();
		return response()->json($streets,200);
	}

    // All Repository
	public function getWarehouses(Request $request){
		$warehouses = Warehouse::select(['id','name'])->get();
		return response()->json($warehouses,200);
	}

	public function getEngineers(Request $request){
		$engineers = Constructor::select(['id','id_card','name'])->where(['type' => 1])->get();
		return response()->json($engineers,200);
	}

	public function getSubcontractors(Request $request){
		$subcontractors = Constructor::select(['id','id_card','name'])->where(['type' => 2])->get();
		return response()->json($subcontractors,200);
	}

	public function getHouseTypes(Request $request){
		$projectID = $request->session()->get('project');
		$houseTypes = SystemData::select(['id','name'])->where(['type' => "HT"])->get();
		return response()->json($houseTypes,200);
	}

	public function getHousesByHouseType(Request $request,$houseType = null){
		if(empty($houseType)){
			$houses = [];
		}else{
			$houses = House::where(['house_type' => $houseType])->get();
		}
		return response()->json($houses,200); 
	}

	public function getProductTypes(Request $request){
		$projectID = $request->session()->get('project');
		$productTypes = SystemData::select(['id','name'])->where(['type' => "IT"])->get();
		return response()->json($productTypes,200);
	}

	public function getProductsByProductType(Request $request,$productType = null){
		if(empty($productType)){
			$products = [];
		}else{
			$products = Item::where(['cat_id' => $productType])->get();
		}
		return response()->json($products,200); 
	}

	public function getPurchaseOrderBySupplierID(Request $request, $supplierID = null){
		$projectID = $request->session()->get('project');
		if(empty($supplierID)){
			$orders = [];
		}else{
			$orders = Order::select(['id','ref_no','delivery_address'])->where([
				'pro_id' => $projectID, 
				'sup_id' => $supplierID,
				'trans_status' => 3
			])->get();
		}
		return response()->json($orders,200);
	}

	public function getSuppliers(Request $request){
		$suppliers = Supplier::select(['id','name','desc'])->where(['status' => 1])->get();
		return response()->json($suppliers,200);
	}

    public function getOrderItemByOrderID(Request $request,$orderID){
        $orderItems = [];
        if($orderID) {
            $columns = [
                'items.code',
                'items.name',
                'items.cat_id',
                'items.cost_purch',
                'items.unit_stock',
                'items.unit_usage',
                'items.unit_purch',
                'order_items.*'
            ];
            $orderItems = OrderItem::select($columns)
                ->leftJoin('items','items.id','order_items.item_id')
                ->where('order_items.po_id',$orderID)
                ->get();
        }
        return response()->json($orderItems,200);
    }

    public function getUnitsByItemID(Request $request,$itemID){
        $units = [];
        if($itemID){
            $units = Unit::leftJoin('items','items.unit_stock','units.to_code')
                ->where('items.id',$itemID)->get();
        }
        return response()->json($units,200);
    }

	public function getOrderItemsByOrderID(Request $request,$orderID){
		$items = [];
		if($orderID){
            $items = OrderItem::leftJoin('items','items.id','order_items.item_id')
                ->where('order_items.po_id',$orderID)->get();
        }
        return response()->json($items,200);
	}
}