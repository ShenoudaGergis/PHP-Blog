<?php

namespace validation;

function validate($flag , ...$values) {
	switch ($flag) {
		case "email" :
			return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $values[0]);
			break;

		case "password" :
			# has at least one letter , number. Character should be letter , number or [!@#$%]
			# the length should be between 8 to 12
			return preg_match("/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}/", $values[0]);
			break;

		case "confirm" :
			return $values[0] === $values[1];
			break;

		case "number" :
			return preg_match("/^\d+$/" , $values[0]);
			break;
		
		default:
			return $values[0];
			break;
	}
}

// print(preg_match("/^df$/", "df"));