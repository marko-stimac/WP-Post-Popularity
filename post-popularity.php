<?php
/**
 * Plugin Name: Post Popularity
 * Description: Keeps track of all time popular posts and pages
 * Version: 1.1.0
 * Author: Marko Štimac
 * Author URI: https://marko-stimac.github.io/
 */

namespace ms\PostPopularity;

defined('ABSPATH') || exit;

require_once 'includes/class-backend.php';
require_once 'includes/class-frontend.php';

new Backend();
new Frontend();