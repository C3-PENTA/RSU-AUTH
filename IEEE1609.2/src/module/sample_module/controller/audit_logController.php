<?php

namespace module\systemInfo\controller;
use classes\user\db\sql;
use classes\user\datatable\datatable;

class audit_logController
{
	use datatable;

	public function indexAction()
	{
	
	}


    public function serversideAction()
    {

		$dt_val = $this->dt_init($_POST);	
		$sql = new sql;

		// draw
		$data["draw"] = @$_POST["draw"];

		// order
		$order_column	 = @$dt_val["order"]["column"]+1;
		$order_direction = @$dt_val["order"]["dir"];
		$dt_order = (isset($order_column)  && $order_direction)? array($order_column." ".$order_direction) : null;

		// pagenation
		$pagenation_start = $dt_val["start"];
		$pagenation_length = ($dt_val["length"])? $dt_val["length"] : 10;
		$pagenation_range = ($pagenation_start)? ($pagenation_start/$pagenation_length) : 0;

		$where = "";

		//--- query::recordsTotal
		$query = array(
			"field"	=> array(
				 "count(*) as recordsTotal"	
			),
			"table"	=> array(
				"console.audit_log"	
			),
			"where" => $where
		);
		$sql->setQuery($query);
		$rows = $sql->select();
		$data["recordsTotal"] 		= $rows[0]["recordsTotal"];

		//--- query::filtering
		$filter_audit_code	= @$_POST["filter_audit_code"];
		$daterange_start		= @strtotime($_POST["daterange_start"]." 00:00:00");
		$daterange_end 			= @strtotime($_POST["daterange_end"]." 23:59:59");

		if($filter_audit_code){
			$where .= " and t1.audit_code like '".$filter_audit_code."'"; 
		}
		if($daterange_start){
			$where .= " and register_date >= '".date("Y-m-d H:i:s", $daterange_start)."'";	
		}
		if($daterange_end){
			$where .= " and register_date <= '".date("Y-m-d H:i:s", $daterange_end)."'";	
		}

		$query = array(
			"field"	=> array(
				 "count(*) as recordsFiltered"	
			),
			"table"	=> array(
				"console.audit_log t1",	
				"console.audit_log_code t2"
			),
			"join_type"	=> "inner join",
			"on"		=> "t1.audit_code = t2.audit_code",
			"where" => $where,
		);
		$sql->setQuery($query);
		$rows = $sql->select();
		$data["recordsFiltered"] 	= $rows[0]["recordsFiltered"];

		//--- query::get rows
		$query = array(
			"field"		=> array(
				 "t1.idx"	
				,"t1.admin_idx"
				,"t2.detail"	
				,"t1.register_date"
			),
			"table"		=> array(
				"console.audit_log t1",
				"console.audit_log_code t2"
			),
			"join_type"	=> "inner join",
			"on"		=> "t1.audit_code = t2.audit_code",
			"where"		=> $where,
			"order_by"	=> $dt_order,
			"group_by"	=> array(),
			"limit"		=> array(
				 $pagenation_range
				,$pagenation_length	
			),
		);
		$sql->setQuery($query);
		$rows = $sql->select();


		$data["data"] = "";
		for($i=0; $i<count($rows); $i++){
			$item = $rows[$i];

			$query1 = array(
				"field"	=> array("administrator"),	
				"table"	=> array("console.admin"),	
				"where"	=> " and admin_idx=".$item["admin_idx"],
				"limit" => array("1")
			);
			$sql->setQuery($query1);
			$rows1 = $sql->select();

			// result data
			$data["data"][$i] = array(
				$item["idx"],
				$rows1[0]["administrator"],
				$item["detail"],
				$item["register_date"],
			);
		}

		echo json_encode($data);

		die();
		
    }

}
?>
