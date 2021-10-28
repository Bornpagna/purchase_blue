<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Datatables;
use Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ApprovalOrderController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
		$this->middleware('CheckProject');
    }

    public function index(){
		$data = [
			'title'			=> trans('lang.approval_order'),
			'icon'			=> 'fa fa-check-square-o',
			'small_title'	=> trans('lang.order_list'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
					'url' 		=> url('/'),
					'caption' 	=> trans('lang.home'),
				],
				'approve'	=> [
					'url' 		=> url('/'),
					'caption' 	=> trans('lang.approval'),
				],
				'order'	=> [
					'caption' 	=> trans('lang.order'),
				],
			],
			'rounte'		=> url("approve/order/dt"),
		];
		return view('approval.order.index',$data);
	}

	public function getDt(Request $request)
    {
		$pro_id = $request->session()->get('project');
		$user_id = Auth::user()->id;
		$OWNER = config("app.owner");
		$ADMIN = config("app.admin");
		$sql = "CALL POApproval({$pro_id},{$user_id},{$OWNER},{$ADMIN});";
        return Datatables::of(DB::select($sql))
		->addColumn('details_url',function($row){
            return url('purch/order/subdt').'/'.$row->id;
        })
		->addColumn('action', function ($row) {
			$rounte_approve = url('approve/order/approve/'.$row->id.'/'.$row->role_id);
			$rounte_reject = url('approve/order/reject/'.$row->id.'/'.$row->role_id);
			$btnApprove = 'onclick="onApprove(this)"';
			$btnReject = 'onclick="onReject(this)"';
			$btnView = 'onclick="onView(this)"';
			$finish = 0;

			$count_user_role = $this->getUserRole($row->role_id, $row->id);
			// print_r($count_user_role[0]["condition"]);exit;
			if($count_user_role){
				if($count_user_role[0]["condition"] == "or"){
					$finish = 1;
				}else{
					if(count($count_user_role) > 1){
						$finish = 0;
					}else{
						$finish = 1;
					}
				}
			}
			

			if(!hasRole('approve_order_signature')){
				$btnApprove = "disabled";
			}
			if(!hasRole('approve_order_reject')){
				$btnReject = "disabled";
			}
			if(!hasRole('approve_order_view')){
				$btnView = "disabled";
			}
			// if (floatval($row->grand_total) <= floatval($row->role_max_amount)) {
			// 	$finish = 1;
			// }
			return
				'<a '.$btnView.' title="'.trans('lang.view').'" class="btn btn-xs btn-info view-record" role_id="'.$row->role_id.'" dep_id="'.$row->dep_id.'" pr_id="'.$row->id.'">'.
				'	<i class="fa fa-eye"></i>'.
				'</a>'.
				'<a '.$btnApprove.' title="'.trans('lang.approve').'" class="btn btn-xs btn-success approval-record" row_finish="'.$finish.'" row_id="'.$row->id.'" row_rounte="'.$rounte_approve.'">'.
				'	<i class="fa fa-check-square-o"></i>'.
				'</a>'.
				'<a '.$btnReject.' title="'.trans('lang.reject').'" class="btn btn-xs btn-danger reject-record" row_id="'.$row->id.'" row_rounte="'.$rounte_reject.'">'.
				'	<i class="fa fa-ban"></i>'.
				'</a>';
       })->make(true);
    }
	public function getUserRole($role_id,$po_id){
		$user_role = DB::select(DB::raw("SELECT * FROM pr_roles JOIN pr_user_assign_roles ON pr_user_assign_roles.role_id= pr_roles.id WHERE pr_roles.id=".$role_id." AND pr_roles.id NOT IN(SELECT pr_approve_orders.role_id FROM pr_approve_orders WHERE pr_approve_orders.role_id = ".$role_id." AND pr_approve_orders.approved_by = pr_user_assign_roles.`user_id` AND pr_approve_orders.po_id=".$po_id." )"));
		// $user_role = DB::table("roles")->join("pr_user_assign_roles","pr_user_assign_roles.role_id","roles.id")
		// ->where("roles.id",$role_id)
		// ->whereNotIn('roles.id',DB::select(DB::raw('SELECT `pr_approve_orders`.`role_id` FROM `pr_approve_orders` WHERE `pr_approve_orders`.role_id = '.$role_id.' AND pr_approve_orders.`approved_by` = pr_user_assign_roles.`user_id` AND `pr_approve_orders`.`po_id` = '.$po_id)))->get();
		// print_r($user_role);exit();
		$array = json_decode(json_encode($user_role), true);
		return $array;
	}

    public function approve(Request $request, $po_id, $role_id){
		$pro_id = $request->session()->get('project');
		$is_finish = $request->is_finish;
		try {
			if ($request->all()) {
				DB::beginTransaction();
				$data = [
					'po_id'			=>$po_id,
					'role_id'		=>$role_id,
					'role_id'		=>$role_id,
					'approved_by'	=>Auth::user()->id,
					'approved_date'	=>date('Y-m-d H:i:s'),
				];
				if(isset($request->signature_pad)){
					$data_uri = $request['signature_pad'];
					$encoded_image = explode(",", $data_uri)[1];
					$decoded_image = base64_decode($encoded_image);
					$fileName=uniqid(rand()).".png";
					$url="assets/upload/picture/signature/".$fileName;
					file_put_contents($url, $decoded_image);
					$data = array_merge($data, array('signature'=>$fileName));
				}else if ($request->hasFile('image')) {
					$signature = upload($request,'image','assets/upload/picture/signature/');
					$data = array_merge($data, ['signature'=>$signature]);
				}
				DB::table('approve_orders')->insert($data);

				if($is_finish==1){
					DB::table('orders')->where(['id'=>$po_id])->update(['trans_status'=>3]);
				}else{
					DB::table('orders')->where(['id'=>$po_id])->update(['trans_status'=>2]);
				}
				DB::commit();
				return redirect()->back()->with('success',trans('lang.save_success'));
			}
			return redirect()->back()->with('success',trans('lang.save_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.save_error').' '.$e->getMessage().' '.$e->getLine());
		}
	}

	public function reject($po_id, $role_id)
    {
		try {
			DB::beginTransaction();
			DB::table('approve_orders')->where(['po_id'=>$po_id])->update(['reject'=>1]);
			DB::table('orders')->where(['id'=>$po_id])->update(['trans_status'=>4]);
			$data = [
				'po_id'			=>$po_id,
				'role_id'		=>$role_id,
				'approved_by'	=>Auth::user()->id,
				'approved_date'	=>date('Y-m-d H:i:s'),
				'reject'		=>2,
			];
			DB::table('approve_orders')->insert($data);
			DB::commit();
			return redirect()->back()->with('success',trans('lang.delete_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error',trans('lang.delete_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }
}
