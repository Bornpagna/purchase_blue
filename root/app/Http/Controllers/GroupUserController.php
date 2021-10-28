<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Datatables;
use Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Model\UserGroup;
use Excel;

class GroupUserController extends Controller
{
    const SYSTEM_TYPE = "GU";
    public function __construct()
    {
        $this->middleware('auth');
		$this->middleware('CheckProject');
    }
	
    public function index(){
		$data = [
			'title'			=> trans('lang.user_group'),
			'icon'			=> 'icon-users',
			'small_title'	=> trans('lang.user_group_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
					'url' 		=> url('/'),
					'caption' 	=> trans('lang.home'),
				],
				'dashboard'	=> [
					'caption' 	=> trans('lang.user_group'),
				],
			],
			'rounte'		=> url("group/dt"),
			'assign'		=> 'GU',
			'types'			=>	''
		];
		
		if(hasRole('user_group_add')){
			$data = array_merge($data, ['rounteSave'=> url('group/save')]);
		}
		if(hasRole('user_group_download')){
			$data = array_merge($data, ['rounteDownload'=> url('group/excel/download')]);
		}
		if(hasRole('user_group_upload')){
			$data = array_merge($data, ['rounteUploade'	=> url('group/excel/upload')]);
		}
		return view('modal.index',$data);
	}
	
	public function getDt()
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
			$rounte_delete = url('group/delete/'.$row->id);
			$rounte_edit = url('group/edit/'.$row->id);
			$rounte_permission = url('group/permis/'.encrypt($row->id).'/'.encrypt(2));
			$rounte_assign = url('group/assign/'.$row->id);
			$btnEdit = 'onclick="onEdit(this)"';
			$btnDelete = 'onclick="onDelete(this)"';
			$btnPermission = 'onclick="onPermission(this)"';
			$btnAssign = 'onclick="onAssign(this)"';
			
			if(!hasRole('user_group_edit')){
				$btnEdit = "disabled";
			}
			if(!hasRole('user_group_delete')){
				$btnDelete = "disabled";
			}
			if(UserGroup::where(['group_id'=>$row->id])->exists()){
				$btnDelete = "disabled";
			}
			if(!hasRole('user_permission')){
				$btnPermission = "disabled";
			}
			if(!hasRole('group_permission')){
				$btnAssign = "disabled";
			}
            return
				'<a '.$btnPermission.' title="'.trans('lang.permission').'" class="btn btn-xs green permission-record" row_id="'.$row->id.'" row_rounte="'.$rounte_permission.'">'.
				'	<i class="icon-shield"></i>'.
				'</a>'.
				'<a '.$btnAssign.' title="'.trans('lang.assign').'" class="btn btn-xs blue assign-record" row_id="'.$row->id.'" row_rounte="'.$rounte_assign.'">'.
				'	<i class="fa fa-users "></i>'.
				'</a>'.
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
	
	public function assign(Request $request, $id){
		try {
			DB::beginTransaction();
			if(count($request['user']) > 0){
				DB::table('user_groups')->where(['group_id'=>$id])->delete();
				$data = [];
				for($i=0;$i<count($request['user']);$i++){
					$data[] = [
						'group_id'		=>$id,
						'user_id'		=>$request['user'][$i],
						'created_by'=>Auth::user()->id,
						'created_at'=>date('Y-m-d H:i:s'),
					];
				}
				DB::table('user_groups')->insert($data);
			}
			DB::commit();
			return redirect()->back()->with('success',trans('lang.assign_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.assign_error').' '.$e->getMessage().' '.$e->getLine());
		}
	}

    public function downloadExcel()
   	{
   		Excel::create('group.export_'.date('Y_m_d_H_i_s'),function($excel){
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
