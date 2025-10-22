<?php
/**
 * Plugin Name: Beautiful Post
 * Description: طراحی حرفه‌ای و جذاب برای صفحه تک مقاله (فقط برای پست‌های بلاگ)
 * Version: 1.0.7
 * Author: Arad Branding
 */

if (!defined('ABSPATH')) exit;

// Load Plugin Update Checker
require plugin_dir_path(__FILE__) . 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/javidmirzaei/single-post-wp/',
    __FILE__,
    'beautiful-post'
);

// Optional: Set the branch to track (defaults to 'main')
$myUpdateChecker->setBranch('main');

// Enable release assets (ZIP files from GitHub Releases)
$myUpdateChecker->getVcsApi()->enableReleaseAssets();

// Load admin settings
require_once plugin_dir_path(__FILE__) . 'admin-settings.php';

class Beautiful_Post_Plugin {
    
    private $settings;
    
    public function __construct() {
        $this->settings = get_option('beautiful_post_settings', array());
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_head', array($this, 'custom_styles'));
        add_filter('the_content', array($this, 'modify_single_post_content'), 20);
        add_filter('comments_template', array($this, 'custom_comments_template'));
    }
    
    public function enqueue_styles() {
        if (is_single() && get_post_type() === 'post') {
            wp_enqueue_style('beautiful-post', plugin_dir_url(__FILE__) . 'style.css', array(), '1.0');
        }
    }
    
    public function custom_styles() {
        if (!is_single() || get_post_type() !== 'post') {
            return;
        }
        
        $css = '<style id="beautiful-post-custom-styles">';
        
        // Author Box
        if (!empty($this->settings['author_box_bg'])) {
            $css .= '.bp-author-box { background: ' . esc_attr($this->settings['author_box_bg']) . '; }';
        }
        if (!empty($this->settings['author_box_text_color'])) {
            $css .= '.bp-author-name, .bp-author-bio, .bp-author-link { color: ' . esc_attr($this->settings['author_box_text_color']) . '; }';
        }
        if (!empty($this->settings['author_box_font_size'])) {
            $css .= '.bp-author-bio { font-size: ' . intval($this->settings['author_box_font_size']) . 'px; }';
        }
        
        $css .= '</style>';
        echo $css;
    }
    
    public function enqueue_scripts() {
        if (is_single() && get_post_type() === 'post') {
            wp_enqueue_script('beautiful-post', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), '1.0', true);
        }
    }
    
    public function modify_single_post_content($content) {
        if (!is_single() || !in_the_loop() || !is_main_query()) {
            return $content;
        }
        
        // فقط برای پست‌های معمولی (بلاگ) - نه محصولات یا post type های دیگر
        if (get_post_type() !== 'post') {
            return $content;
        }
        
        $post_id = get_the_ID();
        $output = '';
        
        // Post Header
        $output .= $this->render_post_header($post_id);
        
        // Featured Image
        $thumbnail = get_the_post_thumbnail_url($post_id, 'large');
        if ($thumbnail) {
            $output .= '<div class="bp-featured-image">';
            $output .= '<img src="' . esc_url($thumbnail) . '" alt="' . esc_attr(get_the_title()) . '">';
            $output .= '</div>';
        }
        
        // Meta Information
        $output .= $this->render_meta_info($post_id);
        
        // Main Content
        $output .= '<div class="bp-content-wrapper">';
        $output .= '<article class="bp-article-content">';
        $output .= $content;
        $output .= '</article>';
        $output .= '</div>';
        
        // Author Info
        if ($this->get_setting('show_author', true)) {
            $output .= $this->render_author_box($post_id);
        }
        
        // Share Buttons
        if ($this->get_setting('show_share_buttons', true)) {
            $output .= $this->render_share_buttons($post_id);
        }
        
        // Related Posts
        if ($this->get_setting('show_related_posts', true)) {
            $output .= $this->render_related_posts($post_id);
        }
        
        return $output;
    }
    
    private function render_post_header($post_id) {
        $output = '<div class="bp-post-header">';
        $output .= '<h1 class="bp-post-title">' . get_the_title() . '</h1>';
        $output .= '</div>';
        
        return $output;
    }
    
    private function render_meta_info($post_id) {
        $show_date = $this->get_setting('show_meta_date', true);
        $show_category = $this->get_setting('show_meta_category', true);
        $show_author = $this->get_setting('show_meta_author', true);
        $show_comments = $this->get_setting('show_meta_comments', true);
        
        // اگر هیچکدام فعال نباشند، چیزی نمایش نده
        if (!$show_date && !$show_category && !$show_author && !$show_comments) {
            return '';
        }
        
        $output = '<div class="bp-meta-info">';
        $output .= '<div class="bp-meta-container">';
        
        // Date
        if ($show_date) {
            $output .= '<span class="bp-meta-item bp-meta-date">';
            $output .= '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
            $output .= '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>';
            $output .= '<line x1="16" y1="2" x2="16" y2="6"></line>';
            $output .= '<line x1="8" y1="2" x2="8" y2="6"></line>';
            $output .= '<line x1="3" y1="10" x2="21" y2="10"></line>';
            $output .= '</svg>';
            $output .= $this->convert_to_persian_numbers(get_the_date('j F Y'));
            $output .= '</span>';
        }
        
        // Categories
        if ($show_category) {
            $categories = get_the_category($post_id);
            if (!empty($categories)) {
                $output .= '<span class="bp-meta-item bp-meta-category">';
                $output .= '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
                $output .= '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>';
                $output .= '<line x1="7" y1="7" x2="7.01" y2="7"></line>';
                $output .= '</svg>';
                foreach ($categories as $category) {
                    $output .= '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                }
                $output .= '</span>';
            }
        }
        
        // Author
        if ($show_author) {
            $output .= '<span class="bp-meta-item bp-meta-author">';
            $output .= '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
            $output .= '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>';
            $output .= '<circle cx="12" cy="7" r="4"></circle>';
            $output .= '</svg>';
            $output .= get_the_author();
            $output .= '</span>';
        }
        
        // Comments Count
        if ($show_comments) {
            $comments_count = get_comments_number($post_id);
            $output .= '<span class="bp-meta-item bp-meta-comments">';
            $output .= '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
            $output .= '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>';
            $output .= '</svg>';
            $output .= $this->convert_to_persian_numbers($comments_count) . ' نظر';
            $output .= '</span>';
        }
        
        $output .= '</div>';
        $output .= '</div>';
        
        return $output;
    }
    
    private function render_author_box($post_id) {
        $author_id = get_post_field('post_author', $post_id);
        $author_name = get_the_author_meta('display_name', $author_id);
        $author_bio = get_the_author_meta('description', $author_id);
        $author_avatar = get_avatar($author_id, 80);
        $author_posts_url = get_author_posts_url($author_id);
        
        $output = '<div class="bp-author-box">';
        $output .= '<div class="bp-author-avatar">' . $author_avatar . '</div>';
        $output .= '<div class="bp-author-info">';
        $output .= '<h3 class="bp-author-name">' . esc_html($author_name) . '</h3>';
        if ($author_bio) {
            $output .= '<p class="bp-author-bio">' . esc_html($author_bio) . '</p>';
        }
        $output .= '<a href="' . esc_url($author_posts_url) . '" class="bp-author-link">مشاهده تمام مقالات</a>';
        $output .= '</div>';
        $output .= '</div>';
        
        return $output;
    }
    
    private function render_share_buttons($post_id) {
        $url = get_permalink($post_id);
        $title = get_the_title($post_id);
        
        $share_platforms = array();
        
        if ($this->get_setting('share_telegram', true)) {
            $share_platforms['telegram'] = array(
                'name' => 'تلگرام',
                'url' => 'https://t.me/share/url?url=' . urlencode($url) . '&text=' . urlencode($title),
                'icon' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/></svg>'
            );
        }
        
        if ($this->get_setting('share_whatsapp', true)) {
            $share_platforms['whatsapp'] = array(
                'name' => 'واتساپ',
                'url' => 'https://wa.me/?text=' . urlencode($title . ' ' . $url),
                'icon' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>'
            );
        }
        
        if ($this->get_setting('share_twitter', true)) {
            $share_platforms['twitter'] = array(
                'name' => 'توییتر',
                'url' => 'https://twitter.com/intent/tweet?url=' . urlencode($url) . '&text=' . urlencode($title),
                'icon' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>'
            );
        }
        
        if ($this->get_setting('share_linkedin', true)) {
            $share_platforms['linkedin'] = array(
                'name' => 'لینکدین',
                'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($url),
                'icon' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>'
            );
        }
        
        if (empty($share_platforms)) {
            return '';
        }
        
        $output = '<div class="bp-share-section">';
        $output .= '<h3 class="bp-share-title">اشتراک‌گذاری این مقاله</h3>';
        $output .= '<div class="bp-share-buttons">';
        
        foreach ($share_platforms as $key => $platform) {
            $output .= '<a href="' . esc_url($platform['url']) . '" class="bp-share-btn bp-share-' . $key . '" target="_blank" rel="noopener">';
            $output .= $platform['icon'];
            $output .= '<span>' . $platform['name'] . '</span>';
            $output .= '</a>';
        }
        
        $output .= '</div>';
        $output .= '</div>';
        
        return $output;
    }
    
    private function render_related_posts($post_id) {
        $related_count = $this->get_setting('related_posts_count', 3);
        $related_columns = $this->get_setting('related_posts_columns', 3);
        $categories = wp_get_post_categories($post_id);
        
        if (empty($categories)) {
            return '';
        }
        
        $args = array(
            'category__in' => $categories,
            'post__not_in' => array($post_id),
            'posts_per_page' => $related_count,
            'ignore_sticky_posts' => 1
        );
        
        $related_query = new WP_Query($args);
        
        if (!$related_query->have_posts()) {
            return '';
        }
        
        $output = '<div class="bp-related-posts">';
        $output .= '<h3 class="bp-related-title">مقالات مرتبط</h3>';
        $output .= '<div class="bp-related-grid bp-related-cols-' . intval($related_columns) . '">';
        
        while ($related_query->have_posts()) {
            $related_query->the_post();
            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            
            $output .= '<article class="bp-related-card">';
            if ($thumbnail) {
                $output .= '<div class="bp-related-image" style="background-image: url(' . esc_url($thumbnail) . ');"></div>';
            }
            $output .= '<div class="bp-related-content">';
            $output .= '<h4 class="bp-related-card-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h4>';
            $output .= '<span class="bp-related-date">' . $this->convert_to_persian_numbers(get_the_date('j F Y')) . '</span>';
            $output .= '</div>';
            $output .= '</article>';
        }
        
        wp_reset_postdata();
        
        $output .= '</div>';
        $output .= '</div>';
        
        return $output;
    }
    
    public function custom_comments_template($template) {
        if (is_single() && get_post_type() === 'post') {
            $plugin_template = plugin_dir_path(__FILE__) . 'comments-template.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        return $template;
    }
    
    private function convert_to_persian_numbers($string) {
        $persian_numbers = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $english_numbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        return str_replace($english_numbers, $persian_numbers, $string);
    }
    
    private function get_setting($key, $default = false) {
        return isset($this->settings[$key]) ? $this->settings[$key] : $default;
    }
}

new Beautiful_Post_Plugin();

