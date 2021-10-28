<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Datatables;
use Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Excel;
use App\Model\Request as Requests;
use App\Model\RequestItem;
use App\Model\BoqItem;
use App\Model\Item;
use App\Model\Unit;
use App\Model\House;
use App\Model\SystemData;
use App\Model\Constructor;
use App\Model\Boq;
use App\Model\BoqHouse;
use Illuminate\Support\Facades\Session;

class RequestController extends Controller
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
			if($request->pr_id){
				$Ref = Requests::select(['requests.id',DB::raw("pr_requests.ref_no as text")])
					->leftJoin('request_items','requests.id','request_items.pr_id')
					->where('requests.trans_status',3)
					->where('requests.pro_id',$pro_id)
					->Orwhere('requests.id',$request->pr_id)
					->groupBy('requests.id')
					->offset(0)
				    ->limit(10)
				    ->paginate(10);
			}else{
				$Ref = Requests::select(['requests.id',DB::raw("pr_requests.ref_no as text")])
					->leftJoin('request_items','requests.id','request_items.pr_id')
					->where('requests.trans_status',3)
					->where('requests.pro_id',$pro_id)
					->where('requests.ref_no','like',''.($request->q).'%')
					->groupBy('requests.id')
					// ->whereRaw(DB::raw("(SELECT COUNT(pr_request_items.`qty`) AS num FROM pr_request_items WHERE pr_request_items.`pr_id` = pr_requests.id AND pr_request_items.`qty` > (pr_request_items.`ordered_qty` + pr_request_items.`closed_qty`)) > ?"),[0])
					->offset(0)
				    ->limit(10)
				    ->paginate(10);
			}

			return response()->json($Ref,200);

    	} catch (\Exception $e) {
    		return response()->json([],200);
    	}
    }

    public function GetDetail(Request $request)
    {
    	try {
			$projectID = $request->session()->get('project');
			$where = ['boqs.status'=> 1];
			$whereUsage = 'AND `pr_usages`.`pro_id` = '.$projectID;
			if(!empty($request["zone_id"])){
				$where = array_merge($where,['boqs.zone_id'=>$request["zone_id"]]);
				$whereUsage .= ' AND pr_usages.zone_id = '.$request["zone_id"];
			}
			if(!empty($request["block_id"])){
				$where = array_merge($where,['boqs.block_id'=>$request["block_id"]]);
				$whereUsage .= ' AND pr_usages.block_id = '.$request["block_id"];
			}
			if(!empty($request["building_id"])){
				$where = array_merge($where,['boqs.building_id'=>$request['building_id']]);
				$whereUsage .= ' AND pr_usages.building_id = '.$request["building_id"];
			}
			if(!empty($request["street_id"])){
				$where = array_merge($where,['boqs.street_id'=>$request["boqs.street_id"]]);
				// $whereUsage .= ' AND boqs.street_id = '.$request["street_id"];
			}
			if(!empty($request["house_type"])){
				$where = array_merge($where,['boq_items.working_type'=>$request["house_type"]]);
			}
			if(!empty($request["house_id"])){
				$where = array_merge($where,['boq_houses.house_id'=>$request["house_id"]]);
			}
			if(!empty($request["boq_id"])){
				$where = array_merge($where,['boqs.id'=>$request["boq_id"]]);
			}
			if(!empty($request["working_type"])){
				$where = array_merge($where,['boq_items.working_type'=>$request["working_type"]]);
			}
			
			$select = [
				'request_items.*',
				'items.cat_id',
				'items.code',
				'items.name',
				'items.alert_qty',
				'items.unit_stock',
				'items.unit_usage',
				'items.unit_purch',
				'items.cost_purch',
				DB::raw("IFNULL((SELECT SUM(`pr_usage_details`.`qty`) FROM `pr_usage_details` JOIN pr_usages ON `pr_usage_details`.`use_id` = `pr_usages`.`id` WHERE `pr_usage_details`.`item_id` = `pr_request_items`.`item_id` {$whereUsage}),0) AS usage_qty"),
				DB::raw('IFNULL((SELECT SUM(`pr_stocks`.`qty`) FROM `pr_stocks` WHERE `pr_stocks`.`item_id` = `pr_request_items`.`item_id` AND `pr_stocks`.`trans_ref` = "I" AND `pr_stocks`.`pro_id` = 1 ),0) AS stock_qty') 
			];

			$OrderItem  = RequestItem::select($select)
						->leftJoin('items','request_items.item_id','items.id')
						->where('request_items.pr_id',$request->id)->get();

			return response()->json($OrderItem,200); 

		} catch (\Exception $e) {
			return response()->json([],200);
		}
    }
	
    public function index(){
		$data = [
			'title'			=> trans('lang.request'),
			'icon'			=> 'fa fa-registered',
			'small_title'	=> trans('lang.request_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'dashboard'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],			
			'rounte'		=> url("purch/request/dt"),
		];
		
		// if(hasRole('purchase_request_add')){
			$data = array_merge($data, ['rounteAdd'=> url('purch/request/add')]);
		// }
		return view('purch.request.index',$data);
	}
	
	public function add($cid=NULL){
		$data = [
			'title'			=> trans('lang.request'),
			'icon'			=> 'fa fa-plus',
			'small_title'	=> trans('lang.add_new'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
					'url' 		=> url('/'),
					'caption' 	=> trans('lang.home'),
				],
				'index'	=> [
					'url' 		=> url('purch/request'),
					'caption' 	=> trans('lang.request'),
				],
				'add'	=> [
					'caption' 	=> trans('lang.add_new'),
				],
			],
			'rounteSave'	=> url('purch/request/save'),
			'rounteBack'	=> url('purch/request'),
		];
		if($cid){
			$id = decrypt($cid);
			$head = Requests::find($id);
			$data = array_merge($data, ['head'=>$head]);
			$body = RequestItem::where(['pr_id'=>$id])->get();
			$data = array_merge($data, ['body'=>$body]);
		}
		return view('purch.request.add', $data);
	}
	
	public function edit(Request $request, $id){
		$id = decrypt($id);
		$obj = Requests::find($id);
		if($obj){
			$data = [
				'title'			=> trans('lang.request'),
				'icon'			=> 'fa fa-edit',
				'small_title'	=> trans('lang.edit'),
				'background'	=> '',
				'link'			=> [
					'home'	=> [
							'url' 		=> url('/'),
							'caption' 	=> trans('lang.home'),
					],
					'index'	=> [
							'url' 		=> url('purch/request'),
							'caption' 	=> trans('lang.request'),
					],
					'edit'	=> [
							'caption' 	=> trans('lang.edit'),
					],
				],
				'rounteSave'	=> url('purch/request/update/'.$id),
				'rounteBack'	=> url('purch/request'),
				'obj'	=> $obj,
			];
			return view('purch.request.edit',$data);
		}else{
			return redirect()->back();
		}
	}
	
	public function getDt(Request $request)
    {
		$pro_id = $request->session()->get('project');
		$dep_id = Auth::user()->dep_id; 
		$user_id = Auth::user()->id;
		$prefix = DB::getTablePrefix();
		
        $columns  = [
			'requests.*',
			DB::raw("(SELECT CONCAT({$prefix}constructors.id_card,' (',{$prefix}constructors.name,')')AS name FROM {$prefix}constructors WHERE {$prefix}constructors.id = {$prefix}requests.request_by) AS request_by"),
			DB::raw("(SELECT CONCAT({$prefix}system_datas.desc,' (',{$prefix}system_datas.name,')') FROM {$prefix}system_datas WHERE {$prefix}system_datas.id={$prefix}requests.dep_id)AS department"),
			DB::raw("(SELECT SUM(pr_request_items.ordered_qty) FROM pr_request_items WHERE pr_request_items.pr_id = pr_requests.id) AS ordered_qty"),
			DB::raw("(SELECT SUM(pr_request_items.closed_qty) FROM pr_request_items WHERE pr_request_items.pr_id = pr_requests.id) AS closed_qty"),
			DB::raw("(SELECT SUM(pr_request_items.qty) FROM pr_request_items WHERE pr_request_items.pr_id = pr_requests.id) AS total_qty")
		];

		$requests = Requests::select($columns)->where('pro_id',$pro_id)->where('trans_status','>',0);

		if(!hasRole('admin') && !hasRole('owner')){
			$requests = $requests->where('dep_id',$dep_id)->orWhere('created_by',$user_id);
		}

		$requests = $requests->get();
		
		return Datatables::of($requests)
		->addColumn('details_url',function($row){
            return url('purch/request/subdt').'/'.$row->id;
        })
		->addColumn('action', function ($row) {
			$rounte_delete = url('purch/request/delete/'.$row->id);
			$rounte_edit = url('purch/request/edit/'.encrypt($row->id));
			$rounte_print = url('purch/request/print/'.encrypt($row->id));
			$rounte_close = url('purch/request/close/'.$row->id);
			$rounte_copy = url('purch/request/add/'.encrypt($row->id));
			$rounte_view = url('purch/request/view/'.$row->id);
			$actionClose = 'disabled';
			$actionEdit = 'onclick="onEdit(this)"';
			$actionDelete = 'onclick="onDelete(this)"';
			$actionPrint = 'onclick="onPrint(this)"';
			$actionView = 'onclick="onView(this)"';
			$actionCopy = 'onclick="onCopy(this)"';
			
			if($row->trans_status==3){
				$obj = RequestItem::where(['pr_id'=>$row->id])->get();
				if(count($obj) > 0){
					foreach($obj as $val){
						if(floatval($val->ordered_qty + $val->closed_qty)!=floatval($val->qty)){
							$actionClose = 'onclick="onClose(this)"';
						}
					}
				}
			}
			
			if(!hasRole('purchase_request_edit')){
				$actionEdit = "disabled";
			}
			if(!hasRole('purchase_request_delete')){
				$actionDelete = "disabled";
			}
			if(!hasRole('purchase_request_print')){
				$actionPrint = "disabled";
			}
			if($row->trans_status != 3){
				$actionPrint = "disabled";
			}
			if(!hasRole('purchase_request_clone')){
				$actionCopy = "disabled";
			}
			if(!hasRole('purchase_request_view')){
				$actionView = "disabled";
			}
			if(!hasRole('purchase_request_close')){
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
			}else if($row->trans_status==5){
				return $btnPrint.$btnEdit.$btnDelete;
			}else if($row->trans_status==3){
				return $btnPrint.$btnView.$btnClose;
			}else{
				return $btnView.$btnCopy.$btnDelete;
			}
       })->make(true);
    }
	
	public function subDt(Request $request, $id)
    {
		$requestItems = RequestItem::select(
			'*'
		)->where('pr_id',$id)->get();	
		return Datatables::of($requestItems)
		->addColumn('item',function($row){
			if($item = Item::find($row->item_id)){
				return "{$item->name} ({$item->code})";
			}
			return '';
		})
		->make(true);
	}

    public function save(Request $request)
    {
		// print_r($request->all());exit;
    	$rules = [
			'reference_no' 	=>'required|max:20|unique_request',
			'trans_date' 	=>'required|max:20',
			'delivery_date'	=>'required|max:20',
			'request_by'	=>'required|max:11',
			'department'	=>'required|max:11',
		];
		
		if(count($request['line_index']) > 0){
			
			for($i=0;$i<count($request['line_index']);$i++){
				$rules['line_index.'.$i]	= 'required';
				$rules['line_item.'.$i]		= 'required|max:11';
				$rules['line_unit.'.$i]		= 'required';
				$rules['line_qty.'.$i]		= 'required';
				$rules['line_boq_set.'.$i]	= 'required';
				$rules['line_price.'.$i]	= 'required';
			}
			
		}
		
		
        Validator::make($request->all(),$rules)->validate();
		// print_r($request['line_index']);exit; 
		try {
			DB::beginTransaction();
			
			$trans_date = date("Y-m-d", strtotime($request->trans_date));
			$delivery_date = date("Y-m-d", strtotime($request->delivery_date));
			$data = [
				'pro_id'		=>$request->session()->get('project'),
				'ref_no'		=>$request->reference_no,
				'trans_date'	=>$trans_date,
				'delivery_date'	=>$delivery_date,
				'request_by'	=>$request->request_by,
				'dep_id'		=>$request->department,
				'note'			=>trim($request->desc),
				'trans_status'	=>$request->status,
				'created_by'	=>Auth::user()->id,
				'created_at'	=>date('Y-m-d H:i:s'),
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s')
			];
			
			if(!$id = DB::table('requests')->insertGetId($data)){
				throw new \Exception("Request not insert");
			}
			

			if(count($request['line_index']) > 0){
				
				// print_r($request->all());exit;
				for($i=0;$i<count($request['line_index']);$i++){
					$details = [
						'pr_id'			=>$id,
						'line_no'		=>$request['line_index'][$i],
						'item_id'		=>$request['line_item'][$i],
						'size'			=>$request['line_size'][$i],
						'unit'			=>$request['line_unit'][$i],
						'qty'			=>$request['line_qty'][$i],
						'boq_set'		=>$request['line_boq_set'][$i],
						'price'			=>$request['line_price'][$i],
						'desc'			=>$request['line_reference'][$i],
						'remark'		=>$request['line_remark'][$i],
					];

					if(!$detailId = DB::table('request_items')->insertGetId($details)){
						DB::rollback();
						throw new \Exception("Request Item[$i] not insert");
					}
				}
			}
			DB::commit();
			if($request->btnSubmit==1){
				return redirect('purch/request')->with('success',trans('lang.save_success'));
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
			'delivery_date'	=>'required|max:20',
			'request_by'	=>'required|max:11',
			'department'	=>'required|max:11',
		];
		if(count($request['line_index']) > 0){
			for($i=0;$i<count($request['line_index']);$i++){
				$rules['line_index.'.$i]	= 'required';
				$rules['line_item.'.$i]		= 'required|max:11';
				$rules['line_unit.'.$i]		= 'required';
				$rules['line_qty.'.$i]		= 'required';
				$rules['line_boq_set.'.$i]	= 'required';
				$rules['line_price.'.$i]	= 'required';
			}
		}
        Validator::make($request->all(),$rules)->validate();
		try {
			DB::beginTransaction();

			if(!$purchaseRequest = Requests::find($id)){
				throw new \Exception("Request[$id] not found");
			}

			$trans_date = date("Y-m-d", strtotime($request->trans_date));
			$delivery_date = date("Y-m-d", strtotime($request->delivery_date));
			$data = [
				'trans_date'	=>$trans_date,
				'dep_id'		=>$request->department,
				'delivery_date'	=>$delivery_date,
				'request_by'	=>$request->request_by,
				'trans_status'	=>$request->status,
				'note'			=>trim($request->desc),
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];
			DB::table('requests')->where(['id'=>$id])->update($data);
			// delete all request items
			DB::table('request_items')->where(['pr_id' => $id])->delete();

			if(count($request['line_index']) > 0){
				for($i=0;$i<count($request['line_index']);$i++){
					$details = [
						'pr_id'			=>$id,
						'line_no'		=>$request['line_index'][$i],
						'item_id'		=>$request['line_item'][$i],
						'size'			=>$request['line_size'][$i],
						'unit'			=>$request['line_unit'][$i],
						'qty'			=>$request['line_qty'][$i],
						'boq_set'		=>$request['line_boq_set'][$i],
						'price'			=>$request['line_price'][$i],
						'desc'			=>$request['line_reference'][$i],
						'remark'		=>$request['line_remark'][$i],
					];

					if(!$requestDetailId = DB::table('request_items')->insertGetId($details)){
						DB::rollback();
						throw new \Exception("Request Item[$i] not insert");
					}
				}
			}
			DB::commit();
			return redirect('purch/request')->with('success',trans('lang.save_success'));
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
			$data = [
				'trans_status'	=>0,
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];
			DB::table('requests')->where(['id'=>$id])->update($data);
			DB::commit();
			return redirect()->back()->with('success',trans('lang.delete_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.delete_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }
	
	public function close($id)
    {
		try {
			DB::beginTransaction();
			$arrObj = RequestItem::where(['pr_id'=>$id])->get();
			if(count($arrObj) > 0){
				foreach($arrObj as $row){
					DB::table('request_items')->where(['id'=>$row->id])->update(['closed_qty'=>(floatval($row->qty) - floatval($row->ordered_qty))]);
				}
			}
			DB::table('requests')->where('id',$id)->update(['is_closed'=>1]);
			DB::commit();
			return redirect()->back()->with('success',trans('lang.close_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.close_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }
	
	public function getStepApprove(Request $request ,$id){
		$pro_id = $request->session()->get('project');
		$sql = "CALL PRApprovalStep({$pro_id},{$id});"; 
		$obj = DB::select($sql);
		return $obj;
	}
	
	public function getItemBOQ(Request $request){
		if($request->ajax()){
			$pro_id = $request->session()->get('project');
			$item_id = $request['item_id'];
			$unit = $request['unit'];
			$warehouse_id = $request['warehouse_id'];
			
			$boq_set = -1;
			$price = 0;
			$request_qty = 0;
			$return_qty = 0;
			$stock_qty = 0;
			$original_boq = 0;
			
			$itemObj = Item::find($item_id);
			if($itemObj){
				$qtySt = 0;
				$unitSt = Unit::where(['from_code'=>$itemObj->unit_purch,'to_code'=>$itemObj->unit_stock])->first();
				if($unitSt){
					$qtySt = $unitSt->factor;
				}
				$qtyPr = 0;
				$unitPr = Unit::where(['from_code'=>$unit,'to_code'=>$itemObj->unit_stock])->first();
				if($unitPr){
					$qtyPr = $unitPr->factor;
				}
				
				$price = (floatval($qtyPr) * floatval($itemObj->cost_purch)) / floatval($qtySt);

			}
			$where = ['boqs.status'=> 1];
			if(!empty($request["zone_id"])){
				$where = array_merge($where,['boqs.zone_id'=>$request["zone_id"]]);
			}
			if(!empty($request["block_id"])){
				$where = array_merge($where,['boqs.block_id'=>$request["block_id"]]);
			}
			if(!empty($request["building_id"])){
				$where = array_merge($where,['boqs.building_id'=>$request['building_id']]);
			}
			if(!empty($request["street_id"])){
				$where = array_merge($where,['boqs.street_id'=>$request["boqs.street_id"]]);
			}
			if(!empty($request["house_type"])){
				$where = array_merge($where,['boq_items.working_type'=>$request["house_type"]]);
			}
			
			if(!empty($request["boq_id"])){
				$where = array_merge($where,['boqs.id'=>$request["boq_id"]]);
			}
			if(!empty($request["working_type"])){
				$where = array_merge($where,['boq_items.working_type'=>$request["working_type"]]);
			}
			$boqs = Boq::where('boqs.pro_id',$pro_id)->where($where)->pluck('id');
			if(!empty($request["house_id"])){
				// $where = array_merge($where,['boq_houses.house_id'=>$request["house_id"]]);
				$housesId = $request["house_id"];
				
				$housesId =  BoqHouse::whereIn('boq_id', $boqs)->whereIn('house_id',$housesId)->pluck('id');
				$strHouseId = str_replace(["[","]"],"",$housesId);
			}else{
				
				$housesId =  BoqHouse::whereIn('boq_id', $boqs)->pluck('id');
				$strHouseId = str_replace(["[","]"],"",$housesId);
			}
			// $strHouseId = $housesId;
			// print_r(str_replace(["[","]"],"",$strHouseId));exit;
			if(count($housesId) > 0){
				$itemBoq = BoqItem::whereIn('boq_house_id',$housesId)->where(['item_id'=>$item_id])->exists();
			}else{
				$itemBoq = BoqItem::where(['item_id'=>$item_id])->exists();
			}
			
			if($itemBoq){
				// print_r($strHouseId);
				$sql_stock = "SELECT (F.stock_qty / F.use_qty) AS stock_qty FROM (SELECT E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS use_qty FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_stocks.`item_id`, pr_stocks.`unit`, pr_stocks.`qty` FROM pr_stocks WHERE pr_stocks.`item_id` = $item_id AND pr_stocks.`warehouse_id` = $warehouse_id AND pr_stocks.`delete` = 0) AS A) AS B) AS C) AS D GROUP BY D.item_id) AS E) AS F";
				$objStock = collect(DB::select($sql_stock))->first();

				if($objStock){
					$stock_qty = floatval($objStock->stock_qty);
				}
				$sql_boq = "SELECT F.item_id, (F.stock_qty / F.boq_qty) AS boq_qty 
							FROM (SELECT E.item_id, E.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = E.unit_stock) AS boq_qty 
							FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (CASE WHEN (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock)!='' THEN (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) ELSE 1 END) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_boq_items.`id`, pr_boq_items.`house_id`,pr_boq_items.`boq_house_id`, pr_boq_items.`item_id`, pr_boq_items.`unit`, SUM((pr_boq_items.`qty_std` + pr_boq_items.`qty_add` )) AS qty FROM pr_boq_items WHERE pr_boq_items.`item_id` = $item_id AND pr_boq_items.`boq_house_id` IN ($strHouseId)) AS A ) AS B) AS C) AS D GROUP BY D.item_id, D.unit_stock) AS E) AS F"; 
				
				$objBOQ = collect(DB::select($sql_boq))->first();
				
				if($objBOQ){
					$original_boq = floatval($objBOQ->boq_qty);
					$boq_set = floatval($objBOQ->boq_qty);
					$sql_request = "SELECT (H.stock_qty / H.request_qty) AS request_qty FROM (SELECT G.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = G.unit_stock) AS request_qty FROM (SELECT F.item_id, F.unit_stock, SUM(F.stock_qty) AS stock_qty FROM (SELECT E.item_id, E.unit_stock, (E.qty * E.stock_qty) AS stock_qty FROM (SELECT D.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = D.unit AND pr_units.`to_code` = D.unit_stock) AS stock_qty FROM (SELECT C.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = C.item_id) AS unit_stock FROM (SELECT B.item_id, B.unit, B.qty FROM (SELECT pr_requests.`id` FROM pr_requests WHERE pr_requests.`pro_id` = $pro_id AND pr_requests.`trans_status` IN (1, 2, 3)) AS A INNER JOIN (SELECT pr_request_items.`pr_id`, pr_request_items.item_id, pr_request_items.`unit`, (pr_request_items.`qty` - pr_request_items.`closed_qty` ) AS qty FROM pr_request_items WHERE pr_request_items.`item_id` = $item_id AND pr_request_items.boq_set!=-1) AS B ON A.`id` = B.pr_id) AS C) AS D) AS E) AS F GROUP BY F.item_id, F.unit_stock) AS G) AS H";
					$objRequest = collect(DB::select($sql_request))->first();

					if($objRequest){
						$request_qty = floatval($objRequest->request_qty);
					}
					
					$sql_return = "SELECT (H.stock_qty / H.return_qty) AS return_qty FROM (SELECT G.stock_qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = G.unit_stock) AS return_qty FROM (SELECT F.item_id, F.unit_stock, SUM(F.stock_qty) AS stock_qty FROM (SELECT E.item_id, E.unit_stock, (E.qty * E.stock_qty) AS stock_qty FROM (SELECT D.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = D.unit AND pr_units.`to_code` = D.unit_stock) AS stock_qty FROM (SELECT C.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = C.item_id) AS unit_stock FROM (SELECT B.item_id, B.unit, B.qty FROM (SELECT pr_return_deliveries.`id` FROM pr_return_deliveries WHERE pr_return_deliveries.`pro_id` = $pro_id AND pr_return_deliveries.`delete` = 0) AS A INNER JOIN (SELECT pr_return_delivery_items.`return_id`, pr_return_delivery_items.item_id, pr_return_delivery_items.`unit`, pr_return_delivery_items.`qty` FROM pr_return_delivery_items WHERE pr_return_delivery_items.`item_id` = $item_id) AS B ON A.`id` = B.return_id) AS C) AS D) AS E) AS F GROUP BY F.item_id, F.unit_stock) AS G) AS H";
					$objReturn = collect(DB::select($sql_return))->first();

					if($objReturn){
						$return_qty = floatval($objReturn->return_qty);
					}
					
					$boq_set = $boq_set - ($request_qty - $return_qty);
					// $boq_set = $boq_set - ($request_qty);
				}
			}

			return ['boq_set'=>$boq_set,'price'=>$price,'return'=>$return_qty,'qty_stock'=>$stock_qty,'request'=>$request_qty,'origin_boq'=>$original_boq];
		}
		return ['boq_set'=>-1,'price'=>0];
	}
	
	public function getRequestDetails(Request $request){
		$prefix = DB::getTablePrefix();
		if($request->ajax()){
			try {
				$pr_id = $request['pr_id'];
				$select = [
					'request_items.*',
					'items.cat_id',
					'items.code',
					'items.name',
					'items.alert_qty',
					'items.unit_stock',
					'items.unit_usage',
					'items.unit_purch',
					'items.cost_purch',
					DB::raw("({$prefix}request_items.`qty` - ({$prefix}request_items.`ordered_qty` + {$prefix}request_items.`closed_qty` )) AS qty"),
				];

				$OrderItem  = RequestItem::select($select)
							->leftJoin('items','request_items.item_id','items.id')
							->where('request_items.pr_id',$pr_id)
							->whereRaw(DB::raw("({$prefix}request_items.`qty` - ({$prefix}request_items.`ordered_qty` + {$prefix}request_items.`closed_qty` )) > ?"),[0])
							->get();

				return response()->json($OrderItem,200);
			} catch (\Exception $e) {
				return response()->json([],200);
			}
		}
		return response()->json([],200);
	}

	public function downloadExcel(Request $request){
		try {
			Excel::create('import_request_' . date('Y_m_d_H_i_s'),function($excel) {
				$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
				$excel->sheet('Draft Purchase Request',function($sheet){
					// Header
					$sheet->cell('A1', 'Reference No');
					$sheet->cell('B1', 'Date');
					$sheet->cell('C1', 'Delivery Date');
					$sheet->cell('D1', 'Department');
					$sheet->cell('E1', 'Request By');
					$sheet->cell('F1', 'Item Code');
					$sheet->cell('G1', 'Unit');
					$sheet->cell('H1', 'Qty');
					$sheet->cell('I1', 'Size');
					$sheet->cell('J1', 'Note');
					$sheet->cell('K1', 'Remark');
					// Example Body
					$sheet->cell('A2', 'PR0001');
					$sheet->cell('B2', date('Y-m-d'));
					$sheet->cell('C2', date('Y-m-d'));
					$sheet->cell('D2', 'Account');
					$sheet->cell('E2', 'Jonh Doe');
					$sheet->cell('F2', 'ITEM001');
					$sheet->cell('G2', 'Pcs');
					$sheet->cell('H2', 2);
					$sheet->cell('I2', '2 x 4 mm (Optional)');
					$sheet->cell('J2', 'Note Text (Optional)');
					$sheet->cell('K2', 'Remark Text (Optional)');
				});
			})->download('xlsx');
		} catch (\Exception $ex) {
			return redirect('purch/request')->with("error", $ex->getMessage());
		}
	}

	public function importExcel(Request $request)
	{
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
				if(empty($row->reference_no)){
					throw new \Exception("Column[A{$cellCount}] is empty");
				}
				$PR = null;
				if($PR = Requests::where('ref_no',$row->reference_no)->first()){
					if($PR->trans_status == 4){
						throw new \Exception("Purchase Request[{$row->reference_no}] was rejected, please check at Column[A:{$cellCount}]");
					}elseif($PR->trans_status == 0){
						throw new \Exception("Purchase Request[{$row->reference_no}] was deleted, please check at Column[A:{$cellCount}]");
					}
				}

				if(empty($row->date)){
					throw new \Exception("Column[B{$cellCount}] is empty");
				}

				if(empty($row->delivery_date)){
					throw new \Exception("Column[C{$cellCount}] is empty");
				}

				if(empty($row->department)){
					throw new \Exception("Column[D{$cellCount}] is empty");
				}

				if(!$department = SystemData::where('type','DP')->where('name',$row->department)->first()){
					throw new \Exception("Department[{$row->department}] not found at Column[D:{$cellCount}].");
				}

				if(empty($row->request_by)){
					throw new \Exception("Column[E{$cellCount}] is empty");
				}

				if(!$requestBy = Constructor::where('status',1)->where('id_card',$row->request_by)->first()){
					throw new \Exception("Request Person[{$row->request_by}] not found at Column[E:{$cellCount}].");
				}

				if(empty($row->item_code)){
					throw new \Exception("Column[F{$cellCount}] is empty");
				}

				if(!$item = Item::where('status',1)->where('code',$row->item_code)->first()){
					throw new \Exception("Item[{$row->item_code}] not found at Column[F:{$cellCount}].");
				}

				if(empty($row->unit)){
					throw new \Exception("Column[G{$cellCount}] is empty");
				}

				if(!$unit = Unit::where('from_desc',$row->unit)->first()){
					throw new \Exception("Unit[{$row->unit}] not found at Column[G:{$cellCount}].");
				}

				if(empty($row->qty)){
					throw new \Exception("Column[H{$cellCount}] is empty");
				}

				$requestItem = [
					'line_no' 		=> formatZero($cellCount, 3),
					'unit'	  		=> $row->unit,
					'qty'	  		=> $row->qty,
					'boq_set' 		=> -1,
					'ordered_qty' 	=> 0,
					'closed_qty' 	=> 0,
					'item_id'		=> $item->id,
					'price'			=> $item->cost_purch,
				];
				
				if(!empty($row->size)){
					$requestItem = array_merge($requestItem,['size' => $row->size]);
				}

				if(!empty($row->remark)){
					$requestItem = array_merge($requestItem,['remark' => $row->remark]);
				}

				if(empty($PR)){
					$request = [
						'ref_no'		=> $row->reference_no,
						'trans_date' 	=> $row->date,
						'delivery_date' => $row->delivery_date,
						'trans_status' 	=> 3,
						'request_by'	=> $requestBy->id,
						'pro_id'		=> $pro_id,
						'dep_id'		=> $department->id,
						'created_by'	=> Auth::user()->id,
						'created_at'	=> date('Y-m-d H:i:s'),
						'updated_by'	=> Auth::user()->id,
						'updated_at'	=> date('Y-m-d H:i:s'),
					];

					

					if(!empty($row->note)){
						$request = array_merge($request,['note' => $row->note]);
					}

					if(!$prId = DB::table('requests')->insertGetId($request)){
						DB::rollback();
						throw new \Exception("Purchase Request[{$row->reference_no}] wasn\'t create, please check at Column[A:{$cellCount}]");
					}

					$requestItem = array_merge($requestItem,['pr_id' => $prId]);
				}else{
					$requestItem = array_merge($requestItem,['pr_id' => $PR->id]);
				}

				if(!$prItemId = DB::table('request_items')->insertGetId($requestItem)){
					DB::rollback();
					throw new \Exception("Item[{$row->item_code}] wasn\'t inserted, please check at Column[F:{$cellCount}]");
				}
			}
			DB::commit();
			return redirect('purch/request')->with("success", trans('lang.upload_success'));
		} catch (\Exception $ex) {
			DB::rollback();
			return redirect('purch/request')->with("error", $ex->getMessage());
		}
	}

	
}
