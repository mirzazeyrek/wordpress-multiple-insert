<?php
 
	/**
     * A method for inserting multiple rows into the specified table
     * 
     *
     *  $insert_arrays = array();
     *  foreach($assets as $asset) {
     *
     *  $insert_arrays[] = array(
     *  'type' => "multiple_row_insert",
     *  'status' => 1,
     *  'name'=>$asset,
     *  'added_date' => current_time( 'mysql' ),
     *  'last_update' => current_time( 'mysql' ));
     *
     *  }
     *
     *  wp_insert_rows($insert_arrays);
     *
     * @param array $row_arrays
     * @return false|int
     *
     * @author	Ugur Mirza ZEYREK
     * @source http://stackoverflow.com/a/12374838/1194797
     */
	 
    function wp_insert_rows($row_arrays = array(), $wp_table_name = null) {
        global $wpdb;
        global $wp_table_name;
        $wp_table_name = esc_sql($wp_table_name);
        // Setup arrays for Actual Values, and Placeholders
        $values = array();
        $place_holders = array();
        $query = "";
        $query_columns = "";

        $query .= "INSERT INTO {$wp_table_name} (";

                foreach($row_arrays as $count => $row_array)
                {

                    foreach($row_array as $key => $value) {

                        if($count == 0) {
                            if($query_columns) {
                            $query_columns .= ",".$key."";
                            } else {
                            $query_columns .= "".$key."";
                            }
                        }

                        $values[] =  $value;

                        if(is_numeric($value)) {
                            if(isset($place_holders[$count])) {
                            $place_holders[$count] .= ", '%d'";
                            } else {
                            $place_holders[$count] .= "( '%d'";
                            }
                        } else {
                            if(isset($place_holders[$count])) {
                            $place_holders[$count] .= ", '%s'";
                            } else {
                            $place_holders[$count] .= "( '%s'";
                            }
                        }
                    }
                            // mind closing the GAP
                            $place_holders[$count] .= ")";
                }

        $query .= " $query_columns ) VALUES ";

        $query .= implode(', ', $place_holders);

        if($wpdb->query($wpdb->prepare($query, $values))){
            return true;
        } else {
            return false;
        }

    }