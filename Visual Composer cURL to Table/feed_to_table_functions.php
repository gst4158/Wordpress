<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
}

/**
 * Gets data from url
 * @param $url
 * @return mixed
 */
function get_data($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

/**
 * Takes any designated keys (set by user inside VC) and merges them into a single key
 * @param $array
 * @param $array_merged_into
 * @return array|bool
 */
function merge_array_values($array, $array_merged_into) {
    if ( !isset($array) || !isset($array_merged_into) )
        return false;

    foreach($array as $item) {

        // explode keys and set values
        $exploded = array();
        $explode = preg_split("/[\s,]+/", $item['convert_url_table_merge_keys']);
        foreach ( $explode as $value ) {
            $exploded[] = $array_merged_into[$value]; // Readies original values for string conversion.
            unset($array_merged_into[$value]); // remove keys that we're changing
        }

        // merge keys as string separated by option
        $merged_keys = implode($item['convert_url_table_merge_option'], $exploded);
        $array_merged_into[$item['convert_url_table_merge_label']] = $merged_keys;

        // move key to start
        if ( $item['convert_url_table_merge_label_location'] === 'true' ) {
            $temp = array($item['convert_url_table_merge_label'] => $array_merged_into[$item['convert_url_table_merge_label']]);
            unset($array_merged_into[$item['convert_url_table_merge_label']]);
            $array_merged_into = $temp + $array_merged_into;
        }
    }

    return $array_merged_into;
}

/**
 * Removes any unwanted array keys
 * @param $array
 * @param $original_array
 * @return bool
 */
function unset_keys($array, $original_array) {
    if ( !isset($array) || !isset($original_array) )
        return false;

    foreach ($array as $item) {
        unset($original_array[$item]);
    }

    return $original_array;
}

/**
 * Updates table keys with user input replacements
 * @param $input_array
 * @param $replacement_array
 * @return array|bool
 */
function set_table_custom_name($input_array, $replacement_array) {
    if ( !isset($input_array) || !isset($replacement_array) )
        return false;

    // explode values
    $input_array = explode("\n", str_replace("\r", "", strip_tags($input_array))); // create array out of each line break

    foreach ( $input_array as $input_array ) {
        // remove white space
        $string = preg_replace('/\s+/', '', strip_tags($input_array)); // removes white space

        // create list of new values
        list($old_value, $new_value) = explode('|', $string);

        // replace keys
        $replacement_array = array_replace($replacement_array,
            array_fill_keys(
            array_keys($replacement_array, $old_value),
                $new_value
            )
        );
    }

    return $replacement_array;
};

/**
 * Divides an array into past, current, and future buckets
 * @param $array
 * @return array|bool
 */
function set_data_buckets($array) {
    if ( !is_array($array) )
        return false;

    // bucket arrays
    $bucket = null;

    // Date variables
    $time_string = null;
    $date_format = __( get_option( 'date_format' ) ) . ' ' .  __( get_option( 'time_format' ) );
    $date_current = current_time( 'mysql' );
    $days_to_extend = 8; // change the number of days out here

    // gets $days_to_extend days out from current date
    $current_date_plus = new DateTime(strtotime(strtotime($date_current)));
    $current_date_plus->add(new DateInterval('P'.$days_to_extend.'D'));
    $current_date_plus = $current_date_plus->format('Y-m-d H:i:s');
    $current_date_plus_str = strtotime($current_date_plus);

    $current_date_neg = new DateTime(strtotime(strtotime($date_current)));
    $current_date_neg->sub(new DateInterval('P'.$days_to_extend.'D'));
    $current_date_neg = $current_date_neg->format('Y-m-d H:i:s');
    $current_date_neg_str = strtotime($current_date_neg);

    /**
     * Checks if value is a date or not
     * If is date places whole item into a bucket
     */
    foreach ( $array as $item ) {

        // get event time and normalize it
        $time_string = sprintf( str_replace('/', '-', $item),
            esc_attr( get_the_date( 'c' ) ),
            esc_html(  date_i18n( $date_format, strtotime( get_date_from_gmt( get_the_date( 'Y-m-d H:i:s' ), 'j F, Y h:i a' ) ) ) )
        );
        $time_string = strtotime($time_string);

        // if this is a field with a date inside
        if ( !empty($time_string) ) {
            // if today's date or $days_to_extend out from it
            if ( ($time_string > $current_date_neg_str) && ($time_string < $current_date_plus_str) ) {
               $bucket = __('current');
            }
            // if in the future
            elseif ( $time_string > $current_date_plus_str ) {
               $bucket = __('future');
            }
            // else, it's a paste item
            else {
               $bucket = __('past');
            }

        };
    }

    return $bucket;
}

/**
 * Get the keys from buckets array (should be past, current, future) and set them as dropdown html option elements
 * @param $array
 * @return bool|string
 */
function set_table_controls($array) {
    if ( !is_array($array) )
        return false;

    $html = '';
    $table_keys = array_keys($array);

    // create dropdown options
    foreach ($table_keys as $item) {
        $html .= '<option value="'.$item.'">'.ucfirst($item).' '.__('Dates').'</option>';
    }
    return $html;
}

/**
 * Creates the table row heads
 * @param $array
 * @return bool|string
 */
function set_table_heads($array) {
    if ( !is_array($array) )
        return false;

    // set vars
    $html = '';

    // foreach loop
    foreach ($array as $item) {
        $row_title = str_replace(array('_'), ' ', $item);
        $html .= '<th>'.__($row_title, 'default').'</th>';
    }

    return $html;
}

/**
 * Creates individual <tr> segments
 * @param $array
 * @return string
 */
function set_table_content_row($array) {
    $html = '';
    $counter = 0;

    // loop through array, creating html table rows creating <tr>'s
    foreach ( $array as $item ) {
        $tr_class = $counter % 2 == 0 ? 'light' : ''; $counter++;
        $html .= '<tr class="'.$tr_class.'">';
        $html .=    set_table_content_td($item);
        $html .= '</tr>';
    } // END foreach loop

    // return html
    return $html;
}

/**
 * Creates individual <td> segments
 * @param $array
 * @return string
 */
function set_table_content_td($array) {
    $html = '';
    // loop through each row's data creating <td>'s
    foreach ( $array as $item ) {
        $html .=  '<td>';
        $html .=      __($item, 'default');
        $html .=  '</td>';
    }

    // return html
    return $html;
}
