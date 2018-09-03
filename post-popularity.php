<?php
/**
 * Plugin Name: BI Post Popularity
 * Description: Keeps track of all time popular posts
 * Version: 1.0.0
 */

namespace Bideja\PostPopularity;

if (!defined('ABSPATH')) {
    exit;
}

require 'includes/backend.php';
require 'includes/frontend.php';

new Backend();
$post_popularity = new Frontend();
add_shortcode('post-popularity', array($post_popularity, 'showComponent'));
