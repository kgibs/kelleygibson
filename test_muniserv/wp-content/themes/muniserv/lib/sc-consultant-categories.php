<?php
// Return shortcode for list of consultant categories
function ms_consultant_categories_shortcode($atts) {
    
    extract(shortcode_atts(array(
        'class' => ''
	), $atts));
    
    $widgetOutput = '';
    $categories = get_terms('consultant_categories', array('parent'=>0, 'hide_empty'=>false)); 
    if($categories) {
        // Shortcode output
        ob_start();
        ?>
        <ul id="ms-consultant-categories" class="cf <?php echo $class; ?>">
            <?php foreach($categories as $category): ?>
            <li><a href="<?php echo site_url('/consultants/search/').'?s_consultant_category='.$category->term_id; ?>"><?php echo $category->name; ?></a></li><?php
            endforeach; ?>
        </ul>

        <?php
        $widgetOutput .= ob_get_clean();
    }
    
    return $widgetOutput;
}
add_shortcode('ms-consultant-categories', 'ms_consultant_categories_shortcode');