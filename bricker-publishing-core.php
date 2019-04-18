<?php
/**
 * Plugin Name: Publishing Core
 * Description: Core functionality for the Bricker Publishing suite of sites
 * Plugin URI: https://packerlandwebsites.com/
 * Version: 1.8.0
 * Author: Packerland Websites
 * Author URI: https://packerlandwebsites.com/
 */

// adds WP-EMail Plugin By Lester 'GaMerZ' Chan shortcode to each blog post
function wpb_custom_emailthis($content){

if (is_single()) {
    $emailthis = email_link('', '', false);
    $content .= '<p>' . $emailthis . '</p>';
}
    return $content;
}

add_filter( "the_content", "wpb_custom_emailthis" );


// Add special code to attach a blog post to an issue and show it with a shortcode.

abstract class WPOrg_Meta_Box
{
    public static function add()
    {
        $screens = ['post', 'wporg_cpt'];

        foreach ($screens as $screen) {
            add_meta_box(
                'wporg_issue_id',          // Unique ID
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

        if (array_key_exists('wporg_page_field', $_POST)) {
            update_post_meta(
                $post_id,
                '_wporg_page_key',
                $_POST['wporg_page_field']
            );
        }
    }

    public static function html($post)
    {
        $value = get_post_meta($post->ID, '_wporg_meta_key', true);

        $page = get_post_meta($post->ID, '_wporg_page_key', true);

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

      <label for="wporg_page_field">What Page Number Is It On?</label>
      <input type="number" name="wporg_page_field" value="<?php echo $page; ?>">

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

function wporg_meta_box_ajax_handler()
{
    if (isset($_POST['wporg_field_value'])) {

        switch ($_POST['wporg_field_value']) {
            case '':
              echo 'success';
              break;

            case 'something':
                echo 'success';
                break;
            default:
                echo 'failure';
                break;
        }
    }
    // ajax handlers must die
    die;
}
// wp_ajax_ is the prefix, wporg_ajax_change is the action we've used in client side code
add_action('wp_ajax_wporg_ajax_change', 'wporg_meta_box_ajax_handler');

function issue_register_ref_page() {
    add_submenu_page(
        'edit.php?post_type=issue',
        __( 'Conncted Articles', 'textdomain' ),
        __( 'Conncted Articles', 'textdomain' ),
        'manage_options',
        'books-shortcode-ref',
        'issues_ref_page_callback'
    );
}

function issue_links_years_shortcode($atts, $year) {
	ob_start();

	$a = shortcode_atts( array(
		'year' => '2018',
	), $atts );

	$pastYear = intval($a['year']) - 1;

  ?>
  <?php

	$args_array = array(
		'post_type' => 'issue',
		'category' => '17',
		'date_query' => array(
			'relation' => 'OR',
			array('year' => $a['year']),
		),
	);
 $getPostsToSelect = get_posts($args_array);
  $url = $_SERVER['SERVER_NAME'];

  ?>

<p><?php echo $a['year']; ?></p>
<p>
	<?php echo $pastYear;?>
</p>
<div class="pt-cv-wrapper"><br>
  <?php

  foreach ($getPostsToSelect as $aPostsToSelect) {
?>



	<div data-id="pt-cv-page-1" class="pt-cv-page" >
		<div class="col-md-3 col-sm-4 col-xs-6 pt-cv-content-item pt-cv-1-col">
			<div class="pt-cv-ifield">
				<a href="http://w6g.a55.myftpupload.com/issue/nursery-issue-july-aug-2018/" class="_self pt-cv-href-thumbnail pt-cv-thumb-default cvplbd" target="_self">
					<img width="198" height="260" src="https://secureservercdn.net/50.62.175.49/w6g.a55.myftpupload.com/wp-content/uploads/2018/06/March-April-2019.jpg?time=1555512534" class="pt-cv-thumbnail img-none" alt="March April 2019 Issue"></a>
				<div class="pt-cv-ctf-list" data-cvc="1">
					<div class="col-md-12 pt-cv-ctf-column">
						<div class="pt-cv-custom-fields pt-cv-ctf-month">
							<div class="pt-cv-ctf-value">June/July</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


<?php

  } ?>
</div>


  <?php



	return ob_get_clean();
}

add_shortcode( 'issueyeararchives', 'issue_links_years_shortcode' );

function issue_links_shortcode() {

	ob_start();

  ?>
  <?php $getPostsToSelect = get_posts('post_type=post&numberposts=-1');
  $url = $_SERVER['SERVER_NAME'];

  ?>
  <div class="pt-cv-view pt-cv-grid pt-cv-colsys pt-cv-pgregular pt-cv-sharp-buttons" id="pt-cv-view-7dd16f5gkf">
    <div data-id="pt-cv-page-1" class="pt-cv-page" data-cvc="1">

  <?php

  foreach ($getPostsToSelect as $aPostsToSelect) {

    $metaData = get_post_meta($aPostsToSelect->ID, '_wporg_meta_key', true );

    $pageNumber = get_post_meta($aPostsToSelect->ID, '_wporg_page_key', true );

    if($metaData != ''){?>


          <div class="col-md-12 col-sm-12 col-xs-12 pt-cv-content-item pt-cv-1-col" data-pid="1799">
            <div class="pt-cv-ifield">
              <div class="pt-cv-title">
                <?php $metaData = str_replace('http://w6g.a55.myftpupload.com/issue/','http://w6g.a55.myftpupload.com/wp-content/issues/', $metaData); ?>

              <?php echo '<a href="'. $metaData .'index.html#p='. $pageNumber .'" target="_blank">'; ?>
                  <?php echo $aPostsToSelect->post_title; ?>
                <?php echo '</a>'; ?>

                </div>
              </div>
            </div>

    <?php
    }
  } ?>

    </div>
  </div>

  <?php



	return ob_get_clean();
}

add_shortcode( 'issuefilelinks', 'issue_links_shortcode' );


function current_issue_links_shortcode() {

	ob_start();

  ?>
  <?php $getPostsToSelect = get_posts('post_type=post&numberposts=-1&category_name=Current Articles');
  $url = $_SERVER['SERVER_NAME'];

  ?>
  <div class="pt-cv-view pt-cv-grid pt-cv-colsys pt-cv-pgregular pt-cv-sharp-buttons" id="pt-cv-view-7dd16f5gkf">
    <div data-id="pt-cv-page-1" class="pt-cv-page" data-cvc="1">

  <?php

  foreach ($getPostsToSelect as $aPostsToSelect) {

    $metaData = get_post_meta($aPostsToSelect->ID, '_wporg_meta_key', true );

    $pageNumber = get_post_meta($aPostsToSelect->ID, '_wporg_page_key', true );

    if($metaData != ''){?>


          <div class="col-md-12 col-sm-12 col-xs-12 pt-cv-content-item pt-cv-1-col" data-pid="1799">
            <div class="pt-cv-ifield">
              <div class="pt-cv-title">
                <?php $metaData = str_replace('http://w6g.a55.myftpupload.com/issue/','http://w6g.a55.myftpupload.com/wp-content/issues/', $metaData); ?>

              <?php echo '<a href="'. $metaData .'index.html#p='. $pageNumber .'" target="_blank">'; ?>
                  <?php echo $aPostsToSelect->post_title; ?>
                <?php echo '</a>'; ?>

                </div>
              </div>
            </div>

    <?php
    }
  } ?>

    </div>
  </div>

  <?php



	return ob_get_clean();
}

add_shortcode( 'currentissuefilelinks', 'current_issue_links_shortcode' );

/**
 * Display callback for the submenu page.
 */
function issues_ref_page_callback() {
    ?>
    <div class="wrap">
      <h1>All Artcles Connected To A Issue</h1>

      <script type="text/javascript">

        console.log("To show of the posts use short code [issuefilelinks] or to show only the posts of 'Current Articles' use [currentissuefilelinks]");

      </script>


      <p>The blog posts that in the "Current Articles" and have a connection are as folows:</p>

      <?php echo apply_filters( 'the_content',' [currentissuefilelinks] '); ?>

    </div>

    <?php
}


//add_action('init', 'issue_register_ref_page');



class PluginCore {

	function __construct() {

		$files = glob(dirname(__FILE__) . '/post-types/*.php');
		foreach ($files as $file) {
		    require_once($file);
		}

		$files = glob(dirname(__FILE__).'/taxonomies/*.php');
		foreach ($files as $file) {
			require_once($file);
		}

		add_action('add_meta_boxes', [$this, 'issue_meta']);
		add_action('save_post', [$this, 'issue_save']);
		add_action('post_edit_form_tag', [$this, 'post_edit_form_tag']);

		//Set the acf-json desintation to here
		add_filter('acf/settings/save_json', function($path) {
		    return dirname(__FILE__) . '/acf-json';
		});
		//Include the /acf-json folder in the places to look for ACF Local JSON files
		add_filter('acf/settings/load_json', function($paths) {
		    $paths[] = dirname(__FILE__) . '/acf-json';
		    return $paths;
		});

		add_action('acf/save_post', [$this, 'acf_save_post'], 11);

		add_action('wp_loaded', [$this, 'add_acf_options']);
		add_action('after_setup_theme', [$this, 'acf_settings_caps']);

		add_shortcode('bricker-media-kit', [$this, 'media_kit_shortcode']);

		add_filter('the_content', [$this, 'issue_content']);
	}

	function media_kit_shortcode() {
		return '<iframe src="'.plugin_dir_url( __FILE__ ).'../../mediakit" width="970" height="600" frameborder="0" scrolling="no" seamless="seamless"></iframe>';
	}

	function acf_save_post() {
		$screen = get_current_screen();
		if(strpos($screen->id, "site-general-settings") !== false) {
			$zip = get_attached_file(get_field('mediakit', 'options')['ID']);
			$targetdir = plugin_dir_path( __FILE__ ).'../../mediakit';
			$filenoext = basename(get_field('mediakit', 'options')['filename'], '.zip');
			if (file_exists($targetdir)) {
				self::rrmdir($targetdir);
			}
			mkdir($targetdir);
			WP_filesystem();
			if(unzip_file($zip, $targetdir)) {
				if(file_exists($targetdir.'/'.$filenoext)) {
					$files = scandir($targetdir.'/'.$filenoext);
					if($files) {
						foreach($files as $file) {
							if($file != '.' && $file != '..') {
								rename($targetdir.'/'.$filenoext.'/'.$file, $targetdir.'/'.$file);
							}
						}
						rmdir($targetdir.'/'.$filenoext);
					}
				}
			} else {
				echo 'unzip failed';
				die();
			}
		}
	}

	function add_acf_options() {
		//Add an ACF Options Page
		if( function_exists('acf_add_options_page') ) {
			acf_add_options_page([
				'page_title' => 'Site General Settings',
				'menu_title' => 'Site Settings',
				'menu_slug' => 'site-general-settings',
				'capability' => 'edit_site_settings',
				'icon_url' => 'dashicons-admin-site'
			]);
		}
	}

	function acf_settings_caps() {
		$role = get_role('administrator');
		$role->add_cap('edit_site_settings');
		$role = get_role('editor');
		$role->add_cap('edit_site_settings');
	}

	function post_edit_form_tag() {
		echo ' enctype="multipart/form-data"';
	}

	function issue_meta() {
		add_meta_box('issue_meta', 'Issue Files Upload', [$this, 'issue_meta_html'], 'issue', 'normal', 'high');
	}

	function issue_meta_html($post) {
		wp_nonce_field( 'issue_meta', 'issue_meta_nonce' );
		if(get_post_meta($post->ID, '_issue_uploaded', true) != '') {
			echo '<script>console.log("Files have been uploaded for this issue. They are located at /wp-content/issues/'.$post->post_name.'");</script>';
			echo 'Files have been uploaded for this issue. Uploading new ones will overwrite the ones currently there. <br/>';
		} else {
			echo 'No files have been uploaded for this issue<br><br>';
		}
		echo '<input type="file" title="issue-zip" name="issue-zip" id="issue-zip" />';
	}

	public static function rrmdir($dir) {
	  foreach(glob($dir . '/*') as $file) {
	    if(is_dir($file)) self::rrmdir($file); else unlink($file);
	  } rmdir($dir);
	}

	function issue_save($post_id) {
		if ( ! isset( $_POST['issue_meta_nonce'] ) ) {
			return;
		}
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['issue_meta_nonce'], 'issue_meta' ) ) {
			return;
		}
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}
		/* OK, it's safe for us to save the data now. */
		// Make sure that it is set.
		if ( empty($_FILES['issue-zip']['name']) ) {
			return;
		}
		$path = self::get_issues_path();
		if(!is_dir($path)) {
			$wut = mkdir($path);

		}
		if($_FILES['issue-zip']['error'] > 0) {
			echo "Error";
		} else {
			$filename = $_FILES['issue-zip']['name'];
			$source = $_FILES["issue-zip"]["tmp_name"];
			$type = $_FILES["issue-zip"]["type"];
			$name = explode(".", $filename);
			$continue = strtolower($name[1]) == 'zip' ? true : false;
			if(!$continue) {
				$message = "The file you are trying to upload is not a .zip file. Please try again.";
				return;
			}
			$post = get_post($post_id);
			$putithere = $post->post_name;
			$filenoext = basename($filename, '.zip');

			$targetdir = $path.'/'.$putithere; // target directory
			$temptarget = $path .'/'. $filename; // target zip file

			/* create directory if not exists', otherwise overwrite */
			/* target directory is same as filename without extension */
			// die('<pre style="display:block;background-color:white;color:black;padding:15px;font-size:16px;font-family:monospace;text-align:left;">'.print_r($targetdir, true).'</pre>');
			if (is_dir($targetdir))  {
				$this->rrmdir($targetdir);
			} else {
				mkdir($targetdir, 0755);
			}
			WP_Filesystem();

			if(move_uploaded_file($source, $temptarget) && unzip_file($temptarget, $targetdir)) {
				if(file_exists($targetdir.'/'.$filenoext)) {
					$files = scandir($targetdir.'/'.$filenoext);
					if($files) {
						foreach($files as $file) {
							if($file != '.' && $file != '..') {
								rename($targetdir.'/'.$filenoext.'/'.$file, $targetdir.'/'.$file);
							}
						}
						rmdir($targetdir.'/'.$filenoext);
					}
				}
				update_post_meta($post_id, '_issue_uploaded', true);
				unlink($temptarget);
			}
		}
	}

	function issue_content($content) {
		global $post;
		if(is_singular('issue')) {
			if(get_field('content_style') == 'Flipbook Embed') {
				$path = self::get_issues_url().'/'.$post->post_name;
				$path = explode(':', $path)[1];
				return '<iframe name="issueFrame" width="970" height="600" seamless="seamless" scrolling="no" frameborder="0" allowtransparency="true" src="http:'.$path.'/index.html'.'"></iframe>';
			}
			else return get_field('content');
		}
		return $content;
	}

	public static function get_issues_path() {
		$upload_dir = wp_upload_dir();
		//Assume that nothing weird is going on with uploads
		$upload_dir = $upload_dir['basedir'];
		$upload_dir = str_replace('/uploads', '', $upload_dir);
		return $upload_dir.'/issues';
	}
	public static function get_issues_url() {
		$upload_dir = wp_upload_dir();
		//Assume that nothing weird is going on with uploads
		$upload_dir = $upload_dir['baseurl'];
		$upload_dir = str_replace('/uploads', '', $upload_dir);
		return $upload_dir.'/issues';
	}

}

$PluginCore = new PluginCore();
