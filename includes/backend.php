<?php 

namespace Bideja\PostPopularity;

if (!defined('ABSPATH')) {
    exit;
}

class Backend
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        add_action('admin_menu', array( $this, 'createSettingsPage' ));
        add_action('admin_init', array( $this, 'registerSettings' ));
        add_action('admin_enqueue_scripts', array($this, 'registerStyles' ));

        $this->types = get_post_types(array('public' => true));

        if ((int) get_option('postpopularity_show_admin') === 1) :
          add_action('manage_posts_custom_column', array($this, 'AddPopularityColumnValue'), 10, 2);
        add_action('manage_pages_custom_column', array($this, 'AddPopularityColumnValue'), 10, 2);
        add_filter('manage_posts_columns', array($this, 'AddPopularityColumn'));
        add_filter('manage_pages_columns', array($this, 'AddPopularityColumn'));
        add_filter('manage_edit-post_sortable_columns', array($this, 'makeColumnsSortable'));
        endif;
    }

    public function registerStyles()
    {
        wp_enqueue_style('post-popularity', plugins_url('../assets/css/admin.css', __FILE__));
    }


    /**
     *
     * Create options page
     */
    public function createSettingsPage()
    {
        add_options_page(
            'Post Popularity',
            'Post Popularity',
            'manage_options',
            'postpopularity.php',
            array( $this, 'pageSettingsCallback' )
        );
    }
    
    /**
     *
     * Register plugin settings
     */
    public function registerSettings()
    {
        register_setting('postpopularity-settings-group', 'postpopularity_show_admin');
        register_setting('postpopularity-settings-group', 'postpopularity_types');
    }

    /**
     * Add Popularity column to post list
     */
    public function AddPopularityColumn($columns)
    {
        $columns['Popularity'] =__('Popularity', 'bideja');
        return $columns;
    }

    /**
     * Add popularity value to column
     */
    public function AddPopularityColumnValue($column, $post_id)
    {
        if ($column == 'Popularity') {
            $count = (int)get_post_meta($post_id, 'post_popularity', true);
            echo !empty($count) ? $count : '-';
        }
    }
     
    // Make popularity value sortable
    public function makeColumnsSortable($columns)
    {
        $columns['Popularity'] = 'post_popularity';

        return $columns;
    }

    /**
     * Reset view count
     */
    public function resetData()
    {
        $args = array(
         'posts_per_page' => -1,
         'post_status' => 'any',
         'post_type' => array('post')
     );
        $posts = get_posts($args);

        foreach ($posts as $post) {
            delete_post_meta($post->ID, 'post_popularity');
        }
        wp_reset_postdata();
    }

    /**
     *
     * Show options form
     */
    public function pageSettingsCallback()
    {
        ?>
          <div class="wrap">

               <h1>Post Popularity settings</h1>

               <?php $this->pageSettingsOptions(); ?>
               <?php $this->pageSettingsReset(); ?>

          </div>
<?php
    }

    /**
     * Form for page settings
     */
    public function pageSettingsOptions()
    {
        ?>
     <!-- Normal options -->
     <form method="post" action="options.php">
          <?php settings_fields('postpopularity-settings-group'); ?>
          <?php do_settings_sections('postpopularity-settings-group'); ?>
          <table class="form-table">
               <tr>
                    <th scope="row">Show view count data in admin list:</th>
                    <td>
                    <input type="checkbox" name="postpopularity_show_admin" value="1" <?php checked(1, get_option('postpopularity_show_admin'), true); ?> />
                    </td>
               </tr>
          </table>
          <?php submit_button(); ?>
     </form>
     <?php
    }

    /**
     * Form for reseting view count
     */
    public function pageSettingsReset()
    {
        ?>
              <hr>
     <p>If you want to reset all post view count click reset button. <strong>Important: This can't be undone!</strong></p>
     <!-- Reset view count -->
     <form method="post" action="">
          <input type="hidden" name="postpopularity_reset" value="1">
          <input type="submit" class="button button-primary button-postpopularity-reset" value="RESET"/>
     </form>
     <?php // Reset data if triggered
     if (isset($_POST['postpopularity_reset']) && (int) $_POST['postpopularity_reset'] === 1) {
         $this->resetData();
         echo '<p>View count reset completed.</p>';
     } ?>
         <?php
    }
}
