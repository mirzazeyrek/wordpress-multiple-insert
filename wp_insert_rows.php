<?php

	/**
	 *  A method for inserting multiple rows into the specified table
	 *  Updated to include the ability to Update existing rows by primary key
	 *  
	 *  Usage Example for insert: 
	 *
	 *  $insert_arrays = array();
	 *  foreach($assets as $asset) {
	 *  $time = current_time( 'mysql' );
	 *  $insert_arrays[] = array(
	 *  'type' => "multiple_row_insert",
	 *  'status' => 1,
	 *  'name'=>$asset,
	 *  'added_date' => $time,
	 *  'last_update' => $time);
	 *
	 *  }
	 *
	 *
	 *  wp_insert_rows($insert_arrays, $wpdb->tablename);
	 *
	 *  Usage Example for update:
	 *
	 *  wp_insert_rows($insert_arrays, $wpdb->tablename, true, "primary_column");
	 *
	 *
	 * @param array $row_arrays
	 * @param string $wp_table_name
	 * @param boolean $update
	 * @param string $primary_key
	 * @return false|int
	 *
	 * @author	Ugur Mirza ZEYREK
	 * @contributor Travis Grenell
	 * @source http://stackoverflow.com/a/12374838/1194797
	 */

function wp_insert_rows($row_arrays = array(), $wp_table_name, $update = false, $primary_key = null) {
	global $wpdb;
	$wp_table_name = esc_sql($wp_table_name);
	// Setup arrays for Actual Values, and Placeholders
	$values        = array();
	$place_holders = array();
	$query         = "";
	$query_columns = "";
	
	$query .= "INSERT INTO `{$wp_table_name}` (";
	foreach ($row_arrays as $count => $row_array) {
		foreach ($row_array as $key => $value) {
			if ($count == 0) {
				if ($query_columns) {
					$query_columns .= ", " . $key . "";
				} else {
					$query_columns .= "" . $key . "";
				}
			}
			
			$values[] = $value;
			
			$symbol = "%s";
			if (is_numeric($value)) {
				if (is_float($value)) {
					$symbol = "%f";
				} else {
					$symbol = "%d";
				}
			}
			if (isset($place_holders[$count])) {
				$place_holders[$count] .= ", '$symbol'";
			} else {
				$place_holders[$count] = "( '$symbol'";
			}
		}
		// mind closing the GAP
		$place_holders[$count] .= ")";
	}
	
	$query .= " $query_columns ) VALUES ";
	
	$query .= implode(', ', $place_holders);
	
	if ($update) {
		$update = " ON DUPLICATE KEY UPDATE $primary_key=VALUES( $primary_key ),";
		$cnt    = 0;
		foreach ($row_arrays[0] as $key => $value) {
			if ($cnt == 0) {
				$update .= "$key=VALUES($key)";
				$cnt = 1;
			} else {
				$update .= ", $key=VALUES($key)";
			}
		}
		$query .= $update;
	}
	
	$sql = $wpdb->prepare($query, $values);
	if ($wpdb->query($sql)) {
		return true;
	} else {
		return false;
	}
}
