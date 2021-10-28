<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Datatables;
use Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use PHPExcel_Worksheet_Drawing;
use Excel;
use App\Model\Item;
use App\Model\Unit;
use App\Model\Stock;
use App\Model\BoqItem;
use App\Model\SupplierItem;
use App\Model\SystemData;

class ItemsController extends Controller
{
    public function __itemuct()
    {
        $this->middleware('auth');
		$this->middleware('CheckProject');
    }
	
    public function index(){
		$data = [
			'title'			=> trans('lang.items'),
			'icon'			=> 'fa fa-tags',
			'small_title'	=> trans('lang.item_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'dashboard'	=> [
						'caption' 	=> trans('lang.items'),
				],
			],			
			'rounte'		=> url("items/dt"),
			'routeDownloadSampleUpdatePrice' => url("items/downloadSampleUpdatePrice"),
			'routeUploadSampleUpdatePrice' => url("items/uploadSampleUpdatePrice"),
		];
		
		if(hasRole('item_add')){
			$data = array_merge($data, ['rounteSave'=> url('items/save')]);
		}
		if(hasRole('item_download')){
			$data = array_merge($data, ['rounteDownload'=> url('items/excel/download')]);
		}
		if(hasRole('item_upload')){
			$data = array_merge($data, ['rounteUploade'	=> url('items/excel/upload')]);
		}
		return view('items.index',$data);
	}
	
	public function getDt()
    {
		$items = Item::get();
        return Datatables::of($items)
		->addColumn('category',function($row){
			if($itemType = SystemData::find($row->cat_id)){
				return $itemType->name;
			}
			return '';
		})
		->addColumn('action', function ($row) {
			$rounte_delete = url('items/delete/'.$row->id);
			$rounte_edit = url('items/edit/'.$row->id);
			$btneEdit = 'onclick="onEdit(this)"';
			$btnDelete = 'onclick="onDelete(this)"';
			
			if(!hasRole('item_edit')){
				$btneEdit = "disabled";
			}
			if((Stock::where(['item_id'=>$row->id])->exists()) || (SupplierItem::where(['item_id'=>$row->id])->exists()) || (BoqItem::where(['item_id'=>$row->id])->exists())){
				$btnDelete = "disabled";
			}
			if(!hasRole('item_delete')){
				$btnDelete = "disabled";
			}
            return
				'<a '.$btneEdit.' title="'.trans('lang.edit').'" class="btn btn-xs yellow edit-record" row_id="'.$row->id.'" row_rounte="'.$rounte_edit.'">'.
				'	<i class="fa fa-edit"></i>'.
				'</a>'.
				'<a '.$btnDelete.' title="'.trans('lang.delete').'" class="btn btn-xs red delete-record" row_id="'.$row->id.'" row_rounte="'.$rounte_delete.'">'.
				'	<i class="fa fa-trash"></i>'.
				'</a>'; 
       })->make(true);
    }

    public function save(Request $request){
		$rules = [
			'code'		=>'required|max:150|unique:items',
			'name' 		=>'required|max:150',
			'item_type' =>'required',
			'unit_stock'=>'required',
			'unit_purch'=>'required',
			'purch_cost'=>'required|max:11',
			'status' 	=>'required',
		];
        Validator::make($request->all(),$rules)->validate();
		DB::beginTransaction();
		try {
			$data = [
				'code'		=>$request->code,
				'name'		=>$request->name,
				'desc'		=>$request->desc,
				'cat_id'	=>$request->item_type,
				'status'	=>$request->status,
				'unit_usage'=>isset($request->unit_usage)?($request->unit_usage):($request->unit_stock),
				'unit_stock'=>$request->unit_stock,
				'unit_purch'=>$request->unit_purch,
				'cost_purch'=>$request->purch_cost,
				'alert_qty'=>$request->alert_qty,
				'created_by'=>Auth::user()->id,
				'created_at'=>date('Y-m-d H:i:s'),
			];
			if ($request->hasFile('photo')) {
				$photo = upload($request,'photo','assets/upload/picture/items/');
				$data = array_merge($data,['photo'=>$photo]);
			}
			$id = DB::table('items')->insertGetId($data);
			DB::commit();
			if($request->ajax()){
				return ['id'=>$id,'code'=>$request->code,'name'=>$request->name];
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
		$unique = '';
		if($request->code != $request->old_code){
			$unique = '|unique:items';
		}
    	$rules = [
			'code'		=>'required|max:150'.$unique,
			'name' 		=>'required|max:150',
			'item_type' =>'required',
			'unit_stock'=>'required',
			'unit_purch'=>'required',
			'purch_cost'=>'required|max:11',
			'status' 	=>'required',
		];
    	Validator::make($request->all(),$rules)->validate();
    	DB::beginTransaction();
		try {
			$data = [
				'code'		=>$request->code,
				'name'		=>$request->name,
				'desc'		=>$request->desc,
				'cat_id'	=>$request->item_type,
				'status'	=>$request->status,
				'unit_usage'=>isset($request->unit_usage)?($request->unit_usage):($request->unit_stock),
				'unit_stock'=>$request->unit_stock,
				'unit_purch'=>$request->unit_purch,
				'cost_purch'=>$request->purch_cost,
				'alert_qty'=>$request->alert_qty,
				'updated_by'=>Auth::user()->id,
				'updated_at'=>date('Y-m-d H:i:s'),
			];
			if ($request->hasFile('photo')) {
				$obj_item = Item::find($id);
				$old_photo = '';
				if($obj_item && $obj_item->photo!=NULL && $obj_item->photo!=''){
					$photo = upload($request,'photo','assets/upload/picture/items/',$obj_item->photo);
				}else{
					$photo = upload($request,'photo','assets/upload/picture/items/');
				}
				$data = array_merge($data,['photo'=>$photo]);
			}
			DB::table('items')->where(['id'=>$id])->update($data);
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
			$pic = DB::table('items')->find($id);
			if($pic){
				\File::Delete(str_replace('root','',base_path('')).'\\assets\\upload\\picture\\items\\'.$pic->photo);
			}
			DB::table('items')->where(['id'=>$id])->delete();
			DB::commit();
			return redirect()->back()->with('success',trans('lang.delete_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.delete_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }

    public function downloadExcel()
   	{
		Excel::create('items.export_'.date('Y_m_d_H_i_s'),function($excel){
   			$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
   			$excel->sheet('Item Matariel',function($sheet){
   				$sheet->cell('A1','Photo');
   				$sheet->cell('B1','Item Code');
   				$sheet->cell('C1','Item Name');
   				$sheet->cell('D1','Category');
   				$sheet->cell('E1','Description');
   				$sheet->cell('F1','Unit Stock');
   				$sheet->cell('G1','Unit Usage');
   				$sheet->cell('H1','Unit Purch');
				$sheet->cell('I1','Purch Price');
				$sheet->cell('J1','Alert QTY');
   				$sheet->cell('K1','Status');
				
				/* $active = trans('lang.active');
				$disable = trans('lang.disable'); */
				$sql = "SELECT 
					  `pr_items`.`id`,
					  pr_items.`photo`,
					  pr_items.`code`,
					  pr_items.`name`,
					  (SELECT pr_system_datas.`name` FROM pr_system_datas WHERE pr_system_datas.`id`=pr_items.`cat_id`) AS category,
					  pr_items.`desc`,
					  pr_items.`unit_stock`,
					  pr_items.`unit_usage`,
					  pr_items.`unit_purch`,
					  pr_items.`cost_purch`,
					  pr_items.`alert_qty`,
					  (CASE WHEN pr_items.`status`=1 THEN 'Active' ELSE 'Disable' END)AS `status`
					FROM
					  pr_items ";
				$data = DB::select($sql);
				if(count($data)>0){
					$key=0;
   					foreach ($data as $value) {
						$photo = 'no-image.jpg';
						if($value->photo!=''){
							$photo = $value->photo;
						}
	   					// $sheet->setHeight(($key+2), 50);
                    	// $objDrawing = new PHPExcel_Worksheet_Drawing;
				        // $objDrawing->setPath(str_replace('root','',base_path('')).'\\assets\\upload\\picture\\items\\'.$photo);
				        // $objDrawing->setCoordinates('A'.($key+2));
				        // $objDrawing->setHeight(50);
				        // $objDrawing->setWorksheet($sheet);
						$sheet->cell('A'.($key+2),'');
						$sheet->cell('B'.($key+2),$value->code);
	   					$sheet->cell('C'.($key+2),$value->name);
	   					$sheet->cell('D'.($key+2),$value->category);
	   					$sheet->cell('E'.($key+2),$value->desc);
	   					$sheet->cell('F'.($key+2),$value->unit_stock);
	   					$sheet->cell('G'.($key+2),$value->unit_usage);
	   					$sheet->cell('H'.($key+2),$value->unit_purch);
	   					$sheet->cell('I'.($key+2),$value->cost_purch);
						$sheet->cell('J'.($key+2),$value->alert_qty);
						$sheet->cell('K'.($key+2),$value->status);
						$key++;
	   				}
   				}
   			});
   		})->download('xlsx');
   	}

   	public function uploadExcel(Request $request)
   	{
		if($request->hasFile('excel')){
			$path = $request->file('excel')->getRealPath();
			$data = Excel::load($path, function($reader) {})->get();
			$pro_id = $request->session()->get('project');
			$error = [];
			
			if(!empty($data) && $data->count()){
				
				foreach ($data as $key => $value) {
					
					// var_dump(count($value));exit;
					// if (count($value)==9) {
						
						if (($value->item_code) && ($value->item_name) && ($value->category) && ($value->description) && ($value->unit_stock) && ($value->unit_usage) && ($value->unit_purch) ) {
							
							if (count(DB::table('items')->where('code','=',$value->item_code)->get(['id'])->toArray())==0) {
								$insert[] = [
									'code'		=>$value->item_code,
									'name'		=>$value->item_name,
									'desc'		=>$value->description,
									'cat_id'	=>checkSystemData($value->category, 'IT', $pro_id),
									'unit_stock'=>$value->unit_stock,
									'unit_usage'=>$value->unit_usage,									
									'unit_purch'=>$value->unit_purch,
									'cost_purch'=>$value->purch_price ? $value->purch_price : 0,
									'alert_qty'=>$value->alert_qty,
									'status'=>($value->status)=="Active"?1:0,
									'created_by'=>Auth::user()->id,
									'created_at'=>date('Y-m-d H:i:s'),
								];
								checkUnit($value->unit_stock, $value->unit_purch);
							}
						}else{
							$error[$key] = array('index' => ($key + 2));
						}
					// }else{
					// 	$request->session()->flash('error',trans('lang.wrong_file'));
					// 	return redirect()->back();
					// }
				}
				if(!empty($insert)){
					DB::table('items')->insert($insert);
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

	public function getProducts(Request $request)
	{
		try {
			$Ref = Item::select(['*',DB::raw("CONCAT(code,' | ',name) as text")])
						->where('code','like','%'.($request->q).'%')
						->orWhere('name','like','%'.($request->q).'%')
						->offset(0)
						->limit(10)
						->paginate(10);
			return response()->json($Ref,200);
    	} catch (\Exception $e) {
    		return response()->json(['id'=>$e->getLine(),'text'=>$e->getMessage()],200);
    	}
	}

	public function sampleUpdatePrice(Request $request)
	{
		try {
			$date = date('Y-m-d H:i:s');
			Excel::create("Sample Update Item Price({$date})",function($excel) {
				$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
				$excel->sheet('Item',function($sheet){
					// Header
					$sheet->cell('A1', 'Code');
					$sheet->cell('B1', 'Unit');
					$sheet->cell('C1', 'Price');
					// Example Body
					$sheet->cell('A2', "ITEM001");
					$sheet->cell('B2', "UNIT001");
					$sheet->cell('C2', 10);
				});
			})->download('xlsx');
		} catch (\Exception $ex) {
			return redirect('items')->with("error", $ex->getMessage());
		}
	}
	
	public function updatePrice(Request $request)
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

			foreach($data as $index => $row){
				$cellCount = $index + 1;

				if(empty($row->code)){
					throw new \Exception("Column[A{$cellCount}] is empty");
				}

				if(!$item = Item::where('code',$row->code)->first()){
					throw new \Exception("Column[A{$cellCount}] item not found");
				}

				if(empty($row->unit)){
					throw new \Exception("Column[B{$cellCount}] is empty");
				}

				if(!$unit = Unit::where('from_desc',$row->unit)->first()){
					throw new \Exception("Column[B{$cellCount}] item not found");
				}

				if(empty($row->price)){
					throw new \Exception("Column[C{$cellCount}] is empty");
				}

				DB::table('items')->where('id',$item->id)->update([
					'unit_stock' => $row->unit,
					'cost_purch' => $row->price,
				]);
			}

			DB::commit();
			return redirect('items')->with("success", trans('lang.upload_success'));
		} catch (\Exception $ex) {
			DB::rollback();
			return redirect('items')->with("error", $ex->getMessage());
		}
	}
}
