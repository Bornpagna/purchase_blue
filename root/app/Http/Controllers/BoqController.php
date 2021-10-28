<?php

namespace App\Http\Controllers;

use App\Model\Boq;
use App\Model\BoqHouse;
use Auth;
use DB;
use Datatables;
use Redirect;
use Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Excel;
use App\Model\House;
use App\Model\SystemData;
use App\Model\BoqItem;
use App\Model\BoqItemDefualt;
use App\Model\UsageDetails;
use App\Model\Item;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class BoqController extends Controller
{
	public function __boqsuct()
    {
        $this->middleware('auth');
		$this->middleware('CheckProject');
    }
	
    public function index(){
		$data = [
			'title'			=> trans('lang.boq'),
			'icon'			=> 'fa fa-bitcoin',
			'small_title'	=> trans('lang.boq_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'dashboard'	=> [
						'caption' 	=> trans('lang.boq'),
				],
			],
			'rounte'		=> url("boqs/dt"),
		];
		
		if(hasRole('boq_add')){
			$data = array_merge($data, ['rounteSave'=> url('boqs/save')]);
		}
		if(hasRole('boq_download')){
			$data = array_merge($data, ['rounteDownload'=> url('boqs/excel/download/0')]);
		}
		if(hasRole('boq_download_sample')){
			$data = array_merge($data, ['rounteExample'	=> url('boqs/excel/example')]);
			$data = array_merge($data, ['rounteUpload'=> url('boqs/excel/upload')]);
		}
		$data = array_merge($data, ['rounteUpdateBoqZone'=> url('boqs/updateBoqZone')]);
		return view('boq.index',$data);
	}
	public function reviseVersion(Request $request,$id){
		$data = [
			'title'			=> trans('lang.boq'),
			'icon'			=> 'fa fa-bitcoin',
			'small_title'	=> trans('lang.boq_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'dashboard'	=> [
						'caption' 	=> trans('lang.boq'),
				],
			],
			'rounte'		=> url("boqs/getreviseboqdt/".$id),
		];
		
		if(hasRole('boq_add')){
			$data = array_merge($data, ['rounteSave'=> url('boqs/save')]);
		}
		if(hasRole('boq_download')){
			$data = array_merge($data, ['rounteDownload'=> url('boqs/excel/download/0')]);
		}
		if(hasRole('boq_download_sample')){
			$data = array_merge($data, ['rounteExample'	=> url('boqs/excel/example')]);
			$data = array_merge($data, ['rounteUpload'=> url('boqs/excel/upload')]);
		}
		return view('boq.revise_version',$data);
	}
	public function replicateBoq(Request $request, $id){
		try {
			DB::beginTransaction();
			// Get old BOQ
			$boq = BOQ::find($id);
			// // print_r($boq);
			// //Duplicate BOQ
			// // print_r($boq->revise_count);exit;
			// $newBoq = $boq->replicate();
			
			// //Add new Value to BOQ
			// $newBoq->created_at = date('Y-m-d H:i:s');
			// $newBoq->revise_by = Auth::user()->id;
			// $newBoq->referent_id = $id;
			// $newBoq->revise_count = $newBoq->revise_count + 1;
			// $newBoq->status = 1;
			// //Save New BOQ
			// $newBoq->save();
			// $newBoqId = $newBoq->id;

			// //get old BOQ House
			$boqHouses = BoqHouse::where('boq_id',$id)->pluck('house_id');
			// if(count($boqHouses) > 0){
			// 	foreach($boqHouses as $key=> $boqHouse){
			// 		//add new Value to BOQ House
			// 		$newBoqHouse =  $boqHouse->replicate();;
			// 		$newBoqHouse->boq_id = $newBoqId;
			// 		//Save New Boq House
			// 		$newBoqHouse->save();
			// 		$newBoqHouseId = $newBoqHouse->id;
			// 		//get Boq house Items
			// 		$boqHouseItems = BoqItem::where('boq_id',$boqHouse->id)->get();
			// 		if(count($boqHouseItems) > 0){
			// 			foreach($boqHouseItems as $key=>$boqHouseItem){
			// 				//Dupplicate Boq House Item
			// 				$newBoqHouseItem =  $boqHouseItem->replicate();

			// 				//add new value to Boq House Item
			// 				$newBoqHouseItem->boq_id = $newBoqHouseId;
			// 				//save new Boq House Item
			// 				$newBoqHouseItem->save();
			// 				DB::table('boq_items')->where('id',$boqHouseItem->id)->update(['remain_qty'=>0]);
			// 			}
			// 		}
			// 	}
			// }

			// $boq->status = 0;
			// $boq->is_revise = 1;
			// DB::table('boqs')->where('id',$id)->update(['status'=>0,'is_revise'=>$boq->is_revise+1]);
			// DB::commit();
			$redirect = url('boqs');
			$items = $this->getBoqItemDefault($id);
			
			$data = [
				'id'			=> $id,
				'title'			=> trans('lang.view'),
				'icon'			=> 'fa fa-eye',
				'small_title'	=> trans('lang.boq_details'),
				'background'	=> '',
				'link'			=> [
					'home'	=> [
							'url' 		=> url('/'),
							'caption' 	=> trans('lang.home'),
					],
					'boq'	=> [
							'url' 		=> url('/boqs'),
							'caption' 	=> trans('lang.boq'),
					],
					'view'	=> [
							'caption' 	=> trans('lang.view'),
					],
				],			
				'rounte'		=> url('boqs/subdt').'/'.$id,			
				'rounteBack'	=> $redirect,
				'boqItems'=>$items,
				'boqItemJson'=>json_encode($items),
				'boq'=>$boq,
				'boqHouses'=>$boqHouses,
				'rounteBack'	=> url('/boqs'),
				
			];
			if(hasRole('revise_boq')){
				$data = array_merge($data, ['rounteSave'=> url('boqs/reviseboq/'.$id)]);
			}

			if(hasRole('boq')){
				$data = array_merge($data, ['rounteBack'=> url('/boqs')]);
			}
			$where = [
				'status'=>'1'
			];
			
			return view('boq.revise_boq',$data);
		}catch (\Exception $e) {
			DB::rollback();
			// print_r($e->getMessage());exit;
			return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
		
	}

	public function getBoq($id){
		$boq = BOQ::find($id);
		return $boq;
	}

	public function getBoqHouse($id){
		$boqHouse = BoqHouse::where('boq_id',$id)->where('status',1);
	}

	public function getBoqItem($id,$house_id = null ,$working_type=null){
		$boqItems = BoqItem::select(
			'boq_items.*',
			'items.cat_id',
			'items.name',
			'system_datas.name as working_type_name',
			DB::raw('SUM(pr_boq_items.qty_std) as total_qty')
		)->join('boq_houses','boq_houses.id','boq_items.boq_house_id')
		->join('system_datas','system_datas.id','boq_items.working_type')
		->join('items','items.id','boq_items.item_id')
		->where('boq_houses.boq_id',$id)
		->where('boq_houses.status',1);
		if($house_id != null){
			$boqItems = $boqItems->whereIn('boq_houses.house_id',[$house_id]);
		}
		if($working_type != null){
			$boqItems = $boqItems->whereIn('boq_items.working_type',$working_type);
		}
		$boqItems = $boqItems->groupBy('boq_items.item_id')
		->orderBy('boq_items.working_type')->get();
		return $boqItems;
	}

	public function getBoqItemDefault($id,$house_id = null ,$working_type=null){
		$boqItems = BoqItemDefualt::select(
			'boq_item_defualts.*',
			'items.cat_id',
			'items.name',
			'system_datas.name as working_type_name',
			DB::raw('(select pr_system_datas.name from pr_system_datas where pr_system_datas.id = pr_items.cat_id) as unit_name'))
		->join('system_datas','system_datas.id','boq_item_defualts.working_type')
		->join('items','items.id','boq_item_defualts.item_id')
		->where('boq_item_defualts.boq_id',$id);
		if($working_type != null){
			$boqItems = $boqItems->whereIn('boq_item_defualts.working_type',$working_type);
		}
		$boqItems = $boqItems->groupBy('boq_item_defualts.item_id')
		->orderBy('boq_item_defualts.working_type')->get();
		return $boqItems;
	}

 	public function reviseBoq(Request $request,$id){
		//  print_r($request->all());exit;
		try {
			DB::beginTransaction();
			// Get old BOQ
			$rules = [
				'zone_id'	=> 'required',
				'block_id' 	=> 'required',
				'building_id' 	=> 'required',
				// 'street_id'	=>'required',
				// 'house'		=>'required',
			];
			if(count($request['working_type_no'])>0){
				for($i=0; $i< count($request['working_type_no']); $i++){
					$working_type_id = $request['working_type_no'][$i];
					if(count($request["line_item_type_".$working_type_id]) > 0){
						for($j=0; $j < count($request["line_item_type_".$working_type_id]); $j++){
							$line_item_type = $request["line_item_type_".$working_type_id][$j];
							$rules['line_item_'.$working_type_id] = 'required';
							$rules['line_unit_'.$working_type_id] = 'required';
							$rules['line_qty_add_'.$working_type_id] = 'required|max:11';
							$rules['line_qty_std_'.$working_type_id] = 'required|max:11';
							$rules['line_cost_'.$working_type_id] = 'required|max:11';
						}
					}
				}
			}
			// exit;
			Validator::make($request->all(),$rules)->validate();
			$where = ['status'=>1];
			if(!empty($request['zone_id'])){
				$where = array_merge($where, ['zone_id'=>$request['zone_id']]);
			}
			if(!empty($request['block_id'])){
				$where = array_merge($where, ['block_id'=>$request['block_id']]);
			}
			if(!empty($request['building_id'])){
				$where = array_merge($where, ['building_id'=>$request['building_id']]);
			}
			if(!empty($request['house_type_id'])){
				$where = array_merge($where, ['house_type'=>$request['house_type_id']]);
			}
			
			if(!empty($request['street_id'])){
				$where = array_merge($where, ['street_id'=>$request['street_id']]);
			}
			if(count($request['house']) > 0){
				$houses = DB::table('houses')->where($where)->whereIn('id',$request['house'])->get();
			}else{
				$houses = DB::table('houses')->where($where)->get();
			}
			
			// if(count($request['line_item_type']) > 0){
			// 	for($i=0;$i<count($request['line_item_type']);$i++){
			// 		$rules['line_item_type.'.$i] = 'required';
			// 		$rules['line_item.'.$i] = 'required';
			// 		$rules['line_unit.'.$i] = 'required';
			// 		$rules['line_qty_add.'.$i] = 'required|max:11';
			// 		$rules['line_qty_std.'.$i] = 'required|max:11';
			// 	}
			// }
			
			$pro_id = $request->session()->get('project');
			$oldBoq = BOQ::find($id);
			if($oldBoq){
				if(count($houses) > 0){
					
					$boq = [
						'pro_id'		=>	$pro_id,
						'zone_id'		=>	$request["zone_id"] ? $request["zone_id"] : 0,
						'block_id'		=>	$request["block_id"] ? $request["block_id"] : 0,
						'building_id'	=>	$request["building_id"] ? $request["building_id"] : 0,
						'house_type'	=>	$request["house_type"] ? $request["house_type"] : 0,
						'street_id'		=>	$request["street_id"] ? $request["street_id"] : 0,
						'boq_code'		=> $oldBoq->boq_code."-".$oldBoq->version,
						// 'line_no'	=>getLineNo($request['house']),
						'trans_date'	=>	date('Y-m-d'),
						'trans_by'		=>	Auth::user()->id,
						'trans_type'	=>	'Entry',
						'referent_id'	=>	$id,
						'created_by'	=> 	Auth::user()->id,
						'status'		=>	1,
						// 'version'		=>	$oldBoq->version + 1
					];
					$boq_id = DB::table('boqs')->insertGetId($boq);
					
					foreach($houses as $house_key=>$house){
						$boq_house = [
							'boq_id'			=>	$boq_id,
							'house_id'			=>	$house->id,
							'boq_house_code'	=>	$oldBoq->boq_code."-".$oldBoq->version."-HOUSE-".$this->getBoqHouseCode($house->id,3),
							'created_by'		=>	Auth::user()->id,
							'created_at'		=>	date('Y-m-d H:i:s'),
							'created_by'		=>  Auth::user()->id,
							'status'			=>	1
						];
						$boq_house_id = DB::table('boq_houses')->insertGetId($boq_house);

						if(count($request["working_type_no"]) > 0){
							foreach($request["working_type_no"] as $key=>$working_type){
								if(count($request["line_item_type_".$working_type]) > 0){
									foreach($request['line_item_type_'.$working_type] as $key=>$item_type){
										$item_types = [
											'boq_id' 		=>	$boq_id,
											'boq_house_id' 	=>	$boq_house_id,
											'house_id'		=>	$house->id,
											'item_id'		=>	$request["line_item_".$working_type][$key],
											'unit'			=>	$request["line_unit_".$working_type][$key],
											'qty_std'		=>	$request["line_qty_std_".$working_type][$key],
											'qty_add'		=>	$request["line_qty_add_".$working_type][$key],
											'cost'			=>	$request["line_cost_".$working_type][$key],
											'remain_qty'	=>	$request["line_qty_std_".$working_type][$key],
											'working_type'	=>	$working_type,
											'created_at'	=>	date('Y-m-d H:i:s'),
											'created_at'	=>	Auth::user()->id,
											'is_closed'		=>	$request["is_close_".$working_type][$key]
										];
										$boq_item = DB::table('boq_items')->insertGetID($item_types);
										if($house_key == 0){
											DB::table('boq_item_defualts')->insertGetID($item_types);
										}
									}
								}
							}
						}
					}
				}

				if ($request->hasFile('document_support')) {
					$photo = upload($request,'document_support','assets/upload/document_support/');
					DB::table('boq_document_support')->insertGetID(['boq_id'=>$id,'file'=>$photo,'created_by'=>Auth::user()->id,'created_at'=>date('Y-m-d H:i:s')]);
				}
			
				$oldBoq->status = 0;
				$oldBoq->is_revise = 1;
				DB::table('boqs')->where('id',$id)->update(['status'=>0,'is_revise'=>$oldBoq->is_revise,'revise_count'=>$oldBoq->revise_count+1,'revise_by'=>Auth::user()->id,'referent_id'=>$boq_id]);
				$boqHouses = DB::table('boq_houses')->where('boq_id',$id)->get();
				if($boqHouses){
					foreach($boqHouses as $house){
						DB::table('boq_items')->where('boq_house_id',$house->id)->update(['remain_qty'=>0]);
					}
				}
			}else{
				return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
			}
			DB::commit();
			return redirect('/boqs');
		}catch (\Exception $e) {
			DB::rollback();
			print_r($e->getMessage()."-> Line".$e->getLine());exit;
			return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
	}

	public function replicateBoqByWorkingType(Request $request,$id,$type){
		
		try {
			$boq = BOQ::find($id);
			$boqHouses = BoqHouse::where('boq_id',$id)->pluck('house_id');
			$redirect = url('boqs');
			$items = $this->getBoqItem($id,$type);
			
			$data = [
				'id'			=> $id,
				'title'			=> trans('lang.view'),
				'icon'			=> 'fa fa-eye',
				'small_title'	=> trans('lang.boq_details'),
				'background'	=> '',
				'link'			=> [
					'home'	=> [
							'url' 		=> url('/'),
							'caption' 	=> trans('lang.home'),
					],
					'boq'	=> [
							'url' 		=> url('/boqs'),
							'caption' 	=> trans('lang.boq'),
					],
					'view'	=> [
							'caption' 	=> trans('lang.view'),
					],
				],			
				'rounte'		=> url('boqs/subdt').'/'.$id,			
				'rounteBack'	=> $redirect,
				'boqItems'=>$items,
				'boqItemJson'=>json_encode($items),
				'boq'=>$boq,
				'boqHouses'=>$boqHouses,
				'rounteBack'	=> url('/boqs'),
			];
			if(hasRole('revise_boq_working_type')){
				$data = array_merge($data, ['rounteSave'=> url('boqs/reviseboqworkingtype/'.$id."/".$type)]);
			}

			if(hasRole('boq')){
				$data = array_merge($data, ['rounteBack'=> url('/boqs')]);
			}
			$where = [
				'status'=>'1'
			];
			
			return view('boq.revise_boq_by_type',$data);
		}catch (\Exception $e) {
			DB::rollback();
			print_r($e->getMessage());exit;
			// return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
	}

	public function reviseBoqWorkingType(Request $request,$id,$working_id){
		try {
			DB::beginTransaction();
			// Get old BOQ
			$boq = BOQ::find($id);
			// //Duplicate BOQ
			// // print_r($boq->revise_count);exit;
			$newBoq = $boq->replicate();
			
			// //Add new Value to BOQ
			$newBoq->created_at = date('Y-m-d H:i:s');
			$newBoq->revise_by = Auth::user()->id;
			$newBoq->referent_id = $id;
			$newBoq->revise_count = $newBoq->revise_count + 1;
			$newBoq->status = 1;
			// //Save New BOQ
			$newBoq->save();
			$newBoqId = $newBoq->id;

			// //get old BOQ House
			$boqHouses = BoqHouse::where('boq_id',$id)->get();
			if(count($boqHouses) > 0){
				foreach($boqHouses as $key=> $boqHouse){
					//add new Value to BOQ House
					$newBoqHouse =  $boqHouse->replicate();
					$newBoqHouse->boq_id = $newBoqId;
					//Save New Boq House
					$newBoqHouse->save();
					$newBoqHouseId = $newBoqHouse->id;
					//get Boq house Items
					$boqHouseItems = BoqItem::where('boq_house_id',$boqHouse->id)->whereNotIn('working_type',[$working_id])->get();
					// print_r($boqHouseItems);exit;
					if(count($boqHouseItems) > 0){
						foreach($boqHouseItems as $key=>$boqHouseItem){
							//Dupplicate Boq House Item
							$newBoqHouseItem =  $boqHouseItem->replicate();

							//add new value to Boq House Item
							$newBoqHouseItem->boq_id = $newBoqId;
							$newBoqHouseItem->boq_house_id = $newBoqHouseId;
							//save new Boq House Item
							$newBoqHouseItem->save();
							
						}
					}
					DB::table('boq_items')->where('boq_house_id',$boqHouse->id)->update(['remain_qty'=>0]);
					if(count($request["working_type_no"]) > 0){
						foreach($request["working_type_no"] as $key=>$working_type){
							if(count($request["line_item_type_".$working_type]) > 0){
								foreach($request['line_item_type_'.$working_type] as $key=>$item_type){
									$item_types = [
										'boq_id' 		=>	$newBoqId,
										'boq_house_id' 	=>	$newBoqHouseId,
										'house_id'		=>	$boqHouse->id,
										'item_id'		=>	$request["line_item_".$working_type][$key],
										'unit'			=>	$request["line_unit_".$working_type][$key],
										'qty_std'		=>	$request["line_qty_std_".$working_type][$key],
										'qty_add'		=>	$request["line_qty_add_".$working_type][$key],
										'cost'			=>	$request["line_cost_".$working_type][$key],
										'remain_qty'	=>	$request["line_qty_std_".$working_type][$key],
										'working_type'	=>	$working_type,
										'created_at'	=>	date('Y-m-d H:i:s'),
										'created_at'	=>	Auth::user()->id,
									];
									$boq_item = DB::table('boq_items')->insertGetID($item_types);
									// print_r($boq_item);exit;
								}
							}
						}
					}
				}
			}

			$boq->status = 0;
			$boq->is_revise = 1;
			DB::table('boqs')->where('id',$id)->update(['status'=>0,'is_revise'=>$boq->is_revise,'revise_count'=>$boq->revise_count+1,'revise_by'=>Auth::user()->id]);
			DB::commit();
		}catch (\Exception $e) {
			DB::rollback();
			print_r($e->getMessage());exit;
			// return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
	}
	
	public function getDt()
    {
        return Datatables::of(getBOQs())
		->addColumn('action', function ($row) {
			$back =  url('/boqs');
			$rounte_download = url('boqs/excel/download/'.$row->id);
			$assign_house = url('boqs/assignHouse/'.encrypt($row->id));
			$rounte_view = url('boqs/viewBoq/'.encrypt($row->id).'/'.encrypt($back));
			$rounte_revise = url('boqs/replicateboq/'.$row->id);
			$rounte_upload_revise = url('boqs/uploadRevise/'.$row->id);
			$rounte_view_revise_version = url('boqs/reviseversion/'.$row->id);
			$rounte_delete = url('boqs/delete/'.$row->id);
			$route_add_type = url('boqs/addworktype/'.$row->id);
			$btnDelete = 'onclick="onDelete(this)"';
			$btnDownload = 'href="'.$rounte_download.'"';
			$btnView = 'onclick="onView(this)"';
			$btnUploadRevise = 'onClick="onExcelRevise(this)"';
			$btnRevise = 'href="'.$rounte_revise.'"';
			$btnAddType = 'href="'.$route_add_type.'"';
			$btnAssignHouse = 'onclick="onAssignHouse(this)"';
			$btnReviswVersion = 'href="'.$rounte_view_revise_version.'"';
			$disableClass="";
			
			
			if(hasRole('boq_download')){
				$btnDownload = "disabled";
			}
			if(BoqItem::where(['boq_id'=>$row->id])->exists()){
				$btnDelete = "disabled";
			}
			if(!hasRole('boq_delete')){
				$btnDelete = "disabled";
			}
			if(!hasRole('boq_view')){
				$btnView = "disabled";
			}
			if($row->revise_count <= 0){
				$disableClass = "disabled";
			}
			return '<div class="dropdown">
			<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			  '.trans('lang.action').' <span class="caret"></span>
			</button><ul class="dropdown-menu dropdown-menu-right">'.
			'<li class='.$disableClass.'><a '.$btnReviswVersion.' title="'.trans('lang.download').'" ><i class="fa fa-file-excel-o"></i> '.trans('lang.revise_count').'</a></li>'.
			'<li><a '.$btnDownload.' title="'.trans('lang.download').'" href="'.url('boqs/downloadBoq').'/'.$row->id.'"><i class="fa fa-file-excel-o"></i> '.trans('lang.download').'</a></li>'.
			'<li><a '.$btnAssignHouse.' title="'.trans('lang.assign_house').'" row_id="'.$row->id.'" row_rounte="'.$assign_house.'"><i class="fa fa-plus"></i> '.trans('lang.assign_house').'</a></li>'.
			'<li><a '.$btnView.' title="'.trans('lang.view').'" row_id="'.$row->id.'" row_rounte="'.$rounte_view.'"><i class="fa fa-eye"></i> '.trans('lang.view').'</a></li>'.
			'<li><a '.$btnRevise.' title="'.trans('lang.manual_revised_boq').'" row_id="'.$row->id.'" row_rounte="'.$rounte_revise.'"><i class="fa fa-pencil-square-o"></i> '.trans('lang.manual_revised_boq').'</a></li>'.
			// '<li><a '.$btnUploadRevise.' title="'.trans('lang.import_revised_boq').'" row_id="'.$row->id.'" row_rounte="'.$rounte_upload_revise.'"><i class="fa fa-pencil-square-o"></i> '.trans('lang.import_revised_boq').'</a></li>'.
			'<li><a '.$btnDelete.' title="'.trans('lang.delete').'" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'"><i class="fa fa-trash"></i> '.trans('lang.delete').'</a></li>'.
			// '<li><a '.$btnAddType.' title="'.trans('lang.add_working_type').'" row_id="'.$row->id.'" row_rounte="'.$route_add_type.'"><i class="fa fa-plus"></i> '.trans('lang.add_working_type').'</a></li>'.
			'</ul></div>';
		})->addColumn('details_url',function($row){
            // return url('boqs/subdt').'/'.$row->id;
			// return url('boqs/boqworkingtypedt').'/'.$row->id;
			return url('boqs/boqhousesdt').'/'.$row->id;
        })->make(true);
    }

	public function getReviseBoqDt($id)
    {
        return Datatables::of(getBOQReviseVersions($id))
		->addColumn('action', function ($row) {
			$back =  url('/boqs');
			$rounte_view = url('boqs/viewreviseboq/'.encrypt($row->id).'/'.encrypt($back));
			$btnView = 'onclick="onView(this)"';
			return
				'<a '.$btnView.' title="'.trans('lang.view').'" class="btn btn-xs view-record" row_id="'.$row->id.'" row_rounte="'.$rounte_view.'">'.
				'	<i class="fa fa-eye"></i><br /><span>'.trans('lang.view').'</span>'.
				'</a>'; 
			
		})->addColumn('details_url',function($row){
			return url('boqs/getreviseboqhousedt').'/'.$row->id;
        })->make(true);
    }
	public function boqHousesDt(Request $request, $id){
		return Datatables::of(getReviseBoqHouses($id))
		->addColumn('action', function ($row) {
			$rounte_revise_boq_house = url('boqs/reviseBoqHouseView/'.$row->boq_id."/".$row->house_id);
			$rounte_revise_boq_house_excel = url('boqs/reviseBoqHouseViewExcel/'.$row->boq_id."/".$row->house_id);
			$rounte_delete = url('boqs/sub/delete/'.$row->id);
			$btnDelete = 'onclick="onDelete(this)"';
			$btnReviseBoqHouse = 'onClick="onReviseBoqHouse(this)"';
			$btnReviseBoqHouseUpload = 'onClick="onExcelRevise(this)"';
			$btnReviseBoqHouseExcel = 'onClick="onReviseBoqHouseExcel(this)"';
			$btnView = 'onclick="onViewBoqHouse(this)"';
			$rounte_view = url('boqs/view/'.encrypt($row->boq_id)."/".encrypt(1).'/'.encrypt($row->house_id));			
			if(!hasRole('boq_edit')){
				$btnEdit = "disabled";
			}
			return				
				'<div class="actions">
				<div class="btn btn-xs"><a '.$btnReviseBoqHouseUpload.' title="'.trans('lang.upload').'" class="revise-record btnUpload" row_id="'.$row->id.'" row_rounte="'.$rounte_revise_boq_house_excel.'">'.
				'	<i class="fa fa-upload"></i><br />'.trans("lang.upload").
				'</a></div>'.
				'<div class="btn btn-xs"><a '.$btnReviseBoqHouse.' title="'.trans('lang.revise_boq').'" class="revise-record" row_id="'.$row->id.'" row_rounte="'.$rounte_revise_boq_house.'">'.
				'	<i class="fa fa-edit"></i><br />'.trans("lang.revise_boq").
				'</a></div>'.
				'<div class="btn btn-xs"><a '.$btnView.' title="'.trans('lang.view').'" class="view-record" row_id="'.$row->id.'" row_rounte="'.$rounte_view.'">'.
				'	<i class="fa fa-eye"></i><br />'.trans("lang.view").
				'</a></div>'.
				// '<div class="btn btn-xs"><a '.$btnReviseBoqHouseExcel.' title="'.trans('lang.import_excel').'" class="view-record" row_id="'.$row->id.'" row_rounte="'.$rounte_revise_boq_house_excel.'">'.
				// '	<i class="fa fa-eye"></i><br />'.trans("lang.import_excel").
				// '</a></div>'.
				'<div class="btn btn-xs"><a '.$btnDelete.' title="'.trans('lang.delete').'" class="delete-boq-record" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'">'.
				'	<i class="fa fa-trash"></i><br />'.trans("lang.delete").
				'</a></div></div>'; 
		})->make(true);
	}

	public function workingTypeDt(Request $request, $id){
		return Datatables::of(getBoqWorkingType($id))
		->addColumn('action', function ($row) {
			$rounte_edit = url('boqs/edit/'.$row->id);
			$rounte_delete = url('boqs/sub/delete/'.$row->id);
			$btnDelete = 'onclick="onDelete(this)"';
			$btnEdit = 'onclick="onEdit(this)"';
			$btnView = 'onclick="onView(this)"';
			$rounte_view = url('boqs/view/'.encrypt($row->id).'/'.encrypt(1));
			
			if(!hasRole('boq_edit')){
				$btnEdit = "disabled";
			}
			// if(UsageDetails::where(['house_id'=>$row->house_id,'item_id'=>$row->item_id])->exists()){
			// 	$btnDelete = "disabled";
			// }
			// if(!hasRole('boq_delete')){
			// 	$btnDelete = "disabled";
			// }
			return
				'<a '.$btnEdit.' title="'.trans('lang.edit').'" class="btn btn-xs yellow edit-record" row_id="'.$row->id.'" row_rounte="'.$rounte_edit.'">'.
				'	<i class="fa fa-edit"></i>'.
				'</a>'.
				'<a '.$btnView.' title="'.trans('lang.view').'" class="btn btn-xs yellow view-record" row_id="'.$row->id.'" row_rounte="'.$rounte_view.'">'.
				'	<i class="fa fa-eye"></i>'.
				'<a '.$btnDelete.' title="'.trans('lang.delete').'" class="btn btn-xs red delete-boq-record" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'">'.
				'	<i class="fa fa-trash"></i>'.
				'</a>'; 
		})->addColumn('details_url',function($row){
            // return url('boqs/subdt').'/'.$row->id;
			return url('boqs/boqworkingtypedt').'/'.$row->id;
        })->make(true);
	}
	public function subWorkingTypeDt(Request $request,$id){
		return Datatables::of(getBOQItemByWorkingType($id))
		->addColumn('action', function ($row) {
			$rounte_edit = url('boqs/edit/'.$row->id);
			$rounte_delete = url('boqs/sub/delete/'.$row->id);
			$btnDelete = 'onclick="onDelete(this)"';
			$btnEdit = 'onclick="onEdit(this)"';
			
			if(!hasRole('boq_edit')){
				$btnEdit = "disabled";
			}
			if(UsageDetails::where(['house_id'=>$row->house_id,'item_id'=>$row->item_id])->exists()){
				$btnDelete = "disabled";
			}
			if(!hasRole('boq_delete')){
				$btnDelete = "disabled";
			}
			return
				'<a '.$btnEdit.' title="'.trans('lang.edit').'" class="btn btn-xs yellow edit-record" row_id="'.$row->id.'" row_rounte="'.$rounte_edit.'">'.
				'	<i class="fa fa-edit"></i>'.
				'</a>'.
				'<a '.$btnDelete.' title="'.trans('lang.delete').'" class="btn btn-xs red delete-boq-record" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'">'.
				'	<i class="fa fa-trash"></i>'.
				'</a>'; 
		})->make(true);
	}
	public function reviseBoqHousesDt(Request $request, $id){
		return Datatables::of(getReviseBoqHouses($id))
		->addColumn('action', function ($row) {
			$rounte_revise_boq_house = url('boqs/reviseBoqHouseView/'.$row->boq_id."/".$row->house_id);
			$rounte_revise_boq_house_excel = url('boqs/reviseBoqHouseView/'.$row->boq_id."/".$row->house_id);
			$rounte_delete = url('boqs/sub/delete/'.$row->id);
			$btnDelete = 'onclick="onDelete(this)"';
			$btnReviseBoqHouse = 'onClick="onReviseBoqHouse(this)"';
			$btnReviseBoqHouseExcel = 'onClick="onReviseBoqHouseExcel(this)"';
			$btnView = 'onclick="onViewBoqHouse(this)"';
			$rounte_view = url('boqs/view/'.encrypt($row->boq_id)."/".encrypt(1).'/'.encrypt($row->house_id));			
			if(!hasRole('boq_edit')){
				$btnEdit = "disabled";
			}
			return
				'<div class="btn btn-xs"><a '.$btnView.' title="'.trans('lang.view').'" class="view-record" row_id="'.$row->id.'" row_rounte="'.$rounte_view.'">'.
				'	<i class="fa fa-eye"></i><br />'.trans("lang.view").
				'</a></div>'; 
		})->make(true);
	}

	public function boqHousesViewDt(Request $request, $id){
		return Datatables::of(getBoqHouses($id))
		->addColumn('action', function ($row) {
			$rounte_revise_boq_house = url('boqs/reviseBoqHouseView/'.$row->boq_id."/".$row->house_id);
			$rounte_delete = url('boqs/sub/delete/'.$row->id);
			$btnDelete = 'onclick="onDelete(this)"';
			$btnReviseBoqHouse = 'onClick="onReviseBoqHouse(this)"';
			$btnView = 'onclick="onViewBoqHouse(this)"';
			$rounte_view = url('boqs/view/'.encrypt($row->boq_id)."/".encrypt(1).'/'.encrypt($row->house_id));
			
			if(!hasRole('boq_edit')){
				$btnEdit = "disabled";
			}
			// if(UsageDetails::where(['house_id'=>$row->house_id,'item_id'=>$row->item_id])->exists()){
			// 	$btnDelete = "disabled";
			// }
			// if(!hasRole('boq_delete')){
			// 	$btnDelete = "disabled";
			// }
			return
				'<div class="actions"><div class="btn btn-xs"><a '.$btnReviseBoqHouse.' title="'.trans('lang.revise_boq').'" class="revise-record" row_id="'.$row->id.'" row_rounte="'.$rounte_revise_boq_house.'">'.
				'	<i class="fa fa-edit"></i><br />'.trans("lang.revise_boq").
				'</a></div>'.
				'<div class="btn btn-xs"><a '.$btnView.' title="'.trans('lang.view').'" class="view-record" row_id="'.$row->id.'" row_rounte="'.$rounte_view.'">'.
				'	<i class="fa fa-eye"></i><br />'.trans("lang.view").
				'</a></div>'.
				'<div class="btn btn-xs"><a '.$btnDelete.' title="'.trans('lang.delete').'" class="delete-boq-record" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'">'.
				'	<i class="fa fa-trash"></i><br />'.trans("lang.delete").
				'</a></div></div>'; 
		})->addColumn('details_url',function($row){
            return url('boqs/getBoqItems').'/'.$row->boq_id."/".$row->house_id;
			// return url('boqs/boqworkingtypedt').'/'.$row->id;
        })->make(true);
	}
	public function reviseBoqHousesViewDt(Request $request, $id){
		return Datatables::of(getReviseBoqHouses($id))
		->addColumn('action', function ($row) {
			$rounte_revise_boq_house = url('boqs/reviseBoqHouseView/'.$row->boq_id."/".$row->house_id);
			$rounte_delete = url('boqs/sub/delete/'.$row->id);
			$btnDelete = 'onclick="onDelete(this)"';
			$btnReviseBoqHouse = 'onClick="onReviseBoqHouse(this)"';
			$btnView = 'onclick="onViewBoqHouse(this)"';
			$rounte_view = url('boqs/view/'.encrypt($row->boq_id)."/".encrypt(1).'/'.encrypt($row->house_id));
			
			if(!hasRole('boq_edit')){
				$btnEdit = "disabled";
			}
			// if(UsageDetails::where(['house_id'=>$row->house_id,'item_id'=>$row->item_id])->exists()){
			// 	$btnDelete = "disabled";
			// }
			// if(!hasRole('boq_delete')){
			// 	$btnDelete = "disabled";
			// }
			return
				'<div class="actions"><div class="btn btn-xs"><a '.$btnReviseBoqHouse.' title="'.trans('lang.revise_boq').'" class="revise-record" row_id="'.$row->id.'" row_rounte="'.$rounte_revise_boq_house.'">'.
				'	<i class="fa fa-edit"></i><br />'.trans("lang.revise_boq").
				'</a></div>'.
				'<div class="btn btn-xs"><a '.$btnView.' title="'.trans('lang.view').'" class="view-record" row_id="'.$row->id.'" row_rounte="'.$rounte_view.'">'.
				'	<i class="fa fa-eye"></i><br />'.trans("lang.view").
				'</a></div>'.
				'<div class="btn btn-xs"><a '.$btnDelete.' title="'.trans('lang.delete').'" class="delete-boq-record" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'">'.
				'	<i class="fa fa-trash"></i><br />'.trans("lang.delete").
				'</a></div></div>'; 
		})->addColumn('details_url',function($row){
            return url('boqs/getBoqItems').'/'.$row->boq_id."/".$row->house_id;
			// return url('boqs/boqworkingtypedt').'/'.$row->id;
        })->make(true);
	}

	public function boqHouses(Request $request, $id){
		return Datatables::of(getBoqHouses($id))
		->addColumn('action', function ($row) {
			$rounte_revise_boq_house = url('boqs/reviseBoqHouseView/'.$row->boq_id."/".$row->house_id);
			$rounte_revise_boq_house_excel = url('boqs/reviseBoqHouseView/'.$row->boq_id."/".$row->house_id);
			$rounte_delete = url('boqs/sub/delete/'.$row->id);
			$btnDelete = 'onclick="onDelete(this)"';
			$btnReviseBoqHouse = 'onClick="onReviseBoqHouse(this)"';
			$btnReviseBoqHouseExcel = 'onClick="onReviseBoqHouseExcel(this)"';
			$btnView = 'onclick="onViewBoqHouse(this)"';
			$rounte_view = url('boqs/view/'.encrypt($row->boq_id).'/'.encrypt(1)."/".encrypt($row->house_id));
			
			if(!hasRole('boq_edit')){ 
				$btnEdit = "disabled";
			}
			// if(UsageDetails::where(['house_id'=>$row->house_id,'item_id'=>$row->item_id])->exists()){
			// 	$btnDelete = "disabled";
			// }
			// if(!hasRole('boq_delete')){
			// 	$btnDelete = "disabled";
			// }
			return
				'<div class="actions"><div class="btn btn-xs"><a '.$btnReviseBoqHouse.' title="'.trans('lang.revise_boq').'" class="revise-record" row_id="'.$row->id.'" row_rounte="'.$rounte_revise_boq_house.'">'.
				'	<i class="fa fa-edit"></i><br />'.trans("lang.revise_boq").
				'</a></div>'.
				'<div class="btn btn-xs"><a '.$btnView.' title="'.trans('lang.view').'" class="view-record" row_id="'.$row->id.'" row_rounte="'.$rounte_view.'">'.
				'	<i class="fa fa-eye"></i><br />'.trans("lang.view").
				'</a></div>'.
				'<div class="btn btn-xs"><a '.$btnReviseBoqHouseExcel.' title="'.trans('lang.view').'" class="view-record" row_id="'.$row->id.'" row_rounte="'.$rounte_revise_boq_house_excel.'">'.
				'	<i class="fa fa-eye"></i><br />'.trans("lang.import").
				'</a></div>'.
				'<div class="btn btn-xs"><a '.$btnDelete.' title="'.trans('lang.delete').'" class="delete-boq-record" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'">'.
				'	<i class="fa fa-trash"></i><br />'.trans("lang.delete").
				'</a></div></div>'; 
		})->make(true);
	}
	public function getBoqHouseItem($id,$house_id = null ,$working_type=null){
		return Datatables::of(getBoqHouseItems($id,$house_id,$working_type))
		->addColumn('action', function ($row) {
			$rounte_edit = url('boqs/edit/'.$row->id);
			$rounte_delete = url('boqs/sub/delete/'.$row->id);
			$btnDelete = 'onclick="onDelete(this)"';
			$btnEdit = 'onclick="onEdit(this)"';
			$btnView = 'onclick="onView(this)"';
			$rounte_view = url('boqs/view/'.encrypt($row->boq_id)."/".encrypt($row->house_id).'/'.encrypt(1));
			
			if(!hasRole('boq_edit')){
				$btnEdit = "disabled";
			}
			// if(UsageDetails::where(['house_id'=>$row->house_id,'item_id'=>$row->item_id])->exists()){
			// 	$btnDelete = "disabled";
			// }
			// if(!hasRole('boq_delete')){
			// 	$btnDelete = "disabled";
			// }
			// return
			// 	'<div class="actions"><div class="btn btn-xs"><a '.$btnEdit.' title="'.trans('lang.revise_boq').'" class="edit-record" row_id="'.$row->id.'" row_rounte="'.$rounte_edit.'">'.
			// 	'	<i class="fa fa-edit"></i><br />'.trans("lang.revise_boq").
			// 	'</a></div>'.
			// 	'<div class="btn btn-xs"><a '.$btnView.' title="'.trans('lang.view').'" class="view-record" row_id="'.$row->id.'" row_rounte="'.$rounte_view.'">'.
			// 	'	<i class="fa fa-eye"></i><br />'.trans("lang.view").
			// 	'</a></div>'.
			// 	'<div class="btn btn-xs"><a '.$btnDelete.' title="'.trans('lang.delete').'" class="delete-boq-record" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'">'.
			// 	'	<i class="fa fa-trash"></i><br />'.trans("lang.delete").
			// 	'</a></div></div>'; 
		})->make(true);
		
	}
	public function subDt(Request $request, $id)
    {
        return Datatables::of(getBOQItems($id))
		->addColumn('action', function ($row) {
			$rounte_edit = url('boqs/edit/'.$row->id);
			$rounte_delete = url('boqs/sub/delete/'.$row->id);
			$btnDelete = 'onclick="onDelete(this)"';
			$btnEdit = 'onclick="onEdit(this)"';
			
			if(!hasRole('boq_edit')){
				$btnEdit = "disabled";
			}
			if(UsageDetails::where(['house_id'=>$row->house_id,'item_id'=>$row->item_id])->exists()){
				$btnDelete = "disabled";
			}
			if(!hasRole('boq_delete')){
				$btnDelete = "disabled";
			}
			return
				'<a '.$btnEdit.' title="'.trans('lang.edit').'" class="btn btn-xs yellow edit-record" row_id="'.$row->id.'" row_rounte="'.$rounte_edit.'">'.
				'	<i class="fa fa-edit"></i>'.
				'</a>'.
				'<a '.$btnDelete.' title="'.trans('lang.delete').'" class="btn btn-xs red delete-boq-record" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'">'.
				'	<i class="fa fa-trash"></i>'.
				'</a>'; 
		})->make(true);
	}
	public function getBoqCode(){
		$last_id = DB::table('boqs')->select('id')->orderBy('id','desc')->first();
		if($last_id){
			return formatZero($last_id->id+1,3);
		}else{
			return formatZero(1,3);
		}
	}

	public function getBoqHouseCode($code){
		return formatZero($code,3);
	}

    public function save(Request $request){
		try {
			$rules = [
				'zone_id'	=> 'required',
				'block_id' 	=> 'required',
				'building_id' 	=> 'required',
				// 'street_id'	=>'required',
				// 'house'		=>'required',
			];
			
			// if(count($request['working_type_no'])>0){
			// 	for($i=0; $i< count($request['working_type_no']); $i++){
			// 		$working_type_id = $request['working_type_no'][$i];
			// 		if(count($request["line_item_type_".$working_type_id]) > 0){
			// 			for($j=0; $j < count($request["line_item_type_".$working_type_id]); $j++){
			// 				$line_item_type = $request["line_item_type_".$working_type_id][$j];
			// 				$rules['line_item_'.$working_type_id] = 'required';
			// 				$rules['line_unit_'.$working_type_id] = 'required';
			// 				$rules['line_qty_add_'.$working_type_id] = 'required|max:11';
			// 				$rules['line_qty_std_'.$working_type_id] = 'required|max:11';
			// 				$rules['line_cost_'.$working_type_id] = 'required|max:11';
			// 			}
			// 		}
			// 	}
			// }
			// // exit;
			// Validator::make($request->all(),$rules)->validate();
			// print_r($request["building_id"]);exit;
			DB::beginTransaction();
			if(is_array($request["building_id"])){
				if(count($request["building_id"]) > 0){
					foreach($request["building_id"] as $key=>$building){
						$this->insertBoqHouse($request,$building);
					}
				}
			}else{
				$this->insertBoqHouse($request,$request["building_id"]);
			}
			
			DB::commit();
			return redirect()->back()->with('success',trans('lang.save_success'));
		} catch (\Exception $e) {
			DB::rollback();
			print_r($e->getMessage().$e->getLine());
			return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
	}

	function insertBoqHouse(Request $request,$building_id=null){
		$where = [];
			if(!empty($request['zone_id'])){
				$where = array_merge($where, ['zone_id'=>$request['zone_id']]);
			}
			if(!empty($request['block_id'])){
				$where = array_merge($where, ['block_id'=>$request['block_id']]);
			}
			if(!empty($building_id)){
				$where = array_merge($where, ['building_id'=>$building_id]);
			}
			if(!empty($request['house_type_id'])){
				$where = array_merge($where, ['house_type'=>$request['house_type_id']]);
			}
			if(!empty($request['street_id'])){
				$where = array_merge($where, ['street_id'=>$request['street_id']]);
			}
			if(!empty($request['house']) && count($request['house']) > 0){
				$houses = DB::table('houses')->where($where)->whereIn('id',$request['house'])->get();
			}else{
				$houses = DB::table('houses')->where($where)->get();
			}			
					
			
			$pro_id = $request->session()->get('project');
			$boqCode = 	"BOQ-".$this->getBoqCode();	
			if(count($houses) > 0){
				$boq = [
					'pro_id'		=>	$pro_id,
					'zone_id'		=>	$request["zone_id"] ? $request["zone_id"] : 0,
					'block_id'		=>	$request["block_id"] ? $request["block_id"] : 0,
					'building_id'	=>	$building_id ? $building_id : 0,
					'house_type'	=>	$request["house_type_id"] ? $request["house_type_id"] : 0,
					'street_id'		=>	$request["street_id"] ? $request["street_id"] : 0,
					'boq_code'		=> $boqCode,
					// 'line_no'	=>getLineNo($request['house']),
					'trans_date'	=>	date('Y-m-d'),
					'trans_by'		=>	Auth::user()->id,
					'trans_type'	=>	'Entry',
					'created_by'	=> 	Auth::user()->id,
					'status'		=>	1,
					'version'		=>	1,
				];
				$boq_id = DB::table('boqs')->insertGetId($boq);
				foreach($houses as $house_key=>$house){
					$boq_house = [
						'boq_id'			=>	$boq_id,
						'house_id'			=>	$house->id,
						'boq_house_code'	=>	$boqCode."-HOUSE-".$this->getBoqHouseCode($house->id,3),
						'created_by'		=>	Auth::user()->id,
						'created_at'		=>	date('Y-m-d H:i:s'),
						'created_by'	=> Auth::user()->id,
						'status'		=>	1,
					];
					$boq_house_id = DB::table('boq_houses')->insertGetId($boq_house);
					if(count($request["working_type_no"]) > 0){
						foreach($request["working_type_no"] as $key=>$working_type){
							if(count($request["line_item_type_".$working_type]) > 0){
								foreach($request['line_item_type_'.$working_type] as $key=>$item_type){
									$item_types = [
										'boq_id' 		=>	$boq_id,
										'boq_house_id' 	=>	$boq_house_id,
										'house_id'		=>	$house->id,
										'item_id'		=>	$request["line_item_".$working_type][$key],
										'unit'			=>	$request["line_unit_".$working_type][$key],
										'qty_std'		=>	$request["line_qty_std_".$working_type][$key],
										'qty_add'		=>	$request["line_qty_add_".$working_type][$key],
										'cost'			=>	$request["line_cost_".$working_type][$key],
										'remain_qty'	=>	$request["line_qty_std_".$working_type][$key],
										'working_type'	=>	$working_type,
										'created_at'	=>	date('Y-m-d H:i:s'),
										'created_at'	=>	Auth::user()->id,
										// 'status'		=>	1,
									];
									$boq_item = DB::table('boq_items')->insertGetID($item_types);
									if($house_key == 0){
										$boq_item_defualt = DB::table('boq_item_defualts')->insertGetID($item_types);
									}
								}
							}
						}
					}
				}				
				
			}
	}

    public function update(Request $request,$id)
    {
		$rules = [
				'street_id'	=>'required',
				'house_id' 	=>'required',
				'item_id' 	=>'required',
				'unit' 		=>'required',
				'qty_std' 	=>'required|max:11',
				'qty_add' 	=>'required|max:11',
			];
    	Validator::make($request->all(),$rules)->validate();
    	DB::beginTransaction();
		try {
			$data = [
				'house_id'	=>$request->house_id,
				'item_id'	=>$request->item_id,
				'unit'		=>$request->unit,
				'qty_std'	=>$request->qty_std,
				'qty_add'	=>$request->qty_add,
				'updated_by'=>Auth::user()->id,
				'updated_at'=>date('Y-m-d H:i:s'),
			];
			DB::table('boq_items')->where(['id'=>$id])->update($data);
			DB::commit();
			return redirect()->back()->with('success',trans('lang.update_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.update_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }
	
	function view(Request $request, $id, $back,$house_id){
		
		$id = decrypt($id);
		$back = decrypt($back);
		$house_id = decrypt($house_id);
		$redirect = url('boqs');
		if($back==2){
			$redirect = url('house');
		}
		$data = [
			'id'			=> $id,
			'title'			=> trans('lang.view'),
			'icon'			=> 'fa fa-eye',
			'small_title'	=> trans('lang.boq_details'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'boq'	=> [
						'url' 		=> url('/boqs'),
						'caption' 	=> trans('lang.boq'),
				],
				'view'	=> [
						'caption' 	=> trans('lang.view'),
				],
			],			
			'rounte'		=> url('boqs/boqhouseworkingtypedt').'/'.$id."/".$house_id,			
			'rounteBack'	=> $redirect,
		];
		return view('boq.view',$data);
	}
	function viewBoq(Request $request, $id, $back){
		
		$id = decrypt($id);
		$back = decrypt($back);
		$redirect = url('boqs');
		if($back==2){
			$redirect = url('house');
		}
		$data = [
			'id'			=> $id,
			'title'			=> trans('lang.view'),
			'icon'			=> 'fa fa-eye',
			'small_title'	=> trans('lang.boq_details'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'boq'	=> [
						'url' 		=> url('/boqs'),
						'caption' 	=> trans('lang.boq'),
				],
				'view'	=> [
						'caption' 	=> trans('lang.view'),
				],
			],			
			'rounte'		=> url('boqs/boqhousesviewdt').'/'.$id,			
			'rounteBack'	=> $redirect,
		];
		return view('boq.viewBoq',$data);
	}
	function viewReviseBoq(Request $request, $id, $back){
		$id = decrypt($id);
		$back = decrypt($back);
		$redirect = url('boqs');
		if($back==2){
			$redirect = url('house');
		}
		$data = [
			'id'			=> $id,
			'title'			=> trans('lang.view'),
			'icon'			=> 'fa fa-eye',
			'small_title'	=> trans('lang.boq_details'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'boq'	=> [
						'url' 		=> url('/boqs'),
						'caption' 	=> trans('lang.boq'),
				],
				'view'	=> [
						'caption' 	=> trans('lang.view'),
				],
			],			
			'rounte'		=> url('boqs/reviseboqhousesviewdts').'/'.$id,			
			'rounteBack'	=> $redirect,
		];
		return view('boq.viewReviseBoq',$data);
	}

	public function getHouseWorkingType(Request $request,$id,$house_id)
    {
        return Datatables::of(getBoqWorkingType($id,$house_id))
		->addColumn('action', function ($row) {
			$rounte_download = url('boqs/excel/download/'.$row->id);
			$rounte_view = url('boqs/view/'.encrypt($row->id).'/'.encrypt(1));
			$rounte_revise = url('boqs/replicateboq/'.$row->id);
			$rounte_delete = url('boqs/delete/'.$row->id);
			$route_add_type = url('boqs/addworktype/'.$row->id);
			$btnDelete = 'onclick="onDelete(this)"';
			$btnDownload = 'href="'.$rounte_download.'"';
			$btnView = 'onclick="onView(this)"';
			$btnRevise = 'href="'.$rounte_revise.'"';
			$btnAddType = 'href="'.$route_add_type.'"';
			
			if(hasRole('boq_download')){
				$btnDownload = "disabled";
			}
			if(BoqItem::where(['boq_id'=>$row->id])->exists()){
				$btnDelete = "disabled";
			}
			if(!hasRole('boq_delete')){
				$btnDelete = "disabled";
			}
			if(!hasRole('boq_view')){
				$btnView = "disabled";
			}
			return
				
				'<a '.$btnView.' title="'.trans('lang.view').'" class="btn btn-xs view-record" row_id="'.$row->working_type.'" row_rounte="'.$rounte_view.'">'.
				'	<i class="fa fa-eye"></i><br /><span>'.trans('lang.view').'</span>'.
				'</a>'.
				'<a '.$btnRevise.' title="'.trans('lang.revised_boq').'" class="btn btn-xs view-record" row_id="'.$row->working_type.'" row_rounte="'.$rounte_revise.'">'.
				'	<i class="fa fa-pencil-square-o"></i><br /><span>'.trans('lang.revised_boq').'</span>'.
				'</a>'.
				'<a '.$btnDelete.' title="'.trans('lang.delete').'" class="btn btn-xs delete-boq-record" row_id="'.$row->working_type.'" row_rounte="'.$rounte_delete.'">'.
				'	<i class="fa fa-trash"></i><br /><span>'.trans('lang.delete').'</span>'.
				'</a>'; 
			// return '<div class="dropdown">
			// <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			//   '.trans('lang.action').' <span class="caret"></span>
			// </button><ul class="dropdown-menu dropdown-menu-right">'.
			// '<li><a '.$btnDownload.' title="'.trans('lang.download').'"><i class="fa fa-file-excel-o"></i> '.trans('lang.download').'</a></li>'.
			// '<li><a '.$btnView.' title="'.trans('lang.view').'" row_id="'.$row->id.'" row_rounte="'.$rounte_view.'"><i class="fa fa-eye"></i> '.trans('lang.view').'</a></li>'.
			// '<li><a '.$btnRevise.' title="'.trans('lang.revised_boq').'" row_id="'.$row->id.'" row_rounte="'.$rounte_revise.'"><i class="fa fa-pencil-square-o"></i> '.trans('lang.revised_boq').'</a></li>'.
			// '<li><a '.$btnDelete.' title="'.trans('lang.delete').'" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'"><i class="fa fa-trash"></i> '.trans('lang.delete').'</a></li>'.
			// '<li><a '.$btnAddType.' title="'.trans('lang.add_working_type').'" row_id="'.$row->id.'" row_rounte="'.$route_add_type.'"><i class="fa fa-plus"></i> '.trans('lang.add_working_type').'</a></li>'.
			// '</ul></div>';
		})->addColumn('details_url',function($row){
            // return url('boqs/subdt').'/'.$row->id;
			// return url('boqs/boqworkingtypedt').'/'.$row->id;
			return url('boqs/getBoqItems')."/".$row->boq_id.'/'.$row->house_id."/".$row->working_type;
        })->make(true);
    }
	
	public function destroy($id)
    {
		try {
			DB::beginTransaction();
			DB::table('boqs')->where(['id'=>$id])->delete();
			DB::commit();
			return redirect()->back()->with('success',trans('lang.delete_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.delete_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }
	
	public function destroySub($id)
    {
		try {
			DB::beginTransaction();
			DB::table('boq_items')->where(['id'=>$id])->delete();
			DB::commit();
			return redirect()->back()->with('success',trans('lang.delete_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.delete_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }
	
	public function downloadExample(Request $request)
   	{
		Excel::create('example_file_upload_boq_house_house_type',function($excel) {
   			$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
   			$excel->sheet('Upload BOQ',function($sheet){
   				$sheet->cell('A1', 'No');
   				$sheet->cell('B1', 'Item Type');
   				$sheet->cell('C1', 'Code');
   				$sheet->cell('D1', 'Items Name');
   				$sheet->cell('E1', 'Unit');
  				$sheet->cell('F1', 'Qty Std');
   				$sheet->cell('G1', 'Qty Add');
  				$sheet->cell('H1', 'Cost');
   			});
   		})->download('xlsx');
   	}
	public function downloadExampleById($id)
   	{
		Excel::create('example_file_upload_boq_house_house_type',function($excel)  use($id){
   			$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
   			$excel->sheet('Upload BOQ',function($sheet) use ($id){
				$cells = 1;
   				$sheet->cell('A1', 'No');
   				$sheet->cell('B1', 'Item Type');
   				$sheet->cell('C1', 'Code');
   				$sheet->cell('D1', 'Items Name');
   				$sheet->cell('E1', 'Unit');
  				$sheet->cell('F1', 'Qty Std');
   				$sheet->cell('G1', 'Qty Add');
  				$sheet->cell('H1', 'Cost');
				$boq = getBOQExcel($id);
				$group = getBOQWorkType($id);	
				if(!empty($group)){
					foreach($group as $rows){
						$cells++;
						$sheet->cell('A'.($cells),$rows->working_type_name);
						$sheet->cell('B'.($cells),"");
						$sheet->cell('C'.($cells),"");
						$sheet->cell('D'.($cells),"");
						$sheet->cell('E'.($cells),"");
						$sheet->cell('F'.($cells),"");
						$sheet->cell('G'.($cells),"");
						$sheet->cell('H'.($cells),"");
						if(!empty($boq)){
							foreach($boq as $value){
								if($value->working_type==$rows->working_type){
									$cells++;
									$sheet->cell('A'.($cells),'');
									$sheet->cell('B'.($cells),$value->item_type);
									$sheet->cell('C'.($cells),$value->code);
									$sheet->cell('D'.($cells),$value->name);
									$sheet->cell('E'.($cells),$value->unit);
									$sheet->cell('F'.($cells),$value->qty_std);
									$sheet->cell('G'.($cells),$value->qty_add);
									$sheet->cell('H'.($cells),$value->cost);
								}
							}
						}						
					}
				}
   			});		
			
   		})->download('xlsx');
   	}

   	public function uploadExcel(Request $request)
   	{		
   		try {
   			if($request->hasFile('excel')){
				$fileUpdate = $request->file('excel');
				$data = Excel::load($fileUpdate->getRealPath(), function($reader) {})->get();
				$error = '';
				$house_id = 0;
				$house_type = 0;
				$main_boq_id = 0;
				if(!empty($data) && $data->count()){
					DB::beginTransaction();										
					// foreach ($data as $key => $value) {		
					// 	if (count($value)==7) {
							// if ($value->house_type) {
							// 	if ($value->item && $value->unit && stringValue($value->qty_std) && stringValue($value->qty_add)) {
							// 		$houseTypeObj = SystemData::where('name',$value->house_type)->where('type','HT')->first();
							// 		$itemObj  = Item::where('code',$value->item)->first();
							// 		if (empty($houseTypeObj)) {
							// 			$request->session()->flash('error','House Type not found');
							// 			return redirect()->back();
							// 		}

							// 		$houseObj = House::where('house_type',$houseTypeObj->id)->get();

							// 		if(empty($houseObj)){
							// 			$request->session()->flash('error','House not found');
							// 			return redirect()->back();
							// 		}

							// 		if (empty($itemObj)) {
							// 			$request->session()->flash('error','Item not found');
							// 			return redirect()->back();
							// 		}

							// 		foreach ($houseObj as $key => $val) {
							// 			$boq_insert = [
							// 				'house_id'   =>$val->id,
							// 				'trans_date' =>date('Y-m-d'),
							// 				'trans_by'   =>Auth::user()->id,
							// 				'trans_type' =>'Entry',
							// 			];

							// 			if ($val->id!=$house_id) {
							// 				$house_id = $val->id;
							// 				$main_boq_id = DB::table('boqs')->insertGetId($boq_insert);
							// 			}

							// 			$insert[] = [
							// 			'boq_id'   =>$main_boq_id,
							// 			'house_id' =>$val->id,
							// 			'item_id'  =>$itemObj->id,
							// 			'unit'     =>$value->unit,
							// 			'qty_std'  =>empty(stringValue($value->qty_std)) ? 0 : stringValue($value->qty_std),
							// 			'qty_add'  =>empty(stringValue($value->qty_add)) ? 0 : stringValue($value->qty_add),
							// 			];
							// 		}

							// 	}else{
							// 		$request->session()->flash('error',"invalid column value");
							// 		return redirect()->back();
							// 	}
							// }else{
							// 	if ($value->house && $value->item && $value->unit && stringValue($value->qty_std) && stringValue($value->qty_add)) {
							// 		$houseObj = House::where('house_no',$value->house)->first();
							// 		$itemObj  = Item::where('code',$value->item)->first();

							// 		if (empty($houseObj)) {
							// 			$request->session()->flash('error','House not found');
							// 			return redirect()->back();
							// 		}

							// 		if (empty($itemObj)) {
							// 			$request->session()->flash('error','Item not found');
							// 			return redirect()->back();
							// 		}

							// 		if ($houseObj->id!=$house_id) {
							// 			$house_id = $houseObj->id;

							// 			$insert_boq = [
							// 				'house_id'   =>$houseObj->id,
							// 				'trans_date' =>date('Y-m-d'),
							// 				'trans_by'   =>Auth::user()->id,
							// 				'trans_type' =>'Entry',
							// 			];

							// 			$main_boq_id = DB::table('boqs')->insertGetId($insert_boq);
							// 		}

							// 		$insert[] = [
							// 			'boq_id'   =>$main_boq_id,
							// 			'house_id' =>$houseObj->id,
							// 			'item_id'  =>$itemObj->id,
							// 			'unit'     =>$value->unit,
							// 			'qty_std'  =>empty(stringValue($value->qty_std)) ? 0 : stringValue($value->qty_std),
							// 			'qty_add'  =>empty(stringValue($value->qty_add)) ? 0 : stringValue($value->qty_add),
							// 		];
							// 	}else{
							// 		$request->session()->flash('error',"invalid column value");
							// 		return redirect()->back();
							// 	}
							// }
							
					// 	}
						
					// }
					$data_view = [
						'request'		=>$request->all(),
						'data'			=>$data,
						'title'			=> trans('lang.view'),
						'icon'			=> 'fa fa-eye',
						'small_title'	=> trans('lang.boq_details'),
						'background'	=> '',
						'link'			=> [
							'home'	=> [
									'url' 		=> url('/'),
									'caption' 	=> trans('lang.home'),
							],
							'boq'	=> [
									'url' 		=> url('/boqs'),
									'caption' 	=> trans('lang.boq'),
							],
							'view'	=> [
									'caption' 	=> trans('lang.view'),
							],
						],						
					];
					return view('boq.previewBoq',$data_view);
					// if(!empty($insert)){
					// 	DB::table('boq_items')->insert($insert);
					// 	DB::commit();
					// 	$request->session()->flash('success',trans('lang.upload_success'));
					// }else{
					// 	DB::rollback();
					// 	$request->session()->flash('error',trans('lang.upload_error'));
					// }
				}
				if ($error) {
					$request->session()->flash('bug',$error);
				}
			}
			return redirect()->back();
   		} catch (\Exception $e) {
   			DB::rollback();
			return redirect()->back()->with('error',$e->getMessage());
   		}
   	}
	public function reviseBoqHouseViewExcel(Request $request,$boq_id,$house_id){
		try {
			if($request->hasFile('excel')){
			 $fileUpdate = $request->file('excel');
			 $data = Excel::load($fileUpdate->getRealPath(), function($reader) {})->get();
			 $error = '';
			 $house_type = 0;
			 $main_boq_id = 0;
			 if(!empty($data) && $data->count()){
				 DB::beginTransaction();										
				 $data_view = [
					 'request'		=>$request->all(),
					 'data'			=>$data,
					 'boq_id'      =>$boq_id,
					 'house_id'     =>$house_id,
					 'title'		=> trans('lang.view'),
					 'icon'			=> 'fa fa-eye',
					 'small_title'	=> trans('lang.boq_details'),
					 'background'	=> '',
					 'link'			=> [
						 'home'	=> [
								 'url' 		=> url('/'),
								 'caption' 	=> trans('lang.home'),
						 ],
						 'boq'	=> [
								 'url' 		=> url('/boqs'),
								 'caption' 	=> trans('lang.boq'),
						 ],
						 'view'	=> [
								 'caption' 	=> trans('lang.view'),
						 ],
					 ],						
				 ];
				 return view('boq.previewBoqRevise',$data_view);
			 }
			 if ($error) {
				 $request->session()->flash('bug',$error);
			 }
		 }
		 return redirect()->back();
		} catch (\Exception $e) {
			DB::rollback();
		 return redirect()->back()->with('error',$e->getMessage());
		}
	}   
	public function uploadBoqPreview(Request $request){
		// print_r($request["building_id"]);exit;
		try {
			DB::beginTransaction();	
			$rules = [
				'zone_id'	=> 'required',
				'block_id' 	=> 'required',
				'building_id' 	=> 'required',
			];
			if(is_array($request["building_id"])){
				if(count($request["building_id"]) > 0){
					foreach($request["building_id"] as $key=>$building){
						$this->insertExcelUpload($request,$building);
					}
				}
			}else{
				$this->insertExcelUpload($request,$request["building_id"]);
			}
			DB::commit();
			return redirect('/boqs')->with('success',trans('lang.save_success'));
		} catch (\Exception $e) {
			DB::rollback();
			print_r($e->getMessage().$e->getLine());exit;
			return redirect('/boqs')->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
	}   
	function insertExcelUpload(Request $request,$building_id){
		$where = ['status'=>1];
		if(!empty($request['zone_id'])){
			$where = array_merge($where, ['zone_id'=>$request['zone_id']]);
		}
		if(!empty($request['block_id'])){
			$where = array_merge($where, ['block_id'=>$request['block_id']]);
		}
		if(!empty($building_id)){
			$where = array_merge($where, ['building_id'=>$building_id]);
		}
		if(!empty($request['house_type'])){
			$where = array_merge($where, ['house_type'=>$request['house_type']]);
		}			
		if(!empty($request['street_id'])){
			$where = array_merge($where, ['street_id'=>$request['street_id']]);
		}			
		if(!empty($request['house']) && count($request['house']) > 0){
			$houses = DB::table('houses')->where($where)->whereIn('id',$request['house'])->get();
		}else{
			$houses = DB::table('houses')->where($where)->get();
		}
				
		$pro_id = $request->session()->get('project');
		if(count($houses) > 0){
			
			$boq = [
				'pro_id'		=>	$pro_id,
				'zone_id'		=>	$request["zone_id"] ? $request["zone_id"] : 0,
				'block_id'		=>	$request["block_id"] ? $request["block_id"] : 0,
				'building_id'	=>	$building_id ? $building_id : 0,
				'house_type'	=>	$request["house_type"] ? $request["house_type"] : 0,
				'street_id'		=>	$request["street_id"] ? $request["street_id"] : 0,
				'boq_code'		=> "BOQ-".$this->getBoqCode(),
				'trans_date'	=>	date('Y-m-d'),
				'trans_by'		=>	Auth::user()->id,
				'trans_type'	=>	'Upload Excel',
				'created_by'	=> 	Auth::user()->id,
				'status'		=>	1
			];
			$boq_id = DB::table('boqs')->insertGetId($boq);				
			foreach($houses as $keyHouse=>$house){
				$boq_house = [
					'boq_id'			=>	$boq_id,
					'house_id'			=>	$house->id,
					'boq_house_code'	=>	"BOQ-".$this->getBoqHouseCode($house->id,3),
					'created_by'		=>	Auth::user()->id,
					'created_at'		=>	date('Y-m-d H:i:s'),
					'created_by'		=>  Auth::user()->id,
					'status'			=>	1,
				];					
				$boq_house_id = DB::table('boq_houses')->insertGetId($boq_house);					
				if(count($request["working_type_no"]) > 0){
					
					foreach($request["working_type_no"] as $key=>$working_type){
						$name_working_type =  $request["working_type"][$key];							
						$working_types = DB::table('system_datas')->where('name',$name_working_type)->where('type','WK')->where('status',1)->first();
						if(!empty($working_types)){
							$working_type_id = $working_types->id;
						}else{
							$working_type_ids = DB::table('system_datas')->insertGetId([
								'name'=> !empty($name_working_type)?$name_working_type:"",
								'desc'=> !empty($name_working_type)?$name_working_type:"",
								'type'=> "WK",
								'status'=> 1,
								'parent_id'=> 1,
								'created_by'=> Auth::user()->id,
								'updated_by'=> 0,
								'created_at'=> date('Y-m-d H:i:s'),
								'updated_at'=> date('Y-m-d H:i:s'),
							]);
							$working_type_id = $working_type_ids;
						}
						if(count($request["item_type_".$working_type]) > 0){
							
							foreach($request['item_type_'.$working_type] as $key=>$item_type){
								$item_ids = 0;
								$item_types = DB::table('system_datas')->where('name',$request['item_type_'.$working_type][$key])->where('type','IT')->where('status',1)->first();
								if(!empty($item_types)){
									$item_types_id = $item_types->id;
								}else{
									$item_types_ids = DB::table('system_datas')->insertGetId([
										'name'=> !empty($request['item_type_'.$working_type][$key])?$request['item_type_'.$working_type][$key]:"",
										'desc'=> !empty($request['item_type_'.$working_type][$key])?$request['item_type_'.$working_type][$key]:"",
										'type'=> "IT",
										'status'=> 1,
										'parent_id'=> 1,
										'created_by'=> Auth::user()->id,
										'updated_by'=> 0,
										'created_at'=> date('Y-m-d H:i:s'),
										'updated_at'=> date('Y-m-d H:i:s'),
									]);
									$item_types_id = $item_types_ids;
								}
								$item_name = DB::table('items')->where('cat_id',$item_types_id)->where('name',$request["item_name_".$working_type][$key])->where('code',$request["code_".$working_type][$key])->where('status',1)->first();
								if(!empty($item_name)){
									$item_ids = $item_name->id;
								}else{
									$item_get_ids = DB::table('items')->insertGetId([
										'cat_id'=>!empty($item_types_id)?$item_types_id:0,
										'code'=> !empty($request["code_".$working_type][$key])?$request["code_".$working_type][$key]:"",
										'name'=> !empty($request["item_name_".$working_type][$key])?$request["item_name_".$working_type][$key]:"",
										'desc'=> !empty($request["item_name_".$working_type][$key])?$request["item_name_".$working_type][$key]:"",
										'alert_qty'=> 1,
										'unit_stock'=> !empty($request["unit_".$working_type][$key])?$request["unit_".$working_type][$key]:"",
										'unit_usage'=> !empty($request["unit_".$working_type][$key])?$request["unit_".$working_type][$key]:"",
										'unit_purch'=> !empty($request["unit_".$working_type][$key])?$request["unit_".$working_type][$key]:"",
										'cost_purch'=> 0,
										'photo'=> "",
										'status'=> 1,
										// 'parent_id'=> 1,
										'created_by'=> Auth::user()->id,
										'updated_by'=> 0,
										'created_at'=> date('Y-m-d H:i:s'),
										'updated_at'=> date('Y-m-d H:i:s'),
									]);
									$item_ids = $item_get_ids;
								}
								$item_types = [
									'boq_id' 		=>	!empty($boq_id)?$boq_id:0,
									'boq_house_id' 	=>	!empty($boq_house_id)?$boq_house_id:0,
									'house_id'		=>	!empty($house->id)?$house->id:0,
									'item_id'		=>	!empty($item_ids)?$item_ids:0,
									'unit'			=>	!empty($request["unit_".$working_type][$key])?$request["unit_".$working_type][$key]:"",
									'qty_std'		=>	!empty($request["qty_std_".$working_type][$key])?$request["qty_std_".$working_type][$key]:0,
									'qty_add'		=>	!empty($request["qty_add_".$working_type][$key])?$request["qty_add_".$working_type][$key]:0,
									'cost'			=>	!empty($request["cost_".$working_type][$key])?$request["cost_".$working_type][$key]:"",
									'remain_qty'	=>	!empty($request["qty_std_".$working_type][$key])?$request["cost_".$working_type][$key]:"",
									'working_type'	=>	!empty($working_type_id)?$working_type_id:0,
									'created_at'	=>	date('Y-m-d H:i:s'),
									'created_at'	=>	Auth::user()->id,
								];
								$boq_item = DB::table('boq_items')->insertGetID($item_types);
								if($keyHouse == 0){
									$item_type_defualt = [
										'boq_id' 		=>	!empty($boq_id)?$boq_id:0,
										'house_id'		=>	!empty($house->id)?$house->id:0,
										'item_id'		=>	!empty($item_ids)?$item_ids:0,
										'unit'			=>	!empty($request["unit_".$working_type][$key])?$request["unit_".$working_type][$key]:"",
										'qty_std'		=>	!empty($request["qty_std_".$working_type][$key])?$request["qty_std_".$working_type][$key]:0,
										'qty_add'		=>	!empty($request["qty_add_".$working_type][$key])?$request["qty_add_".$working_type][$key]:0,
										'cost'			=>	!empty($request["cost_".$working_type][$key])?$request["cost_".$working_type][$key]:"",
										'remain_qty'	=>	!empty($request["qty_std_".$working_type][$key])?$request["cost_".$working_type][$key]:"",
										'working_type'	=>	!empty($working_type_id)?$working_type_id:0,
										'boq_house_id' 	=>	!empty($boq_house_id)?$boq_house_id:0,
										'remain_qty'	=>	!empty($request["qty_std_".$working_type][$key])?$request["cost_".$working_type][$key]:"",
										'created_by'=> Auth::user()->id,
										'updated_by'=> 0,
										'created_at'	=>	date('Y-m-d H:i:s'),
										'created_at'	=>	Auth::user()->id,
									];
									$boq_item_defult = DB::table('boq_item_defualts')->insertGetID($item_type_defualt);
								}
								// print_r(1);exit;
							}
						}
					}
				}
			}	
		}
	}
	public function uploadBoqPreviewRevise(Request $request){
		$boq_id = $request->boq_id;
		$house_id = $request->house_id;
		// $working_type = decrypt($working_type);
		try {
			DB::beginTransaction();
			// get old BOQ
			$oldBoq = Boq::where('id',$boq_id)->first();
			$newBoq = $oldBoq->replicate();
			$newBoq->version = $oldBoq->version + 1;
			$newBoq->revise_count = $oldBoq->revise_count + 1;
			$newBoq->revise_by = Auth::user()->id;
			$newBoq->status = 1;
			$newBoq->save();
			$newBoqID = $newBoq->id;
			// Get old BOQ House
			$boqHouses = BoqHouse::where("boq_id",$boq_id)->get();
			if(count($boqHouses) > 0){
				foreach($boqHouses as $key=>$boqHouse){
					//Duplicate BOQ
					$newBoqHouse = $boqHouse->replicate();
					//Add new Value to BOQ
					$newBoqHouse->created_at = date('Y-m-d H:i:s');
					$newBoqHouse->revise_by = Auth::user()->id;
					$newBoqHouse->version = $boqHouse->version + 1;
					$newBoqHouse->revise_count = $newBoqHouse->revise_count + 1;
					$newBoqHouse->status = 1;
					$newBoqHouse->boq_id = $newBoqID;
					// Save New BOQ
					$newBoqHouse->save();
					$newBoqHouseId = $newBoqHouse->id;
					$oldBoqItems = BoqItem::where('boq_house_id',$boqHouse->id)->get();
					
					if($oldBoqItems){
						foreach($oldBoqItems as $key=>$boqItem){
							$boqItemDB = $boqItem->replicate();
							$boqItemDB->boq_house_id = $newBoqHouseId;
							$boqItemDB->boq_id = $newBoqID;
							$boqItemDB->created_at = date('Y-m-d H:i:s');
							$boqItemDB->save();
						}
					}
				}				
			}
			DB::table('boqs')->where('referent_id',$boq_id)->update(['status'=>0,'is_revise'=>1,'revise_by'=>Auth::user()->id,'referent_id'=>$newBoqID]);
			DB::table('boqs')->where('id',$boq_id)->update(['status'=>0,'is_revise'=>1,'revise_by'=>Auth::user()->id,'referent_id'=>$newBoqID]);
			DB::table('boq_houses')->where("referent_id",$boq_id)->update(['status'=>0,'revise_by'=>Auth::user()->id,'is_revise'=>1,"referent_id"=>$newBoqHouseId,"version"=>$oldBoq->version]);
			DB::table('boq_houses')->where("boq_id",$boq_id)->update(['status'=>0,'revise_by'=>Auth::user()->id,'is_revise'=>1,"referent_id"=>$newBoqHouseId,"version"=>$newBoq->version]);
			DB::table('boq_items')->where('boq_id',$boq_id)->update(['remain_qty'=>0]);
			$boqHouseID = 0;
			$oldBoqHouseId = BoqHouse::where('boq_id',$newBoqID)->where('house_id',$house_id)->first();
			if($oldBoqHouseId){
				if(count($request["working_type_no"]) > 0){
					foreach($request["working_type_no"] as $key=>$working_type){
						$name_working_type =  $request["working_type"][$key];							
						$working_types = DB::table('system_datas')->where('name',$name_working_type)->where('type','WK')->where('status',1)->first();
						if(!empty($working_types)){
							$working_type_id = $working_types->id;
						}else{
							$working_type_ids = DB::table('system_datas')->insertGetId([
								'name'=> !empty($name_working_type)?$name_working_type:"",
								'desc'=> !empty($name_working_type)?$name_working_type:"",
								'type'=> "WK",
								'status'=> 1,
								'parent_id'=> 1,
								'created_by'=> Auth::user()->id,
								'updated_by'=> 0,
								'created_at'=> date('Y-m-d H:i:s'),
								'updated_at'=> date('Y-m-d H:i:s'),
							]);
							$working_type_id = $working_type_ids;
						}
						if(count($request["item_type_".$working_type]) > 0){
							foreach($request['item_type_'.$working_type] as $key=>$item_type){
								$item_types = DB::table('system_datas')->where('name',$request['item_type_'.$working_type][$key])->where('type','IT')->where('status',1)->first();
								if(!empty($item_types)){
									$item_types_id = $item_types->id;
								}else{
									$item_types_ids = DB::table('system_datas')->insertGetId([
										'name'=> !empty($request['item_type_'.$working_type][$key])?$request['item_type_'.$working_type][$key]:"",
										'desc'=> !empty($request['item_type_'.$working_type][$key])?$request['item_type_'.$working_type][$key]:"",
										'type'=> "WK",
										'status'=> 1,
										'parent_id'=> 1,
										'created_by'=> Auth::user()->id,
										'updated_by'=> 0,
										'created_at'=> date('Y-m-d H:i:s'),
										'updated_at'=> date('Y-m-d H:i:s'),
									]);
									$item_types_id = $item_types_ids;
								}
								$item_name = DB::table('items')->where('name',$request["item_name_".$working_type][$key])->where('status',1)->first();
								if(!empty($item_name)){
									$item_name = DB::table('items')->where('cat_id',$item_types_id)->where('name',$request["item_name_".$working_type][$key])->where('status',1)->first();
									if(!empty($item_name)){
										$item_ids = $item_name->id;
									}else{
										$item_get_ids = DB::table('system_datas')->insertGetId([
											'cat_id'=>!empty($item_types_id)?$item_types_id:0,
											'code'=> !empty($request["code_".$working_type][$key])?$request["code_".$working_type][$key]:"",
											'name'=> !empty($request["item_name_".$working_type][$key])?$request["item_name_".$working_type][$key]:"",
											'desc'=> !empty($request["item_name_".$working_type][$key])?$request["item_name_".$working_type][$key]:"",
											'alert_qty'=> 1,
											'unit_stock'=> !empty($request["unit_".$working_type][$key])?$request["unit_".$working_type][$key]:"",
											'unit_usage'=> !empty($request["unit_".$working_type][$key])?$request["unit_".$working_type][$key]:"",
											'unit_purch'=> !empty($request["unit_".$working_type][$key])?$request["unit_".$working_type][$key]:"",
											'cost_purch'=> 0,
											'photo'=> "",
											'status'=> 1,
											'parent_id'=> 1,
											'created_by'=> Auth::user()->id,
											'updated_by'=> 0,
											'created_at'=> date('Y-m-d H:i:s'),
											'updated_at'=> date('Y-m-d H:i:s'),
										]);
										$item_ids = $item_get_ids;
									}
								}
								$item_types = [
									'boq_id' 		=>	!empty($newBoqID)?$newBoqID:0,
									'boq_house_id' 	=>	!empty($oldBoqHouseId->id)?$oldBoqHouseId->id:0,
									'house_id'		=>	!empty($house_id)?$house_id:0,
									'item_id'		=>	!empty($item_ids)?$item_ids:0,
									'unit'			=>	!empty($request["unit_".$working_type][$key])?$request["unit_".$working_type][$key]:"",
									'qty_std'		=>	!empty($request["qty_std_".$working_type][$key])?$request["qty_std_".$working_type][$key]:0,
									'qty_add'		=>	!empty($request["qty_add_".$working_type][$key])?$request["qty_add_".$working_type][$key]:0,
									'cost'			=>	!empty($request["cost_".$working_type][$key])?$request["cost_".$working_type][$key]:"",
									'remain_qty'	=>	!empty($request["qty_std_".$working_type][$key])?$request["cost_".$working_type][$key]:"",
									'working_type'	=>	!empty($working_type_id)?$working_type_id:0,
									'created_at'	=>	date('Y-m-d H:i:s'),
									'created_at'	=>	Auth::user()->id,
								];
								$boq_item = DB::table('boq_items')->insertGetID($item_types);
							}
						}
					}
				}
			}
			if ($request->hasFile('document_support')) {
				$photo = upload($request,'document_support','assets/upload/document_support/');
				DB::table('boq_document_support')->insertGetID(['boq_id'=>$boq_id,'file'=>$photo,'created_by'=>Auth::user()->id,'created_at'=>date('Y-m-d H:i:s')]);
			}	
			DB::commit();
			// return Redirect::to('/')->with(['type' => 'error','message' => 'Your message'])->withInput(Input::except('password'));
			return redirect('/boqs')->with('success',trans('lang.save_success'));
		}catch (\Exception $e) {
			DB::rollback();
			print_r($e->getMessage().$e->getLine());exit;
			return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
	}
    public function downloadExcel($id)
   	{
		Excel::create('BOQ.export_'.date('Y_m_d_H_i_s'),function($excel) use($id){
   			$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
   			$excel->sheet('BOQ Info',function($sheet) use($id){
   				$cells = 1;
				$sheet->cell('A'.$cells,trans('lang.house_no'));
   				$sheet->cell('B'.$cells,trans('lang.street'));
   				$sheet->cell('C'.$cells,trans('lang.line_no'));
   				$sheet->cell('D'.$cells,trans('lang.trans_date'));
   				$sheet->cell('E'.$cells,trans('lang.trans_by'));
   				$sheet->cell('F'.$cells,trans('lang.trans_ref'));
				
				$cells++;
				$boq = getBOQs($id);
				if(count($boq)>0){
					foreach ($boq as $value) {
	   					$sheet->cell('A'.($cells),$value->house_no);
	   					$sheet->cell('B'.($cells),$value->street);
	   					$sheet->cell('C'.($cells),$value->line_no);
	   					$sheet->cell('D'.($cells),$value->trans_date);
	   					$sheet->cell('E'.($cells),$value->trans_by);
	   					$sheet->cell('F'.($cells),$value->trans_type);
						$cells++;
						
						$sheet->cell('A'.$cells,'');
						$sheet->cell('B'.$cells,trans('lang.item_code'));
						$sheet->cell('C'.$cells,trans('lang.item_name'));
						$sheet->cell('D'.$cells,trans('lang.units'));
						$sheet->cell('E'.$cells,trans('lang.qty_std'));
						$sheet->cell('F'.$cells,trans('lang.qty_add'));
						
						$cells++;
						$boq_item = getBOQItems($value->id);
						if($boq_item){
							foreach($boq_item as $val){
								$sheet->cell('A'.($cells),'');
								$sheet->cell('B'.($cells),$val->code);
								$sheet->cell('C'.($cells),$val->name);
								$sheet->cell('D'.($cells),$val->unit);
								$sheet->cell('E'.($cells),$val->qty_std);
								$sheet->cell('F'.($cells),$val->qty_add);
								$cells++;
							}
						}
	   				}
   				}
   			});
   		})->download('xlsx');
   	}

	public function reviseBoqHouseView(Request $request,$boq_id,$house_id){
		try {
			
			$type = $request->working_type;
			$boq = BOQ::find($boq_id);
			$boqHouses = BoqHouse::where('boq_id',$boq_id)->pluck('house_id');
			
			$redirect = url('boqs');
			$items = $this->getBoqItem($boq_id,$house_id,$type);
			// print_r($redirect);exit;
			
			$data = [
				'id'			=> $boq_id,
				'title'			=> trans('lang.view'),
				'icon'			=> 'fa fa-eye',
				'small_title'	=> trans('lang.boq_details'),
				'background'	=> '',
				'link'			=> [
					'home'	=> [
							'url' 		=> url('/'),
							'caption' 	=> trans('lang.home'),
					],
					'boq'	=> [
							'url' 		=> url('/boqs'),
							'caption' 	=> trans('lang.boq'),
					],
					'view'	=> [
							'caption' 	=> trans('lang.view'),
					],
				],			
				'rounte'		=> url('boqs/subdt').'/'.$boq_id,			
				'rounteBack'	=> $redirect,
				'boqItems'=>$items,
				'boqItemJson'=>json_encode($items), 
				'boq'=>$boq,
				'boqHouses'=>$boqHouses,
				'rounteBack'	=> url('/boqs'),
				// 'rounteSave'=> url('boqs/reviseBoqHouse/'.$boq_id."/".$house_id."/".),
			];
			if(hasRole('revise_boq_house')){
				$data = array_merge($data, ['rounteSave'=> url('boqs/reviseBoqHouses/'.encrypt($boq_id)."/".encrypt($house_id)."/".encrypt($type))]);
			}

			if(hasRole('boq')){
				$data = array_merge($data, ['rounteBack'=> url('/boqs')]);
			}
			$where = [
				'status'=>'1'
			];
			
			return view('boq.revise_boq_house_view',$data);
		}catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
	}

	public function reviseBoqHouse(Request $request,$boq_id,$house_id,$working_type){
		$boq_id = decrypt($boq_id);
		$house_id = decrypt($house_id);
		$working_type = decrypt($working_type);
		
		try {
			DB::beginTransaction();
			// get old BOQ
			$oldBoq = Boq::where('id',$boq_id)->first();
			$newBoq = $oldBoq->replicate();
			$newBoq->boq_code = $oldBoq->boq_code."-".$oldBoq->version;
			$newBoq->version = $oldBoq->version + 1;
			$newBoq->revise_count = $oldBoq->revise_count + 1;
			$newBoq->revise_by = Auth::user()->id;
			$newBoq->status = 1;
			$newBoq->save();
			$newBoqID = $newBoq->id;
			// Get old BOQ House
			$boqHouses = BoqHouse::where("boq_id",$boq_id)->get();
			if(count($boqHouses) > 0){
				foreach($boqHouses as $key=>$boqHouse){
					//Duplicate BOQ
					$newBoqHouse = $boqHouse->replicate();
					//Add new Value to BOQ
					$newBoqHouse->created_at = date('Y-m-d H:i:s');
					$newBoqHouse->revise_by = Auth::user()->id;
					$newBoqHouse->version = $boqHouse->version + 1;
					$newBoqHouse->revise_count = $newBoqHouse->revise_count + 1;
					$newBoqHouse->status = 1;
					$newBoqHouse->boq_id = $newBoqID;
					$newBoqHouse->boq_house_code = $newBoq->boq_code."-HOUSE".$newBoqHouse->house_id;
					// Save New BOQ
					$newBoqHouse->save();
					$newBoqHouseId = $newBoqHouse->id;
					
					//get old boq house id of boq house
					// $oldBoqHouseId = BoqHouse::where('boq_id',$boq_id)->where('house_id',$newBoqHouse->house_id)->where('status',1)->first();

					//get old boq house item 
					$oldBoqItems = BoqItem::where('boq_house_id',$boqHouse->id)->get();
					
					if($oldBoqItems){
						foreach($oldBoqItems as $key=>$boqItem){
							$boqItemDB = $boqItem->replicate();
							$boqItemDB->boq_house_id = $newBoqHouseId;
							$boqItemDB->boq_id = $newBoqID;
							$boqItemDB->created_at = date('Y-m-d H:i:s');
							$boqItemDB->save();
						}
					}
				}
				
			}
			DB::table('boqs')->where('referent_id',$boq_id)->update(['status'=>0,'is_revise'=>1,'revise_by'=>Auth::user()->id,'referent_id'=>$newBoqID]);
			DB::table('boqs')->where('id',$boq_id)->update(['status'=>0,'is_revise'=>1,'revise_by'=>Auth::user()->id,'referent_id'=>$newBoqID]);
			DB::table('boq_houses')->where("referent_id",$boq_id)->update(['status'=>0,'revise_by'=>Auth::user()->id,'is_revise'=>1,"referent_id"=>$newBoqHouseId,"version"=>$oldBoq->version]);
			DB::table('boq_houses')->where("boq_id",$boq_id)->update(['status'=>0,'revise_by'=>Auth::user()->id,'is_revise'=>1,"referent_id"=>$newBoqHouseId,"version"=>$newBoq->version]);
			DB::table('boq_items')->where('boq_id',$boq_id)->update(['remain_qty'=>0]);
			DB::table('boq_item_defualts')->where('boq_id',$boq_id)->update(['boq_id'=>$newBoqID]);
			

			$boqHouseID = 0;
			$oldBoqHouseId = BoqHouse::where('boq_id',$newBoqID)->where('house_id',$house_id)->first();
			if($oldBoqHouseId){
				// $boq_house = [
				// 	'boq_id'			=>	$newBoqID,
				// 	'house_id'			=>	$house_id,
				// 	'boq_house_code'	=>	"BOQ-".$this->getBoqHouseCode($house_id,3),
				// 	'created_by'		=>	Auth::user()->id,
				// 	'created_at'		=>	date('Y-m-d H:i:s'),
				// 	'created_by'		=>  Auth::user()->id,
				// 	'status'			=>	1,
				// 	'version'			=>	$oldBoqHouseId->version
				// ];
				// $boqHouseID = DB::table('boq_houses')->insertGetID($boq_house);
				// DB::table('boq_items')->where('boq_id',$newBoqID)->where('boq_house_id',$oldBoqHouseId->id)->update(['remain_qty'=>0]);
				// DB::table('boq_houses')->where("boq_id",$boq_id)->where('id',$oldBoqHouseId->id)->update(['status'=>0,'revise_by'=>Auth::user()->id,'is_revise'=>1,"referent_id"=>$boqHouseID,'version'=>$oldBoqHouseId->version + 1]);

				
				if(count($request["working_type_no"]) > 0){
					foreach($request["working_type_no"] as $key=>$working_type){ 
						DB::table('boq_items')->where('boq_house_id',$oldBoqHouseId->id)->where('working_type',$working_type)->delete();
						if(count($request["line_item_type_".$working_type]) > 0){
							foreach($request['line_item_type_'.$working_type] as $key=>$item_type){
								$item_types = [
									'boq_id' 		=>	$newBoqID,
									'boq_house_id' 	=>	$oldBoqHouseId->id,
									'house_id'		=>	$house_id,
									'item_id'		=>	$request["line_item_".$working_type][$key],
									'unit'			=>	$request["line_unit_".$working_type][$key],
									'qty_std'		=>	$request["line_qty_std_".$working_type][$key],
									'qty_add'		=>	$request["line_qty_add_".$working_type][$key],
									'cost'			=>	$request["line_cost_".$working_type][$key],
									'remain_qty'	=>	$request["line_qty_std_".$working_type][$key],
									'working_type'	=>	$working_type,
									'created_at'	=>	date('Y-m-d H:i:s'),
									'created_at'	=>	Auth::user()->id,
									'is_closed'		=>	$request['is_close_'.$working_type][$key]
								];
								$boq_item = DB::table('boq_items')->insertGetID($item_types);
							}
						}
					}
				}
			}
			if ($request->hasFile('document_support')) {
				$photo = upload($request,'document_support','assets/upload/document_support/');
				DB::table('boq_document_support')->insertGetID(['boq_id'=>$boq_id,'file'=>$photo,'created_by'=>Auth::user()->id,'created_at'=>date('Y-m-d H:i:s')]);
			}
	
			DB::commit();
			// return Redirect::to('/')->with(['type' => 'error','message' => 'Your message'])->withInput(Input::except('password'));
			return redirect('/boqs')->with('success',trans('lang.save_success'));
		}catch (\Exception $e) {
			DB::rollback();
			print_r($e->getMessage().":Line:".$e->getLine());exit;
			return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
	}
	public function assignHouse(Request $request,$boq_id){
		
		$boq_id = decrypt($boq_id);
		try{
			DB::beginTransaction();
			$houses = $request->house;
			
			if(count($houses)>0){
				foreach($houses as $key=>$house){
					// print_r($house);
					$boq_house = [
						'boq_id'			=>	$boq_id,
						'house_id'			=>	$house,
						'boq_house_code'	=>	"BOQ-".$this->getBoqHouseCode($house,3),
						'created_by'		=>	Auth::user()->id,
						'created_at'		=>	date('Y-m-d H:i:s'),
						'created_by'		=> Auth::user()->id,
						'status'			=>	1,
					];
					$boq_house_id = DB::table('boq_houses')->insertGetId($boq_house);
					$boq_items = BoqItemDefualt::where("boq_id",$boq_id)->get();
					if($boq_items){
						foreach($boq_items as $key=>$item){
							$newBoqItem = $item->replicate();
							$newBoqItem->boq_house_id = $boq_house_id;
							$newBoqItem->boq_id = $boq_id;
							$newBoqItem->house_id = $boq_house_id;
							$newBoqItem->setTable("boq_items");
							$newBoqItem->save();

						}
					}
					
				}
			}
			DB::commit();
			return redirect('/boqs')->with('success',trans('lang.save_success'));
		}catch(Exception $e){
			print_r($e->getMessage());
			DB::rollback();
		}

	}
	public function downloadBoq(Request $request,$boq_id){
		Excel::create('BOQ.export_'.date('Y_m_d_H_i_s'),function($excel) use($boq_id){
			$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
			$excel->sheet('BOQ Info',function($sheet) use($boq_id){
				$cells = 1;
				$sheet->cell(('A'.$cells),"List BOQ by House");
				$sheet->mergeCells('A1:Q1');
				$sheet->cell(("A1"),function($cells){
					$cells->setFontSize(15);;
					$cells->setAlignment('center');
					$cells->setFontWeight('bold');
					$cells->setFontFamily('Khmer OS Muol');
				});
				$cells++;
				$sheet->cell('A'.$cells,trans('lang.no'));
				$sheet->cell('B'.$cells,trans('lang.trans_date'));
				$sheet->cell('C'.$cells,trans('lang.boq_code'));
				$sheet->cell('D'.$cells,trans('lang.zone'));
				$sheet->cell('E'.$cells,trans('lang.block'));
				$sheet->cell('F'.$cells,trans('lang.building'));
				$sheet->cell('G'.$cells,trans('lang.street'));
				$sheet->cell('H'.$cells,trans('lang.house_type'));
				$sheet->cell('I'.$cells,trans('lang.house_no'));
				$sheet->cell('J'.$cells,trans('lang.working_type'));
				$sheet->cell('K'.$cells,trans('lang.item_type'));
				$sheet->cell('L'.$cells,trans('lang.item_code'));
				$sheet->cell('M'.$cells,trans('lang.item_name'));
				$sheet->cell('N'.$cells,trans('lang.units'));
				$sheet->cell('O'.$cells,trans('lang.qty_std'));
				$sheet->cell('P'.$cells,trans('lang.qty_add'));
				$sheet->cell('Q'.$cells,trans('lang.cost'));	
				$sheet->cell(("A$cells:Q$cells"),function($cells){
					$cells->setBackground('#337ab7');
					$cells->setAlignment('center');
					$cells->setFontFamily('Khmer OS Battambang');
					$cells->setFontColor('#ffffff');
				});			
				$boq = getBOQExport($boq_id);
				$i = 1;
				if(count($boq)>0){
					foreach ($boq as $value) {
						$cells++;
						$sheet->cell('A'.($cells),$i++);
						$sheet->cell('B'.($cells),date('d/m/Y', strtotime(str_replace('/', '-', $value->trans_date))));
						$sheet->cell('C'.($cells),$value->boq_code);
						$sheet->cell('D'.($cells),$value->zone_name);
						$sheet->cell('E'.($cells),$value->block_name);
						$sheet->cell('F'.($cells),$value->building_name);
						$sheet->cell('G'.($cells),$value->street_name);
						$sheet->cell('H'.($cells),$value->house_type);
						$sheet->cell('I'.($cells),$value->house_no);
						$sheet->cell('J'.($cells),$value->working_type);
						$sheet->cell('K'.($cells),$value->item_type);
						$sheet->cell('L'.($cells),$value->code);
						$sheet->cell('M'.($cells),$value->name);
						$sheet->cell('N'.($cells),$value->unit);
						$sheet->cell('O'.($cells),$value->qty_std);
						$sheet->cell('P'.($cells),$value->qty_add);
						$sheet->cell('Q'.($cells),$value->cost);
						$sheet->cell(("A$cells:Q$cells"),function($cells){
							$cells->setBorder('thin', 'thin', 'thin', 'thin');
							$cells->setAlignment('center');
							$cells->setFontFamily('Khmer OS Battambang');
							$cells->setFontColor('#000000');
						});			
					}
				}
			});
		})->download('xlsx');
	}

	public function updateBoqZoneByHouse(Request $request){
		$boqs = DB::table("boqs")->get();
		if($boqs){
			foreach($boqs as $key=>$boq){
				$house = DB::table("houses")->where("id",$boq->house_id)->first();
				if($house){
					DB::table('boqs')->where("id",$boq->id)->update(["zone_id"=>$house->zone_id,"block_id"=>$house->block_id,"building_id"=>$house->building_id]);
				}
			}
		}
	}
}
