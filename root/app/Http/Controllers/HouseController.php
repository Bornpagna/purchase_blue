<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Datatables;
use Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Facades\Session;
use App\Model\House;
use App\Model\Boq;
use App\Model\BoqItem;
use App\User;
use App\Model\UsageDetails;
use App\Model\SystemData;

class HouseController extends Controller
{
	public function __houseuct()
    {
        $this->middleware('auth');
		$this->middleware('CheckProject');
    }
	
    public function index(){
		$data = [
			'title'			=> trans('lang.house'),
			'icon'			=> 'fa fa-home',
			'small_title'	=> trans('lang.house_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'dashboard'	=> [
						'caption' 	=> trans('lang.house'),
				],
			],			
			'rounte'		=> url("house/dt"),
		];
		if(hasRole('house_add')){
			$data = array_merge($data, ['rounteSave'=> url('house/save')]);
		}
		if(hasRole('house_download')){
			$data = array_merge($data, ['rounteDownload'=> url('house/excel/download')]);
		}
		if(hasRole('house_upload')){
			$data = array_merge($data, ['rounteUploade'	=> url('house/excel/upload')]);
		}
		$data = array_merge($data, ['rounteUploadeUpdate'	=> url('house/excel/uploadUpdateHouse')]);
		return view('house.index',$data);
	}
	
	public function getDt(Request $request)
    {
		$pro_id = $request->session()->get('project');
        $houses = House::where(['pro_id' => $pro_id])->get();
		return Datatables::of($houses)
		->addColumn('house_type_desc',function($row){
			if($houseType = SystemData::find($row->house_type)){
				return $houseType->name;
			}
			return '';
		})
		->addColumn('zone',function($row){
			if($zone = SystemData::find($row->zone_id)){
				return $zone->name;
			}
			return '';
		})
		->addColumn('block',function($row){
			if($block = SystemData::find($row->block_id)){
				return $block->name;
			}
			return '';
		})
		->addColumn('building',function($row){
			if($block = SystemData::find($row->building_id)){
				return $block->name;
			}
			return '';
		})
		->addColumn('street',function($row){
			if($street = SystemData::find($row->street_id)){
				return $street->name;
			}
			return '';
		})
		->addColumn('action', function ($row) {
			$btnEnter = 'onclick="enterBOQ(this)"';
			$btnBoq = 'onclick="onBOQ(this)"';
			$btneEdit = 'onclick="onEdit(this)"';
			$btnDelete = 'onclick="onDelete(this)"';
			
			if(!hasRole('house_edit')){
				$btneEdit = "disabled";
			}
			if((Boq::where(['house_id'=>$row->id])->exists()) || (UsageDetails::where(['house_id'=>$row->id])->exists())){
				$btnDelete = "disabled";
			}
			if(!hasRole('house_delete')){
				$btnDelete = "disabled";
			}
			if($row->status!=1){
				$btnEnter = "disabled";
				$btnBoq = "disabled";
			}
			if(!hasRole('house_enter_boq')){
				$btnEnter = "disabled";
			}
			if(!hasRole('house_upload_boq')){
				$btnBoq = "disabled";
			}
			
			$rounte_enter 	= url('house/importSampleExcelBOQ/'.$row->id);
			$download_url 	= url('house/downloadSampleExcelBOQ/'.$row->id);
			$rounte_boq 	= url('house/boq/upload/'.$row->id);
			$rounte_delete 	= url('house/delete/'.$row->id);
			$rounte_edit 	= url('house/edit/'.$row->id);

			// Enter BOQ
			$action_button  = '<a '.$btnEnter.' title="'.trans('lang.enter_boq').'" class="btn btn-xs blue boq-enter" row_id="'.$row->id.'" row_rounte="'.$rounte_enter.'">';
			$action_button .= '<i class="fa fa-sign-in"></i>';
			$action_button .= '</a>';
			// Download Sample BOQ
			$action_button .= '<a href="'.$download_url.'" title="'.trans('lang.download').'" class="btn btn-xs purple download-record">';
			$action_button .= '<i class="fa fa-file-excel-o"></i>';
			$action_button .= '</a>';
			// Upload BOQ
			$action_button .= '<a '.$btnBoq.' title="'.trans('lang.upload_boq').'" class="btn btn-xs green boq-record" row_id="'.$row->id.'" row_rounte="'.$rounte_boq.'">';
			$action_button .= '<i class="fa fa-upload"></i>';
			$action_button .= '</a>';
			// Edit House
			$action_button .= '<a '.$btneEdit.' title="'.trans('lang.edit').'" class="btn btn-xs yellow edit-record" row_id="'.$row->id.'" row_rounte="'.$rounte_edit.'">';
			$action_button .= '<i class="fa fa-edit"></i>';
			$action_button .= '</a>';
			// Delete House
			$action_button .= '<a '.$btnDelete.' title="'.trans('lang.delete').'" class="btn btn-xs red delete-record" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'">';
			$action_button .= '	<i class="fa fa-trash"></i>';
			$action_button .= '</a>';

			return $action_button;

		})->addColumn('details_url',function($row){
            return url('house/subdt').'/'.$row->id;
        })->make(true);
    }
	
	public function subDt(Request $request, $id)
    {
		$boqs = Boq::where(['house_id' => $id])->get();
        return Datatables::of($boqs)
		->addColumn('trans_by', function ($row) {
			if($user = User::find($row->trans_by)){
				return $user->name;
			}

			return '';
		})
		->addColumn('amount', function ($row) {
			if($boqItems = BoqItem::where(['boq_id' => $row->id])->count()){
				return $boqItems;
			}

			return 0;
		})
		->addColumn('action', function ($row) {
			$rounte_view = url('boqs/view/'.encrypt($row->id).'/'.encrypt(2));
			$rounte_delete = url('boqs/delete/'.$row->id);
			$btnDelete = 'onclick="onDelete(this)"';
			$btnView = 'onclick="onView(this)"';
			
			if(BoqItem::where(['boq_id'=>$row->id])->exists()){
				$btnDelete = "disabled";
			}
			if(!hasRole('boq_view')){
				$btnView = "disabled";
			}
			return
				'<a '.$btnView.' title="'.trans('lang.view').'" class="btn btn-xs yellow view-record" row_id="'.$row->id.'" row_rounte="'.$rounte_view.'">'.
				'	<i class="fa fa-eye"></i>'.
				'</a>'.
				'<a '.$btnDelete.' title="'.trans('lang.delete').'" class="btn btn-xs red delete-boq-record" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'">'.
				'	<i class="fa fa-trash"></i>'.
				'</a>'; 
		})->make(true);
	}

    public function save(Request $request){
		$rules = [
			'street_id' =>'required',
			'house_type'=>'required',
		];
		if(getSetting()->allow_zone==1){
			$rules=array_merge($rules,['zone_id'=>'required']);
		}
		if(getSetting()->allow_block==1){
			$rules=array_merge($rules,['block_id'=>'required']);
		}
		if(count($request['line_house_no']) > 0){
			for($i=0;$i<count($request['line_house_no']);$i++){
				$rules['line_house_no.'.$i] = 'required|max:50|unique_house:'.$request['street_id'];
			}
		}
		if(count($request['line_house_desc']) > 0){
			for($i=0;$i<count($request['line_house_desc']);$i++){
				$rules['line_house_desc.'.$i] = 'required|max:100';
			}
		}
		if(count($request['line_status']) > 0){
			for($i=0;$i<count($request['line_status']);$i++){
				$rules['line_status.'.$i] = 'required';
			}
		}
		Validator::make($request->all(),$rules)->validate();
		DB::beginTransaction();
		try {
			$pro_id = $request->session()->get('project');
			for ($i=0;$i<count($request['line_no']);$i++) { 
				$data = [
					'pro_id'		=>$pro_id,
					'house_type'	=>$request['house_type'],
					'street_id'		=>$request['street_id'],
					'building_id'	=>$request['building_id'],
					'house_no'		=>$request['line_house_no'][$i],
					'house_desc'	=>$request['line_house_desc'][$i],
					'status'		=>$request['line_status'][$i],
					'created_by'	=>Auth::user()->id,
					'created_at'	=>date('Y-m-d H:i:s'),
				];
				if(getSetting()->allow_zone==1){
					$data=array_merge($data,['zone_id'=>$request['zone_id']]);
				}
				if(getSetting()->allow_block==1){
					$data=array_merge($data,['block_id'=>$request['block_id']]);
				}
				DB::table('houses')->insert($data);
			}
			DB::commit();
			return redirect()->back()->with('success',trans('lang.save_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
	}

    public function update(Request $request,$id)
    {
		$unique = '';
		if($request['old_street_id']!=$request['street_id'] || $request['old_house_no']!=$request['house_no']){
			$unique = '|unique_house:'.$request['street_id'];
		}
		$rules = [
			'house_no' 	=>'required|max:50'.$unique,
			'status'	=>'required',
			'desc'		=>'required|max:100',
			'house_type'=>'required',
			'street_id'	=>'required',
		];
		if(getSetting()->allow_zone==1){
			$rules=array_merge($rules,['zone_id'=>'required']);
		}
		if(getSetting()->allow_block==1){
			$rules=array_merge($rules,['block_id'=>'required']);
		}
		Validator::make($request->all(),$rules)->validate();
    	DB::beginTransaction();
		try {
			$data = [
				'house_type'	=>$request['house_type'],
				'street_id'		=>$request['street_id'],
				'building_id'	=>$request['building_id'],
				'house_no'		=>$request['house_no'],
				'house_desc'	=>$request['desc'],
				'status'		=>$request['status'],
				'updated_by'	=>Auth::user()->id,
				'updated_at'	=>date('Y-m-d H:i:s'),
			];
			if(getSetting()->allow_zone==1){
				$data=array_merge($data,['zone_id'=>$request['zone_id']]);
			}
			if(getSetting()->allow_block==1){
				$data=array_merge($data,['block_id'=>$request['block_id']]);
			}
			DB::table('houses')->where(['id'=>$id])->update($data);
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
			DB::table('houses')->where(['id'=>$id])->delete();
			DB::commit();
			return redirect()->back()->with('success',trans('lang.delete_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.delete_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }

    public function downloadExcel()
   	{
   		Excel::create('house_info.export_'.date('Y_m_d_H_i_s'),function($excel){
   			$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
   			$excel->sheet('house',function($sheet){
   				$sheet->cell('A1','House No');
   				$sheet->cell('B1','House Type');
				$sheet->cell('C1','Zone');
				$sheet->cell('D1','Block');
				$sheet->cell('E1','Building');
				$sheet->cell('F1','Street');
   				$sheet->cell('G1','Status');
   				$sheet->cell('H1','Description');
				$stat = [1=>'Starting',2=>'Finished',3=>'Stopped'];
				$pro_id = Session::get('project');
				$sql = "SELECT 
						  pr_houses.`id`,
						  pr_houses.`house_no`,
						  pr_houses.`house_desc`,
						  pr_houses.`house_type`,
						  (SELECT pr_system_datas.`name` FROM pr_system_datas WHERE pr_system_datas.`id`=pr_houses.`zone_id`) AS zone,
						  (SELECT pr_system_datas.`name` FROM pr_system_datas WHERE pr_system_datas.`id`=pr_houses.`block_id`) AS block,
						  (SELECT pr_system_datas.`name` FROM pr_system_datas WHERE pr_system_datas.`id`=pr_houses.`building_id`) AS building,
						  (SELECT pr_system_datas.`name` FROM pr_system_datas WHERE pr_system_datas.`id`=pr_houses.`street_id`) AS street,
						  pr_houses.`status`,
						  (SELECT pr_system_datas.`name` FROM pr_system_datas WHERE `pr_system_datas`.`id`=pr_houses.`house_type`)AS house_type_desc
						FROM pr_houses WHERE pr_houses.`pro_id`='$pro_id'";
				$data = DB::select($sql);
				if(count($data)>0){
					$key=0;
   					foreach ($data as $value) {
	   					$sheet->cell('A'.($key+2),$value->house_no);
	   					$sheet->cell('B'.($key+2),$value->house_type_desc);
	   					$sheet->cell('C'.($key+2),$value->zone);
	   					$sheet->cell('D'.($key+2),$value->block);
						$sheet->cell('E'.($key+2),$value->building);
						$sheet->cell('F'.($key+2),$value->street);
						$sheet->cell('G'.($key+2),isset($stat[$value->status])?$stat[$value->status]:'Stopped');
						$sheet->cell('H'.($key+2),$value->house_desc);
						
						$key++;
	   				}
   				}
   			});
   		})->download('xlsx');
   	}

   	public function uploadExcel(Request $request)
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
			$stat = ["Starting"=>1,"Finished"=>2,"Stopped"=>3];
			foreach($data as $index => $row){
				$cellCount = $index + 1;
				if(empty($row->house_no)){
					throw new \Exception("Column[A{$cellCount}] is empty");
				}
				if(empty($row->house_type)){
					throw new \Exception("Column[B{$cellCount}] is empty");
				}
				if(empty($row->street)){
					throw new \Exception("Column[C{$cellCount}] is empty");
				}
				if(empty($row->description)){
					throw new \Exception("Column[D{$cellCount}] is empty");
				}
				if(empty($row->status)){
					throw new \Exception("Column[E{$cellCount}] is empty");
				}
				// if(getSetting()->allow_zone == 1){
				if(empty($row->zone)){
					throw new \Exception("Column Zone[{$cellCount}] is empty");
				}
				// }

				// if(getSetting()->allow_block == 1){
				if(empty($row->block)){
					throw new \Exception("Column Block[{$cellCount}] is empty");
				}
				// }
				if(empty($row->building)){
					throw new \Exception("Column Block[{$cellCount}] is empty");
				}
				

				if(!$street = SystemData::where(['name'=> $row->street,'type'=> 'ST'])->first()){
					$columnStreet = [
						'name' 		 => $row->street,
						'desc' 		 => $row->street,
						'type' 		 => 'ST',
						'parent_id'  => $pro_id,
						'created_by' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_by' => Auth::user()->id,
						'updated_at' => date('Y-m-d H:i:s')
					];
					if(!$streetID = DB::table('system_datas')->insertGetId($columnStreet)){
						DB::rollback();
						throw new \Exception("Street wan\'t insert at Column[C{$cellCount}].");
					}

					$street = SystemData::find($streetID);
				}
				if(!$zone = SystemData::where(['name'=> $row->zone,'type'=> 'ZN'])->first()){
					$columnZone = [
						'name' 		 => $row->zone,
						'desc' 		 => $row->zone,
						'type' 		 => 'ZN',
						'parent_id'  => $pro_id,
						'created_by' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_by' => Auth::user()->id,
						'updated_at' => date('Y-m-d H:i:s')
					];
					if(!$zoneID = DB::table('system_datas')->insertGetId($columnZone)){
						DB::rollback();
						throw new \Exception("Zone wan\'t insert at Row[{$cellCount}].");
					}
					$zone = SystemData::find($zoneID);
				}
				if(!$block = SystemData::where(['name'=> $row->block,'type'=> 'BK'])->first()){
					$columnBlock = [
						'name' 		 => $row->block,
						'desc' 		 => $row->block,
						'type' 		 => 'BK',
						'parent_id'  => $pro_id,
						'created_by' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_by' => Auth::user()->id,
						'updated_at' => date('Y-m-d H:i:s')
					];
					if(!$blockID = DB::table('system_datas')->insertGetId($columnBlock)){
						DB::rollback();
						throw new \Exception("Block wan\'t insert at Row[{$cellCount}].");
					}
					$block = SystemData::find($blockID);
				}

				if(!$building = SystemData::where(['name'=> $row->building,'type'=> 'BD'])->first()){
					$columnBlock = [
						'name' 		 => $row->building,
						'desc' 		 => $row->building,
						'type' 		 => 'BD',
						'parent_id'  => $pro_id,
						'created_by' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_by' => Auth::user()->id,
						'updated_at' => date('Y-m-d H:i:s')
					];
					if(!$buildingID = DB::table('system_datas')->insertGetId($columnBlock)){
						DB::rollback();
						throw new \Exception("Block wan\'t insert at Row[{$cellCount}].");
					}
					$building = SystemData::find($buildingID);
				}

				
				$house = House::where(['house_no' => $row->house_no])->where('zone_id',$zone->id)->where('block_id',$block->id)->where('building_id',$building->id)->first();
				if(!$house){
					if(!$houseType = SystemData::where(['name'=> $row->house_type,'type'=> 'HT'])->first()){
						$columnHouseType = [
							'name' 		 => $row->house_type,
							'desc' 		 => $row->house_type,
							'type' 		 => 'HT',
							'parent_id'  => $pro_id,
							'created_by' => Auth::user()->id,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_by' => Auth::user()->id,
							'updated_at' => date('Y-m-d H:i:s')
						];
						if(!$houseTypeID = DB::table('system_datas')->insertGetId($columnHouseType)){
							DB::rollback();
							throw new \Exception("House Type wan\'t insert at Column[B{$cellCount}].");
						}
						$houseType = SystemData::find($houseTypeID);
					}
					$columnHouse = [
						'house_no' 	 => $row->house_no,
						'house_desc' => $row->house_no,
						'house_type' => $houseType->id,
						'pro_id' 	 => $pro_id,
						'street_id'  => $street->id,
						'status'     => $stat[$row->status],
						'created_by' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_by' => Auth::user()->id,
						'updated_at' => date('Y-m-d H:i:s')
					];
					$columnHouse = array_merge($columnHouse,['block_id' => $block->id]);
					$columnHouse = array_merge($columnHouse,['zone_id' => $zone->id]);
					$columnHouse = array_merge($columnHouse,['block_id' => $block->id]);
					$columnHouse = array_merge($columnHouse,['building_id' => $building->id]);

					if(!$houseID = DB::table('houses')->insertGetId($columnHouse)){
						DB::rollback();
						throw new \Exception("House wan\'t insert.");
					}
				}
			}
			DB::commit();
			return redirect('house')->with("success", trans('lang.upload_success'));
		}catch(\Exception $ex){
			DB::rollback();
			return redirect('house')->with("error", $ex->getMessage());
		}
   	}
	
	public function uploadBoq(Request $request, $id)
   	{
		if($request->hasFile('excel')){
			$path   = $request->file('excel')->getRealPath();
			$data   = Excel::load($path, function($reader) {})->get();
			$pro_id = $request->session()->get('project');
			$error  = [];
			DB::beginTransaction();
			if(!empty($data) && $data->count()){
				$boq = [
					'house_id'	=>$id,
					'line_no'	=>getLineNo($id),
					'trans_date'=>date('Y-m-d'),
					'trans_by'	=>Auth::user()->id,
					'trans_type'=>'Import'
				];
				$boq_id = DB::table('boqs')->insertGetId($boq);
				foreach ($data as $key => $value) {
					if (count($value)==9) {
						if (($value->item_code) && ($value->item_name) && ($value->item_type) && ($value->unit_stock) && ($value->unit_purch) && stringValue($value->qty_std) && stringValue($value->qty_add) && ($value->unit_boq) && stringValue($value->purch_cost)) {
							$insert[] = [
								'boq_id'	=>$boq_id,
								'house_id'	=>$id,
								'item_id'	=>checkItem($value->item_code, $value->item_name, $value->item_type, $value->unit_stock, $value->unit_purch, $value->purch_cost, $pro_id),
								'unit'		=>$value->unit_boq,
								'qty_std'	=>$value->qty_std,
								'qty_add'	=>$value->qty_add,
							];
						}else{
							$error[$key] = array('index' => ($key + 2));
						}
					}else{
						$request->session()->flash('error',trans('lang.wrong_file'));
						return redirect()->back();
					}
				}
				if(!empty($insert)){
					DB::table('boq_items')->insert($insert);
					DB::commit();
					$request->session()->flash('success',trans('lang.upload_success'));
				}else{
					DB::rollback();
					$request->session()->flash('error',trans('lang.upload_error'));
				}
			}
			if ($error) {
				DB::rollback();
				$request->session()->flash('bug',$error);
			}
		}
		return redirect()->back();
   	}
	
	public function enterBoq(Request $request, $id)
   	{
		$rules = [];
		if(count($request['line_item_type']) > 0){
			for($i=0;$i<count($request['line_item_type']);$i++){
				$rules['line_item_type.'.$i] = 'required';
			}
		}
		if(count($request['line_item']) > 0){
			for($i=0;$i<count($request['line_item']);$i++){
				$rules['line_item.'.$i] = 'required';
			}
		}
		if(count($request['line_unit']) > 0){
			for($i=0;$i<count($request['line_unit']);$i++){
				$rules['line_unit.'.$i] = 'required';
			}
		}
		if(count($request['line_qty_add']) > 0){
			for($i=0;$i<count($request['line_qty_add']);$i++){
				$rules['line_qty_add.'.$i] = 'required|max:11';
			}
		}
		if(count($request['line_qty_std']) > 0){
			for($i=0;$i<count($request['line_qty_std']);$i++){
				$rules['line_qty_std.'.$i] = 'required|max:11';
			}
		}
		Validator::make($request->all(),$rules)->validate();
		DB::beginTransaction();
		try {
			$boq = [
				'house_id'	=>$id,
				'line_no'	=>getLineNo($id),
				'trans_date'=>date('Y-m-d'),
				'trans_by'	=>Auth::user()->id,
				'trans_type'=>'Entry'
			];
			$boq_id = DB::table('boqs')->insertGetId($boq);
			for ($i=0;$i<count($request['line_no']);$i++) { 
				$data[] = [
					'boq_id'	=>$boq_id,
					'house_id'	=>$id,
					'item_id'	=>$request['line_item'][$i],
					'unit'		=>$request['line_unit'][$i],
					'qty_std'	=>$request['line_qty_std'][$i],
					'qty_add'	=>$request['line_qty_add'][$i],
				]; 
			}
			DB::table('boq_items')->insert($data);
			DB::commit();
			return redirect()->back()->with('success',trans('lang.save_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
   	}

	public function downloadSampleExcelBOQ(Request $request,$houseId)
	{
		try {
			if(!$house = House::find($houseId)){
				throw new \Exception("House [{$houseId}] not found.");
			}
			Excel::create("Sample BOQ of House({$house->house_no})",function($excel) use ($house){
				$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
				$excel->sheet("BOQ {$house->house_no}",function($sheet){
					// Header
					$sheet->cell('A1', 'Item Code');
					$sheet->cell('B1', 'Item Name');
					$sheet->cell('C1', 'Item Type');
					$sheet->cell('D1', 'Unit Stock');
					$sheet->cell('E1', 'Unit Purch');
					$sheet->cell('F1', 'Purch Cost');
					$sheet->cell('G1', 'Qty Std');
					$sheet->cell('H1', 'Qty Add');
					$sheet->cell('I1', 'Unit BOQ');
					// Example Body
					$sheet->cell('A2', 'ITEM001');
					$sheet->cell('B2', 'ITEM A');
					$sheet->cell('C2', 'TYPE A');
					$sheet->cell('D2', 'Pcs');
					$sheet->cell('E2', 'Pcs');
					$sheet->cell('F2', 1);
					$sheet->cell('G2', 20);
					$sheet->cell('H2', 2);
					$sheet->cell('I2', 'Box');
				});
			})->download('xlsx');
		} catch (\Exception $ex) {
			return redirect('house')->with("error", $ex->getMessage());
		}
	}

	public function importSampleExcelBOQ(Request $request,$houseId)
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

			if(empty($houseId)){
				throw new \Exception("No house was selected");
			}

			if(!$house = House::find($houseId)){
				throw new \Exception("House[{$houseId}] not found.");
			}

			$pro_id = $request->session()->get('project');

			$boq = [
				'house_id'		=> $house->id,
				'line_no' 		=> formatZero(1, 3),
				'trans_date'	=> date('Y-m-d'),
				'trans_by'		=> Auth::user()->id,
				'trans_type'	=> 'Import',
			];

			if(!$boqId = DB::table('boqs')->insertGetId($boq)){
				DB::rollback();
				throw new \Exception("BOQ for house[{$house->house_no}] was\'t set.");
				exit;
			}

			foreach($data as $index => $row){
				$cellCount = $index + 1;

				if(empty($row->item_code)){
					throw new \Exception("Column[A{$cellCount}] is empty");
				}

				// If item not exists let check name & type
				if(!$item = Item::where('code',$row->item_code)->first()){
					if(empty($row->item_name)){
						throw new \Exception("Column[B{$cellCount}] is empty");
					}
	
					if(empty($row->item_type)){
						throw new \Exception("Column[C{$cellCount}] is empty");
					}
	
					if(!$itemType = SystemData::where(['type' => 'IT', 'name' => $row->item_type])->first()){
						throw new \Exception("Item Type[{$row->item_type}] not found at Column[D:{$cellCount}].");
					}

					if(empty($row->unit_stock)){
						throw new \Exception("Column[E{$cellCount}] is empty");
					}
	
					if(!$unitStock = Unit::where('from_desc',$row->unit_stock)->first()){
						throw new \Exception("Unit[{$row->unit_stock}] not found at Column[E:{$cellCount}].");
					}
	
					if(empty($row->unit_purch)){
						throw new \Exception("Column[F{$cellCount}] is empty");
					}
	
					if(!$unitPurch = Unit::where('from_desc',$row->unit_purch)->first()){
						throw new \Exception("Unit[{$row->unit_purch}] not found at Column[F:{$cellCount}].");
					}
				}

				if(empty($row->qty_std)){
					throw new \Exception("Column[G{$cellCount}] is empty");
				}

				if(empty($row->unit_boq)){
					throw new \Exception("Column[I{$cellCount}] is empty");
				}

				if(!$unitBoq = Unit::where('from_desc',$row->unit_boq)->first()){
					throw new \Exception("Unit[{$row->unit_boq}] not found at Column[I:{$cellCount}].");
				}

				// Check Item & Insert
				if(!$item = Item::where('code',$row->item_code)->first()){
					$data = [
						'cat_id' 		=> $itemType->id,
						'code'			=> $row->item_code,
						'name' 			=> $row->item_name,
						'alert_qty' 	=> 1,
						'desc' 			=> $itemType->name . " | " . $row->item_name,
						'unit_stock' 	=> $row->unit_stock,
						'unit_usage' 	=> $row->unit_stock,
						'unit_purch' 	=> $row->unit_purch,
						'cost_purch' 	=> 1,
						'created_by' 	=> Auth::user()->id,
						'updated_by' 	=> Auth::user()->id,
						'created_at' 	=> date('Y-m-d H:i:s'),
						'updated_at' 	=> date('Y-m-d H:i:s'),
					];

					if(!empty($row->purch_cost)){
						$data = array_merge($data,['cost_purch' => $row->purch_cost]);
					}

					if(!$itemId = DB::table('items')->insertGetId($data)){
						DB::rollback();
						throw new \Exception("Item[{$row->item_code}] wasn\'t insert.");
					}

					$item = Item::find($itemId);
				}

				$boqItem = [
					'boq_id' 	=> $boqId,
					'house_id'	=> $house->id,
					'item_id' 	=> $item->id,
					'unit'		=> $row->unit_boq,
					'qty_std'	=> $row->qty_std,
					'qty_add'	=> 0,
					'updated_by'=> Auth::user()->id,
					'updated_at'=> date('Y-m-d H:i:s'),
				];

				if(!empty($row->qty_add)){
					$boqItem = array_merge($boqItem,['qty_add' => $row->qty_add]);
				}

				if(!$boqItemId = DB::table('boq_items')->insertGetId($boqItem)){
					DB::rollback();
					throw new \Exception("BOQ for house[{$house->house_no}] was\'t set.");
				}
			}
			DB::commit();
			return redirect('house')->with("success", trans('lang.upload_success'));
		} catch (\Exception $ex) {
			DB::rollback();
			return redirect('house')->with("error", $ex->getMessage());
		}
	}

	public function uploadExcelUpdateHouse(Request $request)
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
			$stat = ["Starting"=>1,"Finished"=>2,"Stopped"=>3];
			foreach($data as $index => $row){
				$cellCount = $index + 1;
				if(empty($row->house_no)){
					throw new \Exception("Column[A{$cellCount}] is empty");
				}
				if(empty($row->house_type)){
					throw new \Exception("Column[B{$cellCount}] is empty");
				}
				if(empty($row->street)){
					throw new \Exception("Column[C{$cellCount}] is empty");
				}
				
				
				if(empty($row->description)){
					throw new \Exception("Column[D{$cellCount}] is empty");
				}
				if(empty($row->status)){
					throw new \Exception("Column[E{$cellCount}] is empty");
				}
				// if(getSetting()->allow_zone == 1){
				if(empty($row->zone)){
					throw new \Exception("Column Zone[{$cellCount}] is empty");
				}
				// }

				// if(getSetting()->allow_block == 1){
				if(empty($row->block)){
					throw new \Exception("Column Block[{$cellCount}] is empty");
				}
				// }
				if(empty($row->building)){
					throw new \Exception("Column Block[{$cellCount}] is empty");
				}
				

				if(!$street = SystemData::where(['name'=> $row->street,'type'=> 'ST'])->first()){
					$columnStreet = [
						'name' 		 => $row->street,
						'desc' 		 => $row->street,
						'type' 		 => 'ST',
						'parent_id'  => $pro_id,
						'created_by' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_by' => Auth::user()->id,
						'updated_at' => date('Y-m-d H:i:s')
					];
					if(!$streetID = DB::table('system_datas')->insertGetId($columnStreet)){
						DB::rollback();
						throw new \Exception("Street wan\'t insert at Column[C{$cellCount}].");
					}

					$street = SystemData::find($streetID);
				}
				if(!$zone = SystemData::where(['name'=> $row->zone,'type'=> 'ZN'])->first()){
					$columnZone = [
						'name' 		 => $row->zone,
						'desc' 		 => $row->zone,
						'type' 		 => 'ZN',
						'parent_id'  => $pro_id,
						'created_by' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_by' => Auth::user()->id,
						'updated_at' => date('Y-m-d H:i:s')
					];
					if(!$zoneID = DB::table('system_datas')->insertGetId($columnZone)){
						DB::rollback();
						throw new \Exception("Zone wan\'t insert at Row[{$cellCount}].");
					}
					$zone = SystemData::find($zoneID);
				}
				if(!$block = SystemData::where(['name'=> $row->block,'type'=> 'BK'])->first()){
					$columnBlock = [
						'name' 		 => $row->block,
						'desc' 		 => $row->block,
						'type' 		 => 'BK',
						'parent_id'  => $pro_id,
						'created_by' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_by' => Auth::user()->id,
						'updated_at' => date('Y-m-d H:i:s')
					];
					if(!$blockID = DB::table('system_datas')->insertGetId($columnBlock)){
						DB::rollback();
						throw new \Exception("Block wan\'t insert at Row[{$cellCount}].");
					}
					$block = SystemData::find($blockID);
				}

				if(!$building = SystemData::where(['name'=> $row->building,'type'=> 'BD'])->first()){
					$columnBlock = [
						'name' 		 => $row->building,
						'desc' 		 => $row->building,
						'type' 		 => 'BD',
						'parent_id'  => $pro_id,
						'created_by' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_by' => Auth::user()->id,
						'updated_at' => date('Y-m-d H:i:s')
					];
					if(!$buildingID = DB::table('system_datas')->insertGetId($columnBlock)){
						DB::rollback();
						throw new \Exception("Block wan\'t insert at Row[{$cellCount}].");
					}
					$building = SystemData::find($buildingID);
				}
				if(!$houseType = SystemData::where(['name'=> $row->house_type,'type'=> 'HT'])->first()){
					$columnHouseType = [
						'name' 		 => $row->house_type,
						'desc' 		 => $row->house_type,
						'type' 		 => 'HT',
						'parent_id'  => $pro_id,
						'created_by' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_by' => Auth::user()->id,
						'updated_at' => date('Y-m-d H:i:s')
					];
					if(!$houseTypeID = DB::table('system_datas')->insertGetId($columnHouseType)){
						DB::rollback();
						throw new \Exception("House Type wan\'t insert at Column[B{$cellCount}].");
					}
					$houseType = SystemData::find($houseTypeID);
				}
				
				$house = House::where(['house_no' => $row->house_no])->first();
				if(!$house){
					$columnHouse = [
						'house_no' 	 => $row->house_no,
						'house_desc' => $row->house_no,
						'house_type' => $houseType->id,
						'pro_id' 	 => $pro_id,
						'street_id'  => $street->id,
						'status'     => $stat[$row->status],
						'created_by' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_by' => Auth::user()->id,
						'updated_at' => date('Y-m-d H:i:s')
					];
					$columnHouse = array_merge($columnHouse,['zone_id' => $zone->id]);
					$columnHouse = array_merge($columnHouse,['block_id' => $block->id]);
					$columnHouse = array_merge($columnHouse,['building_id' => $building->id]);

					if(!$houseID = DB::table('houses')->insertGetId($columnHouse)){
						DB::rollback();
						throw new \Exception("House wan\'t insert.");
					}
				}else{
					$columnHouse = [
						'house_desc' => $row->house_no,
						'house_type' => $houseType->id,
						'pro_id' 	 => $pro_id,
						'street_id'  => $street->id,
						'status'     => $stat[$row->status],
						'created_by' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_by' => Auth::user()->id,
						'updated_at' => date('Y-m-d H:i:s')
					];
					$columnHouse = array_merge($columnHouse,['zone_id' => $zone->id]);
					$columnHouse = array_merge($columnHouse,['block_id' => $block->id]);
					$columnHouse = array_merge($columnHouse,['building_id' => $building->id]);

					if(!$houseID = DB::table('houses')->where('id',$house->id)->update($columnHouse)){
						DB::rollback();
						throw new \Exception("House wan\'t insert.");
					}
				}
			}
			DB::commit();
			return redirect('house')->with("success", trans('lang.upload_success'));
		}catch(\Exception $ex){
			DB::rollback();
			return redirect('house')->with("error", $ex->getMessage());
		}
   	}
}
