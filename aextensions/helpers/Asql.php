<?php

class Asql
{
	public static function makeDuplicateInsertSqlFromArray($arr, $tableName) {
		$str1 = '';
		$str2 = '';
		$update = '';
		foreach ( $arr as $k => $v ) {
			$str1 .= "`{$k}`,";
			$str2 .= "'{$v}',";
			$update .= "`$k` = '$v',";
		}
		$sql = "INSERT INTO `{$tableName}` (" . trim ( $str1, ', ' ) . ") VALUES (" . trim ( $str2, ', ' ) . ") ON DUPLICATE KEY UPDATE " . trim ( $update, "," );
		return $sql;
	}
}
