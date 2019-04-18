<?php


    public static function add()
    {
        $screens = ['post', 'wporg_cpt'];

        foreach ($screens as $screen) {
            add_meta_box(
                'wporg_box_id',          // Unique ID
                'Select The Issue Values', // Box title
                [self::class, 'html'],   // Content callback, must be of type callable
                $screen                  // Post type
            );
        }
    }

    public static function save($post_id)
    {
        if (array_key_exists('wporg_field', $_POST)) {
            update_post_meta(
                $post_id,
                '_wporg_meta_key',
                $_POST['wporg_field']
            );
        }
    }

    public static function html($post)
    {
        $value = get_post_meta($post->ID, '_wporg_meta_key', true);

        $query = new WP_Query( array( 'post_type' => 'issue' ) );

        ?>

        <label for="wporg_field">What Issue Does This Article Belong To?</label>
          <select name="wporg_field" id="wporg_field" class="postbox">
            <option value="">Select something...</option>
        <?php

        if ( $query->have_posts() ) : ?>
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>

              <option value="<?php echo get_the_permalink(); ?>" <?php selected( $value, get_the_permalink() ); ?>><?php the_title(); ?></option>

            <?php endwhile; wp_reset_postdata(); ?>
        <!-- show pagination here -->
        <?php else : ?>
            <option value="">No Issues Found</option>
        <?php endif; ?>
      </select>

        <?php
    }


}

add_action('add_meta_boxes', ['WPOrg_Meta_Box', 'add']);
add_action('save_post', ['WPOrg_Meta_Box', 'save']);

function wporg_meta_box_scripts()
{
    // get current admin screen, or null
    $screen = get_current_screen();
    // verify admin screen object
    if (is_object($screen)) {
        // enqueue only for specific post types
        if (in_array($screen->post_type, ['post', 'wporg_cpt'])) {
            // enqueue script
            wp_enqueue_script('wporg_meta_box_script', plugin_dir_url(__FILE__) . 'meta-boxes/js/admin.js', ['jquery']);
            // localize script, create a custom js object
            wp_localize_script(
                'wporg_meta_box_script',
                'wporg_meta_box_obj',
                [
                    'url' => admin_url('admin-ajax.php'),
                ]
            );
        }
    }
}
add_action('admin_enqueue_scripts', 'wporg_meta_box_scripts');
