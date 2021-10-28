<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Datatables;
use Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Excel;
use App\Model\Order;
use App\Model\Request as Requests;
use App\Model\Item;
use App\Model\RequestItem;
use App\Model\OrderItem;
use App\Model\Unit;
use App\User;
use App\Model\Warehouse;
use App\Model\Supplier;
use Exception;

class OrderController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
		$this->middleware('CheckProject');
    }

    public function GetRef(Request $request,$supplierID = 0)
    {
    	try {
    		$pro_id = Session::get('project');
			if($request->po_id){
				$Ref = Order::select(['orders.id',DB::raw("pr_orders.ref_no as text")])
						->where('orders.pro_id',$pro_id)
						->where('orders.trans_status',3)
						->orWhere('orders.id',$request->po_id)
						->groupBy('orders.id')
						->offset(0)
						->limit(10)
						->paginate(10);
			}elseif($supplierID > 0){
				$Ref = Order::select(['orders.id',DB::raw("pr_orders.ref_no as text")])
						->where('orders.pro_id',$pro_id)
						->where('orders.sup_id',$supplierID)
						->where('orders.trans_status',3)
						->groupBy('orders.id')
						->offset(0)
						->limit(10)
						->paginate(10);
			}else{
				$Ref = Order::select(['orders.id',DB::raw("pr_orders.ref_no as text")])
						->where('orders.pro_id',$pro_id)
						->where('orders.trans_status',3)
						->where('orders.ref_no','like','%'.($request->q).'%')
						->groupBy('orders.id')
						->offset(0)
						->limit(10)
						->paginate(10);
			}
			return response()->json($Ref,200);
    	} catch (\Exception $e) {
    		return response()->json(['id'=>$e->getLine(),'text'=>$e->getMessage()],200);
    	}
    }

    public function GetDetail(Request $request)
    {
    	try {
			
			$select = [
				'order_items.*',
				'items.cat_id',
				'items.code',
				'items.name',
				'items.alert_qty',
				'items.unit_stock',
				'items.unit_usage',
				'items.unit_purch',
				'items.cost_purch'
			];

			$OrderItem  = OrderItem::select($select)
						->leftJoin('items','order_items.item_id','items.id')
						->where('order_items.po_id',$request->id)
						->get();

			return response()->json($OrderItem,200); 

		} catch (\Exception $e) {
			return response()->json([],200);
		}
    }
	
    public function index(Request $request){
		$data = [
			'title'			=> trans('lang.order'),
			'icon'			=> 'fa fa-shopping-cart',
			'small_title'	=> trans('lang.order_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'dashboard'	=> [
						'caption' 	=> trans('lang.order'),
				],
			],			
			'rounte'			=> url("purch/order/dt"),
			'rounte_request'	=> url("purch/request/dt"),
			'rounteOrder'		=>	url('purch/order/makeOrder/'.$request->id),
		];
		
		if(hasRole('purchase_order_add')){
			$data = array_merge($data, ['rounteAdd'=> url('purch/order/add')]);
		}
		return view('purch.order.index',$data);
	}
	
	public function add($cid=NULL){
		$data = [
			'title'			=> trans('lang.order'),
			'icon'			=> 'fa fa-plus',
			'small_title'	=> trans('lang.add_new'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'index'	=> [
						'url' 		=> url('purch/order'),
						'caption' 	=> trans('lang.order'),
				],
				'add'	=> [
						'caption' 	=> trans('lang.add_new'),
				],
			],
			'rounteSave'	=> url('purch/order/save'),
			'rounteBack'	=> url('purch/order'),
		];
		if($cid){
			// $id = decrypt($cid);
			$id = $cid;
			$head = Order::find($id);
			$data = array_merge($data, ['head'=>$head]);
			$body = OrderItem::where(['po_id'=>$id])->get();
			$data = array_merge($data, ['body'=>$body]);
		}
		return view('purch.order.add',$data);
	}
	
	public function edit(Request $request, $id){
		$id = decrypt($id);
		$obj = Order::find($id);
		if($obj){
			$data = [
				'title'			=> trans('lang.order'),
				'icon'			=> 'fa fa-edit',
				'small_title'	=> trans('lang.edit'),
				'background'	=> '',
				'link'			=> [
					'home'	=> [
							'url' 		=> url('/'),
							'caption' 	=> trans('lang.home'),
					],
					'index'	=> [
							'url' 		=> url('purch/order'),
							'caption' 	=> trans('lang.order'),
					],
					'edit'	=> [
							'caption' 	=> trans('lang.edit'),
					],
				],
				'rounteSave'	=> url('purch/order/update/'.$id),
				'rounteBack'	=> url('purch/order'),
				'obj'	=> $obj,
			];
			return view('purch.order.edit',$data);
		}else{
			return redirect()->back();
		}
	}

	public function makeOrder(Request $request, $id){
		$id = $id;
		$obj = Requests::find($id);
		if($obj){
			$data = [
				'title'			=> trans('lang.order'),
				'icon'			=> 'fa fa-edit',
				'small_title'	=> trans('lang.edit'),
				'background'	=> '',
				'link'			=> [
					'home'	=> [
							'url' 		=> url('/'),
							'caption' 	=> trans('lang.home'),
					],
					'index'	=> [
							'url' 		=> url('purch/order'),
							'caption' 	=> trans('lang.order'),
					],
					'edit'	=> [
							'caption' 	=> trans('lang.edit'),
					],
				],
				'rounteSave'	=> url('purch/order/save'),
				'rounteBack'	=> url('purch/order'),
				'obj'	=> $obj,
			];
			return view('purch.order.make_order',$data);
		}else{
			return redirect()->back();
		}
	}
	
	public function getDt(Request $order)
    {
		$pro_id  = $order->session()->get('project');
		$dep_id  = Auth::user()->dep_id; 
		$user_id = Auth::user()->id;		
		$prefix  = DB::getTablePrefix();
		$columns = [
			'orders.*',
			DB::raw("(SELECT {$prefix}requests.`ref_no` FROM {$prefix}requests WHERE {$prefix}requests.`id` = {$prefix}orders.pr_id) AS pr_no"),
			DB::raw("(SELECT CONCAT({$prefix}warehouses.`address`, ' (',{$prefix}warehouses.`name`,')')AS `name` FROM {$prefix}warehouses WHERE {$prefix}warehouses.`id` = {$prefix}orders.delivery_address) AS warehouse"),
			DB::raw("(SELECT CONCAT({$prefix}suppliers.`desc`,' (',`{$prefix}suppliers`.`name`,')') AS `name` FROM {$prefix}suppliers WHERE {$prefix}suppliers.`id` = {$prefix}orders.sup_id) AS supplier"),
			DB::raw("(SELECT {$prefix}users.`name` FROM {$prefix}users WHERE {$prefix}users.`id` = {$prefix}orders.ordered_by) AS ordered_by"),
			DB::raw("(SELECT {$prefix}users.`name` FROM {$prefix}users WHERE {$prefix}users.`id` = {$prefix}orders.ordered_by) AS ordered_by"),
		];
		$orders  = Order::select($columns)->where('pro_id',$pro_id)->where('trans_status','>',0);
		if($order->only_active){
			$orders = $orders->where('is_closed',0);
		}
		if(!hasRole('admin')){
			$orders = $orders->where('dep_id',$dep_id)->orWhere('created_by',$user_id);
		}
		
		$orders = $orders->get();

		return Datatables::of($orders)
		->addColumn('details_url',function($row){
            return url('purch/order/subdt').'/'.$row->id;
        })
		->addColumn('action', function ($row) {
			$rounte_delete = url('purch/order/delete/'.$row->id);
			$rounte_edit = url('purch/order/edit/'.encrypt($row->id));
			$rounte_print = url('purch/order/print/'.encrypt($row->id));
			$rounte_view = url('purch/order/view/'.$row->id);
			$rounte_close = url('purch/order/close/'.$row->id);
			$rounte_copy = url('purch/order/copy/'.encrypt($row->id));
			$actionClose = 'disabled';
			$actionEdit = 'onclick="onEdit(this)"';
			$actionDelete = 'onclick="onDelete(this)"';
			$actionPrint = 'onclick="onPrint(this)"';
			$actionView = 'onclick="onView(this)"';
			$actionCopy = 'onclick="onCopy(this)"';
			
			if($row->trans_status==3){
				$obj = OrderItem::where(['po_id'=>$row->id])->get();
				if(count($obj) > 0){
					foreach($obj as $val){
						if(floatval($val->deliv_qty + $val->closed_qty)!=floatval($val->qty)){
							$actionClose = 'onclick="onClose(this)"';
						}
					}
				}
			}
			// print_r(hasRole('purchase_order_close'));
			if(!hasRole('purchase_order_edit')){
				$actionEdit = "disabled";
			}
			if(!hasRole('purchase_order_delete')){
				$actionDelete = "disabled";
			}
			if(!hasRole('purchase_order_print')){
				$actionPrint = "disabled";
			}
			if($row->trans_status != 3){
				$actionPrint = "disabled";
			}
			if(!hasRole('purchase_order_clone')){
				$actionCopy = "disabled";
			}
			if(!hasRole('purchase_order_view')){
				$actionView = "disabled";
			}
			if(!hasRole('purchase_order_close')){
				$actionClose = "disabled";
			}
			
			$btnEdit = 	'<a '.$actionEdit.' title="'.trans('lang.edit').'" class="btn btn-xs btn-warning edit-record" row_id="'.$row->id.'" row_rounte="'.$rounte_edit.'">'.
						'	<i class="fa fa-edit"></i>'.
						'</a>';
			$btnDelete= '<a '.$actionDelete.' title="'.trans('lang.delete').'" class="btn btn-xs btn-danger delete-record" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'">'.
						'	<i class="fa fa-trash"></i>'.
						'</a>';
			$btnPrint = '<a '.$actionPrint.' title="'.trans('lang.print').'" class="btn btn-xs btn-primary print-record" row_id="'.$row->id.'" row_rounte="'.$rounte_print.'">'.
						'	<i class="fa fa-print"></i>'.
						'</a>';
			$btnView  = '<a '.$actionView.' title="'.trans('lang.view').'" class="btn btn-xs btn-info view-record" row_id="'.$row->id.'" row_rounte="'.$rounte_view.'">'.
						'	<i class="fa fa-eye"></i>'.
						'</a>';
			$btnClose = '<a '.$actionClose.' title="'.trans('lang.close').'" class="btn btn-xs btn-danger close-record" row_id="'.$row->id.'" row_rounte="'.$rounte_close.'">'.
						'	<i class="fa fa-ban"></i>'.
						'</a>';
			$btnCopy =  '<a '.$actionCopy.' title="'.trans('lang.clone').'" class="btn btn-xs btn-success copy-record" row_id="'.$row->id.'" row_rounte="'.$rounte_copy.'">'.
						'	<i class="fa fa-clone"></i>'.
						'</a>';
			if($row->trans_status==1){
				return $btnPrint.$btnEdit.$btnDelete;
			}else if($row->trans_status==2){
				return $btnPrint.$btnView.$btnDelete;
			}else if($row->trans_status==3){
				return $btnPrint.$btnView.$btnClose;
			}else if($row->trans_status==5){
				return $btnPrint.$btnEdit.$btnDelete;
			}else{
				return $btnView.$btnCopy.$btnDelete;
			}
       })->make(true);
    }
	
	public function subDt(Request $order, $id)
    {
		$prefix = DB::getTablePrefix();
		$columns    = [
			'order_items.*',
			DB::raw("(SELECT CONCAT({$prefix}items.`name`, ' (', {$prefix}items.`code`, ')') AS `name` FROM {$prefix}items WHERE {$prefix}items.`id` = {$prefix}order_items.`item_id`) AS item"),
		];
		$orderItems = OrderItem::select($columns)->where('po_id',$id)->get();
		return Datatables::of($orderItems)->make(true);
	}

    public function save(Request $request){
		$rules = [
			'reference_no' 		=>'required|max:20|unique_order',
			'trans_date' 		=>'required|max:20',
			'delivery_date'		=>'required|max:20',
			'delivery_address'	=>'required|max:11',
			'supplier'			=>'required|max:11',
			'ordered_by'		=>'required|max:11',
			'sub_total'			=>'required',
			'disc_perc'			=>'required',
			'disc_usd'			=>'required',
			'grand_total'		=>'required',
			'fee_charge'		=>'required',
			'deposit'			=>'required',
		];
		if(count($request['line_index']) > 0){
			for($i=0;$i<count($request['line_index']);$i++){
				$rules['line_index.'.$i]		= 'required';
				$rules['line_item.'.$i]			= 'required|max:11';
				$rules['line_unit.'.$i]			= 'required';
				$rules['line_qty.'.$i]			= 'required';
				$rules['line_price.'.$i]		= 'required';
				$rules['line_amount.'.$i]		= 'required';
				$rules['line_disc_perc.'.$i]	= 'required';
				$rules['line_disc_usd.'.$i]		= 'required';
				$rules['line_grend_total.'.$i]	= 'required';
			}
		}
		
		// Validator::make($request->all(),$rules)->validate();
		// exit;
		try {
			DB::beginTransaction();
			$trans_date = date("Y-m-d", strtotime($request->trans_date));
			$delivery_date = date("Y-m-d", strtotime($request->delivery_date));
			$data = [
				'pro_id'			=>$request->session()->get('project'),
				'pr_id'				=>$request->pr_no,
				'dep_id'			=>Auth::user()->dep_id,
				'sup_id'			=>$request->supplier,
				'ref_no'			=>$request->reference_no,
				'trans_date'		=>$trans_date,
				'delivery_address'	=>$request->delivery_address,
				'delivery_date'		=>$delivery_date,
				'sub_total'			=>$request->sub_total,
				'disc_perc'			=>$request->disc_perc,
				'disc_usd'			=>$request->disc_usd,
				'grand_total'		=>$request->grand_total,
				'fee_charge'		=>$request->fee_charge,
				'deposit'			=>$request->deposit,
				'ordered_by'		=>$request->ordered_by,
				'trans_status'		=>$request->status ? $request->status : 1,
				'note'				=>trim($request->desc),
				'term_pay'			=>trim($request->term_pay),
				'created_by'		=>Auth::user()->id,
				'created_at'		=>date('Y-m-d H:i:s'),
				'updated_by'		=>Auth::user()->id,
				'updated_at'		=>date('Y-m-d H:i:s'),
			];

			if(Auth::user()->dep_id == 0){
				if(!empty($request->ordered_by)){
					if($orderedBy = User::find($request->ordered_by)){
						$data = array_merge($data,['dep_id' => $orderedBy->dep_id]);
					}
				}else{
					if(!empty($request->pr_no)){
						if($purchaseRequest = Requests::find($request->pr_no)){
							$data = array_merge($data,['dep_id' => $purchaseRequest->dep_id]);
						}
					}
				}
			}

			if(!$id = DB::table('orders')->insertGetId($data)){
				throw new \Exception("Order not insert");
			}
			// exit;
			$is_remain_qty = 1;
			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){
					$details[] = [
						'po_id'			=>$id,
						'line_no'		=>$request['line_index'][$i],
						'item_id'		=>$request['line_item'][$i],
						'unit'			=>$request['line_unit'][$i],
						'qty'			=>$request['line_qty'][$i],
						'price'			=>$request['line_price'][$i],
						'amount'		=>$request['line_amount'][$i],
						'disc_perc'		=>$request['line_disc_perc'][$i],
						'disc_usd'		=>$request['line_disc_usd'][$i],
						'total'			=>$request['line_grend_total'][$i],
						'desc'			=>$request['line_reference'][$i],
					];
					
					$unit_stock = '';
					$objItem = Item::where(['id'=>$request['line_item'][$i]])->first();
					if($objItem){
						$unit_stock = $objItem->unit_stock;
					}
					
					$pr_unit = "";
					$pr_ordered_qty = 0;
					$obj = RequestItem::where(['pr_id'=>$request->pr_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->first();
					if($obj){
						$pr_unit = $obj->unit;
						$pr_ordered_qty = $obj->ordered_qty;
					}
					
					$stock_qty_pr = 0;
					$objUnit1 = Unit::where(['from_code'=>$pr_unit,'to_code'=>$unit_stock])->first();
					if($objUnit1){
						$stock_qty_pr = $objUnit1->factor;
					}
					
					$stock_qty_po = 0;
					$objUnit2 = Unit::where(['from_code'=>$request['line_unit'][$i],'to_code'=>$unit_stock])->first();
					if($objUnit2){
						$stock_qty_po = floatval($objUnit2->factor) * floatval($request['line_qty'][$i]);
					}
					
					$new_qty = (floatval($stock_qty_po) / floatval($stock_qty_pr)) + floatval($pr_ordered_qty);
					
					DB::table('request_items')->where(['pr_id'=>$request->pr_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->update(['ordered_qty'=>$new_qty]);
					$remainItem = RequestItem::where(['pr_id'=>$request->pr_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->first();
					// print_r($remainItem->ordered_qty+$remainItem->closed_qty."-".$remainItem->qty."=".(($remainItem->ordered_qty + $remainItem->closed_qty) - $remainItem->qty."<br />"));
					if((($remainItem->ordered_qty + $remainItem->closed_qty) - $remainItem->qty) <= 0){
						$is_remain_qty = 0;
					}
				}
			}
			if($is_remain_qty == 0){
				DB::table("requests")->where('id',$request->pr_no)->update(["is_ordered"=>1]);
			}
			
			DB::table('order_items')->insert($details);
			DB::commit();
			if($request->btnSubmit==1){
				return redirect('purch/order')->with('success',trans('lang.save_success'));
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
			'reference_no' 		=>'required|max:20',
			'trans_date' 		=>'required|max:20',
			'delivery_date'		=>'required|max:20',
			'delivery_address'	=>'required|max:11',
			'supplier'			=>'required|max:11',
			'ordered_by'		=>'required|max:11',
			'sub_total'			=>'required',
			'disc_perc'			=>'required',
			'disc_usd'			=>'required',
			'grand_total'		=>'required',
			'fee_charge'		=>'required',
			'deposit'			=>'required',
		];
		if(count($request['line_index']) > 0){
			for($i=0;$i<count($request['line_index']);$i++){
				$rules['line_index.'.$i]		= 'required';
				$rules['line_item.'.$i]			= 'required|max:11';
				$rules['line_unit.'.$i]			= 'required';
				$rules['line_qty.'.$i]			= 'required';
				$rules['line_price.'.$i]		= 'required';
				$rules['line_amount.'.$i]		= 'required';
				$rules['line_disc_perc.'.$i]	= 'required';
				$rules['line_disc_usd.'.$i]		= 'required';
				$rules['line_grend_total.'.$i]	= 'required';
			}
		}
        Validator::make($request->all(),$rules)->validate();
		try {
			DB::beginTransaction();
			$trans_date = date("Y-m-d", strtotime($request->trans_date));
			$delivery_date = date("Y-m-d", strtotime($request->delivery_date));
			$data = [
				'sup_id'			=>$request->supplier,
				'ref_no'			=>$request->reference_no,
				'trans_date'		=>$trans_date,
				'delivery_address'	=>$request->delivery_address,
				'delivery_date'		=>$delivery_date,
				'sub_total'			=>$request->sub_total,
				'disc_perc'			=>$request->disc_perc,
				'disc_usd'			=>$request->disc_usd,
				'fee_charge'		=>$request->fee_charge,
				'deposit'			=>$request->deposit,
				'grand_total'		=>$request->grand_total,
				'ordered_by'		=>$request->ordered_by,
				'trans_status'		=>$request->status ? $request->status : 1,
				'note'				=>trim($request->desc),
				'term_pay'			=>trim($request->term_pay),
				'updated_by'		=>Auth::user()->id,
				'updated_at'		=>date('Y-m-d H:i:s'),
			];

			if(Auth::user()->dep_id == 0){
				if(!empty($request->ordered_by)){
					if($orderedBy = User::find($request->ordered_by)){
						$data = array_merge($data,['dep_id' => $orderedBy->dep_id]);
					}
				}else{
					if(!empty($request->pr_no)){
						if($purchaseRequest = Requests::find($request->pr_no)){
							$data = array_merge($data,['dep_id' => $purchaseRequest->dep_id]);
						}
					}
				}
			}

			DB::table('orders')->where(['id'=>$id])->update($data);
			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){
					$details = [
						'unit'			=>$request['line_unit'][$i],
						'qty'			=>$request['line_qty'][$i],
						'price'			=>$request['line_price'][$i],
						'amount'		=>$request['line_amount'][$i],
						'disc_perc'		=>$request['line_disc_perc'][$i],
						'disc_usd'		=>$request['line_disc_usd'][$i],
						'total'			=>$request['line_grend_total'][$i],
						'desc'			=>$request['line_reference'][$i],
					];
					
					$unit_stock = '';
					$objItem = Item::where(['id'=>$request['line_item'][$i]])->first();
					if($objItem){
						$unit_stock = $objItem->unit_stock;
					}
					
					$pr_unit = "";
					$pr_ordered_qty = 0;
					$objPR = RequestItem::where(['pr_id'=>$request->pr_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->first();
					if($objPR){
						$pr_unit = $objPR->unit;
						$pr_ordered_qty = $objPR->ordered_qty;
					}
					
					$pr_stock_qty = 0;
					$objUnit2 = Unit::where(['from_code'=>$pr_unit,'to_code'=>$unit_stock])->first();
					if($objUnit2){
						$pr_stock_qty = floatval($objUnit2->factor);
					}
					
					$stock_qty_po = 0;
					$objUnit3 = Unit::where(['from_code'=>$request['line_unit'][$i],'to_code'=>$unit_stock])->first();
					if($objUnit3){
						$stock_qty_po = floatval($objUnit3->factor) * floatval($request['line_qty'][$i]);
					}
					
					$old_po_unit = "";
					$old_po_qty = 0;
					$objPO = OrderItem::where(['po_id'=>$id,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->first();
					if($objPO){
						$old_po_unit = $objPO->unit;
						$old_po_qty = $objPO->qty;
					}
					
					$old_po_stock_qty = 0;
					$objUnit1 = Unit::where(['from_code'=>$old_po_unit,'to_code'=>$unit_stock])->first();
					if($objUnit1){
						$old_po_stock_qty = floatval($objUnit1->factor) * floatval($old_po_qty);
					}
					
					$new_qty = (floatval($pr_ordered_qty) - (floatval($old_po_stock_qty) / floatval($pr_stock_qty))) + (floatval($stock_qty_po) / floatval($pr_stock_qty));
					DB::table('request_items')->where(['pr_id'=>$request->pr_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->update(['ordered_qty'=>$new_qty]);
					
					DB::table('order_items')->where(['po_id'=>$id,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->update($details);
				}
			}
			DB::commit();
			return redirect('purch/order')->with('success',trans('lang.save_success'));
			// return redirect()->back()->with('success',trans('lang.update_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.update_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }
	
	public function destroy($id)
    {
		try {
			DB::beginTransaction();
			$obj = Order::find($id);
			$objPO = OrderItem::where(['po_id'=>$id])->get();
			if(count($objPO) > 0){
				foreach($objPO as $row){
					
					$unit_stock = '';
					$objItem = Item::where(['id'=>$row['item_id']])->first();
					if($objItem){
						$unit_stock = $objItem->unit_stock;
					}
					
					$pr_unit = "";
					$pr_ordered_qty = 0;
					$objPR = RequestItem::where(['pr_id'=>$obj->pr_id,'line_no'=>$row['line_no'],'item_id'=>$row['item_id']])->first();
					if($objPR){
						$pr_unit = $objPR->unit;
						$pr_ordered_qty = $objPR->ordered_qty;
					}
					
					$pr_stock_qty = 0;
					$objUnit2 = Unit::where(['from_code'=>$pr_unit,'to_code'=>$unit_stock])->first();
					if($objUnit2){
						$pr_stock_qty = floatval($objUnit2->factor);
					}
					
					$stock_qty_po = 0;
					$objUnit3 = Unit::where(['from_code'=>$row['unit'],'to_code'=>$unit_stock])->first();
					if($objUnit3){
						$stock_qty_po = floatval($objUnit3->factor) * floatval($row['qty']);
					}
									
					$new_qty = floatval($pr_ordered_qty) - (floatval($stock_qty_po) / floatval($pr_stock_qty));					
					DB::table('request_items')->where(['pr_id'=>$obj->pr_id,'line_no'=>$row['line_no'],'item_id'=>$row['item_id']])->update(['ordered_qty'=>$new_qty]);
				}
			}
			
			$data = [
				'trans_status'	=>0,
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];
			DB::table('orders')->where(['id'=>$id])->update($data);
			DB::commit();
			return redirect()->back()->with('success',trans('lang.delete_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.delete_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }
	
	public function close($id,$make_order=null)
    {
		try {
			
			DB::beginTransaction();
			$obj = Order::find($id);
			$arrObj = OrderItem::where(['po_id'=>$id])->get();
			if(count($arrObj) > 0){
				
				foreach($arrObj as $row){
						
					$unit_stock = '';
					$objItem = Item::where(['id'=>$row['item_id']])->first();
					if($objItem){
						$unit_stock = $objItem->unit_stock;
					}
					
					$pr_unit = "";
					$pr_ordered_qty = 0;
					$objPR = RequestItem::where(['pr_id'=>$obj->pr_id,'line_no'=>$row['line_no'],'item_id'=>$row['item_id']])->first();
					if($objPR){
						$pr_unit = $objPR->unit;
						$pr_ordered_qty = $objPR->ordered_qty;
					}
					
					$pr_stock_qty = 0;
					$objUnit2 = Unit::where(['from_code'=>$pr_unit,'to_code'=>$unit_stock])->first();
					if($objUnit2){
						$pr_stock_qty = floatval($objUnit2->factor);
					}
					
					$stock_qty_po = 0;
					$objUnit3 = Unit::where(['from_code'=>$row['unit'],'to_code'=>$unit_stock])->first();
					if($objUnit3){
						$stock_qty_po = floatval($objUnit3->factor) * (floatval($row->qty) - floatval($row->deliv_qty));
					}
									
					$new_qty = floatval($pr_ordered_qty) - (floatval($stock_qty_po) / floatval($pr_stock_qty));					
					DB::table('request_items')->where(['pr_id'=>$obj->pr_id,'line_no'=>$row['line_no'],'item_id'=>$row['item_id']])->update(['ordered_qty'=>$new_qty]);
				
					DB::table('order_items')->where(['id'=>$row->id])->update(['closed_qty'=>(floatval($row->qty) - floatval($row->deliv_qty))]);
				}
				DB::table('orders')->where('orders.id',$id)->update(['is_closed'=>1]);
			}
			DB::commit();
			if($make_order){
				// return redirect()->route('order/addFromClose', ['id' => $id]);
				return redirect('purch/order?id='.$obj->pr_id)->with('status', 'An activation link was send to your email address.');
			}
			return redirect()->back()->with('success',trans('lang.close_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.close_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }
	public function addFromClose($cid=NULL,$order_id=null){
		$id = $cid;
		$obj = Requests::find($id);
		if($obj){
			$data = [
				'title'			=> trans('lang.order'),
				'icon'			=> 'fa fa-edit',
				'small_title'	=> trans('lang.edit'),
				'background'	=> '',
				'link'			=> [
					'home'	=> [
							'url' 		=> url('/'),
							'caption' 	=> trans('lang.home'),
					],
					'index'	=> [
							'url' 		=> url('purch/order'),
							'caption' 	=> trans('lang.order'),
					],
					'edit'	=> [
							'caption' 	=> trans('lang.edit'),
					],
				],
				'rounteSave'	=> url('purch/order/saveCloseOrder/'.$id),
				'rounteBack'	=> url('purch/order'),
				'obj'	=> $obj,
			];
			return view('purch.order.make_order',$data);
		}else{
			return redirect()->back();
		}
	}

	public function saveCloseOrder(Request $request,$id){
		try{
			DB::beginTransaction();
			$rules = [
				'reference_no' 		=>'required|max:20|unique_order',
				'trans_date' 		=>'required|max:20',
				'delivery_date'		=>'required|max:20',
				'delivery_address'	=>'required|max:11',
				'supplier'			=>'required|max:11',
				'ordered_by'		=>'required|max:11',
				'sub_total'			=>'required',
				'disc_perc'			=>'required',
				'disc_usd'			=>'required',
				'grand_total'		=>'required',
				'fee_charge'		=>'required',
				'deposit'			=>'required',
			];
			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){
					$rules['line_index.'.$i]		= 'required';
					$rules['line_item.'.$i]			= 'required|max:11';
					$rules['line_unit.'.$i]			= 'required';
					$rules['line_qty.'.$i]			= 'required';
					$rules['line_price.'.$i]		= 'required';
					$rules['line_amount.'.$i]		= 'required';
					$rules['line_disc_perc.'.$i]	= 'required';
					$rules['line_disc_usd.'.$i]		= 'required';
					$rules['line_grend_total.'.$i]	= 'required';
				}
			}
			$obj = Order::find($id);
			$arrObj = OrderItem::where(['po_id'=>$id])->get();
			if(count($arrObj) > 0){
				foreach($arrObj as $row){
						
					$unit_stock = '';
					$objItem = Item::where(['id'=>$row['item_id']])->first();
					if($objItem){
						$unit_stock = $objItem->unit_stock;
					}
					
					$pr_unit = "";
					$pr_ordered_qty = 0;
					$objPR = RequestItem::where(['pr_id'=>$obj->pr_id,'line_no'=>$row['line_no'],'item_id'=>$row['item_id']])->first();
					if($objPR){
						$pr_unit = $objPR->unit;
						$pr_ordered_qty = $objPR->ordered_qty;
					}
					
					$pr_stock_qty = 0;
					$objUnit2 = Unit::where(['from_code'=>$pr_unit,'to_code'=>$unit_stock])->first();
					if($objUnit2){
						$pr_stock_qty = floatval($objUnit2->factor);
					}
					
					$stock_qty_po = 0;
					$objUnit3 = Unit::where(['from_code'=>$row['unit'],'to_code'=>$unit_stock])->first();
					if($objUnit3){
						$stock_qty_po = floatval($objUnit3->factor) * (floatval($row->qty) - floatval($row->deliv_qty));
					}
									
					$new_qty = floatval($pr_ordered_qty) - (floatval($stock_qty_po) / floatval($pr_stock_qty));					
					DB::table('request_items')->where(['pr_id'=>$obj->pr_id,'line_no'=>$row['line_no'],'item_id'=>$row['item_id']])->update(['ordered_qty'=>$new_qty]);
				
					DB::table('order_items')->where(['id'=>$row->id])->update(['closed_qty'=>(floatval($row->qty) - floatval($row->deliv_qty))]);
				}
			}
			$trans_date = date("Y-m-d", strtotime($request->trans_date));
			$delivery_date = date("Y-m-d", strtotime($request->delivery_date));
			$data = [
				'pro_id'			=>$request->session()->get('project'),
				'pr_id'				=>$request->pr_no,
				'dep_id'			=>Auth::user()->dep_id,
				'sup_id'			=>$request->supplier,
				'ref_no'			=>$request->reference_no,
				'trans_date'		=>$trans_date,
				'delivery_address'	=>$request->delivery_address,
				'delivery_date'		=>$delivery_date,
				'sub_total'			=>$request->sub_total,
				'disc_perc'			=>$request->disc_perc,
				'disc_usd'			=>$request->disc_usd,
				'grand_total'		=>$request->grand_total,
				'fee_charge'		=>$request->fee_charge,
				'deposit'			=>$request->deposit,
				'ordered_by'		=>$request->ordered_by,
				'trans_status'		=>$request->status ? $request->status : 1,
				'note'				=>trim($request->desc),
				'term_pay'			=>trim($request->term_pay),
				'created_by'		=>Auth::user()->id,
				'created_at'		=>date('Y-m-d H:i:s'),
				'updated_by'		=>Auth::user()->id,
				'updated_at'		=>date('Y-m-d H:i:s'),
			];

			if(Auth::user()->dep_id == 0){
				if(!empty($request->ordered_by)){
					if($orderedBy = User::find($request->ordered_by)){
						$data = array_merge($data,['dep_id' => $orderedBy->dep_id]);
					}
				}else{
					if(!empty($request->pr_no)){
						if($purchaseRequest = Requests::find($request->pr_no)){
							$data = array_merge($data,['dep_id' => $purchaseRequest->dep_id]);
						}
					}
				}
			}
			if(!$id = DB::table('orders')->insertGetId($data)){
				throw new \Exception("Order not insert");
			}
			// exit;
			$is_remain_qty = 1;
			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){
					$details[] = [
						'po_id'			=>$id,
						'line_no'		=>$request['line_index'][$i],
						'item_id'		=>$request['line_item'][$i],
						'unit'			=>$request['line_unit'][$i],
						'qty'			=>$request['line_qty'][$i],
						'price'			=>$request['line_price'][$i],
						'amount'		=>$request['line_amount'][$i],
						'disc_perc'		=>$request['line_disc_perc'][$i],
						'disc_usd'		=>$request['line_disc_usd'][$i],
						'total'			=>$request['line_grend_total'][$i],
						'desc'			=>$request['line_reference'][$i],
					];
					
					$unit_stock = '';
					$objItem = Item::where(['id'=>$request['line_item'][$i]])->first();
					if($objItem){
						$unit_stock = $objItem->unit_stock;
					}
					
					$pr_unit = "";
					$pr_ordered_qty = 0;
					$obj = RequestItem::where(['pr_id'=>$request->pr_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->first();
					if($obj){
						$pr_unit = $obj->unit;
						$pr_ordered_qty = $obj->ordered_qty;
					}
					
					$stock_qty_pr = 0;
					$objUnit1 = Unit::where(['from_code'=>$pr_unit,'to_code'=>$unit_stock])->first();
					if($objUnit1){
						$stock_qty_pr = $objUnit1->factor;
					}
					
					$stock_qty_po = 0;
					$objUnit2 = Unit::where(['from_code'=>$request['line_unit'][$i],'to_code'=>$unit_stock])->first();
					if($objUnit2){
						$stock_qty_po = floatval($objUnit2->factor) * floatval($request['line_qty'][$i]);
					}
					
					$new_qty = (floatval($stock_qty_po) / floatval($stock_qty_pr)) + floatval($pr_ordered_qty);
					DB::table('request_items')->where(['pr_id'=>$request->pr_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->update(['ordered_qty'=>$new_qty]);
					$remainItem = RequestItem::where(['pr_id'=>$request->pr_no,'line_no'=>$request['line_index'][$i],'item_id'=>$request['line_item'][$i]])->first();
					// print_r($remainItem->ordered_qty+$remainItem->closed_qty."-".$remainItem->qty."=".(($remainItem->ordered_qty + $remainItem->closed_qty) - $remainItem->qty."<br />"));
					if((($remainItem->ordered_qty + $remainItem->closed_qty) - $remainItem->qty) <= 0){
						$is_remain_qty = 0;
					}
				}
			}
			if($is_remain_qty == 0){
				DB::table("requests")->where('id',$request->pr_no)->update(["is_ordered"=>1]);
			}
			DB::table('order_items')->insert($details);
			DB::commit();
			if($request->btnSubmit==1){
				return redirect('purch/order')->with('success',trans('lang.save_success'));
			}
			return redirect()->back()->with('success',trans('lang.save_success'));
			
		}catch(Exception $e){
			DB::rollback();
		}
	}
	
	public function getStepApprove(Request $request ,$id){
		$pro_id = $request->session()->get('project');
		$sql = "CALL POApprovalStep({$pro_id},{$id});";
		$obj = DB::select($sql);		
		return $obj;
	}
	
	public function getItemBOQ(Request $order){
		if($order->ajax()){
			$pro_id = $order->session()->get('project');
			$item_id = $order['item_id'];
			$unit = $order['unit'];
			
			$boq_set = -1;
			$price = 0;
			$order_qty = 0;
			$return_qty = 0;
			
			$sql_boq = "SELECT F.item_id, (F.stock_qty / F.boq_qty) AS boq_qty, F.price FROM (SELECT E.item_id, E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS boq_qty, (SELECT pr_items.`cost_purch` FROM pr_items WHERE pr_items.`id`=E.item_id)AS `price` FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_boq_items.`id`, pr_boq_items.`house_id`, pr_boq_items.`item_id`, pr_boq_items.`unit`, (pr_boq_items.`qty_std` + pr_boq_items.`qty_add` ) AS qty FROM pr_boq_items WHERE pr_boq_items.`item_id` = $item_id) AS A WHERE A.`house_id` IN (SELECT pr_houses.`id` FROM pr_houses WHERE pr_houses.`pro_id` = $pro_id)) AS B) AS C) AS D GROUP BY D.item_id, D.unit_stock) AS E) AS F";
			$objBOQ = collect(DB::select($sql_boq))->first();
			if($objBOQ){
				$boq_set = floatval($objBOQ->boq_qty);
				$price = floatval($objBOQ->price);
				
				$sql_order = "SELECT (H.stock_qty / H.order_qty) AS order_qty FROM (SELECT G.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = G.unit_stock) AS order_qty FROM (SELECT F.item_id, F.unit_stock, SUM(F.stock_qty) AS stock_qty FROM (SELECT E.item_id, E.unit_stock, (E.qty * E.stock_qty) AS stock_qty FROM (SELECT D.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = D.unit AND pr_units.`to_code` = D.unit_stock) AS stock_qty FROM (SELECT C.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = C.item_id) AS unit_stock FROM (SELECT B.item_id, B.unit, B.qty FROM (SELECT pr_orders.`id` FROM pr_orders WHERE pr_orders.`pro_id` = $pro_id AND pr_orders.`trans_status` IN (1, 2, 3)) AS A INNER JOIN (SELECT pr_order_items.`pr_id`, pr_order_items.item_id, pr_order_items.`unit`, (pr_order_items.`qty` - pr_order_items.`closed_qty` ) AS qty FROM pr_order_items WHERE pr_order_items.`item_id` = $item_id) AS B ON A.`id` = B.pr_id) AS C) AS D) AS E) AS F GROUP BY F.item_id, F.unit_stock) AS G) AS H";
				$objRequest = collect(DB::select($sql_order))->first();
				if($objRequest){
					$order_qty = floatval($objRequest->order_qty);
				}
				
				$sql_return = "SELECT (H.stock_qty / H.return_qty) AS return_qty FROM (SELECT G.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = G.unit_stock) AS return_qty FROM (SELECT F.item_id, F.unit_stock, SUM(F.stock_qty) AS stock_qty FROM (SELECT E.item_id, E.unit_stock, (E.qty * E.stock_qty) AS stock_qty FROM (SELECT D.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = D.unit AND pr_units.`to_code` = D.unit_stock) AS stock_qty FROM (SELECT C.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = C.item_id) AS unit_stock FROM (SELECT B.item_id, B.unit, B.qty FROM (SELECT pr_return_delivery.`id` FROM pr_return_delivery WHERE pr_return_delivery.`pro_id` = $pro_id AND pr_return_delivery.`delete` = 0) AS A INNER JOIN (SELECT pr_return_delivery_items.`return_id`, pr_return_delivery_items.item_id, pr_return_delivery_items.`unit`, pr_return_delivery_items.`qty` FROM pr_return_delivery_items WHERE pr_return_delivery_items.`item_id` = $item_id) AS B ON A.`id` = B.return_id) AS C) AS D) AS E) AS F GROUP BY F.item_id, F.unit_stock) AS G) AS H";
				$objReturn = collect(DB::select($sql_return))->first();
				if($objReturn){
					$return_qty = floatval($objReturn->return_qty);
				}
				
				$boq_set = $boq_set - ($order_qty - $return_qty);
			}
			return ['boq_set'=>$boq_set,'price'=>$price];
		}
		return ['boq_set'=>-1,'price'=>0];
	}
	
	public function getOrderDetails(Request $request){
		if($request->ajax()){
			$po_id   = $request['po_id'];
			$objHead = Order::find($po_id);
			$select  = [
				'*',
				'cat_id',
				'code',
				'name',
				'alert_qty',
				'unit_stock',
				'unit_usage',
				'unit_purch',
				'cost_purch',
				DB::raw("(pr_order_items.`qty` - (pr_order_items.`deliv_qty` + pr_order_items.`closed_qty`)) AS qty"),
				DB::raw("((pr_order_items.`amount`) - (pr_order_items.`deliv_qty` * pr_order_items.`price`))AS `amount`"),
				DB::raw("((pr_order_items.`total`) - (pr_order_items.`deliv_qty` * pr_order_items.`price`))AS `total`"),
			];

			$objBody  = OrderItem::select($select)
						->leftJoin('items','order_items.item_id','items.id')
						->where('order_items.po_id',$po_id)
						->whereRaw(DB::raw("(pr_order_items.`qty` - (pr_order_items.`deliv_qty` + pr_order_items.`closed_qty`)) > ?"),[0])
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
			Excel::create('import_order_' . date('Y_m_d_H_i_s'),function($excel) {
				$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
				$excel->sheet('Draft Purchase Order',function($sheet){
					// Header
					$sheet->cell('A1', 'Reference No');
					$sheet->cell('B1', 'PR No');
					$sheet->cell('C1', 'Date');
					$sheet->cell('D1', 'Delivery Date');
					$sheet->cell('E1', 'Warehouse');
					$sheet->cell('F1', 'Supplier');
					$sheet->cell('G1', 'Order By');
					$sheet->cell('H1', 'Term of payment');
					$sheet->cell('I1', 'Note');
					$sheet->cell('J1', 'Item Code');
					$sheet->cell('K1', 'Unit');
					$sheet->cell('L1', 'Qty');
					$sheet->cell('M1', 'Price');
					$sheet->cell('N1', 'Amount');
					$sheet->cell('O1', 'Percent Discount');
					$sheet->cell('P1', 'Fixed Discount');
					$sheet->cell('Q1', 'Total');
					$sheet->cell('R1', 'Remark');
					// Example Body
					$sheet->cell('A2', 'PO0001');
					$sheet->cell('B2', 'PR0001');
					$sheet->cell('C2', date('Y-m-d'));
					$sheet->cell('D2', date('Y-m-d'));
					$sheet->cell('E2', 'WH001');
					$sheet->cell('F2', 'SUP001');
					$sheet->cell('G2', 'Jonh Doe');
					$sheet->cell('H2', '2 months');
					$sheet->cell('I2', 'Note Text (Optional)');
					$sheet->cell('J2', 'ITEM001');
					$sheet->cell('K2', 'Pcs');
					$sheet->cell('L2', 1);
					$sheet->cell('M2', 2);
					$sheet->cell('N2', 2);
					$sheet->cell('O2', 0);
					$sheet->cell('P2', 0);
					$sheet->cell('Q2', 2);
					$sheet->cell('R2', 'Remark Text (Optional)');
				});
			})->download('xlsx');
		} catch (\Exception $ex) {
			return redirect('purch/order')->with("error", $ex->getMessage());
		}
	}

	public function importExcel(Request $request)
	{
		try{
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

				if(empty($row->reference_no)){
					throw new \Exception("Column[A{$cellCount}] is empty");
				}

				$PO = null;
				if(!empty($row->reference_no)){
					if($PO = Order::where('ref_no',$row->reference_no)->first()){
						if($PO->trans_status == 4){
							throw new \Exception("Purchase Order[{$row->reference_no}] was rejected, please check at Column[A:{$cellCount}]");
						}elseif($PO->trans_status == 0){
							throw new \Exception("Purchase Order[{$row->reference_no}] was deleted, please check at Column[A:{$cellCount}]");
						}
					}
				}

				if(empty($row->pr_no)){
					throw new \Exception("Column[B{$cellCount}] is empty");
				}

				if(!$requests = Requests::where('ref_no',$row->pr_no)->first()){
					throw new \Exception("Purchase Request[{$row->pr_no}] not found at Column[B:{$cellCount}].");
				}

				if(empty($row->date)){
					throw new \Exception("Column[C{$cellCount}] is empty");
				}

				if(empty($row->delivery_date)){
					throw new \Exception("Column[D{$cellCount}] is empty");
				}

				if(empty($row->warehouse)){
					throw new \Exception("Column[E{$cellCount}] is empty");
				}

				if(!$warehouse = Warehouse::where('name',$row->warehouse)->first()){
					throw new \Exception("Warehouse[{$row->warehouse}] not found at Column[E:{$cellCount}].");
				}

				if(empty($row->supplier)){
					throw new \Exception("Column[F{$cellCount}] is empty");
				}

				if(!$supplier = Supplier::where('name',$row->supplier)->first()){
					throw new \Exception("Supplier[{$row->supplier}] not found at Column[F:{$cellCount}].");
				}

				if(empty($row->order_by)){
					throw new \Exception("Column[G{$cellCount}] is empty");
				}

				if(!$orderBy = User::where('name',$row->order_by)->first()){
					throw new \Exception("Order Person[{$row->order_by}] not found at Column[G:{$cellCount}].");
				}

				if(empty($row->item_code)){
					throw new \Exception("Column[J{$cellCount}] is empty");
				}

				if(!$item = Item::where('status',1)->where('code',$row->item_code)->first()){
					throw new \Exception("Item[{$row->item_code}] not found at Column[J:{$cellCount}].");
				}

				if(empty($row->unit)){
					throw new \Exception("Column[K{$cellCount}] is empty");
				}

				if(!$unit = Unit::where('from_desc',$row->unit)->first()){
					throw new \Exception("Unit[{$row->unit}] not found at Column[K:{$cellCount}].");
				}

				if(empty($row->qty)){
					throw new \Exception("Column[L{$cellCount}] is empty");
				}

				$order = [
					'pro_id' 		=> $pro_id,
					'pr_id'			=> $requests->id,
					'dep_id'		=> $requests->dep_id,
					'sup_id'		=> $supplier->id,
					'trans_date'	=> $row->date,
					'delivery_date'	=> $row->delivery_date,
					'trans_status'	=> 3,
					'sub_total'		=> 0,
					'disc_perc'		=> 0,
					'disc_usd'		=> 0,
					'grand_total'	=> 0,
					'ordered_by'	=> $orderBy->id,
					'created_by' 	=> Auth::user()->id,
					'created_at' 	=> date('Y-m-d H:i:s'),
					'updated_by' 	=> Auth::user()->id,
					'updated_at' 	=> date('Y-m-d H:i:s'),
				];

				if(!empty($row->note)){
					$order = array_merge($order,['note' => $row->note]);
				}

				if(!empty($row->term_of_payment)){
					$order = array_merge($order,['term_pay' => $row->term_of_payment]);
				}

				$orderItem = [
					'line_no' 		=> formatZero($cellCount, 3),
					'item_id'		=> $item->id,
					'unit'			=> $row->unit,
					'qty'			=> $row->qty,
					'deliv_qty'		=> 0,
					'closed_qty'	=> 0,
					'price'			=> 0,
					'amount'		=> 0,
					'total'			=> 0,
				];

				if(!empty($row->price)){
					$orderItem = array_merge($orderItem,['price' => $row->price]);
				}

				if(!empty($row->amount)){
					$orderItem = array_merge($orderItem,['amount' => $row->amount]);
				}

				if(!empty($row->total)){
					$orderItem = array_merge($orderItem,['total' => $row->total]);
				}

				if(!empty($row->percent_discount)){
					$orderItem = array_merge($orderItem,['disc_perc' => $row->percent_discount]);
				}

				if(!empty($row->fixed_discount)){
					$orderItem = array_merge($orderItem,['disc_usd' => $row->fixed_discount]);
				}

				if(!empty($row->remark)){
					$orderItem = array_merge($orderItem,['desc' => $row->remark]);
				}

				if(empty($PO)){
					if(!$poId = DB::table('orders')->insertGetId($order)){
						DB::rollback();
						throw new \Exception("Purchase Order[{$row->reference_no}] wasn\'t create, please check at Column[A:{$cellCount}]");
					}

					$PO = Order::find($poId);

					$orderItem = array_merge($orderItem,['po_id' => $poId]);
				}else{
					$orderItem = array_merge($orderItem,['po_id' => $PO->id]);
				}

				if(!$poItemId = DB::table('order_items')->insertGetId($orderItem)){
					DB::rollback();
					throw new \Exception("Item[{$row->item_code}] wasn\'t inserted, please check at Column[J:{$cellCount}]");
				}

				$poItem = OrderItem::find($poItemId);
				$updateOrder = [
					'sub_total' 	=> $PO->sub_total + $poItem->amount,
					'grand_total' 	=> $PO->grand_total + $poItem->total,
				];
				DB::table('orders')->where('id',$PO->id)->update($updateOrder);
			}

			DB::commit();
			return redirect('purch/order')->with("success", trans('lang.upload_success'));
		}catch(\Exception $ex){
			DB::rollback();
			return redirect('purch/order')->with("error", $ex->getMessage());
		}
	}
}
