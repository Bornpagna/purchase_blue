<?php

use Illuminate\Support\Facades\DB;
use App\Model\Item;
use App\Model\Delivery;
use App\Model\DeliveryItem;
use Illuminate\Support\Facades\Session;

function getDeliveryRef(){
	$pro_id = Session::get('project');
	$data = Delivery::where(['pro_id'=>$pro_id, 'delete'=>0])->get()->pluck('ref_no', 'id');
	foreach ($data as $key => $value) {
		echo '<option value="'.$key.'">'.$value.'</option>';
	}
}

function getReferenceDelivery($edit_id=NULL){
	$pro_id = Session::get('project');
	$condition = "";

	$select = [
		'deliveries.id',
		'deliveries.ref_no',
	];

	if($edit_id){
		$obj = Delivery::select($select)
			->leftJoin('delivery_items','deliveries.id','delivery_items.del_id')
			->where('deliveries.id',$edit_id)
			->where('deliveries.pro_id',$pro_id)
			->groupBy('deliveries.id')
			// ->whereRaw(DB::raw("(SELECT COUNT(pr_delivery_items.`qty`) AS num FROM pr_delivery_items WHERE pr_delivery_items.`del_id` = pr_deliveries.id AND pr_delivery_items.`qty` > pr_delivery_items.`return_qty`) > ?"),[0])
			->get();
	}else{
		$obj = Delivery::select($select)
			->leftJoin('delivery_items','deliveries.id','delivery_items.del_id')
			->where('deliveries.pro_id',$pro_id)
			// ->whereRaw(DB::raw("(SELECT COUNT(pr_delivery_items.`qty`) AS num FROM pr_delivery_items WHERE pr_delivery_items.`del_id` = pr_deliveries.id AND pr_delivery_items.`qty` > pr_delivery_items.`return_qty`) > ?"),[0])
			->get();
	}

	if(count($obj)>0){
		foreach($obj as $row){
			echo '<option value="'.$row->id.'">'.$row->ref_no.'</option>';
		}
	}
}
function getReferencePO($edit_id=NULL){
	$pro_id = Session::get('project');
	$condition = "";
	if($edit_id){
		$condition = " OR A.id = $edit_id ";
	}
	$sql = "SELECT A.* FROM (SELECT pr_orders.`id`, pr_orders.`ref_no`,(SELECT pr_requests.`ref_no` FROM pr_requests WHERE pr_requests.`id`=pr_orders.`pr_id`)AS pr_ref FROM pr_orders WHERE pr_orders.`trans_status` = 3 AND pr_orders.pro_id = $pro_id) AS A WHERE (SELECT COUNT(pr_order_items.`qty`) AS num FROM pr_order_items WHERE pr_order_items.`po_id` = A.id AND pr_order_items.`qty` > (pr_order_items.`deliv_qty` + pr_order_items.`closed_qty` )) > 0 ".$condition;
	$obj = DB::select($sql);

	// if ($edit_id) {
	// 	$obj = Order::select(['orders.id','orders.ref_no'])
	// 			->leftJoin
	// }else{

	// }

	if(count($obj)>0){
		foreach($obj as $row){
			echo '<option value="'.$row->id.'">'.$row->ref_no.' ('.$row->pr_ref.')</option>';
		}
	}
}

function getReferencePR($edit_id=NULL){
	$pro_id = Session::get('project');
	$condition = "";
	if($edit_id){
		$condition = " OR A.id = $edit_id ";
	}
	$sql = "SELECT A.* FROM (SELECT pr_requests.`id`, pr_requests.`ref_no` FROM pr_requests WHERE pr_requests.`trans_status` = 3 AND pr_requests.pro_id = $pro_id) AS A WHERE (SELECT COUNT(pr_request_items.`qty`) AS num FROM pr_request_items WHERE pr_request_items.`pr_id` = A.id AND pr_request_items.`qty` > (pr_request_items.`ordered_qty` + pr_request_items.`closed_qty` )) > 0 ".$condition;
	$obj = DB::select($sql);
	if(count($obj)>0){
		foreach($obj as $row){
			echo '<option value="'.$row->id.'">'.$row->ref_no.'</option>';
		}
	}
}
///////////////BOQ///////////////
function getBOQNumber(){
	$pro_id = Session::get('project');
	$where = ['status'=>1];
	$data = DB::table('boqs')->where($where)->get();
	foreach($data as $row){
		echo '<option value="'.$row->id.'">'.$row->boq_code.'</option>';
	}
}

////////////////////// unit stock ///////////////////////////////
function getSystemData($type=NULL,$val=NULL,$parent_only=NULL){
	$pro_id = Session::get('project');
	$where = [
		'status'=>'1'
	];

	if(($type) && ($type == "IT" || $type == "DP")){
		$where = array_merge($where, ['type'=>$type]);
	}else if($type){
		$where = array_merge($where, ['type'=>$type,'parent_id'=>$pro_id]);
	}
	$parentDatas = DB::table('system_datas')->where($where)->where('parent',0)->get();
	
	foreach($parentDatas as $row){
		if($parent_only){
			if(strval($val) == strval($row->id)){
				echo '<option value="'.$row->id.'" selected>'.$row->name.'</option>';
			}else{ 
				echo '<option value="'.$row->id.'">'.$row->name.'</option>';
			}
		}else{
			$data = DB::table('system_datas')->where($where)->where('parent',$row->id)->get();
			if(count($data) > 0){
				echo '<optgroup label="'.$row->name.'">';
					foreach($data as $key=>$child){
						if(strval($val) == strval($child->id)){
							echo '<option value="'.$child->id.'" selected>'.$child->name.'</option>';
						}else{ 
							echo '<option value="'.$child->id.'">'.$child->name.'</option>';
						}
					}
				echo '</optgroup>';
			}else{
				if(strval($val) == strval($row->id)){
					echo '<option value="'.$row->id.'" selected>'.$row->name.'</option>';
				}else{ 
					echo '<option value="'.$row->id.'">'.$row->name.'</option>';
				}
			}
		}
	}
}
function getSystemDatas($type=NULL,$zone=NULL,$block=Null,$building=Null,$street=Null,$house=Null){	
	$pro_id = Session::get('project');
	$data = DB::table('system_datas')->where('type',$type)->where('parent_id',$pro_id)->where('status','1')->get();
	return $data;
}
function getSystemDataVal($type=NULL,$val=NULL){
	$pro_id = Session::get('project');
	$where = [
		'status'=>'1'
	];
	if(($type) && ($type == "IT" || $type == "DP")){
		$where = array_merge($where, ['type'=>$type]);
	}else if($type){
		$where = array_merge($where, ['type'=>$type,'parent_id'=>$pro_id]);
	}
	$data = DB::table('system_datas')->where($where)->get();
	foreach($data as $row){
		if(($val) == ($row->id)){
			echo '<option value="'.$row->id.'" selected>'.$row->name.'</option>';
		}else{ 
			echo '<option value="'.$row->id.'">'.$row->name.'</option>';
		}
	}
}
function getSystemDataName($type=NULL,$val=NULL){
	$pro_id = Session::get('project');
	$where = [
		'status'=>'1'
	];
	if(($type) && ($type == "IT" || $type == "DP")){
		$where = array_merge($where, ['type'=>$type]);
	}else if($type){
		$where = array_merge($where, ['type'=>$type,'parent_id'=>$pro_id]);
	}
	$data = DB::table('system_datas')->where($where)->get();
	foreach($data as $row){
		if(strval($val) == $row->name){
			echo '<option value="'.$row->id.'" selected>'.$row->name.'</option>';
		}else{ 
			echo '<option value="'.$row->id.'">'.$row->name.'</option>';
		}
	}
}
function getHouseOption($val=NULL,$arr=NULL){
	$where = [
		'status'=>'1'
	];
	$data = DB::table('houses')->where($where)->get();
	if(!empty($arr)){
		foreach($data as $row){
			$select = '';
			foreach($arr as $sub_row){
				if($sub_row==$row->id){
					$select = 'selected';
				}
			}
			echo '<option value="'.$row->id.'" '.$select.'>'.$row->house_no.'</option>';
		}
	}else{
		foreach($data as $row){
			if($val == $row->name){
				echo '<option value="'.$row->id.'" selected>'.$row->house_no.'</option>';
			}else{ 
				echo '<option value="'.$row->id.'">'.$row->house_no.'</option>';
			}
		}
	}	
}
////////////////////// unit stock ///////////////////////////////
function getUnitStock($val=NULL){
	$data = DB::table('units')->where('status','1')->select(['to_code','to_desc'])->distinct('to_code')->get();
	foreach($data as $row){
		if($val!=NULL && $val==$row->to_code){
			echo '<option value="'.$row->to_code.'" selected>'.$row->to_code.' ('.$row->to_desc.')</option>';
		}else{
			echo '<option value="'.$row->to_code.'">'.$row->to_code.' ('.$row->to_desc.')</option>';
		}
	}
}

////////////////////// unit usage ///////////////////////////////
function getUnitUsage($stock=NULL,$val=NULL){
	$data = DB::table('units')->where(['status'=>'1','to_code'=>$stock])->select('from_code','from_desc')->get();
	foreach($data as $row){
		if($val!=NULL && $val==$row->from_code){
			echo '<option value="'.$row->from_code.'" selected>'.$row->from_code.' ('.$row->from_desc.')</option>';
		}else{
			echo '<option value="'.$row->from_code.'">'.$row->from_code.' ('.$row->from_desc.')</option>';
		}
	}
}

////////////////////// unit purchase ///////////////////////////////
function getUnitPurch($stock=NULL,$val=NULL){
	if($stock!=NULL){
		$data = DB::table('units')->where(['status'=>'1','to_code'=>$stock])->select('from_code','from_desc')->get();
		foreach($data as $row){
			if($val!=NULL && $val==$row->from_code){
				echo '<option value="'.$row->from_code.'" selected>'.$row->from_code.' ('.$row->from_desc.')</option>';
			}else{
				echo '<option value="'.$row->from_code.'">'.$row->from_code.' ('.$row->from_desc.')</option>';
			}
		}
	}
}

////////////////////// unit usage with item_id ///////////////////////////////
function getUnitUsageItem($item_id=NULL){
	if($item_id!=NULL){
		$item = Item::find($item_id);
		if($item){
			$data = DB::table('units')->where(['status'=>'1','to_code'=>$item->unit_stock])->select('from_code','from_desc')->get();
			foreach($data as $row){
				if($item->unit_usage==$row->from_code){
					echo '<option value="'.$row->from_code.'" selected>'.$row->from_code.' ('.$row->from_desc.')</option>';
				}else{
					echo '<option value="'.$row->from_code.'">'.$row->from_code.' ('.$row->from_desc.')</option>';
				}
			}
		}
	}
}

////////////////////// unit purchase with item_id ///////////////////////////////
function getUnitPurchItem($item_id=NULL,$val=NULL){
	if($item_id!=NULL){
		$item = Item::find($item_id);
		if($item){
			$data = DB::table('units')->where(['status'=>'1','to_code'=>$item->unit_stock])->select('from_code','from_desc')->get();
			foreach($data as $row){
				if($item->unit_purch==$row->from_code){
					echo '<option value="'.$row->from_code.'" selected>'.$row->from_code.' ('.$row->from_desc.')</option>';
				}else{
					echo '<option value="'.$row->from_code.'">'.$row->from_code.' ('.$row->from_desc.')</option>';
				}
			}
		}
	}
}

////////////////////// get supplier ///////////////////////////////
function getSuppliers($val=NULL){
	$data = DB::table('suppliers')->where('status','1')->select('id','name','desc')->get();
	foreach($data as $row){
		if($val!=NULL && $val==$row->id){
			echo '<option value="'.$row->id.'" selected>'.$row->name.' ('.$row->desc.')</option>';
		}else{
			echo '<option value="'.$row->id.'">'.$row->name.' ('.$row->desc.')</option>';
		}
	}
}

////////////////////// get user ///////////////////////////////
function getUsers($val=NULL){
	$data = DB::table('users')->where('id','!=',config('app.owner'))->where('id','!=',config('app.admin'))->select('id','name')->get();
	foreach($data as $row){
		if($val!=NULL && $val==$row->id){
			echo '<option value="'.$row->id.'" selected>'.$row->name.'</option>';
		}else{
			echo '<option value="'.$row->id.'">'.$row->name.'</option>';
		}
	}
}

////////////////////// get tax ///////////////////////////////
function getWarehouse($val=NULL){
	$pro_id = Session::get('project');
	$data = DB::table('warehouses')->where(['status'=>'1','pro_id'=>$pro_id])->select('id','name')->get();
	foreach($data as $row){
		if($val!=NULL && $val==$row->id){
			echo '<option value="'.$row->id.'" selected>'.$row->name.'</option>';
		}else{
			echo '<option value="'.$row->id.'">'.$row->name.'</option>';
		}
	}
}

////////////////////// get items ///////////////////////////////
function getItems($val=NULL,$cat_id = null){
	$where = ['status'=>1];
	if($cat_id != null){
		$where = array_merge($where,['cat_id'=>$cat_id]);
	}
	$data = DB::table('items')->select('id','code','name','unit_stock')->where($where)->get();
	foreach($data as $row){
		if($val!=NULL && $val==$row->id){
			echo '<option val="'.$row->unit_stock.'" value="'.$row->id.'" selected>'.$row->code.'('.$row->name.')</option>';
		}else{
			echo '<option val="'.$row->unit_stock.'" value="'.$row->id.'">'.$row->code.' ('.$row->name.')</option>';
		}
	}
}
function getItemName($val=NULL,$cat_id = null){
	$where = ['status'=>1];
	if($cat_id != null){
		$where = array_merge($where,['cat_id'=>$cat_id]);
	}
	$data = DB::table('items')->select('id','code','name','unit_stock')->where($where)->get();
	foreach($data as $row){
		if($val!=NULL && $val==$row->name){
			echo '<option val="'.$row->unit_stock.'" value="'.$row->id.'" selected>'.$row->code.'('.$row->name.')</option>';
		}else{
			echo '<option val="'.$row->unit_stock.'" value="'.$row->id.'">'.$row->code.' ('.$row->name.')</option>';
		}
	}
}
////////////////////// get tax ///////////////////////////////
function getConstructor($arr){
	$arrCon = ['',trans('lang.engineer'),trans('lang.sub_const'),trans('lang.worker'),trans('lang.security'),trans('lang.driver')];
	$data = DB::table('constructors')->where(['status'=>'1'])->whereIn('type', $arr)->select('id','name','id_card','tel','type')->get();
	foreach($data as $row){
		echo '<option value="'.$row->id.'">'.$row->id_card.' ('.$row->name.') - '.$row->tel.' - '.$arrCon[$row->type].'</option>';
	}
}