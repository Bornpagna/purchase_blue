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

class WorkingTypeController extends Controller
{
    const SYSTEM_TYPE = "WK";
    public function __construct()
    {
        $this->middleware('auth');
		$this->middleware('CheckProject');
		if(getSetting()->allow_block!=1){
			return redirect()->back();
		}
    }
	
    public function index(){
		$data = [
			'title'			=> trans('lang.working_type'),
			'icon'			=> 'fa fa-square',
			'small_title'	=> trans('lang.working_type_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'dashboard'	=> [
						'caption' 	=> trans('lang.working_type'),
				],
			],			
			'rounte'		=> url("working_type/dt"),
			'types'			=> ''
		];
		
		if(hasRole('working_type_add')){
			$data = array_merge($data, ['rounteSave'=> url('working_type/save')]);
		}
		if(hasRole('working_type_download')){
			$data = array_merge($data, ['rounteDownload'=> url('working_type/excel/download')]);
		}
		if(hasRole('working_type_upload')){
			$data = array_merge($data, ['rounteUploade'	=> url('working_type/excel/upload')]);
		}
		return view('modal.index',$data);
	}
	
	public function getDt(Request $request)
    {
		$pro_id = $request->session()->get('project');
		$blocks = SystemData::where([
			'status'=> 1,
			'parent_id' => $pro_id,
			'type' => self::SYSTEM_TYPE
		])->get();
        return Datatables::of($blocks)
		->addColumn('action', function ($row) {			
			$rounte_boq = url('working_type/upload/'.$row->id);
			$rounte_delete = url('working_type/delete/'.$row->id);
			$rounte_edit = url('working_type/edit/'.$row->id);
			$btnEdit = 'onclick="onEdit(this)"';
			$btnDelete = 'onclick="onDelete(this)"';
			
			if(!hasRole('block_edit')){
				$btnEdit = "disabled";
			}
			if(House::where(['block_id'=>$row->id])->exists()){
				$btnDelete = "disabled";
			}
			if(!hasRole('block_delete')){
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

    public function save(Request $request){
		$rules = [
			'name'	=>'required|max:50|unique_system_data:'.self::SYSTEM_TYPE,
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
		if($request['name']!=$request['old_name']){
			$unique='|unique_system_data:'.self::SYSTEM_TYPE;
		}else{
			$unique='';
		}
    	$rules = [
			'name'	=>'required|max:50'.$unique,
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

    public function downloadExcel(Request $request)
   	{
		$pro_id = $request->session()->get('project');
   		Excel::create('block.export_'.date('Y_m_d_H_i_s'),function($excel)use($pro_id){
   			$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
   			$excel->sheet('Zone Info',function($sheet)use($pro_id){
   				$sheet->cell('A1','Name');
   				$sheet->cell('B1','Description');
				$data = SystemData::where([
					'status'=> 1,
					'parent_id' => $pro_id,
					'type' => self::SYSTEM_TYPE
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
			$path = $request->file('excel')->getRealPath();
			$data = Excel::load($path, function($reader) {})->get();
			$pro_id = $request->session()->get('project');
			$error = '';
			if(!empty($data) && $data->count()){
				foreach ($data as $key => $value) {
					if (count($value)==2) {
						if (($value->name) && ($value->description)) {
							if (count(DB::table('system_datas')->where('name','=',$value->name)->where('type','=',self::SYSTEM_TYPE)->where('parent_id','=',$pro_id)->get(['id','name'])->toArray())==0) {
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
}
