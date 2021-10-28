<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Excel;
use Auth;
use DB;
use Datatables;
use Redirect;
use App\Model\Usage;
use App\Model\BoqItem;
use App\Model\House;
use App\Model\Stock;
use App\Model\Item;
use App\Model\UsageDetails;
use App\Model\Warehouse;
use App\Model\SystemData;
use App\Model\Unit;
use App\Model\Constructor;
use App\Model\Project;
use App\Model\UsageFormula;
use App\Model\UsageFormulaDetail;
use App\User;

class UsagePolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
		$this->middleware('CheckProject');
    }

	public function index(){
		$data = [
			'title'			=> trans('lang.usage'),
			'icon'			=> 'fa fa-legal',
			'small_title'	=> trans('lang.usage_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'dashboard'	=> [
						'caption' 	=> trans('lang.usage'),
				],
			],
			'rounte'		=> url("stock/use/dt"),
		];
		
		if(hasRole('usage_entry_add')){
			$data = array_merge($data, ['rounteAdd'=> url('stock/use/policy/add')]);
		}
		return view('stock.usage.entry.policy.index',$data);
	}

    public function add(Request $request){
		$data = [
			'title'			=> trans('lang.usage'),
			'icon'			=> 'fa fa-plus',
			'small_title'	=> trans('lang.add_new'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'index'	=> [
						'url' 		=> url('stock/use'),
						'caption' 	=> trans('lang.usage'),
				],
				'add'	=> [
						'caption' 	=> trans('lang.add_new'),
				],
			],
			'rounteSave'	=> url('stock/use/policy/save'),
			'rounteBack'	=> url('stock/use'),
			'pro_id'		=> $request->session()->get('project'),
		];
		return view('stock.usage.entry.policy.add',$data);
	}

    public function edit(Request $request, $id){
		$id = decrypt($id);
		$obj = Usage::find($id);
		if($obj){
			$data = [
				'title'			=> trans('lang.usage'),
				'icon'			=> 'fa fa-edit',
				'small_title'	=> trans('lang.edit'),
				'background'	=> '',
				'link'			=> [
					'home'	=> [
							'url' 		=> url('/'),
							'caption' 	=> trans('lang.home'),
					],
					'index'	=> [
							'url' 		=> url('stock/use'),
							'caption' 	=> trans('lang.usage'),
					],
					'edit'	=> [
							'caption' 	=> trans('lang.edit'),
					],
				],
				'rounteSave'	=> url('stock/use/policy/update/'.$id),		
				'rounteBack'	=> url('stock/use/policy'),		
				'obj'	=> $obj,
				'pro_id'=> $request->session()->get('project'),
			];
			return view('stock.usage.entry.policy.edit',$data);
		}else{
			return redirect()->back();
		}
	}

	public function buildUnit(Request $request,$itemId){
		$units = Unit::leftJoin('items','units.to_code','items.unit_stock')->where('items.id',$itemId)->get();
		return response()->json($units,200);
	}

	public function usagePolicy(Request $request){

		$columns = [
			'usage_formula_details.formula_id',
			'usage_formulas.code',
			'usage_formulas.zone_id',
			'usage_formulas.block_id',
			'usage_formulas.street_id',
			'usage_formula_details.house_id',
			'houses.house_no',
			'usage_formula_details.item_id',
			'usage_formula_details.percentage',
		];

		$policy = UsageFormulaDetail::select($columns)
				->leftJoin('usage_formulas','usage_formula_details.formula_id','usage_formulas.id')
				->leftJoin('houses','usage_formula_details.house_id','houses.id');

		if($zoneId = $request->input('zone_id')){
			$policy = $policy->where('usage_formulas.zone_id',$zoneId);
		}

		if($blockId = $request->input('block_id')){
			$policy = $policy->where('usage_formulas.block_id',$blockId);
		}

		if($streetId = $request->input('street_id')){
			$policy = $policy->where('usage_formulas.street_id',$streetId);
		}

		if($houseId = $request->input('house_id')){
			$policy = $policy->where('usage_formula_details.house_id',$houseId);
		}

		$policy = $policy->get();
		return Datatables::of($policy)->make(true);
	}

	public function save(Request $request){
		// print_r($request->all());exit;
		try {
			DB::beginTransaction();
			$rules = [
				'reference_no' 	=>'required|max:20|unique_stock_use',
				'trans_date' 	=>'required|max:20',
				'reference' 	=>'required',
				'engineer' 		=>'required|max:11',
				'warehouse_id' 	=>'required|max:11',
			];
	
			if(getSetting()->usage_constructor==1){
				$rules['sub_const']= 'required|max:11';
			}
	
			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){
					$rules['line_index.'.$i] 	= 'required';
					$rules['item_id.'.$i]		= 'required|max:11';
					$rules['unit_id.'.$i]		= 'required';
					$rules['qty.'.$i]			= 'required';
					$rules['stock_qty.'.$i]		= 'required';
				}
			}

			// Validator::make($request->all(),$rules)->validate();

			$originalDate = $request['trans_date'];
			$trans_date = date("Y-m-d", strtotime($originalDate));

			$data = [
				'pro_id'		=>$request->session()->get('project'),
				'ref_no'		=>$request->reference_no,
				'trans_date'	=>$trans_date,
				'reference'		=>$request->reference,
				'eng_usage'		=>$request->engineer,
				'zone_id'		=> $request->zone_id,
				'block_id'		=> $request->block_id,
				'building_id'	=>	$request->building_id,
				'desc'			=>$request->desc,
				'created_by'	=>Auth::user()->id,
				'created_at'	=>date('Y-m-d H:i:s'),
			];
			$qty =0;

			if(getSetting()->usage_constructor==1){
				$data = array_merge($data, ['sub_usage'=>$request->sub_const]);
			}
			// print_r($data);exit;
			if(!$id = DB::table('usages')->insertGetId($data)){
				throw new \Exception("Usage[{$request->reference_no}] not insert");
			}

			// Usage Policy
			$usagePolicyColumns = [
				'usage_formulas.id',
				'usage_formulas.code',
				'usage_formulas.zone_id',
				'usage_formulas.block_id',
				'usage_formulas.street_id',
				'usage_formula_details.house_id',
				'usage_formula_details.percentage',
			];

			$usagePolicies = UsageFormulaDetail::select($usagePolicyColumns)
				->leftJoin('usage_formulas','usage_formula_details.formula_id','usage_formulas.id')
				->where('usage_formulas.status',1);

			if($zoneId = $request->input('zone_id')){
				$usagePolicies = $usagePolicies->where('usage_formulas.zone_id',$zoneId);
			}

			if($blockId = $request->input('block_id')){
				$usagePolicies = $usagePolicies->where('usage_formulas.block_id',$blockId);
			}

			// print_r($usagePolicies->toSql());exit;

			if($streetId = $request->input('street_id')){
				$usagePolicies = $usagePolicies->where('usage_formulas.street_id',$streetId);
			}

			if($houseId = $request->input('house_id')){
				$usagePolicies = $usagePolicies->where('usage_formula_details.house_id',$houseId);
			}

			
			$usagePolicies = $usagePolicies->get();
			
				// print_r($usagePolicies);exit;
			if(count($usagePolicies) == 0){
				$usagePolicies = [];
				throw new \Exception("Usage policy value not found");
			}
			$qty =0;
			
			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){
					foreach($usagePolicies as $policy){
						$reqQTY = floatval($request['qty'][$i]);
						$calQTY = $reqQTY * (floatval($policy->percentage) / 100);
						if(!$house = House::find($policy->house_id)){
							throw new \Exception("House not found");
						}
						// $id = 1;
						//// FILLED USAGE DETAIL ROW ////
						$detail = [
							'use_id'         =>	$id,
							'warehouse_id'   =>	$request['warehouse_id'],
							'house_id'       =>	$house->id,
							'street_id'      =>	$house->street_id,
							'zone_id'      =>	$house->zone_id,
							'block_id'      =>	$house->block_id,
							'building_id'      =>	$house->building_id,
							'line_no'        =>	$request['line_index'][$i],
							'item_id'        =>	$request['item_id'][$i],
							'unit'           =>	$request['unit_id'][$i],
							'stock_qty'      =>	$request['qty'][$i],
							'boq_set'        =>	$request['boq_set'][$i],
							'created_by'     =>	Auth::user()->id,
							'created_at'     =>	date('Y-m-d H:i:s'),
							
						];
						
						$stockOut = [
							'pro_id'         =>$request->session()->get('project'),
							'ref_id'         =>$id,
							'ref_no'         =>$request->reference_no,
							'ref_type'       =>'usage items',
							'line_no'        =>$request['line_index'][$i],
							'item_id'        =>$request['item_id'][$i],
							// 'unit'           =>$request['unit_id'][$i],
							'warehouse_id'   =>$request['warehouse_id'],
							'trans_date'     =>$trans_date,
							'reference'      =>$request->reference,
							'trans_ref'      =>'O',
							'alloc_ref'      =>getAllocateRef($request->reference_no),
							'created_by'     =>Auth::user()->id,
							'created_at'     =>date('Y-m-d H:i:s'),
						];
						if (getSetting()->is_costing==1) {
							$calQTYRemain = $calQTY;
							$costArr = getItemCost($request['item_id'][$i],$request['unit_id'][$i],$calQTY);
							if ($costArr) {
								if (is_array($costArr) || is_object($costArr)) {
									foreach ($costArr as $cArr) {
										$qty =0;
										$remain_qty = 0;
										// print_r($cArr);
										if($cArr['remain_qty'] >= $cArr['qty']){
											$remain_qty = $cArr['remain_qty'] - $cArr['qty'];
											$qty 	= $cArr['qty'] * -1;
											$cost 	= $cArr['cost'];
											$amount = $qty * $cost;
											$originalAmount = $amount;
											$orginalQty = ($cArr['qty']/$cArr['unit_qty']);
											$originalCost = $originalAmount / ($orginalQty > 0 ? $orginalQty : 1);
											
											$stockOut = array_merge($stockOut,['unit'=>$cArr['unit']]);
											$stockOut = array_merge($stockOut,['qty'    => $qty]);
											$stockOut = array_merge($stockOut,['cost'   => $cost]);
											$stockOut = array_merge($stockOut,['amount' => $amount]);
	
											$detail = array_merge($detail,['qty'    => $orginalQty]);
											$detail = array_merge($detail,['total_cost'    => $originalAmount]);
											$detail = array_merge($detail,['cost' => $originalCost]);
											$detail = array_merge($detail,['qty_converted'=> $qty]);
											$detail = array_merge($detail,['price_converted'=>$cost]);
											$detail = array_merge($detail,['total_price_converted'=>$amount]);
											$detail = array_merge($detail,['unit_converted'=>$cArr['unit']]);
											$calQTY = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
											$calQTYRemain = 0;
										}else{
											$qty = $cArr['remain_qty'] * -1;
											$cost 	= $cArr['cost'];
											$amount = $qty * $cost;
											$originalAmount = $amount;
											$orginalQty = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
											$originalCost = $originalAmount / ($orginalQty > 0 ? $orginalQty : 1);
											$stockOut = array_merge($stockOut,['unit'	=> $cArr['unit']]);
											$stockOut = array_merge($stockOut,['qty'    => $qty]);
											$stockOut = array_merge($stockOut,['cost'   => $cost]);
											$stockOut = array_merge($stockOut,['amount' => $amount]);
	
											$detail = array_merge($detail,['qty'    => $orginalQty]);
											$detail = array_merge($detail,['total_cost'    => $originalAmount]);
											$detail = array_merge($detail,['cost' => $originalCost]);
											$detail = array_merge($detail,['qty_converted'=> $qty]);
											$detail = array_merge($detail,['price_converted'=>$cost]);
											$detail = array_merge($detail,['total_price_converted'=>$amount]);
											$detail = array_merge($detail,['unit_converted'=>$cArr['unit']]);
											$calQTY = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
											$calQTYRemain = $calQTY;
											$remain_qty = 0;
										}
										if(!$stockOutId = DB::table('stocks')->insertGetId($stockOut)){
											DB::rollback();
											throw new \Exception("Stock Out[{$i}] not insert.");
										}else{
											Stock::where('id',$cArr['stock_id'])->update(['remain_qty'=>$remain_qty]);
											if(!$usageDetailId = DB::table('usage_details')->insertGetId($detail)){
												DB::rollback();
												throw new \Exception("UsageDetail[{$i}] not insert.");
											}
										}
									}
										if($calQTY > 0){
											$costArr = getItemCost($request['item_id'][$i],$request['unit_id'][$i],$calQTY);
											foreach ($costArr as $cArr) {
												$remain_qty = 0;
												if($cArr['remain_qty'] >= $cArr['qty']){
													$remain_qty = $cArr['remain_qty'] - $cArr['qty'];
													$qty 	= $cArr['qty'] * -1;
													$cost 	= $cArr['cost'];
													$amount = $qty * $cost;
													$originalAmount = $amount;
													$orginalQty = ($cArr['qty']/$cArr['unit_qty']);
													$originalCost = $originalAmount / ($orginalQty > 0 ? $orginalQty : 1);
													
													$stockOut = array_merge($stockOut,['unit'=>$cArr['unit']]);
													$stockOut = array_merge($stockOut,['qty'    => $qty]);
													$stockOut = array_merge($stockOut,['cost'   => $cost]);
													$stockOut = array_merge($stockOut,['amount' => $amount]);
	
													$detail = array_merge($detail,['qty'    => $orginalQty]);
													$detail = array_merge($detail,['total_cost'    => $originalAmount]);
													$detail = array_merge($detail,['cost' => $originalCost]);
													$detail = array_merge($detail,['qty_converted'=> $qty]);
													$detail = array_merge($detail,['price_converted'=>$cost]);
													$detail = array_merge($detail,['total_price_converted'=>$amount]);
													$detail = array_merge($detail,['unit_converted'=>$cArr['unit']]);
													$calQTY = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
													$calQTYRemain = 0;
												}else{
													$qty = $cArr['remain_qty'] * -1;
													$cost 	= $cArr['cost'];
													$amount = $qty * $cost;
													$originalAmount = $amount;
													$orginalQty = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
													$originalCost = $originalAmount / ($orginalQty > 0 ? $orginalQty : 1);
													$stockOut = array_merge($stockOut,['unit'=>$cArr['unit']]);
													$stockOut = array_merge($stockOut,['qty'    => $qty]);
													$stockOut = array_merge($stockOut,['cost'   => $cost]);
													$stockOut = array_merge($stockOut,['amount' => $amount]);
	
													$detail = array_merge($detail,['qty'    => $calQTY - ($cArr['remain_qty']/$cArr['unit_qty'])]);
													$detail = array_merge($detail,['total_cost'    => $originalAmount]);
													$detail = array_merge($detail,['cost' => $originalCost]);
													$detail = array_merge($detail,['qty_converted'=> $qty]);
													$detail = array_merge($detail,['price_converted'=>$cost]);
													$detail = array_merge($detail,['total_price_converted'=>$amount]);
													$detail = array_merge($detail,['unit_converted'=>$cArr['unit']]);
													$calQTY = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
													$calQTYRemain = $calQTY;
													$remain_qty = 0;
												}
												if(!$stockOutId = DB::table('stocks')->insertGetId($stockOut)){
													DB::rollback();
													throw new \Exception("Stock Out[{$i}] not insert.");
												}else{
													Stock::where('id',$cArr['stock_id'])->update(['remain_qty'=>$remain_qty]);
													if(!$usageDetailId = DB::table('usage_details')->insertGetId($detail)){
														DB::rollback();
														throw new \Exception("UsageDetail[{$i}] not insert.");
													}
												}
											}
										}
	
										if($calQTY > 0){
											$costArr = getItemCost($request['item_id'][$i],$request['unit_id'][$i],$calQTY);
											foreach ($costArr as $cArr) {
												$remain_qty = 0;
												if($cArr['remain_qty'] >= $cArr['qty']){
													$remain_qty = $cArr['qty'] - $cArr['qty'];
													$qty 	= $cArr['qty'] * -1;
													$cost 	= $cArr['cost'];
													$amount = $qty * $cost;
													$originalAmount = $amount;
													$originalCost = $originalAmount / ($calQTY > 0 ? $calQTY : 1);
													$orginalQty =($cArr['remain_qty']/$cArr['unit_qty']);
													$stockOut = array_merge($stockOut,['unit'=>$cArr['unit']]);
													$stockOut = array_merge($stockOut,['qty'    => $qty]);
													$stockOut = array_merge($stockOut,['cost'   => $cost]);
													$stockOut = array_merge($stockOut,['amount' => $amount]);
	
													$detail = array_merge($detail,['qty'    => $orginalQty]);
													$detail = array_merge($detail,['total_cost'    => $originalAmount]);
													$detail = array_merge($detail,['cost' => $originalCost]);
													$detail = array_merge($detail,['qty_converted'=> $qty]);
													$detail = array_merge($detail,['price_converted'=>$cost]);
													$detail = array_merge($detail,['total_price_converted'=>$amount]);
													$detail = array_merge($detail,['unit_converted'=>$cArr['unit']]);
													$calQTY = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
													$calQTYRemain = 0;
												}else{
													$qty = $cArr['remain_qty'] * -1;
													$cost 	= $cArr['cost'];
													$amount = $qty * $cost;
													$originalAmount = $amount;
													$orginalQty = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
													$originalCost = $originalAmount / ($orginalQty > 0 ? $orginalQty : 1);
													$stockOut = array_merge($stockOut,['unit'=>$cArr['unit']]);
													$stockOut = array_merge($stockOut,['qty'    => $qty]);
													$stockOut = array_merge($stockOut,['cost'   => $cost]);
													$stockOut = array_merge($stockOut,['amount' => $amount]);
	
													$detail = array_merge($detail,['qty'    => $calQTY - ($cArr['remain_qty']/$cArr['unit_qty'])]);
													$detail = array_merge($detail,['total_cost'    => $originalAmount]);
													$detail = array_merge($detail,['cost' => $originalCost]);
													$detail = array_merge($detail,['qty_converted'=> $qty]);
													$detail = array_merge($detail,['price_converted'=>$cost]);
													$detail = array_merge($detail,['total_price_converted'=>$amount]);
													$detail = array_merge($detail,['unit_converted'=>$cArr['unit']]);
													$calQTY = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
													$calQTYRemain = $calQTY;
													$remain_qty = 0;
												}
												if(!$stockOutId = DB::table('stocks')->insertGetId($stockOut)){
													DB::rollback();
													throw new \Exception("Stock Out[{$i}] not insert.");
												}else{
													Stock::where('id',$cArr['stock_id'])->update(['remain_qty'=>$remain_qty]);
													if(!$usageDetailId = DB::table('usage_details')->insertGetId($detail)){
														DB::rollback();
														throw new \Exception("UsageDetail[{$i}] not insert.");
													}
												}
											}
										}
									
								}
							}
							
						}else{
							$item_cost = DB::table('items')->where('items.id',$request['item_id'][$i])->first();
							$stockOut = array_merge($stockOut,['qty'  => ($calQTY * (-1))]);
							if(!$stockOutId = DB::table('stocks')->insertGetId($stockOut)){
								DB::rollback();
								throw new \Exception("Stock Out[{$i}] not insert.");
							}else{
								$detail = array_merge($detail,['qty'    => $calQTY]);
								$detail = array_merge($detail,['total_cost'    => $calQTY * $item_cost->cost_purch]);
								$detail = array_merge($detail,['cost' => $item_cost->cost_purch]);
								$detail = array_merge($detail,['qty_converted'=> $calQTY]);
								$detail = array_merge($detail,['price_converted'=>$item_cost->cost_purch]);
								$detail = array_merge($detail,['total_price_converted'=>$calQTY * $item_cost->cost_purch]);
								if(!$usageDetailId = DB::table('usage_details')->insertGetId($detail)){
									DB::rollback();
									throw new \Exception("UsageDetail[{$i}] not insert.");
								}
							}
						}
						
	
						// if (getSetting()->is_costing==1) {
						// 	$costArr = getItemCost($request['item_id'][$i],$request['unit_id'][$i],$request['qty'][$i]);
						// 	print_r($costArr)."<br />";
							// if (getSetting()->stock_account=="FIFO") {
							// 	$sql  = "CALL COSTING_FIFO({$request->session()->get('project')},{$request['item_id'][$i]});";
							// }else{
							// 	$sql  = "CALL COSTING_LIFO({$request->session()->get('project')},{$request['item_id'][$i]});";
							// }
							// $stocks = DB::select($sql);
							// print_r(getSetting()->stock_account);
							// echo "<br />";
							// foreach($stocks as $stock){
							// 	// if($stock->type_ == "ENT"){
							// 		print_r($stock);echo "<br />";
							// 	// }
							// }
							// exit;
							// print_r(count($stocks));
						// 	if(count($stocks) > 0){
						// 		foreach($stocks as $stock){
						// 			print_r($stock);exit;
						// 			$qty =0;
						// 			$remain_qty = 0;
						// 			if($calQTY > 0 && $stock->type_ == "ENT"){
						// 				if($stock->remain_qty >= $calQTY){
						// 					$remain_qty = $stock->remain_qty - $calQTY;
						// 					$qty 	= $calQTY * -1;
						// 					$cost 	= $stock->cost;
						// 					$amount = $qty * $cost;
						// 					$stockOut = array_merge($stockOut,['qty'    => $qty]);
						// 					$stockOut = array_merge($stockOut,['cost'   => $cost]);
						// 					$stockOut = array_merge($stockOut,['amount' => $amount]);

						// 					$detail = array_merge($detail,['qty'    => $qty]);
						// 					$detail = array_merge($detail,['total_cost'    => $amount]);
						// 					$detail = array_merge($detail,['cost' => $cost]);
											
						// 					$calQTY = 0;
						// 				}else{
						// 					$remain_qty = 0;
						// 					$calQTYRemain = $calQTY - $stock->remain_qty; 

						// 					$qty 	= $stock->remain_qty * -1;
						// 					$cost 	= $stock->cost;
						// 					$amount = $qty * $cost;
						// 					$stockOut = array_merge($stockOut,['qty'    => $qty]);
						// 					$stockOut = array_merge($stockOut,['cost'   => $cost]);
						// 					$stockOut = array_merge($stockOut,['amount' => $amount]);

						// 					$detail = array_merge($detail,['qty'    => $qty]);
						// 					$detail = array_merge($detail,['total_cost'    => $amount]);
						// 					$detail = array_merge($detail,['cost' => $cost]);
						// 					$calQTY = $calQTY - $stock->remain_qty;
						// 				}
										
						// 				if(!$stockOutId = DB::table('stocks')->insertGetId($stockOut)){
						// 					DB::rollback();
						// 					throw new \Exception("Stock Out[{$i}] not insert.");
						// 				}else{
						// 					Stock::where('id',$stock->stock_id)->update(['remain_qty'=>$remain_qty]);
						// 					if(!$usageDetailId = DB::table('usage_details')->insertGetId($detail)){
						// 						DB::rollback();
						// 						throw new \Exception("UsageDetail[{$i}] not insert.");
						// 					}
						// 				}

						// 			}
						// 		}
								
						// 	}else{
						// 		DB::rollback();
						// 		throw new \Exception("Stock Out[{$i}] not insert.");
						// 	}
							
						// }else{

						// 	$stockOut = array_merge($stockOut,['qty'  => ($calQTY * (-1))]);
						// 	if(!$stockOutId = DB::table('stocks')->insertGetId($stockOut)){
						// 		DB::rollback();
						// 		throw new \Exception("Stock Out[{$i}] not insert.");
						// 	}
						// }
	
						// if(!$usageDetailId = DB::table('usage_details')->insertGetId($detail)){
						// 	DB::rollback();
						// 	throw new \Exception("UsageDetail[{$i}] not insert.");
						// }
					}
				}
				// exit;
			}
			// exit;
			DB::commit();
			if($request->btnSubmit==1){
				return redirect('stock/use/policy')->with('success',trans('lang.save_success'));
			}
			return redirect()->back()->with('success',trans('lang.save_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',$e->getMessage().' at line '.$e->getLine());
		}
	}

	public function update(Request $request,$id){

		$rules = [
			'reference_no' 	=>'required|max:20|unique_stock_use',
			'trans_date' 	=>'required|max:20',
			'reference' 	=>'required',
			'engineer' 		=>'required|max:11',
			'warehouse_id' 	=>'required|max:11',
		];

		if(getSetting()->usage_constructor==1){
			$rules['sub_const']= 'required|max:11';
		}

		if(count($request['line_index']) > 0){
			for($i=0;$i<count($request['line_index']);$i++){
				$rules['line_index.'.$i] 	= 'required';
				$rules['item_id.'.$i]		= 'required|max:11';
				$rules['unit_id.'.$i]		= 'required';
				$rules['qty.'.$i]			= 'required';
				$rules['stock_qty.'.$i]		= 'required';
				$rules['line_boq_set.'.$i]	= 'required';
			}
		}
        Validator::make($request->all(),$rules)->validate();

		try {
			DB::beginTransaction();

			if(!$usage = Usage::find($id)){
				throw new \Exception("Usage[{$id}] not found.");
			}

			$originalDate = $request['trans_date'];
			$trans_date = date("Y-m-d", strtotime($originalDate));

			$data = [
				'pro_id'		=>$request->session()->get('project'),
				'ref_no'		=>$request->reference_no,
				'trans_date'	=>$trans_date,
				'reference'		=>$request->reference,
				'eng_usage'		=>$request->engineer,
				'desc'			=>$request->desc,
				'created_by'	=>Auth::user()->id,
				'created_at'	=>date('Y-m-d H:i:s'),
			];

			if(getSetting()->usage_constructor==1){
				$data = array_merge($data, ['sub_usage'=>$request->sub_const]);
			}
			
			DB::table('usages')->where(['id'=>$id])->update($data);
			// Delete all usage details
			DB::table('usage_details')->where(['use_id' => $id])->delete();
			// Delete all usage stocks
			DB::table('stocks')->where(['ref_id'=>$id,'ref_no'=>$usage->ref_no,'ref_type'=>'usage items','trans_ref'=>'O'])->delete();

			// Usage Policy
			$usagePolicyColumns = [
				'usage_formulas.id',
				'usage_formulas.code',
				'usage_formulas.zone',
				'usage_formulas.block_id',
				'usage_formulas.street_id',
				'usage_formula_details.house_id',
				'usage_formula_details.percentage',
			];

			$usagePolicies = UsageFormulaDetail::select($usagePolicyColumns)
				->leftJoin('usage_formulas','usage_formula_details.formula_id','usage_formulas.id')
				->where('usage_formulas.status',1);

			if($zoneId = $request->input('zone_id')){
				$usagePolicies = $usagePolicies->where('usage_formulas.zone_id',$zoneId);
			}

			if($blockId = $request->input('block_id')){
				$usagePolicies = $usagePolicies->where('usage_formulas.block_id',$blockId);
			}

			if($streetId = $request->input('street_id')){
				$usagePolicies = $usagePolicies->where('usage_formulas.street_id',$streetId);
			}

			if($houseId = $request->input('house_id')){
				$usagePolicies = $usagePolicies->where('usage_formula_details.house_id',$houseId);
			}

			$usagePolicies = $usagePolicies->get();

			if(count($usagePolicies) == 0){
				throw new \Exception("Usage policy value not found");
			}

			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){

					foreach($usagePolicies as $policy){
						$reqQTY = floatval($request['qty'][$i]);
						$calQTY = $reqQTY * (floatval($policy->percentage) / 100);

						if(!$house = House::find($policy->house_id)){
							throw new \Exception("House not found");
						}

						//// FILLED USAGE DETAIL ROW ////
						$detail = [
							'use_id'         =>$id,
							'warehouse_id'   =>$request['warehouse_id'],
							'house_id'       =>$house->id,
							'street_id'      =>	$house->street_id,
							'zone_id'      =>	$house->zone_id,
							'block_id'      =>	$house->block_id,
							'building_id'      =>	$house->building_id,
							'line_no'        =>$request['line_index'][$i],
							'item_id'        =>$request['item_id'][$i],
							'unit'           =>$request['unit_id'][$i],
							'qty'            =>$calQTY,
							'stock_qty'      =>$request['stock_qty'][$i],
							'boq_set'        =>$request['boq_set'][$i],
							'created_by'     =>Auth::user()->id,
							'created_at'     =>date('Y-m-d H:i:s'),
						];

						$stockOut = [
							'pro_id'         =>$request->session()->get('project'),
							'ref_id'         =>$id,
							'ref_no'         =>$request->reference_no,
							'ref_type'       =>'usage items',
							'line_no'        =>$request['line_index'][$i],
							'item_id'        =>$request['item_id'][$i],
							'unit'           =>$request['unit_id'][$i],
							'warehouse_id'   =>$request['warehouse_id'],
							'trans_date'     =>$trans_date,
							'reference'      =>$request->reference,
							'trans_ref'      =>'O',
							'alloc_ref'      =>getAllocateRef($request->reference_no),
							'created_by'     =>Auth::user()->id,
							'created_at'     =>date('Y-m-d H:i:s'),
						];
	
						if (getSetting()->is_costing==1) {
							$costArr = getItemCost($request['item_id'][$i],$request['unit_id'][$i],$calQTY);
							if ($costArr) {
								if (is_array($costArr) || is_object($costArr)) {
									foreach ($costArr as $cArr) {
										
										$qty 	= $cArr['qty'] * -1;
										$cost 	= $cArr['cost'];
										$amount = $qty * $cost;
										
										$stockOut = array_merge($stockOut,['qty'  => $qty]);
										$stockOut = array_merge($stockOut,['cost' => $cost]);
										$stockOut = array_merge($stockOut,['amount' => $amount]);
				
										if(!$stockOutId = DB::table('stocks')->insertGetId($stockOut)){
											DB::rollback();
											throw new \Exception("Stock Out[{$i}] not insert.");
										}
									}
								}
							}
						}else{
							$stockOut = array_merge($stockOut,['qty'  => ($calQTY * (-1))]);
							if(!$stockOutId = DB::table('stocks')->insertGetId($stockOut)){
								DB::rollback();
								throw new \Exception("Stock Out[{$i}] not insert.");
							}
						}
	
						if(!$usageDetailId = DB::table('usage_details')->insertGetId($detail)){
							DB::rollback();
							throw new \Exception("UsageDetail[{$i}] not insert.");
						}
					}
				}
			}

			DB::commit();
			return redirect('stock/use/policy')->with('success',trans('lang.save_success'));
			// return redirect()->back()->with('success',trans('lang.update_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.update_error').' '.$e->getMessage().' '.$e->getLine());
		}
	}
}
