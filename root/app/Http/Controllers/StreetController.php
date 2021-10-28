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
use App\Model\SystemData;

class StreetController extends Controller
{
    const SYSTEM_TYPE = "ST";
    public function __construct()
    {
        $this->middleware('auth');
		$this->middleware('CheckProject');
    }
	
    public function index(){
		$data = [
			'title'			=> trans('lang.street'),
			'icon'			=> 'fa fa-road',
			'small_title'	=> trans('lang.street_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
					],
				'dashboard'	=> [
						'caption' 	=> trans('lang.street'),
					],
			],
			'rounte'		=> url("street/dt"),
			'types'			=> ''
		];
		
		if(hasRole('street_add')){
			$data = array_merge($data, ['rounteSave'=> url('street/save')]);
		}
		if(hasRole('street_download')){
			$data = array_merge($data, ['rounteDownload'=> url('street/excel/download')]);
		}
		if(hasRole('street_upload')){
			$data = array_merge($data, ['rounteUploade'	=> url('street/excel/upload')]);
		}
		return view('modal.index',$data);
	}
	
	public function getDt(Request $request)
    {
		$pro_id = $request->session()->get('project');
		$streets = SystemData::where([
			'status'=> 1,
			'parent_id' => $pro_id,
			'type' => self::SYSTEM_TYPE
		])->get();
        return Datatables::of($streets)
		->addColumn('action', function ($row) {
			$rounte_boq = url('street/upload/'.$row->id);
			$rounte_delete = url('street/delete/'.$row->id);
			$rounte_edit = url('street/edit/'.$row->id);
			$btneEdit = 'onclick="onEdit(this)"';
			$btnDelete = 'onclick="onDelete(this)"';
			
			if(!hasRole('street_edit')){
				$btneEdit = "disabled";
			}
			if(House::where(['street_id'=>$row->id])->exists()){
				$btnDelete = "disabled";
			}
			if(!hasRole('street_delete')){
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
				'parent_id'	=>$request->session()->get('project'),
				'status' 	=> 1,
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

    public function downloadExcel()
   	{
   		Excel::create('street.export_'.date('Y_m_d_H_i_s'),function($excel){
   			$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
   			$excel->sheet('House Type Info',function($sheet){
   				$sheet->cell('A1','Name');
   				$sheet->cell('B1','Description');
				$pro_id = Session::get('project');
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
