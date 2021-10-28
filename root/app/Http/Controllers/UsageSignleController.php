<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Datatables;
use Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Excel;
use App\Model\Usage;
use App\Model\UsageDetails;
use App\Model\BoqItem;
use App\Model\Constructor;
use App\Model\Item;
use App\Model\Warehouse;
use App\Model\SystemData;
use App\Model\House;
use App\Model\Stock;

class UsageSignleController extends Controller
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
			'rounte'		=> url("stock/use_single/dt"),
		];
		
		if(hasRole('usage_entry_add')){
			$data = array_merge($data, ['rounteAdd'=> url('stock/use_single/add')]);
		}
		return view('stock.usage.entry_multi.index',$data);
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
						'url' 		=> url('stock/use_single'),
						'caption' 	=> trans('lang.usage'),
				],
				'add'	=> [
						'caption' 	=> trans('lang.add_new'),
				],
			],
			'rounteSave'	=> url('stock/use_single/save'),
			'rounteBack'	=> url('stock/use_single'),
			'pro_id'		=> $request->session()->get('project'),
		];
		return view('stock.usage.entry_multi.add',$data);
	}

	public function addWithPolicy(Request $request){
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
			'rounteSave'	=> url('stock/use_single/policy/save'),
			'rounteBack'	=> url('stock/use_single/policy'),
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
							'url' 		=> url('stock/use_single'),
							'caption' 	=> trans('lang.usage'),
					],
					'edit'	=> [
							'caption' 	=> trans('lang.edit'),
					],
				],
				'rounteSave'	=> url('stock/use_single/update/'.$id),		
				'rounteBack'	=> url('stock/use_single'),		
				'obj'	=> $obj,
				'pro_id'=> $request->session()->get('project'),
			];
			return view('stock.usage.entry_multi.edit',$data);
		}else{
			return redirect()->back();
		}
	}
	
	public function getDt(Request $request)
    {
		$pro_id = $request->session()->get('project');
		$usages = Usage::where(['delete'=> 0, 'pro_id' => $pro_id])->get();
        return Datatables::of($usages)
		->addColumn('engineer',function($row){
			if($engineer = Constructor::find($row->eng_usage)){
				return "{$engineer->id_card} ({$engineer->name})";
			}
            return '';
        })
		->addColumn('sub_constructor',function($row){
			if($subcontractor = Constructor::find($row->sub_usage)){
				return "{$subcontractor->id_card} ({$subcontractor->name})";
			}
            return '';
        })
		->addColumn('details_url',function($row){
            return url('stock/use_single/subdt').'/'.$row->id;
        })
		->addColumn('action', function ($row) {
			$rounte_delete = url('stock/use_single/delete/'.$row->id);
			$rounte_edit = url('stock/use_single/edit/'.encrypt($row->id));
			$routePrint = url('stock/use/printUsage/'.($row->id));
			$btnEdit = 'onclick="onEdit(this)"';
			$btnDelete = 'onclick="onDelete(this)"';
			
			if(!hasRole('usage_entry_edit')){
				$btnEdit = "disabled";
			}
			if(!hasRole('usage_entry_delete')){
				$btnDelete = "disabled";
			}
			return
				'<a route="'.$routePrint.'" onclick="onPrint(this);" title="'.trans('lang.print').'" class="btn btn-xs green print-record">'.
				'	<i class="fa fa-print"></i>'.
				'</a>'.
				'<a '.$btnEdit.' title="'.trans('lang.edit').'" class="btn btn-xs yellow edit-record" row_id="'.$row->id.'" row_rounte="'.$rounte_edit.'">'.
				'	<i class="fa fa-edit"></i>'.
				'</a>'.
				'<a '.$btnDelete.' title="'.trans('lang.delete').'" class="btn btn-xs red delete-record" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'">'.
				'	<i class="fa fa-trash"></i>'.
				'</a>'; 
       })->make(true);
    }
	
	public function subDt(Request $request, $id)
    {
		$usageDetails = UsageDetails::where(['delete'=> 0, 'use_id'=> $id])->get();
        return Datatables::of($usageDetails)
		->addColumn('item',function($row){
			if($item = Item::find($row->item_id)){
				return "{$item->name} ({$item->code})";
			}
			return '';
		})
		->addColumn('from_warehouse',function($row){
			if($warehouse = Warehouse::find($row->warehouse_id)){
				return $warehouse->name;
			}
			return '';
		})
		->addColumn('street',function($row){
			if($street = SystemData::find($row->street_id)){
				return $street->name;
			}
			return '';
		})
		->addColumn('on_house',function($row){
			if($house = House::find($row->house_id)){
				return $house->house_no;
			}
			return '';
		})
		->make(true);
	}

    public function save(Request $request){
		$rules = [
			'reference_no' 	=>'required|max:20|unique_stock_use',
			'trans_date' 	=>'required|max:20',
			'reference' 	=>'required',
			'engineer' 		=>'required|max:11',
			'house_id' 		=>'required|max:11',
			'building_id' 	=>'required|max:11',
			'warehouse_id' 	=>'required|max:11',
		];
		if(getSetting()->usage_constructor==1){
			$rules['sub_const']= 'required|max:11';
		}
		if(count($request['line_index']) > 0){
			for($i=0;$i<count($request['line_index']);$i++){
				$rules['line_index.'.$i] 			= 'required';
				$rules['line_item.'.$i]				= 'required|max:11';
				$rules['line_unit.'.$i]				= 'required';
				$rules['line_use_qty.'.$i]			= 'required';
				$rules['line_stock_qty.'.$i]		= 'required';
				$rules['line_boq_set.'.$i]			= 'required';
			}
		}
        Validator::make($request->all(),$rules)->validate();
		try {
			DB::beginTransaction();
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
			
			if(!$id = DB::table('usages')->insertGetId($data)){
				throw new \Exception("Usage[{$request->reference_no}] not insert");
			}
			$house_info = DB::table('houses')->where('id',$request->house_id)->first();

			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){
					
					$calQTY = $request['line_use_qty'][$i];
					$detail = [
						'use_id'         =>$id,
						'warehouse_id'   =>$request->warehouse_id,
						'house_id'       =>$request->house_id,
						'street_id'      =>$house_info->street_id,
						'zone_id'        =>$house_info->zone_id,
						'block_id'       =>$house_info->block_id,
						'building_id'    =>$house_info->building_id,
						'line_no'        =>$request['line_index'][$i],
						'item_id'        =>$request['line_item'][$i],
						'unit'           =>$request['line_unit'][$i],
						// 'qty'            =>$request['line_use_qty'][$i],
						'stock_qty'      =>$request['line_stock_qty'][$i],
						'boq_set'        =>$request['line_boq_set'][$i],
						'note'           =>$request['line_note'][$i],
						'created_by'     =>Auth::user()->id,
						'created_at'     =>date('Y-m-d H:i:s'),
					];

					$stockOut = [
						'pro_id'         =>$request->session()->get('project'),
						'ref_id'         =>$id,
						'ref_no'         =>$request->reference_no,
						'ref_type'       =>'usage items',
						'line_no'        =>$request['line_index'][$i],
						'item_id'        =>$request['line_item'][$i],
						'unit'           =>$request['line_unit'][$i],
						'warehouse_id'   =>$request->warehouse_id,
						'trans_date'     =>$trans_date,
						'reference'      =>$request->reference,
						'trans_ref'      =>'O',
						'alloc_ref'      =>getAllocateRef($request->reference_no),
						'created_by'     =>Auth::user()->id,
						'created_at'     =>date('Y-m-d H:i:s'),
					];

					if (getSetting()->is_costing==1) {
						$calQTYRemain = $calQTY;
						$costArr = getItemCost($request['line_item'][$i],$request['line_unit'][$i],$calQTY);
						// print_r($costArr);exit;
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
										$costArr = getItemCost($request['line_item'][$i],$request['line_unit'][$i],$calQTY);
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
										$costArr = getItemCost($request['line_item'][$i],$request['line_unit'][$i],$calQTY);
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

					// if(!$usageDetailId = DB::table('usage_details')->insertGetId($detail)){
					// 	DB::rollback();
					// 	throw new \Exception("UsageDetail[{$i}] not insert.");
					// }
				}
			}
	
			DB::commit();
			if($request->btnSubmit==1){
				return redirect('stock/use_single')->with('success',trans('lang.save_success'));
			}
			return redirect()->back()->with('success',trans('lang.save_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
	}

    public function update(Request $request, $id)
    {
		$rules = [
			'reference_no' 	=>'required|max:20',
			'trans_date' 	=>'required|max:20',
			'reference' 	=>'required',
			'engineer' 		=>'required|max:11',
			'street_id' 	=>'required|max:11',
			'house_id' 		=>'required|max:11',
			'warehouse_id' 	=>'required|max:11',
		];
		if(getSetting()->usage_constructor==1){
			$rules['sub_const']= 'required|max:11';
		}
		if(count($request['line_index']) > 0){
			for($i=0;$i<count($request['line_index']);$i++){
				$rules['line_index.'.$i] 			= 'required';
				$rules['line_item.'.$i]				= 'required|max:11';
				$rules['line_unit.'.$i]				= 'required';
				$rules['line_use_qty.'.$i]			= 'required';
				$rules['line_stock_qty.'.$i]		= 'required';
				$rules['line_boq_set.'.$i]			= 'required';
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
				'trans_date'	=>$trans_date,
				'reference'		=>$request->reference,
				'eng_usage'		=>$request->engineer,
				'desc'			=>$request->desc,
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];
			if(getSetting()->usage_constructor==1){
				$data = array_merge($data, ['sub_usage'=>$request->sub_const]);
			}
			
			DB::table('usages')->where(['id'=>$id])->update($data);
			// Delete all usage details
			DB::table('usage_details')->where(['use_id' => $id])->delete();
			// Delete all usage stocks
			DB::table('stocks')->where(['ref_id'=>$id,'ref_no'=>$usage->ref_no,'ref_type'=>'usage items','trans_ref'=>'O'])->delete();

			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){

					$detail = [
						'use_id'         =>$id,
						'warehouse_id'   =>$request->warehouse_id,
						'house_id'       =>$request->house_id,
						'street_id'      =>$request->street_id,
						'line_no'        =>$request['line_index'][$i],
						'item_id'        =>$request['line_item'][$i],
						'unit'           =>$request['line_unit'][$i],
						'qty'            =>$request['line_use_qty'][$i],
						'stock_qty'      =>$request['line_stock_qty'][$i],
						'boq_set'        =>$request['line_boq_set'][$i],
						'note'           =>$request['line_note'][$i],
						'created_by'     =>Auth::user()->id,
						'created_at'     =>date('Y-m-d H:i:s'),
					];

					$stockOut = [
						'pro_id'         =>$request->session()->get('project'),
						'ref_id'         =>$id,
						'ref_no'         =>$request->reference_no,
						'ref_type'       =>'usage items',
						'line_no'        =>$request['line_index'][$i],
						'item_id'        =>$request['line_item'][$i],
						'unit'           =>$request['line_unit'][$i],
						'warehouse_id'   =>$request->warehouse_id,
						'trans_date'     =>$trans_date,
						'reference'      =>$request->reference,
						'trans_ref'      =>'O',
						'alloc_ref'      =>getAllocateRef($request->reference_no),
						'created_by'     =>Auth::user()->id,
						'created_at'     =>date('Y-m-d H:i:s'),
					];

					if (getSetting()->is_costing==1) {
						$costArr = getItemCost($request['line_item'][$i],$request['line_unit'][$i],$request['line_use_qty'][$i]);
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
						$stockOut = array_merge($stockOut,['qty'  => (floatval($request['line_use_qty'][$i])*(-1))]);
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
			DB::commit();
			return redirect()->back()->with('success',trans('lang.update_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.update_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }
	
	public function destroy($id)
    {
		try {
			DB::beginTransaction();
			$obj = Usage::find($id);
			if(checkAllocate("O", $obj->ref_no) > 0){
				return redirect()->back()->with('error',trans('lang.trans_error'));
			}
			$data = [
				'delete'		=>1,
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];
			DB::table('usages')->where(['id'=>$id])->update($data);
			$dataDetails = [
				'delete'		=>1,
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];
			DB::table('usage_details')->where(['use_id'=>$id])->update($dataDetails);
			$stock = [
				'delete'		=>1,
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];
			DB::table('stocks')->where(['ref_id'=>$id,'ref_no'=>$obj->ref_no,'ref_type'=>'usage items'])->update($stock);
			DB::commit();
			return redirect()->back()->with('success',trans('lang.delete_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.delete_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }
	
	public function getItemStock(Request $request){
		if($request->ajax()){
			$warehouse_id = $request['warehouse_id'];
			$house_id = $request['house_id'];
			$item_id = $request['item_id'];
			$unit = $request['unit'];
			$prefix = getSetting()->round_number;
			
			$boq_qty = -1;
			$stock_qty = 0;
			$usage_qty = 0;
			$return_qty = 0;
			
			/*$sql_stock = "SELECT (F.stock_qty / F.use_qty) AS stock_qty FROM (SELECT E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS use_qty FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_stocks.`item_id`, pr_stocks.`unit`, pr_stocks.`qty` FROM pr_stocks WHERE pr_stocks.`item_id` = $item_id AND pr_stocks.`warehouse_id` = $warehouse_id AND pr_stocks.`delete` = 0) AS A) AS B) AS C) AS D GROUP BY D.item_id, D.unit_stock) AS E) AS F";*/

			$originalDate = $request['trans_date'];
			$trans_date = date("Y-m-d", strtotime($originalDate));
			$sql = "SELECT ROUND((F.stock_qty / F.adj_qty), $prefix) AS stock_qty FROM (SELECT E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS adj_qty FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_stocks.`item_id`, pr_stocks.`unit`, pr_stocks.`qty` FROM pr_stocks WHERE pr_stocks.`item_id` = $item_id AND pr_stocks.`warehouse_id` = $warehouse_id AND pr_stocks.`delete` = 0 AND pr_stocks.`trans_date` <= '$trans_date') AS A) AS B) AS C) AS D GROUP BY D.item_id) AS E) AS F ";
			$objStock = collect(DB::select($sql))->first();
			if($objStock){
				$stock_qty = floatval($objStock->stock_qty);
			}
			if(BoqItem::where(['house_id'=>$house_id, 'item_id'=>$item_id])->exists()){
				$sql_boq = "SELECT (F.stock_qty / F.boq_qty) AS boq_qty FROM (SELECT E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS boq_qty FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_boq_items.`id`, pr_boq_items.`item_id`, pr_boq_items.`unit`, (pr_boq_items.`qty_std` + pr_boq_items.`qty_add` ) AS qty FROM pr_boq_items WHERE pr_boq_items.`house_id` = $house_id AND pr_boq_items.`item_id` = $item_id) AS A) AS B) AS C) AS D GROUP BY D.item_id, D.unit_stock) AS E) AS F";
				$objBoq = collect(DB::select($sql_boq))->first();
				if($objBoq){
					$boq_qty = floatval($objBoq->boq_qty);
					
					$sql_use = "SELECT (F.stock_qty / F.use_qty) AS use_qty FROM (SELECT E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS use_qty FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.id = A.item_id) AS unit_stock FROM (SELECT pr_usage_details.`id`, pr_usage_details.`item_id`, pr_usage_details.`unit`, pr_usage_details.`qty` FROM pr_usage_details WHERE pr_usage_details.`delete` = 0 AND pr_usage_details.`boq_set` != - 1 AND pr_usage_details.`house_id` = $house_id AND pr_usage_details.`item_id` = $item_id) AS A) AS B) AS C) AS D GROUP BY D.item_id, D.unit_stock) AS E) AS F";
					$objUse = collect(DB::select($sql_use))->first();
					if($objUse){
						$usage_qty = $objUse->use_qty;
						$boq_qty = floatval($boq_qty) - floatval($usage_qty); 
					}
					
					$sql_return = "SELECT (F.stock_qty / F.use_qty) AS return_qty FROM (SELECT E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS use_qty FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.id = A.item_id) AS unit_stock FROM (SELECT pr_return_usage_details.`id`, pr_return_usage_details.`item_id`, pr_return_usage_details.`unit`, pr_return_usage_details.`qty` FROM pr_return_usage_details WHERE pr_return_usage_details.`delete` = 0 AND pr_return_usage_details.`boq_set` != - 1 AND pr_return_usage_details.`house_id` = $house_id AND pr_return_usage_details.`item_id` = $item_id) AS A) AS B) AS C) AS D GROUP BY D.item_id, D.unit_stock) AS E) AS F";
					$objReturn = collect(DB::select($sql_return))->first();
					if($objReturn){
						$return_qty = $objReturn->return_qty;
						$boq_qty = floatval($boq_qty) + floatval($return_qty);
					}
				}
			}
			return ['stock_qty'=>$stock_qty,'boq_set'=>$boq_qty];
		}
		return ['stock_qty'=>0,'boq_set'=>-1];
	}
}
