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
use App\User;

class UsageController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
		$this->middleware('CheckProject');
    }

    public function GetDetail(Request $request)
    {
    	try {
			
			$select = [
				'usage_details.*',
				'items.cat_id',
				'items.code',
				'items.name',
				'items.alert_qty',
				'items.unit_stock',
				'items.unit_usage',
				'items.unit_purch',
				'items.cost_purch'
			];

			$OrderItem  = UsageDetails::select($select)
						->leftJoin('items','usage_details.item_id','items.id')
						->where('usage_details.use_id',$request->id)
						->where('usage_details.delete',0)
						->get();

			return response()->json($OrderItem,200); 

		} catch (\Exception $e) {
			return response()->json([],200);
		}
    }

    public function ItemCost(Request $request)
    {
    	try {
    		return response()->json(getItemCost($request->item_id,$request->unit,$request->qty),200);
    	} catch (Exception $e) {
    		return response()->json(["message"=>$e->getMessage(),"line"=>$e->getLine()],500);
    	}
	}
	
	public function GetRef(Request $request)
	{
		$refs = Usage::where('delete',0)->get(['reference']);
		return response()->json($refs,200);
	}

    public function GetItem(Request $request)
    {
		// print_r($request->q);
    	try {
			$where = ['status'=>1];
			if(!empty($request["cat_id"])){
				$where = array_merge($where,['cat_id'=>$request["cat_id"]]);
			}
			$limit  = 10;
			$page   = 1;
			$offset = 0;

			if ($request->limit) {
				$limit = $request->limit;
			}

			if ($request->page) {
				$page   = $request->page;
			}

			$offset = $page * $limit;

    		$select = ['*',DB::raw("id as item_id"),DB::raw("CONCAT(code,' (',name,')')AS text")];

			$OrderItem  = Item::select($select)
						->where($where);
			if($request->q){
				$OrderItem = $OrderItem->where(function ($query) use ($request) {
						$query->orWhere('items.name','like','%'.($request->q).'%')->orWhere('items.code','like','%'.($request->q).'%');
				});
			}
			
			$OrderItem = $OrderItem->offset($offset)
					    ->limit($limit)
					    ->paginate($limit);

			return response()->json($OrderItem); 

		} catch (\Exception $e) {
			return array(['id'=>'','text'=>'']);
		}
    }

    public function GetHouse(Request $request)
    {
    	try {
		
			$OrderItem  = House::where('street_id',$request->id)->get();

			return response()->json($OrderItem,200); 

		} catch (\Exception $e) {
			return response()->json(['line'=>$e->getLine(),'message'=>$e->getMessage()],500);
		}
    }

    public function GetStreet(Request $request)
    {
    	try {
		
			$OrderItem  = SystemData::where('name','like','%'.($request->q).'%')
							->where('type','ST')
							->where('status',1)
							->orWhere('desc','like','%'.($request->q).'%')
							->where('type','ST')
							->where('status',1)
							->get();

			return response()->json($OrderItem,200); 

		} catch (\Exception $e) {
			return response()->json(['line'=>$e->getLine(),'message'=>$e->getMessage()],500);
		}
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
			'rounteUpdateQty'	=> url('stock/use/updateQtyRemain'),
		];
		
		if(hasRole('usage_entry_add')){
			$data = array_merge($data, ['rounteAdd'=> url('stock/use/add')]);
		}
		return view('stock.usage.entry.index',$data);
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
			'rounteSave'	=> url('stock/use/save'),
			'rounteBack'	=> url('stock/use'),
			
			'pro_id'		=> $request->session()->get('project'),
		];
		return view('stock.usage.entry.add',$data);
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
				'rounteSave'	=> url('stock/use/update/'.$id),		
				'rounteBack'	=> url('stock/use'),		
				'obj'	=> $obj,
				'pro_id'=> $request->session()->get('project'),
			];
			return view('stock.usage.entry.edit',$data);
		}else{
			return redirect()->back();
		}
	}
	
	public function getDt(Request $request)
    {
		$pro_id = $request->session()->get('project');
        $usages = Usage::where(['delete'=> 0, 'pro_id'=> $pro_id])->get();
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
            return url('stock/use/subdt').'/'.$row->id;
        })
		->addColumn('action', function ($row) {
			$rounte_delete = url('stock/use/delete/'.$row->id);
			$rounte_edit = url('stock/use/edit/'.encrypt($row->id));
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
		];
		// if(getSetting()->usage_constructor==1){
		// 	$rules['sub_const']= 'required|max:11';
		// }
		// if(count($request['line_index']) > 0){
		// 	for($i=0;$i<count($request['line_index']);$i++){
		// 		$rules['line_index.'.$i] 			= 'required';
		// 		$rules['line_from_warehouse.'.$i] 	= 'required|max:11';
		// 		$rules['line_building.'.$i] 			= 'required|max:11';
		// 		$rules['line_on_house.'.$i] 		= 'required|max:11';
		// 		$rules['line_item.'.$i]				= 'required|max:11';
		// 		$rules['line_unit.'.$i]				= 'required';
		// 		$rules['line_use_qty.'.$i]			= 'required';
		// 		$rules['line_stock_qty.'.$i]		= 'required';
		// 		$rules['line_boq_set.'.$i]			= 'required';
		// 	}
		// }
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

			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){
					$house_info = DB::table('houses')->where('id',$request['line_on_house'][$i])->first();
					$calQTY = $request['line_use_qty'][$i];
					$detail = [
						'use_id'         =>$id,
						'warehouse_id'   =>$request['line_from_warehouse'][$i],
						'house_id'       =>$request['line_on_house'][$i],
						'zone_id'      	 =>$house_info->zone_id ? $house_info->zone_id : 0,
						'block_id'       =>$house_info->block_id ?  $house_info->block_id : 0,
						'building_id'    =>$request['line_building'][$i],
						// 'street_id'      =>$request['line_street'][$i],
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
						'warehouse_id'   =>$request['line_from_warehouse'][$i],
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
			// exit;
			DB::commit();
			if($request->btnSubmit==1){
				return redirect('stock/use')->with('success',trans('lang.save_success'));
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
		];
		if(getSetting()->usage_constructor==1){
			$rules['sub_const']= 'required|max:11';
		}
		if(count($request['line_index']) > 0){
			for($i=0;$i<count($request['line_index']);$i++){
				$rules['line_index.'.$i] 			= 'required';
				$rules['line_from_warehouse.'.$i] 	= 'required|max:11';
				$rules['line_street.'.$i] 			= 'required|max:11';
				$rules['line_on_house.'.$i] 		= 'required|max:11';
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
						'warehouse_id'   =>$request['line_from_warehouse'][$i],
						'house_id'       =>$request['line_on_house'][$i],
						'street_id'      =>$request['line_street'][$i],
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
						'warehouse_id'   =>$request['line_from_warehouse'][$i],
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
	
	private function checkStock($warehouse_id,$house_id,$item_id,$unit){
		$prefix = getSetting()->round_number;
			
		$boq_qty = -1;
		$stock_qty = 0;
		$usage_qty = 0;
		$return_qty = 0;
		
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
	
	public function getItemStock(Request $request){
		if($request->ajax()){
			$warehouse_id = $request['warehouse_id'];
			$house_id = $request['house_id'];
			$building_id = $request["building_id"];
			$item_id = $request['item_id'];
			$unit = $request['unit'];
			$prefix = getSetting()->round_number;
			
			$boq_qty = -1;
			$stock_qty = 0;
			$usage_qty = 0;
			$return_qty = 0;
			$remain_boq = 0;
			
			$originalDate = $request['trans_date'];
			$trans_date = date("Y-m-d", strtotime($originalDate));
			$sql = "SELECT ROUND((F.stock_qty / F.adj_qty), $prefix) AS stock_qty FROM (SELECT E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS adj_qty FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_stocks.`item_id`, pr_stocks.`unit`, pr_stocks.`qty` FROM pr_stocks WHERE pr_stocks.`item_id` = $item_id AND pr_stocks.`warehouse_id` = $warehouse_id AND pr_stocks.`delete` = 0 AND pr_stocks.`trans_date` <= '$trans_date') AS A) AS B) AS C) AS D GROUP BY D.item_id) AS E) AS F ";
			
			$objStock = collect(DB::select($sql))->first();
			// print_r($objStock);
			if($objStock){
				$stock_qty = floatval($objStock->stock_qty);
			}
			
			if(BoqItem::join('boqs','boqs.id','boq_items.boq_id')->where(['boqs.building_id'=>$building_id,'boq_items.item_id'=>$item_id])->exists()){
				$sql_boq = "SELECT (F.stock_qty / F.boq_qty) AS boq_qty FROM (SELECT E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS boq_qty FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_boq_items.`id`, pr_boq_items.`item_id`, pr_boq_items.`unit`, (pr_boq_items.`qty_std` + pr_boq_items.`qty_add` ) AS qty FROM pr_boq_items JOIN pr_boqs on pr_boqs.id = pr_boq_items.boq_id WHERE pr_boqs.building_id = $building_id AND pr_boq_items.`item_id` = $item_id) AS A) AS B) AS C) AS D GROUP BY D.item_id, D.unit_stock) AS E) AS F";
				$objBoq = collect(DB::select($sql_boq))->first();
				if($objBoq){
					$boq_qty = floatval($objBoq->boq_qty);
					$remain_boq = floatval($objBoq->boq_qty);
					$sql_use = "SELECT (F.stock_qty / F.use_qty) AS use_qty FROM (SELECT E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS use_qty FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.id = A.item_id) AS unit_stock FROM (SELECT pr_usage_details.`id`, pr_usage_details.`item_id`, pr_usage_details.`unit`, pr_usage_details.`qty` FROM pr_usage_details WHERE pr_usage_details.`delete` = 0 AND pr_usage_details.`boq_set` != - 1 AND pr_usage_details.`building_id` = $building_id AND pr_usage_details.`item_id` = $item_id) AS A) AS B) AS C) AS D GROUP BY D.item_id, D.unit_stock) AS E) AS F";
					$objUse = collect(DB::select($sql_use))->first();
					if($objUse){
						$usage_qty = $objUse->use_qty;
						$boq_qty = floatval($boq_qty); 
						$remain_boq = floatval($boq_qty) - floatval($usage_qty); 
					}
					
					$sql_return = "SELECT (F.stock_qty / F.use_qty) AS return_qty FROM (SELECT E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS use_qty FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.id = A.item_id) AS unit_stock FROM (SELECT pr_return_usage_details.`id`, pr_return_usage_details.`item_id`, pr_return_usage_details.`unit`, pr_return_usage_details.`qty` FROM pr_return_usage_details WHERE pr_return_usage_details.`delete` = 0 AND pr_return_usage_details.`boq_set` != - 1 AND pr_return_usage_details.`building_id` = $building_id AND pr_return_usage_details.`item_id` = $item_id) AS A) AS B) AS C) AS D GROUP BY D.item_id, D.unit_stock) AS E) AS F";
					$objReturn = collect(DB::select($sql_return))->first();
					if($objReturn){
						$return_qty = $objReturn->return_qty;
						$boq_qty = floatval($boq_qty) - floatval($return_qty);
					}
				}
			}
			return ['stock_qty'=>$stock_qty,'boq_set'=>$boq_qty,'remain_boq'=>$remain_boq,'usage_qty'=>$usage_qty];
		}
		return ['stock_qty'=>0,'boq_set'=>-1];
	}

	public function getItemStocks(Request $request){
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
			$remain_boq = 0;
			
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
					$remain_boq = floatval($boq_qty);
					$sql_use = "SELECT (F.stock_qty / F.use_qty) AS use_qty FROM (SELECT E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS use_qty FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.id = A.item_id) AS unit_stock FROM (SELECT pr_usage_details.`id`, pr_usage_details.`item_id`, pr_usage_details.`unit`, pr_usage_details.`qty` FROM pr_usage_details WHERE pr_usage_details.`delete` = 0 AND pr_usage_details.`boq_set` != - 1 AND pr_usage_details.`house_id` = $house_id AND pr_usage_details.`item_id` = $item_id) AS A) AS B) AS C) AS D GROUP BY D.item_id, D.unit_stock) AS E) AS F";
					$objUse = collect(DB::select($sql_use))->first();
					if($objUse){
						$usage_qty = $objUse->use_qty;
						$boq_qty = floatval($boq_qty) ; 
						$remain_boq = floatval($boq_qty) - floatval(abs($usage_qty)); 
						
					}
					$sql_return = "SELECT (F.stock_qty / F.use_qty) AS return_qty FROM (SELECT E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS use_qty FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.id = A.item_id) AS unit_stock FROM (SELECT pr_return_usage_details.`id`, pr_return_usage_details.`item_id`, pr_return_usage_details.`unit`, pr_return_usage_details.`qty` FROM pr_return_usage_details WHERE pr_return_usage_details.`delete` = 0 AND pr_return_usage_details.`boq_set` != - 1 AND pr_return_usage_details.`house_id` = $house_id AND pr_return_usage_details.`item_id` = $item_id) AS A) AS B) AS C) AS D GROUP BY D.item_id, D.unit_stock) AS E) AS F";
					$objReturn = collect(DB::select($sql_return))->first();
					if($objReturn){
						$return_qty = $objReturn->return_qty;
						$boq_qty = floatval($boq_qty) - floatval(abs($return_qty));
						$remain_boq = floatval($boq_qty) - floatval(abs($usage_qty)); 
					}
				}
			}
			return ['stock_qty'=>$stock_qty,'boq_set'=>$boq_qty,'remain_boq'=>$remain_boq,"usage_qty"=>$usage_qty];
		}
		return ['stock_qty'=>0,'boq_set'=>-1,'remain_boq'=>0];
	}

	public function downloadExcel(Request $request){
		try {
			Excel::create('import_usage_' . date('Y_m_d_H_i_s'),function($excel) {
				$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
				$excel->sheet('Usage Items',function($sheet){
					// Header
					$sheet->cell('A1', 'Date');
					$sheet->cell('B1', 'Reference No');
					$sheet->cell('C1', 'Reference');
					$sheet->cell('D1', 'Engineer');
					$sheet->cell('E1', 'Subconstructor');
					$sheet->cell('F1', 'Warehouse Name');
					$sheet->cell('G1', 'Street');
					$sheet->cell('H1', 'House');
					$sheet->cell('I1', 'Item Code');
					$sheet->cell('J1', 'Unit');
					$sheet->cell('K1', 'Qty');
					$sheet->cell('L1', 'Note');
					// Example Body
					$sheet->cell('A2', date('Y-m-d'));
					$sheet->cell('B2', 'USE0001');
					$sheet->cell('C2', 'REF0001');
					$sheet->cell('D2', 'Jonh Doe');
					$sheet->cell('E2', 'Jonh Smith');
					$sheet->cell('F2', 'WH0001');
					$sheet->cell('G2', 'ST0001');
					$sheet->cell('H2', 'HS0001');
					$sheet->cell('I2', 'ITEM001');
					$sheet->cell('J2', 'Pcs');
					$sheet->cell('K2', 2);
					$sheet->cell('L2', 'Example row to explain how to input data, and do not forget to delete this example row before you upload this file.');
				});
			})->download('xlsx');
		} catch (\Exception $ex) {
			return redirect('stock/use')->with("error", $ex->getMessage());
		}
	}

	public function importExcel(Request $request){
		try {
			ini_set('max_execution_time', 0);
			DB::beginTransaction();
			// check exist file
			if(!$request->hasFile('excel')){
				throw new \Exception("No file selected.");
			}

			if(!$excel_select = $request->file('excel')){
				throw new \Exception("No excel file was created.");
			}

			if(!$data = Excel::load($excel_select->getRealPath(), function($reader) {})->get()){
				throw new \Exception("This excel file no content.");
			}

			if(empty($data)){
				throw new \Exception("This excel is empty.");
			}

			$pro_id = $request->session()->get('project');

			foreach($data as $index => $row){
				
				$cellCount = $index + 1;
				if(empty($row->date)){
					throw new \Exception("Column[A{$cellCount}] is empty");
				}

				if(empty($row->reference_no)){
					throw new \Exception("Column[B{$cellCount}] is empty");
				}

				if(empty($row->engineer)){
					throw new \Exception("Column[D{$cellCount}] is empty");
				}

				if(!$engineer = Constructor::where(['id_card' => trim($row->engineer),'type'=>1])->first()){
					throw new \Exception("Column[D{$cellCount}] not found");
				}

				if(empty($row->subconstructor)){
					throw new \Exception("Column[E{$cellCount}] is empty");
				}

				if(!$subconstructor = Constructor::where(['id_card' => trim($row->subconstructor),'type'=>2])->first()){
					throw new \Exception("Column[E{$cellCount}] not found");
				}

				if(empty($row->warehouse_name)){
					throw new \Exception("Column[F{$cellCount}] is empty");
				}

				if(!$warehouse = Warehouse::where('name',$row->warehouse_name)->first()){
					throw new \Exception("Column[F{$cellCount}] not found");
				}

				if(empty($row->street)){
					throw new \Exception("Column[G{$cellCount}] is empty");
				}

				if(!$street = SystemData::where('name',$row->street)->where('type','ST')->first()){
					throw new \Exception("Column[G{$cellCount}] not found");
				}

				if(empty($row->house)){
					throw new \Exception("Column[H{$cellCount}] is empty");
				}

				if(!$house = House::where('house_no',$row->house)->first()){
					throw new \Exception("Column[H{$cellCount}] not found");
				}

				if(empty($row->item_code)){
					throw new \Exception("Column[I{$cellCount}] is empty");
				}

				if(!$item = Item::where('code',$row->item_code)->first()){
					throw new \Exception("Column[I{$cellCount}] item not found");
				}

				if(empty($row->unit)){
					throw new \Exception("Column[J{$cellCount}] is empty");
				}

				if(!$unit = Unit::where('from_desc',$row->unit)->first()){
					throw new \Exception("Column[J{$cellCount}] item not found");
				}

				if(empty($row->qty)){
					throw new \Exception("Column[K{$cellCount}] is empty");
				}

				$usage = [
					'pro_id' => $pro_id,
					'ref_no' => $row->reference_no,
					'trans_date' => $row->date,
					'eng_usage' => $engineer->id,
					'sub_usage' => $subconstructor->id,
					'created_by' => Auth::user()->id,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_by' => Auth::user()->id,
					'updated_at' => date('Y-m-d H:i:s'),
				];

				$usage_item = [
					'line_no' => formatZero($cellCount, 3),
					'warehouse_id' => $warehouse->id,
					'street_id' => $street->id,
					'house_id' => $house->id,
					'item_id' => $item->id,
					'unit' => $row->unit,
					'qty' => $row->qty,
					'created_by' => Auth::user()->id,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_by' => Auth::user()->id,
					'updated_at' => date('Y-m-d H:i:s'),
				];

				$stock = [
					'line_no' => formatZero($cellCount, 3),
					'pro_id' => $pro_id,
					'ref_no' => $row->reference_no,
					'ref_type' => 'usage items',
					'item_id' => $item->id,
					'unit' => $row->unit,
					'warehouse_id' => $warehouse->id,
					'trans_date' => $row->date,
					'trans_ref' => 'O',
					'alloc_ref' => $row->reference_no,
					'created_by' => Auth::user()->id,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_by' => Auth::user()->id,
					'updated_at' => date('Y-m-d H:i:s'),
				];

				if(!empty($row->note)){
					$usage = array_merge($usage,['desc' => $row->note]);
				}

				if(!empty($row->reference)){
					$stock =  array_merge($stock,['reference' => $row->reference]);
				}

				if(!$exist_usage = Usage::where('ref_no',$row->reference_no)->first()){
					if(!$usageId  = DB::table('usages')->insertGetId($usage)){
						DB::rollback();
						throw new \Exception("Usage not saved");
					}

					$usage_item = array_merge($usage_item,['use_id' => $usageId]);
					$stock =  array_merge($stock,['ref_id' => $usageId]);
				}else{
					$usage_item = array_merge($usage_item,['use_id' => $exist_usage->id]);
					$stock =  array_merge($stock,['ref_id' => $exist_usage->id]);
				}

				if(!$usageItemId = DB::table('usage_details')->insertGetId($usage_item)){
					DB::rollback();
					throw new \Exception("Usage item not saved");
				}

				if(getSetting()->is_costing == 1){
					$costArr = getItemCost($item->id,$row->unit,$row->qty);
					if ($costArr) {
						if (is_array($costArr) || is_object($costArr)) {
							foreach ($costArr as $cArr) {
								$qty 	= $cArr['qty'] * -1;
								$cost 	= $cArr['cost'];
								$amount = $qty * $cost;
								
								$stock = array_merge($stock,['qty'  => $qty]);
								$stock = array_merge($stock,['cost' => $cost]);
								$stock = array_merge($stock,['amount' => $amount]);
		
								if(!$stockId = DB::table('stocks')->insertGetId($stock)){
									DB::rollback();
									throw new \Exception("Stock Out not insert.");
								}
							}
						}
					}
				}else{
					$stock = array_merge($stock,['qty' => ($row->qty * -1)]);
					if(!$stockId = DB::table('stocks')->insertGetId($stock)){
						DB::rollback();
						throw new \Exception("Stock not saved");
					}
				}
			}
			DB::commit();
			return redirect('stock/use')->with("success", trans('lang.upload_success'));
		} catch (\Exception $ex) {
			DB::rollback();
			return redirect('stock/use')->with("error", $ex->getMessage());
		}
	}

	public function printUsage(Request $request,$usageID = 0){
		$projectID = $request->session()->get('project');
		
		if(!$project = Project::find($projectID)){
			$project = false;
		}

		if(!$usage = Usage::find($usageID)){
			$usage = false;
		}

		if(!$engineer = Constructor::find($usage->eng_usage)){
			$engineer = false;
		}

		if(!$subcontractor = Constructor::find($usage->sub_usage)){
			$subcontractor = false;
		}

		if(!$createdBy = User::find($usage->created_by)){
			$createdBy = false;
		}

		$usageItems = UsageDetails::where('usage_details.use_id',$usageID)->get();

		if(count($usageItems) == 0){
			$usageItems = false;
		}

		$data = [
			'title' 		=> trans('lang.usage_note'),
			'project'		=> $project,
			'usage' 		=> $usage,
			'engineer' 		=> $engineer,
			'subcontractor'	=> $subcontractor,
			'createdBy' 	=> $createdBy,
			'usageItems' 	=> $usageItems,
		];

		return view('stock.usage.print')->with($data);
	}

	public function updateRemainQty(Request $request){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$usages = DB::table('usage_details')->where('usage_details.delete',0)->get();
		if($usages){
			foreach($usages as $key=>$usage){
				$calQTY = $usage->qty;
				echo "Before 1 :".$calQTY; 
				echo "<br />";
				if (getSetting()->is_costing==1) {
					$calQTYRemain = $calQTY;
					$costArr = getItemCost($usage->item_id,$usage->unit,$calQTY);
					if ($costArr) {
						if (is_array($costArr) || is_object($costArr)) {
							foreach ($costArr as $cArr) {
								print_r($cArr);
								echo "<br />";
								$qty =0;
								$remain_qty = 0;
								if($cArr['remain_qty'] >= $cArr['qty']){
									$remain_qty = $cArr['remain_qty'] - $cArr['qty'];
									$qty 	= $cArr['qty'] * -1;
									$cost 	= $cArr['cost'];
									$amount = $qty * $cost;
									$originalAmount = $amount;
									$orginalQty = ($cArr['qty']/$cArr['unit_qty']);
									$originalCost = $originalAmount / ($orginalQty > 0 ? $orginalQty : 1);
									$calQTY = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
									$calQTYRemain = 0;
								}else{
									$qty = $cArr['remain_qty'] * -1;
									$cost 	= $cArr['cost'];
									$amount = $qty * $cost;
									$originalAmount = $amount;
									$orginalQty = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
									$originalCost = $originalAmount / ($orginalQty > 0 ? $orginalQty : 1);
									
									$calQTY = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
									$calQTYRemain = $calQTY;
									$remain_qty = 0;
								}
								// if($cArr["stock_id"]>0){
									$stockOutId = DB::table('stocks')->where('id', $cArr["stock_id"])->update(['remain_qty'=>$remain_qty]);
									// if(!$stockOutId = DB::table('stocks')->where('id', $cArr["stock_id"])->update(['remain_qty'=>$remain_qty])){
									// 	DB::rollback();
									// 	throw new \Exception("Stock Out[{$key}] not insert.");
									// }
								// }
								
								echo "after 1 :".$calQTY;
								echo "<br />";
								echo "remain 1 :".$remain_qty;
								echo "<br />";
							}
								if($calQTY > 0){
									echo "Before 2 :".$calQTY; 
									echo "<br />";
									$costArr = getItemCost($usage->item_id,$usage->unit,$calQTY);
									foreach ($costArr as $cArr) {
										print_r($cArr);
										echo "<br />";
										$remain_qty = 0;
										if($cArr['remain_qty'] >= $cArr['qty']){
											$remain_qty = $cArr['remain_qty'] - $cArr['qty'];
											$qty 	= $cArr['qty'] * -1;
											$cost 	= $cArr['cost'];
											$amount = $qty * $cost;
											$originalAmount = $amount;
											$orginalQty = ($cArr['qty']/$cArr['unit_qty']);
											$originalCost = $originalAmount / ($orginalQty > 0 ? $orginalQty : 1);
											
											
											$calQTY = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
											$calQTYRemain = 0;
										}else{
											$qty = $cArr['remain_qty'] * -1;
											$cost 	= $cArr['cost'];
											$amount = $qty * $cost;
											$originalAmount = $amount;
											$orginalQty = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
											$originalCost = $originalAmount / ($orginalQty > 0 ? $orginalQty : 1);
											
											$calQTY = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
											$calQTYRemain = $calQTY;
											$remain_qty = 0;
										}
										$stockOutId = DB::table('stocks')->where('id', $cArr["stock_id"])->update(['remain_qty'=>$remain_qty]);
										// if(!$stockOutId = DB::table('stocks')->where('id', $cArr["stock_id"])->update(['remain_qty'=>$remain_qty])){
										// 	DB::rollback();
										// 	throw new \Exception("Stock Out[{$key}] not insert.");
										// }
										echo "After 2 :"; print_r($calQTY);
										echo "<br />";
										echo "remain 2 :".$remain_qty;
										echo "<br />";
									}
								}

								if($calQTY > 0){
									$costArr = getItemCost($usage->item_id,$usage->unit,$calQTY);
									echo "Before 3 :".$calQTY; 
									echo "<br />";
									foreach ($costArr as $cArr) {
										print_r($cArr);
										echo "<br />";
										$remain_qty = 0;
										if($cArr['remain_qty'] >= $cArr['qty']){
											$remain_qty = $cArr['qty'] - $cArr['qty'];
											$qty 	= $cArr['qty'] * -1;
											$cost 	= $cArr['cost'];
											$amount = $qty * $cost;
											$originalAmount = $amount;
											$originalCost = $originalAmount / ($calQTY > 0 ? $calQTY : 1);
											$orginalQty =($cArr['remain_qty']/$cArr['unit_qty']);
											
											$calQTY = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
											$calQTYRemain = 0;
										}else{
											$qty = $cArr['remain_qty'] * -1;
											$cost 	= $cArr['cost'];
											$amount = $qty * $cost;
											$originalAmount = $amount;
											$orginalQty = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
											$originalCost = $originalAmount / ($orginalQty > 0 ? $orginalQty : 1);
											
											$calQTY = $calQTY - ($cArr['remain_qty']/$cArr['unit_qty']);
											$calQTYRemain = $calQTY;
											$remain_qty = 0;
										}
										$stockOutId = DB::table('stocks')->where('id', $cArr["stock_id"])->update(['remain_qty'=>$remain_qty]);
										// if(!$stockOutId = DB::table('stocks')->where('id', $cArr["stock_id"])->update(['remain_qty'=>$remain_qty])){
										// 	DB::rollback();
										// 	throw new \Exception("Stock Out[{$key}] not insert.");
										// }

										echo "After 3:"; print_r($calQTY);
										echo "<br />";
										echo "remain 1 :".$remain_qty;
										echo "<br />";
									}
								}
							
						}
					}
					
				}
			}
		}
	}
}
