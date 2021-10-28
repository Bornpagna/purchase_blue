<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Datatables;
use Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Excel;
use App\Model\ReturnDelivery;
use App\Model\ReturnDeliveryItem;
use App\Model\DeliveryItem;
use App\Model\Delivery;
use App\Model\Supplier;
use App\Model\Warehouse;
use App\Model\Item;
use App\Model\Unit;

class ReturnDeliveryController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
		$this->middleware('CheckProject');
    }

    public function GetItem(Request $request)
    {
    	try {
			
			$select = [
				'return_delivery_items.*',
				'items.cat_id',
				'items.code',
				'items.name',
				'items.alert_qty',
				'items.unit_stock',
				'items.unit_usage',
				'items.unit_purch',
				'items.cost_purch'
			];

			$OrderItem  = ReturnDeliveryItem::select($select)
						->leftJoin('items','return_delivery_items.item_id','items.id')
						->where('return_delivery_items.return_id',$request->id)
						->get();

			return response()->json($OrderItem,200); 

		} catch (\Exception $e) {
			return response()->json(['line'=>$e->getLine(),'message'=>$e->getMessage()],500);
		}
    }
	
    public function index(){
		$data = [
			'title'			=> trans('lang.return_delivery'),
			'icon'			=> 'fa fa-exchange',
			'small_title'	=> trans('lang.return_delivery_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'dashboard'	=> [
						'caption' 	=> trans('lang.return_delivery'),
				],
			],
			'rounte'		=> url("stock/redeliv/dt"),
		];
		
		if(hasRole('delivery_return_add')){
			$data = array_merge($data, ['rounteAdd'=> url('stock/redeliv/add')]);
		}
		return view('stock.delivery.return.index', $data);
	}
	
	public function add(Request $request){
		$data = [
			'title'			=> trans('lang.return_delivery'),
			'icon'			=> 'fa fa-plus',
			'small_title'	=> trans('lang.add_new'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
					'url' 		=> url('/'),
					'caption' 	=> trans('lang.home'),
				],
				'index'	=> [
					'url' 		=> url('stock/redeliv'),
					'caption' 	=> trans('lang.return_delivery'),
				],
				'add'	=> [
					'caption' 	=> trans('lang.add_new'),
				],
			],
			'rounteSave'	=> url('stock/redeliv/save'),
			'rounteBack'	=> url('stock/redeliv'),
			'pro_id'		=> $request->session()->get('project'),
		];
		return view('stock.delivery.return.add',$data);
	}
	
	public function edit(Request $request, $id){
		$id  = decrypt($id);
		$obj = ReturnDelivery::find($id);
		if($obj){
			$data = [
				'title'			=> trans('lang.return_delivery'),
				'icon'			=> 'fa fa-edit',
				'small_title'	=> trans('lang.edit'),
				'background'	=> '',
				'link'			=> [
					'home'	=> [
							'url' 		=> url('/'),
							'caption' 	=> trans('lang.home'),
					],
					'index'	=> [
							'url' 		=> url('stock/redeliv'),
							'caption' 	=> trans('lang.return_delivery'),
					],
					'edit'	=> [
							'caption' 	=> trans('lang.edit'),
					],
				],
				'rounteSave' => url('stock/redeliv/update/'.$id),		
				'rounteBack' => url('stock/redeliv'),		
				'obj'        => $obj,
			];
			return view('stock.delivery.return.edit',$data);
		}else{
			return redirect()->back();
		}
	}
	
	public function getDt(Request $request)
    {
		$pro_id = $request->session()->get('project');
		$returnDeliveries = ReturnDelivery::where(['pro_id'=> $pro_id, 'delete' => 0])->get();
        return Datatables::of($returnDeliveries)
		->addColumn('deliv_no',function($row){
			if($delivery = Delivery::find($row->del_id)){
				return $delivery->ref_no;
			}
            return '';
        })
		->addColumn('supplier',function($row){
			if($supplier = Supplier::find($row->sup_id)){
				return "{$supplier->desc} ({$supplier->name})";
			}
            return '';
        })
		->addColumn('details_url',function($row){
            return url('stock/redeliv/subdt').'/'.$row->id;
        })
		->addColumn('action', function ($row) {
			$rounte_delete = url('stock/redeliv/delete/'.$row->id);
			$rounte_edit = url('stock/redeliv/edit/'.encrypt($row->id));
			$btnEdit = 'onclick="onEdit(this)"';
			$btnDelete = 'onclick="onDelete(this)"';
			
			if(!hasRole('delivery_return_edit')){
				$btnEdit = "disabled";
			}
			if(!hasRole('delivery_return_delete')){
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
		$returnDeliveryItems = ReturnDeliveryItem::where(['return_id'=> $id])->get();
        return Datatables::of($returnDeliveryItems)
		->addColumn('from_warehouse',function($row){
			if($warehouse = Warehouse::find($row->warehouse_id)){
				return $warehouse->name;
			}
			return '';
		})
		->addColumn('item',function($row){
			if($item = Item::find($row->item_id)){
				return "{$item->name} ({$item->code})";
			}
			return '';
		})
		->make(true);
	}

    public function save(Request $request){
		$rules = [
			'reference_no' 	=>'required|max:20|unique_return_delivery',
			'trans_date' 	=>'required|max:20',
			'supplier' 		=>'required|max:11',
			'deliv_no' 		=>'required|max:11',
		];
		if(count($request['line_index']) > 0){
			for($i=0;$i<count($request['line_index']);$i++){
				$rules['line_index.'.$i]		= 'required';
				$rules['line_item.'.$i]			= 'required|max:11';
				$rules['line_unit.'.$i]			= 'required';
				$rules['line_qty.'.$i]			= 'required';
				$rules['line_warehouse.'.$i]	= 'required|max:11';
			}
		}
        Validator::make($request->all(),$rules)->validate();
		try {
			DB::beginTransaction();
			$originalDate = $request['trans_date'];
			$trans_date = date("Y-m-d", strtotime($originalDate));
			$data = [
				'pro_id'		=>$request->session()->get('project'),
				'dep_id'		=>$request->deliv_no,
				'sup_id'		=>$request->supplier,
				'ref_no'		=>$request->reference_no,
				'trans_date'	=>$trans_date,
				'desc'			=>$request->desc,
				'created_by'	=>Auth::user()->id,
				'created_at'	=>date('Y-m-d H:i:s'),
			];

			if($request->hasFile('photo')) {
				$photo = upload($request,'photo','assets/upload/picture/return_delivery/');
				$data = array_merge($data,['photo'=>$photo]);
			}

			if(!$id = DB::table('return_deliveries')->insertGetId($data)){
				throw new \Exception("Return Deliveries[{$request->reference_no}] not insert.");
			}

			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){

					$detail = [
						'return_id'      =>$id,
						'warehouse_id'   =>$request['line_warehouse'][$i],
						'line_no'        =>$request['line_index'][$i],
						'item_id'        =>$request['line_item'][$i],
						'unit'           =>$request['line_unit'][$i],
						'qty'            =>$request['line_qty'][$i],
						'note'           =>$request['line_reference'][$i]
					];

					$stockOut = [
						'pro_id'         =>$request->session()->get('project'),
						'ref_id'         =>$id,
						'ref_no'         =>$request->reference_no,
						'ref_type'       =>'return delivery',
						'line_no'        =>$request['line_index'][$i],
						'item_id'        =>$request['line_item'][$i],
						'unit'           =>$request['line_unit'][$i],
						'qty'            =>($request['line_qty'][$i])*(-1),
						'warehouse_id'   =>$request['line_warehouse'][$i],
						'trans_date'     =>$trans_date,
						'reference'      =>$request['line_reference'][$i],
						'trans_ref'      =>'O',
						'alloc_ref'      =>getAllocateRef($request->reference_no),
						'created_by'     =>Auth::user()->id,
						'created_at'     =>date('Y-m-d H:i:s'),
					];

					if (getSetting()->is_costing==1) {
						$itemId = $request['line_item'][$i];
	
						if(!$deliveryItem = DeliveryItem::where(['del_id'=> $request->deliv_no, 'item_id' => $itemId])->first()){
							throw new \Exception("DeliveryItem[{$itemId}] not found.");
						}

						$qty 	= (float)$request['line_qty'][$i] * -1;
						$cost 	= $deliveryItem->price;
						$amount = $qty * $cost;

						$stockOut = array_merge($stockOut,['cost'=>$cost]);
						$stockOut = array_merge($stockOut,['amount'=>$amount]);
					}

					if (getSetting()->is_costing==1) {
						$itemId = $request['line_item'][$i];
	
						if(!$deliveryItem = DeliveryItem::where(['del_id'=> $request->deliv_no, 'item_id' => $itemId])->first()){
							throw new \Exception("DeliveryItem[{$itemId}] not found.");
						}
						if ($request['line_cost'][$i]) {
							$costArr = getMinQTY($request['line_item'][$i],$request['line_unit'][$i],$request['line_qty'][$i],$deliveryItem->price);
							if ($costArr) {
								$unit 	= $costArr['min_unit_to_code'];
								$qty 	= $costArr['qty'];
								$cost 	= $costArr['price'];
								$amount = $qty * $cost;
								$stockOut = array_merge($stockOut,['qty' => $qty]);
								$stockOut = array_merge($stockOut,['remain_qty' => $qty]);
								$stockOut = array_merge($stockOut,['unit' => $unit]);
								$stockOut = array_merge($stockOut,['cost' => $cost]);
								$stockOut = array_merge($stockOut,['amount' => $amount]);

								// $detail = array_merge($detail,['unit_converted'=>$unit]);
								// $detail = array_merge($detail,['qty_converted'=>$qty]);
								// $detail = array_merge($detail,['price_converted'=>$cost]);
								// $detail = array_merge($detail,['total_price_converted'=>$amount]);
								
							}
						}
					}

					// insert return delivery items
					if(!$returnDeliveryItemId = DB::table('return_delivery_items')->insert($detail)){
						DB::rollback();
						throw new \Exception("Return Delivery Item[{$i}] not insert.");
					}
					// insert stocks
					if(!$stockOutId = DB::table('stocks')->insert($stockOut)){
						DB::rollback();
						throw new \Exception("Stock Out[{$i}] not insert.");
					}
					
					$unit_stock = '';
					$objItem = Item::where(['id'=>$request['line_item'][$i]])->first();
					if($objItem){
						$unit_stock = $objItem->unit_stock;
					}
					
					$del_unit = "";
					$del_return_qty = 0;
					$obj = DeliveryItem::where(['del_id'=>$request->deliv_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->first();
					if($obj){
						$del_unit = $obj->unit;
						$del_return_qty = $obj->return_qty;
					}
					
					$stock_qty_del = 0;
					$objUnit = Unit::where(['from_code'=>$del_unit,'to_code'=>$unit_stock])->first();
					if($objUnit){
						$stock_qty_del = $objUnit->factor;
					}
					
					$stock_qty_deliv = 0;
					$objUnit2 = Unit::where(['from_code'=>$request['line_unit'][$i],'to_code'=>$unit_stock])->first();
					if($objUnit2){
						$stock_qty_deliv = floatval($objUnit2->factor) * floatval($request['line_qty'][$i]);
					}
					
					$new_qty = (floatval($stock_qty_deliv) / floatval($stock_qty_del)) + floatval($del_return_qty);
					DB::table('delivery_items')->where(['del_id'=>$request->deliv_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->update(['return_qty'=>$new_qty]);
				}
			}
			
			DB::commit();
			if($request->btnSubmit==1){
				return redirect('stock/redeliv')->with('success',trans('lang.save_success'));
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
			'supplier' 		=>'required|max:11',
			'deliv_no' 		=>'required|max:11',
		];
		if(count($request['line_index']) > 0){
			for($i=0;$i<count($request['line_index']);$i++){
				$rules['line_index.'.$i]		= 'required';
				$rules['line_item.'.$i]			= 'required|max:11';
				$rules['line_unit.'.$i]			= 'required';
				$rules['line_qty.'.$i]			= 'required';
				$rules['line_warehouse.'.$i]	= 'required|max:11';
			}
		}
        Validator::make($request->all(),$rules)->validate();
		try {
			DB::beginTransaction();

			if(!$returnDelivery = ReturnDelivery::find($id)){
				throw new \Exception("ReturnDelivery[{$id}] not found.");
			}

			$originalDate = $request['trans_date'];
			$trans_date = date("Y-m-d", strtotime($originalDate));
			$data = [
				'del_id'		=>$request->deliv_no,
				'sup_id'		=>$request->supplier,
				'ref_no'		=>$request->reference_no,
				'trans_date'	=>$trans_date,
				'sub_total'		=>$request->sub_total,
				'refund'		=>$request->refund,
				'grand_total'	=>$request->grand_total,
				'desc'			=>$request->desc,
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];

			if($request->hasFile('photo')) {
				$photo = upload($request,'photo','assets/upload/picture/return_delivery/');
				$data = array_merge($data,['photo'=>$photo]);
			}

			DB::table('return_deliveries')->where(['id'=>$id])->update($data);
			// delete all return delivery items
			DB::table('return_delivery_items')->where(['return_id'=> $id])->delete();
			// delete all return delivery items stock
			DB::table('stocks')->where([
				'ref_id' => $id,
				'ref_no' => $returnDelivery->ref_no,
				'ref_type' => 'return delivery'
			])->delete();

			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){

					$detail = [
						'warehouse_id'   =>$request['line_warehouse'][$i],
						'unit'           =>$request['line_unit'][$i],
						'qty'            =>$request['line_qty'][$i],
						'price'          =>$request['line_price'][$i],
						'amount'         =>$request['line_amount'][$i],
						'refund'         =>$request['line_refund'][$i],
						'total'          =>$request['line_total'][$i],
						'note'           =>$request['line_reference'][$i]
					];

					$stockOut = [
						'pro_id'         =>$request->session()->get('project'),
						'ref_id'         =>$id,
						'ref_no'         =>$request->reference_no,
						'ref_type'       =>'return delivery',
						'line_no'        =>$request['line_index'][$i],
						'item_id'        =>$request['line_item'][$i],
						'unit'           =>$request['line_unit'][$i],
						'qty'            =>($request['line_qty'][$i])*(-1),
						'warehouse_id'   =>$request['line_warehouse'][$i],
						'trans_date'     =>$trans_date,
						'reference'      =>$request['line_reference'][$i],
						'trans_ref'      =>'O',
						'alloc_ref'      =>getAllocateRef($request->reference_no),
						'created_by'     =>Auth::user()->id,
						'created_at'     =>date('Y-m-d H:i:s'),
					];

					if (getSetting()->is_costing==1) {
						$itemId = $request['line_item'][$i];
	
						if(!$deliveryItem = DeliveryItem::where(['del_id'=> $request->deliv_no, 'item_id' => $itemId])->first()){
							throw new \Exception("DeliveryItem[{$vitemId}] not found.");
						}

						$qty 	= (float)$request['line_qty'][$i] * -1;
						$cost 	= $deliveryItem->price;
						$amount = $qty * $cost;

						$stockOut = array_merge($stockOut,['cost'=>$cost]);
						$stockOut = array_merge($stockOut,['amount'=>$amount]);
					}

					// insert return delivery items
					if(!$returnDeliveryItemId = DB::table('return_delivery_items')->insert($detail)){
						DB::rollback();
						throw new \Exception("Return Delivery Item[{$i}] not insert.");
					}
					// insert stocks
					if(!$stockOutId = DB::table('stocks')->insert($stockOut)){
						DB::rollback();
						throw new \Exception("Stock Out[{$i}] not insert.");
					}
					
					$unit_stock = '';
					$objItem = Item::where(['id'=>$request['line_item'][$i]])->first();
					if($objItem){
						$unit_stock = $objItem->unit_stock;
					}
					
					$del_unit = "";
					$del_return_qty = 0;
					$obj = DeliveryItem::where(['del_id'=>$request->deliv_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->first();
					if($obj){
						$del_unit = $obj->unit;
						$del_return_qty = $obj->return_qty;
					}
					
					$stock_qty_del = 0;
					$objUnit = Unit::where(['from_code'=>$del_unit,'to_code'=>$unit_stock])->first();
					if($objUnit){
						$stock_qty_del = $objUnit->factor;
					}
					
					$stock_qty_deliv = 0;
					$objUnit2 = Unit::where(['from_code'=>$request['line_unit'][$i],'to_code'=>$unit_stock])->first();
					if($objUnit2){
						$stock_qty_deliv = floatval($objUnit2->factor) * floatval($request['line_qty'][$i]);
					}
					
					
					$old_return_unit = "";
					$old_return_qty = 0;
					$objReturn = ReturnDeliveryItem::where(['return_id'=>$id,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->first();
					if($objReturn){
						$old_return_unit = $objReturn->unit;
						$old_return_qty = $objReturn->qty;
					}
					
					$old_return_stock_qty = 0;
					$objUnit3 = Unit::where(['from_code'=>$old_return_unit,'to_code'=>$unit_stock])->first();
					if($objUnit3){
						$old_return_stock_qty = floatval($objUnit3->factor) * floatval($old_return_qty);
					}
					
					$new_qty = (floatval($del_return_qty) - (floatval($old_return_stock_qty) / floatval($stock_qty_del))) + (floatval($stock_qty_deliv) / floatval($stock_qty_del));
					DB::table('delivery_items')->where(['del_id'=>$request->deliv_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->update(['return_qty'=>$new_qty]);
				
					DB::table('return_delivery_items')->where(['return_id'=>$id,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->update($detail);
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
			$obj = ReturnDelivery::find($id);
			$objDeliv = ReturnDeliveryItem::where(['return_id'=>$id])->get();
			if(count($objDeliv) > 0){
				$stockDelete = [
					'delete'		=>1,
					'updated_by'	=>Auth::user()->id,
					'updated_at'	=>date('Y-m-d H:i:s'),
				];
				DB::table('stocks')->where(['ref_id'=>$id,'ref_no'=>$obj->ref_no,'ref_type'=>'return delivery','trans_ref'=>'O'])->update($stockDelete);
				foreach($objDeliv as $row){
					
					$unit_stock = '';
					$objItem = Item::where(['id'=>$row['item_id']])->first();
					if($objItem){
						$unit_stock = $objItem->unit_stock;
					}
					
					$del_unit = "";
					$del_return_qty = 0;
					$objDelivery = DeliveryItem::where(['del_id'=>$obj->del_id,'line_no'=>$row['line_no'],'item_id'=>$row['item_id']])->first();
					if($objDelivery){
						$del_unit = $objDelivery->unit;
						$del_return_qty = $objDelivery->return_qty;
					}
					
					$stock_qty_del = 0;
					$objUnit = Unit::where(['from_code'=>$del_unit,'to_code'=>$unit_stock])->first();
					if($objUnit){
						$stock_qty_del = $objUnit->factor;
					}
					
					$old_return_unit = "";
					$old_return_qty = 0;
					$objReturn = ReturnDeliveryItem::where(['return_id'=>$id,'line_no'=>$row['line_no'],'item_id'=>$row['item_id']])->first();
					if($objReturn){
						$old_return_unit = $objReturn->unit;
						$old_return_qty = $objReturn->qty;
					}
					
					$old_return_stock_qty = 0;
					$objUnit3 = Unit::where(['from_code'=>$old_return_unit,'to_code'=>$unit_stock])->first();
					if($objUnit3){
						$old_return_stock_qty = floatval($objUnit3->factor) * floatval($old_return_qty);
					}
					
					$new_qty = floatval($del_return_qty) - (floatval($old_return_stock_qty) / floatval($stock_qty_del));
					DB::table('delivery_items')->where(['del_id'=>$obj->del_id,'line_no'=>$row['line_no'],'item_id'=>$row['item_id']])->update(['return_qty'=>$new_qty]);
				}
			}
			$data = [
				'delete'		=>1,
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];
			DB::table('return_deliveries')->where(['id'=>$id])->update($data);
			DB::commit();
			return redirect()->back()->with('success',trans('lang.delete_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.delete_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }
	
	function getItemStock(Request $request){
		// print_r($request->ajax());
		if($request->ajax()){
			$warehouse_id = $request['warehouse_id'];
			$item_id = $request['item_id'];
			$unit = $request['unit'];
			$stock_qty = 0;
			$prefix = getSetting()->round_number;
			$originalDate = $request['trans_date'];
			$trans_date = date("Y-m-d", strtotime($originalDate));
			$sql_stock = "SELECT (F.stock_qty / F.use_qty) AS stock_qty FROM (SELECT E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS use_qty FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_stocks.`item_id`, pr_stocks.`unit`, pr_stocks.`qty` FROM pr_stocks WHERE pr_stocks.`item_id` = $item_id AND pr_stocks.`warehouse_id` = $warehouse_id AND pr_stocks.`delete` = 0) AS A) AS B) AS C) AS D GROUP BY D.item_id) AS E) AS F";
			$objStock = collect(DB::select($sql_stock))->first();

			if($objStock){
				$stock_qty = floatval($objStock->stock_qty);
			}
			return $stock_qty;
		}
		return 0;
	}
}
