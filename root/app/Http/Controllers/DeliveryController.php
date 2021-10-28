<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Datatables;
use Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Excel;
use App\Model\Delivery;
use App\Model\DeliveryItem;
use App\Model\OrderItem;
use App\Model\Order;
use App\Model\Request as PurchaseRequest;
use App\Model\Item;
use App\Model\Unit;
use App\Model\Warehouse;
use App\Model\Supplier;
use App\Model\Stock;
use App\Model\Project;
use App\Model\SystemData;
use App\User;

class DeliveryController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
		$this->middleware('CheckProject');
    }

    public function GetRef(Request $request)
    {
    	try {
    		$pro_id = Session::get('project');
			if($request->del_id){
				$Ref = Delivery::select(['deliveries.id',DB::raw("pr_deliveries.ref_no as text")])
						->leftJoin('delivery_items','deliveries.id','delivery_items.del_id')
						->where('deliveries.id',$del_id)
						->where('deliveries.pro_id',$pro_id)
						->groupBy('deliveries.id')
						->offset(0)
						->limit(10)
						->paginate(10);
			}else{
				$Ref = Delivery::select(['deliveries.id',DB::raw("pr_deliveries.ref_no as text")])
						->leftJoin('delivery_items','deliveries.id','delivery_items.del_id')
						->where('deliveries.pro_id',$pro_id)
						->where('deliveries.ref_no','like','%'.($request->q).'%')
						->groupBy('deliveries.id')
						->offset(0)
						->limit(10)
						->paginate(10);
			}
			return response()->json($Ref,200);
    	} catch (\Exception $e) {
    		return response()->json(['id'=>$e->getLine(),'text'=>$e->getMessage()],200);
    	}
    }
	
    public function index(){
		$data = [
			'title'			=> trans('lang.delivery'),
			'icon'			=> 'fa fa-sign-in',
			'small_title'	=> trans('lang.delivery_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'dashboard'	=> [
						'caption' 	=> trans('lang.delivery'),
				],
			],
			'rounte'		=> url("stock/deliv/dt"),
			'rounteOrder'	=> url("purch/order/dt/1/1"),
			
			''	=> url(''),
		];
		
		if(hasRole('delivery_entry_add')){
			$data = array_merge($data, ['rounteAdd'=> url('stock/deliv/add')]);
		}
		return view('stock.delivery.entry.index', $data);
	}
	
	public function add(Request $request){
		$data = [
			'title'			=> trans('lang.delivery'),
			'icon'			=> 'fa fa-plus',
			'small_title'	=> trans('lang.add_new'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
					'url' 		=> url('/'),
					'caption' 	=> trans('lang.home'),
				],
				'index'	=> [
					'url' 		=> url('stock/deliv'),
					'caption' 	=> trans('lang.delivery'),
				],
				'add'	=> [
					'caption' 	=> trans('lang.add_new'),
				],
			],
			'rounteSave'	=> url('stock/deliv/save'),
			'rounteBack'	=> url('stock/deliv'),
			'pro_id'		=> $request->session()->get('project'),
		];
		return view('stock.delivery.entry.add',$data);
	}
	
	public function edit(Request $request, $id){
		$id = decrypt($id);
		$obj = Delivery::find($id);
		if($obj){
			$data = [
				'title'			=> trans('lang.delivery'),
				'icon'			=> 'fa fa-edit',
				'small_title'	=> trans('lang.edit'),
				'background'	=> '',
				'link'			=> [
					'home'	=> [
							'url' 		=> url('/'),
							'caption' 	=> trans('lang.home'),
					],
					'index'	=> [
							'url' 		=> url('stock/deliv'),
							'caption' 	=> trans('lang.delivery'),
					],
					'edit'	=> [
							'caption' 	=> trans('lang.edit'),
					],
				],
				'rounteSave'	=> url('stock/deliv/update/'.$id),		
				'rounteBack'	=> url('stock/deliv'),		
				'obj'	=> $obj,
			];
			return view('stock.delivery.entry.edit',$data);
		}else{
			return redirect()->back();
		}
	}

	public function makeDelivery(Request $request, $id){
		$id = $id;
		$obj = Order::find($id);
		if($obj){
			$data = [
				'title'			=> trans('lang.delivery'),
				'icon'			=> 'fa fa-edit',
				'small_title'	=> trans('lang.edit'),
				'background'	=> '',
				'link'			=> [
					'home'	=> [
							'url' 		=> url('/'),
							'caption' 	=> trans('lang.home'),
					],
					'index'	=> [
							'url' 		=> url('stock/deliv'),
							'caption' 	=> trans('lang.delivery'),
					],
					'edit'	=> [
							'caption' 	=> trans('lang.edit'),
					],
				],
				'rounteSave'	=> url('stock/deliv/save'),		
				'rounteBack'	=> url('stock/deliv'),		
				'obj'	=> $obj,
			];
			return view('stock.delivery.entry.make_delivery',$data);
		}else{
			return redirect()->back();
		}
	}

	public function GetItem(Request $request)
	{
		try {
			
			$select = [
				'delivery_items.*',
				'items.cat_id',
				'items.code',
				'items.name',
				'items.alert_qty',
				'items.unit_stock',
				'items.unit_usage',
				'items.unit_purch',
				'items.cost_purch'
			];

			$OrderItem  = DeliveryItem::select($select)
						->leftJoin('items','delivery_items.item_id','items.id')
						->where('delivery_items.del_id',$request->po_id)
						->get();

			return response()->json($OrderItem,200); 

		} catch (\Exception $e) {
			return response()->json(['line'=>$e->getLine(),'message'=>$e->getMessage()],500);
		}
	}

	public function GetUnit(Request $request)
	{
		try {
			
			$Unit = Unit::where('to_code',$request->unit_stock)->get();

			return response()->json($Unit,200); 

		} catch (\Exception $e) {
			return response()->json(['message'=>$e->getMessage()],500);
		}
	}
	
	public function getDt(Request $request)
    {
		$pro_id = $request->session()->get('project');
        return Datatables::of(Delivery::where(['pro_id'=> $pro_id,'delete' => 0])->get())
		->addColumn('po_ref',function($row){
            if($order = Order::find($row->po_id)){
				return $order->ref_no;
			}
			return '';
        })
		->addColumn('supplier',function($row){
            if($supplier = Supplier::find($row->sup_id)){
				return $supplier->desc . " (" . $supplier->name . ")";
			}
			return '';
        })
		->addColumn('details_url',function($row){
            return url('stock/deliv/subdt').'/'.$row->id;
        })
		->addColumn('action', function ($row) {
			$rounte_delete = url('stock/deliv/delete/'.$row->id);
			$rounte_edit = url('stock/deliv/edit/'.encrypt($row->id));
			$routePrint = url('stock/deliv/printDelivery/'.($row->id));
			$btnEdit = 'onclick="onEdit(this)"';
			$btnDelete = 'onclick="onDelete(this)"';
			
			if(!hasRole('delivery_entry_edit')){
				$btnEdit = "disabled";
			}
			if(!hasRole('delivery_entry_delete')){
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
        return Datatables::of(DeliveryItem::where(['del_id' => $id])->get())
			->addColumn('warehouse',function($data){
				if($warehouse = Warehouse::find($data->warehouse_id)){
					return $warehouse->name;
				}
				return '';
			})
			->addColumn('item',function($data){
				if($item = Item::find($data->item_id)){
					return $item->name . " (" . $item->code .")";
				}
				return '';
			})
			->make(true);
	}

    public function save(Request $request){
		$rules = [
			'reference_no' 	=>'required|max:20|unique_delivery',
			'trans_date' 	=>'required|max:20',
			'supplier' 		=>'required|max:11',
			'po_no' 		=>'required|max:11',
		];
		// if(count($request['line_index']) > 0){
		// 	for($i=0;$i<count($request['line_index']);$i++){
		// 		$rules['line_index.'.$i]		= 'required';
		// 		$rules['line_item.'.$i]			= 'required|max:11';
		// 		$rules['line_unit.'.$i]			= 'required';
		// 		$rules['line_qty.'.$i]			= 'required';
		// 		$rules['line_warehouse.'.$i]	= 'required|max:11';
		// 	}
		// }
        Validator::make($request->all(),$rules)->validate();
		try {
			DB::beginTransaction();
			$originalDate = $request['trans_date'];
			$trans_date = date("Y-m-d", strtotime($originalDate));
			$data = [
				'pro_id'		=>$request->session()->get('project'),
				'po_id'			=>$request->po_no,
				'sup_id'		=>$request->supplier,
				'ref_no'		=>$request->reference_no,
				'trans_date'	=>$trans_date,
				'note'			=>$request->desc,
				'created_by'	=>Auth::user()->id,
				'created_at'	=>date('Y-m-d H:i:s'),
			];

			if($request->hasFile('photo')) {
				$photo = upload($request,'photo','assets/upload/picture/delivery/');
				$data = array_merge($data,['photo'=>$photo]);
			}

			if(!$id = DB::table('deliveries')->insertGetId($data)){
				print_r(new \Exception("Stock In not insert."));exit;
				throw new \Exception("Delivery[{$request->reference_no}] not create.");
			}
			// print_r(count($request['line_index']));exit;
			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){
					// $costArr = getItemCost($request['line_item'][$i],$request['line_unit'][$i],$request['line_qty'][$i],$request['line_cost'][$i]);
					$detail = [
						'del_id'         		=>	$id,
						'warehouse_id'   		=>	$request['line_warehouse'][$i],
						'line_no'        		=>	$request['line_index'][$i],
						'item_id'        		=>	$request['line_item'][$i],
						'unit'           		=>	$request['line_unit'][$i],
						'qty'            		=>	$request['line_qty'][$i],
						'price'          		=>	$request['line_cost'][$i],
						'desc'           		=>	$request['line_reference'][$i],
						'order_qty'		 		=>	$request['order_qty'][$i]
					];

					$stockIn = [
						'pro_id'         =>$request->session()->get('project'),
						'ref_id'         =>$id,
						'ref_no'         =>$request->reference_no,
						'ref_type'       =>'stock delivery',
						'line_no'        =>$request['line_index'][$i],
						'item_id'        =>$request['line_item'][$i],
						// 'unit'           =>$request['line_unit'][$i],
						// 'qty'            =>$request['line_qty'][$i],
						// 'remain_qty'            =>$request['line_qty'][$i],
						'warehouse_id'   =>$request['line_warehouse'][$i],
						'trans_date'     =>$trans_date,
						'trans_ref'      =>'I',
						'alloc_ref'      =>$request->reference_no,
						'reference'      =>$request['line_reference'][$i],
						'created_by'     =>Auth::user()->id,
						'created_at'     =>date('Y-m-d H:i:s'),
					];
					if (getSetting()->is_costing==1) {
						if ($request['line_cost'][$i]) {
							$costArr = getMinQTY($request['line_item'][$i],$request['line_unit'][$i],$request['line_qty'][$i],$request['line_cost'][$i]);
							if ($costArr) {
								$unit 	= $costArr['min_unit_to_code'];
								$qty 	= $costArr['qty'];
								$cost 	= $costArr['price'];
								$amount = $qty * $cost;
								$stockIn = array_merge($stockIn,['qty' => $qty]);
								$stockIn = array_merge($stockIn,['remain_qty' => $qty]);
								$stockIn = array_merge($stockIn,['unit' => $unit]);
								$stockIn = array_merge($stockIn,['cost' => $cost]);
								$stockIn = array_merge($stockIn,['amount' => $amount]);

								$detail = array_merge($detail,['unit_converted'=>$unit]);
								$detail = array_merge($detail,['qty_converted'=>$qty]);
								$detail = array_merge($detail,['price_converted'=>$cost]);
								$detail = array_merge($detail,['total_price_converted'=>$amount]);
								
							}
						}
					}
					// print_r($stockIn);
					// insert delivery items
					if(!$deliveryItemId = DB::table('delivery_items')->insertGetId($detail)){
						DB::rollback();
						print_r(new \Exception("Stock In[{$i}] not insert."));exit;
						throw new \Exception("DeliveryItem[{$i}] not insert.");
					}
					// insert stocks
					if(!$stockInId = DB::table('stocks')->insertGetId($stockIn)){
						DB::rollback();
						print_r(new \Exception("Stock In[{$i}] not insert."));exit;
						throw new \Exception("Stock In[{$i}] not insert.");
					}
					
					$unit_stock = '';
					$objItem = Item::where(['id'=>$request['line_item'][$i]])->first();
					if($objItem){
						$unit_stock = $objItem->unit_stock;
					}
					
					$po_unit = "";
					$po_deliv_qty = 0;
					$obj = OrderItem::where(['po_id'=>$request->po_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->first();
					if($obj){
						$po_unit = $obj->unit;
						$po_deliv_qty = $obj->deliv_qty;
					}
					
					$stock_qty_po = 0;
					$objUnit = Unit::where(['from_code'=>$po_unit,'to_code'=>$unit_stock])->first();
					if($objUnit){
						$stock_qty_po = $objUnit->factor;
					}
					
					$stock_qty_deliv = 0;
					$objUnit2 = Unit::where(['from_code'=>$request['line_unit'][$i],'to_code'=>$unit_stock])->first();
					if($objUnit2){
						$stock_qty_deliv = floatval($objUnit2->factor) * floatval($request['line_qty'][$i]);
					}
					
					$new_qty = (floatval($stock_qty_deliv) / floatval($stock_qty_po)) + floatval($po_deliv_qty);
					DB::table('order_items')->where(['po_id'=>$request->po_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->update(['deliv_qty'=>$new_qty]);
				}
				// exit;
			}
			DB::commit();
			if($request->btnSubmit==1){
				return redirect('stock/deliv')->with('success',trans('lang.save_success'));
			}
			print_r(new \Exception("Stock In not insert."));exit;
			return redirect()->back()->with('success',trans('lang.save_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
	}

    public function update(Request $request, $id)
    {
		// $rules = [
		// 	'reference_no' 	=>'required|max:20',
		// 	'trans_date' 	=>'required|max:20',
		// 	'supplier' 		=>'required|max:11',
		// 	'po_no' 		=>'required|max:11',
		// ];
		// if(count($request['line_index']) > 0){
		// 	for($i=0;$i<count($request['line_index']);$i++){
		// 		$rules['line_index.'.$i]		= 'required';
		// 		$rules['line_item.'.$i]			= 'required|max:11';
		// 		$rules['line_unit.'.$i]			= 'required';
		// 		$rules['line_qty.'.$i]			= 'required';
		// 		$rules['line_warehouse.'.$i]	= 'required|max:11';
		// 	}
		// }
        // Validator::make($request->all(),$rules)->validate();
		try {
			DB::beginTransaction();

			if(!$delivery = Delivery::find($id)){
				throw new \Exception("Delivery[{$id}] not found.");
			}

			if(checkAllocate("O", $request->reference_no) > 0){
				return redirect()->back()->with('error',trans('lang.trans_error'));
			}

			$originalDate = $request['trans_date'];
			$trans_date = date("Y-m-d", strtotime($originalDate));

			$data = [
				'sup_id'		=>$request->supplier,
				'trans_date'	=>$trans_date,
				'note'			=>$request->desc,
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];

			if($request->hasFile('photo')) {
				$photo = upload($request,'photo','assets/upload/picture/delivery/');
				$data = array_merge($data,['photo'=>$photo]);
			}

			// update delivery
			DB::table('deliveries')->where(['id'=>$id])->update($data);
			// delete all delivery items
			DB::table('delivery_items')->where('del_id',$id)->delete();
			// delete all delivery stocks
			DB::table('stocks')->where([
				'ref_id' => $id,
				'ref_no' => $delivery->ref_no,
				'ref_type' => 'stock delivery'
			])->delete();
			
			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){
					$detail = [
						'del_id'         =>$id,
						'warehouse_id'   =>$request['line_warehouse'][$i],
						'line_no'        =>$request['line_index'][$i],
						'item_id'        =>$request['line_item'][$i],
						'unit'           =>$request['line_unit'][$i],
						'qty'            =>$request['line_qty'][$i],
						'price'          =>$request['line_price'][$i],
						'desc'           =>$request['line_reference'][$i],
						'order_qty'		 =>$request['order_qty'][$i]
					];

					$stockIn = [
						'pro_id'         =>$request->session()->get('project'),
						'ref_id'         =>$id,
						'ref_no'         =>$request->reference_no,
						'ref_type'       =>'stock delivery',
						'line_no'        =>$request['line_index'][$i],
						'item_id'        =>$request['line_item'][$i],
						// 'unit'           =>$request['line_unit'][$i],
						// 'qty'            =>$request['line_qty'][$i],
						'warehouse_id'   =>$request['line_warehouse'][$i],
						'trans_date'     =>$trans_date,
						'trans_ref'      =>'I',
						'alloc_ref'      =>$request->reference_no,
						'reference'      =>$request['line_reference'][$i],
						'created_by'     =>Auth::user()->id,
						'created_at'     =>date('Y-m-d H:i:s'),
					];
					
					if (getSetting()->is_costing==1) {
						if ($request['line_cost'][$i]) {
							$costArr = getMinQTY($request['line_item'][$i],$request['line_unit'][$i],$request['line_qty'][$i],$request['line_cost'][$i]);
							if ($costArr) {
								$unit 	= $costArr['min_unit_to_code'];
								$qty 	= $costArr['qty'];
								$cost 	= $costArr['price'];
								$amount = $qty * $cost;
								$stockIn = array_merge($stockIn,['qty' => $qty]);
								$stockIn = array_merge($stockIn,['remain_qty' => $qty]);
								$stockIn = array_merge($stockIn,['unit' => $unit]);
								$stockIn = array_merge($stockIn,['cost' => $cost]);
								$stockIn = array_merge($stockIn,['amount' => $amount]);

								$detail = array_merge($detail,['unit_converted'=>$unit]);
								$detail = array_merge($detail,['qty_converted'=>$qty]);
								$detail = array_merge($detail,['price_converted'=>$cost]);
								$detail = array_merge($detail,['total_price_converted'=>$amount]);
								
							}
						}
					}

					// insert delivery items
					if(!$deliveryItemId = DB::table('delivery_items')->insertGetId($detail)){
						DB::rollback();
						throw new \Exception("Delivery Item[{$i}] not insert.");
					}
					// insert stock
					if(!$stockInId = DB::table('stocks')->insertGetId($stockIn)){
						DB::rollback();
						throw new \Exception("Stock In[{$i}] not insert.");
					}


					$unit_stock = '';
					$objItem = Item::where(['id'=>$request['line_item'][$i]])->first();
					if($objItem){
						$unit_stock = $objItem->unit_stock;
					}
					
					$po_unit = "";
					$po_deliv_qty = 0;
					$obj = OrderItem::where(['po_id'=>$request->po_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->first();
					if($obj){
						$po_unit = $obj->unit;
						$po_deliv_qty = $obj->deliv_qty;
					}
					
					$stock_qty_po = 0;
					$objUnit = Unit::where(['from_code'=>$po_unit,'to_code'=>$unit_stock])->first();
					if($objUnit){
						$stock_qty_po = $objUnit->factor;
					}
					
					$stock_qty_deliv = 0;
					$objUnit2 = Unit::where(['from_code'=>$request['line_unit'][$i],'to_code'=>$unit_stock])->first();
					if($objUnit2){
						$stock_qty_deliv = floatval($objUnit2->factor) * floatval($request['line_qty'][$i]);
					}
					
					$old_deliv_unit = "";
					$old_deliv_qty = 0;
					$objDeliv = DeliveryItem::where(['del_id'=>$id,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->first();
					if($objDeliv){
						$old_deliv_unit = $objDeliv->unit;
						$old_deliv_qty = $objDeliv->qty;
					}
					
					$old_deliv_stock_qty = 0;
					$objUnit3 = Unit::where(['from_code'=>$old_deliv_unit,'to_code'=>$unit_stock])->first();
					if($objUnit3){
						$old_deliv_stock_qty = floatval($objUnit3->factor) * floatval($old_deliv_qty);
					}
					
					$new_qty = (floatval($po_deliv_qty) - (floatval($old_deliv_stock_qty) / floatval($stock_qty_po))) + (floatval($stock_qty_deliv) / floatval($stock_qty_po));
					DB::table('order_items')->where(['po_id'=>$request->po_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->update(['deliv_qty'=>$new_qty]);
				}
			}

			DB::commit();
			if($request->btnSubmit==1){
				return redirect('stock/deliv')->with('success',trans('lang.save_success'));
			}
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
			$obj = Delivery::find($id);
			$objDeliv = DeliveryItem::where(['del_id'=>$id])->get();
			
			if(checkAllocate("O", $obj->ref_no) > 0){
				return redirect()->back()->with('error',trans('lang.trans_error'));
			}
			if(count($objDeliv) > 0){
				foreach($objDeliv as $row){
					$stock = [
						'delete'		=>1,
						'updated_by'	=>Auth::user()->id,
						'updated_at'	=>date('Y-m-d H:i:s'),
					];
					DB::table('stocks')->where(['ref_id'=>$id,'ref_no'=>$obj->ref_no,'ref_type'=>'stock delivery','line_no'=>$row['line_no'],'trans_ref'=>'I'])->update($stock);
					
					$unit_stock = '';
					$objItem = Item::where(['id'=>$row['item_id']])->first();
					if($objItem){
						$unit_stock = $objItem->unit_stock;
					}
					
					$po_unit = "";
					$po_deliv_qty = 0;
					$objOrd = OrderItem::where(['po_id'=>$obj->po_id,'line_no'=>$row['line_no'],'item_id'=>$row['item_id']])->first();
					if($objOrd){
						$po_unit = $objOrd->unit;
						$po_deliv_qty = $objOrd->deliv_qty;
					}
					
					$stock_qty_po = 0;
					$objUnit = Unit::where(['from_code'=>$po_unit,'to_code'=>$unit_stock])->first();
					if($objUnit){
						$stock_qty_po = $objUnit->factor;
					}
					
					$stock_qty_deliv = 0;
					$objUnit3 = Unit::where(['from_code'=>$row['unit'],'to_code'=>$unit_stock])->first();
					if($objUnit3){
						$stock_qty_deliv = floatval($objUnit3->factor) * floatval($row['qty']);
					}
									
					$new_qty = floatval($po_deliv_qty) - (floatval($stock_qty_deliv) / floatval($stock_qty_po));					
					DB::table('order_items')->where(['po_id'=>$obj->po_id,'line_no'=>$row['line_no'],'item_id'=>$row['item_id']])->update(['deliv_qty'=>$new_qty]);
				}
			}
			$data = [
				'delete'		=>1,
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];
			DB::table('deliveries')->where(['id'=>$id])->update($data);
			DB::commit();
			return redirect()->back()->with('success',trans('lang.delete_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.delete_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }
	
	public function getDeliveryDetails(Request $request){
		if($request->ajax()){
			$del_id = $request['del_id'];
			$objHead = Delivery::find($del_id);

			$select = [
				'delivery_items.*',
				'items.cat_id',
				'items.code',
				'items.name',
				'items.alert_qty',
				'items.unit_stock',
				'items.unit_usage',
				'items.unit_purch',
				'items.cost_purch',
				DB::raw("(pr_delivery_items.qty - pr_delivery_items.return_qty) AS qty"),
			];

			$objBody = DeliveryItem::select($select)
						->leftJoin('items','delivery_items.item_id','items.id')
						->where('delivery_items.del_id',$del_id)
						->whereRaw(DB::raw("(pr_delivery_items.qty - pr_delivery_items.return_qty) > ?"),[0])
						->get();

			if($objHead){
				$data = [
					'head' =>$objHead,					
					'body' =>$objBody,					
				];
				return $data;
			}
			return null;
		}
		return null;
	}

	public function downloadExcel(Request $request){
		try {
			Excel::create('import_delivery_' . date('Y_m_d_H_i_s'),function($excel) {
				$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
				$excel->sheet('Delivery Items',function($sheet){
					// Header
					$sheet->cell('A1', 'Date');
					$sheet->cell('B1', 'Warehouse Name');
					$sheet->cell('C1', 'Supplier Name');
					$sheet->cell('D1', 'PO No'); // allow null po
					$sheet->cell('E1', 'Invoice No'); // Check this invoice no for exists
					$sheet->cell('F1', 'Item Code');
					$sheet->cell('G1', 'Unit');
					$sheet->cell('H1', 'Price');
					$sheet->cell('I1', 'Qty');
					$sheet->cell('J1', 'Amount');
					$sheet->cell('K1', 'Method(W = Whole discount, I = Item discount)');
					$sheet->cell('L1', 'Discount(Fixed)');
					$sheet->cell('M1', 'Discount(%)');
					$sheet->cell('N1', 'Total');
					$sheet->cell('O1', 'Note');
					// Example Body
					$sheet->cell('A2', date('Y-m-d'));
					$sheet->cell('B2', 'Main-W');
					$sheet->cell('C2', 'Jonh Doe');
					$sheet->cell('D2', 'PO0001'); // allow null po
					$sheet->cell('E2', 'INV0001'); // Check this invoice no for exists
					$sheet->cell('F2', 'ITEM001');
					$sheet->cell('G2', 'Pcs');
					$sheet->cell('H2', 2);
					$sheet->cell('I2', 4);
					$sheet->cell('J2', 8);
					$sheet->cell('K2', 'I');
					$sheet->cell('L2', 0);
					$sheet->cell('M2', 0);
					$sheet->cell('N2', 8);
					$sheet->cell('O2', 'Example row to explain how to input data, and do not forget to delete this example row before you upload this file.');
				});
			})->download('xlsx');
		} catch (\Exception $ex) {
			return redirect('stock/deliv')->with("error", $ex->getMessage());
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

				if(empty($row->warehouse_name)){
					throw new \Exception("Column[B{$cellCount}] is empty");
				}

				if(!$warehouse = Warehouse::where('name',$row->warehouse_name)->first()){
					throw new \Exception("Column[B{$cellCount}] not found");
				}

				if(empty($row->supplier_name)){
					throw new \Exception("Column[C{$cellCount}] is empty");
				}

				if(empty($row->invoice_no)){
					throw new \Exception("Column[E{$cellCount}] is empty");
				}

				if(empty($row->item_code)){
					throw new \Exception("Column[F{$cellCount}] is empty");
				}

				if(!$item = Item::where('code',$row->item_code)->first()){
					throw new \Exception("Column[F{$cellCount}] item not found");
				}

				if(empty($row->unit)){
					throw new \Exception("Column[G{$cellCount}] is empty");
				}

				if(!$unit = Unit::where('from_desc',$row->unit)->first()){
					throw new \Exception("Column[G{$cellCount}] item not found");
				}

				if(empty($row->price)){
					throw new \Exception("Column[H{$cellCount}] is empty");
				}

				if(empty($row->qty)){
					throw new \Exception("Column[I{$cellCount}] is empty");
				}

				if(empty($row->amount)){
					throw new \Exception("Column[M{$cellCount}] is empty");
				}

				if(empty($row->methodw_whole_discount_i_item_discount)){
					throw new \Exception("Column[K{$cellCount}] is empty");
				}

				if($row->methodw_whole_discount_i_item_discount != "W" 
				&& $row->methodw_whole_discount_i_item_discount != "I"){
					throw new \Exception("Column[K{$cellCount}] was wrong character");
				}

				if(empty($row->total)){
					throw new \Exception("Column[N{$cellCount}] is empty");
				}

				$delivery = [
					'pro_id' => $pro_id,
					'ref_no' => $row->invoice_no,
					'trans_date' => $row->date,
					'created_by' => Auth::user()->id,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_by' => Auth::user()->id,
					'updated_at' => date('Y-m-d H:i:s'),
				];

				$delivery_item = [
					'line_no' => formatZero($cellCount, 3),
					'warehouse_id' => $warehouse->id,
					'item_id' => $item->id,
					'unit' => $row->unit,
					'qty' => $row->qty,
					'price' => $row->price,
					'amount' => $row->amount,
					'total' => $row->total,
				];

				$stock = [
					'line_no' 		=> formatZero($cellCount, 3),
					'pro_id' 		=> $pro_id,
					'ref_no' 		=> $row->invoice_no,
					'ref_type' 		=> 'stock delivery',
					'item_id' 		=> $item->id,
					'unit' 			=> $row->unit,
					'qty' 			=> $row->qty,
					'cost' 			=> $row->price,
					'amount' 		=> $row->price * $row->qty,
					'warehouse_id' 	=> $warehouse->id,
					'trans_date' 	=> $row->date,
					'trans_ref' 	=> 'I',
					'alloc_ref' 	=> $row->invoice_no,
					'created_by' 	=> Auth::user()->id,
					'created_at' 	=> date('Y-m-d H:i:s'),
					'updated_by' 	=> Auth::user()->id,
					'updated_at' 	=> date('Y-m-d H:i:s'),
				];

				if(!$supplier = Supplier::where('name',$row->supplier_name)->first()){
					$supplier_ = [
						'name' => $row->supplier_name,
						'desc' => $row->supplier_name,
						'tel' => "No Phone",
						'email' => "No Email",
						'address' => "No Address",
						'status' => 1,
						'created_by' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
					];

					if(!$supplierId = DB::table('suppliers')->insertGetId($supplier_)){
						DB::rollback();
						throw new \Exception("Supplier not created");
					}

					$delivery = array_merge($delivery,['sup_id' => $supplierId]);
				}else{
					$delivery = array_merge($delivery,['sup_id' => $supplier->id]);
				}

				if(!empty($row->po_no)){
					if(!$po = Order::where('ref_no',$row->po_no)->first()){
						$delivery = array_merge($delivery,['po_id' => 0]);
					}else{
						$delivery = array_merge($delivery,['po_id' => $po->id]);
					}
				}

				if(!empty($row->discountfixed)){
					if($row->methodw_whole_discount_i_item_discount == "W"){
						$delivery_item = array_merge($delivery_item,['disc_usd' => $row->discountfixed]);
					}else{
						$delivery_item = array_merge($delivery_item,['disc_usd' => $row->discountfixed]);
					}
				}

				if(!empty($row->discount)){
					if($row->methodw_whole_discount_i_item_discount == "W"){
						$delivery_item = array_merge($delivery_item,['disc_perc' => $row->discount]);
					}else{
						$delivery_item = array_merge($delivery_item,['disc_perc' => $row->discount]);
					}
				}

				if(!empty($row->note)){
					$delivery = array_merge($delivery,['note' => $row->note]);
				}

				if(!$exist_delivery = Delivery::where('ref_no',$row->invoice_no)->first()){
					if(!$deliveryId  = DB::table('deliveries')->insertGetId($delivery)){
						DB::rollback();
						throw new \Exception("Delivery not saved");
					}

					$delivery_item = array_merge($delivery_item,['del_id' => $deliveryId]);
					$stock =  array_merge($stock,['ref_id' => $deliveryId]);
				}else{
					$delivery_item = array_merge($delivery_item,['del_id' => $exist_delivery->id]);
					$stock =  array_merge($stock,['ref_id' => $exist_delivery->id]);
				}

				if(!$deliveryItemId = DB::table('delivery_items')->insertGetId($delivery_item)){
					DB::rollback();
					throw new \Exception("Delivery item not saved");
				}

				if(!$stockId = DB::table('stocks')->insertGetId($stock)){
					DB::rollback();
					throw new \Exception("Stock not saved");
				}
			}

			DB::commit();
			return redirect('stock/deliv')->with("success", trans('lang.upload_success'));
		} catch (\Exception $ex) {
			DB::rollback();
			return redirect('stock/deliv')->with("error", $ex->getMessage());
		}
	}

	public function add2(Request $request)
	{
		$data = [
			'title'			=> trans('lang.delivery'),
			'icon'			=> 'fa fa-plus',
			'small_title'	=> trans('lang.add_new'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
					'url' 		=> url('/'),
					'caption' 	=> trans('lang.home'),
				],
				'index'	=> [
					'url' 		=> url('stock/deliv'),
					'caption' 	=> trans('lang.delivery'),
				],
				'add'	=> [
					'caption' 	=> trans('lang.add_new'),
				],
			],
			'rounteSave'	=> url('stock/deliv/save'),
			'rounteBack'	=> url('stock/deliv'),
			'pro_id'		=> $request->session()->get('project'),
		];
		return view('stock.delivery.entry.add2',$data);
	}

	public function store2(Request $request)
	{

	}

	public function listOfOriginOrders(Request $request, $orderID){
		$projectID = $request->session()->get('project');
		$orderDetails = OrderItem::where(['order_items.po_id' => $orderID])->get();
		return Datatables::of($orderDetails)
				->addColumn('code',function($row){
					if($item = Item::find($row->item_id)){
						return $item->code;
					}
					return '';
				})
				->addColumn('name',function($row){
					if($item = Item::find($row->item_id)){
						return $item->name;
					}
					return '';
				})
				->addColumn('cat_id',function($row){
					if($item = Item::find($row->item_id)){
						return $item->cat_id;
					}
					return '';
				})
				->addColumn('unit_stock',function($row){
					if($item = Item::find($row->item_id)){
						return $item->unit_stock;
					}
					return '';
				})
				->addColumn('unit_usage',function($row){
					if($item = Item::find($row->item_id)){
						return $item->unit_usage;
					}
					return '';
				})
				->addColumn('unit_purch',function($row){
					if($item = Item::find($row->item_id)){
						return $item->unit_purch;
					}
					return '';
				})
				->addColumn('cost_purch',function($row){
					if($item = Item::find($row->item_id)){
						return $item->cost_purch;
					}
					return '';
				})
				->addColumn('photo',function($row){
					if($item = Item::find($row->item_id)){
						return $item->photo;
					}
					return '';
				})
				// delivery item
				->addColumn('delivery_qty',function($row){
					$deliveryItems = DeliveryItem::select([
							'delivery_items.item_id',
							'delivery_items.unit',
							DB::raw("SUM(qty) AS qty")
						])
						->leftJoin('deliveries','deliveries.id','delivery_items.del_id')
						->where('deliveries.po_id',$row->po_id)
						->where('delivery_items.item_id',$row->item_id)
						->groupBy(['delivery_items.item_id','delivery_items.unit'])
						->get();
					if(!empty($deliveryItems) && count($deliveryItems) > 0){
						$qtyTotal = 0;
						foreach($deliveryItems as $deliveryItem){
							if($unit = Unit::select(['factor'])->where(['to_code' => $row->unit, 'from_code' => $deliveryItem->unit])->first()){
								$qtyTotal += $deliveryItem->qty / $unit->factor;
							}else{
								$qtyTotal += $deliveryItem->qty;
							}
						}
						return $qtyTotal;
					}
					return 0;
				})
				->addColumn('on_hold',function($row){
					return 0;
				})
				->addColumn('can_delivery',function($row){
					return 0;
				})
				->make(true);
	}

	public function listOfDeliveriesByOrderID(Request $request, $orderID){
		$projectID = $request->session()->get('project');
		return Datatables::of(Delivery::where(['pro_id'=> $projectID,'po_id' => $orderID,'delete' => 0])->get())
		->addColumn('po_ref',function($row){
            if($order = Order::find($row->po_id)){
				return $order->ref_no;
			}
			return '';
        })
		->addColumn('supplier',function($row){
            if($supplier = Supplier::find($row->sup_id)){
				return $supplier->desc . " (" . $supplier->name . ")";
			}
			return '';
        })
		->addColumn('details_url',function($row){
            return url('stock/deliv/subdt').'/'.$row->id;
        })->make(true);
	}

	public function printDelivery(Request $request,$deliveryID = 0){
		$projectID = $request->session()->get('project');
		
		if(!$project = Project::find($projectID)){
			$project = false;
		}

		if(!$delivery = Delivery::find($deliveryID)){
			$delivery = false;
		}

		if(!$order = Order::find($delivery->po_id)){
			$order = false;
		}

		if(!$purchaseRequest = PurchaseRequest::find($order->pr_id)){
			$purchaseRequest = false;
		}

		if(!$warehouse = Warehouse::find($order->delivery_address)){
			$warehouse = false;
		}

		if(!$orderBy = User::find($order->ordered_by)){
			$orderBy = false;
		}

		if(!$createdBy = User::find($delivery->created_by)){
			$createdBy = false;
		}

		if(!$supplier = Supplier::find($delivery->sup_id)){
			$supplier = false;
		}

		if(!$department = SystemData::find($order->dep_id)){
			$department = false;
		}

		$columns = [
			'delivery_items.*',
			"items.code AS item_code",
			"items.name AS item_name",
			"system_datas.name AS item_type",
			"warehouses.name AS warehouse",
		];
		$deliveryItems = DeliveryItem::select($columns)
			->leftJoin('items','items.id','delivery_items.item_id')
			->leftJoin('system_datas','system_datas.id','items.cat_id')
			->leftJoin('warehouses','warehouses.id','delivery_items.warehouse_id')
			->where('delivery_items.del_id',$deliveryID)
			->get();

		if(count($deliveryItems) == 0){
			$deliveryItems = false;
		}

		$data = [
			'title' 		=> trans('lang.delivery_note'),
			'project'		=> $project,
			'order' 		=> $order,
			'createdBy' 	=> $createdBy,
			'purchaseRequest' => $purchaseRequest,
			'warehouse' 	=> $warehouse,
			'orderBy' 		=> $orderBy,
			'supplier' 		=> $supplier,
			'department' 	=> $department,
			'delivery'		=> $delivery,
			'deliveryItems' => $deliveryItems,
		];

		return view('stock.delivery.entry.print')->with($data);
	}
}
