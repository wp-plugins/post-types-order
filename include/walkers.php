<?php

    class Post_Types_Order_Walker extends Walker 
        {

            var $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');


            function start_lvl(&$output, $depth = 0, $args = array()) {
                $indent = str_repeat("\t", $depth);
                $output .= "\n$indent<ul class='children'>\n";
            }


            function end_lvl(&$output, $depth = 0, $args = array()) {
                $indent = str_repeat("\t", $depth);
                $output .= "$indent</ul>\n";
            }


            function start_el(&$output, $page, $depth = 0, $args = array(), $id = 0) {
                if ( $depth )
                    $indent = str_repeat("\t", $depth);
                else
                    $indent = '';

                extract($args, EXTR_SKIP);

                //----
                if($page->post_type == 'attachment')
                    $image_id = $page->ID;
                    else
                    $image_id = get_post_thumbnail_id( $page->ID , 'post-thumbnail' ); 
                if ($image_id > 0)
                        {
                            $image = wp_get_attachment_image_src( $image_id , array(195,195)); 
                            if($image !== FALSE)
                                $image_html =  '<img style="width:50px"  src="'. $image[0] .'" alt="" />';
                                else
                                $image_html =  '<img src="'. CPTURL .'/images/nt.png" alt="" />'; 
                        }
                        else
                            {
                                $image_html =  '<img src="'. CPTURL .'/images/nt.png" alt="" />';    
                            } 
                $output .= $indent . '<li id="item_'.$page->ID.'"><span>'. $page->ID . ' ' .apply_filters( 'the_title', $page->post_title, $page->ID ).'</span> ' . $image_html;
                
                
                //$output .= $indent . '<li id="item_'.$page->ID.'"><span>'.apply_filters( 'the_title', $page->post_title, $page->ID ).'</span>';
            }


            function end_el(&$output, $page, $depth = 0, $args = array()) {
                $output .= "</li>\n";
            }

        }



?>