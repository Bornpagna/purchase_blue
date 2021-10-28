<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;
use Datatables;
use Redirect;
use Excel;
use App\Model\UsageFormula;
use App\Model\UsageFormulaDetail;
use App\Model\Item;
use App\Model\House;
use App\Model\SystemData;
use App\User;

class UsageFormulaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
		$this->middleware('CheckProject');
    }

    public function index(Request $request){
        $data = [
			'title'			=> trans('lang.usage_formula'),
			'icon'			=> 'fa fa-legal',
			'small_title'	=> trans('lang.new_usage_formula'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' => url('/'),
						'caption' => trans('lang.home'),
				],
				'setup_option'	=> [
                        'url' => '#',
						'caption' => trans('lang.setup_option'),
				],
                'usage_formula'	=> [
                    'caption' => trans('lang.usage_formula'),
                ],
			],
			'route'		    => url("usageFormula"),
            'routeAdd'      => url('usageFormula/add'),
            'routeDownload' => url('usageFormula/downloadExcel'),
            'routeUpload'   => url('usageFormula/importExcel'),
		];
        return view('usage_formula.index')->with($data);
    }

    public function datatables(Request $request){
        $usageFormulas = UsageFormula::where(['status' => 1])->get();
        return Datatables::of($usageFormulas)
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
        ->addColumn('street',function($row){
            if($street = SystemData::find($row->street_id)){
                return $street->name;
            }
            return '';
        })
        ->addColumn('updated_by',function($row){
            if($user = User::find($row->updated_by)){
                return $user->name;
            }
            return '';
        })
        ->addColumn('details_url',function($row){
            return url("usageFormula/subDatatables/{$row->id}");
        })
        ->addColumn('action',function($row){
            $routeDelete = url("usageFormula/delete/{$row->id}");
			$routeEdit   = url("usageFormula/edit/{$row->id}");
			$btnEdit = 'onclick="onEdit(this)"';
			$btnDelete = 'onclick="onDelete(this)"';
			
			return
				'<a '.$btnEdit.' title="'.trans('lang.edit').'" class="btn btn-xs yellow edit-record" row_id="'.$row->id.'" row_rounte="'.$routeEdit.'">'.
				'	<i class="fa fa-edit"></i>'.
				'</a>'.
				'<a '.$btnDelete.' title="'.trans('lang.delete').'" class="btn btn-xs red delete-record" row_id="'.$row->id.'" row_rounte="'.$routeDelete.'">'.
				'	<i class="fa fa-trash"></i>'.
				'</a>';
        })
        ->make(true);
    }

    public function sub_datatables(Request $request,$id){
        $usageFormulas = UsageFormulaDetail::where(['formula_id'=> $id])->get();
        return Datatables::of($usageFormulas)
        ->addColumn('house_type',function($row){
            if($house = House::find($row->house_id)){
                if($houseType = SystemData::find($house->house_type)){
                    return $houseType->name;
                }
            }
            return '';
        })
        ->addColumn('house_no',function($row){
            if($house = House::find($row->house_id)){
                return $house->house_no;
            }
            return '';
        })
        ->addColumn('item_type',function($row){
            if($item = Item::find($row->item_id)){
                if($itemType = SystemData::find($item->cat_id)){
                    return $itemType->name;
                }
            }
            return '';
        })
        ->addColumn('item',function($row){
            if($item = Item::find($row->item_id)){
                return "{$item->name} ({$item->code})";
            }
            return '';
        })
        ->make(true);
    }

    public function add(Request $request){
        $data = [
			'title'			=> trans('lang.usage_formula'),
			'icon'			=> 'fa fa-legal',
			'small_title'	=> trans('lang.new_usage_formula'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' => url('/'),
						'caption' => trans('lang.home'),
				],
				'setup_option'	=> [
                        'url' => '#',
						'caption' => trans('lang.setup_option'),
				],
                'usage_formula'	=> [
                    'url'     => url('usageFormula'),
                    'caption' => trans('lang.usage_formula'),
                ],
                'new_usage_formula'	=> [
                    'caption' => trans('lang.new_usage_formula'),
                ],
			],
			'route'		    => url("usageFormula"),
		];
        return view('usage_formula.add')->with($data);
    }

    public function edit(Request $request,$id){
        
        $prefix = DB::getTablePrefix();
        $columnUsageFormula = [
            '*',
            DB::raw("(SELECT {$prefix}system_datas.name FROM {$prefix}system_datas WHERE {$prefix}system_datas.id = {$prefix}usage_formulas.zone_id) AS zone"),
			DB::raw("(SELECT {$prefix}system_datas.name FROM {$prefix}system_datas WHERE {$prefix}system_datas.id = {$prefix}usage_formulas.block_id) AS block"),
            DB::raw("(SELECT {$prefix}system_datas.name FROM {$prefix}system_datas WHERE {$prefix}system_datas.id = {$prefix}usage_formulas.building_id) AS building"),
            DB::raw("(SELECT {$prefix}system_datas.name FROM {$prefix}system_datas WHERE {$prefix}system_datas.id = {$prefix}usage_formulas.house_type_id) AS house_type"),
			DB::raw("(SELECT {$prefix}system_datas.name FROM {$prefix}system_datas WHERE {$prefix}system_datas.id = {$prefix}usage_formulas.street_id) AS street"),
        ];
        if(!$usageFormula = UsageFormula::select($columnUsageFormula)->find($id)){
            $usageFormula = false;
        }

        $columnUsageFormulaDetail = [
            '*',
            'houses.house_no',
            'houses.house_type',
            DB::raw("(SELECT {$prefix}system_datas.name FROM {$prefix}system_datas WHERE {$prefix}system_datas.id = {$prefix}houses.house_type AND {$prefix}system_datas.type='HT' LIMIT 1) AS house_Type"),
        ];
        if(!$usageFormulaDetail = UsageFormulaDetail::select($columnUsageFormulaDetail)
        ->leftJoin('houses','houses.id','usage_formula_details.house_id')
        ->where(['formula_id' => $id])->get()){
            $usageFormulaDetail = false;
        }

        $data = [
			'title'			=> trans('lang.usage_formula'),
			'icon'			=> 'fa fa-legal',
			'small_title'	=> trans('lang.new_usage_formula'),
			'background'	=> '',
			'link'			=> [
				'home'	=> [
						'url' => url('/'),
						'caption' => trans('lang.home'),
				],
				'setup_option'	=> [
                        'url' => '#',
						'caption' => trans('lang.setup_option'),
				],
                'usage_formula'	=> [
                    'url'     => url('usageFormula'),
                    'caption' => trans('lang.usage_formula'),
                ],
                'edit_usage_formula'	=> [
                    'caption' => trans('lang.edit_usage_formula'),
                ],
			],
			'route'		=> url("usageFormula/update/{$id}"),
            'routeBack' => url("usageFormula"),
		];
        return view('usage_formula.edit')
                ->with($data)
                ->with('usageFormula',$usageFormula)
                ->with('usageFormulaDetail',$usageFormulaDetail);
    }

    public function store(Request $request){        
        try {
            DB::beginTransaction();

            // $rules = ['street_id' => 'required'];
            // if(count($request['line_percent']) > 0){
            //     for($i=0;$i<count($request['line_percent']);$i++){
            //         $rules = array_merge($rules,['line_percent' => 'required']);
            //     }
            // }
            // Validator::make($request->all(),$rules)->validate();

            $column = [];
            $code = '';

            if($zoneID = $request->input('zone_id')){
                if($zone = SystemData::find($zoneID)){
                    $code .= "{$zone->name}-";
                }
            }

            if($blockID = $request->input('block_id')){
                if($block = SystemData::find($blockID)){
                    $code .= "{$block->name}-";
                }
            }
            if($buildingID = $request->input('building_id')){
                if($building = SystemData::find($buildingID)){
                    $code .= "{$building->name}-";
                }
            }

            if($streetID = $request->input('street_id')){
                if($street = SystemData::find($streetID)){
                    $code .= "{$street->name}-";
                }
            }

            if($houseTypeID = $request->input('house_type')){
                if($houseType = SystemData::find($houseTypeID)){
                    $code .= $houseType->name;
                }
            }

            $column = [
                'code' => $code,
                'street_id' => $streetID,
                'created_by' => Auth::user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if($zoneID){
                $column = array_merge($column,['zone_id' => $zoneID]);
            }

            if($blockID){
                $column = array_merge($column,['block_id' => $blockID]);
            }

            if($buildingID){
                $column = array_merge($column,['building_id' => $buildingID]);
            }

            if($houseTypeID){
                $column = array_merge($column,['house_type_id' => $houseTypeID]);
            }

            if(!$usageFormulaID = DB::table('usage_formulas')->insertGetId($column)){
                DB::rollback();
                throw new \Exception("UsageFormula not insert.");
            }

            if(is_array($request['line_percent']) && count($request['line_percent']) > 0){
                for($i=0; $i < count($request['line_percent']); $i++){
                    $detail = [
                        'formula_id' => $usageFormulaID,
                        'house_id'   => $request['line_house_id'][$i],
                        'percentage' => $request['line_percent'][$i],
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];

                    if(!$usageFormulaDetailID = DB::table('usage_formula_details')->insertGetId($detail)){
                        DB::rollback();
                        throw new \Exception("UsageFormulaDetail not insert.");
                    }
                }
            }
            
            DB::commit();
            if($request->input('save_new')){
                return redirect('usageFormula/add')->with('success', trans('lang.save_successful'));
            }
            return redirect('usageFormula')->with('success', trans('lang.save_successful'));
        } catch (\Exception $ex) {
            DB::rollback();
            return redirect('usageFormula/add')->with('error', $ex->getMessage() . " at line " . $ex->getLine());
        }
    }

    public function update(Request $request,$id){
        // print_r($request->all());exit;
        try {
            DB::beginTransaction();

            if(!$usageFormula = UsageFormula::find($id)){
                throw new \Exception("UsageFormula[{$id}] not found.");
            }

            // $rules = ['street_id' => 'required'];
            // if(count($request['line_percent']) > 0){
            //     for($i=0;$i<count($request['line_percent']);$i++){
            //         $rules = array_merge($rules,['line_percent' => 'required']);
            //     }
            // }
            // Validator::make($request->all(),$rules)->validate();

            $column = [
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if($zoneID = $request->input('zone_id')){
                $column = array_merge($column,['zone_id' => $zoneID]);
            }

            if($blockID = $request->input('block_id')){
                $column = array_merge($column,['block_id' => $blockID]);
            }

            if($buildingID = $request->input('building_id')){
                $column = array_merge($column,['building_id' => $buildingID]);
            }

            if($houseTypeID = $request->input('house_type')){
                $column = array_merge($column,['house_type_id' => $houseTypeID]);
            }

            if($streetID = $request->input('street_id')){
                $column = array_merge($column,['street_id' => $streetID]);
            }

            DB::table('usage_formulas')->where(['id' => $id])->update($column);
            // Delete all detail
            DB::table('usage_formula_details')->where(['formula_id' => $id])->delete();

            if(count($request['line_percent']) > 0){
                for($i=0; $i < count($request['line_percent']); $i++){
                    $detail = [
                        'formula_id' => $usageFormula->id,
                        'house_id'   => $request['line_house_id'][$i],
                        'percentage' => $request['line_percent'][$i],
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];

                    if(!$usageFormulaDetailID = DB::table('usage_formula_details')->insertGetId($detail)){
                        DB::rollback();
                        throw new \Exception("UsageFormulaDetail not insert.");
                    }
                }
            }
            
            DB::commit();
            return redirect('usageFormula')->with('success', trans('lang.save_successful'));
        } catch (\Exception $ex) {
            DB::rollback();
            return redirect("usageFormula/{$id}/edit")->with('error', $ex->getMessage());
        }
    }

    public function downloadExcel(Request $request){
        try {
            $date = date('Y-m-d H:i:s');
			Excel::create("Usage Policy({$date})",function($excel) {
				$excel->setCreator(Auth::user()->name)->setCompany(config('app.name'));
				$excel->sheet('Usage Policy',function($sheet){
					// Header
					$sheet->cell('A1', 'Zone');
					$sheet->cell('B1', 'Block');
					$sheet->cell('C1', 'Street');
					$sheet->cell('D1', 'House');
					$sheet->cell('E1', 'Percent');
					// Example Body
					$sheet->cell('A2', 'Zone 1');
					$sheet->cell('B2', 'Block 1');
					$sheet->cell('C2', 'Street 1');
					$sheet->cell('D2', 'House 1');
					$sheet->cell('E2', '20');

                    $sheet->cell('A3', 'Zone 1');
					$sheet->cell('B3', 'Block 1');
					$sheet->cell('C3', 'Street 1');
					$sheet->cell('D3', 'House 2');
					$sheet->cell('E3', '80');
				});
			})->download('xlsx');
		} catch (\Exception $ex) {
			return redirect('usageFormula')->with("error", $ex->getMessage());
		}
    }

    public function importExcel(Request $request){
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

			$pro_id = $request->session()->get('project');

			foreach($data as $index => $row){
				
				$cellCount = $index + 1;
				if(getSetting()->allow_zone == 1 && empty($row->zone)){
					throw new \Exception("Column[A{$cellCount}] is empty");
				}

                if(getSetting()->allow_zone == 1 && !$zone = SystemData::where(['name' => $row->zone,'type' => 'ZN'])->first()){
					throw new \Exception("Column[A{$cellCount}] Zone[{$row->zone}] not found");
				}

				if(getSetting()->allow_block == 1 && empty($row->block)){
					throw new \Exception("Column[B{$cellCount}] is empty");
				}

                if(getSetting()->allow_block == 1 && !$block = SystemData::where(['name' => $row->block,'type' => 'BK'])->first()){
					throw new \Exception("Column[B{$cellCount}] Block[{$row->block}] not found");
				}

				if(empty($row->street)){
					throw new \Exception("Column[C{$cellCount}] is empty");
				}

                if(!$street = SystemData::where(['name' => $row->street,'type' => 'ST'])->first()){
					throw new \Exception("Column[C{$cellCount}] Street[{$row->street}] not found");
				}

                if(empty($row->house)){
					throw new \Exception("Column[D{$cellCount}] is empty");
				}

                if(!$house = House::where(['house_no' => $row->house])->first()){
					throw new \Exception("Column[DD{$cellCount}] House[{$row->house}] not found");
				}

                if(empty($row->percent)){
					throw new \Exception("Column[E{$cellCount}] is empty");
				}

                $code = '';
                $usageFormulaColumn = [
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $usageFormula = UsageFormula::select(['*']);

                if(getSetting()->allow_zone == 1 && !empty($zone)){
                    $code .= "{$zone->name}-";
                    $usageFormulaColumn = array_merge($usageFormulaColumn,['zone_id' => $zone->id]);
                    $usageFormula = $usageFormula->where('zone_id',$zone->id);
                }

                if(getSetting()->allow_block == 1 && !empty($block)){
                    $code .= "{$block->name}-";
                    $usageFormulaColumn = array_merge($usageFormulaColumn,['block_id' => $block->id]);
                    $usageFormula = $usageFormula->where('block_id',$block->id);
                }

                if($street){
                    $code .= "{$street->name}-";
                    $usageFormulaColumn = array_merge($usageFormulaColumn,['street_id' => $street->id]);
                    $usageFormula = $usageFormula->where('street_id',$street->id);
                }

                $usageFormulaColumn = array_merge($usageFormulaColumn,['code' => $code]);

                if(!$usageFormula = $usageFormula->first()){
                    if(!$usageFormulaID = DB::table('usage_formulas')->insertGetId($usageFormulaColumn)){
                        DB::rollback();
                        throw new \Exception("UsageFormula not insert.");
                    }

                    $usageFormula = UsageFormula::find($usageFormulaID);
                }

                $usageFormulaDetailColumn = [
                    'formula_id' => $usageFormula->id,
                    'house_id'   => $house->id,
                    'percentage' => $row->percent,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                if(!$usageFormulaDetail = UsageFormulaDetail::where(['formula_id' => $usageFormula->id,'house_id' => $house->id])->first()){
                    $usageFormulaDetailColumn = array_merge($usageFormulaDetailColumn,['created_by' => Auth::user()->id,'created_at' => date('Y-m-d H:i:s')]);
                    if(!$usageFormulaDetailID = DB::table('usage_formula_details')->insertGetId($usageFormulaDetailColumn)){
                        DB::rollback();
                        throw new \Exception("UsageFormulaDetail not insert.");
                    }
                }else{
                    DB::rollback();
                    throw new \Exception("UsageFormulaDetail at House[{$house->house_no}] was exist please, recheck again.");
                    // DB::table('usage_formula_details')->where(['id' => $usageFormulaDetail->id])->update($usageFormulaDetailColumn);
                }
			}
			DB::commit();
			return redirect('usageFormula')->with("success", trans('lang.upload_success'));
		} catch (\Exception $ex) {
			DB::rollback();
			return redirect('usageFormula')->with("error", $ex->getMessage());
		}
    }

    public function destroy(Request $request,$id){
        try {
			DB::beginTransaction();
			if(!$usageFormula = UsageFormula::find($id)){
                throw new \Exception("UsageFormula[{$id}] not found.");
            }
			$data = [
                'status' => 0,
                'updated_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s')
            ];
			DB::table('usage_formulas')->where(['id'=>$id])->delete();
            DB::table('usage_formula_details')->where(['formula_id'=>$id])->delete();
			DB::commit();
			return redirect('usageFormula')->with('success',trans('lang.delete_success'));
		} catch (\Exception $e) {
			DB::rollback();
			return redirect('usageFormula')->with('error',trans('lang.delete_error').' '.$e->getMessage().' '.$e->getLine());
		}
    }
}
