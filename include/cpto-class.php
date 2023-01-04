<?php

    class CPTO 
        {
            var $current_post_type = null;
            
            function CPTO() 
                {
                    add_action( 'admin_init', array(&$this, 'registerFiles'), 11 );
                    add_action( 'admin_init', array(&$this, 'checkPost'), 10 );
                    add_action( 'admin_menu', array(&$this, 'addMenu') );
                    
                    
                    
                    add_action( 'wp_ajax_update-custom-type-order', array(&$this, 'saveAjaxOrder') );
                }

            function registerFiles() 
                {
                    if ( $this->current_post_type != null ) 
                        {
                            wp_enqueue_script('jQuery');
                            wp_enqueue_script('jquery-ui-sortable');
                        }
                        
                    wp_register_style('CPTStyleSheets', CPTURL . '/css/cpt.css');
                    wp_enqueue_style( 'CPTStyleSheets');
                }
            
            function checkPost() 
                {
                    if ( isset($_GET['page']) && substr($_GET['page'], 0, 17) == 'order-post-types-' ) 
                        {
                            $this->current_post_type = get_post_type_object(str_replace( 'order-post-types-', '', $_GET['page'] ));
                            if ( $this->current_post_type == null) 
                                {
                                    wp_die('Invalid post type');
                                }
                        }
                }
            
            function saveAjaxOrder() 
                {
                    global $wpdb;
                    
                    parse_str($_POST['order'], $data);
                    
                    if (is_array($data))
                    foreach($data as $key => $values ) 
                        {
                            if ( $key == 'item' ) 
                                {
                                    foreach( $values as $position => $id ) 
                                        {
                                            $data = array('menu_order' => $position);
                                            $data = apply_filters('post-types-order_save-ajax-order', $data, $key, $id);
                                            
                                            $wpdb->update( $wpdb->posts, $data, array('ID' => $id) );
                                        } 
                                } 
                            else 
                                {
                                    foreach( $values as $position => $id ) 
                                        {
                                            $data = array('menu_order' => $position, 'post_parent' => str_replace('item_', '', $key));
                                            $data = apply_filters('post-types-order_save-ajax-order', $data, $key, $id);
                                            
                                            $wpdb->update( $wpdb->posts, $data, array('ID' => $id) );
                                        }
                                }
                        }
                }
            

            function addMenu() 
                {
                    global $userdata;
                    //put a menu for all custom_type
                    $post_types = get_post_types();
                    
                    $options          =     cpt_get_options();
                    //get the required user capability
                    $capability = '';
                    if(isset($options['capability']) && !empty($options['capability']))
                        {
                            $capability = $options['capability'];
                        }
                    else if (is_numeric($options['level']))
                        {
                            $capability = userdata_get_user_level();
                        }
                        else
                            {
                                $capability = 'install_plugins';  
                            }
                    
                    foreach( $post_types as $post_type_name ) 
                        {
                            if ($post_type_name == 'page')
                                continue;
                                
                            //ignore bbpress
                            if ($post_type_name == 'reply' || $post_type_name == 'topic')
                                continue;
                            
                            if(is_post_type_hierarchical($post_type_name))
                                continue;
                                
                            $post_type_data = get_post_type_object( $post_type_name );
                            if($post_type_data->show_ui === FALSE)
                                continue;
                                
                            if(isset($options['show_reorder_interfaces'][$post_type_name]) && $options['show_reorder_interfaces'][$post_type_name] != 'show')
                                continue;
                            
                            if ($post_type_name == 'post')
                                add_submenu_page('edit.php', __('Re-Order', 'post-types-order'), __('Re-Order', 'post-types-order'), $capability, 'order-post-types-'.$post_type_name, array(&$this, 'SortPage') );
                            elseif ($post_type_name == 'attachment') 
                                add_submenu_page('upload.php', __('Re-Order', 'post-types-order'), __('Re-Order', 'post-types-order'), $capability, 'order-post-types-'.$post_type_name, array(&$this, 'SortPage') ); 
                            else
                                {
                                    add_submenu_page('edit.php?post_type='.$post_type_name, __('Re-Order', 'post-types-order'), __('Re-Order', 'post-types-order'), $capability, 'order-post-types-'.$post_type_name, array(&$this, 'SortPage') );    
                                }
                        }
                }
            

            function SortPage() 
                {
                    ?>
                    <div class="wrap">
                        <div class="icon32" id="icon-edit"><br></div>
                        <h2><?php echo $this->current_post_type->labels->singular_name . ' -  '. __('Re-Order', 'post-types-order') ?></h2>

                        <?php cpt_info_box(); ?>  
                        <div class="group-buttom">
                            <div class="label">Order</div>
                            <button id="sort-button-asc" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sort-alpha-up" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M10.082 5.629 9.664 7H8.598l1.789-5.332h1.234L13.402 7h-1.12l-.419-1.371h-1.781zm1.57-.785L11 2.687h-.047l-.652 2.157h1.351z"/>
                                    <path d="M12.96 14H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645V14zm-8.46-.5a.5.5 0 0 1-1 0V3.707L2.354 4.854a.5.5 0 1 1-.708-.708l2-1.999.007-.007a.498.498 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L4.5 3.707V13.5z"/>
                                </svg>
                            </button>
                            <button id="sort-button-desc" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sort-alpha-down" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M10.082 5.629 9.664 7H8.598l1.789-5.332h1.234L13.402 7h-1.12l-.419-1.371h-1.781zm1.57-.785L11 2.687h-.047l-.652 2.157h1.351z"/>
                                    <path d="M12.96 14H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645V14zM4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293V2.5z"/>
                                </svg>
                            </button>
                        </div>
                        <div id="ajax-response"></div>
                        
                        <noscript>
                            <div class="error message">
                                <p><?php _e('This plugin can\'t work without javascript, because it\'s use drag and drop and AJAX.', 'post-types-order') ?></p>
                            </div>
                        </noscript>
                        
                        <div id="order-post-type">
                            <ul id="sortable">
                                <?php $this->listPages('hide_empty=0&title_li=&post_type='.$this->current_post_type->name); ?>
                            </ul>
                            
                            <div class="clear"></div>
                        </div>
                        
                        <p class="submit">
                            <a href="javascript: void(0)" id="save-order" class="button-primary"><?php _e('Update', 'post-types-order' ) ?></a>
                        </p>
                        
                        <script type="text/javascript">
                            jQuery(document).ready(function() {
                                jQuery("#sortable").sortable({
                                    'tolerance':'intersect',
                                    'cursor':'pointer',
                                    'items':'li',
                                    'placeholder':'placeholder',
                                    'nested': 'ul'
                                });
                                
                                jQuery("#sortable").disableSelection();
                                jQuery("#save-order").bind( "click", function() {
                                    
                                    jQuery("html, body").animate({ scrollTop: 0 }, "fast");
                                    
                                    jQuery.post( ajaxurl, { action:'update-custom-type-order', order:jQuery("#sortable").sortable("serialize") }, function() {
                                        jQuery("#ajax-response").html('<div class="message updated fade"><p><?php _e('Items Order Updated', 'post-types-order') ?></p></div>');
                                        jQuery("#ajax-response div").delay(3000).hide("slow");
                                    });
                                });
                                
                                jQuery("#sort-button-asc").on("click", function() {
                                    jQuery("#sortable li").sort(asc_sort).appendTo('#sortable');
                                    function asc_sort(a, b){
                                    return (jQuery(b).text()) < (jQuery(a).text()) ? 1 : -1;    
                                    }
                                });

                                jQuery("#sort-button-desc").on("click", function() {
                                    jQuery("#sortable li").sort(desc_sort).appendTo('#sortable');
                                    function desc_sort(a, b){
                                    return (jQuery(b).text()) > (jQuery(a).text()) ? 1 : -1;    
                                    }
                                });
                            });
                        </script>
                        
                    </div>
                    <?php
                }

            function listPages($args = '') 
                {
                    $defaults = array(
                        'depth'             => -1, 
                        'show_date'         => '',
                        'date_format'       => get_option('date_format'),
                        'child_of'          => 0, 
                        'exclude'           => '',
                        'title_li'          => __('Pages'), 
                        'echo'              => 1,
                        'authors'           => '', 
                        'sort_column'       => 'menu_order',
                        'link_before'       => '', 
                        'link_after'        => '', 
                        'walker'            => '',
                        'post_status'       =>  'any' 
                    );

                    $r = wp_parse_args( $args, $defaults );
                    extract( $r, EXTR_SKIP );

                    $output = '';
                
                    $r['exclude'] = preg_replace('/[^0-9,]/', '', $r['exclude']);
                    $exclude_array = ( $r['exclude'] ) ? explode(',', $r['exclude']) : array();
                    $r['exclude'] = implode( ',', apply_filters('wp_list_pages_excludes', $exclude_array) );

                    // Query pages.
                    $r['hierarchical'] = 0;
                    $args = array(
                                'sort_column'       =>  'menu_order',
                                'post_type'         =>  $post_type,
                                'posts_per_page'    => -1,
                                'post_status'       =>  'any',
                                'orderby'            => array(
                                                            'menu_order'    => 'ASC',
                                                            'post_date'     =>  'DESC'
                                                            )
                    );
                    
                    $the_query = new WP_Query($args);
                    $pages = $the_query->posts;

                    if ( !empty($pages) ) 
                        {
                            $output .= $this->walkTree($pages, $r['depth'], $r);
                        }

                    $output = apply_filters('wp_list_pages', $output, $r);

                    if ( $r['echo'] )
                        echo $output;
                    else
                        return $output;
                }
            
            function walkTree($pages, $depth, $r) 
                {
                    if ( empty($r['walker']) )
                        $walker = new Post_Types_Order_Walker;
                    else
                        $walker = $r['walker'];

                    $args = array($pages, $depth, $r);
                    return call_user_func_array(array(&$walker, 'walk'), $args);
                }
        }
   



?>