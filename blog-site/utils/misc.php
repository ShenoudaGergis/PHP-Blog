<?php

require_once "./utils/validate.php";

function generateParams($params) {
	$builder = "";
	foreach ($params as $key => $value) {
		if($value !== null) {
			if(is_array($value)) {
				$builder .= http_build_query([$key => $value]) . "&";
			} else $builder .= "$key=$value&";
		}
	}
	return $builder;
}

//-----------------------------------------------------------------------------

function fetchParams($target) {

	$values = [];
	foreach ($target as $key => $data) {
		if(gettype($_REQUEST[$key]) === "array") {
			if($key !== "tag") {
				$values[$key] = $data[1];
				continue;
			}
			$values[$key] = [];
			foreach ($_REQUEST[$key] as $v) {
				if(\validation\validate($data[0] , $v)) {
					$values[$key][] = ($data[0] === "number") ? intval($v) : $v;
				} else {$values[$key] = $data[1];break;}				
			}
			if(is_array($values[$key]) && count($values[$key]) === 0) $values[$key] = $data[1];	
		} else {
			if($key === "tag") {
				$values[$key] = $data[1];
				continue;
			}
			if(\validation\validate($data[0] , $_REQUEST[$key])) {
				$values[$key] = ($data[0] === "number") ? intval($_REQUEST[$key]) : $_REQUEST[$key];
			} else $values[$key] = $data[1];
		}
	}
	// var_export($values);
	return $values;
}

