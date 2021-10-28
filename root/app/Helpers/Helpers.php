<?php
use App\Model\Setting;
use App\Model\Boq;
use App\Model\BoqHouse;
use App\Model\BoqItem;
use App\Model\SystemData;
use Illuminate\Support\Facades\Session;
use Intervention\Image\ImageManagerStatic as Image;

function getAllocateRef($ref){
	$pro_id = Session::get('project');
	$obj = collect(DB::select("SELECT MAX(pr_stocks.`alloc_ref`) AS max_val FROM pr_stocks WHERE pr_stocks.`delete` = 0 AND pr_stocks.`trans_ref` = 'O'AND pr_stocks.`trans_date` > '2000-01-01'AND pr_stocks.`pro_id` = $pro_id AND pr_stocks.`alloc_ref` LIKE '$ref%'"))->first();
	if($obj){
		$alloc_ref = formatZero(intval(str_replace($ref, '', $obj->max_val)) + 1, 4);
		$ref = $ref.$alloc_ref;
	}else{
		$ref = $ref.formatZero(1, 4);
	}
	return $ref;
}

function getMinQTY($item_id,$unit,$requestQty=0,$requestPrice = 0)
{
	$sql = "SELECT a.*, ($requestQty * a.unit_qty) AS qty, ($requestPrice / a.unit_qty) AS price FROM (SELECT (CASE WHEN (SELECT `pr_units`.`factor` FROM `pr_units` WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = $item_id)) != ''THEN (SELECT `pr_units`.`factor` FROM `pr_units` WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = $item_id)) ELSE 1 END ) AS unit_qty, (SELECT pr_units.`to_code` FROM pr_units WHERE pr_units.`from_code` = (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = $item_id) AND pr_units.`to_code` = (SELECT pr_units.`to_code` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`factor` = (SELECT MIN(pr_units.`factor`) FROM pr_units WHERE pr_units.`from_code` = '$unit') LIMIT 1)) AS min_unit_to_code) AS a "; 

	$result = collect(DB::select(DB::raw($sql)))->first();

	if ($result) {
		return (array)$result;
	}else{
		return [];
	}
}

function getItemCost($item_id,$unit,$requestQty=0,$requestPrice = 0)
{
	$costing         = [];
	$subtract        = 0;
	$addition        = 0;
	$additionalArray = [];
	$min_key         = 0;
	$max_key         = 0;
	$qty             = 0;
	$pro_id          = Session::get('project');
	$StockAccount    = getSetting()->stock_account;
	$MinQTY          = (array) getMinQTY($item_id,$unit,$requestQty,$requestPrice);
	$unit_qty = 1;

	if ($StockAccount=="FIFO") {
		$sql  = "CALL COSTING_FIFO({$pro_id},{$item_id});";
	}else{
		$sql  = "CALL COSTING_LIFO({$pro_id},{$item_id});";
	}

	if ($MinQTY) {
		if ($MinQTY["min_unit_to_code"]) {
			$unit = $MinQTY["min_unit_to_code"];
		}
		if($MinQTY["unit_qty"]){
			$unit_qty = $MinQTY["unit_qty"];
		}
		$requestQty = $MinQTY["qty"];
		$requestPrice = $MinQTY["price"];
	}
	// print_r($MinQTY);

	if (getSetting()->is_costing==1) {
		$CostingRs = DB::select($sql);
		
		if (count($CostingRs) > 0) {
			if ($StockAccount=='FIFO') {
				foreach ($CostingRs as $ck => $CostingRs) {
					if ($CostingRs->type_=='INV') {
						$subtract += (float)($CostingRs->qty);
					}elseif($CostingRs->type_=='ADJ' && $CostingRs->qty < 0){
						$subtract += (float)($CostingRs->qty * -1);
					}elseif($CostingRs->type_=='ENT'){
						$addition += floatval($CostingRs->qty);
						$additionalArray[] = [
							'id'           =>$CostingRs->id,
							'code'         =>$CostingRs->code,
							'trans_date'   =>$CostingRs->trans_date,
							'main_cost'    =>$CostingRs->main_cost,
							'item_id'      =>$CostingRs->item_id,
							'qty'          =>$CostingRs->qty,
							'unit'         =>$CostingRs->unit,
							'cost'         =>$CostingRs->cost,
							'warehouse_id' =>$CostingRs->warehouse_id,
							'type_'        =>$CostingRs->type_,
							'remain_qty'   =>$CostingRs->remain_qty,
							'stock_id'     =>$CostingRs->stock_id,
							'original_cost'         =>$CostingRs->original_cost,
							'unit_qty'		=>	$unit_qty,
							'request_qty'	=>  $requestQty
						];
					}
				}
				
				if (count($additionalArray) > 0) {
					$min_key = min(array_keys($additionalArray));
					$max_key = max(array_keys($additionalArray));
					$qty     = (float)($additionalArray[0]['qty'] - $subtract);
				}else{
					$qty = $qty - $subtract;
				}
				// print_r($additionalArray);exit;
				subtractOld : {
					if ($qty < 0) {
						$min_key++;
						if (isset($additionalArray[$min_key])) {
							$qty = (float)($additionalArray[$min_key]['qty'] - ($qty * -1));
							if ($min_key < $max_key) {
								$additionalArray[$min_key-1] = [
									'id'           =>$additionalArray[$min_key-1]['id'],
									'code'         =>$additionalArray[$min_key-1]['code'],
									'trans_date'   =>$additionalArray[$min_key-1]['trans_date'],
									'main_cost'    =>$additionalArray[$min_key-1]['main_cost'],
									'item_id'      =>$additionalArray[$min_key-1]['item_id'],
									'qty'          =>0,
									'unit'         =>$additionalArray[$min_key-1]['unit'],
									'cost'         =>$additionalArray[$min_key-1]['cost'],
									'warehouse_id' =>$additionalArray[$min_key-1]['warehouse_id'],
									'type_'        =>$additionalArray[$min_key-1]['type_'],
									'remain_qty'   =>$additionalArray[$min_key-1]['remain_qty'],
									'stock_id'     =>$additionalArray[$min_key-1]['stock_id'],
									'mainCost'	=> $additionalArray[$min_key-1]['main_cost'],
									'original_cost'	=> $additionalArray[$min_key-1]['original_cost'],
									'unit_qty'		=>	$additionalArray[$min_key-1]['unit_qty'],
									'request_qty'	=>  $additionalArray[$min_key-1]['request_qty'],
								];
								goto subtractOld;
							}else{
								$additionalArray[$min_key-1] = [
									'id'           =>$additionalArray[$min_key-1]['id'],
									'code'         =>$additionalArray[$min_key-1]['code'],
									'trans_date'   =>$additionalArray[$min_key-1]['trans_date'],
									'main_cost'    =>$additionalArray[$min_key-1]['main_cost'],
									'item_id'      =>$additionalArray[$min_key-1]['item_id'],
									'qty'          =>$additionalArray[$min_key-1]['qty'],
									'unit'         =>$additionalArray[$min_key-1]['unit'],
									'cost'         =>$additionalArray[$min_key-1]['cost'],
									'warehouse_id' =>$additionalArray[$min_key-1]['warehouse_id'],
									'type_'        =>$additionalArray[$min_key-1]['type_'],
									'remain_qty'   =>$additionalArray[$min_key-1]['remain_qty'],
									'stock_id'     =>$additionalArray[$min_key-1]['stock_id'],
									'mainCost'	=> $additionalArray[$min_key-1]['main_cost'],
									'original_cost'	=> $additionalArray[$min_key-1]['original_cost'],
									'unit_qty'		=>	$additionalArray[$min_key-1]['unit_qty'],
									'request_qty'	=>  $additionalArray[$min_key-1]['request_qty'],
								];
								goto findCost;
							}
						}
					}else{
						// print_r($additionalArray);exit;
						$additionalArray[$min_key] = [
							'id'           =>$additionalArray[$min_key]['id'],
							'code'         =>$additionalArray[$min_key]['code'],
							'trans_date'   =>$additionalArray[$min_key]['trans_date'],
							'main_cost'    =>$additionalArray[$min_key]['main_cost'],
							'item_id'      =>$additionalArray[$min_key]['item_id'],
							'qty'          =>$qty,
							'unit'         =>$additionalArray[$min_key]['unit'],
							'cost'         =>$additionalArray[$min_key]['cost'],
							'warehouse_id' =>$additionalArray[$min_key]['warehouse_id'],
							'type_'        =>$additionalArray[$min_key]['type_'],
							'remain_qty'   =>$additionalArray[$min_key]['remain_qty'],
							'stock_id'     =>$additionalArray[$min_key]['stock_id'],
							'mainCost'	=> $additionalArray[$min_key]['main_cost'],
							'original_cost'	=> $additionalArray[$min_key]['original_cost'],
							'unit_qty'		=>	$additionalArray[$min_key]['unit_qty'],
							'request_qty'	=>  $additionalArray[$min_key]['request_qty'],
						];
						goto findCost;
					}
				}
				findCost : {
					$oldRequest = $requestQty;
					$usedQty = 0;
					
					for($i=0;$i<count($additionalArray);$i++) { 
						if (isset($additionalArray[$i])) {
							$qty = (float)($additionalArray[$i]['qty'] - $requestQty);
							if ($qty < 0) {
								$requestQty = (float)($qty * -1);
								if ($oldRequest > $usedQty) {
									$usedQty += (float)($additionalArray[$i]['qty']);
									$costing[] = [
										'item_id' =>$additionalArray[$i]['item_id'],
										'qty'     =>$additionalArray[$i]['qty'],
										'unit'    =>$additionalArray[$i]['unit'],
										'cost'    =>$additionalArray[$i]['cost'],
										'remain_qty'    =>$additionalArray[$i]['remain_qty'],
										'stock_id'     =>$additionalArray[$i]['stock_id'],
										'mainCost'	=> $additionalArray[$i]['main_cost'],
										'original_cost'	=> $additionalArray[$i]['original_cost'],
										'unit_qty'		=>	$additionalArray[$i]['unit_qty'],
										'request_qty'	=>  $additionalArray[$i]['request_qty'],
									];
								}
							}else{
								if ($oldRequest > $usedQty) {
									$usedQty += (float)($requestQty);
									$costing[] = [
										'item_id' =>$additionalArray[$i]['item_id'],
										'qty'     =>$requestQty,
										'unit'    =>$additionalArray[$i]['unit'],
										'cost'    =>$additionalArray[$i]['cost'],
										'remain_qty'    =>$additionalArray[$i]['remain_qty'],
										'stock_id'     =>$additionalArray[$i]['stock_id'],
										'mainCost'	=> $additionalArray[$i]['main_cost'],
										'original_cost'	=> $additionalArray[$i]['original_cost'],
										'unit_qty'		=>	$additionalArray[$i]['unit_qty'],
										'request_qty'	=>  $additionalArray[$i]['request_qty'],
									];
								}
							}
						}
					}
				}
			}else{
				foreach ($CostingRs as $ck => $CostingRs) {
					if ($CostingRs->type_=='INV') {
						$subtract += $CostingRs->qty;
					}elseif($CostingRs->type_=='ADJ' && $CostingRs->qty < 0){
						$subtract += (float)($CostingRs->qty * -1);
					}elseif($CostingRs->type_=='ENT'){
						$additionalArray[] = [
							'id'           =>$CostingRs->id,
							'code'         =>$CostingRs->code,
							'trans_date'   =>$CostingRs->trans_date,
							'main_cost'    =>$CostingRs->main_cost,
							'item_id'      =>$CostingRs->item_id,
							'qty'          =>$CostingRs->qty,
							'unit'         =>$CostingRs->unit,
							'cost'         =>$CostingRs->cost,
							'warehouse_id' =>$CostingRs->warehouse_id,
							'type_'        =>$CostingRs->type_,
							'remain_qty'   =>$CostingRs->remain_qty,
							'stock_id'     =>$CostingRs->stock_id,
							'original_cost'	=> $CostingRs->original_cost,
							'unit_qty'		=>	$unit_qty,
							'request_qty'	=>  $requestQty
						];
					}
				}

				if (count($additionalArray) > 0) {
					$min_key = min(array_keys($additionalArray));
					$max_key = max(array_keys($additionalArray));
					$qty = (float)($additionalArray[$min_key]['qty'] - $subtract);
				}else{
					$qty = $qty - $subtract;
				}

				subtractOld_ : {
					if ($qty < 0) {
						$min_key++;
						if (isset($additionalArray[$min_key])) {
							$qty = (float)($additionalArray[$min_key]['qty'] - ($qty * -1));
							if ($min_key < $max_key) {
								$additionalArray[$min_key-1] = [
									'id'           =>$additionalArray[$min_key-1]['id'],
									'code'         =>$additionalArray[$min_key-1]['code'],
									'trans_date'   =>$additionalArray[$min_key-1]['trans_date'],
									'main_cost'    =>$additionalArray[$min_key-1]['main_cost'],
									'item_id'      =>$additionalArray[$min_key-1]['item_id'],
									'qty'          =>0,
									'unit'         =>$additionalArray[$min_key-1]['unit'],
									'cost'         =>$additionalArray[$min_key-1]['cost'],
									'warehouse_id' =>$additionalArray[$min_key-1]['warehouse_id'],
									'type_'        =>$additionalArray[$min_key-1]['type_'],
									'remain_qty'   =>$additionalArray[$min_key-1]['remain_qty'],
									'stock_id'     =>$additionalArray[$min_key-1]['stock_id'],
									'mainCost'	=> $additionalArray[$min_key-1]['main_cost'],
									'original_cost'	=> $additionalArray[$min_key-1]['original_cost'],
									'unit_qty'		=>	$additionalArray[$min_key-1]['unit_qty'],
									'request_qty'	=>  $additionalArray[$min_key-1]['request_qty'],
								];
								goto subtractOld_;
							}else{
								$additionalArray[$min_key-1] = [
									'id'           =>$additionalArray[$min_key-1]['id'],
									'code'         =>$additionalArray[$min_key-1]['code'],
									'trans_date'   =>$additionalArray[$min_key-1]['trans_date'],
									'main_cost'    =>$additionalArray[$min_key-1]['main_cost'],
									'item_id'      =>$additionalArray[$min_key-1]['item_id'],
									'qty'          =>$additionalArray[$min_key-1]['qty'],
									'unit'         =>$additionalArray[$min_key-1]['unit'],
									'cost'         =>$additionalArray[$min_key-1]['cost'],
									'warehouse_id' =>$additionalArray[$min_key-1]['warehouse_id'],
									'type_'        =>$additionalArray[$min_key-1]['type_'],
									'remain_qty'   =>$additionalArray[$min_key-1]['remain_qty'],
									'stock_id'     =>$additionalArray[$min_key-1]['stock_id'],
									'mainCost'	=> $additionalArray[$min_key-1]['main_cost'],
									'original_cost'	=> $additionalArray[$min_key-1]['original_cost'],
									'unit_qty'		=>	$additionalArray[$min_key-1]['unit_qty'],
									'request_qty'	=>  $additionalArray[$min_key-1]['request_qty'],
								];
								goto findCost_;
							}
						}
					}else{
						$additionalArray[$min_key] = [
							'id'           =>$additionalArray[$min_key]['id'],
							'code'         =>$additionalArray[$min_key]['code'],
							'trans_date'   =>$additionalArray[$min_key]['trans_date'],
							'main_cost'    =>$additionalArray[$min_key]['main_cost'],
							'item_id'      =>$additionalArray[$min_key]['item_id'],
							'qty'          =>$qty,
							'unit'         =>$additionalArray[$min_key]['unit'],
							'cost'         =>$additionalArray[$min_key]['cost'],
							'warehouse_id' =>$additionalArray[$min_key]['warehouse_id'],
							'type_'        =>$additionalArray[$min_key]['type_'],
							'remain_qty'   =>$additionalArray[$min_key]['remain_qty'],
							'stock_id'     =>$additionalArray[$min_key]['stock_id'],
							'mainCost'	=> $additionalArray[$min_key]['main_cost'],
							'original_cost'	=> $additionalArray[$min_key]['original_cost'],
							'unit_qty'		=>	$additionalArray[$min_key]['unit_qty'],
							'request_qty'	=>  $additionalArray[$min_key]['request_qty'],
						];
						goto findCost_;
					}
				}

				findCost_ : {
					$oldRequest = $requestQty;
					$usedQty = 0;
					for($i=0;$i<count($additionalArray);$i++) { 
						if (isset($additionalArray[$i])) {
							$qty = (float)($additionalArray[$i]['qty'] - $requestQty);
							if ($qty < 0) {
								$requestQty = (float)($qty * -1);
								if ($oldRequest > $usedQty) {
									$usedQty += (float)($additionalArray[$i]['qty']);
									$costing[] = [
										'item_id' =>$additionalArray[$i]['item_id'],
										'qty'     =>$additionalArray[$i]['qty'],
										'unit'    =>$additionalArray[$i]['unit'],
										'cost'    =>$additionalArray[$i]['cost'],
										'remain_qty'    =>$additionalArray[$i]['remain_qty'],
										'stock_id'     =>$additionalArray[$i]['stock_id'],
										'mainCost'	=> $additionalArray[$i]['main_cost'],
										'original_cost'	=> $additionalArray[$i]['original_cost'],
										'unit_qty'		=>	$additionalArray[$i]['unit_qty'],
										'request_qty'	=>  $additionalArray[$i]['request_qty'],
									];
								}
							}else{
								if ($oldRequest > $usedQty) {
									$usedQty += (float)($requestQty);
									$costing[] = [
										'item_id' =>$additionalArray[$i]['item_id'],
										'qty'     =>$requestQty,
										'unit'    =>$additionalArray[$i]['unit'],
										'cost'    =>$additionalArray[$i]['cost'],
										'remain_qty'    =>$additionalArray[$i]['remain_qty'],
										'stock_id'     =>$additionalArray[$i]['stock_id'],
										'mainCost'	=> $additionalArray[$i]['main_cost'],
										'original_cost'	=> $additionalArray[$i]['original_cost'],
										'unit_qty'		=>	$additionalArray[$i]['unit_qty'],
										'request_qty'	=>  $additionalArray[$i]['request_qty'],
									];
								}
							}
						}
					}
				}
			}
		}else{
			$costing[] = [
				'item_id' =>$item_id,
				'qty'     =>$requestQty,
				'unit'    =>$unit,
				'cost'    =>$requestPrice,
				'main_cost'	=> 0,
				'original_cost'	=> 0,
				'unit_qty'		=>	$unit_qty,
				'remain_qty'    =>0,
				'request_qty'     =>$requestQty,
				'stock_id'     =>0,
			];
		}
	}else{
		$costing[] = [
			'item_id' 		=>$item_id,
			'qty'     		=>$requestQty,
			'unit'    		=>$unit,
			'cost'    		=>$requestPrice,
			'main_cost'		=> 0,
			'original_cost'	=> 0,
			'unit_qty'		=>	$unit_qty,
			'remain_qty'    =>0,
			'request_qty'   =>$requestQty,
			'stock_id'     	=>0,
		];
	}
	return $costing;
}

function romanize($num) {
	$lookup = ['M'=>1000,'CM'=>900,'D'=>500,'CD'=>400,'C'=>100,'XC'=>90,'L'=>50,'XL'=>40,'X'=>10,'IX'=>9,'V'=>5,'IV'=>4,'I'=>1];
	$integer = intval($num);
	$result = '';
		foreach($lookup as $roman => $value){
			// Determine the number of matches
			$matches = intval($integer/$value);
		   
			// Add the same number of characters to the string
			$result .= str_repeat($roman,$matches);
		   
			// Set the integer to be the remainder of the integer and the value
			$integer = $integer % $value;
		   }
		   
		   // The Roman numeral should be built, return it
		   return $result;
		  
}

function getAllocateStock($warehouse_id, $item_id, $unit, $qty){
	
	$collectData = new \Illuminate\Support\Collection;
	$pro_id = Session::get('project');
	$objQty = collect(DB::select("SELECT (A.qty * A.qty_stock) AS qty_stock FROM (SELECT ($qty) AS qty, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = '$unit'AND pr_units.`to_code` = pr_items.`unit_stock`) AS qty_stock FROM pr_items WHERE pr_items.`id` = $item_id) AS A"))->first();
	if($objQty){
		$qty = $objQty->qty_stock;
	}
	$objStockIn = DB::select("SELECT C.ref_no, C.item_id, C.unit_stock, (C.qty * C.qty_stock) AS qty_stock FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS qty_stock FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_stocks.`ref_no`, pr_stocks.`item_id`, pr_stocks.`unit`, pr_stocks.`qty` FROM pr_stocks WHERE pr_stocks.`delete` = 0 AND pr_stocks.`trans_ref` = 'I'AND pr_stocks.`trans_date` > '2000-01-01'AND pr_stocks.`pro_id`=$pro_id AND pr_stocks.`warehouse_id` = $warehouse_id AND pr_stocks.`item_id` = $item_id) AS A) AS B) AS C");
	if(count($objStockIn)>0){
		foreach($objStockIn as $row){
			$objStockOut = collect(DB::select("SELECT D.item_id, D.unit_stock, SUM(D.qty_stock) AS qty_stock FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.qty_stock) AS qty_stock FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS qty_stock FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_stocks.`item_id`, pr_stocks.`unit`, pr_stocks.`qty` FROM pr_stocks WHERE pr_stocks.`delete` = 0 AND pr_stocks.`trans_ref` = 'O'AND pr_stocks.`trans_date` > '2000-01-01'AND pr_stocks.`pro_id` = $pro_id AND pr_stocks.`warehouse_id` = $warehouse_id AND pr_stocks.`item_id` = $item_id AND pr_stocks.`alloc_ref` LIKE '$row->ref_no%') AS A) AS B) AS C) AS D GROUP BY D.item_id, D.unit_stock"))->first();
			$stock_out = 0;
			if($objStockOut){
				$stock_out = floatval($objStockOut->qty_stock)*(-1);
			}
			if(floatval($row->qty_stock) > $stock_out){
				$qty_stock = floatval($row->qty_stock) - $stock_out;
				if($qty_stock >= $qty ){
					$collectData->push([
						'ref_no'	=> $row->ref_no,
						'item_id'	=> $row->item_id,
						'unit'		=> $row->unit_stock,
						'qty'		=> $qty,
					]);
					break;
				}else{
					$collectData->push([
						'ref_no'	=> $row->ref_no,
						'item_id'	=> $row->item_id,
						'unit'		=> $row->unit_stock,
						'qty'		=> $qty_stock,
					]);
					$qty = $qty - $qty_stock;
				}
			}
		}
	}
	return $collectData;
}

function getBOQs($id=NULL){
	$where = '';
	if($id && $id!=0){
		$where = ' and pr_boqs.id = '.$id;
	}
	// $sql = "SELECT `pr_boqs`.`id`, pr_boqs.`house_id`, 
	// (SELECT pr_houses.`house_no` FROM pr_houses WHERE pr_houses.`id` = pr_boqs.`house_id`) AS house_no, 
	// (SELECT pr_houses.`street_id` FROM pr_houses WHERE pr_houses.`id` = pr_boqs.`house_id` LIMIT 1) AS street_id, 
	// (SELECT pr_system_datas.`name` FROM pr_system_datas WHERE pr_system_datas.`type` = 'ST' AND pr_system_datas.`id` = (SELECT pr_houses.`street_id` FROM pr_houses WHERE pr_houses.`id` = pr_boqs.`house_id` LIMIT 1) LIMIT 1) AS street, 
	// pr_boqs.`line_no`, pr_boqs.`trans_date`, pr_boqs.trans_by AS trans_by_id, 
	// (SELECT pr_users.`name` FROM pr_users WHERE pr_users.`id` = pr_boqs.`trans_by`) AS `trans_by`, pr_boqs.`trans_type` FROM pr_boqs".$where; 
	// return DB::select($sql);
	$sql = "SELECT *,
		(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_boqs`.`zone_id`) AS zone_name,
		(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_boqs`.`block_id`) AS block_name,
		(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_boqs`.`building_id`) AS building_name,
		(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_boqs`.`street_id`) AS street_name,
		 pr_boqs.trans_by AS trans_by_id,
		 (SELECT pr_users.`name` FROM pr_users WHERE pr_users.`id` = pr_boqs.`trans_by`) AS `trans_by`
	FROM `pr_boqs` where pr_boqs.status = 1 ".$where;
	return DB::select($sql);
}
function getBOQExcel($id=NULL){
	$where = '';
	if($id && $id!=0){
		$where = ' and pr_boq_items.boq_house_id = '.$id.' GROUP BY pr_boq_items.id';
	}      
	$sql = "SELECT pr_boq_items.*,
	(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = pr_items.`cat_id`) AS item_type,	
	pr_items.`name`,
	pr_items.`desc`,
	pr_items.`code`	
	FROM pr_boq_items 
	JOIN pr_boqs
		ON pr_boqs.id = pr_boq_items.boq_id 
	JOIN pr_items
		ON pr_items.id=pr_boq_items.item_id	
	WHERE pr_boqs.status = 1 ".$where;
	return DB::select($sql);
}
function getBOQWorkType($id=NULL){
	$where = '';
	if($id && $id!=0){
		$where = ' and pr_boq_items.boq_house_id = '.$id.' GROUP BY pr_boq_items.working_type';
	}      
	$sql = "SELECT pr_boq_items.*,
	(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = pr_boq_items.`working_type`) AS working_type_name
	FROM pr_boq_items 
	JOIN pr_boqs
		ON pr_boqs.id = pr_boq_items.boq_id 
	WHERE pr_boqs.status = 1 ".$where;
	return DB::select($sql);
}
function getBOQExport($id=NULL){
	$where = '';
	if($id && $id!=0){
		$where = ' and pr_boqs.id = '.$id;
	}
	$sql = "SELECT
		pr_boqs.`boq_code`,	
		pr_boqs.trans_date,	
		(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_boqs`.`zone_id`) AS zone_name,
		(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_boqs`.`block_id`) AS block_name,
		(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_boqs`.`building_id`) AS building_name,
		(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_boqs`.`street_id`) AS street_name,
		(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_boqs`.`house_type`) AS house_type,
		pr_boq_items.`item_id`,
		(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = pr_items.`cat_id`) AS item_type,	
		pr_items.`name`,
		pr_items.`desc`,
		pr_items.`code`,
		pr_boq_items.`qty_add`,
		pr_boq_items.`qty_std`,
		pr_boq_items.`unit`,
		pr_boq_items.`working_type`,
		pr_boq_items.`cost`,
		(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = pr_boq_items.`working_type`) AS working_type,
		pr_houses.`house_no`,
		pr_houses.`house_desc`
		FROM
			pr_boq_items
		JOIN pr_boqs
			ON pr_boqs.id = pr_boq_items.boq_id
		JOIN pr_boq_houses
			ON pr_boq_houses.boq_id = pr_boqs.id
		JOIN pr_houses
			ON pr_houses.id = pr_boq_items.`house_id` 
		JOIN pr_items
			ON pr_items.id=pr_boq_items.item_id where pr_boqs.status = 1 ".$where;
	return DB::select($sql);
}
function getBOQReviseVersions($id=NULL){
	$where = '';
	if($id && $id!=0){
		$where = ' and pr_boqs.referent_id = '.$id;
	}
	// $sql = "SELECT `pr_boqs`.`id`, pr_boqs.`house_id`, 
	// (SELECT pr_houses.`house_no` FROM pr_houses WHERE pr_houses.`id` = pr_boqs.`house_id`) AS house_no, 
	// (SELECT pr_houses.`street_id` FROM pr_houses WHERE pr_houses.`id` = pr_boqs.`house_id` LIMIT 1) AS street_id, 
	// (SELECT pr_system_datas.`name` FROM pr_system_datas WHERE pr_system_datas.`type` = 'ST' AND pr_system_datas.`id` = (SELECT pr_houses.`street_id` FROM pr_houses WHERE pr_houses.`id` = pr_boqs.`house_id` LIMIT 1) LIMIT 1) AS street, 
	// pr_boqs.`line_no`, pr_boqs.`trans_date`, pr_boqs.trans_by AS trans_by_id, 
	// (SELECT pr_users.`name` FROM pr_users WHERE pr_users.`id` = pr_boqs.`trans_by`) AS `trans_by`, pr_boqs.`trans_type` FROM pr_boqs".$where; 
	// return DB::select($sql);
	$sql = "SELECT *,
		(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_boqs`.`zone_id`) AS zone_name,
		(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_boqs`.`block_id`) AS block_name,
		(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_boqs`.`building_id`) AS building_name,
		(SELECT pr_system_datas.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_boqs`.`street_id`) AS street_name,
		 pr_boqs.trans_by AS trans_by_id,
		 (SELECT pr_users.`name` FROM pr_users WHERE pr_users.`id` = pr_boqs.`trans_by`) AS `trans_by`

	FROM `pr_boqs` where pr_boqs.status = 0 ".$where;
	return DB::select($sql);
}
function getBoqWorkingType($id,$house_id = null){
	// $where = ['status'=>1];
	// $sql = "SELECT * FROM `pr_boq_items` JOIN `pr_system_datas` ON `pr_system_datas`.`id` = `pr_boq_items`.`working_type` WHERE `pr_boq_items`.`boq_id`=".$id;
	// if($house_id != null){
	// 	$sql = $sql." JOIN pr_boq_houses on pr_boq_items.boq_house_id = pr_boq_houses.house_id";
	// }
	// return DB::select($sql);
	// print_r($id);
	// print_r($house_id);

	$working_type = Boq::select(
		"boqs.id AS boq_id",
		DB::raw("(SELECT `pr_system_datas`.`name` FROM `pr_system_datas` WHERE `pr_system_datas`.`id` = `pr_boq_items`.`working_type`) AS working_type_name"),
	"boq_houses.boq_house_code",
	"boq_houses.house_id",
	"houses.house_no" ,
	"boq_items.working_type",
	"boq_items.working_type AS id")
	->join("boq_houses","boq_houses.boq_id","boqs.id")
	->join("boq_items","boq_items.boq_house_id","boq_houses.id")
	->join("houses","houses.id","boq_houses.house_id")->where("boqs.id",$id)->where("boq_houses.house_id",$house_id)->groupBy("boq_items.working_type")->get();
	return $working_type;
}
function getBoqHouses($id=null){
	$where = '';
	if($id && $id!=0){
		$where = ' AND `pr_boq_houses`.`boq_id` = '.$id;
	}
	$sql = "SELECT * ,
				(SELECT `pr_houses`.`house_no` FROM `pr_houses` WHERE `pr_houses`.id = `pr_boq_houses`.`house_id`) AS house,
				(SELECT `pr_houses`.`house_desc` FROM `pr_houses` WHERE `pr_houses`.id = `pr_boq_houses`.`house_id`) AS house_desc
			FROM `pr_boq_houses` WHERE pr_boq_houses.status = 1 ".$where;
	return DB::select($sql);
}

function getReviseBoqHouses($id=null){
	$where = '';
	if($id && $id!=0){
		$where = ' `pr_boq_houses`.`boq_id` = '.$id;
	}
	$sql = "SELECT * ,
				(SELECT `pr_houses`.`house_no` FROM `pr_houses` WHERE `pr_houses`.id = `pr_boq_houses`.`house_id`) AS house,
				(SELECT `pr_houses`.`house_desc` FROM `pr_houses` WHERE `pr_houses`.id = `pr_boq_houses`.`house_id`) AS house_desc
			FROM `pr_boq_houses` WHERE ".$where;
	return DB::select($sql);
}

function getBoqHouseItems($id,$house_id = null ,$working_type=null){
	$boqItems = BoqItem::select(
		'items.*',
		'boq_items.*',
		'boq_items.unit as boq_unit',
		'items.cat_id',
		'system_datas.name as working_type_name',
		DB::raw('SUM(pr_boq_items.qty_std) as total_qty')
	)->join('boq_houses','boq_houses.id','boq_items.boq_house_id')
	->join('system_datas','system_datas.id','boq_items.working_type')
	->join('items','items.id','boq_items.item_id')
	->where('boq_houses.boq_id',$id);
	if($house_id != null){
		$boqItems = $boqItems->where('boq_houses.house_id',$house_id);
	}
	if($working_type != null){
		$boqItems = $boqItems->where('boq_items.working_type',$working_type);
	}
	$boqItems = $boqItems->groupBy('boq_items.id')
	->orderBy('boq_items.working_type')->get();
	
	return $boqItems; 
}

function getBOQItems($id){
	$sql = "SELECT pr_boq_items.`id`, pr_boq_items.`item_id`, pr_boq_items.`house_id`, (SELECT pr_houses.house_no FROM pr_houses WHERE pr_houses.id = pr_boq_items.`house_id`) AS house_no, (SELECT pr_items.`code` FROM pr_items WHERE pr_items.`id` = pr_boq_items.`item_id`) AS `code`, (SELECT pr_items.`name` FROM pr_items WHERE pr_items.`id` = pr_boq_items.`item_id`) AS `name`, pr_boq_items.`unit`, pr_boq_items.`qty_std`, pr_boq_items.`qty_add` FROM pr_boq_items WHERE pr_boq_items.`boq_id` = $id"; return DB::select($sql);
}

function getBOQItemByWorkingType($id = null,$working_type = null){
	$sql = "SELECT * FROM `pr_boq_items` JOIN `pr_items` ON `pr_items`.`id`=`pr_boq_items`.`item_id` WHERE `pr_boq_items`.`boq_id` =".$id." AND `pr_boq_items`.`working_type` = ".$working_type;
	return DB::select($sql);
}

function getLineNo($id){
	$line_no = 1;
	$obj = collect(DB::select("SELECT (CASE WHEN A.line_no!='' THEN A.line_no+1 ELSE 1 END)AS line_no FROM(SELECT MAX(CAST(pr_boqs.`line_no` AS DECIMAL))AS line_no FROM pr_boqs WHERE pr_boqs.`house_id`='$id')AS A"))->first();
	if($obj){
		$line_no = $obj->line_no;
	}
	return formatZero($line_no, 3);
}

function upload($request,$controlName,$destination,$old_name=NULL)
{
	
	$photoFile = $request->file($controlName);
	$new_name = '';
	if($old_name){
		$new_name = $old_name;
	}else{
		$new_name = 'picture_'.date('Y_m_d_H_i_s_').$photoFile->getClientOriginalName();
		$photoFile->move($destination, $new_name);
	}
	
	/* $image_size = explode(',',getSetting()->image_size);
	$image_resize = Image::make($photoFile->getRealPath());
	$width        = $image_resize->getWidth();
	$height       = $image_resize->getHeight();
	if ($image_size[0] > 0 || $image_size[1] > 0) {
		$width  = $image_size[0];
		$height = $image_size[1];
	}
	$image_resize->resize($width,$height);
	$image_resize->save($destination.$newName); */
	
    return $new_name;
}

function isNullToZero($val){
	if($val=='' || $val==null){
		return 0;
	}else{
		return str_replace(",","",$val);
	}
}

function isNullToString($val){
	if($val){
		return '';
	}
	return $val;
}

function formatZero($val,$max){
	return str_pad($val, $max, '0', STR_PAD_LEFT);
}

function formatQty($qty)
{
	$formatted = "" . number_format(sprintf('%0.2f', preg_replace("/[^0-9.]/", "", $qty)), getSetting()->round_number);
    return $qty < 0 ? "-"."{$formatted}" : "{$formatted}";
}

function formatDollars($dollars)
{
	$formatted = "$" . number_format(sprintf('%0.2f', preg_replace("/[^0-9.]/", "", $dollars)), getSetting()->round_dollar);
    return $dollars < 0 ? "({$formatted})" : "{$formatted}";
}

function getSetting(){
	return Setting::find(1);
}

function hasRole($session_key)
{
	if (Session::get('owner')=='Y') {
		return true;
	}else if(Session::get('admin')=='Y' && $session_key!='owner'){
		return true;
	}else if(Session::get($session_key)=='Y'){
		return true;
	}else{
		return false;
	}
}

function getNotification(){
	$now_date   = date('Y-m-d H:i:s');
	$user_id = Auth::user()->id;
	$pro_id = Session::get('project');
	$OWNER = config("app.owner");
	$ADMIN = config("app.admin");
	$alert_list = [];
	
	if(hasRole('approve_request')){
		// $sql = "SELECT G.* FROM (SELECT T1.* FROM (SELECT PR.*, RO.role_id, RO.role_max_amount, DATEDIFF(PR.now_date, PR.created_at) AS day_ago FROM (SELECT (CASE WHEN TOP.max_amount > 0 THEN TOP.id ELSE DEP.id END ) AS `role_id`, (CASE WHEN TOP.min_amount = 0 THEN DEP.min_amount ELSE TOP.min_amount END ) AS `role_min_amount`, (CASE WHEN TOP.max_amount = 0 THEN DEP.max_amount ELSE TOP.max_amount END ) AS `role_max_amount`, TOP.dep_id FROM (SELECT `pr_roles`.`id`, pr_roles.`min_amount`, pr_roles.`max_amount`, pr_roles.`dep_id` FROM `pr_roles` WHERE pr_roles.`zone` = 1 AND pr_roles.`level` = 1) AS TOP LEFT JOIN (SELECT `pr_roles`.`id`, pr_roles.`min_amount`, pr_roles.`max_amount`, pr_roles.`dep_id` FROM `pr_roles` WHERE pr_roles.`zone` = 1 AND pr_roles.`level` = 2) AS DEP ON TOP.id = DEP.dep_id) AS RO INNER JOIN (SELECT B.id, B.ref_no, B.dep_id, B.created_at, B.now_date, SUM(B.amount) AS request_amount FROM (SELECT A.*, (pr_request_items.`qty` * pr_request_items.`price` ) AS amount FROM (SELECT pr_requests.`id`, pr_requests.`ref_no`, pr_requests.`dep_id`, pr_requests.`created_at`, ('$now_date')AS now_date FROM pr_requests WHERE pr_requests.`pro_id` = $pro_id AND pr_requests.`trans_status` IN (1, 2)) AS A INNER JOIN pr_request_items ON A.id = pr_request_items.`pr_id`) AS B GROUP BY B.id) AS PR ON (RO.dep_id = PR.dep_id OR RO.dep_id = 0 ) AND (RO.role_min_amount <= PR.request_amount ) AND RO.role_id NOT IN (SELECT pr_approve_requests.`role_id` FROM `pr_approve_requests` WHERE `pr_approve_requests`.`pr_id` = PR.id AND `pr_approve_requests`.`role_id` = RO.role_id AND `pr_approve_requests`.`reject` = 0)) AS T1 INNER JOIN (SELECT C.id, MIN(C.role_max_amount) AS role_max_amount_step FROM (SELECT PR.id, RO.role_max_amount FROM (SELECT (CASE WHEN TOP.max_amount > 0 THEN TOP.id ELSE DEP.id END ) AS `role_id`, (CASE WHEN TOP.min_amount = 0 THEN DEP.min_amount ELSE TOP.min_amount END ) AS `role_min_amount`, (CASE WHEN TOP.max_amount = 0 THEN DEP.max_amount ELSE TOP.max_amount END ) AS `role_max_amount`, TOP.dep_id FROM (SELECT `pr_roles`.`id`, pr_roles.`min_amount`, pr_roles.`max_amount`, pr_roles.`dep_id` FROM `pr_roles` WHERE pr_roles.`zone` = 1 AND pr_roles.`level` = 1) AS TOP LEFT JOIN (SELECT `pr_roles`.`id`, pr_roles.`min_amount`, pr_roles.`max_amount`, pr_roles.`dep_id` FROM `pr_roles` WHERE pr_roles.`zone` = 1 AND pr_roles.`level` = 2) AS DEP ON TOP.id = DEP.dep_id) AS RO INNER JOIN (SELECT B.id, B.dep_id, SUM(B.amount) AS request_amount FROM (SELECT A.*, (pr_request_items.`qty` * pr_request_items.`price` ) AS amount FROM (SELECT pr_requests.`id`, pr_requests.`dep_id` FROM pr_requests WHERE pr_requests.`pro_id` = $pro_id AND pr_requests.`trans_status` IN (1, 2)) AS A INNER JOIN pr_request_items ON A.id = pr_request_items.`pr_id`) AS B GROUP BY B.id) AS PR ON (RO.dep_id = PR.dep_id OR RO.dep_id = 0 ) AND (RO.role_min_amount <= PR.request_amount ) AND RO.role_id NOT IN (SELECT pr_approve_requests.`role_id` FROM `pr_approve_requests` WHERE `pr_approve_requests`.`pr_id` = PR.id AND `pr_approve_requests`.`role_id` = RO.role_id AND `pr_approve_requests`.`reject` = 0)) AS C GROUP BY C.id) AS T2 ON T1.id = T2.id AND T1.role_max_amount = T2.role_max_amount_step) AS G WHERE $user_id IN (".config('app.owner').",".config('app.admin').",(SELECT `pr_user_assign_roles`.`user_id` FROM `pr_user_assign_roles` WHERE `pr_user_assign_roles`.`role_id` = G.role_id))"; 
		$sql = "CALL PRApproval({$pro_id},{$user_id},{$OWNER},{$ADMIN});";
		$alert = DB::select($sql);
		
		if (count($alert) > 0) {
			foreach ($alert as $alert) {
				$alert_list[] = [
					'toastId'    	=>$alert->id,
					'ref_no'     	=>$alert->ref_no,
					'day_ago' 		=>$alert->day_ago,
					'alert' 		=>'Purchase Request Alert',
					'type'		 	=>'PR',
					'url'			=> url('approve/request'),
				];
			}
		}
	}
	
	if(hasRole('approve_order')){
		// $sql = "SELECT G.* FROM (SELECT T1.* FROM (SELECT PO.*, RO.role_id, RO.role_max_amount, DATEDIFF(PO.now_date, PO.created_at) AS day_ago FROM (SELECT (CASE WHEN TOP.max_amount > 0 THEN TOP.id ELSE DEP.id END ) AS `role_id`, (CASE WHEN TOP.min_amount = 0 THEN DEP.min_amount ELSE TOP.min_amount END ) AS `role_min_amount`, (CASE WHEN TOP.max_amount = 0 THEN DEP.max_amount ELSE TOP.max_amount END ) AS `role_max_amount`, TOP.dep_id FROM (SELECT `pr_roles`.`id`, pr_roles.`min_amount`, pr_roles.`max_amount`, pr_roles.`dep_id` FROM `pr_roles` WHERE pr_roles.`zone` = 2 AND pr_roles.`level` = 1) AS TOP LEFT JOIN (SELECT `pr_roles`.`id`, pr_roles.`min_amount`, pr_roles.`max_amount`, pr_roles.`dep_id` FROM `pr_roles` WHERE pr_roles.`zone` = 2 AND pr_roles.`level` = 2) AS DEP ON TOP.id = DEP.dep_id) AS RO INNER JOIN (SELECT pr_orders.`id`, pr_orders.`dep_id`, pr_orders.`ref_no`, pr_orders.`grand_total`, pr_orders.`created_at`, ('$now_date') AS now_date FROM pr_orders WHERE pr_orders.`pro_id` = $pro_id AND pr_orders.`trans_status` IN (1, 2)) AS PO ON (RO.dep_id = PO.dep_id OR RO.dep_id = 0 ) AND (RO.role_min_amount <= PO.grand_total ) AND RO.role_id NOT IN (SELECT pr_approve_orders.`role_id` FROM `pr_approve_orders` WHERE `pr_approve_orders`.`po_id` = PO.id AND `pr_approve_orders`.`role_id` = RO.role_id)) AS T1 INNER JOIN (SELECT C.id, MIN(C.role_max_amount) AS role_max_amount_step FROM (SELECT PO.id, RO.role_max_amount FROM (SELECT (CASE WHEN TOP.max_amount > 0 THEN TOP.id ELSE DEP.id END ) AS `role_id`, (CASE WHEN TOP.min_amount = 0 THEN DEP.min_amount ELSE TOP.min_amount END ) AS `role_min_amount`, (CASE WHEN TOP.max_amount = 0 THEN DEP.max_amount ELSE TOP.max_amount END ) AS `role_max_amount`, TOP.dep_id FROM (SELECT `pr_roles`.`id`, pr_roles.`min_amount`, pr_roles.`max_amount`, pr_roles.`dep_id` FROM `pr_roles` WHERE pr_roles.`zone` = 2 AND pr_roles.`level` = 1) AS TOP LEFT JOIN (SELECT `pr_roles`.`id`, pr_roles.`min_amount`, pr_roles.`max_amount`, pr_roles.`dep_id` FROM `pr_roles` WHERE pr_roles.`zone` = 2 AND pr_roles.`level` = 2) AS DEP ON TOP.id = DEP.dep_id) AS RO INNER JOIN (SELECT pr_orders.`id`, pr_orders.`dep_id`, pr_orders.`grand_total` FROM pr_orders WHERE pr_orders.`pro_id` = $pro_id AND pr_orders.`trans_status` IN (1, 2)) AS PO ON (RO.dep_id = PO.dep_id OR RO.dep_id = 0 ) AND (RO.role_min_amount <= PO.grand_total ) AND RO.role_id NOT IN (SELECT pr_approve_orders.`role_id` FROM `pr_approve_orders` WHERE `pr_approve_orders`.`po_id` = PO.id AND `pr_approve_orders`.`role_id` = RO.role_id)) AS C GROUP BY C.id) AS T2 ON T1.id = T2.id AND T1.role_max_amount = T2.role_max_amount_step) AS G WHERE $user_id IN (".config('app.owner').",".config('app.admin').",(SELECT `pr_user_assign_roles`.`user_id` FROM `pr_user_assign_roles` WHERE `pr_user_assign_roles`.`role_id` = G.role_id))"; 
		$sql = "CALL POApproval({$pro_id},{$user_id},{$OWNER},{$ADMIN});";
		$alert = DB::select($sql);
		
		if (count($alert) > 0) {
			foreach ($alert as $alert) {
				$alert_list[] = [
					'toastId'    	=>$alert->id,
					'ref_no'     	=>$alert->ref_no,
					'day_ago' 		=>$alert->day_ago,
					'alert' 		=>'Purchase Order Alert',
					'type'		 	=>'PO',
					'url'			=> url('approve/order'),
				];
			}
		}
	}
	
	if(hasRole('stock_balance')){
		// $sql = "SELECT F.warehouse_id, (SELECT pr_warehouses.`name` FROM pr_warehouses WHERE pr_warehouses.`id` = F.warehouse_id) AS warehouse, F.item_id, (SELECT pr_items.`code` FROM pr_items WHERE pr_items.`id` = F.item_id) AS ref_no, F.stock_qty FROM (SELECT E.warehouse_id, E.item_id, SUM(E.stock_qty) AS stock_qty FROM (SELECT D.warehouse_id, D.item_id, D.stock_qty FROM (SELECT C.item_id, C.unit_stock, (C.qty * C.stock_qty) AS stock_qty, C.warehouse_id FROM (SELECT B.*, (SELECT pr_units.`factor` FROM pr_units WHERE pr_units.`from_code` = B.unit AND pr_units.`to_code` = B.unit_stock) AS stock_qty FROM (SELECT A.*, (SELECT pr_items.`unit_stock` FROM pr_items WHERE pr_items.`id` = A.item_id) AS unit_stock FROM (SELECT pr_stocks.`item_id`, pr_stocks.`unit`, pr_stocks.`qty`, pr_stocks.`warehouse_id` FROM pr_stocks WHERE pr_stocks.`delete` = 0 AND pr_stocks.`pro_id` = $pro_id AND (SELECT pr_items.`alert_qty` FROM pr_items WHERE pr_items.`id`=pr_stocks.`item_id`) > -1 AND (SELECT `pr_stock_alerts`.`id` FROM pr_stock_alerts WHERE pr_stock_alerts.`item_id`=pr_stocks.`item_id` AND pr_stock_alerts.`warehouse_id`=pr_stocks.`warehouse_id` LIMIT 1) !='') AS A) AS B) AS C) AS D) AS E GROUP BY E.warehouse_id, E.item_id) AS F WHERE F.stock_qty <= 0"; 
		$sql = "CALL AlertQTYStock({$pro_id});";
		$alert = DB::select($sql);
		
		if (count($alert) > 0) {
			foreach ($alert as $alert) {
				$alert_list[] = [
					'toastId'    	=>$alert->item_id,
					'ref_no'     	=>$alert->code,
					'day_ago' 		=>0,
					'alert' 		=>'Stock Balance Alert',
					'type'		 	=>'ST',
					'url'			=> url('stock/balance'),
				];
			}
		}
	}
	
	return collect($alert_list);
}