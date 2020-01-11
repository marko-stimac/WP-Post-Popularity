<?php

/**
 * Show post count values in post listings and create option page for reseting all post count
 */

namespace ms\PostPopularity;

defined('ABSPATH') || exit;

class Backend
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        add_action('admin_menu', array($this, 'createSettingsPage'));
        add_action('admin_init', array($this, 'registerSettings'));
        add_action('admin_enqueue_scripts', array($this, 'registerStyles'));

        $this->types = get_post_types(array('public' => true));

        add_action('manage_posts_custom_column', array($this, 'AddPopularityColumnValue'), 10, 2);
        add_action('manage_pages_custom_column', array($this, 'AddPopularityColumnValue'), 10, 2);
        add_filter('manage_posts_columns', array($this, 'AddPopularityColumn'));
        add_filter('manage_pages_columns', array($this, 'AddPopularityColumn'));
        add_filter('manage_edit-post_sortable_columns', array($this, 'makeColumnsSortable'));
        add_filter('plugin_row_meta', array($this, 'modify_plugin_meta'), 10, 2);
    }

    public function registerStyles()
    {
        wp_enqueue_style('post-popularity', plugins_url('/assets/post-popularity-admin.css', __DIR__));
    }

    /**
     * Create options page
     */
    public function createSettingsPage()
    {
        add_options_page(
            'Post Popularity',
            'Post Popularity',
            'manage_options',
            'postpopularity.php',
            array($this, 'pageSettingsCallback')
        );
    }

    /**
     * Register plugin settings
     */
    public function registerSettings()
    {
        register_setting('postpopularity-settings-group', 'postpopularity_types');
    }

    /**
     * Add Popularity column to post list
     */
    public function AddPopularityColumn($columns)
    {
        $columns['Popularity'] = __('Popularity', 'ms');
        return $columns;
    }

    /**
     * Add popularity value to column
     */
    public function AddPopularityColumnValue($column, $post_id)
    {
        if ($column == 'Popularity') {
            $count = (int) get_post_meta($post_id, 'post_popularity', true);
            echo !empty($count) ? $count : '-';
        }
    }

    /**
     * Make popularity value sortable
     */
    public function makeColumnsSortable($columns)
    {
        $columns['Popularity'] = 'post_popularity';

        return $columns;
    }

    /**
     * Add link to readme file on installed plugin listing
     */
    public function modify_plugin_meta($links_array, $file)
    {
        if (strpos($file, 'post-popularity.php') !== false) {
            $links_array[] = '<a href="' . esc_url(get_admin_url(null, 'options-general.php?page=postpopularity.php')) . '">Settings</a>';
        }
        return $links_array;
    }

    /**
     * Reset view count
     */
    public function resetData()
    {
        $args = array(
            'posts_per_page' => -1,
            'post_status' => 'any',
            'post_type' => array('any'),
        );
        $posts = get_posts($args);

        foreach ($posts as $post) {
            delete_post_meta($post->ID, 'post_popularity');
        }
        wp_reset_postdata();
    }

    /**
     * Show options form
     */
    public function pageSettingsCallback()
    {
        ?>
		<div class="wrap">

			<h1>Post Popularity settings</h1>

			<?php $this->pageSettingsReset();?>

		</div>
	<?php
}

    /**
     * Form for reseting view count
     */
    public function pageSettingsReset()
    {
        ?>
		<p>If you want to reset all post view count click reset button. <strong>Important: This can't be undone!</strong></p>
		<form method="post" action="">
			<input type="hidden" name="postpopularity_reset" value="1">
			<input type="submit" class="button button-primary button-postpopularity-reset" value="RESET" />
		</form>
		<?php
if (isset($_POST['postpopularity_reset']) && (int) $_POST['postpopularity_reset'] === 1) {
            $this->resetData();
            echo '<p>View count reset completed.</p>';
        }?>
<?php
}
}
