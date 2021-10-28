<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Datatables;
use Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Excel;
use App\Model\House;
use App\Model\SystemData;
use App\Model\Unit;
use App\Model\Item;
use App\Model\Boq;
use App\Model\BoqItem;

class HouseTypeController extends Controller
{
	const SYSTEM_TYPE = "HT";
    public function __construct()
    {
        $this->middleware('auth');
		$this->middleware('CheckProject');
    }
	
    public function index(){
		$data = [
			'title'			=> trans('lang.house_type'),
			'icon'			=> 'fa fa-building-o',
			'small_title'	=> trans('lang.house_type_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
					],
				'dashboard'	=> [
						'caption' 	=> trans('lang.house_type'),
					],
			],
			'type'			=> self::SYSTEM_TYPE,				
			'rounte'		=> url("housetype/dt"),
			'types'			=> ''
		];
		
		if(hasRole('item_type_add')){
			$data = array_merge($data, ['rounteSave'=> url('housetype/save')]);
		}
		if(hasRole('item_type_download')){
			$data = array_merge($data, ['rounteDownload'=> url('housetype/excel/download')]);
		}
		if(hasRole('item_type_upload')){
			$data = array_merge($data, ['rounteUploade'	=> url('housetype/excel/upload')]);
		}
		return view('modal.index',$data);
	}
	
	public function getDt(Request $request)
    {
		$pro_id = $request->session()->get('project');
		$prefix = DB::getTablePrefix();
		$columns    = [
			'system_datas.id',
			'system_datas.name',
			'system_datas.desc',
			'system_datas.type',
			DB::raw("(SELECT COUNT({$prefix}houses.id) AS total FROM {$prefix}houses WHERE {$prefix}houses.status != 0 AND {$prefix}houses.house_type = {$prefix}system_datas.id) AS status"),
		];
		$houseTypes = SystemData::select($columns)
					->where([
						'status'=> 1,
						'parent_id' => $pro_id,
						'type' => self::SYSTEM_TYPE
					])->get();
        return Datatables::of($houseTypes)
		->addColumn('action', function ($row) {
			$btnDelete = 'onclick="onDelete(this)"';
			$btneEdit = 'onclick="onEdit(this)"';
			
			if(!hasRole('item_type_edit')){
				$btneEdit = "disabled";
			}
			if(!hasRole('item_type_delete')){
				$btnDelete = "disabled";
			}
			if($row->status>0){
				$btnDelete = "disabled";
			}
			$btnEnter = 'onclick="enterBOQ(this)"';
			if($row->status<=0){
				$btnEnter = "disabled";
			}
			if(!hasRole('house_type_enter_boq')){
				$btnEnter = "disabled";
			}
			$btnUpload = 'onclick="onBOQ(this)"';
			if($row->status<=0){
				$btnUpload = "disabled";
			}
			if(!hasRole('house_type_upload_boq')){
				$btnUpload = "disabled";
			}
			
			$rounte_boq 	= url('housetype/importSampleExcelBOQ/'.$row->id);
			$download_url 	= url('housetype/downloadSampleExcelBOQ/'.$row->id);
			$rounte_enter 	= url('housetype/boq/enter/'.$row->id);
			$rounte_delete 	= url('housetype/delete/'.$row->id);
			$rounte_edit 	= url('housetype/edit/'.$row->id);

			// Enter BOQ
			$action_button  = '<a '.$btnEnter.' title="'.trans('lang.enter_boq').'" class="btn btn-xs blue boq-enter" row_id="'.$row->id.'" row_rounte="'.$rounte_enter.'">';
			$action_button .= '<i class="fa fa-sign-in"></i>';
			$action_button .= '</a>';
			// Download Sample BOQ
			$action_button .= '<a href="'.$download_url.'" title="'.trans('lang.download').'" class="btn btn-xs purple download-record">';
			$action_button .= '<i class="fa fa-file-excel-o"></i>';
			$action_button .= '</a>';
			// Upload BOQ
			$action_button .= '<a '.$btnUpload.' title="'.trans('lang.upload_boq').'" class="btn btn-xs green boq-record" row_id="'.$row->id.'" row_rounte="'.$rounte_boq.'">';
			$action_button .= '<i class="fa fa-upload"></i>';
			$action_button .= '</a>';
			// Edit House Type
			$action_button .= '<a '.$btneEdit.' title="'.trans('lang.edit').'" class="btn btn-xs yellow edit-record" row_id="'.$row->id.'" row_rounte="'.$rounte_edit.'">';
			$action_button .= '<i class="fa fa-edit"></i>';
			$action_button .= '</a>';
			// Delete House Type
			$action_button .= '<a '.$btnDelete.' title="'.trans('lang.delete').'" class="btn btn-xs red delete-record" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'">';
			$action_button .= '	<i class="fa fa-trash"></i>';
			$action_button .= '</a>';

			return $action_button;

       })->make(true);
    }

    public function save(Request $request){
		$rules = [
				'name'	=>'required|max:50',
				'desc' 	=>'required|max:200',
			];
        Validator::make($request->all(),$rules)->validate();
		DB::beginTransaction();
		try {
			$data = [
				'name'		=>$request->name,
				'desc'		=>$request->desc,
				'type'		=>self::SYSTEM_TYPE,
				'status'    => 1,
				'parent_id'	=>$request->session()->get('project'),
				'created_by'=>Auth::user()->id,
				'created_at'=>date('Y-m-d H:i:s'),
			];
			$id = DB::table('system_datas')->insertGetId($data);
			DB::commit();
			if($request->ajax()){
				return ['id'=>$id,'name'=>$request->name];
			}else{
				return redirect()->back()->with('success',trans('lang.save_success'));
			}
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
	}

    public function update(Request $request,$id)
    {
    	$rules = [
				'name'	=>'required|max:50',
				'desc' 	=>'required|max:200',
		];
    	Validator::make($request->all(),$rules)->validate();
    	DB::beginTransaction();
		try {
			$data = [
				'name'		=>$request->name,
				'desc'		=>$request->desc,
				'type'		=>self::SYSTEM_TYPE,
				'parent_id'	=>$request->session()->get('project'),
				'updated_by'=>Auth::user()->id,
				'updated_at'=>date('Y-m-d H:i:s'),
			];
			DB::table('system_datas')->where(['id'=>$id])->update($data);
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
			DB::table('system_datas')->where(['id'=>$id])->delete();
			DB::commit();
			return redirect()->back()->with('success',trans('lang.delete_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.delete_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }

    public function downloadExcel()
   	{
   		Excel::create('house_type.export_'.date('Y_m_d_H_i_s'),function($excel){
   			$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
   			$excel->sheet('House Type Info',function($sheet){
   				$sheet->cell('A1','Name');
   				$sheet->cell('B1','Description');
				$data = SystemData::where([
					'status'=> 1,
					'type'=> self::SYSTEM_TYPE,
				])->get();
				if(count($data)>0){
					$key=0;
   					foreach ($data as $value) {
	   					$sheet->cell('A'.($key+2),$value->name);
	   					$sheet->cell('B'.($key+2),$value->desc);
						$key++;
	   				}
   				}
   			});
   		})->download('xlsx');
   	}

   	public function uploadExcel(Request $request)
   	{
		if($request->hasFile('excel')){
			$path  = $request->file('excel')->getRealPath();
			$data  = Excel::load($path, function($reader) {})->get();
			$error = [];
			if(!empty($data) && $data->count()){
				foreach ($data as $key => $value) {
					if (count($value)==2) {
						if (($value->name) && ($value->description)) {
							if (count(DB::table('system_datas')->where('name','=',$value->name)->where('type','=',self::SYSTEM_TYPE)->get(['id','name'])->toArray())==0) {
								$insert[] = [
									'name' 		=> $value->name, 
									'desc' 		=> $value->description,
									'type'		=>self::SYSTEM_TYPE,
									'parent_id'	=>$request->session()->get('project'),
									'created_by' 	=> Auth::user()->id,
									'created_at' 	=> date('Y-m-d H:i:s')
								];
							}
						}else{
							$error[$key] = array('index' => ($key + 2));
						}
					}else{
						$request->session()->flash('error',trans('lang.wrong_file'));
						return redirect()->back();
					}
				}
				if(!empty($insert)){
					DB::table('system_datas')->insert($insert);
					$request->session()->flash('success',trans('lang.upload_success'));
				}else{
					$request->session()->flash('error',trans('lang.upload_error'));
				}
			}
			if ($error) {
				$request->session()->flash('bug',$error);
			}
		}
		return redirect()->back();
   	}
	
	public function uploadBoq(Request $request, $id)
   	{
		if($request->hasFile('excel')){
			$path = $request->file('excel')->getRealPath();
			$data = Excel::load($path, function($reader) {})->get();
			$pro_id = $request->session()->get('project');
			$error = '';
			DB::beginTransaction();
			if(!empty($data) && $data->count()){
				$house = House::where(['status'=>1,'house_type'=>$id])->get(['id']);
				if(count($house)>0){
					foreach($house as $val){
						$boq = [
							'house_id'	=>$val->id,
							'line_no'	=>getLineNo($val->id),
							'trans_date'=>date('Y-m-d'),
							'trans_by'	=>Auth::user()->id,
							'trans_type'=>'Import'
						];
						$boq_id[$val->id] = DB::table('boqs')->insertGetId($boq);
					}
					if(!empty($boq_id)){
						foreach ($data as $key => $value) {
							if (count($value)==9) {
								if(($value->item_code) && ($value->item_name) && ($value->item_type) && ($value->unit_stock) && ($value->unit_purch) && stringValue($value->qty_std) && stringValue($value->qty_add) && ($value->unit_boq) && stringValue($value->purch_cost)) {
									foreach($boq_id as $k=>$row){
										$insert[] = [
											'boq_id'	=>$row,
											'house_id'	=>$k,
											'item_id'	=>checkItem($value->item_code, $value->item_name, $value->item_type, $value->unit_stock, $value->unit_purch, $value->purch_cost, $pro_id),
											'unit'		=>$value->unit_boq,
											'qty_std'	=>$value->qty_std,
											'qty_add'	=>$value->qty_add,
										];
									}
								}else{
									$error[$key] = array('index' => ($key + 2));
								}
							}else{
								$request->session()->flash('error',trans('lang.wrong_file'));
								return redirect()->back();
							}
						}
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
			$house = House::where(['status'=>1,'house_type'=>$id])->get(['id']);
			if(count($house)>0){
				foreach($house as $val){
					$boq = [
						'house_id'	=>$val->id,
						'line_no'	=>getLineNo($val->id),
						'trans_date'=>date('Y-m-d'),
						'trans_by'	=>Auth::user()->id,
						'trans_type'=>'Entry'
					];
					$boq_id[$val->id] = DB::table('boqs')->insertGetId($boq);
				}
				if(!empty($boq_id)){
					foreach($boq_id as $k=>$row){
						for ($i=0;$i<count($request['line_no']);$i++) {
							$data[] = [
								'boq_id'	=>$row,
								'house_id'	=>$k,
								'item_id'	=>$request['line_item'][$i],
								'unit'		=>$request['line_unit'][$i],
								'qty_std'	=>$request['line_qty_std'][$i],
								'qty_add'	=>$request['line_qty_add'][$i],
							]; 
						}
					}
				}
			}
			if(!empty($data)){
				DB::table('boq_items')->insert($data);
				DB::commit();
				return redirect()->back()->with('success',trans('lang.save_success'));
			}else{
				DB::rollback();
				return redirect()->back()->with('error',trans('lang.save_error'));
			}
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
   	}

	public function downloadSampleExcelBOQ(Request $request,$houseTypeId)
	{
		try {
			if(!$houseType = SystemData::where(['type'=> self::SYSTEM_TYPE,'id' => $houseTypeId])->first()){
				throw new \Exception("House Type[{$houseTypeId}] not found.");
			}
			Excel::create("Sample BOQ of {$houseType->name}",function($excel) use ($houseType){
				$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
				$excel->sheet("BOQ {$houseType->name}",function($sheet){
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
			return redirect('housetype')->with("error", $ex->getMessage());
		}
	}

	public function importSampleExcelBOQ(Request $request,$houseTypeId)
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

			if(empty($houseTypeId)){
				throw new \Exception("No house type was selected");
			}

			if(!$houseType = SystemData::where(['type'=> self::SYSTEM_TYPE, 'id' => $houseTypeId])->first()){
				throw new \Exception("House Type[{$houseTypeId}] not found.");
			}

			$pro_id = $request->session()->get('project');

			// get all houses from house type
			$houses = House::select(['id','house_no','house_type'])->where('house_type',$houseTypeId)->get();
			// and insert boqs first
			$boqs = [];
			if(!empty($houses) && count($houses) > 0){
				foreach($houses as $index => $house){
					$boq = [
						'house_id'		=> $house->id,
						'line_no' 		=> getLineNo($house->id),
						'trans_date'	=> date('Y-m-d'),
						'trans_by'		=> Auth::user()->id,
						'trans_type'	=> 'Import',
					];

					if(!$boqId = DB::table('boqs')->insertGetId($boq)){
						DB::rollback();
						throw new \Exception("BOQ for house[{$house->house_no}] was\'t set.");
						exit;
					}

					array_push($boqs,['boq_id' => $boqId,'house_id' => $house->id]);
				}
			}else{
				throw new \Exception("No house inside house type[{$houseType->name}].");
			}

			if(count($boqs) == 0){
				DB::rollback();
				throw new \Exception("No BOQ wasn\'t created.");
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

				foreach ($boqs as $boq) {

					$boqItem = [
						'boq_id' 	=> $boq['boq_id'],
						'house_id' 	=> $boq['house_id'],
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
						exit;
					}
				}
			}
			DB::commit();
			return redirect('housetype')->with("success", trans('lang.upload_success'));
		} catch (\Exception $ex) {
			DB::rollback();
			return redirect('housetype')->with("error", $ex->getMessage());
		}
	}
}
