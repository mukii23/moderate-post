<?php

/* 
 * @Class 'Moderate_widget'.
 * The file will initialize the widget fro back-end.
 */


//Function to register the widget for wordpress

function moderate_register_widget() {
register_widget( 'Moderate_widget' );
}
add_action( 'widgets_init', 'moderate_register_widget' );


class Moderate_widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            // widget ID
                'moderate_post',
            // widget name
                __('Moderate Posts', 'moderate_posts_widget'),
            // widget description
                array('description' => __('This widget will update the existing posts', 'moderate_posts_widget'),)
        );
    }
    
/*
 * widget() function contains output of widget
 */
    
    public function widget($args, $instance) {
        
        $title    = apply_filters('widget_title', $instance['title']);
        $postId   = $instance['post_link'];
        $pTitle   = $instance['post_title'];
        $pExcerpt = $instance['post_excerpt'];
        $pCatg    = $instance['post_cat'];
        $pTag     = isset( $instance['post_tag'] ) ? $instance['post_tag'] : array();
        $pImg     = $instance['image_uri'];
        
        // @args array() for post update.
        $update_post  = array(
            'ID'           => $postId,
            'post_title'   => $pTitle,
            'post_content' => $pExcerpt,
        );
 
        // Update the post into the database
        wp_update_post( $update_post );

        // Post @tags update.        
        wp_set_post_tags( $postId, $pTag);
          
        // Post @category update.
        wp_set_post_categories( $postId, array( $pCatg ) );
        
        //  Featured Image update.
        $thumbnail_id = get_image_id($pImg);
        set_post_thumbnail( $postId, $thumbnail_id );
        
        echo $args['before_widget'];
        //if title exists.
        if (!empty($title)):
            echo $args['before_title'] . $title . $args['after_title'];
        endif;
        //Subtitle        
        echo '<p>'.$instance['sub_title'].'</p>';
        
        //Post Output
        $output = '<div class="moderate_posts">'
                . '<ul>'
                . '<li>'
                . '<a class="md_image" href="'.get_permalink( $postId ).'"><img src="'.$pImg.'"></a>'
                . '<div class="md_content">'
                . '<p><a href="'.get_permalink( $postId ).'">'.$pTitle.'</a></p>'
                . '<span>'.get_the_date( 'F j, Y', $postId ).'</span>'
                . '</div>'
                . '</li>'
                . '</ul>'
                . '</div>';
        echo $output;
        echo $args['after_widget'];
    }
    
/*******
 ***  'form()' function will setup the backend of widget settings.
 *******/
    public function form($instance) {

        //Check for Widget fields
        if (isset($instance['title']))
            $title = $instance['title'];
        else
            $title = __('Updated Posts', 'moderate_posts_widget');
        
        if (isset($instance['sub_title'])){
            $subtitle = $instance['sub_title'];
        }
        if (isset($instance['post_title'])){
            $post_title = $instance['post_title'];
        }
        if (isset($instance['post_link'])){
            $post_link = $instance['post_link'];
        }
        if (isset($instance['post_excerpt'])){
            $post_excerpt = $instance['post_excerpt'];
        }

        if (isset($instance['post_tag'])) {
            $post_tag = $instance['post_tag'];
        }

        if (isset($instance['post_cat'])){
            $post_cat = $instance['post_cat'];
        }
        if (isset($instance['image_uri'])){
            $image_uri = $instance['image_uri'];
        }
        
//      @array, @counter fields for 'post_tag'
        $field_num = count($post_tag);
        $post_tag[$field_num + 1] = '';
        $fields_html = array();
        $fields_counter = 0;
        
//        Get Category link
        $category_link = get_category_link( $post_cat );
    ?>

    <!-- **
    ##### HTML View for widget backend #####
    **-->
    
    <!--1. Widget Title-->
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        
    <!--2.Sub Title-->
        <p>
            <label for="<?php echo $this->get_field_id('sub_title'); ?>"><?php _e('Sub Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('sub_title'); ?>" name="<?php echo $this->get_field_name('sub_title'); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>" />
        </p>
        
    <!--3. Post Title-->
        <p>
            <label for="<?php echo $this->get_field_id('post_title'); ?>"><?php _e('Post Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('post_title'); ?>" name="<?php echo $this->get_field_name('post_title'); ?>" type="text" value="<?php echo esc_attr($post_title); ?>" />
        </p>
        
    <!--4. Post Link-->
        <p>
            <label for="<?php echo $this->get_field_id('post_link'); ?>"><?php _e('Post Link:'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('post_link'); ?>" name="<?php echo $this->get_field_name('post_link'); ?>">
                     <?php 
                    global $post;
                    $args = array('numberposts' => -1);
                    $posts = get_posts($args);
                    foreach ($posts as $post) : setup_postdata($post);
                        ?>
                        <option value="<?php echo $post->ID; ?>" <?php selected($instance['post_link'], $post->ID); ?>><?php the_title(); ?></option>
                        <?php
                    endforeach;
                    ?>
            </select>
            <span><i>Choose post to edit</i></span>
        </p>
        
    <!--5. Post Excerpt-->
        <p>
            <label for="<?php echo $this->get_field_id('post_excerpt'); ?>"><?php _e('Post Excerpt:'); ?></label>
            <textarea class="widefat" id="<?php echo $this->get_field_id( 'post_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'post_excerpt' ); ?>" style="height:60px;"><?php echo esc_attr($post_excerpt); ?></textarea>
        </p>
        
    <!--6. Post Tag-->
        <p id="appendFields">
            
            <?php 
            foreach ($post_tag as $name => $value) {
                $fields_html[] = sprintf(
                        '<p style="display: flex;"><input type="text" name="%1$s[%2$s]" value="%3$s" class="widefat feature%2$s">'
                        . '<span class="remove-field button button-primary button-large">Remove</span></p>', 
                        $this->get_field_name('post_tag'),
                        $fields_counter, 
                        esc_attr($value)
                );
                $fields_counter += 1;
                
                if ($fields_counter == $field_num)
                    break;

            }
            print 'Post Tags:<br />' . join($fields_html);
            ?>
            <script>
                jQuery(document).ready(function($){

                    var fieldname = <?php echo json_encode($this->get_field_name( 'post_tag' )) ?>;
                    var fieldnum = <?php echo json_encode($fields_counter - 1) ?>;

                     var count = fieldnum;
                     
                //  Append fields for @tags
                     $('.<?php echo $this->get_field_id('addfeature') ?>').click(function() {
                        $("#<?php echo $this->get_field_id( 'field_clone' );?>").append("<p style='display: flex;'><input type='text' name='"+fieldname+"["+(count+1) +"]' value='' class='widefat feature"+ (count+1) +"'><span class='remove-field button button-primary button-large'>Remove</span></p>" );
                        count++;
                    });
                    
                //  Remove append fields from @tags 
                    $(".remove-field").live('click', function() {
                        $(this).parent().remove();
                    });

                });
            </script>   
            
            <!--span will display the appended fields from jquery-->
            <span id="<?php echo $this->get_field_id( 'field_clone' );?>"></span>
            
            <!--Add button-->
            <input class="button <?php echo $this->get_field_id('addfeature'); ?>" type="button" value="Add" id="addfeature" />

        </p>
        
    <!--7. Post Category-->
        <p>
            <label for="<?php echo $this->get_field_id('post_cat'); ?>"><?php _e('Post Category:'); ?></label>
            <select class="widefat selectCat" id="<?php echo $this->get_field_id('post_cat'); ?>" name="<?php echo $this->get_field_name('post_cat'); ?>">
            <?php foreach(get_terms('category','parent=0&hide_empty=0') as $term) { ?>
                    <option <?php selected( $instance['post_cat'], $term->term_id ); ?> value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
            <?php } ?> 
            </select>
            <!--See More link-->
            <a href="<?php echo esc_url( $category_link ); ?>" target="_blank"><i>See more</i></a>
        </p>
        
    <!--8. Post Image-->
        <p>
            <label for="<?= $this->get_field_id('image_uri'); ?>">Featured Image</label>
            <img class="<?= $this->id ?>_img" src="<?= (!empty($instance['image_uri'])) ? $instance['image_uri'] : ''; ?>" style="margin:0;padding:0;max-width:100%;display:block"/>
            <input type="text" class="widefat <?= $this->id ?>_url" name="<?= $this->get_field_name('image_uri'); ?>" value="<?= $image_uri; ?>" style="margin-top:5px;" />
            <input type="button" id="<?= $this->id ?>" class="button button-primary js_custom_upload_media" value="Upload Image" style="margin-top:5px;" />
        </p>
        
        <?php
    }
    
    /*****
     ***  'update()' function will update the widget settings.
     *****/
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['sub_title'] = ( !empty( $new_instance['sub_title'] ) ) ? strip_tags( $new_instance['sub_title'] ) : '';
        $instance['post_title'] = ( !empty( $new_instance['post_title'] ) ) ? strip_tags( $new_instance['post_title'] ) : '';
        $instance['post_link'] = ( !empty( $new_instance['post_link'] ) ) ? strip_tags( $new_instance['post_link'] ) : '';
        $instance['post_excerpt'] = ( !empty( $new_instance['post_excerpt'] ) ) ? strip_tags( $new_instance['post_excerpt'] ) : '';
        $instance['post_tag'] = array();
        if( isset( $new_instance['post_tag'] ) ){
            foreach( $new_instance['post_tag'] as $pTagarray ){
                $instance['post_tag'][] = $pTagarray;
            }
        }
        $instance['post_cat'] = ( !empty( $new_instance['post_cat'] ) ) ? strip_tags( $new_instance['post_cat'] ) : '';
        $instance['image_uri'] = ( !empty( $new_instance['image_uri'] ) ) ? $new_instance['image_uri'] : '';
        
        return $instance;
    }

}
