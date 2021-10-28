<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Datatables;
use Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Excel;
use App\User;
use App\Model\Role;

class DepartmentController extends Controller
{
	const SYSTEM_TYPE = "DP";
    public function __construct()
    {
        $this->middleware('auth');
		$this->middleware('CheckProject');
    }
	
    public function index(){
		$data = [
			'title'			=> trans('lang.department'),
			'icon'			=> 'fa fa-sitemap',
			'small_title'	=> trans('lang.department_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'dashboard'	=> [
						'caption' 	=> trans('lang.department'),
				],
			],
			'rounte'		=> url("depart/dt"),
			'types'			=> ''
		];
		
		if(hasRole('department_add')){
			$data = array_merge($data, ['rounteSave'=> url('depart/save')]);
		}
		if(hasRole('department_download')){
			$data = array_merge($data, ['rounteDownload'=> url('depart/excel/download')]);
		}
		if(hasRole('department_upload')){
			$data = array_merge($data, ['rounteUploade'	=> url('depart/excel/upload')]);
		}
		return view('modal.index',$data);
	}
	
	public function getDt(Request $request)
    {
		$sql = "SELECT 
				  `pr_system_datas`.`id`,
				  `pr_system_datas`.`name`,
				  `pr_system_datas`.`desc`,
				  `pr_system_datas`.`status`,
				  `pr_system_datas`.`type` 
				FROM
				  `pr_system_datas` 
				WHERE `pr_system_datas`.`status` = '1'
				  AND `pr_system_datas`.`type` = '".self::SYSTEM_TYPE."'";
        return Datatables::of(DB::select($sql))
		->addColumn('action', function ($row) {
			$rounte_delete = url('depart/delete/'.$row->id);
			$rounte_edit = url('depart/edit/'.$row->id);
			$btnEdit = 'onclick="onEdit(this)"';
			$btnDelete = 'onclick="onDelete(this)"';
			
			if(!hasRole('department_edit')){
				$btnEdit = "disabled";
			}
			if((User::where(['dep_id'=>$row->id])->exists()) || (Role::where(['dep_id'=>$row->id,'min_amount'=>0,'max_amount'=>0,'level'=>1])->exists())){
				$btnDelete = "disabled";
			}
			if(!hasRole('department_delete')){
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
   		Excel::create('depart.export_'.date('Y_m_d_H_i_s'),function($excel){
   			$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
   			$excel->sheet('Zone Info',function($sheet){
   				$sheet->cell('A1','Name');
   				$sheet->cell('B1','Description');
				$sql = "SELECT 
						  `pr_system_datas`.`id`,
						  `pr_system_datas`.`name`,
						  `pr_system_datas`.`desc`,
						  `pr_system_datas`.`type` 
						FROM
						  `pr_system_datas` 
						WHERE `pr_system_datas`.`status` = '1' 
						  AND `pr_system_datas`.`type` = '".self::SYSTEM_TYPE."'";
				$data = DB::select($sql);
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
			$error = '';
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
}
