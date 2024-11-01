<?php

if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class FB_Feeds_List_Table extends WP_List_Table {

    function __construct() {
        parent::__construct(array(
            'singular' => 'feed_list', //Singular label
            'plural' => 'feeds_list', //plural label, also this well be one of the table css class
            'ajax' => false //We won't support Ajax for this table
        ));
    }

    function column_title($item) {

        //Build row actions
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&feed=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['ID']),
            'delete' => sprintf('<a href="?page=%s&action=%s&feed=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['ID']),
        );

        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s', $item['title'], $item['ID'], $this->row_actions($actions)
        );
    }

    function get_columns() {

        return $columns = array(
            'id' => '<input type="checkbox" />',
            'feed_name' => __('Name'),
            'feed_type' => __('Type'),
            'published' => __('Published'),
            'edit' => __('Edit'),
            'delete' => __('Delete')
        );
    }

    /**
     * Decide which columns to activate the sorting functionality on
     * @return array $sortable, the array of columns that can be sorted by the user
     */
    public function get_sortable_columns() {
        return $sortable = array(
            'id' => array('id', true),
            'feed_name' => array('feed_name', true),
            'feed_type' => array('feed_type', true),
            'published' => array('published', true)
        );
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    /**
     * Prepare the table with different parameters, pagination, columns and table elements
     */
    function prepare_items() {
        global $wpdb, $_wp_column_headers;
        $screen = get_current_screen();


        /* -- Preparing your query -- */
        $query = "SELECT * FROM " . $wpdb->prefix . FB_FEED_TABLE;

        /* -- Ordering parameters -- */
        //Parameters that are going to be used to order the result
        $orderby = !empty($_GET["orderby"]) ? $_GET["orderby"] : 'ASC';
        $order = !empty($_GET["order"]) ? $_GET["order"] : '';
        if (!empty($orderby) & !empty($order)) {
            $query.=' ORDER BY ' . $orderby . ' ' . $order;
        }

        /* -- Pagination parameters -- */
        //Number of elements in your table?
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        //How many to display per page?
        $perpage = 5;
        //Which page is this?
        $paged = !empty($_GET["paged"]) ? $_GET["paged"] : '';
        //Page Number
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }
        //How many pages do we have in total?
        $totalpages = ceil($totalitems / $perpage);
        //adjust the query to take pagination into account
        if (!empty($paged) && !empty($perpage)) {
            $offset = ($paged - 1) * $perpage;
            $query.=' LIMIT ' . (int) $offset . ',' . (int) $perpage;
        }

        /* -- Register the pagination -- */
        $this->set_pagination_args(array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ));
        //The pagination links are automatically built according to those parameters

        /* -- Register the Columns -- */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        /* -- Fetch the items -- */

        $query = esc_sql($query);
        $this->items = $wpdb->get_results($query);
    }

    /**
     * Display the rows of records in the table
     * @return string, echo the markup of the rows
     */
    function display_rows() {

        //Get the records registered in the prepare_items method
        $records = $this->items;

        //Get the columns registered in the get_columns and get_sortable_columns methods
        list( $columns, $hidden ) = $this->get_column_info();

        //Loop for each record
        if (!empty($records)) {
            foreach ($records as $rec) {

                //Open the line
                echo '<tr id="record_' . $rec->id . '">';
                foreach ($columns as $column_name => $column_display_name) {
                    //Style attributes for each col
                    $class = "class='$column_name column-$column_name'";
                    $style = "";
                    if (in_array($column_name, $hidden))
                        $style = ' style="display:none;"';
                    $attributes = $class . $style;

                    //edit link
                    $editlink = '/wp-admin/link.php?action=edit&link_id=' . (int) $rec->id;

                    //Display the cell
                    switch ($column_name) {
                        case "id": echo '<td ' . $attributes . '><span style="margin-left:6px;"><input type="checkbox" name="id" value="' . stripslashes($rec->id) . '" />' . stripslashes($rec->id) . '</span></td>';
                            break;
                        case "feed_name": echo '<td ' . $attributes . '>' . stripslashes($rec->feed_name) . '</td>';
                            break;
                        case "feed_type": echo '<td ' . $attributes . '>' . stripslashes($rec->feed_type) . '</td>';
                            break;
                        case "published": echo '<td ' . $attributes . '>' . $rec->published . '</td>';
                            break;
                    }
                }




                echo '<td>' . sprintf('<a href="?page=%s&action=%s&feed=%s">Edit</a>', $_REQUEST['page'], 'edit', stripslashes($rec->id)) . '</td>';
                echo '<td>' . sprintf('<a href="?page=%s&action=%s&feed=%s">Delete</a>', $_REQUEST['page'], 'delete', stripslashes($rec->id)) . '</td>';

                //Close the line
                echo'</tr>';
            }
        } else {
            echo "records empty";
        }
    }

}
