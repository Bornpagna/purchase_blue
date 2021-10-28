<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Datatables;
use Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Excel;
use App\Model\StockAdjust;
use App\Model\StockAdjustDetails;
use App\Model\Item;
use App\Model\Warehouse;
use App\Model\Stock;

class AdjustController extends Controller
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
				'stock_adjust_details.*',
				'items.cat_id',
				'items.code',
				'items.name',
				'items.alert_qty',
				'items.unit_stock',
				'items.unit_usage',
				'items.unit_purch',
				'items.cost_purch'
			];

			$OrderItem  = StockAdjustDetails::select($select)
						->leftJoin('items','stock_adjust_details.item_id','items.id')
						->where('stock_adjust_details.adjust_id',$request->id)
						->where('stock_adjust_details.delete',0)
						->get();

			return response()->json($OrderItem,200); 

		} catch (\Exception $e) {
			return response()->json([],200);
		}
    }
	
    public function index(){
		$data = [
			'title'			=> trans('lang.adjustment'),
			'icon'			=> 'fa fa-adjust',
			'small_title'	=> trans('lang.adjustment_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'dashboard'	=> [
						'caption' 	=> trans('lang.adjustment'),
				],
			],
			'rounte'		=> url("stock/adjust/dt"),
		];
		
		if(hasRole('stock_adjust_add')){
			$data = array_merge($data, ['rounteAdd'=> url('stock/adjust/add')]);
		}
		return view('stock.adjust.index',$data);
	}
	
	public function add(){
		$data = [
			'title'			=> trans('lang.adjustment'),
			'icon'			=> 'fa fa-plus',
			'small_title'	=> trans('lang.add_new'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'index'	=> [
						'url' 		=> url('stock/adjust'),
						'caption' 	=> trans('lang.adjustment'),
				],
				'add'	=> [
						'caption' 	=> trans('lang.add_new'),
				],
			],
			'rounteSave'	=> url('stock/adjust/save'),
			'rounteBack'	=> url('stock/adjust'),
		];
		return view('stock.adjust.add',$data);
	}
	
	public function edit(Request $request, $id){
		$id = decrypt($id);
		$obj = StockAdjust::find($id);
		if($obj){
			$data = [
				'title'			=> trans('lang.adjustment'),
				'icon'			=> 'fa fa-edit',
				'small_title'	=> trans('lang.edit'),
				'background'	=> '',
				'link'			=> [
					'home'	=> [
							'url' 		=> url('/'),
							'caption' 	=> trans('lang.home'),
					],
					'index'	=> [
							'url' 		=> url('stock/adjust'),
							'caption' 	=> trans('lang.adjustment'),
					],
					'edit'	=> [
							'caption' 	=> trans('lang.edit'),
					],
				],
				'rounteSave'	=> url('stock/adjust/update/'.$id),		
				'rounteBack'	=> url('stock/adjust'),		
				'obj'	=> $obj,		
			];
			return view('stock.adjust.edit',$data);
		}else{
			return redirect()->back();
		}
	}
	
	public function getDt(Request $request)
    {
		$pro_id = $request->session()->get('project');
		$sql = StockAdjust::where(['delete'=> 0, 'pro_id'=> $pro_id])->get();

        return Datatables::of($sql)
		->addColumn('details_url',function($row){
            return url('stock/adjust/subdt').'/'.$row->id;
        })
		->addColumn('action', function ($row) {
			$rounte_delete = url('stock/adjust/delete/'.$row->id);
			$rounte_edit = url('stock/adjust/edit/'.encrypt($row->id));
			$btnEdit = 'onclick="onEdit(this)"';
			$btnDelete = 'onclick="onDelete(this)"';
			
			if(!hasRole('stock_adjust_edit')){
				$btnEdit = "disabled";
			}
			if(!hasRole('stock_adjust_delete')){
				$btnDelete = "disabled";
			}
			return
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
		$stockAdjustments = StockAdjustDetails::where(['stock_adjust_details.delete' => 0, 'stock_adjust_details.adjust_id' => $id])->get();
		return Datatables::of($stockAdjustments)
		->addColumn('item',function($row){
			if($item = Item::find($row->item_id)){
				return "{$item->name} ({$item->code})";
			}
			return '';
		})
		->addColumn('warehouse',function($row){
			if($warehouse = Warehouse::find($row->warehouse_id)){
				return $warehouse->name;
			}
			return '';
		})
		->make(true);
	}

    public function save(Request $request){
		$rules = [
			'reference_no' 	=>'required|max:20|unique_stock_adjust',
			'trans_date' 	=>'required|max:20',
			'reference' 	=>'required',
		];
		if(count($request['line_index']) > 0){
			for($i=0;$i<count($request['line_index']);$i++){
				$rules['line_index.'.$i] 		= 'required';
				$rules['line_warehouse.'.$i] 	= 'required|max:11';
				$rules['line_item.'.$i] 		= 'required|max:11';
				$rules['line_unit.'.$i] 		= 'required';
				$rules['line_qty_stock.'.$i]	= 'required';
				$rules['line_qty_exactly.'.$i]	= 'required';
				$rules['line_qty_adjust.'.$i]	= 'required';
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
				'desc'			=>$request->desc,
				'created_by'	=>Auth::user()->id,
				'created_at'	=>date('Y-m-d H:i:s'),
			];

			if(!$id = DB::table('stock_adjusts')->insertGetId($data)){
				DB::rollback();
				throw new \Exception("Stock adjustment not insert.");
			}

			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){
					$reqQTY = floatval($request['line_qty_adjust'][$i]);
					$calQTY = $reqQTY ;
					$detail = [
						'adjust_id'      =>$id,
						'warehouse_id'   =>$request['line_warehouse'][$i],
						'line_no'        =>$request['line_index'][$i],
						'item_id'        =>$request['line_item'][$i],
						'unit'           =>$request['line_unit'][$i],
						'stock_qty'      =>$request['line_qty_stock'][$i],
						'current_qty'    =>$request['line_qty_exactly'][$i],
						'adjust_qty'     =>$request['line_qty_adjust'][$i],
						'note'           =>$request['line_note'][$i],
						'created_by'     =>Auth::user()->id,
						'created_at'     =>date('Y-m-d H:i:s'),
					];
					$defualCost = DB::table('items')->where('id',$request['line_item'][$i])->first();
					$isStockOut = (floatval($request['line_qty_adjust'][$i]) < 0);
					if (getSetting()->is_costing==1) {
						$costArr = getItemCost($request['line_item'][$i],$request['line_unit'][$i],$request['line_qty_adjust'][$i]);
						if ($costArr) {
							if (is_array($costArr) || is_object($costArr)) {
								foreach ($costArr as $cArr) {
									// attributes
									$qty    = (float)$cArr['qty'];
									$cost   = (float)$cArr["cost"];
									$amount = $qty * $cost;
									// cost for stocks table
									$stock = [
										'pro_id'         =>	$request->session()->get('project'),
										'ref_id'         =>	$id,
										'ref_no'         =>	$request->reference_no,
										'ref_type'       =>	'stock adjust',
										'line_no'        =>	$request['line_index'][$i],
										'item_id'        =>	$request['line_item'][$i],
										'unit'           =>	$request['line_unit'][$i],
										'qty'            =>	$isStockOut ? ($qty * -1) : $qty,
										'cost'           =>	$cost,
										'amount'         =>	$amount,
										'warehouse_id'   =>	$request['line_warehouse'][$i],
										'trans_date'     =>	$trans_date,
										'reference'      =>	$request->reference,
										'trans_ref'      => $isStockOut ? 'O' : 'I',
										'alloc_ref'      =>	getAllocateRef($request->reference_no),
										'created_by'     =>	Auth::user()->id,
										'created_at'     =>	date('Y-m-d H:i:s'),
									];
									
									if(!$stockID = DB::table('stocks')->insertGetId($stock)){
										DB::rollback();
										throw new \Exception("Stock out not insert.");
									}
								}
							}
						}else{
							$stock = [
								'pro_id'         =>	$request->session()->get('project'),
								'ref_id'         =>	$id,
								'ref_no'         =>	$request->reference_no,
								'ref_type'       =>	'stock adjust',
								'line_no'        =>	$request['line_index'][$i],
								'item_id'        =>	$request['line_item'][$i],
								'unit'           =>	$request['line_unit'][$i],
								'qty'            =>	$request['line_qty_adjust'][$i],
								'cost'           =>	$defualCost->cost_purch,
								'amount'         =>	$request['line_qty_adjust'][$i] * $defualCost->cost_purch,
								'warehouse_id'   =>	$request['line_warehouse'][$i],
								'trans_date'     =>	$trans_date,
								'reference'      =>	$request->reference,
								'trans_ref'      => $isStockOut ? 'O' : 'I',
								'alloc_ref'      =>	getAllocateRef($request->reference_no),
								'created_by'     =>	Auth::user()->id,
								'created_at'     =>	date('Y-m-d H:i:s'),
							];
							
							if(!$stockID = DB::table('stocks')->insertGetId($stock)){
								DB::rollback();
								throw new \Exception("Stock out not insert.");
							}
						}
					}else{

						$stock = [
							'pro_id'         =>	$request->session()->get('project'),
							'ref_id'         =>	$id,
							'ref_no'         =>	$request->reference_no,
							'ref_type'       =>	'stock adjust',
							'line_no'        =>	$request['line_index'][$i],
							'item_id'        =>	$request['line_item'][$i],
							'unit'           =>	$request['line_unit'][$i],
							'qty'            =>	($request['line_qty_adjust'][$i]),
							'warehouse_id'   =>	$request['line_warehouse'][$i],
							'trans_date'     =>	$trans_date,
							'reference'      =>	$request->reference,
							'trans_ref'      => $isStockOut ? 'O' : 'I',
							'alloc_ref'      =>	getAllocateRef($request->reference_no),
							'created_by'     =>	Auth::user()->id,
							'created_at'     =>	date('Y-m-d H:i:s'),
						];
						
						if(!$stockID = DB::table('stocks')->insertGetId($stock)){
							DB::rollback();
							throw new \Exception("Stock out not insert.");
						}
					}

					// $stockOut = [
					// 	'pro_id'         =>	$request->session()->get('project'),
					// 	'ref_id'         =>	$id,
					// 	'ref_no'         =>	$request->reference_no,
					// 	'ref_type'       =>	'stock adjust',
					// 	'line_no'        =>	$request['line_index'][$i],
					// 	'item_id'        =>	$request['line_item'][$i],
					// 	'unit'           =>	$request['line_unit'][$i],
					// 	// 'qty'            =>	$isStockOut ? ($calQTY * -1) : $calQTY,
					// 	// 'cost'           =>	$cost,
					// 	// 'amount'         =>	$amount,
					// 	'warehouse_id'   =>	$request['line_warehouse'][$i],
					// 	'trans_date'     =>	$trans_date,
					// 	'reference'      =>	$request->reference,
					// 	'trans_ref'      => $isStockOut ? 'O' : 'I',
					// 	'alloc_ref'      =>	getAllocateRef($request->reference_no),
					// 	'created_by'     =>	Auth::user()->id,
					// 	'created_at'     =>	date('Y-m-d H:i:s'),
					// ];

					// if (getSetting()->is_costing==1) {
					// 	if (getSetting()->stock_account=="FIFO") {
					// 		$sql  = "CALL COSTING_FIFO({$request->session()->get('project')},{$request['line_item'][$i]});";
					// 	}else{
					// 		$sql  = "CALL COSTING_LIFO({$request->session()->get('project')},{$request['line_item'][$i]});";
					// 	}
						
					// 	$stocks = DB::select($sql);
					// 	if(count($stocks) > 0){
					// 		foreach($stocks as $key=>$stock){
					// 			$qty =0;
					// 			$remain_qty = 0;
					// 			if($stock->type_ == "ADJ" ){
					// 				if($key == 0){
					// 					if($stock->remain_qty >= $calQTY){
					// 						$remain_qty = $stock->remain_qty + $calQTY;
					// 						$qty 	= $isStockOut ? ($calQTY * 1) : $calQTY;
					// 						$cost 	= $stock->cost;
					// 						$amount = $qty * $cost;
					// 						$stockOut = array_merge($stockOut,['qty'    => $qty]);
					// 						$stockOut = array_merge($stockOut,['cost'   => $cost]);
					// 						$stockOut = array_merge($stockOut,['amount' => $amount]);

					// 						// $detail = array_merge($detail,['qty'    => $qty]);
					// 						// $detail = array_merge($detail,['total_cost'    => $amount]);
					// 						// $detail = array_merge($detail,['cost' => $cost]);
											
					// 						$calQTY = 0;
					// 					}else{
					// 						$remain_qty = 0;
					// 						$calQTYRemain = $calQTY - $stock->remain_qty; 

					// 						$qty 	= $stock->remain_qty * -1;
					// 						$cost 	= $stock->cost;
					// 						$amount = $qty * $cost;
					// 						$stockOut = array_merge($stockOut,['qty'    => $qty]);
					// 						$stockOut = array_merge($stockOut,['cost'   => $cost]);
					// 						$stockOut = array_merge($stockOut,['amount' => $amount]);

					// 						// $detail = array_merge($detail,['qty'    => $qty]);
					// 						// $detail = array_merge($detail,['total_cost'    => $amount]);
					// 						// $detail = array_merge($detail,['cost' => $cost]);
					// 						$calQTY = $calQTY - $stock->remain_qty;
					// 					}
										
					// 					if(!$stockOutId = DB::table('stocks')->insertGetId($stockOut)){
					// 						DB::rollback();
					// 						print_r(2222);exit;
					// 						throw new \Exception("Stock Out[{$i}] not insert.");
					// 					}else{
					// 						Stock::where('id',$stock->stock_id)->update(['remain_qty'=>$remain_qty]);
					// 						if(!$stockAdjustDetailsID = DB::table('stock_adjust_details')->insertGetId($detail)){
					// 							DB::rollback();
					// 							throw new \Exception("Stock adjust details not insert.");
					// 						}
					// 					}
					// 				}
					// 			}
					// 			else{
					// 				if($key == 0){
					// 					$qty 	= $isStockOut ? ($calQTY * 1) : $calQTY;
					// 					$cost 	= $stock->cost;
					// 					$amount = $qty * $cost;
					// 					$stockOut = array_merge($stockOut,['qty'    => $qty]);
					// 					$stockOut = array_merge($stockOut,['cost'   => $cost]);
					// 					$stockOut = array_merge($stockOut,['amount' => $amount]);

					// 					if(!$stockOutId = DB::table('stocks')->insertGetId($stockOut)){
					// 						DB::rollback();
					// 						throw new \Exception("Stock Out[{$i}] not insert.");
					// 					}else{
					// 						Stock::where('id',$stock->stock_id)->update(['remain_qty'=>$remain_qty]);
					// 						if(!$stockAdjustDetailsID = DB::table('stock_adjust_details')->insertGetId($detail)){
					// 							DB::rollback();
					// 							throw new \Exception("Stock adjust details not insert.");
					// 						}
					// 					}
					// 				}
					// 			}
					// 		}
							
					// 	}else{
					// 		DB::rollback();
					// 		// print_r(123);exit;
					// 		throw new \Exception("Stock Out[{$i}] not insert.");
					// 	}
						
					// }else{

					// 	$stockOut = array_merge($stockOut,['qty'  => ($calQTY * (-1))]);
					// 	if(!$stockOutId = DB::table('stocks')->insertGetId($stockOut)){
					// 		DB::rollback();
					// 		print_r(111);exit;
					// 		throw new \Exception("Stock Out[{$i}] not insert.");
					// 	}
					// }

					// if(!$stockAdjustDetailsID = DB::table('stock_adjust_details')->insertGetId($detail)){
					// 	DB::rollback();
					// 	throw new \Exception("Stock adjust details not insert.");
					// }
				}
			}
			if(!$stockAdjustDetailsID = DB::table('stock_adjust_details')->insertGetId($detail)){
				DB::rollback();
				throw new \Exception("Stock adjust details not insert.");
			}
			DB::commit();
			if($request->btnSubmit==1){
				return redirect('stock/adjust')->with('success',trans('lang.save_success'));
			}
			// return redirect()->back()->with('success',trans('lang.save_success'));
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
		];
		if(count($request['line_index']) > 0){
			for($i=0;$i<count($request['line_index']);$i++){
				$rules['line_index.'.$i] 		= 'required';
				$rules['line_warehouse.'.$i] 	= 'required|max:11';
				$rules['line_item.'.$i] 		= 'required|max:11';
				$rules['line_unit.'.$i] 		= 'required';
				$rules['line_qty_stock.'.$i]	= 'required';
				$rules['line_qty_exactly.'.$i]	= 'required';
				$rules['line_qty_adjust.'.$i]	= 'required';
			}
		}
        Validator::make($request->all(),$rules)->validate();
		try {
			DB::beginTransaction();

			if(!$stockAdjust = StockAdjust::find($id)){
				throw new \Exception("StockAdjust[{$id}] not found.");
			}

			if(checkAllocate("O", $request->reference_no) > 0){
				return redirect()->back()->with('error',trans('lang.trans_error'));
			}
			$originalDate = $request['trans_date'];
			$trans_date = date("Y-m-d", strtotime($originalDate));
			$data = [
				'trans_date'	=>$trans_date,
				'reference'		=>$request->reference,
				'desc'			=>$request->desc,
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];

			DB::table('stock_adjusts')->where(['id'=>$id])->update($data);
			// delete all last stock adjustment transactions
			DB::table('stocks')->where(['ref_id'=>$id,'ref_no'=>$stockAdjust->ref_no,'ref_type'=>'stock adjust'])->delete();
			DB::table('stock_adjust_details')->where(['adjust_id' => $id])->delete();

			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){

					$detail = [
						'adjust_id'      =>$id,
						'warehouse_id'   =>$request['line_warehouse'][$i],
						'line_no'        =>$request['line_index'][$i],
						'item_id'        =>$request['line_item'][$i],
						'unit'           =>$request['line_unit'][$i],
						'stock_qty'      =>$request['line_qty_stock'][$i],
						'current_qty'    =>$request['line_qty_exactly'][$i],
						'adjust_qty'     =>$request['line_qty_adjust'][$i],
						'note'           =>$request['line_note'][$i],
						'created_by'     =>Auth::user()->id,
						'created_at'     =>date('Y-m-d H:i:s'),
					];

					$isStockOut = (floatval($request['line_qty_adjust'][$i]) < 0);

					if (getSetting()->is_costing==1) {
						$costArr = getItemCost($request['line_item'][$i],$request['line_unit'][$i],$request['line_qty_adjust'][$i]);
						if ($costArr) {
							if (is_array($costArr) || is_object($costArr)) {
								foreach ($costArr as $cArr) {
									// attributes
									$qty    = (float)$cArr['qty'];
									$cost   = (float)$cArr["cost"];
									$amount = $qty * $cost;
									// cost for stocks table
									$stock = [
										'pro_id'         =>	$request->session()->get('project'),
										'ref_id'         =>	$id,
										'ref_no'         =>	$request->reference_no,
										'ref_type'       =>	'stock adjust',
										'line_no'        =>	$request['line_index'][$i],
										'item_id'        =>	$request['line_item'][$i],
										'unit'           =>	$request['line_unit'][$i],
										'qty'            =>	$isStockOut ? ($qty * -1) : $qty,
										'cost'           =>	$cost,
										'amount'         =>	$amount,
										'warehouse_id'   =>	$request['line_warehouse'][$i],
										'trans_date'     =>	$trans_date,
										'reference'      =>	$request->reference,
										'trans_ref'      => $isStockOut ? 'O' : 'I',
										'alloc_ref'      =>	getAllocateRef($request->reference_no),
										'created_by'     =>	Auth::user()->id,
										'created_at'     =>	date('Y-m-d H:i:s'),
									];
									
									if(!$stockID = DB::table('stocks')->insertGetId($stock)){
										DB::rollback();
										throw new \Exception("Stock out not insert.");
									}
								}
							}
						}
					}else{
						$stock = [
							'pro_id'         =>	$request->session()->get('project'),
							'ref_id'         =>	$id,
							'ref_no'         =>	$request->reference_no,
							'ref_type'       =>	'stock adjust',
							'line_no'        =>	$request['line_index'][$i],
							'item_id'        =>	$request['line_item'][$i],
							'unit'           =>	$request['line_unit'][$i],
							'qty'            =>	($request['line_qty_adjust'][$i]),
							'warehouse_id'   =>	$request['line_warehouse'][$i],
							'trans_date'     =>	$trans_date,
							'reference'      =>	$request->reference,
							'trans_ref'      => $isStockOut ? 'O' : 'I',
							'alloc_ref'      =>	getAllocateRef($request->reference_no),
							'created_by'     =>	Auth::user()->id,
							'created_at'     =>	date('Y-m-d H:i:s'),
						];
						
						if(!$stockID = DB::table('stocks')->insertGetId($stock)){
							DB::rollback();
							throw new \Exception("Stock out not insert.");
						}
					}

					if(!$stockAdjustDetailsID = DB::table('stock_adjust_details')->insertGetId($detail)){
						DB::rollback();
						throw new \Exception("Stock adjust details not insert.");
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
			$obj = StockAdjust::find($id);
			if(checkAllocate("O", $obj->ref_no) > 0){
				return redirect()->back()->with('error',trans('lang.trans_error'));
			}
			$data = [
				'delete'		=>1,
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];
			DB::table('stock_adjusts')->where(['id'=>$id])->update($data);
			$dataDetails = [
				'delete'		=>1,
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];
			DB::table('stock_adjust_details')->where(['adjust_id'=>$id])->update($dataDetails);
			$stock = [
				'delete'		=>1,
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];
			DB::table('stocks')->where(['ref_id'=>$id,'ref_no'=>$obj->ref_no,'ref_type'=>'stock adjust'])->update($stock);
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
			$item_id = $request['item_id'];
			$unit = $request['unit'];
			$prefix = getSetting()->round_number;
			$originalDate = $request['trans_date'];
			$trans_date = date("Y-m-d", strtotime($originalDate));
			$sql = "SELECT ROUND((F.stock_qty / F.adj_qty), $prefix) AS stock_qty FROM (SELECT E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS adj_qty FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_stocks.`item_id`, pr_stocks.`unit`, pr_stocks.`qty` FROM pr_stocks WHERE pr_stocks.`item_id` = $item_id AND pr_stocks.`warehouse_id` = $warehouse_id AND pr_stocks.`delete` = 0 AND pr_stocks.`trans_date` <= '$trans_date') AS A) AS B) AS C) AS D GROUP BY D.item_id) AS E) AS F "; 
			$obj = collect(DB::select($sql))->first();
			if($obj){
				return $obj->stock_qty;
			}
		}
		return 0;
	}

	public function ImportAdjust(){
		
	}
}
