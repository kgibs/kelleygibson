<?php
$paged = $_SESSION['s_consultant_page'] = (intval(get_query_var('paged')) ? intval(get_query_var('paged')) : 1);
$_SESSION['s_consultant_query'] = $_SERVER['QUERY_STRING'];
$join = array();
$where = array();
$vars = array();

$where[] = "$wpdb->posts.post_type = 'consultant'";
$where[] = "($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')";
$where[] = "($wpdb->postmeta.meta_key = 'consultant_membership')";

if( ! empty( $_REQUEST['s_consultant_title'] ) ) {
    $where[] = "$wpdb->posts.post_title LIKE %s";
    $vars[] = '%'.$_REQUEST['s_consultant_title'].'%';
}
if( ! empty( $_REQUEST['s_consultant_contact_name'] ) ) {
    $join[] = "INNER JOIN $wpdb->postmeta AS mt1 ON (wp_posts.ID = mt1.post_id)";
    $join[] = "INNER JOIN $wpdb->postmeta AS mt2 ON (wp_posts.ID = mt2.post_id)";
    $where[] = "((mt1.meta_key = 'consultant_contact_fname' AND CAST(mt1.meta_value AS CHAR) LIKE %s) 
            OR (mt2.meta_key = 'consultant_contact_lname' AND CAST(mt2.meta_value AS CHAR) LIKE %s))";
    $vars[] = '%'.$_REQUEST['s_consultant_contact_name'].'%';
    $vars[] = '%'.$_REQUEST['s_consultant_contact_name'].'%';
}
if( isset( $_REQUEST['s_consultant_category'] ) && 0 != $_REQUEST['s_consultant_category'] ) {
    $join[] = "INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id)";
    $where[] = "( $wpdb->term_relationships.term_taxonomy_id IN (%d) )";
    $vars[] = (int)$_REQUEST['s_consultant_category'];
}
if( ! empty( $_REQUEST['s_consultant_province'] ) ) {
    $join[] = "INNER JOIN $wpdb->postmeta AS mt3 ON (wp_posts.ID = mt3.post_id)";
    $where[] = "(mt3.meta_key = 'consultant_province' AND CAST(mt3.meta_value AS CHAR) = %s)";
    $vars[] = $_REQUEST['s_consultant_province'];
}
if( ! empty( $_REQUEST['s_consultant_city'] ) ) {
    $join[] = "INNER JOIN $wpdb->postmeta AS mt4 ON (wp_posts.ID = mt4.post_id)";
    $where[] = "(mt4.meta_key = 'consultant_city' AND CAST(mt4.meta_value AS CHAR) LIKE %s) )";
    $vars[] = '%'.$_REQUEST['s_consultant_city'].'%';
}
if( ! empty( $_REQUEST['s_consultant_alpha'] ) ) {
    $where[] = "$wpdb->posts.post_title LIKE %s";
    $vars[] = $_REQUEST['s_consultant_alpha'].'%';
}

$sql = "SELECT SQL_CALC_FOUND_ROWS $wpdb->posts.ID FROM $wpdb->posts 
        INNER JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ".
        implode(" ", $join).
        " WHERE ".implode(" AND ", $where).
        " GROUP BY $wpdb->posts.ID 
        ORDER BY $wpdb->postmeta.meta_value+0 DESC, $wpdb->posts.post_title ASC 
        LIMIT 0, 20";

/*
SELECT FOUND_ROWS()
 */

$results = $wpdb->get_results($wpdb->prepare($sql, $vars));