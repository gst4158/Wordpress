/**
 * Include functions used by this file
 */
include_once 'feed_to_table_functions.php';

/**
 * Build Table
 */
$table_row_data = null;
$table_buckets = null;
$tr_class = null;
$returned_content = get_data( html_entity_decode($convert_url_feed_url) );
$array = json_decode($returned_content);
$counter = 0;

/**
 * Table Set up
 */
if ( is_array($array) && !empty($array) ) {
    foreach ($array as $key => $item) {
        // cleans stdclass object to array
        $clean_array = json_decode(json_encode($item), true);

        // Merge array values into single keys
        $merged_array_values = merge_array_values($convert_url_key_merge, $clean_array);

        // creates array of keys we want to remove
        $unset_array = explode("\n", str_replace("\r", "", strip_tags($convert_url_unwanted_keys)));

        // unset any unwanted keys
        $table_row_data = unset_keys($unset_array, $merged_array_values);

        // divide table data into buckets
        $table_buckets[set_data_buckets($table_row_data)][] = $table_row_data;

    }; // END foreach loop
}; // END !is_array($array)

/**
 * Set table heads
 * gets table row keys
 * rename any table he from user options $convert_url_key_rename
 */
$table_heads = array_keys($table_row_data); // get only the keys
$table_heads_num = count($table_heads); // table section-title col=NUM
$table_heads = set_table_custom_name($convert_url_key_rename, $table_heads); // User option to change table keys
$table_heads = set_table_heads($table_heads); // generate the html for table heads

/**
 * Creates table controls above table-wrapper
 * Only creates controls if there is more than one set of dates
 */
// create table controls
$table_controls =  set_table_controls($table_buckets);
if ( isset($table_controls) && count($table_buckets) > 1 ) {
    $output .=  '<div class="table-select-ctrl">';
    $output .=      '<p>'.__('Please select a date range:','default').'</p>';
    $output .=      '<select class="table-select-select" name="vc_table_date_range">';
    $output .=          $table_controls;
    $output .=      '</select>';
    $output .=  '</div>';
}

/**
 * Creates table for each date set (buckets)
 */
foreach($table_buckets as $key => $bucket) {
    $output .=  '<div class="vc-toggle-wrapper table-'.$key.'">';

    // create table html
    $output .=      '<div class="vc-table-to-feed table-wrap" data-mobile-cards="true">';
    $output .=          '<table>';

                            // set table heads
    $output .=              '<tr class="table-title">';
    $output .=                  $table_heads;
    $output .=              '</tr>';
    $output .=              '<tr class="section-title blank-title"><th colspan="' . $table_heads_num . '"></th></tr>';

                            // create table rows
    $output .=              set_table_content_row($bucket);

    $output .=          '</table>';
    $output .=      '</div>';
    $output .= '</div>';

}
echo $output;
