<?php
if (!defined('ABSPATH')) exit;

class Beautiful_Post_Settings {
    
    private $options;
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }
    
    public function add_plugin_page() {
        add_menu_page(
            'تنظیمات صفحه پست',
            'تنظیمات صفحه پست',
            'manage_options',
            'beautiful-post-settings',
            array($this, 'create_admin_page'),
            'dashicons-format-aside',
            66
        );
    }
    
    public function create_admin_page() {
        $this->options = get_option('beautiful_post_settings');
        ?>
        <div class="wrap" dir="rtl">
            <h1>⚙️ تنظیمات صفحه پست</h1>
            <p>تنظیمات صفحه تک مقاله را سفارشی‌سازی کنید</p>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('beautiful_post_option_group');
                do_settings_sections('beautiful-post-settings');
                submit_button('ذخیره تنظیمات');
                ?>
            </form>
            
            <div style="margin-top: 30px; padding: 20px; background: #fff; border-right: 4px solid #667eea; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3>📖 راهنما</h3>
                <p>این پلاگین به صورت خودکار صفحات تک مقاله را زیباسازی می‌کند. تنظیمات را طبق نیاز خود تغییر دهید.</p>
                <p><strong>💡 نکته:</strong> بعد از ذخیره تنظیمات، هر صفحه مقاله‌ای را رفرش کنید تا تغییرات اعمال شوند.</p>
            </div>
        </div>
        
        <style>
            .form-table th {
                text-align: right;
                padding-right: 0;
                padding-left: 10px;
            }
            .form-table td {
                text-align: right;
            }
            h2.title {
                border-right: 4px solid #667eea;
                padding-right: 15px;
                margin-top: 30px;
            }
        </style>
        <?php
    }
    
    public function page_init() {
        register_setting(
            'beautiful_post_option_group',
            'beautiful_post_settings',
            array($this, 'sanitize')
        );
        
        // Author Section
        add_settings_section(
            'author_section',
            '👤 تنظیمات نویسنده',
            array($this, 'author_section_info'),
            'beautiful-post-settings'
        );
        
        add_settings_field(
            'show_author',
            'نمایش باکس نویسنده',
            array($this, 'show_author_callback'),
            'beautiful-post-settings',
            'author_section'
        );
        
        // Share Buttons Section
        add_settings_section(
            'share_section',
            '🔗 تنظیمات اشتراک‌گذاری',
            array($this, 'share_section_info'),
            'beautiful-post-settings'
        );
        
        add_settings_field(
            'show_share_buttons',
            'نمایش دکمه‌های اشتراک‌گذاری',
            array($this, 'show_share_buttons_callback'),
            'beautiful-post-settings',
            'share_section'
        );
        
        add_settings_field(
            'share_telegram',
            'تلگرام',
            array($this, 'share_telegram_callback'),
            'beautiful-post-settings',
            'share_section'
        );
        
        add_settings_field(
            'share_whatsapp',
            'واتساپ',
            array($this, 'share_whatsapp_callback'),
            'beautiful-post-settings',
            'share_section'
        );
        
        add_settings_field(
            'share_twitter',
            'توییتر',
            array($this, 'share_twitter_callback'),
            'beautiful-post-settings',
            'share_section'
        );
        
        add_settings_field(
            'share_linkedin',
            'لینکدین',
            array($this, 'share_linkedin_callback'),
            'beautiful-post-settings',
            'share_section'
        );
        
        // Related Posts Section
        add_settings_section(
            'related_section',
            '📚 تنظیمات مقالات مرتبط',
            array($this, 'related_section_info'),
            'beautiful-post-settings'
        );
        
        add_settings_field(
            'show_related_posts',
            'نمایش مقالات مرتبط',
            array($this, 'show_related_posts_callback'),
            'beautiful-post-settings',
            'related_section'
        );
        
        add_settings_field(
            'related_posts_count',
            'تعداد مقالات مرتبط',
            array($this, 'related_posts_count_callback'),
            'beautiful-post-settings',
            'related_section'
        );
        
        add_settings_field(
            'related_posts_columns',
            'تعداد ستون‌ها',
            array($this, 'related_posts_columns_callback'),
            'beautiful-post-settings',
            'related_section'
        );
        
        // Meta Info Section
        add_settings_section(
            'meta_section',
            '📊 تنظیمات متا اطلاعات',
            array($this, 'meta_section_info'),
            'beautiful-post-settings'
        );
        
        add_settings_field(
            'show_meta_date',
            'نمایش تاریخ',
            array($this, 'show_meta_date_callback'),
            'beautiful-post-settings',
            'meta_section'
        );
        
        add_settings_field(
            'show_meta_category',
            'نمایش دسته‌بندی',
            array($this, 'show_meta_category_callback'),
            'beautiful-post-settings',
            'meta_section'
        );
        
        add_settings_field(
            'show_meta_author',
            'نمایش نویسنده (در متا)',
            array($this, 'show_meta_author_callback'),
            'beautiful-post-settings',
            'meta_section'
        );
        
        add_settings_field(
            'show_meta_comments',
            'نمایش تعداد نظرات',
            array($this, 'show_meta_comments_callback'),
            'beautiful-post-settings',
            'meta_section'
        );
        
        // Author Box Styling
        add_settings_section(
            'author_style_section',
            '🎨 استایل باکس نویسنده',
            array($this, 'author_style_section_info'),
            'beautiful-post-settings'
        );
        
        add_settings_field(
            'author_box_bg',
            'رنگ پس‌زمینه',
            array($this, 'author_box_bg_callback'),
            'beautiful-post-settings',
            'author_style_section'
        );
        
        add_settings_field(
            'author_box_text_color',
            'رنگ متن',
            array($this, 'author_box_text_color_callback'),
            'beautiful-post-settings',
            'author_style_section'
        );
        
        add_settings_field(
            'author_box_font_size',
            'سایز فونت',
            array($this, 'author_box_font_size_callback'),
            'beautiful-post-settings',
            'author_style_section'
        );
    }
    
    public function sanitize($input) {
        $new_input = array();
        
        // Author
        $new_input['show_author'] = isset($input['show_author']) ? true : false;
        
        // Share Buttons
        $new_input['show_share_buttons'] = isset($input['show_share_buttons']) ? true : false;
        $new_input['share_telegram'] = isset($input['share_telegram']) ? true : false;
        $new_input['share_whatsapp'] = isset($input['share_whatsapp']) ? true : false;
        $new_input['share_twitter'] = isset($input['share_twitter']) ? true : false;
        $new_input['share_linkedin'] = isset($input['share_linkedin']) ? true : false;
        
        // Related Posts
        $new_input['show_related_posts'] = isset($input['show_related_posts']) ? true : false;
        $new_input['related_posts_count'] = isset($input['related_posts_count']) ? absint($input['related_posts_count']) : 3;
        $new_input['related_posts_columns'] = isset($input['related_posts_columns']) ? absint($input['related_posts_columns']) : 3;
        
        // Meta Info
        $new_input['show_meta_date'] = isset($input['show_meta_date']) ? true : false;
        $new_input['show_meta_category'] = isset($input['show_meta_category']) ? true : false;
        $new_input['show_meta_author'] = isset($input['show_meta_author']) ? true : false;
        $new_input['show_meta_comments'] = isset($input['show_meta_comments']) ? true : false;
        
        // Author Box Style
        $new_input['author_box_bg'] = isset($input['author_box_bg']) ? sanitize_text_field($input['author_box_bg']) : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        $new_input['author_box_text_color'] = isset($input['author_box_text_color']) ? sanitize_hex_color($input['author_box_text_color']) : '#ffffff';
        $new_input['author_box_font_size'] = isset($input['author_box_font_size']) ? absint($input['author_box_font_size']) : 16;
        
        return $new_input;
    }
    
    // Section Callbacks
    public function author_section_info() {
        echo '<p>تنظیمات نمایش اطلاعات نویسنده در انتهای مقاله</p>';
    }
    
    public function share_section_info() {
        echo '<p>تنظیمات دکمه‌های اشتراک‌گذاری در شبکه‌های اجتماعی</p>';
    }
    
    public function related_section_info() {
        echo '<p>تنظیمات نمایش مقالات مرتبط در انتهای صفحه</p>';
    }
    
    public function meta_section_info() {
        echo '<p>انتخاب کنید کدام اطلاعات در بالای مقاله نمایش داده شود</p>';
    }
    
    public function author_style_section_info() {
        echo '<p>سفارشی‌سازی ظاهر باکس نویسنده</p>';
    }
    
    // Field Callbacks
    public function show_author_callback() {
        printf(
            '<label><input type="checkbox" name="beautiful_post_settings[show_author]" value="1" %s /> نمایش باکس اطلاعات نویسنده</label>',
            checked($this->get_option('show_author', true), true, false)
        );
    }
    
    public function show_share_buttons_callback() {
        printf(
            '<label><input type="checkbox" name="beautiful_post_settings[show_share_buttons]" value="1" %s /> نمایش دکمه‌های اشتراک‌گذاری</label>',
            checked($this->get_option('show_share_buttons', true), true, false)
        );
    }
    
    public function share_telegram_callback() {
        printf(
            '<label><input type="checkbox" name="beautiful_post_settings[share_telegram]" value="1" %s /> فعال کردن اشتراک‌گذاری در تلگرام</label>',
            checked($this->get_option('share_telegram', true), true, false)
        );
    }
    
    public function share_whatsapp_callback() {
        printf(
            '<label><input type="checkbox" name="beautiful_post_settings[share_whatsapp]" value="1" %s /> فعال کردن اشتراک‌گذاری در واتساپ</label>',
            checked($this->get_option('share_whatsapp', true), true, false)
        );
    }
    
    public function share_twitter_callback() {
        printf(
            '<label><input type="checkbox" name="beautiful_post_settings[share_twitter]" value="1" %s /> فعال کردن اشتراک‌گذاری در توییتر</label>',
            checked($this->get_option('share_twitter', true), true, false)
        );
    }
    
    public function share_linkedin_callback() {
        printf(
            '<label><input type="checkbox" name="beautiful_post_settings[share_linkedin]" value="1" %s /> فعال کردن اشتراک‌گذاری در لینکدین</label>',
            checked($this->get_option('share_linkedin', true), true, false)
        );
    }
    
    public function show_related_posts_callback() {
        printf(
            '<label><input type="checkbox" name="beautiful_post_settings[show_related_posts]" value="1" %s /> نمایش مقالات مرتبط</label>',
            checked($this->get_option('show_related_posts', true), true, false)
        );
    }
    
    public function related_posts_count_callback() {
        printf(
            '<input type="number" name="beautiful_post_settings[related_posts_count]" value="%s" min="1" max="12" /> <small>تعداد مقالات مرتبط که نمایش داده شود</small>',
            esc_attr($this->get_option('related_posts_count', 3))
        );
    }
    
    public function related_posts_columns_callback() {
        $value = $this->get_option('related_posts_columns', 3);
        ?>
        <select name="beautiful_post_settings[related_posts_columns]">
            <option value="1" <?php selected($value, 1); ?>>۱ ستون</option>
            <option value="2" <?php selected($value, 2); ?>>۲ ستون</option>
            <option value="3" <?php selected($value, 3); ?>>۳ ستون</option>
            <option value="4" <?php selected($value, 4); ?>>۴ ستون</option>
        </select>
        <?php
    }
    
    public function show_meta_date_callback() {
        printf(
            '<label><input type="checkbox" name="beautiful_post_settings[show_meta_date]" value="1" %s /> نمایش تاریخ انتشار</label>',
            checked($this->get_option('show_meta_date', true), true, false)
        );
    }
    
    public function show_meta_category_callback() {
        printf(
            '<label><input type="checkbox" name="beautiful_post_settings[show_meta_category]" value="1" %s /> نمایش دسته‌بندی</label>',
            checked($this->get_option('show_meta_category', true), true, false)
        );
    }
    
    public function show_meta_author_callback() {
        printf(
            '<label><input type="checkbox" name="beautiful_post_settings[show_meta_author]" value="1" %s /> نمایش نویسنده</label>',
            checked($this->get_option('show_meta_author', true), true, false)
        );
    }
    
    public function show_meta_comments_callback() {
        printf(
            '<label><input type="checkbox" name="beautiful_post_settings[show_meta_comments]" value="1" %s /> نمایش تعداد نظرات</label>',
            checked($this->get_option('show_meta_comments', true), true, false)
        );
    }
    
    public function author_box_bg_callback() {
        printf(
            '<input type="text" name="beautiful_post_settings[author_box_bg]" value="%s" placeholder="linear-gradient(135deg, #667eea 0%%, #764ba2 100%%)" style="width: 400px;" /><br><small>می‌توانید از gradient استفاده کنید</small>',
            esc_attr($this->get_option('author_box_bg', 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'))
        );
    }
    
    public function author_box_text_color_callback() {
        printf(
            '<input type="text" name="beautiful_post_settings[author_box_text_color]" value="%s" /><br><small>رنگ متن و عناوین در باکس نویسنده</small>',
            esc_attr($this->get_option('author_box_text_color', '#ffffff'))
        );
    }
    
    public function author_box_font_size_callback() {
        printf(
            '<input type="number" name="beautiful_post_settings[author_box_font_size]" value="%s" min="12" max="24" /> px',
            esc_attr($this->get_option('author_box_font_size', 16))
        );
    }
    
    private function get_option($key, $default = false) {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }
        return $default;
    }
}

if (is_admin()) {
    new Beautiful_Post_Settings();
}

