<?php

/**
 * Trigger post count when user visits a post/page
 */

namespace ms\PostPopularity;

defined('ABSPATH') || exit;

class Frontend
{

    // Session duration (1 day)
    private $session_timer = 24 * 60 * 60;

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'registerScripts'));
        add_action('wp_ajax_set_post_popularity', array($this, 'setPostPopularity'));
        add_action('wp_ajax_nopriv_set_post_popularity', array($this, 'setPostPopularity'));
        add_action('wp_ajax_add_post_to_session', array($this, 'addPostToSession'));
        add_action('wp_ajax_nopriv_add_post_to_session', array($this, 'addPostToSession'));
        add_action('init', array($this, 'startSession'), 1);
    }

    /**
     * Register scripts
     */
    public function registerScripts()
    {

        // If session duration is over clear it
        $session_life = time() - $_SESSION['session_timeout'];
        if ($session_life > $this->session_timer) {
            $this->clearSession();
        }

        wp_enqueue_script('post-popularity', plugins_url('/assets/post-popularity.js', __DIR__), array('jquery'), null, true);
        wp_localize_script(
            'post-popularity',
            'postpopularity',
            array(
                'url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('nonce'),
                'is_singular' => is_singular(),
                'post_id' => get_the_ID(),
                'visited_pages' => $_SESSION['visited_pages'],
            )
        );
    }

    /**
     * Starts logging session
     */
    public function startSession()
    {
        if (!session_id()) {
            session_start();
            if (empty($_SESSION['visited_pages'])) {
                $_SESSION['visited_pages'] = array();
            }
            if (empty($_SESSION['session_timeout'])) {
                $_SESSION['session_timeout'] = time() + $this->session_timer;
            }
        }
    }

    /**
     * Clear session
     */
    private function clearSession()
    {
        $_SESSION['visited_pages'] = array();
        unset($_SESSION['session_timeout']);
    }

    /**
     * Add visited page ID to user's session
     */
    public function addPostToSession()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'nonce')) {
            wp_die();
        }

        // Dodaj članak u sesiju
        if (isset($_SESSION['visited_pages']) && !in_array($_POST['post_id'], $_SESSION['visited_pages'])) {
            array_push($_SESSION['visited_pages'], $_POST['post_id']);
        }
        wp_die();
    }

    /**
     * Increase post popularity
     */
    public function setPostPopularity()
    {

        if (empty($_POST['is_singular'])) {
            wp_die();
        }

        if (!wp_verify_nonce($_POST['nonce'], 'nonce')) {
            wp_die();
        }

        global $post;
        $post_id = $_POST['post_id'];
        $count_key = 'post_popularity';

        // If post has already been added to session exit
        if (in_array($post_id, $_POST['visited_pages'])) {
            wp_die();
        }

        // Set post popularity to 1 if it is first time, otherwise increase it by 1
        $count = (int) get_post_meta($post_id, $count_key, true);
        if (empty($count)) {
            $count = 0;
            delete_post_meta($post_id, $count_key);
            add_post_meta($post_id, $count_key, '1');
        } else {
            update_post_meta($post_id, $count_key, ++$count);
        }
        wp_die();
    }
}
