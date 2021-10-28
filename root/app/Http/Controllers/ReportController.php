<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Datatables;
use DB;
use Auth;
use Excel;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_IOFactory;
use App\Model\Item;
use App\Model\Order;
use App\Model\OrderItem;
use App\Model\Boq;
use App\Model\BoqItem;
use App\Model\Usage;
use App\Model\UsageDetails;
use App\Model\SystemData;
use App\Model\Constructor;
use App\Model\House;
use App\Model\Warehouse;
use App\Model\Setting;
use App\Model\Supplier;
use App\Model\RequestItem;
use App\Model\Unit;
use App\Model\Delivery;
use App\Model\Stock;
use App\Model\ApproveRequest;
use App\Model\ApproveOrder;
use App\Model\Project;
use App\Model\Request as PurchaseRequest;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('CheckProject');
    }

    public function get_po_code(Request $request)
    {
    	$codes = Order::select(['id','ref_no','pro_id','trans_status'])->where('trans_status','!=',0)->where('pro_id',$request->session()->get('project'))->get();
    	if (!$codes) {
    		$codes = [];
    	}

    	return response()->json($codes,200);
    }

    public function requests(Request $request)
    {
    	$data = [
			'title'       => trans('rep.request'),
			'icon'        => 'fa fa-registered',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
			'rounte'     => url("stock/deliv/dt"),
			'rounteAdd'  => url('stock/deliv/add'),
			'rounteBack' => url('stock/deliv/add'),
		];
		return view('reports.purchase.request', $data);
    }

    public function request_1(Request $request)
    {
    	$data = [
			'title'       => trans('rep.request_1'),
			'icon'        => 'fa fa-registered',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
		];
		return view('reports.purchase.request_1', $data);
    }

    public function request_2(Request $request)
    {
    	$data = [
			'title'       => trans('rep.request_2'),
			'icon'        => 'fa fa-registered',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
		];
		return view('reports.purchase.request_2', $data);
    }

    public function orders(Request $request)
    {
    	$data = [
			'title'       => trans('rep.order'),
			'icon'        => 'fa fa-shopping-cart',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
		];
		return view('reports.purchase.order', $data);
    }

    public function order_1(Request $request)
    {
    	$data = [
			'title'       => trans('rep.order_1'),
			'icon'        => 'fa fa-shopping-cart',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
		];
		return view('reports.purchase.order_1', $data);
    }

    public function delivery(Request $request)
    {
    	$data = [
			'title'       => trans('rep.delivery'),
			'icon'        => 'fa fa-bar-chart',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
					'url' 		=> url('/'),
					'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
					'url' 		=> '#',
					'caption' 	=> trans('lang.report'),
				],
				'delivery'	=> [
					'caption' 	=> trans('lang.delivery'),
				],
			],
		];
		return view('reports.delivery', $data);
    }

    public function delivery_details(Request $request)
    {
    	$data = [
			'title'       => trans('lang.delivery_details'),
			'icon'        => 'fa fa-bar-chart',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
					'url' 		=> url('/'),
					'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
					'url' 		=> '#',
					'caption' 	=> trans('lang.report'),
				],
				'delivery'	=> [
					'caption' 	=> trans('lang.delivery_details'),
				],
			],
		];
		return view('reports.delivery_details', $data);
    }

    public function delivery_details_get_date(Request $request){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
    	$start_date = $request->start_date;
		$end_date = $request->end_date;
		$project_id   = $request->session()->get('project');
		$prefix = getSetting()->round_number;
		$ref_id = "";
		$item_id = "";
		$where = "";
		if(count($request->ref_id) > 0){
			foreach ($request->ref_id as $key => $value) {
				if($ref_id==""){
					$ref_id = $value;
				}else{
					$ref_id .= ",".$value;
				}
			}
		}

		if(count($request->item_id) > 0){
			foreach ($request->item_id as $key => $value) {
				if($item_id==""){
					$item_id = $value;
				}else{
					$item_id .= ",".$value;
				}
			}
		}
		if($item_id != "" && $ref_id != ""){
			$where = " WHERE A.id IN ($ref_id) OR A.item_id IN ($item_id)";
		}elseif ($item_id != "") {
			$where = " WHERE A.item_id IN ($item_id)";
		}elseif ($ref_id != "") {
			$where = " WHERE A.id IN ($ref_id)";
		}

		$prefix = getSetting()->round_number;
		$sql = "SELECT A.id, A.trans_date, A.ref_no, (SELECT `pr_orders`.`ref_no` FROM `pr_orders` WHERE pr_orders.`id`=A.po_id)AS po_no, (SELECT pr_suppliers.name FROM pr_suppliers WHERE pr_suppliers.id=A.sup_id)AS supplier, (SELECT pr_warehouses.name FROM pr_warehouses WHERE pr_warehouses.id=A.warehouse_id)AS warehouse, (SELECT CONCAT(pr_items.code,' (', pr_items.name,')')AS item_desc FROM pr_items WHERE pr_items.id=A.item_id)AS item_desc, ROUND(A.qty, $prefix)AS qty, ROUND(A.return_qty, $prefix)AS return_qty, A.unit FROM (SELECT `pr_deliveries`.id, `pr_deliveries`.`trans_date`, `pr_deliveries`.`ref_no`, `pr_deliveries`.`po_id`, `pr_deliveries`.`sup_id`, pr_delivery_items.`warehouse_id`, `pr_delivery_items`.`item_id`, `pr_delivery_items`.`qty`, `pr_delivery_items`.`return_qty`, `pr_delivery_items`.`unit` FROM pr_deliveries INNER JOIN pr_delivery_items ON pr_deliveries.`id` = pr_delivery_items.`del_id` AND pr_deliveries.`delete` = 0 AND `pr_deliveries`.`pro_id` = $project_id AND `pr_deliveries`.`trans_date` BETWEEN '$start_date'AND '$end_date') AS A $where ";
		$report = DB::select($sql);
		if ($request->version == 'generate') {
    		return $report;
    	}else if($request->version == 'excel'){
    		Excel::create('delivery_details_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
          $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
          $excel->sheet('stock delivery details',function($sheet) use($report,$start_date,$end_date){
          $startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:B2');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(90);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':J'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':J'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(20);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':J'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':J'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(20);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':J'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report_delivery_details'));
					$sheet->cell(('C'.$startRows.':J'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(14);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':C'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':C'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.line_no'));
                    $sheet->cell(('B'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('C'.$startRows),trans('lang.reference_no'));
                    $sheet->cell(('D'.$startRows),trans('lang.po_no'));
                    $sheet->cell(('E'.$startRows),trans('lang.supplier'));
                    $sheet->cell(('F'.$startRows),trans('lang.warehouse'));
                    $sheet->cell(('G'.$startRows),trans('lang.items'));
                    $sheet->cell(('H'.$startRows),trans('lang.qty'));
                    $sheet->cell(('I'.$startRows),trans('rep.return_qty'));
                    $sheet->cell(('J'.$startRows),trans('lang.units'));

                    $sheet->cell(('A'.$startRows.':J'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as $key => $dval) {
                		$startRows++;
            		$sheet->cell('A'.($startRows), formatZero(($key + 1), 3));
				        $sheet->cell('B'.($startRows), $dval->trans_date);
				        $sheet->cell('C'.($startRows), $dval->ref_no." ");
				        $sheet->cell('D'.($startRows), $dval->po_no);
	                    $sheet->cell('E'.($startRows), $dval->supplier);
	                    $sheet->cell('F'.($startRows), $dval->warehouse);
	                    $sheet->cell('G'.($startRows), $dval->item_desc);
	                    $sheet->cell('H'.($startRows), $dval->qty);
	                    $sheet->cell('I'.($startRows), $dval->return_qty);
	                    $sheet->cell('J'.($startRows), $dval->unit);
	                    $sheet->cell(('A'.$startRows.':J'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                    });
	                    $sheet->cell(('A'.$startRows.':B'.$startRows),function($cells){
	                        $cells->setAlignment('center');
	                    });
	                    $sheet->cell(('J'.$startRows),function($cells){
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}
    }

    public function returns(Request $request)
    {
    	$data = [
			'title'       => trans('rep.return'),
			'icon'        => 'fa fa-shopping-cart',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
		];
		return view('reports.return', $data);
    }

    public function delivery_1(Request $request)
    {
    	$data = [
			'title'       => trans('rep.delivery_1'),
			'icon'        => 'fa fa-shopping-cart',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
		];
		return view('reports.delivery_1', $data);
    }

    public function order_2(Request $request)
    {
    	$data = [
			'title'       => trans('rep.order_2'),
			'icon'        => 'fa fa-shopping-cart',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
		];
		return view('reports.purchase.order_2', $data);
    }

    public function usage(Request $request)
    {
    	$data = [
			'title'       => trans('rep.usage'),
			'icon'        => 'fa fa-shopping-cart',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
		];
		return view('reports.usage.usage', $data);
    }

    public function return_usage(Request $request)
    {
    	$data = [
			'title'       => trans('rep.return_usage'),
			'icon'        => 'fa fa-shopping-cart',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
		];
		return view('reports.usage.return', $data);
    }
	public function usageHouse(Request $request)
    {
    	$data = [
			'title'       => trans('rep.usage_house'),
			'icon'        => 'fa fa-shopping-cart',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
		];
		return view('reports.usage.usage_house', $data);
    }
    public function sub_boq(Request $request)
    {
    	$data = [
			'title'       => trans('rep.sub_boq'),
			'icon'        => 'fa fa-shopping-cart',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
		];
		return view('reports.boq.sub_boq', $data);
    }

    public function stock_balance(Request $request)
    {
    	$data = [
			'title'       => trans('rep.stock_balance'),
			'icon'        => 'fa fa-balance-scale',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
					'url' 		=> url('/'),
					'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
					'url' 		=> '#',
					'caption' 	=> trans('lang.report'),
				],
				'stock'	=> [
					'caption' 	=> trans('lang.report_stock_balance'),
				]
			],
		];
		return view('reports.stock_balance', $data);
    }

    public function stock_balance_get_date(Request $request){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
    	$start_date = $request->start_date;
		$end_date = $request->end_date; 
		$project_id   = $request->session()->get('project');
		$warehouse_id = "";
		$item_id = "";

		if($request->warehouse_id){
			foreach ($request->warehouse_id as $key => $value) {
				if($warehouse_id==""){
					$warehouse_id = $value;
				}else{
					$warehouse_id .= ",".$value;
				}
			}
		}

		if($request->item_id){
			foreach ($request->item_id as $key => $value) {
				if($item_id==""){
					$item_id = $value;
				}else{
					$item_id .= ",".$value;
				}
			}
		}
		if($item_id != ""){
			$item_id = " AND pr_stocks.`item_id` IN($item_id) ";
		}
		if($warehouse_id != ""){
			$warehouse_id = " AND pr_stocks.`warehouse_id` IN($warehouse_id) ";
		}

		$prefix = getSetting()->round_number;
		$sql = "SELECT G.warehouse_id, (SELECT pr_warehouses.`name` FROM pr_warehouses WHERE pr_warehouses.`id` = G.warehouse_id) AS warehouse, 
		(SELECT CONCAT(pr_system_datas.`name`) AS `cat_name` 
		FROM pr_system_datas WHERE pr_system_datas.`id` = (SELECT pr_items.`cat_id` FROM pr_items WHERE pr_items.`id` = G.item_id LIMIT 1) LIMIT 1) AS cat_name, 
		G.item_id, (SELECT CONCAT(pr_items.`name`) AS `name` FROM pr_items WHERE pr_items.`id` = G.item_id) AS item, 
		G.item_id, (SELECT CONCAT(pr_items.`code`) AS `code` FROM pr_items WHERE pr_items.`id` = G.item_id) AS item_code, 
		G.unit_stock, ROUND(G.begin_balance, $prefix)AS begin_balance, ROUND(G.stock_in, $prefix)AS stock_in, 
		ROUND(G.stock_out, $prefix)AS stock_out, ROUND(ROUND(G.begin_balance, $prefix) + (ROUND(G.stock_in, $prefix) + ROUND(G.stock_out, $prefix)), $prefix) AS ending_balance 
		FROM (SELECT F.warehouse_id, F.item_id, F.unit_stock, SUM(F.begin_balance) AS begin_balance, SUM(F.stock_in) AS stock_in, SUM(F.stock_out) AS stock_out 
		FROM (SELECT E.warehouse_id, E.item_id, E.unit_stock, (0) AS begin_balance, SUM(E.stock_in) AS stock_in, SUM(E.stock_out) AS stock_out 
		FROM (SELECT D.warehouse_id, D.item_id, D.unit_stock, (CASE WHEN D.stock_qty >= 0 THEN D.stock_qty ELSE 0 END ) AS stock_in, 
		(CASE WHEN D.stock_qty <= 0 THEN D.stock_qty ELSE 0 END ) AS stock_out FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty, 
		C.warehouse_id FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty 
		FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock 
		FROM (SELECT pr_stocks.`item_id`, pr_stocks.`unit`, pr_stocks.`qty`, pr_stocks.`warehouse_id` FROM pr_stocks WHERE pr_stocks.`delete` = 0 $item_id $warehouse_id AND pr_stocks.`pro_id` = $project_id AND pr_stocks.`trans_date` BETWEEN '$start_date'AND '$end_date') AS A) AS B) AS C) AS D) AS E GROUP BY E.warehouse_id, E.item_id, E.unit_stock UNION SELECT E.warehouse_id, E.item_id, E.unit_stock, SUM(E.stock_qty) AS begin_balance, (0) AS stock_in, (0) AS stock_out FROM (SELECT D.warehouse_id, D.item_id, D.unit_stock, D.stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty, C.warehouse_id FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_stocks.`item_id`, pr_stocks.`unit`, pr_stocks.`qty`, pr_stocks.`warehouse_id` FROM pr_stocks WHERE pr_stocks.`delete` = 0 $item_id $warehouse_id AND pr_stocks.`pro_id` = $project_id AND pr_stocks.`trans_date` < '$start_date') AS A) AS B) AS C) AS D) AS E GROUP BY E.warehouse_id, E.item_id, E.unit_stock) AS F GROUP BY F.warehouse_id, F.item_id, F.unit_stock) AS G ";
		$report = DB::select($sql);
		if ($request->version == 'generate') {
    		return $report;
    	}else if($request->version == 'excel'){
    		Excel::create('stock_details_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet('stock details',function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A2');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(80);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('B'.$startRows.':I'.$startRows));
					$sheet->cell(('B'.$startRows),getSetting()->report_header);
					$sheet->cell(('B'.$startRows.':J'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(20);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('B'.$startRows.':I'.$startRows));
					$sheet->cell(('B'.$startRows),getSetting()->company_name);
					$sheet->cell(('B'.$startRows.':J'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(20);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('B'.$startRows.':I'.$startRows));
					$sheet->cell(('B'.$startRows),trans('lang.report_stock_balance'));
					$sheet->cell(('B'.$startRows.':J'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(14);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':B'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':B'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.line_no'));
                    $sheet->cell(('B'.$startRows),trans('lang.code'));
                    $sheet->cell(('C'.$startRows),trans('lang.items'));
                    $sheet->cell(('D'.$startRows),trans('lang.item_type'));
                    $sheet->cell(('E'.$startRows),trans('lang.unit_stock'));
                    $sheet->cell(('F'.$startRows),trans('lang.begin_balance'));
                    $sheet->cell(('G'.$startRows),trans('lang.stock_in'));
                    $sheet->cell(('H'.$startRows),trans('lang.stock_out'));
                    $sheet->cell(('I'.$startRows),trans('lang.ending_balance'));
                    $sheet->cell(('J'.$startRows),trans('lang.warehouse'));

                    $sheet->cell(('A'.$startRows.':J'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as $key => $dval) {
                		$startRows++;
            			$sheet->cell('A'.($startRows), formatZero(($key + 1), 3));
				        $sheet->cell('B'.($startRows), $dval->item_code);
				        $sheet->cell('C'.($startRows), $dval->item);
				        $sheet->cell('D'.($startRows), $dval->cat_name);
				        $sheet->cell('E'.($startRows), $dval->unit_stock);
	                    $sheet->cell('F'.($startRows), $dval->begin_balance);
	                    $sheet->cell('G'.($startRows), $dval->stock_in);
	                    $sheet->cell('H'.($startRows), $dval->stock_out);
	                    $sheet->cell('I'.($startRows), $dval->ending_balance);
	                    $sheet->cell('J'.($startRows), $dval->warehouse);
	                    $sheet->cell(('A'.$startRows.':J'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                    });
	                    $sheet->cell(('D'.$startRows),function($cells){
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}
    }

    public function purchase_items(Request $request)
    {
    	$data = [
			'title'       => trans('lang.purchase_items'),
			'icon'        => 'fa fa-area-chart',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.purchase_items'),
				],
			],
		];
		return view('reports.purchase_items', $data);
    }

    function  purchase_items_get_date(Request $request){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
    	$start_date = $request->start_date;
		$end_date = $request->end_date;
		$project_id   = $request->session()->get('project');
		$item_id = "";
		$report = [];
		if(count($request->item_id) > 0){
			foreach ($request->item_id as $key => $value) {
				if($item_id==""){
					$item_id = $value;
				}else{
					$item_id .= ",".$value;
				}
			}
		}

		$sql = "SELECT E.id, E.ref_no, E.trans_date, E.item_id, (SELECT CONCAT(pr_items.`code`, ' (', pr_items.`name`, ')') AS `name` FROM pr_items WHERE pr_items.`id` = E.item_id) AS item_name,(SELECT CONCAT(pr_system_datas.`name`, ' (', pr_system_datas.`desc`, ')') AS `cat_name` FROM pr_system_datas WHERE pr_system_datas.`id` = (SELECT pr_items.`cat_id` FROM pr_items WHERE pr_items.`id` = E.item_id LIMIT 1) LIMIT 1)AS cat_name, E.pr_qty, E.pr_close_qty, (CASE WHEN E.po_qty != ''THEN E.po_qty ELSE 0 END ) AS po_qty, (CASE WHEN E.po_close_qty != ''THEN E.po_close_qty ELSE 0 END ) AS po_close_qty, (CASE WHEN E.deliv_qty != ''THEN E.deliv_qty ELSE 0 END ) AS deliv_qty, E.unit_stock FROM (SELECT D.id, D.ref_no, D.trans_date, D.item_id, D.pr_qty, D.pr_close_qty, D.unit_stock, SUM(D.po_qty) AS po_qty, SUM(D.po_close_qty) AS po_close_qty, SUM(D.deliv_qty) AS deliv_qty FROM (SELECT C.id, C.po_id, C.ref_no, C.trans_date, C.request_by, C.dep_id, C.item_id, C.pr_qty, C.pr_close_qty, C.unit_stock, (C.po_qty * C.po_stock_qty) AS po_qty, (C.po_close_qty * C.po_stock_qty) AS po_close_qty, (C.deliv_qty * C.po_stock_qty) AS deliv_qty FROM (SELECT B.id, pr_order_items.`po_id`, B.ref_no, B.trans_date, B.request_by, B.dep_id, B.item_id, (B.pr_qty * B.pr_stock_qty) AS pr_qty, (B.pr_close_qty * B.pr_stock_qty) AS pr_close_qty, B.unit_stock, pr_order_items.`qty` AS po_qty, pr_order_items.`closed_qty` AS po_close_qty, pr_order_items.`deliv_qty`, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = pr_order_items.`unit` AND pr_units.`to_code` = B.unit_stock) AS po_stock_qty FROM (SELECT A.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = A.pr_unit AND pr_units.`to_code` = A.unit_stock) AS pr_stock_qty, pr_orders.`id` AS po_id FROM (SELECT pr_requests.id, pr_requests.`ref_no`, pr_requests.`trans_date`, pr_requests.`request_by`, pr_requests.`dep_id`, pr_request_items.`item_id`, pr_request_items.`unit` AS pr_unit, pr_request_items.`qty` AS pr_qty, pr_request_items.`closed_qty` AS pr_close_qty, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = pr_request_items.`item_id`) AS unit_stock FROM pr_requests INNER JOIN pr_request_items ON pr_requests.`id` = pr_request_items.`pr_id` AND pr_request_items.`item_id` IN ($item_id) AND pr_requests.`pro_id` = $project_id AND pr_requests.`trans_status` != 0 AND pr_requests.`trans_date` BETWEEN '$start_date'AND '$end_date') AS A LEFT JOIN pr_orders ON A.`id` = pr_orders.`pr_id` AND pr_orders.`trans_status` != 0) AS B LEFT JOIN pr_order_items ON B.po_id = pr_order_items.`po_id` AND B.item_id = pr_order_items.`item_id`) AS C) AS D GROUP BY D.item_id, D.id, D.ref_no, D.trans_date, D.pr_qty, D.pr_close_qty, D.unit_stock) AS E ORDER BY E.trans_date ASC ";
		$report = DB::select($sql);
		if ($request->version == 'generate') {
    		return $report;
    	}else if($request->version == 'excel'){
    		Excel::create('stock_details_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet('stock details',function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A2');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(80);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('B'.$startRows.':H'.$startRows));
					$sheet->cell(('B'.$startRows),getSetting()->report_header);
					$sheet->cell(('B'.$startRows.':H'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(20);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('B'.$startRows.':H'.$startRows));
					$sheet->cell(('B'.$startRows),getSetting()->company_name);
					$sheet->cell(('B'.$startRows.':H'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(20);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('B'.$startRows.':H'.$startRows));
					$sheet->cell(('B'.$startRows),trans('lang.stock_details'));
					$sheet->cell(('B'.$startRows.':H'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(14);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.ref_type'));
                    $sheet->cell(('B'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('C'.$startRows),trans('lang.trans_ref'));
                    $sheet->cell(('D'.$startRows),trans('lang.reference'));
                    $sheet->cell(('E'.$startRows),trans('lang.trans_line'));
                    $sheet->cell(('F'.$startRows),trans('lang.qty'));
                    $sheet->cell(('G'.$startRows),trans('lang.on_hand'));
                    $sheet->cell(('H'.$startRows),trans('lang.units'));

                    $sheet->cell(('A'.$startRows.':H'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as $key => $dval) {
                		if(is_array($dval)){
                			$startRows++;
                			foreach ($dval as $val) {
						        $sheet->mergeCells(('A'.$startRows.':B'.$startRows));
	                			$sheet->cell('A'.($startRows), formatZero(($key + 1), 3).' - '.$val->item);
						        $sheet->cell('C'.($startRows), trans("lang.item_type"));
						        $sheet->mergeCells(('D'.$startRows.':E'.$startRows));
						        $sheet->cell('D'.($startRows), $val->cat_name);
			                    $sheet->cell('F'.($startRows), trans("lang.begin_balance"));
			                    $sheet->cell('G'.($startRows), $val->begin_balance);
			                    $sheet->cell('H'.($startRows), $val->unit_stock);
			                    $sheet->cell(('A'.$startRows.':H'.$startRows),function($cells){
			                        $cells->setFontFamily('Khmer OS Battambang');
			                        $cells->setFontSize(11);
			                    });
			                    $sheet->cell(('B'.$startRows),function($cells){
			                        $cells->setAlignment('center');
			                    });
			                    $sheet->cell(('E'.$startRows),function($cells){
			                        $cells->setAlignment('center');
			                    });
			                    $sheet->cell(('H'.$startRows),function($cells){
			                        $cells->setAlignment('center');
			                    });
                			}

                			$stock_qty = 0;
                			$begin_balance = 0;
                			$unit_stock = "";
                			$item_name = "";
                			$item_category = "";
                			foreach ($dval as $val) {
                				$stock_qty = $stock_qty + floatval($val->stock_qty);
                				$begin_balance = $begin_balance + floatval($val->begin_balance);
                				$unit_stock = $val->unit_stock;
                				$item_name = $val->item;
                				$item_category = $val->cat_name;
		                		$startRows++;

						        $sheet->cell('A'.($startRows),$val->ref_type);
						        $sheet->cell('B'.($startRows),date("d/m/Y",strtotime($val->trans_date)));
						        $sheet->cell('C'.($startRows),$val->ref_no);
			                    $sheet->cell('D'.($startRows),$val->reference);
			                    $sheet->cell('E'.($startRows),$val->line_no);
			                    $sheet->cell('F'.($startRows),$val->stock_qty);
			                    $sheet->cell('G'.($startRows),(floatval($val->begin_balance) + $stock_qty));
			                    $sheet->cell('H'.($startRows),$val->unit_stock);
			                    $sheet->cell(('A'.$startRows.':H'.$startRows),function($cells){
			                        $cells->setFontFamily('Khmer OS Battambang');
			                        $cells->setFontSize(11);
			                    });
			                    $sheet->cell(('B'.$startRows),function($cells){
			                        $cells->setAlignment('center');
			                    });
			                    $sheet->cell(('E'.$startRows),function($cells){
			                        $cells->setAlignment('center');
			                    });
			                    $sheet->cell(('H'.$startRows),function($cells){
			                        $cells->setAlignment('center');
			                    });
                			}
                			$startRows++;
                			$sheet->cell(('A'.$startRows.':H'.$startRows),function($cells){
		                        $cells->setFontFamily('Khmer OS Battambang');
		                        $cells->setFontSize(11);
		                    });
		                    $sheet->cell(('B'.$startRows),function($cells){
		                        $cells->setAlignment('center');
		                    });
		                    $sheet->cell(('E'.$startRows),function($cells){
		                        $cells->setAlignment('center');
		                    });
		                    $sheet->cell(('H'.$startRows),function($cells){
		                        $cells->setAlignment('center');
		                    });
                			$sheet->mergeCells(('A'.$startRows.':B'.$startRows));
                			$sheet->cell('A'.($startRows), formatZero(($key + 1), 3).' - '.$item_name);
					        $sheet->cell('C'.($startRows), trans("lang.item_type"));
					        $sheet->mergeCells(('D'.$startRows.':E'.$startRows));
					        $sheet->cell('D'.($startRows), $item_category);
		                    $sheet->cell('F'.($startRows), trans("lang.ending_balance"));
		                    $sheet->cell('G'.($startRows), ($begin_balance + $stock_qty));
		                    $sheet->cell('H'.($startRows), $unit_stock);
                		}else{
                			$startRows++;
                			$sheet->mergeCells(('A'.$startRows.':H'.$startRows));
                			$sheet->cell('A'.($startRows), formatZero(($key + 1), 3).' - '.$dval->code. ' ('.$dval->name.')');
                		}
                	}
                });
            })->download('xlsx');
    	}
    }

    public function stock_details(Request $request)
    {
    	$data = [
			'title'       => trans('lang.stock_details'),
			'icon'        => 'fa fa-area-chart',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.stock_details'),
				],
			],
		];
		return view('reports.stock_details', $data);
    }

    function  stock_details_get_date(Request $request){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
    	$start_date = $request->start_date;
		$end_date = $request->end_date;
		$project_id   = $request->session()->get('project');
		$warehouse_id = "";
		$report = [];
		if(count($request->warehouse_id) > 0){
			foreach ($request->warehouse_id as $key => $value) {
				if($warehouse_id==""){
					$warehouse_id = $value;
				}else{
					$warehouse_id .= ",".$value;
				}
			}
		}
		if(count($request->item_id) > 0){
			foreach ($request->item_id as $key => $value) {
				$sql = "SELECT F.item_id,F.item,F.cat_name,F.ref_type,F.ref_no,(CASE WHEN F.reference!='' THEN F.reference ELSE '' END)AS reference,F.line_no,F.trans_date,(CASE WHEN F.stock_qty!='' THEN F.stock_qty ELSE 0 END)AS stock_qty,(CASE WHEN F.begin_balance!='' THEN F.begin_balance ELSE 0 END)AS begin_balance,F.unit_stock FROM (SELECT E.*, (SELECT CONCAT(pr_system_datas.`name`, ' (', pr_system_datas.`desc`, ')') AS `cat_name` FROM pr_system_datas WHERE pr_system_datas.`id` = (SELECT pr_items.`cat_id` FROM pr_items WHERE pr_items.`id` = E.item_id LIMIT 1) LIMIT 1) AS cat_name, (SELECT CONCAT(pr_items.`code`, ' (', pr_items.`name`, ')') AS `name` FROM pr_items WHERE pr_items.`id` = E.item_id) AS item, (SELECT SUM(Z.stock_qty) AS begin_balance FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_stocks.`item_id`, pr_stocks.`unit`, pr_stocks.`qty` FROM pr_stocks WHERE pr_stocks.`delete` = 0 AND pr_stocks.`pro_id` = $project_id AND pr_stocks.`item_id` = $value AND pr_stocks.`warehouse_id` IN ('$warehouse_id') AND pr_stocks.`trans_date` < '$start_date') AS A) AS B) AS C) AS Z GROUP BY Z.item_id, Z.unit_stock) AS begin_balance FROM (SELECT CC.item_id,CC.ref_type,CC.ref_no,CC.reference,CC.line_no,CC.trans_date,CC.unit_stock,SUM(CC.stock_qty)AS stock_qty FROM(SELECT C.item_id, C.`ref_type`, C.`ref_no`, C.`reference`, C.`line_no`, C.`trans_date`, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_stocks.`item_id`, pr_stocks.`ref_type`, pr_stocks.`ref_no`, pr_stocks.`reference`, pr_stocks.`line_no`, pr_stocks.`trans_date`, pr_stocks.`unit`, pr_stocks.`qty` FROM pr_stocks WHERE pr_stocks.`delete` = 0 AND pr_stocks.`pro_id` = $project_id AND pr_stocks.`item_id` = $value AND pr_stocks.`warehouse_id` IN ($warehouse_id) AND pr_stocks.`trans_date` BETWEEN '$start_date'AND '$end_date') AS A) AS B) AS C) AS CC GROUP BY CC.item_id, CC.ref_type,CC.ref_no,CC.line_no,CC.trans_date) AS E) AS F ORDER BY F.trans_date ASC"; 
				$record = DB::select($sql);
				if ($record) {
					$report[$key] = $record;
				}else{
					$sql = "SELECT F.id, F.item, F.cat_name, SUM(F.begin_balance) AS begin_balance, F.unit_stock FROM (SELECT (E.item_id) AS id, (SELECT CONCAT(pr_items.`code`, '(', `pr_items`.`name`, ')') AS `name` FROM pr_items WHERE pr_items.`id` = E.item_id) AS item, (SELECT CONCAT(pr_system_datas.`name`, ' (', pr_system_datas.`desc`, ')') AS `cat_name` FROM pr_system_datas WHERE pr_system_datas.`id` = (SELECT pr_items.`cat_id` FROM pr_items WHERE pr_items.`id` = E.item_id LIMIT 1) LIMIT 1) AS cat_name, E.begin_balance, E.unit_stock FROM (SELECT D.item_id, D.unit_stock, SUM(D.stock_qty) AS begin_balance FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_stocks.`item_id`, pr_stocks.`unit`, pr_stocks.`qty` FROM pr_stocks WHERE pr_stocks.`delete` = 0 AND pr_stocks.`pro_id` = $project_id AND pr_stocks.`item_id` = $value AND pr_stocks.`warehouse_id` IN ('$warehouse_id') AND pr_stocks.`trans_date` < '$start_date') AS A) AS B) AS C) AS D GROUP BY D.item_id, D.unit_stock) AS E UNION (SELECT pr_items.id, (CONCAT(pr_items.`code`, '(', `pr_items`.`name`, ')') ) AS item, (SELECT CONCAT(pr_system_datas.`name`, ' (', pr_system_datas.`desc`, ')') AS `cat_name` FROM pr_system_datas WHERE pr_system_datas.`id` = pr_items.`cat_id`) AS cat_name, (0) AS begin_balance, pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = $value)) AS F GROUP BY F.id ";
					$record = collect(DB::select($sql))->first();
					$report[$key] = $record;
				}
			}
		}
		if ($request->version == 'generate') {
    		return $report;
    	}else if($request->version == 'excel'){
    		Excel::create('stock_details_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet('stock details',function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A2');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(80);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('B'.$startRows.':H'.$startRows));
					$sheet->cell(('B'.$startRows),getSetting()->report_header);
					$sheet->cell(('B'.$startRows.':H'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(20);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('B'.$startRows.':H'.$startRows));
					$sheet->cell(('B'.$startRows),getSetting()->company_name);
					$sheet->cell(('B'.$startRows.':H'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(20);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('B'.$startRows.':H'.$startRows));
					$sheet->cell(('B'.$startRows),trans('lang.stock_details'));
					$sheet->cell(('B'.$startRows.':H'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(14);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.ref_type'));
                    $sheet->cell(('B'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('C'.$startRows),trans('lang.trans_ref'));
                    $sheet->cell(('D'.$startRows),trans('lang.reference'));
                    $sheet->cell(('E'.$startRows),trans('lang.trans_line'));
                    $sheet->cell(('F'.$startRows),trans('lang.qty'));
                    $sheet->cell(('G'.$startRows),trans('lang.on_hand'));
                    $sheet->cell(('H'.$startRows),trans('lang.units'));

                    $sheet->cell(('A'.$startRows.':H'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as $key => $dval) {
                		if(is_array($dval)){
                			$startRows++;
                			foreach ($dval as $val) {
						        $sheet->mergeCells(('A'.$startRows.':B'.$startRows));
	                			$sheet->cell('A'.($startRows), formatZero(($key + 1), 3).' - '.$val->item);
						        $sheet->cell('C'.($startRows), trans("lang.item_type"));
						        $sheet->mergeCells(('D'.$startRows.':E'.$startRows));
						        $sheet->cell('D'.($startRows), $val->cat_name);
			                    $sheet->cell('F'.($startRows), trans("lang.begin_balance"));
			                    $sheet->cell('G'.($startRows), $val->begin_balance);
			                    $sheet->cell('H'.($startRows), $val->unit_stock);
			                    $sheet->cell(('A'.$startRows.':H'.$startRows),function($cells){
			                        $cells->setFontFamily('Khmer OS Battambang');
			                        $cells->setFontSize(11);
			                    });
			                    $sheet->cell(('B'.$startRows),function($cells){
			                        $cells->setAlignment('center');
			                    });
			                    $sheet->cell(('E'.$startRows),function($cells){
			                        $cells->setAlignment('center');
			                    });
			                    $sheet->cell(('H'.$startRows),function($cells){
			                        $cells->setAlignment('center');
			                    });
                			}

                			$stock_qty = 0;
                			$begin_balance = 0;
                			$unit_stock = "";
                			$item_name = "";
                			$item_category = "";
                			foreach ($dval as $val) {
                				$stock_qty = $stock_qty + floatval($val->stock_qty);
                				$begin_balance = $begin_balance + floatval($val->begin_balance);
                				$unit_stock = $val->unit_stock;
                				$item_name = $val->item;
                				$item_category = $val->cat_name;
		                		$startRows++;

						        $sheet->cell('A'.($startRows),$val->ref_type);
						        $sheet->cell('B'.($startRows),date("d/m/Y",strtotime($val->trans_date)));
						        $sheet->cell('C'.($startRows),$val->ref_no);
			                    $sheet->cell('D'.($startRows),$val->reference);
			                    $sheet->cell('E'.($startRows),$val->line_no);
			                    $sheet->cell('F'.($startRows),$val->stock_qty);
			                    $sheet->cell('G'.($startRows),(floatval($val->begin_balance) + $stock_qty));
			                    $sheet->cell('H'.($startRows),$val->unit_stock);
			                    $sheet->cell(('A'.$startRows.':H'.$startRows),function($cells){
			                        $cells->setFontFamily('Khmer OS Battambang');
			                        $cells->setFontSize(11);
			                    });
			                    $sheet->cell(('B'.$startRows),function($cells){
			                        $cells->setAlignment('center');
			                    });
			                    $sheet->cell(('E'.$startRows),function($cells){
			                        $cells->setAlignment('center');
			                    });
			                    $sheet->cell(('H'.$startRows),function($cells){
			                        $cells->setAlignment('center');
			                    });
                			}
                			$startRows++;
                			$sheet->cell(('A'.$startRows.':H'.$startRows),function($cells){
		                        $cells->setFontFamily('Khmer OS Battambang');
		                        $cells->setFontSize(11);
		                    });
		                    $sheet->cell(('B'.$startRows),function($cells){
		                        $cells->setAlignment('center');
		                    });
		                    $sheet->cell(('E'.$startRows),function($cells){
		                        $cells->setAlignment('center');
		                    });
		                    $sheet->cell(('H'.$startRows),function($cells){
		                        $cells->setAlignment('center');
		                    });
                			$sheet->mergeCells(('A'.$startRows.':B'.$startRows));
                			$sheet->cell('A'.($startRows), formatZero(($key + 1), 3).' - '.$item_name);
					        $sheet->cell('C'.($startRows), trans("lang.item_type"));
					        $sheet->mergeCells(('D'.$startRows.':E'.$startRows));
					        $sheet->cell('D'.($startRows), $item_category);
		                    $sheet->cell('F'.($startRows), trans("lang.ending_balance"));
		                    $sheet->cell('G'.($startRows), ($begin_balance + $stock_qty));
		                    $sheet->cell('H'.($startRows), $unit_stock);
                		}else{
                			$startRows++;
                			$sheet->cell(('A'.$startRows.':H'.$startRows),function($cells){
		                        $cells->setFontFamily('Khmer OS Battambang');
		                        $cells->setFontSize(11);
		                    });
		                    $sheet->cell(('B'.$startRows),function($cells){
		                        $cells->setAlignment('center');
		                    });
		                    $sheet->cell(('E'.$startRows),function($cells){
		                        $cells->setAlignment('center');
		                    });
		                    $sheet->cell(('H'.$startRows),function($cells){
		                        $cells->setAlignment('center');
		                    });
                			$sheet->mergeCells(('A'.$startRows.':B'.$startRows));
                			$sheet->cell('A'.($startRows), formatZero(($key + 1), 3).' - '.$dval->item);
					        $sheet->cell('C'.($startRows), trans("lang.item_type"));
					        $sheet->mergeCells(('D'.$startRows.':E'.$startRows));
					        $sheet->cell('D'.($startRows), $dval->cat_name);
		                    $sheet->cell('F'.($startRows), trans("lang.ending_balance"));
		                    $sheet->cell('G'.($startRows), $dval->begin_balance);
		                    $sheet->cell('H'.($startRows), $dval->unit_stock);
                		}
                	}
                });
            })->download('xlsx');
    	}
    }

    public function all_stock_transaction(Request $request)
    {
    	$data = [
			'title'       => trans('rep.all_stock_transaction'),
			'icon'        => 'fa fa-bar-chart',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
		];
		return view('reports.all_stock_transaction', $data);
    }

    public function boq_detail(Request $request)
    {
    	$data = [
			'title'       => trans('rep.boq_detail'),
			'icon'        => 'fa fa-shopping-cart',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
		];
		return view('reports.boq.boq_detail', $data);
    }

    public function print_request(Request $request,$id)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
    	$id = decrypt($id);
		$prefix = DB::getTablePrefix();
    	$request_obj = PurchaseRequest::select(['*',
    		DB::raw("(select concat({$prefix}constructors.id_card,' ',{$prefix}constructors.name,' (',{$prefix}constructors.tel,')')AS aa_ from {$prefix}constructors where {$prefix}constructors.id=request_by)AS request_by_people"),
    		DB::raw("(select {$prefix}users.position from {$prefix}users where {$prefix}users.id=created_by)AS position"),
    		DB::raw("(select {$prefix}system_datas.desc from {$prefix}system_datas where {$prefix}system_datas.id=dep_id)AS department"),
    		DB::raw("(select {$prefix}users.signature from {$prefix}users where {$prefix}users.id=created_by)AS signature"),
    		DB::raw("(select {$prefix}users.name from {$prefix}users where {$prefix}users.id=created_by)AS user_request")])->where('id',$id)->get()->first();
    	$requestItems_obj = RequestItem::select([
    		'id','line_no','pr_id','item_id',
    		DB::raw("(select {$prefix}items.code from {$prefix}items where {$prefix}items.id=item_id)AS item_code"),
    		DB::raw("(select {$prefix}items.name from {$prefix}items where {$prefix}items.id=item_id)AS item_name"),
    		DB::raw("(select {$prefix}units.from_desc from {$prefix}units where {$prefix}units.from_code=unit limit 1)AS unit_stock"),
    		'qty','unit','price','desc','remark','size'
    	])->where('pr_id',$id)->get();
    	$request_approve = ApproveRequest::select(['*',
    		DB::raw("(select {$prefix}users.position from {$prefix}users where {$prefix}users.id=approved_by)AS position"),
    		DB::raw("(select {$prefix}users.name from {$prefix}users where {$prefix}users.id=approved_by)AS approved_people")])->where('pr_id',$id)->get();

    	if (!$request_obj) {
    		exit();
    	}

    	if (count($requestItems_obj)==0) {
    		exit();
    	}

    	if (count($request_approve)==0) {
    		exit();
    	}

    	$data = [
			'title'            => trans('lang.pr_paper'),
			'icon'             => 'fa fa-shopping-cart',
			'small_title'      => trans('lang.report'),
			'background'       => '',
			'request_obj'      =>$request_obj,
			'requestItems_obj' =>$requestItems_obj,
			'request_approve'  =>$request_approve,
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'approval'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.approval'),
				],
				'request'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.request'),
				],
				'print'	=> [
						'caption' 	=> trans('lang.print'),
				],
			],
		];
		return view('approval.request.print', $data);
    }

    public function print_order(Request $request,$id)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
    	$id = decrypt($id);
		$prefix = DB::getTablePrefix();
    	$order = Order::select(['*',
    		DB::raw("(select {$prefix}users.position from {$prefix}users where {$prefix}users.id=created_by)AS position"),
    		DB::raw("(select {$prefix}users.name from {$prefix}users where {$prefix}users.id=created_by)AS user_request"),
    		DB::raw("(select {$prefix}users.signature from {$prefix}users where {$prefix}users.id=created_by)AS signature"),
    		DB::raw("(select {$prefix}users.position from {$prefix}users where {$prefix}users.id=ordered_by)AS order_position"),
    		DB::raw("(select {$prefix}users.name from {$prefix}users where {$prefix}users.id=ordered_by)AS ordered_user"),
    		DB::raw("(select {$prefix}users.signature from {$prefix}users where {$prefix}users.id=ordered_by)AS ordered_signature"),
    		DB::raw("(select {$prefix}projects.name from {$prefix}projects where {$prefix}projects.id=pro_id)AS project"),
    		DB::raw("(select {$prefix}requests.ref_no from {$prefix}requests where {$prefix}requests.id=pr_id)AS pr_no"),
    		DB::raw("(select {$prefix}system_datas.name from {$prefix}system_datas where {$prefix}system_datas.id=dep_id)AS department")
    	])->where('id',$id)->get()->first();

    	$orderItems = OrderItem::select(['*',
			DB::raw("(select {$prefix}items.code from {$prefix}items where {$prefix}items.id=item_id)AS item_code"),
			DB::raw("(select {$prefix}items.name from {$prefix}items where {$prefix}items.id=item_id)AS item_name"),
			DB::raw("(select {$prefix}units.from_desc from {$prefix}units where {$prefix}units.from_code=unit limit 1)AS unit_stock")])->where('po_id',$id)->get();

    	$orderApproved = ApproveOrder::select(['*',
    		DB::raw("(select {$prefix}users.position from {$prefix}users where {$prefix}users.id=approved_by)AS position"),
    		DB::raw("(select {$prefix}users.name from {$prefix}users where {$prefix}users.id=approved_by)AS approved_people")])->where('po_id',$id)->get();

    	if (!$order) {
    		exit();
    	}

    	$supplier = Supplier::find($order->sup_id);

    	if (!$supplier) {
    		exit();
    	}

    	if (count($orderItems)==0) {
    		exit();
    	}

    	if (count($orderApproved)==0) {
    		exit();
    	}

    	$data = [
			'title'          => trans('lang.po_paper'),
			'icon'           => 'fa fa-shopping-cart',
			'small_title'    => trans('lang.report'),
			'background'     => '',
			'order'          => $order,
			'orderItems'     => $orderItems,
			'orderApproved'  => $orderApproved,
			'supplier'       => $supplier,
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'approval'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.approval'),
				],
				'order'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.order'),
				],
				'print'	=> [
						'caption' 	=> trans('lang.print'),
				],
			],
		];
		return view('approval.order.print', $data);
    }

    public function view_report_purchase_request_detail()
    {
    	$data = [
			'title'            => trans('rep.report_purchase_request_detail'),
			'icon'             => 'fa fa-shopping-cart',
			'small_title'      => trans('lang.report'),
			'background'       => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.request'),
				],
				'request_detail'	=> [
						'url' 		=> url('/report/purchase-detail'),
						'caption' 	=> trans('rep.report_purchase_request_detail'),
				],
			],
		];

    	return view("reports.purchase_request_detail",$data);
    }

    public function report_purchase_request_detail(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
    	try {
    		$start_date   = $request->query("start_date");
			$end_date     = $request->query("end_date");
			$project_id   = $request->session()->get('project');
			$dep_id       = $request->query("dep_id");
			$trans_status = $request->query("trans_status");
			$version      = $request->query("version");

			if ($dep_id!='') {
				$np = str_replace(",","','",$dep_id);
				$np .="'";
				$np = substr($np,2);
				$dep_id = "AND r.`dep_id` IN(".$np.") ";
			}else{
				$dep_id = '';
			}

			if ($trans_status!='') {
				$np = str_replace(",","','",$trans_status);
				$np .="'";
				$np = substr($np,2);
				$trans_status = "AND r.`trans_status` IN(".$np.") ";
			}else{
				$trans_status = '';
			}

	    	$sql = "SELECT ri.`id`, ri.`pr_id`, r.`ref_no`, r.`trans_date`, r.`delivery_date`, r.`note`, r.`pro_id`, (SELECT pr_projects.`name` FROM `pr_projects` WHERE `pr_projects`.`id`=r.`pro_id`)AS project, r.`dep_id`, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=r.`dep_id`)AS department, r.`request_by`, (SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id`=r.`request_by`)AS request_people_code, (SELECT `pr_constructors`.`id_card` FROM `pr_constructors` WHERE `pr_constructors`.`id`=r.`request_by`)AS request_people_name, r.`trans_status`, ri.`item_id`,(SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE pr_items.`id`=ri.`item_id`)AS category_id, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=(SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE pr_items.`id`=ri.`item_id`))AS category, (SELECT pr_items.`code` FROM `pr_items` WHERE `pr_items`.`id`=ri.item_id)AS item_code, (SELECT pr_items.`name` FROM `pr_items` WHERE `pr_items`.`id`=ri.item_id)AS item_name,(SELECT `pr_units`.`to_code` FROM `pr_units` WHERE `pr_units`.`from_code`=ri.`unit` LIMIT 1)AS unit, ri.`qty`, ri.`boq_set`, ri.`ordered_qty`, ri.`closed_qty`, ri.`price`, ri.`desc` FROM `pr_request_items` AS ri INNER JOIN `pr_requests` AS r ON ri.`pr_id` = r.`id` AND r.`pro_id` = $project_id AND r.`trans_status`!=0 $dep_id $trans_status AND r.`trans_date` BETWEEN '$start_date'AND '$end_date' ";

	    	$report = DB::select($sql);

	    	if ($version=="datatables") {
	    		$response = Datatables::of($report)->make(true);
	    	}elseif($version=="print"){
	    		$response = response()->json($report);
	    	}elseif($version=="excel"){
	    		Excel::create('purchase_request_detail_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
	                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
	                $excel->sheet(trans('rep.report_purchase_request_detail'),function($sheet) use($report,$start_date,$end_date){
	                	$startRows = 1;
						$sheet->setAutoSize(true);
						$sheet->mergeCells('A1:A3');
						$objDrawing = new PHPExcel_Worksheet_Drawing;
				        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
				        $objDrawing->setCoordinates('A1');
				        $objDrawing->setHeight(150);
				        $objDrawing->setWorksheet($sheet);

						$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
						$sheet->cell(('C'.$startRows),getSetting()->report_header);
						$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(28);
	                        $cells->setFontColor(getSetting()->report_header_color);
	                        $cells->setAlignment('center');
						});
						$startRows++;
						$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
						$sheet->cell(('C'.$startRows),getSetting()->company_name);
						$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(28);
	                        $cells->setFontColor(getSetting()->company_name_color);
	                        $cells->setAlignment('center');
						});
						$startRows++;
						$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
						$sheet->cell(('C'.$startRows),trans('rep.report_purchase_request_detail'));
						$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(16);
	                        $cells->setFontColor(getSetting()->report_header_color);
	                        $cells->setAlignment('center');
						});
						$startRows++;
						$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
						$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
						$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('left');
						});

						$startRows++;
	                    $sheet->cell(('A'.$startRows),trans('lang.trans_date'));
	                    $sheet->cell(('B'.$startRows),trans('lang.reference_no'));
	                    $sheet->cell(('C'.$startRows),trans('lang.department'));
	                    $sheet->cell(('D'.$startRows),trans('lang.request_by'));
	                    $sheet->cell(('E'.$startRows),trans('lang.item_type'));
	                    $sheet->cell(('F'.$startRows),trans('lang.item_code'));
	                    $sheet->cell(('G'.$startRows),trans('lang.item_name'));
	                    $sheet->cell(('H'.$startRows),trans('lang.qty'));
	                    $sheet->cell(('I'.$startRows),trans('lang.units'));
	                    $sheet->cell(('J'.$startRows),trans('lang.boq'));
	                    $sheet->cell(('K'.$startRows),trans('lang.status'));
	                    $sheet->cell(('L'.$startRows),trans('lang.desc'));

	                    $sheet->cell(('A'.$startRows.':L'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setFontColor('#ffffff');
	                        $cells->setBackground(getSetting()->report_header_color);
	                        $cells->setAlignment('center');
	                    });

	                	foreach ($report as  $dval) {
	                		$startRows++;
	                		$trans_status = '';
	                		if ($dval->trans_status==1) {
	                			$trans_status = 'Pendding';
	                		}elseif($dval->trans_status==2){
	                			$trans_status = 'Approving';
	                		}elseif($dval->trans_status==3){
	                			$trans_status = 'Completed';
	                		}elseif($dval->trans_status==4){
	                			$trans_status = 'Rejected';
	                		}
					        $sheet->cell('A'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
					        $sheet->cell('B'.($startRows),$dval->ref_no);
		                    $sheet->cell('C'.($startRows),($dval->department!=null ? $dval->department : '#N/A'));
		                    $sheet->cell('D'.($startRows),($dval->request_people_name!=null ? $dval->request_people_name : '#N/A'));
		                    $sheet->cell('E'.($startRows),$dval->category);
		                    $sheet->cell('F'.($startRows),$dval->item_code);
		                    $sheet->cell('G'.($startRows),$dval->item_name);
		                    $sheet->cell('H'.($startRows),$dval->qty);
		                    $sheet->cell('I'.($startRows),$dval->unit);
		                    $sheet->cell('J'.($startRows),($dval->boq_set==-1 ? 'No' : 'Yes'));
		                    $sheet->cell('K'.($startRows),$trans_status);
		                    $sheet->cell('L'.($startRows),$dval->desc);
		                    $sheet->setColumnFormat([('H'.$startRows)=>'0']);
		                    $sheet->cell(('A'.$startRows.':L'.$startRows),function($cells){
		                        $cells->setFontFamily('Khmer OS Battambang');
		                        $cells->setFontSize(11);
		                        $cells->setAlignment('center');
		                    });
	                	}
	                });
	            })->download('xlsx');
	    	}

	    	return $response;
    	} catch (\Exception $e) {
    		var_dump($e->getMessage());exit;
    	}
    }

    public function view_report_purchase_and_order()
    {
    	$data = [
			'title'            => trans('rep.report_purchase_and_order'),
			'icon'             => 'fa fa-shopping-cart',
			'small_title'      => trans('lang.report'),
			'background'       => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.request'),
				],
				'request_detail'	=> [
						'url' 		=> url('/report/purchase-and-order'),
						'caption' 	=> trans('rep.report_purchase_and_order'),
				],
			],
		];

    	return view("reports.purchase.request",$data);
    }

    public function report_purchase_and_order(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
    	try {
    		$start_date   = $request->query("start_date");
			$end_date     = $request->query("end_date");
			$project_id   = $request->session()->get('project');
			$dep_id       = $request->query("dep_id");
			$trans_status = $request->query("trans_status");
			$version      = $request->query("version");

			if ($dep_id!='') {
				$np = str_replace(",","','",$dep_id);
				$np .="'";
				$np = substr($np,2);
				$dep_id = "AND r.`dep_id` IN(".$np.") ";
			}else{
				$dep_id = '';
			}

			if ($trans_status!='') {
				$np = str_replace(",","','",$trans_status);
				$np .="'";
				$np = substr($np,2);
				$trans_status = "AND r.`trans_status` IN(".$np.") ";
			}else{
				$trans_status = '';
			}

	    	$sql = "SELECT ri.`id`, ri.`pr_id`, r.`ref_no`, r.`trans_date`, r.`delivery_date`, r.`note`, r.`pro_id`, (SELECT pr_projects.`name` FROM `pr_projects` WHERE `pr_projects`.`id`=r.`pro_id`)AS project, r.`dep_id`, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=r.`dep_id`)AS department, r.`request_by`, (SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id`=r.`request_by`)AS request_people_code, (SELECT `pr_constructors`.`id_card` FROM `pr_constructors` WHERE `pr_constructors`.`id`=r.`request_by`)AS request_people_name, r.`trans_status`, ri.`item_id`,(SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE pr_items.`id`=ri.`item_id`)AS category_id, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=(SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE pr_items.`id`=ri.`item_id`))AS category, (SELECT pr_items.`code` FROM `pr_items` WHERE `pr_items`.`id`=ri.item_id)AS item_code, (SELECT pr_items.`name` FROM `pr_items` WHERE `pr_items`.`id`=ri.item_id)AS item_name,(SELECT `pr_units`.`to_code` FROM `pr_units` WHERE `pr_units`.`from_code`=ri.`unit` LIMIT 1)AS unit, ri.`qty`, ri.`boq_set`, ri.`ordered_qty`, ri.`closed_qty`, ri.`price`, ri.`desc` FROM `pr_request_items` AS ri INNER JOIN `pr_requests` AS r ON ri.`pr_id` = r.`id` AND r.`pro_id` = $project_id AND r.`trans_status`!=0 $dep_id $trans_status AND r.`trans_date` BETWEEN '$start_date'AND '$end_date' ";

	    	$report = DB::select($sql);

	    	if ($version=="datatables") {
	    		$response = Datatables::of($report)->make(true);
	    	}elseif($version=="print"){
	    		$response = response()->json($report);
	    	}elseif($version=="excel"){
	    		Excel::create('purchase_request_and_order_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
	                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
	                $excel->sheet(trans('rep.report_purchase_and_order'),function($sheet) use($report,$start_date,$end_date){
	                	$startRows = 1;
						$sheet->setAutoSize(true);
						$sheet->mergeCells('A1:A3');
						$objDrawing = new PHPExcel_Worksheet_Drawing;
				        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
				        $objDrawing->setCoordinates('A1');
				        $objDrawing->setHeight(150);
				        $objDrawing->setWorksheet($sheet);

						$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
						$sheet->cell(('C'.$startRows),getSetting()->report_header);
						$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(28);
	                        $cells->setFontColor(getSetting()->report_header_color);
	                        $cells->setAlignment('center');
						});
						$startRows++;
						$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
						$sheet->cell(('C'.$startRows),getSetting()->company_name);
						$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(28);
	                        $cells->setFontColor(getSetting()->company_name_color);
	                        $cells->setAlignment('center');
						});
						$startRows++;
						$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
						$sheet->cell(('C'.$startRows),trans('rep.report_purchase_and_order'));
						$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(16);
	                        $cells->setFontColor(getSetting()->report_header_color);
	                        $cells->setAlignment('center');
						});
						$startRows++;
						$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
						$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
						$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('left');
						});

						$startRows++;
	                    $sheet->cell(('A'.$startRows),trans('lang.trans_date'));
	                    $sheet->cell(('B'.$startRows),trans('lang.reference_no'));
	                    $sheet->cell(('C'.$startRows),trans('lang.department'));
	                    $sheet->cell(('D'.$startRows),trans('lang.request_by'));
	                    $sheet->cell(('E'.$startRows),trans('lang.item_type'));
	                    $sheet->cell(('F'.$startRows),trans('lang.item_code'));
	                    $sheet->cell(('G'.$startRows),trans('lang.item_name'));
	                    $sheet->cell(('H'.$startRows),trans('lang.qty'));
	                    $sheet->cell(('I'.$startRows),trans('rep.ordered_qty'));
	                    $sheet->cell(('J'.$startRows),trans('lang.units'));
	                    $sheet->cell(('K'.$startRows),trans('lang.boq'));
	                    $sheet->cell(('L'.$startRows),trans('lang.status'));

	                    $sheet->cell(('A'.$startRows.':L'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setFontColor('#ffffff');
	                        $cells->setBackground(getSetting()->report_header_color);
	                        $cells->setAlignment('center');
	                    });

	                	foreach ($report as  $dval) {
	                		$startRows++;
	                		$trans_status = '';
	                		if ($dval->trans_status==1) {
	                			$trans_status = 'Pendding';
	                		}elseif($dval->trans_status==2){
	                			$trans_status = 'Approving';
	                		}elseif($dval->trans_status==3){
	                			$trans_status = 'Completed';
	                		}elseif($dval->trans_status==4){
	                			$trans_status = 'Rejected';
	                		}
					        $sheet->cell('A'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
					        $sheet->cell('B'.($startRows),$dval->ref_no);
		                    $sheet->cell('C'.($startRows),($dval->department!=null ? $dval->department : '#N/A'));
		                    $sheet->cell('D'.($startRows),($dval->request_people_name!=null ? $dval->request_people_name : '#N/A'));
		                    $sheet->cell('E'.($startRows),$dval->category);
		                    $sheet->cell('F'.($startRows),$dval->item_code);
		                    $sheet->cell('G'.($startRows),$dval->item_name);
		                    $sheet->cell('H'.($startRows),$dval->qty);
		                    $sheet->cell('I'.($startRows),$dval->ordered_qty);
		                    $sheet->cell('J'.($startRows),$dval->unit);
		                    $sheet->cell('K'.($startRows),($dval->boq_set==-1 ? 'No' : 'Yes'));
		                    $sheet->cell('L'.($startRows),$trans_status);
		                    $sheet->setColumnFormat([('H'.$startRows)=>'0',('I'.$startRows)=>'0']);
		                    $sheet->cell(('A'.$startRows.':L'.$startRows),function($cells){
		                        $cells->setFontFamily('Khmer OS Battambang');
		                        $cells->setFontSize(11);
		                        $cells->setAlignment('center');
		                    });
	                	}
	                });
	            })->download('xlsx');
	    	}

	    	return $response;
    	} catch (\Exception $e) {
    		var_dump($e->getMessage());exit;
    	}
    }

    public function view_report_purchase_and_order_delivery()
    {
    	$data = [
			'title'            => trans('rep.report_purchase_and_order_delivery'),
			'icon'             => 'fa fa-shopping-cart',
			'small_title'      => trans('lang.report'),
			'background'       => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.purchase'),
				],
				'request'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.request'),
				],
				'request_detail'	=> [
						'url' 		=> url('/report/purchase-and-order-delivery'),
						'caption' 	=> trans('rep.report_purchase_and_order_delivery'),
				],
			],
		];

    	return view("reports.purchase_request_order_delivery",$data);
    }

    public function view_report_usage_costing()
    {
    	$data = [
			'title'            => trans('rep.report_usage_costing'),
			'icon'             => 'fa fa-dollar',
			'small_title'      => trans('lang.report'),
			'background'       => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.usage'),
				],
				'request_detail'	=> [
						'url' 		=> url('/report/usage-costing'),
						'caption' 	=> trans('rep.report_usage_costing'),
				],
			],
		];

    	return view("reports.usage.usage_costing",$data);
    }

	public function printUsageCosting(Request $request){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$eng_usage    = $request->query("eng_usage");
		$sub_usage    = $request->query("sub_usage");
		$zone_id      = $request->query("zone_id");
		$block_id     = $request->query("block_id");
		$street_id    = $request->query("street_id");
		$house_type   = $request->query("house_type");
		$house_id     = $request->query("house_id");
		$item_type    = $request->query("item_type");
		$item_id      = $request->query("item_id");
		$use_id       = $request->query("use_id");
		$warehouse_id = $request->query("warehouse_id");
		$trans_status = $request->query("trans_status");
		$group_by     = $request->query("group_by");
		$version      = $request->query("version");
		$search       = $request->search['value'];

		$columns = [
			'usage_details.*',
			'usages.reference',
			'usages.ref_no',
			'usages.eng_usage',
			'usages.sub_usage',
			'usages.trans_date',
			'usages.pro_id',
			'houses.house_no',
			'houses.house_desc',
			'houses.house_type',
			'items.code as item_code',
			'items.name as item_name',
			'items.cat_id'
		];

		$report  = UsageDetails::select($columns)
					->leftJoin('usages','usages.id','usage_details.use_id')
					->leftJoin('houses','houses.id','usage_details.house_id')
					->leftJoin('items','items.id','usage_details.item_id')
					->where('usages.delete',0)
					->where('usages.pro_id',$project_id)
					->where('usages.trans_date','>=',$start_date)
					->where('usages.trans_date','<=',$end_date);

		if ($use_id) {
			$report = $report->where('usage_details.use_id',$use_id);
		}

		if ($warehouse_id) {
			$report = $report->where('usage_details.warehouse_id',$warehouse_id);
		}

		if ($eng_usage) {
			$report = $report->where('usages.eng_usage',$eng_usage);
		}

		if ($sub_usage) {
			$report = $report->where('usages.sub_usage',$sub_usage);
		}

		if ($zone_id) {
			if($houseIds = House::where(['zone_id' => $zone_id])->pluck('id')){
				$report = $report->whereIn('usage_details.house_id',[$houseIds]);
			}
		}

		if ($block_id) {
			if($houseIds = House::where(['block_id' => $block_id])->pluck('id')){
				$report = $report->whereIn('usage_details.house_id',[$houseIds]);
			}
		}

		if ($street_id) {
			$report = $report->where('usage_details.street_id',$street_id);
		}

		if ($house_type) {
			if($houseIds = House::where(['house_type' => $house_type])->pluck('id')){
				$report = $report->whereIn('usage_details.house_id',[$houseIds]);
			}
		}

		if ($item_id) {
			$report = $report->where('usage_details.house_id',$item_id);
		}

		if ($group_by) {
			$report = $report->groupBy('usages.id');
		}

    	$report = $report->get();

		$data = [
			'title' => trans('lang.report_usage_costing'),
			'report'=> $report,
		];
		return view("reports.usage.print.usage_costing",$data);
	}

    public function report_purchase_and_order_delivery(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
    	try {
    		$start_date   = $request->query("start_date");
			$end_date     = $request->query("end_date");
			$project_id   = $request->session()->get('project');
			$dep_id       = $request->query("dep_id");
			$trans_status = $request->query("trans_status");
			$version      = $request->query("version");

			if ($dep_id!='') {
				$np = str_replace(",","','",$dep_id);
				$np .="'";
				$np = substr($np,2);
				$dep_id = " IN(".$np.") ";
			}else{
				$dep_id = " !=0";
			}

			if ($trans_status!='') {
				$np = str_replace(",","','",$trans_status);
				$np .="'";
				$np = substr($np,2);
				$trans_status = " IN(".$np.") ";
			}else{
				$trans_status = " !=0";
			}

			$sup_id = "!=0";

        $sql1 = "SELECT d.pro_id, d.ref_no, d.trans_date, d.trans_status, d.request_by, d.dep_id, d.pr_id, d.item_id, d.unit_stock, d.boq_set, d.department, d.request_nick, d.request_name, d.item_code, d.item_name, d.unit, ROUND(d.qty, 3) AS qty, ROUND(d.ordered_qty, 3) AS ordered_qty, ROUND(d.deliv_qty, 3) AS deliv_qty FROM (SELECT c.*, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = c.dep_id AND `pr_system_datas`.`type` = 'DP') AS department, (SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id` = c.request_by) AS request_nick, (SELECT `pr_constructors`.`id_card` FROM `pr_constructors` WHERE `pr_constructors`.`id` = c.request_by) AS request_name, (SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id` = c.item_id) AS item_code, (SELECT `pr_items`.`name` FROM `pr_items` WHERE `pr_items`.`id` = c.item_id) AS item_name, (SELECT `pr_units`.`from_desc` FROM `pr_units` WHERE `pr_units`.`from_code` = c.unit_stock LIMIT 1) AS unit FROM (SELECT b.pro_id, b.ref_no, b.trans_date, b.trans_status, b.request_by, b.dep_id, b.pr_id, b.item_id, b.unit_stock, b.boq_set, b.qty, b.ordered_qty, SUM(b.deliv_qty) AS deliv_qty FROM (SELECT a.pro_id, a.ref_no, a.trans_date, a.trans_status, a.request_by, a.dep_id, a.pr_id, a.item_id, a.unit_stock, a.boq_set, a.qty, a.ordered_qty, ( CASE WHEN a.deliv_qty != '' THEN a.deliv_qty ELSE 0 END ) AS deliv_qty FROM (SELECT block_a.pro_id, block_a.ref_no, block_a.trans_date, block_a.trans_status, block_a.request_by, block_a.dep_id, block_a.pr_id, block_a.item_id, block_a.unit_stock, block_a.boq_set, block_a.qty, block_a.ordered_qty, block_b.deliv_qty FROM (SELECT rd.pro_id, rd.ref_no, rd.trans_date, rd.trans_status, rd.request_by, rd.dep_id, rd.pr_id, rd.item_id, rd.unit, rd.unit_stock, rd.boq_set, SUM(rd.qty) AS qty, SUM(rd.ordered_qty) AS ordered_qty FROM (SELECT rc.pro_id, rc.ref_no, rc.trans_date, rc.trans_status, rc.request_by, rc.dep_id, rc.pr_id, rc.item_id, rc.unit, rc.boq_set, rc.unit_stock, (rc.qty * rc.factor) AS qty, (rc.ordered_qty * rc.factor) AS ordered_qty FROM (SELECT rb.pro_id, rb.ref_no, rb.trans_date, rb.trans_status, rb.request_by, rb.dep_id, rb.pr_id, rb.item_id, rb.unit, rb.boq_set, rb.unit_stock, rb.qty, rb.ordered_qty, ( CASE WHEN rb.factor != '' THEN rb.factor ELSE 1 END ) AS factor FROM (SELECT ra.*, (SELECT `pr_units`.`factor` FROM `pr_units` WHERE `pr_units`.`from_code` = ra.unit AND `pr_units`.`to_code` = ra.unit_stock) AS factor FROM (SELECT `pr_requests`.`pro_id`, `pr_requests`.`ref_no`, `pr_requests`.`trans_date`, `pr_requests`.`trans_status`, `pr_requests`.`request_by`, `pr_requests`.`dep_id`, `pr_request_items`.`pr_id`, `pr_request_items`.`item_id`, `pr_request_items`.`unit`, `pr_request_items`.`boq_set`, (SELECT `pr_items`.`unit_stock` FROM `pr_items` WHERE `pr_items`.`id` = `pr_request_items`.`item_id`) AS unit_stock, SUM(`pr_request_items`.`qty`) AS qty, SUM( `pr_request_items`.`ordered_qty` ) AS ordered_qty FROM `pr_requests` INNER JOIN `pr_request_items` ON `pr_requests`.`id` = `pr_request_items`.`pr_id` AND `pr_requests`.`pro_id`=$project_id AND `pr_requests`.`trans_status` != 0 AND `pr_requests`.`dep_id` $dep_id AND `pr_requests`.`trans_status` $trans_status AND `pr_requests`.`trans_date` BETWEEN '$start_date' AND '$end_date' GROUP BY `pr_requests`.`pro_id`, `pr_requests`.`id`, `pr_request_items`.`item_id`, `pr_request_items`.`unit`) AS ra) AS rb) AS rc) AS rd GROUP BY rd.pro_id, rd.pr_id, rd.item_id, rd.unit_stock) AS block_a LEFT JOIN (SELECT dd.pro_id, (SELECT `pr_orders`.`pr_id` FROM `pr_orders` WHERE `pr_orders`.`id` = dd.po_id) AS pr_id, dd.po_id, dd.item_id, dd.unit_stock, SUM(dd.qty) AS deliv_qty FROM (SELECT dc.pro_id, dc.po_id, dc.item_id, dc.unit_del, dc.unit_stock, (dc.qty * dc.factor_unit_del) AS qty FROM (SELECT db.pro_id, db.po_id, db.item_id, db.unit_del, db.unit_stock, db.qty, ( CASE WHEN db.factor_unit_del != '' THEN db.factor_unit_del ELSE 1 END ) AS factor_unit_del FROM (SELECT da.pro_id, da.po_id, da.item_id, da.unit_del, da.unit_stock, da.qty, (SELECT `pr_units`.`factor` FROM `pr_units` WHERE `pr_units`.`from_code` = da.unit_del AND `pr_units`.`to_code` = da.unit_stock LIMIT 1) AS factor_unit_del FROM (SELECT `pr_deliveries`.`pro_id`, `pr_deliveries`.`po_id`, `pr_delivery_items`.`item_id`, `pr_delivery_items`.`unit` AS unit_del, (SELECT `pr_items`.`unit_stock` FROM `pr_items` WHERE `pr_items`.`id` = `pr_delivery_items`.`item_id`) AS unit_stock, SUM(`pr_delivery_items`.`qty`) AS qty FROM `pr_deliveries` INNER JOIN `pr_delivery_items` ON `pr_deliveries`.`id` = `pr_delivery_items`.`del_id` AND `pr_deliveries`.`delete` = 0 GROUP BY `pr_deliveries`.`pro_id`, `pr_deliveries`.`po_id`, `pr_delivery_items`.`item_id`, `pr_delivery_items`.`unit`) AS da) AS db) AS dc) AS dd GROUP BY dd.pro_id, dd.po_id, dd.item_id, dd.unit_stock) AS block_b ON block_a.pr_id = block_b.pr_id AND block_a.item_id = block_b.item_id) AS a) AS b GROUP BY b.pro_id, b.pr_id, b.item_id) AS c) AS d ";

        // var_dump($sql1);exit;

	    	$sql = "SELECT * FROM(SELECT F.pro_id,F.boq_set,F.dep_id, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=F.dep_id AND `pr_system_datas`.`type`='DP')AS department, F.pr_id, F.ref_no, F.trans_date,F.trans_status, F.request_by, (SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id`=F.request_by)AS request_nick, (SELECT `pr_constructors`.`id_card` FROM `pr_constructors` WHERE `pr_constructors`.`id`=F.request_by)AS request_name, F.item_id, (SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id`=F.item_id)AS item_code, (SELECT `pr_items`.`name` FROM `pr_items` WHERE pr_items.`id`=F.item_id)AS item_name, ROUND(F.qty,3)AS qty, ROUND(F.ordered_qty,3)AS ordered_qty, ROUND(F.closed_qty,3)AS closed_qty, ROUND(F.deliv_qty,3)AS deliv_qty, ROUND(F.del_closed_qty,3)AS del_closed_qty, (SELECT `pr_units`.`from_desc` FROM `pr_units` WHERE `pr_units`.`from_code`=F.unit LIMIT 1)AS unit FROM (SELECT E.pro_id, E.dep_id, E.pr_id, E.ref_no, E.trans_date, E.request_by,E.trans_status,E.boq_set, E.item_id, SUM(E.qty) AS qty, SUM(E.ordered_qty) AS ordered_qty, SUM(E.closed_qty) AS closed_qty, SUM(E.deliv_qty) AS deliv_qty, SUM(E.del_closed_qty) AS del_closed_qty, E.unit FROM (SELECT D.id, D.pro_id, D.dep_id, D.pr_id, D.ref_no,D.boq_set, D.trans_date, D.trans_status, D.request_by, D.item_id, D.qty, D.ordered_qty, D.closed_qty, (D.deliv_qty * D.unit_qty) AS deliv_qty, (D.del_closed_qty * D.unit_qty) AS del_closed_qty, D.unit FROM (SELECT C.id, C.pro_id, C.dep_id, C.pr_id, C.ref_no, C.trans_date, C.request_by,C.boq_set, C.trans_status, C.item_id, C.po_item_id, C.qty, C.ordered_qty, C.closed_qty, C.deliv_qty, C.del_closed_qty, C.unit, C.po_unit, (CASE WHEN C.unit_qty != ''THEN C.unit_qty ELSE 1 END ) AS unit_qty FROM (SELECT B.*, (SELECT `pr_units`.`factor` FROM `pr_units` WHERE `pr_units`.`from_code` = B.unit AND `pr_units`.`to_code` = B.po_unit) AS unit_qty FROM (SELECT A.id, A.pro_id, A.dep_id, A.pr_id, A.ref_no, A.trans_date, A.request_by, A.trans_status, A.item_id, A.po_item_id, SUM(A.qty) AS qty, SUM(A.ordered_qty) AS ordered_qty, SUM(A.closed_qty) AS closed_qty, SUM(A.deliv_qty) AS deliv_qty, SUM(A.del_closed_qty) AS del_closed_qty, A.unit,A.boq_set, A.po_unit FROM (SELECT oo.id, oo.pro_id, oo.dep_id, oo.pr_id, oo.ref_no, oo.trans_date, oo.request_by, oo.trans_status,oo.boq_set, oo.item_id, oo.po_item_id, oo.unit, oo.po_unit, oo.qty, oo.ordered_qty, oo.closed_qty, (CASE WHEN oo.deliv_qty != ''THEN oo.deliv_qty ELSE 0 END ) AS deliv_qty, (CASE WHEN oo.del_closed_qty != ''THEN oo.del_closed_qty ELSE 0 END ) AS del_closed_qty FROM (SELECT Block_a.*, Block_b.po_id, Block_b.po_item_id, Block_b.deliv_qty, Block_b.closed_qty AS del_closed_qty, Block_b.po_unit FROM (SELECT ri.`id`, r.id AS pr_id, r.`ref_no`, r.`trans_date`, r.`delivery_date`, r.`note`, r.`pro_id`, r.`dep_id`, r.`request_by`, r.`trans_status`, ri.`item_id`, ri.`unit`, ri.`qty`, ri.`boq_set`, ri.`ordered_qty`, ri.`closed_qty`, ri.`price`, ri.`desc` FROM `pr_request_items` AS ri INNER JOIN `pr_requests` AS r ON r.`id` = ri.`pr_id` AND r.`pro_id` = $project_id AND r.`trans_status` $trans_status AND r.`trans_date` BETWEEN '$start_date'AND '$end_date') AS Block_a LEFT JOIN (SELECT ord_.pr_id, ord_.id AS po_id, ord_.`ref_no` AS po_code, ordi.`item_id` AS po_item_id, ordi.`qty` AS po_qty, ordi.`deliv_qty`, ordi.`closed_qty`, ordi.`unit` AS po_unit FROM `pr_orders` AS ord_ INNER JOIN `pr_order_items` AS ordi ON ord_.`id` = ordi.`po_id` AND ord_.`pro_id` = $project_id AND ord_.`dep_id` $dep_id AND ord_.`sup_id` $sup_id) AS Block_b ON Block_a.pr_id = Block_b.pr_id AND Block_a.item_id = Block_b.po_item_id) AS oo) AS A GROUP BY A.pr_id, A.item_id, A.unit, A.po_unit) AS B) AS C) AS D) AS E GROUP BY E.pro_id, E.pr_id, E.item_id, E.unit) AS F)AS G ";

	    	$report = DB::select($sql1);

	    	if ($version=="datatables") {
	    		$response = Datatables::of($report)->make(true);
	    	}elseif($version=="print"){
	    		$response = response()->json($report);
	    	}elseif($version=="excel"){
	    		Excel::create('PR_PO_DELIVERY_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
	                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
	                $excel->sheet("PR & PO & Delivery",function($sheet) use($report,$start_date,$end_date){
	                	$startRows = 1;
						$sheet->setAutoSize(true);
						$sheet->mergeCells('A1:A3');
						$objDrawing = new PHPExcel_Worksheet_Drawing;
				        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
				        $objDrawing->setCoordinates('A1');
				        $objDrawing->setHeight(150);
				        $objDrawing->setWorksheet($sheet);

						$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
						$sheet->cell(('C'.$startRows),getSetting()->report_header);
						$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(28);
	                        $cells->setFontColor(getSetting()->report_header_color);
	                        $cells->setAlignment('center');
						});
						$startRows++;
						$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
						$sheet->cell(('C'.$startRows),getSetting()->company_name);
						$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(28);
	                        $cells->setFontColor(getSetting()->company_name_color);
	                        $cells->setAlignment('center');
						});
						$startRows++;
						$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
						$sheet->cell(('C'.$startRows),trans('rep.report_purchase_and_order_delivery'));
						$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(16);
	                        $cells->setFontColor(getSetting()->report_header_color);
	                        $cells->setAlignment('center');
						});
						$startRows++;
						$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
						$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
						$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('left');
						});

						$startRows++;

						$sheet->mergeCells(('D'.$startRows.':E'.$startRows));

	                    $sheet->cell(('A'.$startRows),trans('lang.trans_date'));
	                    $sheet->cell(('B'.$startRows),trans('lang.reference_no'));
	                    $sheet->cell(('C'.$startRows),trans('lang.department'));
	                    $sheet->cell(('D'.$startRows),trans('lang.request_by'));
	                    $sheet->cell(('F'.$startRows),trans('lang.item_code'));
	                    $sheet->cell(('G'.$startRows),trans('lang.item_name'));
	                    $sheet->cell(('H'.$startRows),trans('lang.qty'));
	                    $sheet->cell(('I'.$startRows),trans('rep.ordered_qty'));
	                    $sheet->cell(('J'.$startRows),trans('rep.delivery_qty'));
	                    $sheet->cell(('K'.$startRows),trans('lang.units'));
	                    $sheet->cell(('L'.$startRows),trans('lang.boq'));
	                    $sheet->cell(('M'.$startRows),trans('lang.status'));

	                    $sheet->cell(('A'.$startRows.':M'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setFontColor('#ffffff');
	                        $cells->setBackground(getSetting()->report_header_color);
	                        $cells->setAlignment('center');
	                    });

	                	foreach ($report as  $dval) {
	                		$startRows++;
	                		$trans_status = '';
	                		if ($dval->trans_status==1) {
	                			$trans_status = 'Pendding';
	                		}elseif($dval->trans_status==2){
	                			$trans_status = 'Approving';
	                		}elseif($dval->trans_status==3){
	                			$trans_status = 'Completed';
	                		}elseif($dval->trans_status==4){
	                			$trans_status = 'Rejected';
	                		}
					        $sheet->cell('A'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
					        $sheet->cell('B'.($startRows),$dval->ref_no);
		                    $sheet->cell('C'.($startRows),($dval->department!=null ? $dval->department : '#N/A'));
		                    $sheet->cell('D'.($startRows),($dval->request_nick!=null ? $dval->request_nick : '#N/A'));
		                    $sheet->cell('E'.($startRows),($dval->request_name!=null ? $dval->request_name : '#N/A'));
		                    $sheet->cell('F'.($startRows),$dval->item_code);
		                    $sheet->cell('G'.($startRows),$dval->item_name);
		                    $sheet->cell('H'.($startRows),$dval->qty);
		                    $sheet->cell('I'.($startRows),$dval->ordered_qty);
		                    $sheet->cell('J'.($startRows),$dval->deliv_qty);
		                    $sheet->cell('K'.($startRows),$dval->unit);
		                    $sheet->cell('L'.($startRows),($dval->boq_set==-1 ? 'No' : 'Yes'));
		                    $sheet->cell('M'.($startRows),$trans_status);
		                    $sheet->setColumnFormat([('J'.$startRows)=>'0',('I'.$startRows)=>'0',('H'.$startRows)=>'0']);
		                    $sheet->cell(('A'.$startRows.':M'.$startRows),function($cells){
		                        $cells->setFontFamily('Khmer OS Battambang');
		                        $cells->setFontSize(11);
		                        $cells->setAlignment('center');
		                    });
	                	}
	                });
	            })->download('xlsx');
	    	}

	    	return $response;
    	} catch (\Exception $e) {
    		var_dump($e->getMessage());exit;
    	}
    }

    public function generate_requests(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$dep_id       = $request->query("dep_id");
		$trans_status = $request->query("trans_status");
		$version      = $request->query("version");

		if ($dep_id!='') {
			$np = str_replace(",","','",$dep_id);
			$np .="'";
			$np = substr($np,2);
			$dep_id = "AND r.`dep_id` IN(".$np.") ";
		}else{
			$dep_id = '';
		}

		if ($trans_status!='') {
			$np = str_replace(",","','",$trans_status);
			$np .="'";
			$np = substr($np,2);
			$trans_status = "AND r.`trans_status` IN(".$np.") ";
		}else{
			$trans_status = '';
		}

    	$sql = "SELECT ri.`id`, ri.`pr_id`, r.`ref_no`, r.`trans_date`, r.`delivery_date`, r.`note`, r.`pro_id`, (SELECT pr_projects.`name` FROM `pr_projects` WHERE `pr_projects`.`id`=r.`pro_id`)AS project, r.`dep_id`, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=r.`dep_id`)AS department, r.`request_by`, (SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id`=r.`request_by`)AS request_people_code, (SELECT `pr_constructors`.`id_card` FROM `pr_constructors` WHERE `pr_constructors`.`id`=r.`request_by`)AS request_people_name, r.`trans_status`, ri.`item_id`,(SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE pr_items.`id`=ri.`item_id`)AS category_id, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=(SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE pr_items.`id`=ri.`item_id`))AS category, (SELECT pr_items.`code` FROM `pr_items` WHERE `pr_items`.`id`=ri.item_id)AS item_code, (SELECT pr_items.`name` FROM `pr_items` WHERE `pr_items`.`id`=ri.item_id)AS item_name, ri.`unit`, ri.`qty`, ri.`boq_set`, ri.`ordered_qty`, ri.`closed_qty`, ri.`price`, ri.`desc` FROM `pr_request_items` AS ri INNER JOIN `pr_requests` AS r ON ri.`pr_id` = r.`id` AND r.`pro_id` = $project_id AND r.`trans_status`!=0 $dep_id $trans_status AND r.`trans_date` BETWEEN '$start_date'AND '$end_date' ";

    	$report = DB::select($sql);

    	if ($version=="datatables") {
    		$response = Datatables::of($report)->make(true);
    	}elseif($version=="print"){
    		$response = response()->json($report);
    	}elseif($version=="excel"){
    		Excel::create('purchase_request_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet('purchase request report',function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(150);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report_request_items'));
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('rep.project'));
                    $sheet->cell(('B'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('C'.$startRows),trans('lang.reference_no'));
                    $sheet->cell(('D'.$startRows),trans('lang.department'));
                    $sheet->cell(('E'.$startRows),trans('lang.request_by'));
                    $sheet->cell(('F'.$startRows),trans('lang.delivery_date'));
                    $sheet->cell(('G'.$startRows),trans('lang.item_type'));
                    $sheet->cell(('H'.$startRows),trans('lang.item_code'));
                    $sheet->cell(('I'.$startRows),trans('lang.item_name'));
                    $sheet->cell(('J'.$startRows),trans('lang.qty'));
                    $sheet->cell(('K'.$startRows),trans('rep.ordered_qty'));
                    $sheet->cell(('L'.$startRows),trans('lang.units'));
                    $sheet->cell(('M'.$startRows),trans('lang.boq'));
                    $sheet->cell(('N'.$startRows),trans('lang.status'));
                    $sheet->cell(('O'.$startRows),trans('lang.desc'));

                    $sheet->cell(('A'.$startRows.':O'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as  $dval) {
                		$startRows++;
                		$trans_status = '';
                		if ($dval->trans_status==1) {
                			$trans_status = 'Pendding';
                		}elseif($dval->trans_status==2){
                			$trans_status = 'Approving';
                		}elseif($dval->trans_status==3){
                			$trans_status = 'Completed';
                		}elseif($dval->trans_status==4){
                			$trans_status = 'Rejected';
                		}
				        $sheet->cell('A'.($startRows),$dval->project);
				        $sheet->cell('B'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
				        $sheet->cell('C'.($startRows),$dval->ref_no);
	                    $sheet->cell('D'.($startRows),($dval->department!=null ? $dval->department : '#N/A'));
	                    $sheet->cell('E'.($startRows),($dval->request_people_name!=null ? $dval->request_people_name : '#N/A'));
	                    $sheet->cell('F'.($startRows),date("d/m/Y",strtotime($dval->delivery_date)));
	                    $sheet->cell('G'.($startRows),$dval->category);
	                    $sheet->cell('H'.($startRows),$dval->item_code);
	                    $sheet->cell('I'.($startRows),$dval->item_name);
	                    $sheet->cell('J'.($startRows),$dval->qty);
	                    $sheet->cell('K'.($startRows),$dval->ordered_qty);
	                    $sheet->cell('L'.($startRows),$dval->unit);
	                    $sheet->cell('M'.($startRows),($dval->boq_set==-1 ? 'No' : 'Yes'));
	                    $sheet->cell('N'.($startRows),$trans_status);
	                    $sheet->cell('O'.($startRows),$dval->desc);
	                    $sheet->setColumnFormat([('J'.$startRows)=>'0',('K'.$startRows)=>'0']);
	                    $sheet->cell(('A'.$startRows.':O'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}

    	return $response;
    }

    public function generate_request_1(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$dep_id       = $request->query("dep_id");
		$pr_id        = $request->query("pr_id");
		$trans_status = $request->query("trans_status");
		$version      = $request->query("version");

		if ($pr_id!='') {
			$np = str_replace(",","','",$pr_id);
			$np .="'";
			$np = substr($np,2);
			$pr_id = "AND req.`id` IN(".$np.") ";
		}else{
			$pr_id = '';
		}

		if ($dep_id!='') {
			$np = str_replace(",","','",$dep_id);
			$np .="'";
			$np = substr($np,2);
			$dep_id = "AND req.`dep_id` IN(".$np.") ";
		}else{
			$dep_id = '';
		}

		if ($trans_status!='') {
			$np = str_replace(",","','",$trans_status);
			$np .="'";
			$np = substr($np,2);
			$trans_status = "AND req.`trans_status` IN(".$np.") ";
		}else{
			$trans_status = '';
		}

    	$sql = "SELECT req.id, req.`ref_no` AS pr_no, ord_.`ref_no` AS po_no, req.`pro_id`, (SELECT `pr_projects`.`name` FROM `pr_projects` WHERE `pr_projects`.`id`=req.`pro_id`)AS project, req.`dep_id`, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=req.`dep_id`)AS department, req.`request_by`, (SELECT `pr_constructors`.`id_card` FROM `pr_constructors` WHERE `pr_constructors`.`id`=req.`request_by`)AS request_people_name, (SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id`=req.`request_by`)AS request_people_code, req.`trans_date`, req.`trans_status`, ord_.`grand_total`, ord_.`sub_total`, ord_.`ordered_by`, (SELECT `pr_users`.`name` FROM `pr_users` WHERE `pr_users`.`id`=ord_.`ordered_by`)AS order_people_name, (SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id`=ord_.`ordered_by`)AS order_people_code, ord_.`sup_id`, (SELECT `pr_suppliers`.`name` FROM `pr_suppliers` WHERE `pr_suppliers`.`id`=ord_.`sup_id`)AS supplier FROM `pr_requests` AS req INNER JOIN `pr_orders` AS ord_ ON ord_.`pr_id` = req.`id` AND req.`pro_id` = $project_id AND req.`trans_status`!=0 $pr_id $dep_id $trans_status AND req.`trans_date` BETWEEN '$start_date'AND '$end_date' ";

    	$report = DB::select($sql);

    	if ($version=="datatables") {
    		$response = Datatables::of($report)->make(true);
    	}elseif($version=="print"){
    		$response = response()->json($report);
    	}elseif($version=="excel"){
    		Excel::create('purchase_request_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet('purchase request report',function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(150);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':G'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':G'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':G'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report_request_items'));
					$sheet->cell(('C'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('rep.project'));
                    $sheet->cell(('B'.$startRows),trans('lang.reference_no'));
                    $sheet->cell(('C'.$startRows),trans('rep.order_code'));
                    $sheet->cell(('D'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('E'.$startRows),trans('lang.department'));
                    $sheet->cell(('F'.$startRows),trans('lang.request_by'));
                    $sheet->cell(('G'.$startRows),trans('lang.ordered_by'));
                    $sheet->cell(('H'.$startRows),trans('lang.grand_total'));
                    $sheet->cell(('I'.$startRows),trans('lang.status'));
                    $sheet->cell(('J'.$startRows),trans('lang.desc'));

                    $sheet->cell(('A'.$startRows.':J'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as  $dval) {
                		$startRows++;
                		$trans_status = '';
                		if ($dval->trans_status==1) {
                			$trans_status = 'Pendding';
                		}elseif($dval->trans_status==2){
                			$trans_status = 'Approving';
                		}elseif($dval->trans_status==3){
                			$trans_status = 'Completed';
                		}elseif($dval->trans_status==4){
                			$trans_status = 'Rejected';
                		}
				        $sheet->cell('A'.($startRows),$dval->project);
				        $sheet->cell('B'.($startRows),$dval->pr_no);
				        $sheet->cell('C'.($startRows),$dval->po_no);
	                    $sheet->cell('D'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
	                    $sheet->cell('E'.($startRows),($dval->department!=null ? $dval->department : '#N/A'));
	                    $sheet->cell('F'.($startRows),($dval->request_people_name!=null ? $dval->request_people_name : '#N/A'));
	                    $sheet->cell('G'.($startRows),($dval->order_people_name!=null ? $dval->order_people_name : '#N/A'));
	                    $sheet->cell('H'.($startRows),$dval->grand_total);
	                    $sheet->cell('I'.($startRows),$trans_status);
	                    $sheet->cell('J'.($startRows),$dval->desc);
	                    $sheet->setColumnFormat([('H'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)']);
	                    $sheet->cell(('A'.$startRows.':J'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}

    	return $response;
    }

    public function generate_request_2(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$dep_id       = $request->query("dep_id");
		$pr_id        = $request->query("pr_id");
		$trans_status = $request->query("trans_status");
		$version      = $request->query("version");

		if ($pr_id!='') {
			$np = str_replace(",","','",$pr_id);
			$np .="'";
			$np = substr($np,2);
			$pr_id = "AND req.`id` IN(".$np.") ";
		}else{
			$pr_id = '';
		}

		if ($dep_id!='') {
			$np = str_replace(",","','",$dep_id);
			$np .="'";
			$np = substr($np,2);
			$dep_id = "AND req.`dep_id` IN(".$np.") ";
		}else{
			$dep_id = '';
		}

		if ($trans_status!='') {
			$np = str_replace(",","','",$trans_status);
			$np .="'";
			$np = substr($np,2);
			$trans_status = "AND req.`trans_status` IN(".$np.") ";
		}else{
			$trans_status = '';
		}

    	$sql = "SELECT req.`id`, req.`ref_no`, req.`trans_date`, req.`delivery_date`, req.`trans_status`, req.`request_by`, (SELECT pr_constructors.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id` = req.request_by) AS request_people_code, (SELECT pr_constructors.`id_card` FROM `pr_constructors` WHERE `pr_constructors`.`id` = req.request_by) AS request_people_name, req.`pro_id`, (SELECT `pr_projects`.`name` FROM `pr_projects` WHERE `pr_projects`.`id` = req.pro_id) AS project, req.`dep_id`, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = req.`dep_id`) AS department, req.`note` FROM `pr_requests` AS req WHERE req.`pro_id` = $project_id AND req.`trans_status`!=0 AND req.`trans_date` BETWEEN '$start_date'AND '$end_date'$pr_id $dep_id $trans_status ";

    	$report = DB::select($sql);

    	if ($version=="datatables") {
    		$response = Datatables::of($report)->make(true);
    	}elseif($version=="print"){
    		$response = response()->json($report);
    	}elseif($version=="excel"){
    		Excel::create('purchase_request_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet('purchase request report',function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(150);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':G'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':G'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':G'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report_request_items'));
					$sheet->cell(('C'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('rep.project'));
                    $sheet->cell(('B'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('C'.$startRows),trans('lang.reference_no'));
                    $sheet->cell(('D'.$startRows),trans('lang.department'));
                    $sheet->cell(('E'.$startRows),trans('lang.request_by'));
                    $sheet->cell(('F'.$startRows),trans('lang.request_by'));
                    $sheet->cell(('G'.$startRows),trans('lang.delivery_date'));
                    $sheet->cell(('H'.$startRows),trans('lang.note'));
                    $sheet->cell(('I'.$startRows),trans('lang.status'));
                    $sheet->cell(('J'.$startRows),trans('lang.desc'));

                    $sheet->cell(('A'.$startRows.':I'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as  $dval) {
                		$startRows++;
                		$trans_status = '';
                		if ($dval->trans_status==1) {
                			$trans_status = 'Pendding';
                		}elseif($dval->trans_status==2){
                			$trans_status = 'Approving';
                		}elseif($dval->trans_status==3){
                			$trans_status = 'Completed';
                		}elseif($dval->trans_status==4){
                			$trans_status = 'Rejected';
                		}
				        $sheet->cell('A'.($startRows),$dval->project);
				        $sheet->cell('B'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
				        $sheet->cell('C'.($startRows),$dval->ref_no);
	                    $sheet->cell('D'.($startRows),($dval->department!=null ? $dval->department : '#N/A'));
	                    $sheet->cell('E'.($startRows),($dval->request_people_name!=null ? $dval->request_people_name : '#N/A'));
	                    $sheet->cell('F'.($startRows),($dval->request_people_code!=null ? $dval->request_people_code : '#N/A'));
	                    $sheet->cell('G'.($startRows),date("d/m/Y",strtotime($dval->delivery_date)));
	                    $sheet->cell('H'.($startRows),$dval->note);
	                    $sheet->cell('I'.($startRows),$trans_status);
	                    $sheet->cell('J'.($startRows),$dval->desc);
	                    $sheet->cell(('A'.$startRows.':J'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}

    	return $response;
    }

    public function generate_orders(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$dep_id       = $request->query("dep_id");
		$sup_id       = $request->query("sup_id");
		$po_id        = $request->query("po_id");
		$trans_status = $request->query("trans_status");
		$group_by     = $request->query("group_by");
		$version      = $request->query("version");

		if (!empty($group_by)) {
			$group_by = ' GROUP BY oi.`po_id`';
		}else{
			$group_by = '';
		}

		if ($po_id!='') {
			$po_id = "AND oi.`po_id` = $po_id";
		}else{
			$po_id = '';
		}

		if ($sup_id!='') {
			$np = str_replace(",","','",$sup_id);
			$np .="'";
			$np = substr($np,2);
			$sup_id = "AND o.`sup_id` IN(".$np.") ";
		}else{
			$sup_id = '';
		}

		if ($dep_id!='') {
			$np = str_replace(",","','",$dep_id);
			$np .="'";
			$np = substr($np,2);
			$dep_id = "AND o.`dep_id` IN(".$np.") ";
		}else{
			$dep_id = '';
		}

		if ($trans_status!='') {
			$np = str_replace(",","','",$trans_status);
			$np .="'";
			$np = substr($np,2);
			$trans_status = "AND o.`trans_status` IN(".$np.") ";
		}else{
			$trans_status = '';
		}

    	$sql = "SELECT oi.`id`, oi.`po_id`, o.`ref_no`, o.`pro_id`, (SELECT `pr_projects`.`name` FROM `pr_projects` WHERE `pr_projects`.`id`=o.`pro_id`)AS project, o.`pr_id`, (SELECT `pr_requests`.`ref_no` FROM `pr_requests` WHERE `pr_requests`.`id`=o.`pr_id`)AS pr_no, o.`dep_id`, (SELECT `pr_system_datas`.`name` FROM pr_system_datas WHERE `pr_system_datas`.`id`=o.`dep_id`)AS department, o.`sup_id`, (SELECT `pr_suppliers`.`name` FROM `pr_suppliers` WHERE `pr_suppliers`.`id`=o.`sup_id`)AS supplier, o.`trans_date`, o.`delivery_date`, o.`delivery_address`, o.`trans_status`, o.`sub_total`, o.`disc_perc`, o.`disc_usd`, o.`grand_total`, o.`ordered_by`,  (SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id`=o.`ordered_by`)AS ordered_people_code, (SELECT `pr_constructors`.`id_card` FROM `pr_constructors` WHERE `pr_constructors`.`id`=o.`ordered_by`)AS ordered_people_name, o.`note`, oi.`item_id`,  (SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id`=oi.item_id)AS item_code, (SELECT `pr_items`.`name` FROM `pr_items` WHERE `pr_items`.`id`=oi.item_id)AS item_name, oi.`unit`, oi.`qty`, oi.`deliv_qty`, oi.`closed_qty`, oi.`price`, oi.`amount`, oi.`disc_perc`, oi.`disc_usd`, oi.`total`, oi.`desc` FROM `pr_order_items` AS oi INNER JOIN `pr_orders` AS o ON oi.`po_id` = o.`id` $po_id AND o.`pro_id` = $project_id AND o.`trans_status` != 0 $trans_status AND o.`trans_date` BETWEEN '$start_date'AND '$end_date' $dep_id $sup_id $group_by";

    	$report = DB::select($sql);

    	if ($version=="datatables") {
    		$response = Datatables::of($report)->make(true);
    	}elseif($version=="print"){
    		$response = response()->json($report);
    	}elseif($version=="excel"){
    		Excel::create('purchase_order_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet('purchase request report',function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(150);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':G'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':G'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':G'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report_order_items'));
					$sheet->cell(('C'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('rep.project'));
                    $sheet->cell(('B'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('C'.$startRows),trans('lang.reference_no'));
                    $sheet->cell(('D'.$startRows),trans('lang.department'));
                    $sheet->cell(('E'.$startRows),trans('lang.request_by'));
                    $sheet->cell(('F'.$startRows),trans('lang.request_by'));
                    $sheet->cell(('G'.$startRows),trans('lang.delivery_date'));
                    $sheet->cell(('H'.$startRows),trans('lang.note'));
                    $sheet->cell(('I'.$startRows),trans('lang.status'));

                    $sheet->cell(('A'.$startRows.':I'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as  $dval) {
                		$startRows++;
                		$trans_status = '';
                		if ($dval->trans_status==1) {
                			$trans_status = 'Pendding';
                		}elseif($dval->trans_status==2){
                			$trans_status = 'Approving';
                		}elseif($dval->trans_status==3){
                			$trans_status = 'Completed';
                		}elseif($dval->trans_status==4){
                			$trans_status = 'Rejected';
                		}
				        $sheet->cell('A'.($startRows),$dval->project);
				        $sheet->cell('B'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
				        $sheet->cell('C'.($startRows),$dval->ref_no);
	                    $sheet->cell('D'.($startRows),($dval->department!=null ? $dval->department : '#N/A'));
	                    $sheet->cell('E'.($startRows),($dval->ordered_people_name!=null ? $dval->ordered_people_name : '#N/A'));
	                    $sheet->cell('F'.($startRows),($dval->ordered_people_code!=null ? $dval->ordered_people_code : '#N/A'));
	                    $sheet->cell('G'.($startRows),date("d/m/Y",strtotime($dval->delivery_date)));
	                    $sheet->cell('H'.($startRows),$dval->note);
	                    $sheet->cell('I'.($startRows),$trans_status);
	                    $sheet->cell(('A'.$startRows.':I'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}

    	return $response;
    }

    public function generate_order_1(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$dep_id       = $request->query("dep_id");
		$sup_id       = $request->query("sup_id");
		$po_id        = $request->query("po_id");
		$trans_status = $request->query("trans_status");
		$group_by     = $request->query("group_by");
		$version      = $request->query("version");

		if (!empty($group_by)) {
			$group_by = ' GROUP BY oi.`po_id`';
		}else{
			$group_by = '';
		}

		if ($po_id!='') {
			$po_id = "AND oi.`po_id` = $po_id";
		}else{
			$po_id = '';
		}

		if ($sup_id!='') {
			$np = str_replace(",","','",$sup_id);
			$np .="'";
			$np = substr($np,2);
			$sup_id = "AND o.`sup_id` IN(".$np.") ";
		}else{
			$sup_id = '';
		}

		if ($dep_id!='') {
			$np = str_replace(",","','",$dep_id);
			$np .="'";
			$np = substr($np,2);
			$dep_id = "AND o.`dep_id` IN(".$np.") ";
		}else{
			$dep_id = '';
		}

		if ($trans_status!='') {
			$np = str_replace(",","','",$trans_status);
			$np .="'";
			$np = substr($np,2);
			$trans_status = "AND o.`trans_status` IN(".$np.") ";
		}else{
			$trans_status = '';
		}

    	$sql = "SELECT o.*,(SELECT `pr_warehouses`.`name` FROM pr_warehouses WHERE pr_warehouses.`id`=o.`delivery_address`)AS delivery_addr_name, (SELECT `pr_projects`.`name` FROM `pr_projects` WHERE `pr_projects`.`id`=o.pro_id)AS project, (SELECT `pr_requests`.`ref_no` FROM `pr_requests` WHERE `pr_requests`.`id`=o.pr_id)AS pr_no, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=o.dep_id)AS department, (SELECT `pr_suppliers`.`name` FROM `pr_suppliers` WHERE `pr_suppliers`.`id`=o.`sup_id`)AS supplier, oi.`item_id`, (SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id`=oi.item_id)AS item_code, (SELECT `pr_items`.`name` FROM `pr_items` WHERE `pr_items`.`id`=oi.item_id)AS item_name,(SELECT `pr_units`.`from_desc` FROM `pr_units` WHERE `pr_units`.`from_code`=oi.unit LIMIT 1)AS unit, oi.`qty`, oi.`deliv_qty`, oi.`closed_qty`, oi.`price`, oi.`amount`, oi.`disc_perc`, oi.`disc_usd`, oi.`total`, oi.`desc` FROM `pr_orders` AS o INNER JOIN `pr_order_items` AS oi ON oi.`po_id` = o.`id` AND o.`pro_id` = $project_id AND o.`trans_status` != 0 $trans_status $dep_id $sup_id AND o.`trans_date` BETWEEN '$start_date'AND '$end_date'";

    	$report = DB::select($sql);

    	if ($version=="datatables") {
    		$response = Datatables::of($report)->make(true);
    	}elseif($version=="print"){
    		$response = response()->json($report);
    	}elseif($version=="excel"){
    		Excel::create('purchase_order_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet('purchase order report',function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(150);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report_order_items'));
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('rep.project'));
                    $sheet->cell(('B'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('C'.$startRows),trans('lang.reference_no'));
                    $sheet->cell(('D'.$startRows),trans('lang.request_no'));
                    // $sheet->cell(('E'.$startRows),trans('lang.department'));
                    $sheet->cell(('E'.$startRows),trans('lang.supplier'));
                    $sheet->cell(('F'.$startRows),trans('lang.delivery_date'));
                    $sheet->cell(('G'.$startRows),trans('lang.delivery_address'));
                    $sheet->cell(('H'.$startRows),trans('lang.item_code'));
                    $sheet->cell(('I'.$startRows),trans('lang.item_name'));
                    $sheet->cell(('J'.$startRows),trans('lang.qty'));
                    $sheet->cell(('K'.$startRows),trans('rep.delivery_qty'));
                    $sheet->cell(('L'.$startRows),trans('rep.closed_qty'));
                    $sheet->cell(('M'.$startRows),trans('lang.units'));
                    $sheet->cell(('N'.$startRows),trans('lang.price'));
                    $sheet->cell(('O'.$startRows),trans('lang.amount'));

                    $sheet->cell(('A'.$startRows.':O'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as  $dval) {
                		$startRows++;
				        $sheet->cell('A'.($startRows),$dval->project);
				        $sheet->cell('B'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
				        $sheet->cell('C'.($startRows),$dval->ref_no);
	                    $sheet->cell('D'.($startRows),$dval->pr_no);
	                    // $sheet->cell('E'.($startRows),($dval->department!=null ? $dval->department : '#N/A'));
	                    $sheet->cell('E'.($startRows),($dval->supplier!=null ? $dval->supplier : '#N/A'));
	                    $sheet->cell('F'.($startRows),date("d/m/Y",strtotime($dval->delivery_date)));
	                    $sheet->cell('G'.($startRows),($dval->delivery_addr_name!=null ? $dval->delivery_addr_name : '#N/A'));
	                    $sheet->cell('H'.($startRows),$dval->item_code);
	                    $sheet->cell('I'.($startRows),$dval->item_name);
	                    $sheet->cell('J'.($startRows),$dval->qty);
	                    $sheet->cell('K'.($startRows),$dval->deliv_qty);
	                    $sheet->cell('L'.($startRows),$dval->closed_qty);
	                    $sheet->cell('M'.($startRows),$dval->unit);
	                    $sheet->cell('N'.($startRows),$dval->price);
	                    $sheet->cell('O'.($startRows),$dval->amount);
	                    $sheet->setColumnFormat([('N'.$startRows.':O'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)']);
	                    $sheet->setColumnFormat([('K'.$startRows)=>'0',('L'.$startRows)=>'0',('M'.$startRows)=>'0']);
	                    $sheet->cell(('A'.$startRows.':N'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}

    	return $response;
    }

    public function generate_order_2(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$dep_id       = $request->query("dep_id");
		$sup_id       = $request->query("sup_id");
		$po_id        = $request->query("po_id");
		$item_id      = $request->query("item_id");
		$trans_status = $request->query("trans_status");
		$group_by     = $request->query("group_by");
		$version      = $request->query("version");

		if (!empty($group_by)) {
			$group_by = ' GROUP BY oi.`po_id`';
		}else{
			$group_by = '';
		}

		if ($po_id!='') {
			$np = str_replace(",","','",$po_id);
			$np .="'";
			$np = substr($np,2);
			$po_id = "AND oi.`po_id` IN(".$np.") ";
		}else{
			$po_id = '';
		}

		if ($sup_id!='') {
			$np = str_replace(",","','",$sup_id);
			$np .="'";
			$np = substr($np,2);
			$sup_id = "AND o.`sup_id` IN(".$np.") ";
		}else{
			$sup_id = '';
		}

		if ($dep_id!='') {
			$np = str_replace(",","','",$dep_id);
			$np .="'";
			$np = substr($np,2);
			$dep_id = "AND o.`dep_id` IN(".$np.") ";
		}else{
			$dep_id = '';
		}

		if ($item_id!='') {
			$np = str_replace(",","','",$item_id);
			$np .="'";
			$np = substr($np,2);
			$item_id = "AND oi.`item_id` IN(".$np.") ";
		}else{
			$item_id = '';
		}

		if ($trans_status!='') {
			$np = str_replace(",","','",$trans_status);
			$np .="'";
			$np = substr($np,2);
			$trans_status = "AND o.`trans_status` IN(".$np.") ";
		}else{
			$trans_status = '';
		}

    	$sql = "SELECT o.*,(SELECT `pr_warehouses`.`name` FROM pr_warehouses WHERE pr_warehouses.`id`=o.`delivery_address`)AS delivery_addr_name, (SELECT `pr_projects`.`name` FROM `pr_projects` WHERE `pr_projects`.`id`=o.pro_id)AS project, (SELECT `pr_requests`.`ref_no` FROM `pr_requests` WHERE `pr_requests`.`id`=o.pr_id)AS pr_no, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=o.dep_id)AS department, (SELECT `pr_suppliers`.`name` FROM `pr_suppliers` WHERE `pr_suppliers`.`id`=o.`sup_id`)AS supplier, oi.`item_id`, (SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id`=oi.item_id)AS item_code, (SELECT `pr_items`.`name` FROM `pr_items` WHERE `pr_items`.`id`=oi.item_id)AS item_name, oi.`unit`, oi.`qty`, oi.`deliv_qty`, oi.`closed_qty`, oi.`price`, oi.`amount`, oi.`disc_perc`, oi.`disc_usd`, oi.`total`, oi.`desc` FROM `pr_orders` AS o INNER JOIN `pr_order_items` AS oi ON oi.`po_id` = o.`id` $po_id AND o.`pro_id` = $project_id AND o.`trans_status` != 0 $item_id $dep_id $sup_id AND o.`trans_date` BETWEEN '$start_date'AND '$end_date'";

    	$report = DB::select($sql);

    	if ($version=="datatables") {
    		$response = Datatables::of($report)->addColumn('delivery_obj',function($order){
    			return json_encode(Delivery::join('delivery_items','deliveries.id','=','delivery_items.del_id')->where('po_id',$order->id)->where('delivery_items.item_id',$order->item_id)->where('delete',0)->get());
    		})->make(true);
    	}elseif($version=="print"){
    		$response = response()->json(Datatables::of($report)->addColumn('delivery_obj',function($order){
    			return json_encode(Delivery::join('delivery_items','deliveries.id','=','delivery_items.del_id')->where('po_id',$order->id)->where('delivery_items.item_id',$order->item_id)->where('delete',0)->get());
    		})->make(true));
    	}elseif($version=="excel"){
    		Excel::create('purchase_order_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet('purchase order report',function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(150);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report_order_items'));
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('rep.project'));
                    $sheet->cell(('B'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('C'.$startRows),trans('lang.reference_no'));
                    $sheet->cell(('D'.$startRows),trans('lang.request_no'));
                    $sheet->cell(('E'.$startRows),trans('lang.department'));
                    $sheet->cell(('F'.$startRows),trans('lang.supplier'));
                    $sheet->cell(('G'.$startRows),trans('lang.delivery_date'));
                    $sheet->cell(('H'.$startRows),trans('lang.delivery_address'));
                    $sheet->cell(('I'.$startRows),trans('lang.item_code'));
                    $sheet->cell(('J'.$startRows),trans('lang.item_name'));
                    $sheet->cell(('K'.$startRows),trans('lang.qty'));
                    $sheet->cell(('L'.$startRows),trans('rep.delivery_qty'));
                    $sheet->cell(('M'.$startRows),trans('rep.closed_qty'));
                    $sheet->cell(('N'.$startRows),trans('lang.units'));

                    $sheet->cell(('A'.$startRows.':N'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as  $dval) {
                		$startRows++;
				        $sheet->cell('A'.($startRows),$dval->project);
				        $sheet->cell('B'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
				        $sheet->cell('C'.($startRows),$dval->ref_no);
	                    $sheet->cell('D'.($startRows),$dval->pr_no);
	                    $sheet->cell('E'.($startRows),($dval->department!=null ? $dval->department : '#N/A'));
	                    $sheet->cell('F'.($startRows),($dval->supplier!=null ? $dval->supplier : '#N/A'));
	                    $sheet->cell('G'.($startRows),date("d/m/Y",strtotime($dval->delivery_date)));
	                    $sheet->cell('H'.($startRows),($dval->delivery_addr_name!=null ? $dval->delivery_addr_name : '#N/A'));
	                    $sheet->cell('I'.($startRows),$dval->item_code);
	                    $sheet->cell('J'.($startRows),$dval->item_name);
	                    $sheet->cell('K'.($startRows),$dval->qty);
	                    $sheet->cell('L'.($startRows),$dval->deliv_qty);
	                    $sheet->cell('M'.($startRows),$dval->closed_qty);
	                    $sheet->cell('N'.($startRows),$dval->unit);
	                    $sheet->setColumnFormat([('K'.$startRows)=>'0',('L'.$startRows)=>'0',('M'.$startRows)=>'0']);
	                    $sheet->cell(('A'.$startRows.':N'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}

    	return $response;
    }

    public function generate_delivery(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$dep_id       = $request->query("dep_id");
		$sup_id       = $request->query("sup_id");
		$po_id        = $request->query("po_id");
		$del_id       = $request->query("del_id");
		$warehouse_id = $request->query("warehouse_id");
		$trans_status = $request->query("trans_status");
		$group_by     = $request->query("group_by");
		$version      = $request->query("version");

		if (!empty($group_by)) {
			$group_by = ' GROUP BY d.`id`';
		}else{
			$group_by = '';
		}

		if (empty($del_id)) {
			$del_id = '';
		}else{
			$del_id = 'AND di.del_id ='.$del_id;
		}

		if ($po_id!='') {
			$np = str_replace(",","','",$po_id);
			$np .="'";
			$np = substr($np,2);
			$po_id = "AND d.`po_id` IN(".$np.") ";
		}else{
			$po_id = '';
		}

		if ($sup_id!='') {
			$np = str_replace(",","','",$sup_id);
			$np .="'";
			$np = substr($np,2);
			$sup_id = "AND d.`sup_id` IN(".$np.") ";
		}else{
			$sup_id = '';
		}

		if ($dep_id!='') {
			$np = str_replace(",","','",$dep_id);
			$np .="'";
			$np = substr($np,2);
			$dep_id = "AND d.`dep_id` IN(".$np.") ";
		}else{
			$dep_id = '';
		}

		if ($warehouse_id!='') {
			$np = str_replace(",","','",$warehouse_id);
			$np .="'";
			$np = substr($np,2);
			$warehouse_id = "AND di.`warehouse_id` IN(".$np.") ";
		}else{
			$warehouse_id = '';
		}

    	$sql = "SELECT d.*, (SELECT `pr_projects`.`name` FROM `pr_projects` WHERE `pr_projects`.`id` = d.pro_id) AS project, (SELECT `pr_orders`.`ref_no` FROM `pr_orders` WHERE `pr_orders`.`id` = d.po_id) AS po_no, (SELECT `pr_suppliers`.`name` FROM `pr_suppliers` WHERE `pr_suppliers`.`id` = d.sup_id) AS supplier, di.`warehouse_id`, (SELECT `pr_warehouses`.`name` FROM `pr_warehouses` WHERE pr_warehouses.`id` = di.warehouse_id) AS warehouse, di.`item_id`, (SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id` = di.item_id) AS item_code, (SELECT `pr_items`.`name` FROM `pr_items` WHERE `pr_items`.`id` = di.`item_id`) AS item_name, di.`unit`, di.`qty`, di.`return_qty`, di.price, di.total, di.amount, di.desc FROM `pr_deliveries` AS d INNER JOIN `pr_delivery_items` AS di ON d.`id` = di.del_id $del_id AND d.`delete` = 0 AND d.`pro_id` = $project_id $po_id $sup_id $warehouse_id AND d.`trans_date` BETWEEN '$start_date'AND '$end_date' $group_by";

    	$report = DB::select($sql);

    	if ($version=="datatables") {
    		$response = Datatables::of($report)->make(true);
    	}elseif($version=="print"){
    		$response = response()->json($report);
    	}elseif($version=="excel"){
    		Excel::create('delivery_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet('Delivery Report',function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(150);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report'). ' ' .trans('lang.delivery'));
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.project'));
                    $sheet->cell(('B'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('C'.$startRows),trans('lang.reference_no'));
                    $sheet->cell(('D'.$startRows),trans('lang.request_no'));
                    $sheet->cell(('E'.$startRows),trans('lang.department'));
                    $sheet->cell(('F'.$startRows),trans('lang.supplier'));
                    $sheet->cell(('G'.$startRows),trans('lang.delivery_date'));
                    $sheet->cell(('H'.$startRows),trans('lang.delivery_address'));
                    $sheet->cell(('I'.$startRows),trans('lang.item_code'));
                    $sheet->cell(('J'.$startRows),trans('lang.item_name'));
                    $sheet->cell(('K'.$startRows),trans('lang.qty'));
                    $sheet->cell(('L'.$startRows),trans('lang.delivery_qty'));
                    $sheet->cell(('M'.$startRows),trans('lang.close_qty'));
					$sheet->cell(('N'.$startRows),trans('lang.units'));
					$sheet->cell(('O'.$startRows),trans('lang.price'));
					$sheet->cell(('P'.$startRows),trans('lang.amount'));
					$sheet->cell(('Q'.$startRows),trans('lang.discount')."(%)");
					$sheet->cell(('R'.$startRows),trans('lang.discount')."($)");
					$sheet->cell(('S'.$startRows),trans('lang.total'));
					$sheet->cell(('T'.$startRows),trans('lang.desc'));

                    $sheet->cell(('A'.$startRows.':T'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as  $dval) {
                		$startRows++;
				        $sheet->cell('A'.($startRows),$dval->project);
				        $sheet->cell('B'.($startRows),date("Y-m-d",strtotime($dval->trans_date)));
				        $sheet->cell('C'.($startRows),$dval->ref_no);
	                    $sheet->cell('D'.($startRows),!empty($dval->pr_no) ? $dval->pr_no : '#N/A');
	                    $sheet->cell('E'.($startRows),!empty($dval->department) ? $dval->department : '#N/A');
	                    $sheet->cell('F'.($startRows),!empty($dval->supplier) ? $dval->supplier : '#N/A');
	                    $sheet->cell('G'.($startRows),!empty($dval->delivery_date) ? date("d/m/Y",strtotime($dval->delivery_date)) : '#N/A');
	                    $sheet->cell('H'.($startRows),!empty($dval->delivery_addr_name) ? $dval->delivery_addr_name : '#N/A');
	                    $sheet->cell('I'.($startRows),$dval->item_code);
	                    $sheet->cell('J'.($startRows),$dval->item_name);
	                    $sheet->cell('K'.($startRows),$dval->qty);
	                    $sheet->cell('L'.($startRows),!empty($dval->deliv_qty) ? $dval->deliv_qty : 0);
	                    $sheet->cell('M'.($startRows),!empty($dval->closed_qty) ? $dval->closed_qty : 0);
						$sheet->cell('N'.($startRows),$dval->unit);
						$sheet->cell(('O'.$startRows),$dval->price);
						$sheet->cell(('P'.$startRows),$dval->amount);
						$sheet->cell(('Q'.$startRows),$dval->disc_perc);
						$sheet->cell(('R'.$startRows),$dval->disc_usd);
						$sheet->cell(('S'.$startRows),$dval->total);
						$sheet->cell(('T'.$startRows),$dval->desc);
	                    $sheet->setColumnFormat([
							('K'.$startRows)=>'0',
							('L'.$startRows)=>'0',
							('M'.$startRows)=>'0',
							('O'.$startRows)=>'_("$"* #,##0.0000_);_("$"* \(#,##0.0000\);_("$"* "-"??_);_(@_)',
							('P'.$startRows)=>'_("$"* #,##0.0000_);_("$"* \(#,##0.0000\);_("$"* "-"??_);_(@_)',
							('Q'.$startRows)=>'_("$"* #,##0.0000_);_("$"* \(#,##0.0000\);_("$"* "-"??_);_(@_)',
							('R'.$startRows)=>'_("$"* #,##0.0000_);_("$"* \(#,##0.0000\);_("$"* "-"??_);_(@_)',
							('S'.$startRows)=>'_("$"* #,##0.0000_);_("$"* \(#,##0.0000\);_("$"* "-"??_);_(@_)',
						]);
	                    $sheet->cell(('A'.$startRows.':T'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}

    	return $response;
    }

    public function generate_return(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$dep_id       = $request->query("dep_id");
		$sup_id       = $request->query("sup_id");
		$po_id        = $request->query("po_id");
		$return_id    = $request->query("return_id");
		$del_id       = $request->query("del_id");
		$warehouse_id = $request->query("warehouse_id");
		$trans_status = $request->query("trans_status");
		$group_by     = $request->query("group_by");
		$version      = $request->query("version");

		if (!empty($group_by)) {
			$group_by = ' GROUP BY rd.`id`';
		}else{
			$group_by = '';
		}

		if (empty($return_id)) {
			$return_id = '';
		}else{
			$return_id = 'AND rdi.`return_id`='.$return_id;
		}

		if ($po_id!='') {
			$np = str_replace(",","','",$po_id);
			$np .="'";
			$np = substr($np,2);
			$po_id = "AND rd.`po_id` IN(".$np.") ";
		}else{
			$po_id = '';
		}

		if ($sup_id!='') {
			$np = str_replace(",","','",$sup_id);
			$np .="'";
			$np = substr($np,2);
			$sup_id = "AND rd.`sup_id` IN(".$np.") ";
		}else{
			$sup_id = '';
		}

		if ($del_id!='') {
			$np = str_replace(",","','",$del_id);
			$np .="'";
			$np = substr($np,2);
			$del_id = "AND rd.`del_id` IN(".$np.") ";
		}else{
			$del_id = '';
		}

		if ($warehouse_id!='') {
			$np = str_replace(",","','",$warehouse_id);
			$np .="'";
			$np = substr($np,2);
			$warehouse_id = "AND rdi.`warehouse_id` IN(".$np.") ";
		}else{
			$warehouse_id = '';
		}

    	$sql = "SELECT rd.*, (SELECT `pr_projects`.`name` FROM `pr_projects` WHERE `pr_projects`.`id`=rd.pro_id)AS project, (SELECT `pr_deliveries`.`ref_no` FROM `pr_deliveries` WHERE `pr_deliveries`.`id`=rd.del_id)AS del_no, (SELECT `pr_suppliers`.`name` FROM `pr_suppliers` WHERE `pr_suppliers`.`id`=rd.`sup_id`)AS supplier, rdi.`warehouse_id`, rdi.`item_id`, (SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id` = rdi.item_id) AS item_code, (SELECT `pr_items`.`name` FROM `pr_items` WHERE `pr_items`.`id` = rdi.item_id) AS item_name, rdi.`warehouse_id`, (SELECT `pr_warehouses`.`name` FROM `pr_warehouses` WHERE `pr_warehouses`.`id`=rdi.`warehouse_id`)AS warehouse, rdi.`unit`, rdi.`qty`, rdi.`amount`, rdi.`refund`, rdi.`total`, rdi.`note` FROM `pr_return_deliveries` AS rd INNER JOIN `pr_return_delivery_items` AS rdi ON rd.`id` = rdi.`return_id` $return_id AND rd.`pro_id` = $project_id AND rd.`delete` = 0 $del_id $sup_id $warehouse_id AND rd.`trans_date` BETWEEN '$start_date'AND '$end_date' $group_by";

    	$report = DB::select($sql);

    	if ($version=="datatables") {
    		$response = Datatables::of($report)->make(true);
    	}elseif($version=="print"){
    		$response = response()->json($report);
    	}elseif($version=="excel"){
    		Excel::create('return_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet('purchase request report',function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(150);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report').trans('lang.request'));
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.project'));
                    $sheet->cell(('B'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('C'.$startRows),trans('lang.reference_no'));
                    $sheet->cell(('D'.$startRows),trans('lang.request_no'));
                    $sheet->cell(('E'.$startRows),trans('lang.department'));
                    $sheet->cell(('F'.$startRows),trans('lang.supplier'));
                    $sheet->cell(('G'.$startRows),trans('lang.delivery_date'));
                    $sheet->cell(('H'.$startRows),trans('lang.delivery_address'));
                    $sheet->cell(('I'.$startRows),trans('lang.item_code'));
                    $sheet->cell(('J'.$startRows),trans('lang.item_name'));
                    $sheet->cell(('K'.$startRows),trans('lang.qty'));
                    $sheet->cell(('L'.$startRows),trans('lang.delivery_qty'));
                    $sheet->cell(('M'.$startRows),trans('lang.closed_qty'));
                    $sheet->cell(('N'.$startRows),trans('lang.units'));

                    $sheet->cell(('A'.$startRows.':N'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as  $dval) {
                		$startRows++;
				        $sheet->cell('A'.($startRows),$dval->project);
				        $sheet->cell('B'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
				        $sheet->cell('C'.($startRows),$dval->ref_no);
	                    $sheet->cell('D'.($startRows),$dval->pr_no);
	                    $sheet->cell('E'.($startRows),($dval->department!=null ? $dval->department : '#N/A'));
	                    $sheet->cell('F'.($startRows),($dval->supplier!=null ? $dval->supplier : '#N/A'));
	                    $sheet->cell('G'.($startRows),date("d/m/Y",strtotime($dval->delivery_date)));
	                    $sheet->cell('H'.($startRows),($dval->delivery_addr_name!=null ? $dval->delivery_addr_name : '#N/A'));
	                    $sheet->cell('I'.($startRows),$dval->item_code);
	                    $sheet->cell('J'.($startRows),$dval->item_name);
	                    $sheet->cell('K'.($startRows),$dval->qty);
	                    $sheet->cell('L'.($startRows),$dval->deliv_qty);
	                    $sheet->cell('M'.($startRows),$dval->closed_qty);
	                    $sheet->cell('N'.($startRows),$dval->unit);
	                    $sheet->setColumnFormat([('K'.$startRows)=>'0',('L'.$startRows)=>'0',('M'.$startRows)=>'0']);
	                    $sheet->cell(('A'.$startRows.':N'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}

    	return $response;
    }

    public function getHouse(Request $request)
    {
    	if (isset($request->house_type)) {
    		return response()->json(House::select(['id','house_no','house_desc','pro_id','house_type'])->where('pro_id',Session::get('project'))->where('house_type',$request->house_type)->get());
    	}else{
    		return response()->json(House::select(['id','house_no','house_desc','pro_id','street_id'])->where('pro_id',Session::get('project'))->where('street_id',$request->street_id)->get());
    	}

    }

    public function generate_usage(Request $request)
    {
    	ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$eng_usage    = $request->query("eng_usage");
		$sub_usage    = $request->query("sub_usage");
		$street_id    = $request->query("street_id");
		$house_id     = $request->query("house_id");
		$use_id       = $request->query("use_id");
		$warehouse_id = $request->query("warehouse_id");
		$trans_status = $request->query("trans_status");
		$group_by     = $request->query("group_by");
		$version      = $request->query("version");
		$search       = $request->search['value'];

		if (!empty($group_by)) {
			$group_by = ' GROUP BY us.`id`';
		}else{
			$group_by = '';
		}

		if (empty($use_id)) {
			$use_id = '';
		}else{
			$use_id = ' AND usd.`use_id`='.$use_id;
		}

		if (!empty($eng_usage)) {
			$eng_usage = ' AND us.eng_usage='.$eng_usage;
		}else{
			$eng_usage = '';
		}

		if (!empty($sub_usage)) {
			$sub_usage = ' AND us.sub_usage='.$sub_usage;
		}else{
			$sub_usage = '';
		}

		if (!empty($street_id)) {
			$street_id = ' AND usd.`street_id`='.$street_id;
		}else{
			$street_id = '';
		}

		if ($house_id!='') {
			$np = str_replace(",","','",$house_id);
			$np .="'";
			$np = substr($np,2);
			$house_id = "AND usd.`house_id` IN(".$np.") ";
		}else{
			$house_id = '';
		}

		if ($warehouse_id!='') {
			$np = str_replace(",","','",$warehouse_id);
			$np .="'";
			$np = substr($np,2);
			$warehouse_id = "AND usd.`warehouse_id` IN(".$np.") ";
		}else{
			$warehouse_id = '';
		}

    	$sql = "SELECT us.*,(SELECT `pr_projects`.`name` FROM `pr_projects` WHERE `pr_projects`.`id`=us.`pro_id`)AS project, (SELECT `pr_constructors`.`id_card` FROM `pr_constructors` WHERE `pr_constructors`.`id`=us.eng_usage)AS engineer_code, (SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id`=us.eng_usage)AS engineer_name, (SELECT `pr_constructors`.`id_card` FROM `pr_constructors` WHERE `pr_constructors`.`id`=us.sub_usage)AS subconstructor_code, (SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id`=us.sub_usage)AS subconstructor_name, usd.`warehouse_id`, (SELECT `pr_warehouses`.`name` FROM pr_warehouses WHERE `pr_warehouses`.`id`=usd.warehouse_id)AS warehouse, usd.`street_id`, (SELECT `pr_system_datas`.`name` FROM pr_system_datas WHERE `pr_system_datas`.`id`=usd.street_id)AS street, usd.`house_id`, (SELECT `pr_houses`.`house_no` FROM `pr_houses` WHERE `pr_houses`.`id`=usd.house_id)AS house_no, (SELECT `pr_houses`.`house_desc` FROM `pr_houses` WHERE `pr_houses`.`id`=usd.house_id)AS house_desc, (SELECT `pr_houses`.`house_type` FROM `pr_houses` WHERE `pr_houses`.`id`=usd.house_id)AS house_type, usd.`item_id`, (SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id`=usd.item_id)AS item_code, (SELECT `pr_items`.`name` FROM `pr_items` WHERE `pr_items`.`id`=usd.`item_id`)AS item_name, (SELECT `pr_units`.`from_desc` FROM pr_units WHERE `pr_units`.`from_code`=usd.`unit` LIMIT 1)AS unit, usd.`qty`, usd.`stock_qty`, usd.`boq_set`, usd.`note`,0 as cost,0 AS total_cost FROM `pr_usages` AS us INNER JOIN `pr_usage_details` AS usd ON usd.`use_id` = us.id $use_id AND us.`pro_id`=$project_id AND us.`delete` =0 AND us.`trans_date` BETWEEN '$start_date' AND '$end_date' $warehouse_id $eng_usage $sub_usage $street_id $house_id ";

    	$report = DB::select($sql);

    	if ($version=="datatables") {
    		$response = Datatables::of($report)->make(true);
    	}elseif($version=="print"){
    		$response = response()->json($report);
    	}elseif($version=="excel"){
    		Excel::create('usage_items_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
          $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
          $excel->sheet('Usage Item Report',function($sheet) use($report,$start_date,$end_date){
          $startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
	        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
	        $objDrawing->setCoordinates('A1');
	        $objDrawing->setHeight(150);
	        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
            $cells->setFontSize(28);
            $cells->setFontColor(getSetting()->report_header_color);
            $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
					$cells->setFontFamily('Khmer OS Battambang');
              $cells->setFontSize(28);
              $cells->setFontColor(getSetting()->company_name_color);
              $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report')." ".trans('lang.usage'));
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('B'.$startRows),trans('lang.reference_no'));
                    $sheet->cell(('C'.$startRows),trans('lang.reference'));
                    $sheet->cell(('D'.$startRows),trans('lang.engineer'));
                    $sheet->cell(('E'.$startRows),trans('lang.constructor'));
                    $sheet->cell(('F'.$startRows),trans('lang.warehouse'));
                    $sheet->cell(('G'.$startRows),trans('lang.street'));
                    $sheet->cell(('H'.$startRows),trans('lang.house'));
                    $sheet->cell(('I'.$startRows),trans('lang.item_code'));
                    $sheet->cell(('J'.$startRows),trans('lang.item_name'));
                    $sheet->cell(('K'.$startRows),trans('lang.qty'));
                    $sheet->cell(('L'.$startRows),trans('lang.units'));

                    $sheet->cell(('A'.$startRows.':L'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as  $dval) {
                		$startRows++;
      				        $sheet->cell('A'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
      				        $sheet->cell('B'.($startRows),$dval->ref_no);
      				        $sheet->cell('C'.($startRows),$dval->reference);
	                    $sheet->cell('D'.($startRows),$dval->engineer_code." - ".$dval->engineer_name);
	                    $sheet->cell('E'.($startRows),$dval->subconstructor_code." - ".$dval->subconstructor_name);
	                    $sheet->cell('F'.($startRows),$dval->warehouse);
	                    $sheet->cell('G'.($startRows),$dval->street);
	                    $sheet->cell('H'.($startRows),$dval->house_no);
	                    $sheet->cell('I'.($startRows),$dval->item_code);
	                    $sheet->cell('J'.($startRows),$dval->item_name);
	                    $sheet->cell('K'.($startRows),$dval->qty);
	                    $sheet->cell('L'.($startRows),$dval->unit);
	                    $sheet->setColumnFormat([('K'.$startRows)=>'0']);
	                    $sheet->cell(('A'.$startRows.':L'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}

    	return $response;
    }
	public function generate_usageHouse(Request $request)
    {
    	ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$eng_usage    = $request->query("eng_usage");
		$sub_usage    = $request->query("sub_usage");
		$block_id    = $request->query("block_id");
		$street_id    = $request->query("street_id");
		$house_id     = $request->query("house_id");
		$use_id       = $request->query("use_id");
		$warehouse_id = $request->query("warehouse_id");
		$trans_status = $request->query("trans_status");
		$group_by     = $request->query("group_by");
		$version      = $request->query("version");
		$search       = $request->search['value'];
		// if (!empty($group_by)) {
		// 	$group_by = ' GROUP BY us.`id`';
		// }else{
		// 	$group_by = '';
		// }
		// if (empty($use_id)) {
		// 	$use_id = '';
		// }else{
		// 	$use_id = ' AND usd.`use_id`='.$use_id;
		// }
		// if (!empty($eng_usage)) {
		// 	$eng_usage = ' AND us.eng_usage='.$eng_usage;
		// }else{
		// 	$eng_usage = '';
		// }
		// if (!empty($sub_usage)) {
		// 	$sub_usage = ' AND us.sub_usage='.$sub_usage;
		// }else{
		// 	$sub_usage = '';
		// }
		// if (!empty($street_id)) {
		// 	$street_id = ' AND usd.`street_id`='.$street_id;
		// }else{
		// 	$street_id = '';
		// }
		// if (!empty($block_id)) {
		// 	$block_id = ' AND usd.`block_id`='.$block_id;
		// }else{
		// 	$street_id = '';
		// }
		// if ($house_id!='') {
		// 	$np = str_replace(",","','",$house_id);
		// 	$np .="'";
		// 	$np = substr($np,2);
		// 	$house_id = "AND usd.`house_id` IN(".$np.") ";
		// }else{
		// 	$house_id = '';
		// }
		// if ($warehouse_id!='') {
		// 	$np = str_replace(",","','",$warehouse_id);
		// 	$np .="'";
		// 	$np = substr($np,2);
		// 	$warehouse_id = "AND usd.`warehouse_id` IN(".$np.") ";
		// }else{
		// 	$warehouse_id = '';
		// }
    	// $sql = "SELECT us.*,(SELECT `pr_projects`.`name` FROM `pr_projects` 
		// WHERE `pr_projects`.`id`=us.`pro_id`)AS project, (SELECT `pr_constructors`.`id_card` 
		// FROM `pr_constructors` WHERE `pr_constructors`.`id`=us.eng_usage)AS engineer_code, 
		// (SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id`=us.eng_usage)AS engineer_name, 
		// (SELECT `pr_constructors`.`id_card` FROM `pr_constructors` WHERE `pr_constructors`.`id`=us.sub_usage)AS subconstructor_code, 
		// (SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id`=us.sub_usage)AS subconstructor_name,
		//  usd.`warehouse_id`, (SELECT `pr_warehouses`.`name` FROM pr_warehouses WHERE `pr_warehouses`.`id`=usd.warehouse_id)AS warehouse, 
		//  usd.`street_id`, (SELECT `pr_system_datas`.`name` FROM pr_system_datas WHERE `pr_system_datas`.`id`=usd.street_id)AS street,
		//  usd.`house_id`, (SELECT `pr_houses`.`house_no` FROM `pr_houses` WHERE `pr_houses`.`id`=usd.house_id)AS house_no, (SELECT `pr_houses`.`house_desc` FROM `pr_houses` WHERE `pr_houses`.`id`=usd.house_id)AS house_desc, (SELECT `pr_houses`.`house_type` FROM `pr_houses` WHERE `pr_houses`.`id`=usd.house_id)AS house_type, usd.`item_id`, (SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id`=usd.item_id)AS item_code, (SELECT `pr_items`.`name` FROM `pr_items` WHERE `pr_items`.`id`=usd.`item_id`)AS item_name, (SELECT `pr_units`.`from_desc` FROM pr_units WHERE `pr_units`.`from_code`=usd.`unit` LIMIT 1)AS unit, usd.`qty`, usd.`stock_qty`, usd.`boq_set`, usd.`note`,0 as cost,0 AS total_cost FROM `pr_usages` AS us INNER JOIN `pr_usage_details` AS usd ON usd.`use_id` = us.id $use_id AND us.`pro_id`=$project_id AND us.`delete` =0 AND us.`trans_date` BETWEEN '$start_date' AND '$end_date' $warehouse_id $eng_usage $sub_usage $street_id $house_id ";
    	// $report = DB::select($sql);
		DB::statement(DB::raw('set @rownum=0'));
		$report = DB::table('usage_details')
		->leftJoin('houses','usage_details.house_id','houses.id')
		->select(
			DB::raw('@rownum  := @rownum  + 1 AS rownum'),
			DB::raw('(SELECT pr_system_datas.name FROM pr_system_datas WHERE pr_system_datas.id=pr_houses.house_type) as house_type_name'),
			DB::raw('(SELECT pr_system_datas.name FROM pr_system_datas WHERE pr_system_datas.id=pr_houses.street_id) as street_name'),
			DB::raw('(SELECT pr_system_datas.name FROM pr_system_datas WHERE pr_system_datas.id=pr_houses.block_id) as block_name'),
			DB::raw('(SELECT pr_system_datas.name FROM pr_system_datas WHERE pr_system_datas.id=pr_houses.zone_id) as zone_name'),
			DB::raw('SUM(pr_usage_details.total_cost ) as cost_usage'),
			'houses.*'
		);
		if(!empty($block_id)){
			$report = $report->where('houses.block_id',$block_id);
		}
		if(!empty($street_id)){
			$report = $report->where('houses.street_id',$street_id);
		}
		if(!empty($zone_id)){
			$report = $report->where('houses.zone_id',$zone_id);
		}
		$report = $report->groupBy('usage_details.house_id')->Orderby('houses.id',"DESC")->get();		
    	if ($version=="datatables") {
    		$response = Datatables::of($report)
			->addColumn('details_url',function($row){
				return url("report/generate_usageItem/{$row->id}?v=1&version=datatables");
			})
			->addColumn('action',function($row){
				return '<div class="actions">
					<a title="'.trans('lang.print').'" onclick="onPrint(this);" version="printMain" id="'.$row->id.'" class="btn btn-circle btn-icon-only btn-default">
						<i class="fa fa-print"></i>
					</a>
					<a title="'.trans('lang.download').'" onclick="onPrint(this);" version="excelMain" id="'.$row->id.'"  class="btn btn-circle btn-icon-only btn-default">
						<i class="fa fa-file-excel-o"></i>
					</a>
				</div>';
			})
			->make(true);
    	}elseif($version=="print"){
    		$response = response()->json($report);
    	}elseif($version=="export"){
    		Excel::create('usage_house_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
          $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
          $excel->sheet('Usage House Report',function($sheet) use($report,$start_date,$end_date){
          $startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
	        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
	        $objDrawing->setCoordinates('A1');
	        $objDrawing->setHeight(150);
	        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':G'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
            $cells->setFontSize(28);
            $cells->setFontColor(getSetting()->report_header_color);
            $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':G'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':G'.$startRows),function($cells){
					$cells->setFontFamily('Khmer OS Battambang');
              $cells->setFontSize(28);
              $cells->setFontColor(getSetting()->company_name_color);
              $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':G'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report')." ".trans('lang.usage'));
					$sheet->cell(('C'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.id'));
                    $sheet->cell(('B'.$startRows),trans('lang.house_type'));
                    $sheet->cell(('C'.$startRows),trans('lang.house_no'));
                    $sheet->cell(('D'.$startRows),trans('lang.street'));
                    $sheet->cell(('E'.$startRows),trans('lang.block'));
                    $sheet->cell(('F'.$startRows),trans('lang.zone'));
                    $sheet->cell(('G'.$startRows),trans('lang.usage_qty'));
                    $sheet->cell(('A'.$startRows.':G'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });
					$pulsh = 0;
                	foreach ($report as  $dval) {
                		$startRows++;
						$sheet->cell('A'.($startRows),$pulsh++);
						$sheet->cell('B'.($startRows),$dval->house_type_name);
						$sheet->cell('C'.($startRows),$dval->house_no);
	                    $sheet->cell('D'.($startRows),$dval->street_name);
	                    $sheet->cell('E'.($startRows),$dval->block_name);
	                    $sheet->cell('F'.($startRows),$dval->zone_name);
	                    $sheet->cell('G'.($startRows),$dval->cost_usage);
	                    $sheet->cell(('A'.$startRows.':G'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}
    	return $response;
    }
	public function generate_usageItem(Request $request,$house_id){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$eng_usage    = $request->query("eng_usage");
		$sub_usage    = $request->query("sub_usage");
		$street_id    = $request->query("street_id");
		// $house_id     = $request->query("house_id");
		$use_id       = $request->query("use_id");
		$warehouse_id = $request->query("warehouse_id");
		$trans_status = $request->query("trans_status");
		$group_by     = $request->query("group_by");
		$version      = $request->query("version");
		$search       = $request->search['value'];

		if (!empty($group_by)) {
			$group_by = ' GROUP BY us.`id`';
		}else{
			$group_by = '';
		}

		if (empty($use_id)) {
			$use_id = '';
		}else{
			$use_id = ' AND usd.`use_id`='.$use_id;
		}

		if (!empty($eng_usage)) {
			$eng_usage = ' AND us.eng_usage='.$eng_usage;
		}else{
			$eng_usage = '';
		}

		if (!empty($sub_usage)) {
			$sub_usage = ' AND us.sub_usage='.$sub_usage;
		}else{
			$sub_usage = '';
		}

		if (!empty($street_id)) {
			$street_id = ' AND usd.`street_id`='.$street_id;
		}else{
			$street_id = '';
		}

		if ($house_id!='') {
			$np = str_replace(",","','",$house_id);
			$np .="'";
			$np = substr($np,2);
			// $house_id = "AND usd.`house_id` IN(".$np.") ";
			$house_id = "AND usd.`house_id` = $house_id ";
		}else{
			$house_id = '';
		}

		if ($warehouse_id!='') {
			$np = str_replace(",","','",$warehouse_id);
			$np .="'";
			$np = substr($np,2);
			$warehouse_id = "AND usd.`warehouse_id` IN(".$np.") ";
		}else{
			$warehouse_id = '';
		}

    	$sql = "
		SELECT us.*,
		(SELECT `pr_houses`.`house_no` FROM `pr_houses` WHERE `pr_houses`.`id`=usd.house_id)AS house_no, 
		(SELECT `pr_houses`.`house_desc` FROM `pr_houses` WHERE `pr_houses`.`id`=usd.house_id)AS house_desc, 
		(SELECT `pr_houses`.`house_type` FROM `pr_houses` WHERE `pr_houses`.`id`=usd.house_id)AS house_type, 
		usd.`item_id`, 
		(SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id`=usd.item_id)AS item_code, 
		(SELECT `pr_items`.`name` FROM `pr_items` WHERE `pr_items`.`id`=usd.`item_id`)AS item_name, 
		(SELECT `pr_units`.`from_desc` FROM pr_units WHERE `pr_units`.`from_code`=usd.`unit` LIMIT 1)AS unit, 
		usd.`qty`, 
		usd.`house_id`, 
		usd.`stock_qty`, 
		(SELECT pr_stocks.cost FROM pr_stocks WHERE pr_stocks.ref_no = us.ref_no  LIMIT 1) as cost_usage,
		SUM(usd.qty) as total_qty,
		SUM(usd.total_cost) as total_cost_usage,
		usd.`boq_set`, usd.`note`,0 as cost,0 AS total_cost FROM `pr_usages` AS us 
		INNER JOIN `pr_usage_details` AS usd ON usd.`use_id` = us.id $use_id 
		AND us.`pro_id`=$project_id 
		AND us.`delete` =0 
		$house_id GROUP BY usd.item_id";
    	$report = DB::select($sql);
    	if ($version=="datatables") {
    		$response = Datatables::of($report)
			->addColumn('details_url',function($row){
				return url("report/generate_usageHouseDetail/{$row->house_id}/{$row->item_id}?v=1&version=datatables");
			})
			->addColumn('rownum',function($row){
				return $row->item_id;
			})
			->addColumn('action',function($row){
				return '<div class="actions">
					<a title="'.trans('lang.print').'" onclick="onPrint(this);" version="printDetial" id="'.$row->item_id.'"  class="btn btn-circle btn-icon-only btn-default">
						<i class="fa fa-print"></i>
					</a>
					<a title="'.trans('lang.download').'" onclick="onPrint(this);" version="excelDetail" id="'.$row->item_id.'"   class="btn btn-circle btn-icon-only btn-default">
						<i class="fa fa-file-excel-o"></i>
					</a>
				</div>';
			})
			->make(true);
    	}elseif($version=="printMain"){
    		$response = response()->json($report);
    	}elseif($version=="excelMain"){
    		Excel::create('usage_items_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
          $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
          $excel->sheet('Usage Item Report',function($sheet) use($report,$start_date,$end_date){
          $startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
	        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
	        $objDrawing->setCoordinates('A1');
	        $objDrawing->setHeight(150);
	        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':E'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':E'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
            $cells->setFontSize(28);
            $cells->setFontColor(getSetting()->report_header_color);
            $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':E'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':E'.$startRows),function($cells){
					$cells->setFontFamily('Khmer OS Battambang');
              $cells->setFontSize(28);
              $cells->setFontColor(getSetting()->company_name_color);
              $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':E'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report')." ".trans('lang.usage'));
					$sheet->cell(('C'.$startRows.':E'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.item_code'));
                    $sheet->cell(('B'.$startRows),trans('lang.item_name'));
                    $sheet->cell(('C'.$startRows),trans('lang.qty'));
                    $sheet->cell(('D'.$startRows),trans('lang.units'));
                    $sheet->cell(('E'.$startRows),trans('lang.total'));
                    $sheet->cell(('A'.$startRows.':E'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });
                	foreach ($report as  $dval) {
                		$startRows++;
      				        $sheet->cell('A'.($startRows),$dval->item_code);
      				        $sheet->cell('B'.($startRows),$dval->item_name);
      				        $sheet->cell('C'.($startRows),$dval->total_qty);
	                    	$sheet->cell('D'.($startRows),$dval->unit);
	                    	$sheet->cell('E'.($startRows),$dval->total_cost_usage);
	                    	$sheet->cell(('A'.$startRows.':E'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}
    	return $response;
	}
	public function generate_usageHouseDetail(Request $request,$house_id,$item_id){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$eng_usage    = $request->query("eng_usage");
		$sub_usage    = $request->query("sub_usage");
		$street_id    = $request->query("street_id");
		// $house_id     = $request->query("house_id");
		$use_id       = $request->query("use_id");
		$warehouse_id = $request->query("warehouse_id");
		$trans_status = $request->query("trans_status");
		$group_by     = $request->query("group_by");
		$version      = $request->query("version");
		$search       = $request->search['value'];

		if (!empty($group_by)) {
			$group_by = ' GROUP BY us.`id`';
		}else{
			$group_by = '';
		}

		if (empty($use_id)) {
			$use_id = '';
		}else{
			$use_id = ' AND usd.`use_id`='.$use_id;
		}

		if (!empty($eng_usage)) {
			$eng_usage = ' AND us.eng_usage='.$eng_usage;
		}else{
			$eng_usage = '';
		}

		if (!empty($sub_usage)) {
			$sub_usage = ' AND us.sub_usage='.$sub_usage;
		}else{
			$sub_usage = '';
		}

		if (!empty($street_id)) {
			$street_id = ' AND usd.`street_id`='.$street_id;
		}else{
			$street_id = '';
		}

		if ($house_id!='') {
			$np = str_replace(",","','",$house_id);
			$np .="'";
			$np = substr($np,2);
			// $house_id = "AND usd.`house_id` IN(".$np.") ";
			$house_id = "AND usd.`house_id` = $house_id ";
		}else{
			$house_id = '';
		}

		if ($warehouse_id!='') {
			$np = str_replace(",","','",$warehouse_id);
			$np .="'";
			$np = substr($np,2);
			$warehouse_id = "AND usd.`warehouse_id` IN(".$np.") ";
		}else{
			$warehouse_id = '';
		}

    	$sql = "SELECT us.*,(SELECT `pr_projects`.`name` FROM `pr_projects` WHERE `pr_projects`.`id`=us.`pro_id`)AS project, 
		(SELECT `pr_constructors`.`id_card` FROM `pr_constructors` WHERE `pr_constructors`.`id`=us.eng_usage)AS engineer_code, 
		(SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id`=us.eng_usage)AS engineer_name, 
		(SELECT `pr_constructors`.`id_card` FROM `pr_constructors` WHERE `pr_constructors`.`id`=us.sub_usage)AS subconstructor_code, 
		(SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id`=us.sub_usage)AS subconstructor_name, 
		usd.`warehouse_id`, 
		(SELECT `pr_warehouses`.`name` FROM pr_warehouses WHERE `pr_warehouses`.`id`=usd.warehouse_id)AS warehouse, 
		usd.`street_id`, 
		(SELECT `pr_system_datas`.`name` FROM pr_system_datas WHERE `pr_system_datas`.`id`=usd.street_id)AS street, 
		usd.`house_id`, 
		(SELECT `pr_houses`.`house_no` FROM `pr_houses` WHERE `pr_houses`.`id`=usd.house_id)AS house_no, 
		(SELECT `pr_houses`.`house_desc` FROM `pr_houses` WHERE `pr_houses`.`id`=usd.house_id)AS house_desc, 
		(SELECT `pr_houses`.`house_type` FROM `pr_houses` WHERE `pr_houses`.`id`=usd.house_id)AS house_type, 
		usd.`item_id`, 
		(SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id`=usd.item_id)AS item_code, 
		(SELECT `pr_items`.`name` FROM `pr_items` WHERE `pr_items`.`id`=usd.`item_id`)AS item_name, 
		(SELECT `pr_units`.`from_desc` FROM pr_units WHERE `pr_units`.`from_code`=usd.`unit` LIMIT 1)AS unit, 
		usd.`stock_qty`, 
		usd.id, 
		usd.total_cost as total_cost_usage,
		usd.cost as cost_usage,
		usd.qty as qty,
		usd.`boq_set`, usd.`note`,0 as cost,0 AS total_cost FROM `pr_usages` AS us 
		INNER JOIN `pr_usage_details` AS usd ON usd.`use_id` = us.id $use_id 
		AND us.`pro_id`=$project_id 
		AND us.`delete` =0 $house_id AND usd.item_id = $item_id ";
		// print_r($sql);exit;
    	$report = DB::select($sql);
    	if ($version=="datatables") {
    		$response = Datatables::of($report)->make(true);
    	}elseif($version=="printDetial"){
    		$response = response()->json($report);
    	}elseif($version=="excelDetail"){
    		Excel::create('usage_items_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
          $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
          $excel->sheet('Usage Item Report',function($sheet) use($report,$start_date,$end_date){
          $startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
	        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
	        $objDrawing->setCoordinates('A1');
	        $objDrawing->setHeight(150);
	        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
            $cells->setFontSize(28);
            $cells->setFontColor(getSetting()->report_header_color);
            $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
					$cells->setFontFamily('Khmer OS Battambang');
              $cells->setFontSize(28);
              $cells->setFontColor(getSetting()->company_name_color);
              $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report')." ".trans('lang.usage'));
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('B'.$startRows),trans('lang.reference_no'));
                    $sheet->cell(('C'.$startRows),trans('lang.reference'));
                    $sheet->cell(('D'.$startRows),trans('lang.engineer'));
                    $sheet->cell(('E'.$startRows),trans('lang.constructor'));
                    $sheet->cell(('F'.$startRows),trans('lang.warehouse'));
                    $sheet->cell(('G'.$startRows),trans('lang.street'));
                    $sheet->cell(('H'.$startRows),trans('lang.house'));
                    $sheet->cell(('I'.$startRows),trans('lang.item_code'));
                    $sheet->cell(('J'.$startRows),trans('lang.item_name'));
                    $sheet->cell(('K'.$startRows),trans('lang.qty'));
                    $sheet->cell(('L'.$startRows),trans('lang.units'));

                    $sheet->cell(('A'.$startRows.':L'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as  $dval) {
                		$startRows++;
      				        $sheet->cell('A'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
      				        $sheet->cell('B'.($startRows),$dval->ref_no);
      				        $sheet->cell('C'.($startRows),$dval->reference);
	                    $sheet->cell('D'.($startRows),$dval->engineer_code." - ".$dval->engineer_name);
	                    $sheet->cell('E'.($startRows),$dval->subconstructor_code." - ".$dval->subconstructor_name);
	                    $sheet->cell('F'.($startRows),$dval->warehouse);
	                    $sheet->cell('G'.($startRows),$dval->street);
	                    $sheet->cell('H'.($startRows),$dval->house_no);
	                    $sheet->cell('I'.($startRows),$dval->item_code);
	                    $sheet->cell('J'.($startRows),$dval->item_name);
	                    $sheet->cell('K'.($startRows),$dval->qty);
	                    $sheet->cell('L'.($startRows),$dval->unit);
	                    $sheet->setColumnFormat([('K'.$startRows)=>'0']);
	                    $sheet->cell(('A'.$startRows.':L'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}

    	return $response;
	}
    public function generate_return_usage(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$eng_usage    = $request->query("eng_usage");
		$sub_usage    = $request->query("sub_usage");
		$street_id    = $request->query("street_id");
		$del_id       = $request->query("del_id");
		$house_id     = $request->query("house_id");
		$warehouse_id = $request->query("warehouse_id");
		$trans_status = $request->query("trans_status");
		$group_by     = $request->query("group_by");
		$version      = $request->query("version");
		$search       = $request->search['value'];

		if (!empty($group_by)) {
			$group_by = ' GROUP BY reu.`id`';
		}else{
			$group_by = '';
		}

		if (empty($del_id)) {
			$del_id = '';
		}else{
			$del_id = 'AND reud.`return_id`='.$del_id;
		}

		//reud.`return_id`

		if (!empty($eng_usage)) {
			$eng_usage = ' AND reu.eng_return='.$eng_usage;
		}else{
			$eng_usage = '';
		}

		if (!empty($sub_usage)) {
			$sub_usage = ' AND reu.sub_return='.$sub_usage;
		}else{
			$sub_usage = '';
		}

		if (!empty($street_id)) {
			$street_id = ' AND reud.`street_id`='.$street_id;
		}else{
			$street_id = '';
		}

		if ($house_id!='') {
			$np = str_replace(",","','",$house_id);
			$np .="'";
			$np = substr($np,2);
			$house_id = "AND reud.`house_id` IN(".$np.") ";
		}else{
			$house_id = '';
		}

		if ($warehouse_id!='') {
			$np = str_replace(",","','",$warehouse_id);
			$np .="'";
			$np = substr($np,2);
			$warehouse_id = "AND reud.`warehouse_id` IN(".$np.") ";
		}else{
			$warehouse_id = '';
		}

    	$sql = "SELECT reu.*, (SELECT `pr_projects`.`name` FROM `pr_projects` WHERE `pr_projects`.`id`=reu.`pro_id`)AS project, (SELECT `pr_constructors`.`id_card` FROM `pr_constructors` WHERE `pr_constructors`.`id`=reu.eng_return)AS engineer_code, (SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id`=reu.eng_return)AS engineer_name, (SELECT `pr_constructors`.`id_card` FROM `pr_constructors` WHERE `pr_constructors`.`id`=reu.sub_return)AS subconstructor_code, (SELECT `pr_constructors`.`name` FROM `pr_constructors` WHERE `pr_constructors`.`id`=reu.sub_return)AS subconstructor_name, reud.`warehouse_id`, (SELECT `pr_warehouses`.`name` FROM `pr_warehouses` WHERE `pr_warehouses`.`id`=reud.`warehouse_id`)AS warehouse, reud.`street_id`, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=reud.`street_id`)AS street, reud.`house_id`, (SELECT `pr_houses`.`house_no` FROM `pr_houses` WHERE `pr_houses`.`id`=reud.`house_id`)AS house_no, reud.`item_id`, (SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id`=reud.item_id)AS item_code, (SELECT `pr_items`.`name` FROM `pr_items` WHERE pr_items.`id`=reud.`item_id`)AS item_name, (SELECT `pr_units`.`from_desc` FROM `pr_units` WHERE `pr_units`.`from_code`=reud.`unit` LIMIT 1)AS unit, reud.qty, reud.usage_qty, reud.boq_set, reud.note FROM `pr_return_usages` AS reu INNER JOIN `pr_return_usage_details` AS reud ON reud.`return_id` = reu.`id` $del_id AND reu.`pro_id` = $project_id AND reu.`delete` = 0 AND reud.`delete` = 0 AND reu.`trans_date` BETWEEN '$start_date'AND '$end_date'$eng_usage $sub_usage $group_by";

    	$report = DB::select($sql);

    	if ($version=="datatables") {
    		$response = Datatables::of($report)->make(true);
    	}elseif($version=="print"){
    		$response = response()->json($report);
    	}elseif($version=="excel"){
    		Excel::create('return_usage_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet('purchase request report',function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(150);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report').trans('lang.request'));
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.project'));
                    $sheet->cell(('B'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('C'.$startRows),trans('lang.reference_no'));
                    $sheet->cell(('D'.$startRows),trans('lang.request_no'));
                    $sheet->cell(('E'.$startRows),trans('lang.department'));
                    $sheet->cell(('F'.$startRows),trans('lang.supplier'));
                    $sheet->cell(('G'.$startRows),trans('lang.delivery_date'));
                    $sheet->cell(('H'.$startRows),trans('lang.delivery_address'));
                    $sheet->cell(('I'.$startRows),trans('lang.item_code'));
                    $sheet->cell(('J'.$startRows),trans('lang.item_name'));
                    $sheet->cell(('K'.$startRows),trans('lang.qty'));
                    $sheet->cell(('L'.$startRows),trans('lang.delivery_qty'));
                    $sheet->cell(('M'.$startRows),trans('lang.closed_qty'));
                    $sheet->cell(('N'.$startRows),trans('lang.units'));

                    $sheet->cell(('A'.$startRows.':N'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as  $dval) {
                		$startRows++;
				        $sheet->cell('A'.($startRows),$dval->project);
				        $sheet->cell('B'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
				        $sheet->cell('C'.($startRows),$dval->ref_no);
	                    $sheet->cell('D'.($startRows),$dval->pr_no);
	                    $sheet->cell('E'.($startRows),($dval->department!=null ? $dval->department : '#N/A'));
	                    $sheet->cell('F'.($startRows),($dval->supplier!=null ? $dval->supplier : '#N/A'));
	                    $sheet->cell('G'.($startRows),date("d/m/Y",strtotime($dval->delivery_date)));
	                    $sheet->cell('H'.($startRows),($dval->delivery_addr_name!=null ? $dval->delivery_addr_name : '#N/A'));
	                    $sheet->cell('I'.($startRows),$dval->item_code);
	                    $sheet->cell('J'.($startRows),$dval->item_name);
	                    $sheet->cell('K'.($startRows),$dval->qty);
	                    $sheet->cell('L'.($startRows),$dval->deliv_qty);
	                    $sheet->cell('M'.($startRows),$dval->closed_qty);
	                    $sheet->cell('N'.($startRows),$dval->unit);
	                    $sheet->setColumnFormat([('K'.$startRows)=>'0',('L'.$startRows)=>'0',('M'.$startRows)=>'0']);
	                    $sheet->cell(('A'.$startRows.':N'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}

    	return $response;
    }


    public function generate_sub_boq(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		// print_r($request->all());exit;
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$item_id      = $request->query("item_id");
		$house_id     = $request->query("house_id");
		$house_type   = $request->query("house_type");
		$trans_status = $request->query("trans_status");
		$group_by     = $request->query("group_by");
		$version      = $request->query("version");

		if ($house_id!='') {
			$np = str_replace(",","','",$house_id);
			$np .="'";
			$np = substr($np,2);
			$house_id = "AND boq.`house_id` IN(".$np.") ";
		}else{
			$house_id = '';
		}

		if (empty($house_type)) {
			$house_type = 0;
		}

		if ($item_id!='') {
			$np = str_replace(",","','",$item_id);
			$np .="'";
			$np = substr($np,2);
			$item_id = "AND boqi.`item_id` IN(".$np.") ";
		}else{
			$item_id = '';
		}

    	$sql = "SELECT (SELECT `pr_projects`.`name` FROM `pr_projects` WHERE `pr_projects`.`id`=$project_id)AS project,boq.`trans_by`, boq.`house_id`, boqi.cost,
		 (SELECT `pr_houses`.`house_no` FROM `pr_houses` WHERE `pr_houses`.id=pr_boq_houses.`house_id`)AS house_no, 
		 (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=(SELECT `pr_houses`.`house_type` FROM `pr_houses` WHERE `pr_houses`.`id`=pr_boq_houses.`house_id`))AS house_type, boqi.`item_id`,
		  (SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id`=boqi.item_id)AS item_code, (SELECT `pr_items`.`name` FROM `pr_items` WHERE `pr_items`.`id`=boqi.item_id)AS item_name, 
		  (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=(SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE `pr_items`.`id`=boqi.`item_id`))AS item_type,
		  (SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE `pr_items`.`id`=boqi.`item_id`)AS item_type_id,
		  (SELECT `pr_system_datas`.`desc` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=(SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE `pr_items`.`id`=boqi.`item_id`))AS item_type_desc,
		  (SELECT `pr_items`.`cost_purch` FROM `pr_items` WHERE `pr_items`.`id`=boqi.`item_id` AND `pr_items`.`unit_purch`=boqi.`unit`)AS item_price, boqi.`qty_std`, boqi.`qty_add`, 
		  (SELECT `pr_units`.`from_desc` FROM `pr_units` WHERE `pr_units`.`from_code`=boqi.`unit` LIMIT 1)AS unit FROM `pr_boqs` AS boq JOIN pr_boq_houses ON `pr_boq_houses`.boq_id = boq.id
		  INNER JOIN `pr_boq_items` AS boqi ON boqi.`boq_house_id` = pr_boq_houses.`id` AND pr_boq_houses.`house_id` IN(SELECT `pr_houses`.`id` FROM `pr_houses` WHERE `pr_houses`.`house_type`=$house_type) $house_id $item_id ";
		//   print_r($sql);

    	$report = DB::select($sql);

    	if ($version=="datatables") {
    		$response = Datatables::of($report)->make(true);
    	}elseif($version=="print"){
    		$response = response()->json($report);
    	}elseif($version=="excel"){
			$date = date('Y-m-d H:i:s');
    		Excel::create("BOQ ({$date})",function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet("BOQ Detail",function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(150);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':H'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':H'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':H'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':H'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':H'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report').trans('lang.request'));
					$sheet->cell(('C'.$startRows.':H'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.project'));
                    $sheet->cell(('B'.$startRows),trans('lang.house_type'));
                    $sheet->cell(('C'.$startRows),trans('lang.house'));
                    $sheet->cell(('D'.$startRows),trans('lang.item_type'));
                    $sheet->cell(('E'.$startRows),trans('lang.item_code'));
                    $sheet->cell(('F'.$startRows),trans('lang.item_name'));
                    $sheet->cell(('G'.$startRows),trans('lang.boq_std'));
                    $sheet->cell(('H'.$startRows),trans('lang.boq_add'));
                    $sheet->cell(('I'.$startRows),trans('lang.unit'));
                    $sheet->cell(('J'.$startRows),trans('lang.price'));

                    $sheet->cell(('A'.$startRows.':J'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as  $dval) {
                		$startRows++;
				        $sheet->cell('A'.($startRows),$dval->project);
				        $sheet->cell('B'.($startRows),$dval->house_type);
				        $sheet->cell('C'.($startRows),$dval->house_no);
	                    $sheet->cell('D'.($startRows),$dval->item_type);
	                    $sheet->cell('E'.($startRows),$dval->item_code);
	                    $sheet->cell('F'.($startRows),$dval->item_name);
	                    $sheet->cell('G'.($startRows),$dval->qty_std);
	                    $sheet->cell('H'.($startRows),$dval->qty_add);
	                    $sheet->cell('I'.($startRows),$dval->unit);
	                    $sheet->cell('J'.($startRows),$dval->item_price);

	                    $sheet->setColumnFormat([
							('G'.$startRows)=>'0',
							('H'.$startRows)=>'0',
							('J'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)'
						]);

	                    $sheet->cell(('A'.$startRows.':J'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}

    	return $response;
    }

    public function generate_boq_detail(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$item_id      = $request->query("item_id");
		$house_id     = $request->query("house_id");
		$house_type   = $request->query("house_type");
		$trans_status = $request->query("trans_status");
		$group_by     = $request->query("group_by");
		$version      = $request->query("version");

		if ($house_id!='') {
			$np = str_replace(",","','",$house_id);
			$np .="'";
			$np = substr($np,2);
			$house_id = "AND boq.`house_id` IN(".$np.") ";
		}else{
			$house_id = '';
		}

		if (empty($house_type)) {
			$house_type = 0;
		}

		if ($item_id!='') {
			$np = str_replace(",","','",$item_id);
			$np .="'";
			$np = substr($np,2);
			$item_id = "AND boqi.`item_id` IN(".$np.") ";
		}else{
			$item_id = '';
		}
		// print_r($request->all());exit;
    	$sql = "SELECT (SELECT `pr_projects`.`name` FROM `pr_projects` WHERE `pr_projects`.`id`=$project_id)AS project,boq.`trans_by`, boq.`house_id`, (SELECT `pr_houses`.`house_no` FROM `pr_houses` WHERE `pr_houses`.id=boq.`house_id`)AS house_no, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=(SELECT `pr_houses`.`house_type` FROM `pr_houses` WHERE `pr_houses`.`id`=boq.`house_id`))AS house_type, boqi.`item_id`, (SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id`=boqi.item_id)AS item_code, (SELECT `pr_items`.`name` FROM `pr_items` WHERE `pr_items`.`id`=boqi.item_id)AS item_name, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=(SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE `pr_items`.`id`=boqi.`item_id`))AS item_type,(SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE `pr_items`.`id`=boqi.`item_id`)AS item_type_id,(SELECT `pr_system_datas`.`desc` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=(SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE `pr_items`.`id`=boqi.`item_id`))AS item_type_desc,(SELECT `pr_items`.`cost_purch` FROM `pr_items` WHERE `pr_items`.`id`=boqi.`item_id` AND `pr_items`.`unit_purch`=boqi.`unit`)AS item_price, boqi.`qty_std`, boqi.`qty_add`, (SELECT `pr_units`.`from_desc` FROM `pr_units` WHERE `pr_units`.`from_code`=boqi.`unit` LIMIT 1)AS unit FROM `pr_boqs` AS boq INNER JOIN `pr_boq_items` AS boqi ON boqi.`boq_id` = boq.`id` AND boq.is_revise = 0 AND boq.`house_id` IN(SELECT `pr_houses`.`id` FROM `pr_houses` WHERE `pr_houses`.`house_type`=$house_type) $house_id $item_id ";

    	$report = DB::select($sql);

    	if ($version=="datatables") {
    		$response = Datatables::of($report)->make(true);
    	}elseif($version=="print"){
    		$response = response()->json($report);
    	}elseif($version=="excel"){
    		Excel::create('boq_detail_report_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet('purchase request report',function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(150);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report').trans('lang.request'));
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.project'));
                    $sheet->cell(('B'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('C'.$startRows),trans('lang.reference_no'));
                    $sheet->cell(('D'.$startRows),trans('lang.request_no'));
                    $sheet->cell(('E'.$startRows),trans('lang.department'));
                    $sheet->cell(('F'.$startRows),trans('lang.supplier'));
                    $sheet->cell(('G'.$startRows),trans('lang.delivery_date'));
                    $sheet->cell(('H'.$startRows),trans('lang.delivery_address'));
                    $sheet->cell(('I'.$startRows),trans('lang.item_code'));
                    $sheet->cell(('J'.$startRows),trans('lang.item_name'));
                    $sheet->cell(('K'.$startRows),trans('lang.qty'));
                    $sheet->cell(('L'.$startRows),trans('lang.delivery_qty'));
                    $sheet->cell(('M'.$startRows),trans('lang.closed_qty'));
                    $sheet->cell(('N'.$startRows),trans('lang.units'));

                    $sheet->cell(('A'.$startRows.':N'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as  $dval) {
                		$startRows++;
				        $sheet->cell('A'.($startRows),$dval->project);
				        $sheet->cell('B'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
				        $sheet->cell('C'.($startRows),$dval->ref_no);
	                    $sheet->cell('D'.($startRows),$dval->pr_no);
	                    $sheet->cell('E'.($startRows),($dval->department!=null ? $dval->department : '#N/A'));
	                    $sheet->cell('F'.($startRows),($dval->supplier!=null ? $dval->supplier : '#N/A'));
	                    $sheet->cell('G'.($startRows),date("d/m/Y",strtotime($dval->delivery_date)));
	                    $sheet->cell('H'.($startRows),($dval->delivery_addr_name!=null ? $dval->delivery_addr_name : '#N/A'));
	                    $sheet->cell('I'.($startRows),$dval->item_code);
	                    $sheet->cell('J'.($startRows),$dval->item_name);
	                    $sheet->cell('K'.($startRows),$dval->qty);
	                    $sheet->cell('L'.($startRows),$dval->deliv_qty);
	                    $sheet->cell('M'.($startRows),$dval->closed_qty);
	                    $sheet->cell('N'.($startRows),$dval->unit);
	                    $sheet->setColumnFormat([('K'.$startRows)=>'0',('L'.$startRows)=>'0',('M'.$startRows)=>'0']);
	                    $sheet->cell(('A'.$startRows.':N'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}

    	return $response;
    }

    public function stock_balance_detail(Request $request,$item_id)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
    	$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$warehouse_id = $request->query("warehouse_id");
		$group_by     = $request->query("group_by");
		$version      = $request->query("version");

		if (empty($group_by)) {
			$group_by = 0;
		}

		if ($warehouse_id!='') {
			$np = str_replace(",","','",$warehouse_id);
			$np .="'";
			$np = substr($np,2);
			$warehouse_id = $np;
		}else{
			$warehouse_id = '';
		}

    	$sql = "CALL stock_balance_detail($project_id,$warehouse_id,'$start_date','$end_date',$item_id);";

    	return Datatables::of(DB::select($sql))->make(true);
    }

    public function generate_stock_balance(Request $request)
    {
    	ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$warehouse_id = $request->query("warehouse_id");
		$group_by     = $request->query("group_by");
		$hideZero     = $request->query("hideZero");
		$version      = $request->query("version");

		if (empty($group_by)) {
			$group_by = 0;
		}

		if ($warehouse_id!='') {
			$np = str_replace(",","','",$warehouse_id);
			$np .="'";
			$np = substr($np,2);
			$warehouse_id = $np;
		}else{
			$warehouse_id = '0';
		}

    	$sql = "CALL stock_balance($project_id,$warehouse_id,'$start_date','$end_date',$group_by,$hideZero);";

    	$report = DB::select($sql);

    	if ($version=="datatables") {
    		$response = Datatables::of($report)->addColumn('detail_',function($obj){
    			return url("/report/stock_balance_detail/{$obj->item_id}");
    		})->make(true);
    	}elseif($version=="print"){
    		$response = response()->json($report);
    	}elseif($version=="excel"){
    		Excel::create('stock_balance_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet('Stock Balance Report',function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(150);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':G'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':G'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':G'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report').trans('lang.request'));
					$sheet->cell(('C'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.line_no'));
                    $sheet->cell(('B'.$startRows),trans('lang.warehouse'));
                    $sheet->cell(('C'.$startRows),trans('lang.item_type'));
                    $sheet->cell(('D'.$startRows),trans('lang.item_code'));
                    $sheet->cell(('E'.$startRows),trans('lang.item_name'));
                    $sheet->cell(('F'.$startRows),trans('lang.qty'));
                    $sheet->cell(('G'.$startRows),trans('lang.units'));

                    $sheet->cell(('A'.$startRows.':G'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as $key =>  $dval) {
                		$startRows++;
				        $sheet->cell('A'.($startRows),$key+1);
				        $sheet->cell('B'.($startRows),$dval->warehouse);
				        $sheet->cell('C'.($startRows),$dval->category);
	                    $sheet->cell('D'.($startRows),$dval->item_code);
	                    $sheet->cell('E'.($startRows),$dval->item_name);
	                    $sheet->cell('F'.($startRows),$dval->qty);
	                    $sheet->cell('G'.($startRows),$dval->unit_stock);
	                    $sheet->setColumnFormat([('F'.$startRows)=>'0']);
	                    $sheet->cell(('A'.$startRows.':G'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}

    	return $response;
    }

    public function generate_all_stock_transaction(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date       = $request->query("start_date");
		$end_date         = $request->query("end_date");
		$project_id       = $request->session()->get('project');
		$warehouse_id     = $request->query("warehouse_id");
		$transaction_type = $request->query("transaction_type");
		$version          = $request->query("version");

		if (empty($warehouse_id)) {
			$warehouse_id = 0;
		}

		if (empty($transaction_type)) {
			$transaction_type = 0;
		}

    	$sql = "CALL all_stock_transaction($project_id,$warehouse_id,'$start_date','$end_date',$transaction_type);";

    	$report = DB::select($sql);

    	if ($version=="datatables") {
    		$response = Datatables::of($report)->make(true);
    	}elseif($version=="print"){
    		$response = response()->json($report);
    	}elseif($version=="excel"){
    		Excel::create('all_stock_transaction_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
                $excel->sheet('All Stock Transaction',function($sheet) use($report,$start_date,$end_date){
                	$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
			        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
			        $objDrawing->setCoordinates('A1');
			        $objDrawing->setHeight(150);
			        $objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(28);
                        $cells->setFontColor(getSetting()->company_name_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report').trans('lang.all_stock_transaction'));
					$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.project'));
                    $sheet->cell(('B'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('C'.$startRows),trans('lang.reference_no'));
                    $sheet->cell(('D'.$startRows),trans('lang.request_no'));
                    $sheet->cell(('E'.$startRows),trans('lang.department'));
                    $sheet->cell(('F'.$startRows),trans('lang.supplier'));
                    $sheet->cell(('G'.$startRows),trans('lang.delivery_date'));
                    $sheet->cell(('H'.$startRows),trans('lang.delivery_address'));
                    $sheet->cell(('I'.$startRows),trans('lang.item_code'));
                    $sheet->cell(('J'.$startRows),trans('lang.item_name'));
                    $sheet->cell(('K'.$startRows),trans('lang.qty'));
                    $sheet->cell(('L'.$startRows),trans('lang.delivery_qty'));
                    $sheet->cell(('M'.$startRows),trans('lang.closed_qty'));
                    $sheet->cell(('N'.$startRows),trans('lang.units'));

                    $sheet->cell(('A'.$startRows.':N'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as  $dval) {
                		$startRows++;
				        $sheet->cell('A'.($startRows),$dval->project);
				        $sheet->cell('B'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
				        $sheet->cell('C'.($startRows),$dval->ref_no);
	                    $sheet->cell('D'.($startRows),$dval->pr_no);
	                    $sheet->cell('E'.($startRows),($dval->department!=null ? $dval->department : '#N/A'));
	                    $sheet->cell('F'.($startRows),($dval->supplier!=null ? $dval->supplier : '#N/A'));
	                    $sheet->cell('G'.($startRows),date("d/m/Y",strtotime($dval->delivery_date)));
	                    $sheet->cell('H'.($startRows),($dval->delivery_addr_name!=null ? $dval->delivery_addr_name : '#N/A'));
	                    $sheet->cell('I'.($startRows),$dval->item_code);
	                    $sheet->cell('J'.($startRows),$dval->item_name);
	                    $sheet->cell('K'.($startRows),$dval->qty);
	                    $sheet->cell('L'.($startRows),$dval->deliv_qty);
	                    $sheet->cell('M'.($startRows),$dval->closed_qty);
	                    $sheet->cell('N'.($startRows),$dval->unit);
	                    $sheet->setColumnFormat([('K'.$startRows)=>'0',('L'.$startRows)=>'0',('M'.$startRows)=>'0']);
	                    $sheet->cell(('A'.$startRows.':N'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}

    	return $response;
    }

    public function view_delivery_with_return(Request $request)
    {
    	$data = [
			'title'       => trans('rep.delivery'),
			'icon'        => 'fa fa-truck',
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'delivery'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('rep.delivery'),
				]
			]
		];
    	return view("reports.delivery_with_return",$data);
    }

    public function delivery_with_return(Request $request)
    {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
    	try {
			$start_date   = $request->start_date;
			$end_date     = $request->end_date;
			$project_id   = $request->session()->get('project');
			$warehouse_id = "";
			$item_id      = "";
			$po_id        = "";
			$sup_id       = "";
			$version      = $request->query("version");

			if ($request->warehouse_id) {
				$warehouse_id = $request->warehouse_id;
			}

			if ($request->item_id) {
				$item_id      = $request->item_id;
			}

			if ($request->po_id) {
				$po_id        = $request->po_id;
			}

			if ($request->sup_id) {
				$sup_id       = $request->sup_id;
			}

			if ($warehouse_id!='') {
				$np = str_replace(",","','",$warehouse_id);
				$np .="'";
				$np = substr($np,2);
				$warehouse_id = "AND Ai.`warehouse_id` IN($np) ";
			}else{
				$warehouse_id = "AND Ai.`warehouse_id` != 0 ";
			}

			if ($item_id!='') {
				$np = str_replace(",","','",$item_id);
				$np .="'";
				$np = substr($np,2);
				$item_id = "AND Ai.`item_id` IN($np) ";
			}else{
				$item_id = "AND Ai.`item_id` != 0 ";
			}


			if ($po_id!='') {
				$np = str_replace(",","','",$po_id);
				$np .="'";
				$np = substr($np,2);
				$po_id = "AND A.`po_id` IN($np) ";
			}else{
				$po_id = "AND A.`po_id` != 0 ";
			}

			if ($sup_id!='') {
				$np = str_replace(",","','",$sup_id);
				$np .="'";
				$np = substr($np,2);
				$sup_id = "AND A.`sup_id` IN($np) ";
			}else{
				$sup_id = "AND A.`sup_id` != 0 ";
			}

			$sql = "SELECT D.trans_date, D.ref_no, D.po_code, D.supplier, D.warehouse_name, D.category, D.item_code, D.item_name, D.qty, D.return_qty, D.unit_name FROM (SELECT C.id, C.trans_date, C.ref_no, C.po_id, C.po_code, C.sup_id, C.supplier, C.warehouse_id, C.warehouse_name, C.category_id, (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = C.category_id AND `pr_system_datas`.`type` = 'IT') AS category, C.item_code, C.item_name, ROUND((C.qty * C.unit_qty), 3) AS qty, ROUND((C.return_qty * C.unit_qty), 3) AS return_qty, C.unit_name FROM (SELECT B.*, (SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE `pr_items`.`id` = B.item_id) AS category_id, (SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id` = B.item_id) AS item_code, (SELECT `pr_items`.`name` FROM `pr_items` WHERE `pr_items`.`id` = B.item_id) AS item_name, (SELECT `pr_orders`.`ref_no` FROM `pr_orders` WHERE `pr_orders`.`id` = B.po_id) AS po_code, (SELECT `pr_suppliers`.`name` FROM `pr_suppliers` WHERE `pr_suppliers`.`id` = B.sup_id) AS supplier, (SELECT `pr_warehouses`.`name` FROM `pr_warehouses` WHERE `pr_warehouses`.`id` = B.warehouse_id) AS warehouse_name, (SELECT `pr_units`.`factor` FROM `pr_units` WHERE `pr_units`.`from_code` = B.unit LIMIT 1) AS unit_qty, (SELECT `pr_units`.`to_code` FROM `pr_units` WHERE `pr_units`.`from_code` = B.unit LIMIT 1) AS unit_name FROM (SELECT A.`id`, A.`trans_date`, A.`ref_no`, A.`po_id`, A.`sup_id`, A.`sub_total`, A.`disc_perc`, A.`disc_usd`, A.`grand_total`, A.`note`, Ai.`warehouse_id`, Ai.`item_id`, Ai.`qty`, Ai.`return_qty`, Ai.`unit`, Ai.`price`, Ai.`amount`, Ai.`total`, Ai.`desc` FROM `pr_deliveries` AS A INNER JOIN `pr_delivery_items` AS Ai ON A.`id` = Ai.`del_id` AND A.`delete` = 0 AND A.`pro_id` = $project_id AND A.`trans_date` BETWEEN '$start_date'AND '$end_date' $po_id $sup_id $warehouse_id $item_id) AS B) AS C) AS D ";

			$report = DB::select($sql);

			if ($version=="datatables") {
	    		$response = Datatables::of($report)->make(true);
	    	}elseif($version=="print"){
	    		$response = response()->json($report);
	    	}elseif($version=="excel"){
	    		Excel::create('delivery_with_return_'.date('Y_m_d_H_i_s'),function($excel) use($report,$start_date,$end_date){
	                $excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
	                $excel->sheet('Delivery with Return Delivery',function($sheet) use($report,$start_date,$end_date){
	                	$startRows = 1;
						$sheet->setAutoSize(true);
						$sheet->mergeCells('A1:A3');
						$objDrawing = new PHPExcel_Worksheet_Drawing;
				        $objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
				        $objDrawing->setCoordinates('A1');
				        $objDrawing->setHeight(150);
				        $objDrawing->setWorksheet($sheet);

						$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
						$sheet->cell(('C'.$startRows),getSetting()->report_header);
						$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(28);
	                        $cells->setFontColor(getSetting()->report_header_color);
	                        $cells->setAlignment('center');
						});
						$startRows++;
						$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
						$sheet->cell(('C'.$startRows),getSetting()->company_name);
						$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(28);
	                        $cells->setFontColor(getSetting()->company_name_color);
	                        $cells->setAlignment('center');
						});
						$startRows++;
						$sheet->mergeCells(('C'.$startRows.':L'.$startRows));
						$sheet->cell(('C'.$startRows),trans('lang.report').trans('lang.delivery'));
						$sheet->cell(('C'.$startRows.':L'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(16);
	                        $cells->setFontColor(getSetting()->report_header_color);
	                        $cells->setAlignment('center');
						});
						$startRows++;
						$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
						$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
						$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('left');
						});

						$startRows++;
	                    $sheet->cell(('A'.$startRows),trans('lang.trans_date'));
	                    $sheet->cell(('B'.$startRows),trans('lang.reference_no'));
	                    $sheet->cell(('C'.$startRows),trans('rep.po_code'));
	                    $sheet->cell(('D'.$startRows),trans('lang.supplier'));
	                    $sheet->cell(('E'.$startRows),trans('lang.warehouse'));
	                    $sheet->cell(('F'.$startRows),trans('lang.category'));
	                    $sheet->cell(('G'.$startRows),trans('lang.item_code'));
	                    $sheet->cell(('H'.$startRows),trans('lang.item_name'));
	                    $sheet->cell(('I'.$startRows),trans('rep.delivery_qty'));
	                    $sheet->cell(('J'.$startRows),trans('rep.return_qty'));
	                    $sheet->cell(('K'.$startRows),trans('lang.units'));
	                    $sheet->cell(('L'.$startRows),trans('lang.other'));

	                    $sheet->cell(('A'.$startRows.':L'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setFontColor('#ffffff');
	                        $cells->setBackground(getSetting()->report_header_color);
	                        $cells->setAlignment('center');
	                    });

	                	foreach ($report as  $dval) {
	                		$startRows++;
					        $sheet->cell('A'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
					        $sheet->cell('B'.($startRows),$dval->ref_no);
					        $sheet->cell('C'.($startRows),$dval->po_code);
		                    $sheet->cell('D'.($startRows),$dval->supplier);
		                    $sheet->cell('E'.($startRows),$dval->warehouse_name);
		                    $sheet->cell('F'.($startRows),$dval->category);
		                    $sheet->cell('G'.($startRows),$dval->item_code);
		                    $sheet->cell('H'.($startRows),$dval->item_name);
		                    $sheet->cell('I'.($startRows),$dval->qty);
		                    $sheet->cell('J'.($startRows),$dval->return_qty);
		                    $sheet->cell('K'.($startRows),$dval->unit_name);
		                    $sheet->cell('L'.($startRows),"");
		                    $sheet->setColumnFormat([('I'.$startRows)=>'0',('J'.$startRows)=>'0']);
		                    $sheet->cell(('A'.$startRows.':K'.$startRows),function($cells){
		                        $cells->setFontFamily('Khmer OS Battambang');
		                        $cells->setFontSize(11);
		                        $cells->setAlignment('center');
		                    });
		                    $sheet->cell(('H'.$startRows),function($cells){
		                        $cells->setFontFamily('Khmer OS Battambang');
		                        $cells->setFontSize(11);
		                        $cells->setAlignment('left');
		                    });
	                	}
	                });
	            })->download('xlsx');
	    	}

	    	return $response;

    	} catch (\Exception $e) {
    		var_dump($e->getMessage());exit;
    		return redirect()->back();
    	}
    }

	public function subUsageCosting(Request $request,$usageID,$houseID,$itemID){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$version = $request->query('version');

		$report = Stock::select([
			'usage_details.use_id',
			'usage_details.house_id',
			'usage_details.item_id',
			'usage_details.street_id',
			'stocks.*',
			'items.code',
			'items.name',
			'items.unit_stock',
			'items.unit_usage'
			])
		->leftJoin('usage_details',function($join){
			$join->on('usage_details.item_id','stocks.item_id')
				 ->on('usage_details.warehouse_id','stocks.warehouse_id');
		})
		->leftJoin('items','items.id','usage_details.item_id')
		->where('usage_details.delete',0)
		->where('stocks.delete',0)
		->where('stocks.trans_ref','O')
		->where('stocks.ref_type','usage items')
		->where('usage_details.use_id',$usageID)
		->where('usage_details.house_id',$houseID)
		->where('usage_details.item_id',$itemID)
		->groupBy('stocks.id')->get();

		if($version == 'datatables'){
			return Datatables::of($report)
			->addColumn('unit',function($row){
				if($item = Item::find($row->item_id)){
					if($unit = Unit::where(['from_code' => $row->unit, 'to_code' => $item->unit_usage])->first()){
						return $unit->to_desc;
					}
				}
				return $row->unit;
			})
			->addColumn('qty',function($row){
				if($item = Item::find($row->item_id)){
					if($unit = Unit::where(['from_code' => $row->unit, 'to_code' => $item->unit_usage])->first()){
						if($unit->factor < 1){
							return $row->qty / $unit->factor;
						}else{
							return $row->qty * $unit->factor;
						}
					}
				}
				return $row->qty;
			})
			->addColumn('cost',function($row){
				if($item = Item::find($row->item_id)){
					if($unit = Unit::where(['from_code' => $row->unit, 'to_code' => $item->unit_usage])->first()){
						return $row->cost / $unit->factor;
					}
				}
				return $row->cost;
			})
			->addColumn('factor',function($row){
				if($item = Item::find($row->item_id)){
					if($unit = Unit::where(['from_code' => $row->unit, 'to_code' => $item->unit_usage])->first()){
						return $unit->factor;
					}
				}
				return 0;
			})
			->make(true);
		}elseif($version == 'excel'){
			$date = date('Y-m-d H:i:s');
			Excel::create("Usage Costing Detail({$date})",function($excel) use($report,$request,$usageID,$houseID,$itemID){
				$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
				$excel->sheet("Usage Costing Detail",function($sheet) use($report,$request,$usageID,$houseID,$itemID){
				$startRows = 1;
				$sheet->setAutoSize(true);
				$sheet->mergeCells('A1:A3');
				$objDrawing = new PHPExcel_Worksheet_Drawing;
				$objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
				$objDrawing->setCoordinates('A1');
				$objDrawing->setHeight(150);
				$objDrawing->setWorksheet($sheet);
	
						$sheet->mergeCells(('C'.$startRows.':P'.$startRows));
						$sheet->cell(('C'.$startRows),getSetting()->report_header);
						$sheet->cell(('C'.$startRows.':P'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
							$cells->setFontSize(28);
							$cells->setFontColor(getSetting()->report_header_color);
							$cells->setAlignment('center');
						});
						$startRows++;
						$sheet->mergeCells(('C'.$startRows.':P'.$startRows));
						$sheet->cell(('C'.$startRows),getSetting()->company_name);
						$sheet->cell(('C'.$startRows.':P'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
							$cells->setFontSize(28);
							$cells->setFontColor(getSetting()->company_name_color);
							$cells->setAlignment('center');
						});
						$startRows++;
						$sheet->mergeCells(('C'.$startRows.':P'.$startRows));
						$sheet->cell(('C'.$startRows),trans('lang.report')." ".trans('rep.report_usage_costing'));
						$sheet->cell(('C'.$startRows.':P'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
							$cells->setFontSize(16);
							$cells->setFontColor(getSetting()->report_header_color);
							$cells->setAlignment('center');
						});
						$startRows++;
						$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
						// Date
						$start_date = $request->query('start_date');
						$end_date = $request->query('end_date');
						$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
						$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
							$cells->setFontSize(11);
							$cells->setAlignment('left');
						});
	
						$startRows++;
						$sheet->cell(('A'.$startRows),trans('lang.trans_date'));
						$sheet->cell(('B'.$startRows),trans('lang.warehouse'));
						$sheet->cell(('C'.$startRows),trans('lang.engineer'));
						$sheet->cell(('D'.$startRows),trans('lang.subcontractor'));
						$sheet->cell(('E'.$startRows),trans('lang.zone'));
						$sheet->cell(('F'.$startRows),trans('lang.block'));
						$sheet->cell(('G'.$startRows),trans('lang.street'));
						$sheet->cell(('H'.$startRows),trans('lang.house_type'));
						$sheet->cell(('I'.$startRows),trans('lang.house'));
						$sheet->cell(('J'.$startRows),trans('lang.name'));
						$sheet->cell(('K'.$startRows),trans('lang.num'));
						$sheet->cell(('L'.$startRows),trans('lang.item_type'));
						$sheet->cell(('M'.$startRows),trans('lang.item_code'));
						$sheet->cell(('N'.$startRows),trans('lang.item_name'));
						$sheet->cell(('O'.$startRows),trans('lang.qty'));
						$sheet->cell(('P'.$startRows),trans('lang.unit'));
						$sheet->cell(('Q'.$startRows),trans('lang.cost'));
						$sheet->cell(('R'.$startRows),trans('lang.asset_value'));
	
						$sheet->cell(('A'.$startRows.':R'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
							$cells->setFontSize(11);
							$cells->setFontColor('#ffffff');
							$cells->setBackground(getSetting()->report_header_color);
							$cells->setAlignment('center');
						});
	
						foreach ($report as  $dval) {
							$NA = "N/A";
							$startRows++;
							// DATE
							$sheet->cell('A'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
							// WAREHOUSE
							$sheet->cell('B'.($startRows),$NA);
							if($warehouse = Warehouse::find($dval->warehouse_id)){
								$sheet->cell('B'.($startRows),$warehouse->name);
							}
							// ENGINEER
							$sheet->cell('C'.($startRows),$NA);
							if($engineer = Constructor::find($dval->eng_usage)){
								$sheet->cell('C'.($startRows),$engineer->id_card);
							}
							// SUBCONTRACTOR
							$sheet->cell('D'.($startRows),$NA);
							if($subcontractor = Constructor::find($dval->sub_usage)){
								$sheet->cell('D'.($startRows),$subcontractor->id_card);
							}
							// ZONE
							$sheet->cell('E'.($startRows),$NA);
							if(getSetting()->allow_zone == 1){
								if($house = House::find($dval->house_id)){
									if($zone = SystemData::find($house->zone_id)){
										$sheet->cell('E'.($startRows),$zone->name);
									}
								}
							}
							// BLOCK
							$sheet->cell('F'.($startRows),$NA);
							if(getSetting()->allow_block == 1){
								if($house = House::find($dval->house_id)){
									if($block = SystemData::find($house->block_id)){
										$sheet->cell('F'.($startRows),$block->name);
									}
								}
							}
							// STREET
							$sheet->cell('G'.($startRows),$NA);
							if($street = SystemData::find($dval->street_id)){
								$sheet->cell('G'.($startRows),$street->name);
							}
							// HOUSE TYPE
							$sheet->cell('H'.($startRows),$NA);
							if($house = House::find($dval->house_id)){
								if($houseType = SystemData::find($house->house_type)){
									$sheet->cell('H'.($startRows),$houseType->name);
								}
							}
							// HOUSE
							$sheet->cell('I'.($startRows),$NA);
							if($house = House::find($dval->house_id)){
								$sheet->cell('I'.($startRows),$house->house_no);
							}
							// NAME
							$sheet->cell('J'.($startRows),$dval->ref_no);
							// NUM
							$sheet->cell('K'.($startRows),$dval->id);
							// ITEM TYPE
							$sheet->cell('L'.($startRows),$NA);
							// ITEM CODE
							$sheet->cell('M'.($startRows),$NA);
							// ITEM NAME
							$sheet->cell('N'.($startRows),$NA);
							if($item = Item::find($dval->item_id)){
								if($itemType = SystemData::find($item->cat_id)){
									$sheet->cell('L'.($startRows),$itemType->name);
								}
								// ITEM CODE
								$sheet->cell('M'.($startRows),$item->code);
								// ITEM NAME
								$sheet->cell('N'.($startRows),$item->name);
							}
							// QTY
							$sheet->cell('O'.($startRows),$dval->qty);
							if($unit = Unit::where(['from_code' => $dval->unit, 'to_code' => $dval->unit_usage])->first()){
								if($unit->factor < 1){
									$sheet->cell('O'.($startRows),($dval->qty / $unit->factor));
								}else{
									$sheet->cell('O'.($startRows),($dval->qty * $unit->factor));
								}
							}
							// UNIT
							$sheet->cell('P'.($startRows),$dval->unit);
							if($unit = Unit::where(['from_code' => $dval->unit, 'to_code' => $dval->unit_usage])->first()){
								$sheet->cell('P'.($startRows),$unit->to_desc);
							}
							// COST
							$sheet->cell('Q'.($startRows),$dval->cost);
							if($unit = Unit::where(['from_code' => $dval->unit, 'to_code' => $dval->unit_usage])->first()){
								$sheet->cell('Q'.($startRows),($dval->cost / $unit->factor));
							}
							// ASSET VALUE
							$sheet->cell('R'.($startRows),$dval->amount);

							$sheet->setColumnFormat([
								('O'.$startRows)=>'0',
								('Q'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)',
								('R'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)'
								]);
							$sheet->cell(('A'.$startRows.':R'.$startRows),function($cells){
								$cells->setFontFamily('Khmer OS Battambang');
								$cells->setFontSize(11);
								$cells->setAlignment('center');
							});
						}
					});
				})->download('xlsx');
		}elseif($version == 'print'){

			$warehouse = false;
			if($wareID = $request->query('warehouse_id')){
				if(!$warehouse = Warehouse::find($wareID)){
					$warehouse = false;
				}
			}

			$engineer = false;
			if($engID = $request->query('eng_usage')){
				if(!$engineer = Constructor::find($engID)){
					$engineer = false;
				}
			}

			$subcontractor = false;
			if($subID = $request->query('sub_usage')){
				if(!$subcontractor = Constructor::find($subID)){
					$subcontractor = false;
				}
			}

			$zone = false;
			if(getSetting()->allow_zone == 1){
				if($zoneID = $request->query('zone_id')){
					if(!$zone = SystemData::find($zoneID)){
						$zone = false;
					}
				}
			}

			$block = false;
			if(getSetting()->allow_block == 1){
				if($blockID = $request->query('block_id')){
					if(!$block = SystemData::find($blockID)){
						$block = false;
					}
				}
			}

			$street = false;
			if($streetID = $request->query('street_id')){
				if(!$street = SystemData::find($streetID)){
					$street = false;
				}
			}

			$houseType = false;
			if($houseTypeID = $request->query('house_type')){
				if(!$houseType = SystemData::find($houseTypeID)){
					$houseType = false;
				}
			}

			$house = false;
			if($houseID = $request->query('house_id')){
				if(!$house = House::find($houseID)){
					$house = false;
				}
			}

			$itemType = false;
			if($item = Item::find($itemID)){
				if(!$itemType = SystemData::find($item->cat_id)){
					$itemType = false;
				}
			}

			$item = false;
			if(!$item = Item::find($itemID)){
				$item = false;
			}

			if(!$startDate = $request->query('start_date')){
				$startDate = date('Y-m-d');
			}

			if(!$endDate = $request->query('end_date')){
				$endDate = date('Y-m-d');
			}

			$data = [
				'title' => trans('lang.usage_costing_detail'),
				'startDate' => $startDate,
				'endDate' => $endDate,
				'warehouse' => $warehouse,
				'engineer' => $engineer,
				'subcontractor' => $subcontractor,
				'zone' => $zone,
				'block' => $block,
				'street' => $street,
				'houseType' => $houseType,
				'house' => $house,
				'itemType' => $itemType,
				'item' => $item,
				'report'=> $report,
			];
			return view('reports.usage.print.sub_usage_costing',$data);
		}else{
			return redirect(url('report/usage-costing'));
		}
	}

    public function generate_usage_costing(Request $request)
    {
    	ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$start_date   = $request->query("start_date");
		$end_date     = $request->query("end_date");
		$project_id   = $request->session()->get('project');
		$eng_usage    = $request->query("eng_usage");
		$sub_usage    = $request->query("sub_usage");
		$zone_id      = $request->query("zone_id");
		$block_id     = $request->query("block_id");
		$street_id    = $request->query("street_id");
		$house_type   = $request->query("house_type");
		$house_id     = $request->query("house_id");
		$item_type    = $request->query("item_type");
		$item_id      = $request->query("item_id");
		$use_id       = $request->query("use_id");
		$warehouse_id = $request->query("warehouse_id");
		$trans_status = $request->query("trans_status");
		$group_by     = $request->query("group_by");
		$version      = $request->query("version");
		$search       = $request->search['value'];

		$columns = [
			'usage_details.*',
			'usages.reference',
			'usages.ref_no',
			'usages.eng_usage',
			'usages.sub_usage',
			'usages.trans_date',
			'usages.pro_id',
			'houses.house_no',
			'houses.house_desc',
			'houses.house_type',
			'items.code as item_code',
			'items.name as item_name',
			'items.cat_id',
			'items.unit_usage',
			DB::raw('(SELECT SUM(pr_usage_details.`qty`) FROM pr_usage_details WHERE pr_usages.`id` = pr_usage_details.`use_id`) AS use_qty')
		];

		$report  = UsageDetails::select($columns)
					->leftJoin('usages','usages.id','usage_details.use_id')
					->leftJoin('houses','houses.id','usage_details.house_id')
					->leftJoin('items','items.id','usage_details.item_id')
					->where('usages.delete',0)
					->where('usages.pro_id',$project_id)
					->where('usages.trans_date','>=',$start_date)
					->where('usages.trans_date','<=',$end_date);

		if ($use_id) {
			$report = $report->where('usage_details.use_id',$use_id);
		}

		if ($warehouse_id) {
			$report = $report->where('usage_details.warehouse_id',$warehouse_id);
		}

		if ($eng_usage) {
			$report = $report->where('usages.eng_usage',$eng_usage);
		}

		if ($sub_usage) {
			$report = $report->where('usages.sub_usage',$sub_usage);
		}

		if ($zone_id) {
			if($houseIds = House::where(['zone_id' => $zone_id])->pluck('id')){
				$report = $report->whereIn('usage_details.house_id',[$houseIds]);
			}
		}

		if ($block_id) {
			if($houseIds = House::where(['block_id' => $block_id])->pluck('id')){
				$report = $report->whereIn('usage_details.house_id',[$houseIds]);
			}
		}

		if ($street_id) {
			$report = $report->where('usage_details.street_id',$street_id);
		}

		if ($house_type) {
			if($houseIds = House::where(['house_type' => $house_type])->pluck('id')){
				$report = $report->whereIn('usage_details.house_id',[$houseIds]);
			}
		}

		if ($house_id) {
			$report = $report->where('usage_details.house_id',$house_id);
		}

		if ($item_id) {
			$report = $report->where('usage_details.item_id',$item_id);
		}

		if ($group_by) {
			$report = $report->groupBy('usages.id')->groupBy('items.id');
		}
		

    	$report = $report->get();
		$sum_qty = 0;
    	if ($version=="datatables") {
    		return Datatables::of($report)
			->addColumn('details_url',function($row){
				
				return url("report/usage/subUsageCosting/{$row->use_id}/{$row->house_id}/{$row->item_id}?v=1&version=datatables");
			})
			->addColumn('action',function($row) use($request){
				$routePrint = url("report/usage/subUsageCosting/{$row->use_id}/{$row->house_id}/{$row->item_id}?v=1&version=print");
				$routeExcel = url("report/usage/subUsageCosting/{$row->use_id}/{$row->house_id}/{$row->item_id}?v=1&version=excel");

				if ($use_id = $request->use_id) {
					$routeExcel .= "&use_id={$use_id}";
					$routePrint .= "&use_id={$use_id}";
				}
		
				if ($warehouse_id = $request->warehouse_id) {
					$routeExcel .= "&warehouse_id={$warehouse_id}";
					$routePrint .= "&warehouse_id={$warehouse_id}";
				}
		
				if ($eng_usage = $request->eng_usage) {
					$routeExcel .= "&eng_usage={$eng_usage}";
					$routePrint .= "&eng_usage={$eng_usage}";
				}
		
				if ($sub_usage = $request->sub_usage) {
					$routeExcel .= "&sub_usage={$sub_usage}";
					$routePrint .= "&sub_usage={$sub_usage}";
				}
		
				if ($zone_id = $request->zone_id) {
					$routeExcel .= "&zone_id={$zone_id}";
					$routePrint .= "&zone_id={$zone_id}";
				}
		
				if ($block_id = $request->block_id) {
					$routeExcel .= "&block_id={$block_id}";
					$routePrint .= "&block_id={$block_id}";
				}
		
				if ($street_id = $request->street_id) {
					$routeExcel .= "&street_id={$street_id}";
					$routePrint .= "&street_id={$street_id}";
				}
		
				if ($house_type = $request->house_type) {
					$routeExcel .= "&house_type={$house_type}";
					$routePrint .= "&house_type={$house_type}";
				}
		
				if ($item_id = $request->item_id) {
					$routeExcel .= "&item_id={$item_id}";
					$routePrint .= "&item_id={$item_id}";
				}

				if($end_date = $request->end_date){
					$routeExcel .= "&end_date={$end_date}";
					$routePrint .= "&end_date={$end_date}";
				}

				if($start_date = $request->start_date){
					$routeExcel .= "&start_date={$start_date}";
					$routePrint .= "&start_date={$start_date}";
				}

				return
					'<a onclick="onDetailPrint(this);" route="'.$routePrint.'" target="_new" title="'.trans('lang.print').'" class="btn btn-xs purple print-record">'.
					'	<i class="fa fa-print"></i>'.
					'</a>'.
					'<a href="'.$routeExcel.'" title="'.trans('lang.download').'" class="btn btn-xs green download-record">'.
					'	<i class="fa fa-file-excel-o"></i>'.
					'</a>';
			})
			->addColumn('unit',function($row){
				if($unit = Unit::where(['from_code' => $row->unit, 'to_code' => $row->unit_usage])->first()){
					return $unit->to_desc;
				}
				return '';
			})
			->addColumn('qty',function($row){
				if($unit = Unit::where(['from_code' => $row->unit, 'to_code' => $row->unit_usage])->first()){
					if($unit->factor < 1){
						return $row->qty / $unit->factor;
					}else{
						return $row->qty * $unit->factor;
					}
				}
				return $row->qty;
			})
			->addColumn('amount',function($row){
				$prefix = DB::getTablePrefix();
				$amount = 0;
				if($stocks = Stock::select([DB::raw("SUM({$prefix}stocks.amount) AS amount")])->where(['ref_id' => $row->use_id,'ref_no' => $row->ref_no])->get()){
					foreach($stocks as $stock){
						$amount += $stock->amount;
					}
				}
				return $amount;
			})
			->addColumn('project',function($row){
				if($project = Project::find($row->pro_id)){
					return $project->name;
				}
				return '';
			})
			->addColumn('engineer_code',function($row){
				if($engineer = Constructor::find($row->eng_usage)){
					return $engineer->name;
				}
				return '';
			})
			->addColumn('engineer_name',function($row){
				if($engineer = Constructor::find($row->eng_usage)){
					return $engineer->id_card;
				}
				return '';
			})
			->addColumn('subconstructor_code',function($row){
				if($subcontractor = Constructor::find($row->sub_usage)){
					return $subcontractor->name;
				}
				return '';
			})
			->addColumn('subconstructor_name',function($row){
				if($subcontractor = Constructor::find($row->sub_usage)){
					return $subcontractor->id_card;
				}
				return '';
			})
			->addColumn('warehouse',function($row){
				if($warehouse = Warehouse::find($row->warehouse_id)){
					return $warehouse->name;
				}
				return '';
			})
			->addColumn('street',function($row){
				if($street = SystemData::find($row->street_id)){
					return $street->name;
				}
				return '';
			})
			->addColumn('house_type',function($row){
				if($houseType = SystemData::find($row->house_type)){
					return $houseType->name;
				}
				return '';
			})
			->addColumn('item_type',function($row){
				if($itemType = SystemData::find($row->cat_id)){
					return $itemType->name;
				}
				return '';
			})
			->make(true);
    	}elseif($version=="print"){
			$data = [
				'title' => trans('lang.usage_costing'),
				'report' => $report,
			];
			return view('reports.usage.print.usage_costing',$data);
    	}elseif($version=="excel"){
			$date = date('Y-m-d H:i:s');
    		Excel::create("Usage Costing({$date})",function($excel) use($report,$start_date,$end_date){
				$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
				$excel->sheet('Usage With Item Cost Report',function($sheet) use($report,$start_date,$end_date){
				$startRows = 1;
				$sheet->setAutoSize(true);
				$sheet->mergeCells('A1:A3');
				$objDrawing = new PHPExcel_Worksheet_Drawing;
				$objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
				$objDrawing->setCoordinates('A1');
				$objDrawing->setHeight(150);
				$objDrawing->setWorksheet($sheet);

				$sheet->mergeCells(('C'.$startRows.':P'.$startRows));
				$sheet->cell(('C'.$startRows),getSetting()->report_header);
				$sheet->cell(('C'.$startRows.':P'.$startRows),function($cells){
					$cells->setFontFamily('Khmer OS Battambang');
					$cells->setFontSize(28);
					$cells->setFontColor(getSetting()->report_header_color);
					$cells->setAlignment('center');
				});
				$startRows++;
				$sheet->mergeCells(('C'.$startRows.':P'.$startRows));
				$sheet->cell(('C'.$startRows),getSetting()->company_name);
				$sheet->cell(('C'.$startRows.':P'.$startRows),function($cells){
					$cells->setFontFamily('Khmer OS Battambang');
					$cells->setFontSize(28);
             		$cells->setFontColor(getSetting()->company_name_color);
              		$cells->setAlignment('center');
				});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':P'.$startRows));
					$sheet->cell(('C'.$startRows),trans('rep.report_usage_costing'));
					$sheet->cell(('C'.$startRows.':P'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(16);
                        $cells->setFontColor(getSetting()->report_header_color);
                        $cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($start_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($end_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setAlignment('left');
					});

					$startRows++;
                    $sheet->cell(('A'.$startRows),trans('lang.trans_date'));
                    $sheet->cell(('B'.$startRows),trans('lang.reference_no'));
                    $sheet->cell(('C'.$startRows),trans('lang.warehouse'));
                    $sheet->cell(('D'.$startRows),trans('lang.engineer_code'));
                    $sheet->cell(('E'.$startRows),trans('lang.engineer_name'));
                    $sheet->cell(('F'.$startRows),trans('lang.subcontractor_code'));
                    $sheet->cell(('G'.$startRows),trans('lang.subcontractor_name'));
                    $sheet->cell(('H'.$startRows),trans('lang.zone'));
                    $sheet->cell(('I'.$startRows),trans('lang.block'));
                    $sheet->cell(('J'.$startRows),trans('lang.street'));
                    $sheet->cell(('K'.$startRows),trans('lang.house_type'));
                    $sheet->cell(('L'.$startRows),trans('lang.house'));
                    $sheet->cell(('M'.$startRows),trans('lang.item_type'));
					$sheet->cell(('N'.$startRows),trans('lang.item_code'));
					$sheet->cell(('O'.$startRows),trans('lang.item_name'));
					$sheet->cell(('P'.$startRows),trans('lang.qty'));
					$sheet->cell(('Q'.$startRows),trans('lang.unit'));
					$sheet->cell(('R'.$startRows),trans('lang.asset_value'));

                    $sheet->cell(('A'.$startRows.':R'.$startRows),function($cells){
                        $cells->setFontFamily('Khmer OS Battambang');
                        $cells->setFontSize(11);
                        $cells->setFontColor('#ffffff');
                        $cells->setBackground(getSetting()->report_header_color);
                        $cells->setAlignment('center');
                    });

                	foreach ($report as  $dval) {
                		$startRows++;
						$NA = "N/A";
						// DATE
  				        $sheet->cell('A'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
						// REFERENCE
						$sheet->cell('B'.($startRows),$dval->ref_no);
						// WAREHOUSE
  				        $sheet->cell('C'.($startRows),$NA);
						if($warehouse = Warehouse::find($dval->warehouse_id)){
							$sheet->cell('C'.($startRows),$warehouse->name);
						}
						// ENGINEER CODE
	                    $sheet->cell('D'.($startRows),$NA);
						// ENGINEER NAME
	                    $sheet->cell('E'.($startRows),$NA);
						if($engineer = Constructor::find($dval->eng_usage)){
							$sheet->cell('D'.($startRows),$engineer->name);
							$sheet->cell('E'.($startRows),$engineer->id_card);
						}
						// SUBCONTRACTOR CODE
	                    $sheet->cell('F'.($startRows),$NA);
						// SUBCONTRACTOR NAME
	                    $sheet->cell('G'.($startRows),$NA);
						if($subcontractor = Constructor::find($dval->sub_usage)){
							$sheet->cell('F'.($startRows),$subcontractor->name);
							$sheet->cell('G'.($startRows),$subcontractor->id_card);
						}
						
						// STREET
	                    $sheet->cell('J'.($startRows),$NA);
						if($street = SystemData::find($dval->street_id)){
							$sheet->cell('J'.($startRows),$street->name);
						}

						// ZONE
	                    $sheet->cell('H'.($startRows),$NA);
						// BLOCK
	                    $sheet->cell('I'.($startRows),$NA);
						// HOUSE TYPE
	                    $sheet->cell('K'.($startRows),$NA);
						// HOUSE
	                    $sheet->cell('L'.($startRows),$NA);
						if($house = House::find($dval->house_id)){
							if(getSetting()->allow_zone == 1 && $zone = SystemData::find($house->zone_id)){
								$sheet->cell('H'.($startRows),$zone->name);
							}
							if(getSetting()->allow_block == 1 && $block = SystemData::find($house->block_id)){
								$sheet->cell('I'.($startRows),$block->name);
							}
							if($houseType = SystemData::find($house->house_type)){
								$sheet->cell('K'.($startRows),$houseType->name);
							}
							$sheet->cell('L'.($startRows),$house->house_no);
						}
						// ITEM TYPE
	                    $sheet->cell('M'.($startRows),$NA);
						// ITEM CODE
						$sheet->cell('N'.($startRows),$NA);
						// ITEM NAME
						$sheet->cell('O'.($startRows),$NA);
						if($item = Item::find($dval->item_id)){
							if($itemType = SystemData::find($item->cat_id)){
								$sheet->cell('M'.($startRows),$itemType->name);
							}
							$sheet->cell('N'.($startRows),$item->code);
							$sheet->cell('O'.($startRows),$item->name);
						}
						// QTY
						$sheet->cell('P'.($startRows),$dval->qty);
						// UNIT
						$sheet->cell('Q'.($startRows),$dval->unit);
						if($unit = Unit::where(['from_code' => $dval->unit, 'to_code' => $dval->unit_usage])->first()){
							if($unit->factor < 1){
								$sheet->cell('P'.($startRows),$dval->qty / $unit->factor);
							}else{
								$sheet->cell('P'.($startRows),$dval->qty * $unit->factor);
							}
							$sheet->cell('Q'.($startRows),$unit->to_desc);
						}
						// ASSET VALUE
						$sheet->cell('R'.($startRows),$dval->amount);

	                    $sheet->setColumnFormat([
							('P'.$startRows)=>'0',
							('R'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)'
						]);

	                    $sheet->cell(('A'.$startRows.':R'.$startRows),function($cells){
	                        $cells->setFontFamily('Khmer OS Battambang');
	                        $cells->setFontSize(11);
	                        $cells->setAlignment('center');
	                    });
                	}
                });
            })->download('xlsx');
    	}
    }

	// Compare BOQ with Usage
	public function reportCompareBOQWithUsage(Request $request)
	{
		$data = [
			'title'       => trans('lang.report'),
			'icon'        => 'fa fa-file-excel-o',
			'small_title' => trans('lang.report_usage_with_boq'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'usage'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.usage'),
				],
				'usage_with_boq'	=> [
						'caption' 	=> trans('lang.report_usage_with_boq'),
				],
			],
		];
		return view('reports.usage.usage_with_boq', $data);
	}

	public function printCompareBOQWithUsage(Request $request){
		
		try{
			$projectID = $request->session()->get('project');
			$report  = UsageDetails::leftJoin('usages','usages.id','usage_details.use_id');

			if(!empty($request->query('from_date')) && !empty($request->query('to_date'))){
				$from 	= $request->query('from_date');
				$to   	= $request->query('to_date');
				// $report = $report->whereBetween('trans_date',[$from,$to]);
			}

			if(!empty($request->query('warehouse'))){
				$warehouse = $request->query('warehouse');
				$report = $report->where('warehouse_id', $warehouse);
			}

			if(!empty($request->query('engineer'))){
				$engineer = $request->query('engineer');
				$report = $report->where('eng_usage', $engineer);
			}

			if(!empty($request->query('subcontractor'))){
				$subcontractor = $request->query('subcontractor');
				$report = $report->where('sub_usage', $subcontractor);
			}

			if(!empty($request->query('house_type'))){
				if(!empty($request->query('house'))){
					$house  = $request->query('house');
					$report = $report->where('house_id', $house);
				}else{
					$house_type  = $request->query('house_type');
					if($houseType = SystemData::where(['id'=> $house_type, 'type'=> 'HT'])->first()){
						$houses = House::where(['house_type' => $houseType->id])->pluck('id');
						$report = $report->whereIn('house_id', $houses->toArray());
					}
				}
			}else{
				if(!empty($request->query('house'))){
					$house  = $request->query('house');
					$report = $report->where('house_id', $house);
				}
			}

			if(!empty($request->query('product_type'))){
				if(!empty($request->query('product'))){
					$product  = $request->query('product');
					$report = $report->where('item_id', $product);
				}else{
					$product_type  = $request->query('product_type');
					if($category = SystemData::where(['id'=> $product_type, 'type'=> 'IT'])->first()){
						$items = Item::where(['cat_id' => $category->id])->pluck('id');
						$report = $report->whereIn('item_id', $items->toArray());
					}
				}
			}else{
				if(!empty($request->query('product'))){
					$product = $request->query('product');
					$report = $report->where('item_id', $product);
				}
			}

			$report = $report->get();
			$data = [
				'title' 	=> trans('lang.report_usage_with_boq'),
				'setting' 	=> Setting::first(),
				'report' 	=> $report,
				'projectID' => $projectID,
			];
		}catch(\Exception $ex){
			$data = [
				'title' 	=> trans('lang.report_usage_with_boq'),
				'setting' 	=> Setting::first(),
				'report'	=> false,
				'projectID' => $projectID,
			];
		}

		return view('reports.usage.print_usage_with_boq')->with($data);
	}

	public function generateCompareBOQWithUsage(Request $request)
	{
		
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$version = $request->query("version");
		$from_date = $request->query("from_date");
		$to_date = $request->query("to_date");
		$search  = $request->search['value'];


    	$report  = UsageDetails::leftJoin('usages','usages.id','usage_details.use_id');
		// print_r($report->limit(9000)->get());exit;
		if(!empty($request->query('from_date')) && !empty($request->query('to_date'))){
			$from 	= $request->query('from_date');
			$to   	= $request->query('to_date');
			// $report = $report->whereBetween('trans_date',[$from,$to]);
		}

		if(!empty($request->query('warehouse'))){
			$warehouse = $request->query('warehouse');
			$report = $report->where('warehouse_id', $warehouse);
		}

		if(!empty($request->query('engineer'))){
			$engineer = $request->query('engineer');
			$report = $report->where('eng_usage', $engineer);
		}

		if(!empty($request->query('subcontractor'))){
			$subcontractor = $request->query('subcontractor');
			$report = $report->where('sub_usage', $subcontractor);
		}

		if(!empty($request->query('house_type'))){
			if(!empty($request->query('house'))){
				$house  = $request->query('house');
				$report = $report->where('house_id', $house);
			}else{
				$house_type  = $request->query('house_type');
				if($houseType = SystemData::where(['id'=> $house_type, 'type'=> 'HT'])->first()){
					$houses = House::where(['house_type' => $houseType->id])->pluck('id');
					$report = $report->whereIn('house_id', $houses->toArray());
				}
			}
		}else{
			if(!empty($request->query('house'))){
				$house  = $request->query('house');
				$report = $report->where('house_id', $house);
			}
		}

		if(!empty($request->query('product_type'))){
			if(!empty($request->query('product'))){
				$product  = $request->query('product');
				$report = $report->where('item_id', $product);
			}else{
				$product_type  = $request->query('product_type');
				if($category = SystemData::where(['id'=> $product_type, 'type'=> 'IT'])->first()){
					$items = Item::where(['cat_id' => $category->id])->pluck('id');
					$report = $report->whereIn('item_id', $items->toArray());
				}
			}
		}else{
			if(!empty($request->query('product'))){
				$product = $request->query('product');
				$report = $report->where('item_id', $product);
			}
		}

		$report = $report->get();
		
		
    	if ($version=="datatables") {
    		$response = Datatables::of($report)
			->addColumn('warehouse',function($row){
				if($warehouse = Warehouse::select(['id','name'])->where(['id' => $row->warehouse_id])->first()){
					return $warehouse->name;
				}
				return null;
			})
			->addColumn('engineer',function($row){
				if($engineer = Constructor::select(['id','id_card','name'])->where(['id' => $row->eng_usage])->first()){
					return $engineer->id_card . ' - ' . $engineer->name;
				}
				return null;
			})
			->addColumn('subcontractor',function($row){
				if($subcontractor = Constructor::select(['id','id_card','name'])->where(['id' => $row->sub_usage])->first()){
					return $subcontractor->id_card . ' - ' . $subcontractor->name;
				}
				return null;
			})
			->addColumn('house_type',function($row){
				if($house = House::select(['id','house_type'])->where(['id'=> $row->house_id])->first()){
					if($houseType = SystemData::where(['id' => $house->house_type])->first()){
						return $houseType->name;
					}
				}
				return null;
			})
			->addColumn('house_no',function($row){
				if($house = House::select(['id','house_no'])->where(['id' => $row->house_id])->first()){
					return $house->house_no;
				}
				return null;
			})
			->addColumn('item_type',function($row){
				if($item = Item::select(['id','cat_id'])->where(['id' => $row->item_id])->first()){
					if($category = SystemData::select(['id','name'])->where(['id' => $item->cat_id])->first()){
						return $category->name;
					}
				}
				return null;
			})
			->addColumn('item_code',function($row){
				if($item = Item::select(['id','code'])->where(['id' => $row->item_id])->first()){
					return $item->code;
				}
				return null;
			})
			->addColumn('item_name',function($row){
				if($item = Item::select(['id','name'])->where(['id' => $row->item_id])->first()){
					return $item->name;
				}
				return null;
			})
			->addColumn('boq_unit',function($row){
				if($boqItem = BoqItem::join('boqs','boqs.id','boq_items.boq_id')->where('boqs.status',1)->where('boqs.is_revise',0)->where(['boq_items.house_id' => $row->house_id,'boq_items.item_id' => $row->item_id])->first()){
					return $boqItem->unit;
				}
				return null;
			})
			->addColumn('boq_std',function($row){
				if($boqItem = BoqItem::join('boqs','boqs.id','boq_items.boq_id')->where('boqs.status',1)->where('boqs.is_revise',0)->where(['boq_items.house_id' => $row->house_id,'boq_items.item_id' => $row->item_id])->first()){
					return $boqItem->qty_std;
				}
				return null;
			})
			->addColumn('boq_add',function($row){
				if($boqItem = BoqItem::join('boqs','boqs.id','boq_items.boq_id')->where('boqs.status',1)->where('boqs.is_revise',0)->where(['boq_items.house_id' => $row->house_id,'boq_items.item_id' => $row->item_id])->first()){
					return $boqItem->qty_add;
				}
				return null;
			})
			->make(true);
    	}elseif($version=="print"){
    		$response = Datatables::of($report)
			->addColumn('warehouse',function($row){
				if($warehouse = Warehouse::select(['id','name'])->where(['id' => $row->warehouse_id])->first()){
					return $warehouse->name;
				}
				return null;
			})
			->addColumn('engineer',function($row){
				if($engineer = Constructor::select(['id','id_card'])->where(['id' => $row->eng_usage])->first()){
					return $engineer->id_card;
				}
				return null;
			})
			->addColumn('subcontractor',function($row){
				if($subcontractor = Constructor::select(['id','id_card'])->where(['id' => $row->sub_usage])->first()){
					return $subcontractor->id_card;
				}
				return null;
			})
			->addColumn('house_type',function($row){
				if($house = House::select(['id','house_type'])->where(['id'=> $row->house_id])->first()){
					if($houseType = SystemData::where(['id' => $house->house_type])->first()){
						return $houseType->name;
					}
				}
				return null;
			})
			->addColumn('house_no',function($row){
				if($house = House::select(['id','house_no'])->where(['id' => $row->house_id])->first()){
					return $house->house_no;
				}
				return null;
			})
			->addColumn('item_type',function($row){
				if($item = Item::select(['id','cat_id'])->where(['id' => $row->item_id])->first()){
					if($category = SystemData::select(['id','name'])->where(['id' => $item->cat_id])->first()){
						return $category->name;
					}
				}
				return null;
			})
			->addColumn('item_code',function($row){
				if($item = Item::select(['id','code'])->where(['id' => $row->item_id])->first()){
					return $item->code;
				}
				return null;
			})
			->addColumn('item_name',function($row){
				if($item = Item::select(['id','name'])->where(['id' => $row->item_id])->first()){
					return $item->name;
				}
				return null;
			})
			->addColumn('boq_unit',function($row){
				if($boqItem = BoqItem::join('boqs','boqs.id','boq_items.boq_id')->where('boqs.status',1)->where('boqs.is_revise',0)->where(['boq_items.house_id' => $row->house_id,'boq_items.item_id' => $row->item_id])->first()){
					return $boqItem->unit;
				}
				return null;
			})
			->addColumn('boq_std',function($row){
				if($boqItem = BoqItem::join('boqs','boqs.id','boq_items.boq_id')->where('boqs.status',1)->where('boqs.is_revise',0)->where(['boq_items.house_id' => $row->house_id,'boq_items.item_id' => $row->item_id])->first()){
					return $boqItem->qty_std;
				}
				return null;
			})
			->addColumn('boq_add',function($row){
				if($boqItem = BoqItem::join('boqs','boqs.id','boq_items.boq_id')->where('boqs.status',1)->where('boqs.is_revise',0)->where(['boq_items.house_id' => $row->house_id,'boq_items.item_id' => $row->item_id])->first()){
					return $boqItem->qty_add;
				}
				return null;
			})
			->make(true);
    	}elseif($version=="excel"){
			$date = date("Y-m-d H:i:s");
    		Excel::create("Usage with BOQ({$date})",function($excel) use($report,$from_date,$to_date){
					$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
					$excel->sheet('Usage with BOQ',function($sheet) use($report,$from_date,$to_date){
					$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
					$objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
					$objDrawing->setCoordinates('A1');
					$objDrawing->setHeight(150);
					$objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':M'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':M'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(28);
						$cells->setFontColor(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':M'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':M'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(28);
						$cells->setFontColor(getSetting()->company_name_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':M'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report_usage_with_boq'));
					$sheet->cell(('C'.$startRows.':M'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(16);
						$cells->setFontColor(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($from_date)))." ".trans("lang.to")." : ".(date('d/m/Y',strtotime($to_date))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(11);
						$cells->setAlignment('left');
					});

					$startRows++;
					$sheet->cell(('A'.$startRows),trans('lang.trans_date'));
					$sheet->cell(('B'.$startRows),trans('lang.reference_no'));
					$sheet->cell(('C'.$startRows),trans('lang.reference'));
					$sheet->cell(('D'.$startRows),trans('lang.engineer_name'));
					$sheet->cell(('E'.$startRows),trans('lang.engineer_code'));
					$sheet->cell(('F'.$startRows),trans('lang.contractor_name'));
					$sheet->cell(('G'.$startRows),trans('lang.contractor_code'));
					$sheet->cell(('H'.$startRows),trans('lang.warehouse'));
					$sheet->cell(('I'.$startRows),trans('lang.street'));
					$sheet->cell(('J'.$startRows),trans('lang.house_type'));
					$sheet->cell(('K'.$startRows),trans('lang.house'));
					$sheet->cell(('L'.$startRows),trans('lang.item_type'));
					$sheet->cell(('M'.$startRows),trans('lang.item_code'));
					$sheet->cell(('N'.$startRows),trans('lang.item_name'));
					$sheet->cell(('O'.$startRows),trans('lang.boq_std'));
					$sheet->cell(('P'.$startRows),trans('lang.boq_add'));
					$sheet->cell(('Q'.$startRows),trans('lang.boq_unit'));
					$sheet->cell(('R'.$startRows),trans('lang.qty'));
					$sheet->cell(('S'.$startRows),trans('lang.units'));
					$sheet->cell(('T'.$startRows),trans('lang.cost'));

					$sheet->cell(('A'.$startRows.':T'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(11);
						$cells->setFontColor('#ffffff');
						$cells->setBackground(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});

					$total_cost = 0;
					foreach ($report as  $dval) {
						$startRows++;
						$total_cost += floatval($dval->total_cost);
						$sheet->cell('A'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
						$sheet->cell('B'.($startRows),$dval->ref_no);
						$sheet->cell('C'.($startRows),$dval->reference);

						if($engineer = Constructor::where(['id'=> $dval->eng_usage])->first()){
							$sheet->cell('D'.($startRows),$engineer->id_card);
							$sheet->cell('E'.($startRows),$engineer->name);
						}else{
							$sheet->cell('D'.($startRows),"N/A");
							$sheet->cell('E'.($startRows),"N/A");
						}

						if($subcontractor = Constructor::where(['id'=> $dval->sub_usage])->first()){
							$sheet->cell('F'.($startRows),$subcontractor->id_card);
							$sheet->cell('G'.($startRows),$subcontractor->name);
						}else{
							$sheet->cell('F'.($startRows),"N/A");
							$sheet->cell('G'.($startRows),"N/A");
						}

						if($warehouse = Warehouse::where(['id'=> $dval->warehouse_id])->first()){
							$sheet->cell('H'.($startRows),$warehouse->name);
						}else{
							$sheet->cell('H'.($startRows),"N/A");
						}

						if($house = House::where(['id'=> $dval->house_id])->first()){
							$sheet->cell(('K'.$startRows),$house->house_no);

							// House Type
							if($houseType = SystemData::where(['id'=> $house->house_type])->first()){
								$sheet->cell('J'.($startRows),$houseType->name);
							}else{
								$sheet->cell('J'.($startRows),"N/A");
							}

							// Street
							if($street = SystemData::where(['id'=> $house->street_id])->first()){
								$sheet->cell('I'.($startRows),$street->name);
							}else{
								$sheet->cell('I'.($startRows),"N/A");
							}
						}else{
							$sheet->cell('I'.($startRows),"N/A");
							$sheet->cell('K'.($startRows),"N/A");
							$sheet->cell('H'.($startRows),"N/A");
						}

						if($item = Item::where(['id'=> $dval->item_id])->first()){
							$sheet->cell('M'.($startRows),$item->code);
							$sheet->cell('N'.($startRows),$item->name);
							if($itemType = SystemData::where(['id'=> $item->cat_id, 'type'=> 'IT'])->first()){
								$sheet->cell('L'.($startRows),$itemType->name);
							}
						}else{
							$sheet->cell('L'.($startRows),"N/A");
							$sheet->cell('M'.($startRows),"N/A");
							$sheet->cell('N'.($startRows),"N/A");
						}

						if($boqItem = BoqItem::join('boqs','boqs.id','boq_items.boq_id')->where('boqs.status',1)->where('boqs.is_revise',0)->where(['boq_items.house_id'=> $dval->house_id, 'boq_items.item_id' => $dval->item_id])->first()){
							$sheet->cell('O'.($startRows),$boqItem->qty_std);
							$sheet->cell('P'.($startRows),$boqItem->qty_add);
							$sheet->cell('Q'.($startRows),$boqItem->unit);
						}else{
							$sheet->cell('O'.($startRows),0);
							$sheet->cell('P'.($startRows),0);
							$sheet->cell('Q'.($startRows),"N/A");
						}
						
						$sheet->cell('R'.($startRows),$dval->qty);
						$sheet->cell('S'.($startRows),$dval->unit);
						$sheet->cell('T'.($startRows),$dval->total_cost);

						$sheet->setColumnFormat([
							('O'.$startRows)=>'0.00',
							('P'.$startRows)=>'0.00',
							('R'.$startRows)=>'0.00',
							('T'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)'
						]);

						$sheet->cell(('A'.$startRows.':T'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
							$cells->setFontSize(11);
							$cells->setAlignment('center');
						});
					}

					$startRows++;
					$sheet->cell('A'.($startRows),'');
					$sheet->cell('B'.($startRows),'');
					$sheet->cell('C'.($startRows),'');
					$sheet->cell('D'.($startRows),'');
					$sheet->cell('E'.($startRows),'');
					$sheet->cell('F'.($startRows),'');
					$sheet->cell('G'.($startRows),'');
					$sheet->cell('H'.($startRows),'');
					$sheet->cell('I'.($startRows),'');
					$sheet->cell('J'.($startRows),'');
					$sheet->cell('K'.($startRows),'');
					$sheet->cell('L'.($startRows),'');
					$sheet->cell('M'.($startRows),'');
					$sheet->cell('N'.($startRows),'');
					$sheet->cell('O'.($startRows),'');
					$sheet->cell('P'.($startRows),'');
					$sheet->cell('Q'.($startRows),'');
					$sheet->cell('R'.($startRows),'');
					$sheet->cell('S'.($startRows),trans('lang.total'));
					$sheet->cell('T'.($startRows),$total_cost);
					$sheet->setColumnFormat([('T'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)']);
					$sheet->cell(('A'.$startRows.':T'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(11);
						$cells->setAlignment('center');
					});
				});
			})->download('xlsx');
    	}

    	return $response;
	}

	public function reportRemainingBOQ(Request $request){
		$data = [
			'title'       => trans('lang.report'),
			'icon'        => 'fa fa-file-excel-o',
			'small_title' => trans('lang.report_remaining_boq'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'boq'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.boq'),
				],
				'remaining_boq'	=> [
						'caption' 	=> trans('lang.report_remaining_boq'),
				],
			],
		];
		return view('reports.boq.report_remaining_boq', $data);
	}

	public function printRemainingBOQ(Request $request){

		try{
			ini_set('max_execution_time', 0);
			$projectID 	= $request->session()->get('project');
			$version 	= $request->query("version");
			$endDate 	= $request->query("end_date");
			$zoneID 	= $request->query("zone");
			$blockID 	= $request->query("block");
			$streetID 	= $request->query("street");
			$houseType 	= $request->query("house_type");
			$houseID 	= $request->query("house");
			$itemType 	= $request->query("product_type");
			$itemID 	= $request->query("product");

			$report  = BoqItem::leftJoin('boqs','boqs.id','boq_items.boq_id');

			if($zoneID){
				if($houseIds = House::where('zone_id',$zoneID)->pluck('id')){
					$report = $report->whereIn('boq_items.house_id',$houseIds);
				}
			}

			if($blockID){
				if($houseIds = House::where('block_id',$blockID)->pluck('id')){
					$report = $report->whereIn('boq_items.house_id',$houseIds);
				}
			}

			if($streetID){
				if($houseIds = House::where('street_id',$streetID)->pluck('id')){
					$report = $report->whereIn('boq_items.house_id',$houseIds);
				}
			}

			if($houseType){
				if($houseIds = House::where('house_type',$houseType)->pluck('id')){
					$report = $report->whereIn('boq_items.house_id',$houseIds);
				}
			}

			if($houseID){
				$report = $report->where('boq_items.house_id',$houseID);
			}

			if($itemType){
				if($itemIds = Item::where('cat_id',$itemType)->pluck('id')){
					$report = $report->whereIn('boq_items.item_id',$itemIds);
				}
			}

			if($itemID){
				$report = $report->where('boq_items.item_id',$itemID);
			}

			$report = $report->where('boqs.is_revise',0)->where('boqs_status',1)->get();
			$data = [
				'request'   => $request->all(),
				'title' 	=> trans('lang.report_remaining_boq'),
				'setting' 	=> Setting::first(),
				'report' 	=> $report,
				'projectID' => $projectID,
			];
		}catch(\Exception $ex){
			$data = [
				'request'   => $request->all(),
				'title' 	=> trans('lang.report_remaining_boq'),
				'setting' 	=> Setting::first(),
				'report'	=> false,
				'projectID' => $projectID,
			];
		}

		return view('reports.boq.report_remaining_boq_print')->with($data);
	}

	public function generateRemainingBOQ(Request $request){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$version 	= $request->query("version");
		$endDate 	= date("Y-m-d H:i:s",strtotime($request->query("end_date")));
		$zoneID 	= $request->query("zone");
		$blockID 	= $request->query("block");
		$streetID 	= $request->query("street");
		$houseType 	= $request->query("house_type");
		$houseID 	= $request->query("house");
		$itemType 	= $request->query("product_type");
		$itemID 	= $request->query("product");

		$report  = BoqItem::select('boq_items.*','boq_items.house_id as boq_house_id','boqs.trans_date')->join('boqs','boqs.id','boq_items.boq_id');

		if($zoneID){
			if($houseIds = House::where('zone_id',$zoneID)->pluck('id')){
				$report = $report->whereIn('boq_items.house_id',$houseIds);
			}
		}

		if($blockID){
			if($houseIds = House::where('block_id',$blockID)->pluck('id')){
				$report = $report->whereIn('boq_items.house_id',$houseIds);
			}
		}

		if($streetID){
			if($houseIds = House::where('street_id',$streetID)->pluck('id')){
				$report = $report->whereIn('boq_items.house_id',$houseIds);
			}
		}

		if($houseType){
			if($houseIds = House::where('house_type',$houseType)->pluck('id')){
				$report = $report->whereIn('boq_items.house_id',$houseIds);
			}
		}

		if($houseID){
			$report = $report->where('boq_items.house_id',$houseID);
		}

		if($itemType){
			if($itemIds = Item::where('cat_id',$itemType)->pluck('id')){
				$report = $report->whereIn('boq_items.item_id',$itemIds);
			}
		}

		if($itemID){
			$report = $report->where('boq_items.item_id',$itemID);
		}

		$report = $report->where('boqs.is_revise',0)->where('boqs.status',1)->get();
		// print_r($report);exit;

		if($version=="datatables"){
			$response = Datatables::of($report)
			
				->addColumn('zone',function($row){
					if(getSetting()->allow_zone == 1){
						if($house = House::where(['id'=> $row->house_id])->first()){
							if($houseType = SystemData::where(['id' => $house->zone_id])->first()){
								return $houseType->name;
							}
						}
					}
					return null;
				})

				->addColumn('block',function($row){
					if(getSetting()->allow_block == 1){
						if($house = House::where(['id'=> $row->house_id])->first()){
							if($houseType = SystemData::where(['id' => $house->block_id])->first()){
								return $houseType->name;
							}
						}
					}
					return null;
				})
				->addColumn('building',function($row){
					// if(getSetting()->allow_block == 1){
						if($house = House::where(['id'=> $row->house_id])->first()){
							if($houseType = SystemData::where(['id' => $house->building_id])->first()){
								return $houseType->name;
							}
						}
					// }
					return null;
				})

				->addColumn('street',function($row){
					if($house = House::where(['id'=> $row->house_id])->first()){
						if($houseType = SystemData::where(['id' => $house->street_id])->first()){
							return $houseType->name;
						}
					}
					return null;
				})

				->addColumn('house_type',function($row){
					if($house = House::select(['id','house_type'])->where(['id'=> $row->house_id])->first()){
						if($houseType = SystemData::where(['id' => $house->house_type])->first()){
							return $houseType->name;
						}
					}
					return null;
				})
				->addColumn('house_no',function($row){
					if($house = House::select(['id','house_no'])->where(['id' => $row->house_id])->first()){
						return $house->house_no;
					}
					return null;
				})
				->addColumn('item_type',function($row){
					if($item = Item::select(['id','cat_id'])->where(['id' => $row->item_id])->first()){
						if($category = SystemData::select(['id','name'])->where(['id' => $item->cat_id])->first()){
							return $category->name;
						}
					}
					return null;
				})
				->addColumn('item_code',function($row){
					if($item = Item::select(['id','code'])->where(['id' => $row->item_id])->first()){
						return $item->code;
					}
					return null;
				})
				->addColumn('item_name',function($row){
					if($item = Item::select(['id','name'])->where(['id' => $row->item_id])->first()){
						return $item->name;
					}
					return null;
				})
				->addColumn('usage_unit',function($row) use($endDate){

					$usageDetail = UsageDetails::select(['*']);

					if($endDate){
						$usageDetail = $usageDetail->leftJoin('usages','usages.id','usage_details.use_id')->where('usages.trans_date','<=',$endDate);
					}

					$usageDetail = $usageDetail->where([
						'usage_details.house_id' => $row->house_id,
						'usage_details.item_id'  => $row->item_id,
						'usage_details.delete'	 => 0
						])->first();

					if($usageDetail){
						return $usageDetail->unit;
					}
					return null;
				})
				->addColumn('usage_qty',function($row) use($endDate){
					$usageDetail = UsageDetails::select([DB::raw("SUM(qty) as qty")]);

					if($endDate){
						$usageDetail = $usageDetail->leftJoin('usages','usages.id','usage_details.use_id')->where('usages.trans_date','<=',$endDate);
					}

					$usageDetail = $usageDetail->where([
						'usage_details.house_id' => $row->house_id,
						'usage_details.item_id'  => $row->item_id,
						'usage_details.delete'	 => 0
						])->groupBy(['usage_details.house_id','usage_details.item_id'])->get();
					$qty = 0;
					if($usageDetail){
						foreach($usageDetail as $detail){
							$qty += (float)$detail->qty;
						}
					}
					return $qty;
				})
				->addColumn('usage_cost',function($row) use($endDate){

					$usageDetail = UsageDetails::select(['*']);

					if($endDate){
						$usageDetail = $usageDetail->leftJoin('usages','usages.id','usage_details.use_id')->where('usages.trans_date','<=',$endDate);
					}

					$usageDetail = $usageDetail->where([
						'usage_details.house_id' => $row->house_id,
						'usage_details.item_id'  => $row->item_id,
						'usage_details.delete'	 => 0
						])->first();

					if($usageDetail){
						return $usageDetail->total_cost;
					}
					return 0;
				})
				->make(true);
		}elseif($version=="excel"){
			$date = date("Y-m-d H:i:s");
    		Excel::create("Remaining BOQ Each House({$date})",function($excel) use($report,$endDate){
				$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
				$excel->sheet($endDate,function($sheet) use($report,$endDate){
					$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
					$objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
					$objDrawing->setCoordinates('A1');
					$objDrawing->setHeight(150);
					$objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':U'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':U'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(28);
						$cells->setFontColor(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':U'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':U'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(28);
						$cells->setFontColor(getSetting()->company_name_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':U'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report_remaining_boq'));
					$sheet->cell(('C'.$startRows.':U'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(16);
						$cells->setFontColor(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($endDate))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(11);
						$cells->setAlignment('left');
					});

					$startRows++;
					$sheet->cell(('A'.$startRows),trans('lang.trans_date'));
					$sheet->cell(('B'.$startRows),trans('lang.reference_no'));
					$sheet->cell(('C'.$startRows),trans('lang.reference'));
					$sheet->cell(('D'.$startRows),trans('lang.engineer_name'));
					$sheet->cell(('E'.$startRows),trans('lang.engineer_code'));
					$sheet->cell(('F'.$startRows),trans('lang.contractor_name'));
					$sheet->cell(('G'.$startRows),trans('lang.contractor_code'));
					$sheet->cell(('H'.$startRows),trans('lang.warehouse'));
					$sheet->cell(('I'.$startRows),trans('lang.zone'));
					$sheet->cell(('J'.$startRows),trans('lang.block'));
					$sheet->cell(('K'.$startRows),trans('lang.street'));
					$sheet->cell(('L'.$startRows),trans('lang.house_type'));
					$sheet->cell(('M'.$startRows),trans('lang.house'));
					$sheet->cell(('N'.$startRows),trans('lang.item_type'));
					$sheet->cell(('O'.$startRows),trans('lang.item_code'));
					$sheet->cell(('P'.$startRows),trans('lang.item_name'));
					$sheet->cell(('Q'.$startRows),trans('lang.boq_std'));
					$sheet->cell(('R'.$startRows),trans('lang.boq_add'));
					$sheet->cell(('S'.$startRows),trans('lang.boq_unit'));
					$sheet->cell(('T'.$startRows),trans('lang.qty'));
					$sheet->cell(('U'.$startRows),trans('lang.units'));
					$sheet->cell(('V'.$startRows),trans('lang.cost'));
					$sheet->cell(('W'.$startRows),trans('lang.note'));

					$sheet->cell(('A'.$startRows.':W'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(11);
						$cells->setFontColor('#ffffff');
						$cells->setBackground(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});

					$total_cost = 0;
					foreach ($report as  $dval) {
						$startRows++;
						$sheet->cell('A'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
						
						if($house = House::where(['id'=> $dval->house_id])->first()){
							$sheet->cell(('M'.$startRows),$house->house_no);

							// ZONE
							if($zone = SystemData::where(['id'=> $house->zone_id])->first()){
								if(getSetting()->allow_zone == 1){
									$sheet->cell('I'.($startRows),$zone->name);
								}else{
									$sheet->cell('I'.($startRows),"N/A");
								}
							}else{
								$sheet->cell('I'.($startRows),"N/A");
							}

							// Block
							if($block = SystemData::where(['id'=> $house->block_id])->first()){
								if(getSetting()->allow_block == 1){
									$sheet->cell('J'.($startRows),$block->name);
								}else{
									$sheet->cell('J'.($startRows),"N/A");
								}
							}else{
								$sheet->cell('J'.($startRows),"N/A");
							}

							// House Type
							if($houseType = SystemData::where(['id'=> $house->house_type])->first()){
								$sheet->cell('L'.($startRows),$houseType->name);
							}else{
								$sheet->cell('L'.($startRows),"N/A");
							}

							// Street
							if($street = SystemData::where(['id'=> $house->street_id])->first()){
								$sheet->cell('K'.($startRows),$street->name);
							}else{
								$sheet->cell('K'.($startRows),"N/A");
							}
						}else{
							$sheet->cell('I'.($startRows),"N/A");
							$sheet->cell('J'.($startRows),"N/A");
							$sheet->cell('K'.($startRows),"N/A");
							$sheet->cell('L'.($startRows),"N/A");
							$sheet->cell('M'.($startRows),"N/A");
						}

						if($item = Item::where(['id'=> $dval->item_id])->first()){
							$sheet->cell('O'.($startRows),$item->code);
							$sheet->cell('P'.($startRows),$item->name);
							if($itemType = SystemData::where(['id'=> $item->cat_id, 'type'=> 'IT'])->first()){
								$sheet->cell('N'.($startRows),$itemType->name);
							}
						}else{
							$sheet->cell('O'.($startRows),"N/A");
							$sheet->cell('P'.($startRows),"N/A");
							$sheet->cell('N'.($startRows),"N/A");
						}

						$sheet->cell('Q'.($startRows),$dval->qty_std);
						$sheet->cell('R'.($startRows),$dval->qty_add);
						$sheet->cell('S'.($startRows),$dval->unit);

						if($usageDetails = UsageDetails::where(['house_id'=> $dval->house_id, 'item_id' => $dval->item_id, 'delete' => 0])->first()){
							$total_cost += floatval($usageDetails->total_cost);
							if($usage = Usage::find($usageDetails->use_id)){
								$sheet->cell('B'.($startRows),$usage->ref_no);
								$sheet->cell('C'.($startRows),$usage->reference);

								if($engineer = Constructor::where(['id'=> $usage->eng_usage])->first()){
									$sheet->cell('D'.($startRows),$engineer->id_card);
									$sheet->cell('E'.($startRows),$engineer->name);
								}else{
									$sheet->cell('D'.($startRows),"N/A");
									$sheet->cell('E'.($startRows),"N/A");
								}

								if($subcontractor = Constructor::where(['id'=> $usage->sub_usage])->first()){
									$sheet->cell('F'.($startRows),$subcontractor->id_card);
									$sheet->cell('G'.($startRows),$subcontractor->name);
								}else{
									$sheet->cell('F'.($startRows),"N/A");
									$sheet->cell('G'.($startRows),"N/A");
								}
							}

							if($warehouse = Warehouse::find($usageDetails->warehouse_id)){
								$sheet->cell('H'.($startRows),$warehouse->name);
							}else{
								$sheet->cell('H'.($startRows),"N/A");
							}
							
							$sheet->cell('T'.($startRows),$usageDetails->qty);
							$sheet->cell('U'.($startRows),$usageDetails->unit);
							$sheet->cell('V'.($startRows),$usageDetails->total_cost);
							$sheet->cell('W'.($startRows),$usageDetails->note);
						}else{
							$sheet->cell('B'.($startRows),"N/A");
							$sheet->cell('C'.($startRows),"N/A");
							$sheet->cell('D'.($startRows),"N/A");
							$sheet->cell('E'.($startRows),"N/A");
							$sheet->cell('F'.($startRows),"N/A");
							$sheet->cell('G'.($startRows),"N/A");
							$sheet->cell('T'.($startRows),0);
							$sheet->cell('U'.($startRows),"N/A");
							$sheet->cell('V'.($startRows),0);
							$sheet->cell('W'.($startRows),'');
						}

						$sheet->setColumnFormat([
							('Q'.$startRows)=>'0.00',
							('R'.$startRows)=>'0.00',
							('T'.$startRows)=>'0.00',
							('V'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)'
						]);

						$sheet->cell(('A'.$startRows.':W'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
							$cells->setFontSize(11);
							$cells->setAlignment('center');
						});
					}

					$startRows++;
					$sheet->cell('A'.($startRows),'');
					$sheet->cell('B'.($startRows),'');
					$sheet->cell('C'.($startRows),'');
					$sheet->cell('D'.($startRows),'');
					$sheet->cell('E'.($startRows),'');
					$sheet->cell('F'.($startRows),'');
					$sheet->cell('G'.($startRows),'');
					$sheet->cell('H'.($startRows),'');
					$sheet->cell('I'.($startRows),'');
					$sheet->cell('J'.($startRows),'');
					$sheet->cell('K'.($startRows),'');
					$sheet->cell('L'.($startRows),'');
					$sheet->cell('M'.($startRows),'');
					$sheet->cell('N'.($startRows),'');
					$sheet->cell('O'.($startRows),'');
					$sheet->cell('P'.($startRows),'');
					$sheet->cell('Q'.($startRows),'');
					$sheet->cell('R'.($startRows),'');
					$sheet->cell('S'.($startRows),'');
					$sheet->cell('T'.($startRows),'');
					$sheet->cell('U'.($startRows),trans('lang.total'));
					$sheet->cell('V'.($startRows),$total_cost);
					$sheet->cell('W'.($startRows),'');
					$sheet->setColumnFormat([('V'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)']);
					$sheet->cell(('U'.$startRows.':V'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(11);
						$cells->setFontColor('#ffffff');
						$cells->setBackground(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});
				});
			})->download('xlsx');
		}

		return $response;
	}

	public function reportRemainingBOQTotal(Request $request){
		$data = [
			'title'       => trans('lang.report'),
			'icon'        => 'fa fa-file-excel-o',
			'small_title' => trans('lang.report_remaining_boq_total'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'boq'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.boq'),
				],
				'remaining_boq'	=> [
						'caption' 	=> trans('lang.report_remaining_boq_total'),
				],
			],
		];
		return view('reports.boq.report_remaining_boq_total', $data);
	}

	public function generateRemainingBOQTotal(Request $request){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$version 	= $request->query("version");
		$endDate 	= $request->query("end_date");
		$itemType 	= $request->query("product_type");
		$itemID 	= $request->query("product");

		$columns = [
			'boq_items.id',
			'boq_items.boq_id',
			'boq_items.house_id',
			'boq_items.item_id',
			'boq_items.unit',
			'items.code',
			'items.name',
			'items.cat_id',
			'items.unit_stock',
			'items.unit_purch',
			'items.unit_usage',
			'items.cost_purch',
			'boqs.trans_date',
		];
		$report  = BoqItem::select($columns)
				->join('boqs','boqs.id','boq_items.boq_id')
				->leftJoin('items','items.id','boq_items.item_id');

		if($endDate){
			$report = $report->where('boqs.trans_date','<=',$endDate);
		}

		if($itemType){
			$report = $report->where('items.cat_id',$itemType);
		}

		if($itemID){
			$report = $report->where('boq_items.item_id',$itemID);
		}

		$report = $report->where('boqs.is_revise',0)->where('boqs.status',1)->groupBy(['boq_items.item_id'])->get();

		if($version=="datatables"){
			$response = Datatables::of($report)
				->addColumn('qty_std',function($row){
					$columns  = [
						'boq_items.*',
						'units.factor',
						// DB::raw("(CASE WHEN (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '{$row->unit_stock}' AND pr_units.`to_code` = pr_boq_items.unit)!='' THEN (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '{$row->unit_stock}' AND pr_units.`to_code` = pr_boq_items.unit) ELSE 1 END) as factor")
					];
					$boqItems = BoqItem::select($columns)
							->join('boqs','boqs.id','boq_items.boq_id')
							->leftJoin('units',function($join) use($row){
								$join->on('units.from_code','boq_items.unit')
									 ->where('units.to_code',$row->unit_purch);
							})
							->where('item_id',$row->item_id)
							->where('boqs.is_revise',0)
							->where('boqs.status',1)
							->get();
					$qtyStd = 0;
					if(!empty($boqItems) && count($boqItems) > 0){
						foreach($boqItems as $boqItem){
							$factor = 1;
							if(!empty($boqItem->factor)){
								$factor = (float)$boqItem->factor;
							}

							$qtyStd += (float)$boqItem->qty_std * $factor;
						}
					}
					return $qtyStd;
				})
				->addColumn('qty_add',function($row){
					$columns  = [
						'boq_items.*',
						'units.factor',
						// DB::raw("(CASE WHEN (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '{$row->unit_stock}' AND pr_units.`to_code` = pr_boq_items.unit)!='' THEN (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '{$row->unit_stock}' AND pr_units.`to_code` = pr_boq_items.unit) ELSE 1 END) as factor")
					];
					$boqItems = BoqItem::select($columns)
							->join('boqs','boqs.id','boq_items.boq_id')
							->leftJoin('units',function($join) use($row){
								$join->on('units.from_code','boq_items.unit')
									 ->where('units.to_code',$row->unit_purch);
							})
							->where('item_id',$row->item_id)
							->where('boqs.is_revise',0)
							->where('boqs.status',1)
							->get();
					$qtyAdd = 0;
					if(!empty($boqItems) && count($boqItems) > 0){
						foreach($boqItems as $boqItem){
							$factor = 1;
							if(!empty($boqItem->factor)){
								$factor = (float)$boqItem->factor;
							}

							$qtyAdd += (float)$boqItem->qty_add * $factor;
						}
					}
					return $qtyAdd;
				})
				->addColumn('qty_request',function($row){
					$columns  = [
						'request_items.*',
						'units.factor',
					];
					$requestItems = RequestItem::select($columns)
							->leftJoin('units',function($join) use($row){
								$join->on('units.from_code','request_items.unit')
									 ->where('units.to_code',$row->unit_purch);
							})
							->where('item_id',$row->item_id)
							->get();
					$qtyRequest = 0;
					if(!empty($requestItems) && count($requestItems) > 0){
						foreach($requestItems as $requestItem){
							$factor = 1;
							if(!empty($requestItem->factor)){
								$factor = (float)$requestItem->factor;
							}

							$qtyRequest += (float)$requestItem->qty * $factor;
						}
					}
					return $qtyRequest;
				})
				->addColumn('item_type',function($row){
					if($itemType = SystemData::find($row->cat_id)){
						return $itemType->name;
					}
					return '';
				})
				->make(true);
		}elseif($version=="excel"){
			$date = date("Y-m-d H:i:s");
    		Excel::create("Remaining BOQ Total({$date})",function($excel) use($report,$endDate){
				$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
				$excel->sheet($endDate,function($sheet) use($report,$endDate){
					$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
					$objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
					$objDrawing->setCoordinates('A1');
					$objDrawing->setHeight(150);
					$objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':H'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':H'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(28);
						$cells->setFontColor(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':H'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':H'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(28);
						$cells->setFontColor(getSetting()->company_name_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':H'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.report_remaining_boq_total'));
					$sheet->cell(('C'.$startRows.':H'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(16);
						$cells->setFontColor(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':D'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($endDate))));
					$sheet->cell(('A'.$startRows.':D'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(11);
						$cells->setAlignment('left');
					});

					$startRows++;
					$sheet->cell(('A'.$startRows),trans('lang.trans_date'));
					$sheet->cell(('B'.$startRows),trans('lang.item_type'));
					$sheet->cell(('C'.$startRows),trans('lang.item_code'));
					$sheet->cell(('D'.$startRows),trans('lang.item_name'));
					$sheet->cell(('E'.$startRows),trans('lang.boq_std'));
					$sheet->cell(('F'.$startRows),trans('lang.boq_add'));
					$sheet->cell(('G'.$startRows),trans('lang.request_qty'));
					$sheet->cell(('H'.$startRows),trans('lang.unit'));
					
					$sheet->cell(('A'.$startRows.':H'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(11);
						$cells->setFontColor('#ffffff');
						$cells->setBackground(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});

					$total_cost = 0;
					foreach ($report as  $dval) {
						$startRows++;
						$sheet->cell('A'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
						
						if($item = Item::where(['id'=> $dval->item_id])->first()){
							$sheet->cell('C'.($startRows),$item->code);
							$sheet->cell('D'.($startRows),$item->name);
							if($itemType = SystemData::where(['id'=> $item->cat_id, 'type'=> 'IT'])->first()){
								$sheet->cell('B'.($startRows),$itemType->name);
							}
						}else{
							$sheet->cell('B'.($startRows),"N/A");
							$sheet->cell('C'.($startRows),"N/A");
							$sheet->cell('D'.($startRows),"N/A");
						}
						$qtyAdd = 0;
						$qtyStd = 0;
						$boqItems = BoqItem::select(['boq_items.*','units.factor'])
								->join('boqs','boqs.id','boq_items.boq_id')
								->leftJoin('units',function($join) use($dval){
									$join->on('units.from_code','boq_items.unit')
										 ->where('units.to_code',$dval->unit_purch);
								})
								->where('item_id',$dval->item_id)
								->where('boqs.status',1)
								->where('boqs.is_revise',0)
								->get();
						
						if(!empty($boqItems) && count($boqItems) > 0){
							foreach($boqItems as $boqItem){
								$factor = 1;
								if(!empty($boqItem->factor)){
									$factor = (float)$boqItem->factor;
								}
								$qtyStd += (float)$boqItem->qty_std * $factor;
								$qtyAdd += (float)$boqItem->qty_add * $factor;
							}
						}

						$sheet->cell('E'.($startRows),$qtyStd);
						$sheet->cell('F'.($startRows),$qtyAdd);

						$requestItems = RequestItem::select(['request_items.*','units.factor'])
								->leftJoin('units',function($join) use($dval){
									$join->on('units.from_code','request_items.unit')
										 ->where('units.to_code',$dval->unit_purch);
								})
								->where('item_id',$dval->item_id)
								->get();
						$qtyRequest = 0;
						if(!empty($requestItems) && count($requestItems) > 0){
							foreach($requestItems as $requestItem){
								$factor = 1;
								if(!empty($requestItem->factor)){
									$factor = (float)$requestItem->factor;
								}

								$qtyRequest += (float)$requestItem->qty * $factor;
							}
						}
						$sheet->cell('G'.($startRows),$qtyRequest);

						$sheet->cell('H'.($startRows),$dval->unit);

						$sheet->setColumnFormat([
							('E'.$startRows)=>'0.00',
							('F'.$startRows)=>'0.00',
							('G'.$startRows)=>'0.00',
						]);

						$sheet->cell(('A'.$startRows.':H'.$startRows),function($cells){
							$cells->setFontFamily('Khmer OS Battambang');
							$cells->setFontSize(11);
							$cells->setAlignment('center');
						});
					}
				});
			})->download('xlsx');
		}

		return $response;
	}

	public function printRemainingBOQTotal(Request $request){

		$endDate 	= $request->query("end_date");
		$itemType 	= $request->query("product_type");
		$itemID 	= $request->query("product");

		$columns = [
			'boq_items.id',
			'boq_items.boq_id',
			'boq_items.house_id',
			'boq_items.item_id',
			'boq_items.unit',
			'items.code',
			'items.name',
			'items.cat_id',
			'items.unit_stock',
			'items.unit_purch',
			'items.unit_usage',
			'items.cost_purch',
			'boqs.trans_date',
		];
		$report  = BoqItem::select($columns)
				->join('boqs','boqs.id','boq_items.boq_id')
				->leftJoin('items','items.id','boq_items.item_id');

		if($endDate){
			$report = $report->where('boqs.trans_date','<=',$endDate);
		}

		if($itemType){
			$report = $report->where('items.cat_id',$itemType);
		}

		if($itemID){
			$report = $report->where('boq_items.item_id',$itemID);
		}

		$report = $report->where('boqs.status',1)->where('boqs.is_revise',0)->groupBy(['boq_items.item_id'])->get();

		$data = [
			'title' 	=> trans('lang.report_remaining_boq_total'),
			'report'	=> $report,
		];

		return view('reports.boq.report_remaining_boq_total_print')->with($data);
	}

	public function inventoryValuationDetail(Request $request){
		$data = [
			'title'       => trans('lang.report'),
			'icon'        => 'fa fa-file-excel-o',
			'small_title' => trans('lang.inventory_valuation_detail'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'stock'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.stock'),
				],
				'inventory_valuation_detail' => [
						'caption' 	=> trans('lang.inventory_valuation_detail'),
				],
			],
		];
		return view('reports.inventory.inventory_valuation_detail', $data);
	}

	public function generateInventoryValuationDetailSubDataTable(Request $request,$itemID){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$version 	= $request->query("version");
		$endDate 	= $request->query("end_date");
		$warehouseID= $request->query("warehouse");
		$stockType	= $request->query("stock_type");
		$prefix 	= DB::getTablePrefix();

		if(!$endDate = date('Y-m-d',strtotime($endDate))){
			$endDate = date('Y-m-d');
		}

		$columns = [
			// -- columns main
			'stocks.item_id',
			'stocks.qty',
			'stocks.cost',
			'stocks.amount',
			"stocks.amount AS asset_value",
			DB::raw("(CASE WHEN {$prefix}units.factor=NULL THEN 1 ELSE {$prefix}units.factor END) AS factor"),
			'items.cost_purch',
			// -- columns stock
			'stocks.id',
			'stocks.pro_id',
			'stocks.ref_id',
			'stocks.ref_no',
			'stocks.ref_type',
			'stocks.line_no',
			'stocks.unit',
			'stocks.warehouse_id',
			'stocks.trans_date',
			'stocks.trans_ref',
			'stocks.alloc_ref',
			'stocks.reference',
			// -- columns items
			'items.code',
			'items.name',
			'items.cat_id',
			'items.unit_stock',
			'items.unit_purch',
			'items.unit_usage',
			// -- columns units
			'units.from_code',
			'units.from_desc',
			'units.to_code',
			'units.to_desc'
		];
		$report = Stock::select($columns)
				->leftJoin('items','items.id','stocks.item_id')
				->leftJoin('units',function($join){
					$join->on('units.from_code','stocks.unit')->on('units.to_code','items.unit_stock');
				})
				->where('items.status',1)
				->where('stocks.delete',0);

		if($endDate){
			$report = $report->where('stocks.trans_date','<=',$endDate);
		}

		if($stockType){
			$report = $report->where('stocks.ref_type',$stockType);
		}

		if($warehouseID){
			$report = $report->where('stocks.warehouse_id',$warehouseID);
		}

		if($itemID){
			$report = $report->where('stocks.item_id',$itemID);
		}
		
		$report = $report->orderBy('stocks.trans_date','ASC');
		
		
		// ->get();

		if($version=="datatables"){
			$response = Datatables::of($report)
				->addColumn('item_type',function($row){
					if($itemType = SystemData::find($row->cat_id)){
						return $itemType->name;
					}
					return '';
				})
				->make(true);
		}elseif($version=="excel"){
			$date = date("Y-m-d H:i:s");
    		Excel::create(trans("lang.inventory_valuation_detail_items") . "- ({$date})",function($excel) use($report,$endDate){
				$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
				$excel->sheet($endDate,function($sheet) use($report,$endDate){
					$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
					$objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
					$objDrawing->setCoordinates('A1');
					$objDrawing->setHeight(150);
					$objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':K'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':K'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(28);
						$cells->setFontColor(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':K'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':K'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(28);
						$cells->setFontColor(getSetting()->company_name_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':K'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.inventory_valuation_detail_items'));
					$sheet->cell(('C'.$startRows.':K'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(16);
						$cells->setFontColor(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':M'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($endDate))));
					$sheet->cell(('A'.$startRows.':M'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(11);
						$cells->setAlignment('left');
					});

					$startRows++;
					$sheet->cell(('A'.$startRows),trans('lang.type'));
					$sheet->cell(('B'.$startRows),trans('lang.date'));
					$sheet->cell(('C'.$startRows),trans('lang.name'));
					$sheet->cell(('D'.$startRows),trans('lang.num'));
					$sheet->cell(('E'.$startRows),trans('lang.qty'));
					$sheet->cell(('F'.$startRows),trans('lang.cost'));
					$sheet->cell(('G'.$startRows),trans('lang.on_hand'));
					$sheet->cell(('H'.$startRows),trans('lang.unit'));
					$sheet->cell(('I'.$startRows),trans('lang.avg_cost'));
					$sheet->cell(('J'.$startRows),trans('lang.asset_value'));
					$sheet->cell(('K'.$startRows),trans('lang.item_type'));
					$sheet->cell(('L'.$startRows),trans('lang.item_code'));
					$sheet->cell(('M'.$startRows),trans('lang.item_name'));

					$sheet->cell(('A'.$startRows.':M'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(11);
						$cells->setFontColor('#ffffff');
						$cells->setBackground(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});

					$onHand = 0;
					foreach ($report as $index => $dval) {
						$startRows++;
						$sheet->cell('A'.($startRows),$dval->ref_type);
						$sheet->cell('B'.($startRows),date("d/m/Y",strtotime($dval->trans_date)));
						$sheet->cell('C'.($startRows),$dval->ref_no);
						$sheet->cell('D'.($startRows),$dval->id);
						

						$factor = $dval->factor;
						$amount = $dval->amount;
						$qty    = $dval->qty;
						$cost   = $dval->cost;
	
						if($factor < 1){
							$qty = $qty / $factor;
						}else{
							$qty = $qty * $factor;
						}
	
						$cost = $cost / $factor;

						// On Hand
						if($index == 0){
							$onHand = 0 + (float)$qty;
						}else{
							$onHand = $onHand + (float)$qty;
						}

						$sheet->cell('E'.($startRows),$qty);
						$sheet->cell('F'.($startRows),$cost);

						$sheet->cell('G'.($startRows),$onHand);
						// Unit
						$sheet->cell('H'.($startRows),$dval->to_desc);
						// Avg Cost
						$sheet->cell('I'.($startRows),$cost);
						// Asset Value
						$sheet->cell('J'.($startRows),$amount);
						// Item Type & Item
						if($itemType = SystemData::find($dval->cat_id)){
							$sheet->cell('K'.($startRows),$itemType->name);
						}else{
							$sheet->cell('K'.($startRows),"N/A");
						}
						$sheet->cell('L'.($startRows),$dval->code);
						$sheet->cell('M'.($startRows),$dval->name);
						$sheet->setColumnFormat([
							('E'.$startRows)=>'0.00',
							('G'.$startRows)=>'0.00',
							('F'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)',
							('I'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)',
							('J'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)',
							('F'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)',
						]);
					}
				});
			})->download('xlsx');
		}

		return $response;
	}

	public function inventoryValuationDetailSubDataTable(Request $request,$itemID = 0){
		$endDate 		= $request->query("end_date");
		$itemType 		= $request->query("product_type");
		$warehouseID	= $request->query("warehouse");
		$stockType		= $request->query("stock_type");

		if($request->query("product")){
			$itemID = $request->query("product");
		}

		$stockTypes = [
			['name' => trans("lang.stock_entry"), "id" => 'stock entry'],
			['name' => trans("lang.stock_import"), "id" => 'stock import'],
			['name' => trans("lang.stock_move"),"id" => 'stock move'],
			['name' => trans("lang.return_usage"),"id" => 'return usage'],
			['name' => trans("lang.stock_adjust"),"id" => 'stock adjust'],
			['name' => trans("lang.usage_items"),"id" => 'usage items'],
			['name' => trans("lang.stock_delivery"),"id" => 'stock delivery'],
			['name' => trans("lang.return_delivery"),"id" => 'return delivery'],
		];

		$data = [
			'title'       => trans('lang.report'),
			'icon'        => 'fa fa-file-excel-o',
			'small_title' => trans('lang.inventory_valuation_detail_items'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'stock'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.stock'),
				],
				'inventory_valuation_detail' => [
					'url' 		=> url('reports/inventory/inventoryValutionDetail'),
					'caption' 	=> trans('lang.inventory_valuation_detail'),
				],
				'inventory_valuation_detail_items' => [
						'caption' 	=> trans('lang.inventory_valuation_detail_items'),
				],
			],
			'stockTypes' => $stockTypes,
			'endDate' => $endDate,
			'itemType' => $itemType,
			'itemID' => $itemID,
			'warehouseID' => $warehouseID,
		];
		return view('reports.inventory.inventory_valuation_detail_sub_datatable', $data);
	}

	public function generateInventoryValuationDetail(Request $request){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$version 		= $request->query("version");
		$endDate 		= $request->query("end_date");
		$itemType 		= $request->query("product_type");
		$itemID 		= $request->query("product");
		$warehouseID	= $request->query("warehouse");
		$prefix 		= DB::getTablePrefix();

		if(!$endDate = date('Y-m-d',strtotime($endDate))){
			$endDate = date('Y-m-d');
		}

		$columns = [
			// -- columns main
			'stocks.item_id',
			'stocks.qty',
			'stocks.cost',
			'stocks.amount',
			"stocks.amount AS asset_value",
			DB::raw("(CASE WHEN {$prefix}units.factor=NULL THEN 1 ELSE {$prefix}units.factor END) AS factor"),
			'items.cost_purch',
			// -- columns stock
			'stocks.id',
			'stocks.pro_id',
			'stocks.ref_id',
			'stocks.ref_no',
			'stocks.line_no',
			'stocks.unit',
			'stocks.warehouse_id',
			'stocks.trans_date',
			'stocks.trans_ref',
			'stocks.alloc_ref',
			'stocks.reference',
			// -- columns items
			'items.code',
			'items.name',
			'items.cat_id',
			'items.unit_stock',
			'items.unit_purch',
			'items.unit_usage',
			// -- columns units
			'units.from_code',
			'units.from_desc',
			'units.to_code',
			'units.to_desc'
		];
		$coreQuery = Stock::select($columns)
				->leftJoin('items','items.id','stocks.item_id')
				->leftJoin('units',function($join){
					$join->on('units.from_code','stocks.unit')->on('units.to_code','items.unit_stock');
				})
				->whereRaw(DB::raw("{$prefix}stocks.delete=0"));

		if($endDate){
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.trans_date <= '{$endDate}'"));
		}

		if($warehouseID){
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.warehouse_id IN({$warehouseID})"));
		}
		
		if($itemType){
			$allItems = Item::where('cat_id',$itemType)->pluck('id');
			$allItems = trim((string)$allItems,'[]');
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.item_id IN({$allItems})"));
		}
		
		if($itemID){
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.item_id IN({$itemID})"));
		}
		
		$allStockQuery = $coreQuery->toSql();
		$allStockColumns = [
			'*',
			DB::raw("(CASE WHEN ALL_STOCK.factor < 1 THEN (ALL_STOCK.qty / ALL_STOCK.factor) ELSE (ALL_STOCK.qty * ALL_STOCK.factor) END) as exact_qty"),
		];
		$allStock = DB::table(DB::raw("({$allStockQuery}) AS ALL_STOCK"))->select($allStockColumns);


		$stockSummaryQuery = $allStock->toSql();
		$stockSummaryColumns = [
			'item_id',
			'code',
			'name',
			'cat_id',
			'unit_stock',
			'unit',
			'from_code',
			'from_desc',
			'to_code',
			'to_desc',
			'factor',
			DB::raw("SUM(STOCK_SUMMARY.qty) as qty"),
			DB::raw("SUM(STOCK_SUMMARY.exact_qty) as exact_qty"),
			DB::raw("SUM(STOCK_SUMMARY.cost) as cost"),
			DB::raw("SUM(STOCK_SUMMARY.asset_value) as asset_value"),
			DB::raw("SUM(STOCK_SUMMARY.amount) as amount")
		];
		$stockSummary = DB::table(DB::raw("({$stockSummaryQuery}) AS STOCK_SUMMARY"))
				->select($stockSummaryColumns)
				->groupBy('item_id');

		$report = $stockSummary->get();

		if($version=="datatables"){
			$response = Datatables::of($report)
				->addColumn('item_type',function($row){
					if($itemType = SystemData::find($row->cat_id)){
						return $itemType->name;
					}
					return '';
				})
				->addColumn('details_url',function($row) use($endDate){
					return url("report/inventory/generateInventoryValuationDetailSubDataTable/{$row->item_id}");
				})
				->addColumn('action',function($row) use($endDate,$itemID,$itemType,$warehouseID){
					$routeView 	= url("report/inventory/inventoryValuationDetailSubDataTable/{$row->item_id}?v=1");
					$routeExcel = url("report/inventory/generateInventoryValuationDetailSubDataTable/{$row->item_id}?v=1&version=excel");

					if($endDate){
						$routeExcel .= "&end_date={$endDate}";
						$routeView .= "&end_date={$endDate}";
					}

					if($warehouseID){
						$routeExcel .= "&warehouse={$warehouseID}";
						$routeView .= "&warehouse={$warehouseID}";
					}

					if($itemType){
						$routeExcel .= "&product_type={$itemType}";
						$routeView .= "&product_type={$itemType}";
					}

					if($itemID){
						$routeExcel .= "&product={$itemID}";
						$routeView .= "&product={$itemID}";
					}

					return
						'<a href="'.$routeView.'" target="_new" title="'.trans('lang.view').'" class="btn btn-xs purple view-record">'.
						'	<i class="fa fa-eye"></i>'.
						'</a>'.
						'<a href="'.$routeExcel.'" title="'.trans('lang.download').'" class="btn btn-xs green download-record">'.
						'	<i class="fa fa-file-excel-o"></i>'.
						'</a>';
				})
				->make(true);
		}elseif($version=="excel"){
			$date = date("Y-m-d H:i:s");
    		Excel::create(trans("lang.inventory_valuation_summary") . "- ({$date})",function($excel) use($report,$endDate){
				$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
				$excel->sheet($endDate,function($sheet) use($report,$endDate){
					$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
					$objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
					$objDrawing->setCoordinates('A1');
					$objDrawing->setHeight(150);
					$objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':F'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':F'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(28);
						$cells->setFontColor(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':F'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':F'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(28);
						$cells->setFontColor(getSetting()->company_name_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':F'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.inventory_valuation_summary'));
					$sheet->cell(('C'.$startRows.':F'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(16);
						$cells->setFontColor(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':G'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($endDate))));
					$sheet->cell(('A'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(11);
						$cells->setAlignment('left');
					});

					$startRows++;
					$sheet->cell(('A'.$startRows),trans('lang.item_type'));
					$sheet->cell(('B'.$startRows),trans('lang.item_code'));
					$sheet->cell(('C'.$startRows),trans('lang.item_name'));
					$sheet->cell(('D'.$startRows),trans('lang.on_hand'));
					$sheet->cell(('E'.$startRows),trans('lang.unit'));
					$sheet->cell(('F'.$startRows),trans('lang.avg_cost'));
					$sheet->cell(('G'.$startRows),trans('lang.asset_value'));

					$sheet->cell(('A'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(11);
						$cells->setFontColor('#ffffff');
						$cells->setBackground(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});

					foreach ($report as $dval) {
						$startRows++;
						$sheet->cell('B'.($startRows),$dval->code);
						$sheet->cell('C'.($startRows),$dval->name);
						$sheet->cell('D'.($startRows),$dval->exact_qty);
						$sheet->cell('E'.($startRows),$dval->to_desc);
						// Avg Cost
						$avgCost = ($dval->asset_value > 0 ? ((float)$dval->exact_qty / (float)$dval->asset_value) : 0);
						$sheet->cell('F'.($startRows),$avgCost);
						// Asset Value
						$sheet->cell('G'.($startRows),$dval->asset_value);
						// Item Type & Item
						if($itemType = SystemData::find($dval->cat_id)){
							$sheet->cell('A'.($startRows),$itemType->name);
						}else{
							$sheet->cell('A'.($startRows),"N/A");
						}
						$sheet->setColumnFormat([
							('D'.$startRows)=>'0.00',
							('F'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)',
							('G'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)',
						]);
					}
				});
			})->download('xlsx');
		}

		return $response;
	}

	public function printInventoryValuationDetail(Request $request){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$version 		= $request->query("version");
		$endDate 		= $request->query("end_date");
		$itemType 		= $request->query("product_type");
		$itemID 		= $request->query("product");
		$warehouseID	= $request->query("warehouse");
		$prefix 		= DB::getTablePrefix();

		if(!$endDate = date('Y-m-d',strtotime($endDate))){
			$endDate = date('Y-m-d');
		}

		$columns = [
			// -- columns main
			'stocks.item_id',
			'stocks.qty',
			'stocks.cost',
			'stocks.amount',
			"stocks.amount AS asset_value",
			DB::raw("(CASE WHEN {$prefix}units.factor=NULL THEN 1 ELSE {$prefix}units.factor END) AS factor"),
			'items.cost_purch',
			// -- columns stock
			'stocks.id',
			'stocks.pro_id',
			'stocks.ref_id',
			'stocks.ref_no',
			'stocks.line_no',
			'stocks.unit',
			'stocks.warehouse_id',
			'stocks.trans_date',
			'stocks.trans_ref',
			'stocks.alloc_ref',
			'stocks.reference',
			// -- columns items
			'items.code',
			'items.name',
			'items.cat_id',
			'items.unit_stock',
			'items.unit_purch',
			'items.unit_usage',
			// -- columns units
			'units.from_code',
			'units.from_desc',
			'units.to_code',
			'units.to_desc'
		];
		$coreQuery = Stock::select($columns)
				->leftJoin('items','items.id','stocks.item_id')
				->leftJoin('units',function($join){
					$join->on('units.from_code','stocks.unit')->on('units.to_code','items.unit_stock');
				})
				->whereRaw(DB::raw("{$prefix}stocks.delete=0"));

		if($endDate){
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.trans_date <= '{$endDate}'"));
		}

		if($warehouseID){
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.warehouse_id IN({$warehouseID})"));
		}
		
		if($itemType){
			$allItems = Item::where('cat_id',$itemType)->pluck('id');
			$allItems = trim((string)$allItems,'[]');
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.item_id IN({$allItems})"));
		}
		
		if($itemID){
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.item_id IN({$itemID})"));
		}
		
		$allStockQuery = $coreQuery->toSql();
		$allStockColumns = [
			'*',
			DB::raw("(CASE WHEN ALL_STOCK.factor < 1 THEN (ALL_STOCK.qty / ALL_STOCK.factor) ELSE (ALL_STOCK.qty * ALL_STOCK.factor) END) as exact_qty"),
		];
		$allStock = DB::table(DB::raw("({$allStockQuery}) AS ALL_STOCK"))->select($allStockColumns);


		$stockSummaryQuery = $allStock->toSql();
		$stockSummaryColumns = [
			'item_id',
			'code',
			'name',
			'cat_id',
			'unit_stock',
			'unit',
			'from_code',
			'from_desc',
			'to_code',
			'to_desc',
			'factor',
			DB::raw("SUM(STOCK_SUMMARY.qty) as qty"),
			DB::raw("SUM(STOCK_SUMMARY.exact_qty) as exact_qty"),
			DB::raw("SUM(STOCK_SUMMARY.cost) as cost"),
			DB::raw("SUM(STOCK_SUMMARY.asset_value) as asset_value"),
			DB::raw("SUM(STOCK_SUMMARY.amount) as amount")
		];
		$stockSummary = DB::table(DB::raw("({$stockSummaryQuery}) AS STOCK_SUMMARY"))
				->select($stockSummaryColumns)
				->groupBy('item_id');

		$report = $stockSummary->get();

		if($itemID){
			if(!$item = Item::find($itemID)){
				$item = false;
			}
		}else{
			$item = false;
		}

		if($itemType){
			if(!$category = SystemData::find($itemType)){
				$category = false;
			}
		}else{
			$category = false;
		}

		if($warehouseID){
			if(!$warehouse = Warehouse::find($warehouseID)){
				$warehouse = false;
			}
		}else{
			$warehouse = false;
		}

		$data = [
			'report' => $report,
			'title'  => trans('lang.inventory_valuation_detail'),
			'endDate' => $endDate,
			'item' => $item,
			'itemType' => $category,
			'warehouse' => $warehouse,
		];

		return view('reports.inventory.print.inventory_valuation_detail_print')->with($data);
	}

	public function printInventoryValuationDetailSubDataTable(Request $request,$itemID){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$version 	= $request->query("version");
		$endDate 	= $request->query("end_date");
		$warehouseID= $request->query("warehouse");
		$stockType	= $request->query("stock_type");
		$itemTypeID	= $request->query("product_type");
		$prefix 	= DB::getTablePrefix();

		if(!$endDate = date('Y-m-d',strtotime($endDate))){
			$endDate = date('Y-m-d');
		}

		if($request->query("product")){
			$itemID	= $request->query("product");
		}

		if(!$item = Item::find($itemID)){
			$item = false;
		}

		if(!$warehouse = Warehouse::find($warehouseID)){
			$warehouse = false;
		}

		if(!$itemType = SystemData::find($itemTypeID)){
			$itemType = false;
		}

		$columns = [
			// -- columns main
			'stocks.item_id',
			'stocks.qty',
			'stocks.cost',
			'stocks.amount',
			"stocks.amount AS asset_value",
			DB::raw("(CASE WHEN {$prefix}units.factor=NULL THEN 1 ELSE {$prefix}units.factor END) AS factor"),
			'items.cost_purch',
			// -- columns stock
			'stocks.id',
			'stocks.pro_id',
			'stocks.ref_id',
			'stocks.ref_no',
			'stocks.ref_type',
			'stocks.line_no',
			'stocks.unit',
			'stocks.warehouse_id',
			'stocks.trans_date',
			'stocks.trans_ref',
			'stocks.alloc_ref',
			'stocks.reference',
			// -- columns items
			'items.code',
			'items.name',
			'items.cat_id',
			'items.unit_stock',
			'items.unit_purch',
			'items.unit_usage',
			// -- columns units
			'units.from_code',
			'units.from_desc',
			'units.to_code',
			'units.to_desc'
		];
		$report = Stock::select($columns)
				->leftJoin('items','items.id','stocks.item_id')
				->leftJoin('units',function($join){
					$join->on('units.from_code','stocks.unit')->on('units.to_code','items.unit_stock');
				})
				->where('items.status',1)
				->where('stocks.delete',0);

		if($endDate){
			$report = $report->where('stocks.trans_date','<=',$endDate);
		}

		if($stockType){
			$report = $report->where('stocks.ref_type',$stockType);
		}

		if($warehouseID){
			$report = $report->where('stocks.warehouse_id',$warehouseID);
		}

		if($itemID){
			$report = $report->where('stocks.item_id',$itemID);
		}

		$report = $report->orderBy('stocks.trans_date','ASC')->get();

		$data = [
			'report' => $report,
			'title'  => trans('lang.inventory_valuation_detail_items'),
			'endDate' => $endDate,
			'item' => $item,
			'itemType' => $itemType,
			'warehouse' => $warehouse,
			'stockType' => $stockType,
		];

		return view('reports.inventory.print.inventory_valuation_detail_sub_datatable_print')->with($data);
	}

	public function inventoryValuationSummary(Request $request){
		$data = [
			'title'       => trans('lang.report'),
			'icon'        => 'fa fa-file-excel-o',
			'small_title' => trans('lang.inventory_valuation_summary'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'stock'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.stock'),
				],
				'inventory_valuation_summary' => [
						'caption' 	=> trans('lang.inventory_valuation_summary'),
				],
			],
		];
		return view('reports.inventory.inventory_valuation_summary', $data);
	}

	public function generateInventoryValuationSummary(Request $request){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$version 		= $request->query("version");
		$endDate 		= $request->query("end_date");
		$itemType 		= $request->query("product_type");
		$itemID 		= $request->query("product");
		$warehouseID	= $request->query("warehouse");
		$prefix 		= DB::getTablePrefix();

		if(!$endDate = date('Y-m-d',strtotime($endDate))){
			$endDate = date('Y-m-d');
		}

		$columns = [
			// -- columns main
			'stocks.item_id',
			'stocks.qty',
			'stocks.cost',
			'stocks.amount',
			"stocks.amount AS asset_value",
			DB::raw("(CASE WHEN {$prefix}units.factor=NULL THEN 1 ELSE {$prefix}units.factor END) AS factor"),
			'items.cost_purch',
			// -- columns stock
			'stocks.id',
			'stocks.pro_id',
			'stocks.ref_id',
			'stocks.ref_no',
			'stocks.line_no',
			'stocks.unit',
			'stocks.warehouse_id',
			'stocks.trans_date',
			'stocks.trans_ref',
			'stocks.alloc_ref',
			'stocks.reference',
			// -- columns items
			'items.code',
			'items.name',
			'items.cat_id',
			'items.unit_stock',
			'items.unit_purch',
			'items.unit_usage',
			// -- columns units
			'units.from_code',
			'units.from_desc',
			'units.to_code',
			'units.to_desc'
		];
		$coreQuery = Stock::select($columns)
				->leftJoin('items','items.id','stocks.item_id')
				->leftJoin('units',function($join){
					$join->on('units.from_code','stocks.unit')->on('units.to_code','items.unit_stock');
				})
				->whereRaw(DB::raw("{$prefix}stocks.delete=0"));

		if($endDate){
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.trans_date <= '{$endDate}'"));
		}

		if($warehouseID){
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.warehouse_id IN({$warehouseID})"));
		}
		
		if($itemType){
			$allItems = Item::where('cat_id',$itemType)->pluck('id');
			$allItems = trim((string)$allItems,'[]');
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.item_id IN({$allItems})"));
		}
		
		if($itemID){
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.item_id IN({$itemID})"));
		}
		
		$allStockQuery = $coreQuery->toSql();
		$allStockColumns = [
			'*',
			DB::raw("(CASE WHEN ALL_STOCK.factor < 1 THEN (ALL_STOCK.qty / ALL_STOCK.factor) ELSE (ALL_STOCK.qty * ALL_STOCK.factor) END) as exact_qty"),
		];
		$allStock = DB::table(DB::raw("({$allStockQuery}) AS ALL_STOCK"))->select($allStockColumns);


		$stockSummaryQuery = $allStock->toSql();
		$stockSummaryColumns = [
			'item_id',
			'code',
			'name',
			'cat_id',
			'unit_stock',
			'unit',
			'from_code',
			'from_desc',
			'to_code',
			'to_desc',
			'factor',
			DB::raw("SUM(STOCK_SUMMARY.qty) as qty"),
			DB::raw("SUM(STOCK_SUMMARY.exact_qty) as exact_qty"),
			DB::raw("SUM(STOCK_SUMMARY.cost) as cost"),
			DB::raw("SUM(STOCK_SUMMARY.asset_value) as asset_value"),
			DB::raw("SUM(STOCK_SUMMARY.amount) as amount")
		];
		$stockSummary = DB::table(DB::raw("({$stockSummaryQuery}) AS STOCK_SUMMARY"))
				->select($stockSummaryColumns)
				->groupBy('item_id');

		$report = $stockSummary->get();

		if($version=="datatables"){
			$response = Datatables::of($report)
				->addColumn('item_type',function($row){
					if($itemType = SystemData::find($row->cat_id)){
						return $itemType->name;
					}
					return '';
				})
				->make(true);
		}elseif($version=="excel"){
			$date = date("Y-m-d H:i:s");
    		Excel::create(trans("lang.inventory_valuation_detail") . "- ({$date})",function($excel) use($report,$endDate){
				$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
				$excel->sheet(trans("lang.inventory_valuation_detail"),function($sheet) use($report,$endDate){
					$startRows = 1;
					$sheet->setAutoSize(true);
					$sheet->mergeCells('A1:A3');
					$objDrawing = new PHPExcel_Worksheet_Drawing;
					$objDrawing->setPath(str_replace('root','',base_path('assets/upload/temps/app_icon.png')));
					$objDrawing->setCoordinates('A1');
					$objDrawing->setHeight(150);
					$objDrawing->setWorksheet($sheet);

					$sheet->mergeCells(('C'.$startRows.':F'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->report_header);
					$sheet->cell(('C'.$startRows.':F'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(28);
						$cells->setFontColor(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':F'.$startRows));
					$sheet->cell(('C'.$startRows),getSetting()->company_name);
					$sheet->cell(('C'.$startRows.':F'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(28);
						$cells->setFontColor(getSetting()->company_name_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('C'.$startRows.':F'.$startRows));
					$sheet->cell(('C'.$startRows),trans('lang.inventory_valuation_detail'));
					$sheet->cell(('C'.$startRows.':F'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(16);
						$cells->setFontColor(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});
					$startRows++;
					$sheet->mergeCells(('A'.$startRows.':G'.$startRows));
					$sheet->cell(('A'.$startRows),"*".trans("lang.date")." : ".(date('d/m/Y',strtotime($endDate))));
					$sheet->cell(('A'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(11);
						$cells->setAlignment('left');
					});

					$startRows++;
					$sheet->cell(('A'.$startRows),trans('lang.item_type'));
					$sheet->cell(('B'.$startRows),trans('lang.item_code'));
					$sheet->cell(('C'.$startRows),trans('lang.item_name'));
					$sheet->cell(('D'.$startRows),trans('lang.on_hand'));
					$sheet->cell(('E'.$startRows),trans('lang.unit'));
					$sheet->cell(('F'.$startRows),trans('lang.avg_cost'));
					$sheet->cell(('G'.$startRows),trans('lang.asset_value'));

					$sheet->cell(('A'.$startRows.':G'.$startRows),function($cells){
						$cells->setFontFamily('Khmer OS Battambang');
						$cells->setFontSize(11);
						$cells->setFontColor('#ffffff');
						$cells->setBackground(getSetting()->report_header_color);
						$cells->setAlignment('center');
					});

					foreach ($report as $dval) {
						$startRows++;
						$sheet->cell('B'.($startRows),$dval->code);
						$sheet->cell('C'.($startRows),$dval->name);
						$sheet->cell('D'.($startRows),$dval->exact_qty);
						$sheet->cell('E'.($startRows),$dval->to_desc);
						// Avg Cost
						$avgCost = ($dval->asset_value > 0 ? ((float)$dval->exact_qty / (float)$dval->asset_value) : 0);
						$sheet->cell('F'.($startRows),$avgCost);
						// Asset Value
						$sheet->cell('G'.($startRows),$dval->asset_value);
						// Item Type & Item
						if($itemType = SystemData::find($dval->cat_id)){
							$sheet->cell('A'.($startRows),$itemType->name);
						}else{
							$sheet->cell('A'.($startRows),"N/A");
						}
						$sheet->setColumnFormat([
							('D'.$startRows)=>'0.00',
							('F'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)',
							('G'.$startRows)=>'_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)',
						]);
					}
				});
			})->download('xlsx');
		}

		return $response;
	}

	public function printInventoryValuationSummary(Request $request){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$version 		= $request->query("version");
		$endDate 		= $request->query("end_date");
		$itemType 		= $request->query("product_type");
		$itemID 		= $request->query("product");
		$warehouseID	= $request->query("warehouse");
		$prefix 		= DB::getTablePrefix();

		if(!$endDate = date('Y-m-d',strtotime($endDate))){
			$endDate = date('Y-m-d');
		}

		$columns = [
			// -- columns main
			'stocks.item_id',
			'stocks.qty',
			'stocks.cost',
			'stocks.amount',
			"stocks.amount AS asset_value",
			DB::raw("(CASE WHEN {$prefix}units.factor=NULL THEN 1 ELSE {$prefix}units.factor END) AS factor"),
			'items.cost_purch',
			// -- columns stock
			'stocks.id',
			'stocks.pro_id',
			'stocks.ref_id',
			'stocks.ref_no',
			'stocks.line_no',
			'stocks.unit',
			'stocks.warehouse_id',
			'stocks.trans_date',
			'stocks.trans_ref',
			'stocks.alloc_ref',
			'stocks.reference',
			// -- columns items
			'items.code',
			'items.name',
			'items.cat_id',
			'items.unit_stock',
			'items.unit_purch',
			'items.unit_usage',
			// -- columns units
			'units.from_code',
			'units.from_desc',
			'units.to_code',
			'units.to_desc'
		];
		$coreQuery = Stock::select($columns)
				->leftJoin('items','items.id','stocks.item_id')
				->leftJoin('units',function($join){
					$join->on('units.from_code','stocks.unit')->on('units.to_code','items.unit_stock');
				})
				->whereRaw(DB::raw("{$prefix}stocks.delete=0"));

		if($endDate){
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.trans_date <= '{$endDate}'"));
		}

		if($warehouseID){
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.warehouse_id IN({$warehouseID})"));
		}
		
		if($itemType){
			$allItems = Item::where('cat_id',$itemType)->pluck('id');
			$allItems = trim((string)$allItems,'[]');
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.item_id IN({$allItems})"));
		}
		
		if($itemID){
			$coreQuery = $coreQuery->whereRaw(DB::raw("{$prefix}stocks.item_id IN({$itemID})"));
		}
		
		$allStockQuery = $coreQuery->toSql();
		$allStockColumns = [
			'*',
			DB::raw("(CASE WHEN ALL_STOCK.factor < 1 THEN (ALL_STOCK.qty / ALL_STOCK.factor) ELSE (ALL_STOCK.qty * ALL_STOCK.factor) END) as exact_qty"),
		];
		$allStock = DB::table(DB::raw("({$allStockQuery}) AS ALL_STOCK"))->select($allStockColumns);


		$stockSummaryQuery = $allStock->toSql();
		$stockSummaryColumns = [
			'item_id',
			'code',
			'name',
			'cat_id',
			'unit_stock',
			'unit',
			'from_code',
			'from_desc',
			'to_code',
			'to_desc',
			'factor',
			DB::raw("SUM(STOCK_SUMMARY.qty) as qty"),
			DB::raw("SUM(STOCK_SUMMARY.exact_qty) as exact_qty"),
			DB::raw("SUM(STOCK_SUMMARY.cost) as cost"),
			DB::raw("SUM(STOCK_SUMMARY.asset_value) as asset_value"),
			DB::raw("SUM(STOCK_SUMMARY.amount) as amount")
		];
		$stockSummary = DB::table(DB::raw("({$stockSummaryQuery}) AS STOCK_SUMMARY"))
				->select($stockSummaryColumns)
				->groupBy('item_id');

		$report = $stockSummary->get();

		if($itemID){
			if(!$item = Item::find($itemID)){
				$item = false;
			}
		}else{
			$item = false;
		}

		if($itemType){
			if(!$category = SystemData::find($itemType)){
				$category = false;
			}
		}else{
			$category = false;
		}

		if($warehouseID){
			if(!$warehouse = Warehouse::find($warehouseID)){
				$warehouse = false;
			}
		}else{
			$warehouse = false;
		}
		$data = [
			'report' => $report,
			'title'  => trans('lang.inventory_valuation_summary'),
			'endDate' => $endDate,
			'item' => $item,
			'itemType' => $category,
			'warehouse' => $warehouse,
		];

		return view('reports.inventory.print.inventory_valuation_summary_print')->with($data);
	}
	public function boqTreeView(Request $request){
		$pro_id = Session::get('project');
		
		$house = DB::table('houses')
		->select(
			'houses.*',
			DB::raw('(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_houses`.`zone_id`) AS zone_name'),
			DB::raw('(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_houses`.`block_id`) AS block_name'),
			DB::raw('(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_houses`.`building_id`) AS building_name'),
			DB::raw('(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_houses`.`street_id`) AS street_name'),
			DB::raw('(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_houses`.`house_type`) AS house_type_name')
		)->where('status',1)->get();

		$zone = DB::table('system_datas')->where('type','ZN')->where('parent_id',$pro_id)->where('status','1')->get();
		$li_html='';
		foreach($zone as $row_zone){
			$li_html.='<li><a>'.$row_zone->name.'</a>';
				$li_html.='<ul>';
					$block = DB::table('houses')->select('houses.zone_id','houses.block_id',DB::raw('(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_houses`.`block_id`) AS block_name'))
					->where('status',1)->where('houses.zone_id',$row_zone->id)
					->groupBy('houses.block_id')->get();				
					foreach($block as $blocks){
						$li_html.='<li>';
							$li_html.='<a>'.$blocks->block_name.'</a>';
							$li_html.='<ul>';
								$building = DB::table('houses')->select('houses.zone_id','houses.block_id','houses.building_id',
								DB::raw('(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_houses`.`building_id`) AS building_name'))
								->where('status',1)->where('houses.zone_id',$row_zone->id)->where('houses.block_id',$blocks->block_id)
								->groupBy('houses.block_id')->get();
								foreach($building as $buildings){
									$li_html.='<li>';
										$li_html.='<a>'.$buildings->building_name.'</a>';
										$li_html.='<ul>';
											$street = DB::table('houses')->select('houses.zone_id','houses.block_id','houses.building_id','houses.street_id',
											DB::raw('(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_houses`.`street_id`) AS street_name'))
											->where('status',1)->where('houses.zone_id',$row_zone->id)->where('houses.block_id',$blocks->block_id)
											->where('houses.building_id',$buildings->building_id)
											->groupBy('houses.street_id')->get();
											foreach($street as $streets){
												$li_html.='<li>';
													$li_html.='<a>'.$streets->street_name.'</a>';
													$li_html.='<ul>';
														$house_type = DB::table('houses')->select('houses.zone_id','houses.block_id','houses.building_id','houses.street_id','houses.house_type',
														DB::raw('(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_houses`.`house_type`) AS house_type_name'))
														->where('status',1)->where('houses.zone_id',$row_zone->id)->where('houses.block_id',$blocks->block_id)
														->where('houses.building_id',$buildings->building_id)->where('houses.street_id',$streets->street_id)
														->groupBy('houses.house_type')->get();
														foreach($house_type as $house_types){
															$li_html.='<li>';
																$li_html.='<a>'.$house_types->house_type_name.'</a>';
																$li_html.='<ul>';
																	$house = DB::table('houses')->select('houses.*')
																	->where('status',1)->where('houses.zone_id',$row_zone->id)->where('houses.block_id',$blocks->block_id)
																	->where('houses.building_id',$buildings->building_id)->where('houses.street_id',$streets->street_id)->get();
																	foreach($house as $houses){
																		$li_html.='<li>';
																			$li_html.='<a style="padding-left:7px;" onclick="geBOQFromHouse('.$houses->id.')">'.$houses->house_no.'</a>';
																		$li_html.='</li>';
																	}		
																$li_html.='</ul>';
															$li_html.='</li>';
														}		
													$li_html.='</ul>';
												$li_html.='</li>';
											}		
										$li_html.='</ul>';
									$li_html.='</li>';
								}		
							$li_html.='</ul>';	
						$li_html.='</li>';
					}		
				$li_html.='</ul>';	
			$li_html.='</li>';	
		} 
		$data = [
			'title'       => trans('lang.tree_view'),
			'icon'        => 'fa fa-shopping-cart',
			// 'house'       => $house,
			'li_html'     =>$li_html,
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.tree_view'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
		];
		return view('reports.boq.tree')->with($data);
	}
	public function getBoq(Request $request){
		$report ="";
		$project_id = Session::get('project');
		if(!empty($request->house_id)){
			// $sql = "SELECT (SELECT `pr_projects`.`name` FROM `pr_projects` WHERE `pr_projects`.`id`=$project_id)AS project,boq.`trans_by`, 
			// pr_boq_houses.`house_id`,
			// (SELECT `pr_houses`.`house_no` FROM `pr_houses` WHERE `pr_houses`.id=pr_boq_houses.`house_id`)AS house_no, 
			// (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=(SELECT `pr_houses`.`house_type` FROM `pr_houses` WHERE `pr_houses`.`id`=pr_boq_houses.`house_id`))AS house_type, boqi.`item_id`,
			// (SELECT `pr_items`.`code` FROM `pr_items` WHERE `pr_items`.`id`=boqi.item_id)AS item_code, (SELECT `pr_items`.`name` FROM `pr_items` WHERE `pr_items`.`id`=boqi.item_id)AS item_name, 
			// (SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=(SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE `pr_items`.`id`=boqi.`item_id`))AS item_type,
			// (SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE `pr_items`.`id`=boqi.`item_id`)AS item_type_id,
			// (SELECT `pr_system_datas`.`desc` FROM `pr_system_datas` WHERE `pr_system_datas`.`id`=(SELECT `pr_items`.`cat_id` FROM `pr_items` WHERE `pr_items`.`id`=boqi.`item_id`))AS item_type_desc,
			// (SELECT `pr_items`.`cost_purch` FROM `pr_items` WHERE `pr_items`.`id`=boqi.`item_id` AND `pr_items`.`unit_purch`=boqi.`unit`)AS item_price, boqi.`qty_std`, boqi.`qty_add`, 
			// (SELECT `pr_units`.`from_desc` FROM `pr_units` WHERE `pr_units`.`from_code`=boqi.`unit` LIMIT 1)AS unit 
			// FROM `pr_boqs` AS boq 
			// JOIN pr_boq_houses ON `pr_boq_houses`.boq_id = boq.id
			// JOIN `pr_boq_items` AS boqi ON boqi.`boq_house_id` WHERE  pr_boq_houses.`house_id`= $request->house_id";
			$sql = "SELECT pr_boq_items.*,
			(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = pr_boq_items.`working_type`) AS working_type,
			(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = pr_items.`cat_id`) AS item_type,	
			(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_houses`.`house_type`) AS house_type,
			pr_items.`name`,
			pr_items.`desc`,
			pr_items.`code`,
			pr_houses.`house_no`,
			pr_houses.`house_desc`	
			FROM pr_boq_items 
			JOIN pr_boq_houses 
				ON pr_boq_houses.id = pr_boq_items.boq_house_id
			JOIN pr_boqs
				ON pr_boqs.id = pr_boq_houses.boq_id 
			JOIN pr_houses
				ON pr_houses.id = pr_boq_houses.`house_id`
			JOIN pr_items
				ON pr_items.id=pr_boq_items.item_id	
			WHERE pr_boqs.status = 1 AND pr_boqs.is_revise = 0 AND pr_boq_items.house_id=$request->house_id";
			$report = DB::select($sql);
		}
		return response()->json($report);
	}
	public function getBoqprint(Request $request){
		$report ="";
		$project_id = Session::get('project');
		if(!empty($request->house_id)){
			$sql = "SELECT pr_boq_items.*,
			(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = pr_boq_items.`working_type`) AS working_type,
			(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = pr_items.`cat_id`) AS item_type,	
			(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_houses`.`house_type`) AS house_type,
			pr_items.`name`,
			pr_items.`desc`,
			pr_items.`code`,
			pr_houses.`house_no`,
			pr_houses.`house_desc`	
			FROM pr_boq_items 
			JOIN pr_houses
				ON pr_houses.id = pr_boq_items.`house_id`
			JOIN pr_boqs
				ON pr_boqs.id = pr_boq_items.boq_id 
			JOIN pr_items
				ON pr_items.id=pr_boq_items.item_id	
			WHERE pr_boqs.status = 1 AND pr_boq_items.house_id=$request->house_id";
			$house = DB::select($sql);
		}
		$data = [
			'title'       => trans('lang.tree_view'),
			'icon'        => 'fa fa-shopping-cart',
			'house'		  => $house,	
			'small_title' => trans('lang.report'),
			'background'  => '',
			'link'        => [
				'home'	=> [
						'url' 		=> url('/'),
						'caption' 	=> trans('lang.home'),
				],
				'report'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.report'),
				],
				'purchase'	=> [
						'url' 		=> '#',
						'caption' 	=> trans('lang.tree_view'),
				],
				'request'	=> [
						'caption' 	=> trans('lang.request'),
				],
			],
		];
		return view('reports.boq.print_tree')->with($data);
	}
	public function getBoqexport(Request $request){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		$report ="";
		$project_id = Session::get('project');
		if(!empty($request->house_id)){
			$sql = "SELECT pr_boq_items.*,
			(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = pr_boq_items.`working_type`) AS working_type,
			(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = pr_items.`cat_id`) AS item_type,	
			(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_houses`.`house_type`) AS house_type,
			pr_items.`name`,
			pr_items.`desc`,
			pr_items.`code`,
			pr_houses.`house_no`,
			pr_houses.`house_desc`	
			FROM pr_boq_items 
			JOIN pr_boq_houses 
				ON pr_boq_houses.id = boq_items.boq_house_id
			JOIN pr_boqs
				ON pr_boqs.id = pr_boq_houses.boq_id 
			JOIN pr_houses
				ON pr_houses.id = pr_boq_houses.`house_id`
			JOIN pr_items
				ON pr_items.id=pr_boq_items.item_id	
			WHERE pr_boqs.status = 1 AND pr_boqs.is_revise = 0 AND pr_boq_items.house_id=$request->house_id";
			$report = DB::select($sql);
		}
		Excel::create('Tree BOQ.export_'.date('Y_m_d_H_i_s'),function($excel) use($report){
			$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
			$excel->sheet('Tree BOQ Info',function($sheet) use($report){
				$cells = 1;
				$sheet->cell(('A'.$cells),"List BOQ by House");
				$sheet->mergeCells('A1:J1');
				$sheet->cell(("A1"),function($cells){
					$cells->setFontSize(15);;
					$cells->setAlignment('center');
					$cells->setFontWeight('bold');
					$cells->setFontFamily('Khmer OS Muol');
				});
				$cells++;
				$sheet->cell('A'.$cells,trans('lang.working_type'));
				$sheet->cell('B'.$cells,trans('lang.house_type'));
				$sheet->cell('C'.$cells,trans('lang.house_no'));
				$sheet->cell('D'.$cells,trans('lang.item_type'));
				$sheet->cell('E'.$cells,trans('lang.item_code'));
				$sheet->cell('F'.$cells,trans('lang.item_name'));
				$sheet->cell('G'.$cells,trans('lang.boq_qty'));
				$sheet->cell('H'.$cells,trans('lang.add_qty'));
				$sheet->cell('I'.$cells,trans('lang.units'));
				$sheet->cell('J'.$cells,trans('lang.price'));	
				$sheet->cell(("A$cells:J$cells"),function($cells){
					$cells->setBackground('#337ab7');
					$cells->setAlignment('center');
					$cells->setFontFamily('Khmer OS Battambang');
					$cells->setFontColor('#ffffff');
				});			
				$i = 1;
				if(count($report)>0){
					foreach ($report as $value) {
						$cells++;
						$sheet->cell('A'.($cells),$value->working_type);
						$sheet->cell('B'.($cells),$value->house_type);
						$sheet->cell('C'.($cells),$value->house_no);
						$sheet->cell('D'.($cells),$value->item_type);
						$sheet->cell('E'.($cells),$value->code);
						$sheet->cell('F'.($cells),$value->name);
						$sheet->cell('G'.($cells),$value->qty_std);
						$sheet->cell('H'.($cells),$value->qty_add);
						$sheet->cell('I'.($cells),$value->unit);
						$sheet->cell('J'.($cells),$value->cost);
						$sheet->cell(("A$cells:J$cells"),function($cells){
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
}