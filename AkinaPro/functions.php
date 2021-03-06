<?php
/**
 * Akina functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Akina
 */

define( 'Akina_Version', '20161228' );

if (!function_exists('akina_setup')) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */

    if (!function_exists('optionsframework_init')) {
        define('OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/');
        require_once dirname(__FILE__) . '/inc/options-framework.php';
    }


    function akina_setup()
    {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on Akina, use a find and replace
         * to change 'akina' to the name of your theme in all the template files.
         */
        load_theme_textdomain('akina', get_template_directory() . '/languages');


        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');
        set_post_thumbnail_size(150, 150, true);

        // This theme uses wp_nav_menu() in one location.
        // 注册菜单
        if (function_exists('register_nav_menus')) {
            register_nav_menus(array(
                'header_nav' => __('站点导航')
            ));
        }
        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));

        /*
         * Enable support for Post Formats.
         * See https://developer.wordpress.org/themes/functionality/post-formats/
         */
        add_theme_support('post-formats', array(
            'aside',
            'image',
            'status',
        ));

        // Set up the WordPress core custom background feature.
        add_theme_support('custom-background', apply_filters('akina_custom_background_args', array(
            'default-color' => 'ffffff',
            'default-image' => '',
        )));

        add_filter('pre_option_link_manager_enabled', '__return_true');

        // 优化代码
        add_filter('show_admin_bar', '__return_false');
        //去除头部冗余代码
        remove_action('wp_head', 'feed_links_extra', 3);
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'start_post_rel_link', 10, 0);
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wp_generator'); //隐藏wordpress版本
        remove_filter('the_content', 'wptexturize'); //取消标点符号转义

        remove_action('rest_api_init', 'wp_oembed_register_route');
        remove_filter('rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10, 4);
        remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
        remove_filter('oembed_response_data', 'get_oembed_response_data_rich', 10, 4);
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
        // Remove the Link header for the WP REST API
        // [link] => <http://cnzhx.net/wp-json/>; rel="https://api.w.org/"
        remove_action('template_redirect', 'rest_output_link_header', 11, 0);

        function coolwp_remove_open_sans_from_wp_core()
        {
            wp_deregister_style('open-sans');
            wp_register_style('open-sans', false);
            wp_enqueue_style('open-sans', '');
        }

        add_action('init', 'coolwp_remove_open_sans_from_wp_core');

        /**
         * Disable the emoji's
         */
        function disable_emojis()
        {
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('admin_print_scripts', 'print_emoji_detection_script');
            remove_action('wp_print_styles', 'print_emoji_styles');
            remove_action('admin_print_styles', 'print_emoji_styles');
            remove_filter('the_content_feed', 'wp_staticize_emoji');
            remove_filter('comment_text_rss', 'wp_staticize_emoji');
            remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
            add_filter('tiny_mce_plugins', 'disable_emojis_tinymce');
        }

        add_action('init', 'disable_emojis');

        /**
         * Filter function used to remove the tinymce emoji plugin.
         *
         * @param    array $plugins
         * @return   array             Difference betwen the two arrays
         */
        function disable_emojis_tinymce($plugins)
        {
            if (is_array($plugins)) {
                return array_diff($plugins, array('wpemoji'));
            } else {
                return array();
            }
        }

        // 移除菜单冗余代码
        add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1);
        add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1);
        add_filter('page_css_class', 'my_css_attributes_filter', 100, 1);
        function my_css_attributes_filter($var)
        {
            return is_array($var) ? array_intersect($var, array('current-menu-item', 'current-post-ancestor', 'current-menu-ancestor', 'current-menu-parent')) : '';
        }

    }
endif;
add_action('after_setup_theme', 'akina_setup');

function admin_lettering()
{
    echo '<style type="text/css">
     body{ font-family: Microsoft YaHei;}
    </style>';
}

add_action('admin_head', 'admin_lettering');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function akina_content_width()
{
    $GLOBALS['content_width'] = apply_filters('akina_content_width', 640);
}

add_action('after_setup_theme', 'akina_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
/*function akina_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'akina' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'akina' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'akina_widgets_init' );
*/

/**
 * Enqueue scripts and styles.
 */
function akina_scripts()
{
    //wp_enqueue_style( 'akina-style', '//blogresource.oss-cn-hangzhou.aliyuncs.com/css/style.css',Akina_Version );

    wp_enqueue_style('akina-style', get_stylesheet_uri(),Akina_Version);

    wp_enqueue_script('jquery_js', '//cdn.bootcss.com/jquery/1.8.2/jquery.min.js', array(), Akina_Version, true);

    wp_enqueue_script('jquery_pjax_js', '//cdn.bootcss.com/jquery.pjax/1.9.6/jquery.pjax.min.js', array(), Akina_Version, true);

    wp_enqueue_script('baguetteBox_js', '//cdn.bootcss.com/baguettebox.js/1.8.0/baguetteBox.min.js', array(), Akina_Version, true);

    //wp_enqueue_script('functions', '//blogresource.oss-cn-hangzhou.aliyuncs.com/javascript/functions.min.js', array(), Akina_Version, true);

    wp_enqueue_script('functions', get_template_directory_uri() . '/js/functions.min.js', array(), Akina_Version, true);

    wp_localize_script('functions', 'themeAdminAjax', array('url' => admin_url('admin-ajax.php')));

    //插入脚本
    $prism_highlighters = akina_option('prism_highlighters') ? 'yes' : 'no';
    wp_localize_script('functions','iiong', array(
        'prism_highlighters' => $prism_highlighters
    ));

}

add_action('wp_enqueue_scripts', 'akina_scripts');


/**
 * load .php.
 */
require_once('inc/functions-themes.php');

require_once('inc/functions-customize.php');


/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Akina
 */

if (!function_exists('akina_posted_on')) :
    /**
     * Prints HTML with meta information for the current post-date/time and author.
     */
    function akina_posted_on()
    {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf($time_string,
            esc_attr(get_the_date('c')),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date('c')),
            esc_html(get_the_modified_date())
        );

        $posted_on = sprintf(
            esc_html_x('Posted on %s', 'post date', 'akina'),
            '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
        );

        $byline = sprintf(
            esc_html_x('by %s', 'post author', 'akina'),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
        );

        echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

    }
endif;

if (!function_exists('akina_entry_footer')) :
    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function akina_entry_footer()
    {
        // Hide category and tag text for pages.
        if ('post' === get_post_type()) {
            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list(esc_html__(', ', 'akina'));
            if ($categories_list && akina_categorized_blog()) {
                printf('<span class="cat-links">' . esc_html__('Posted in %1$s', 'akina') . '</span>', $categories_list); // WPCS: XSS OK.
            }

            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list('', esc_html__(', ', 'akina'));
            if ($tags_list) {
                printf('<span class="tags-links">' . esc_html__('Tagged %1$s', 'akina') . '</span>', $tags_list); // WPCS: XSS OK.
            }
        }

        if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
            echo '<span class="comments-link">';
            /* translators: %s: post title */
            comments_popup_link(sprintf(wp_kses(__('Leave a Comment<span class="screen-reader-text"> on %s</span>', 'akina'), array('span' => array('class' => array()))), get_the_title()));
            echo '</span>';
        }

        edit_post_link(
            sprintf(
            /* translators: %s: Name of current post */
                esc_html__('Edit %s', 'akina'),
                the_title('<span class="screen-reader-text">"', '"</span>', false)
            ),
            '<span class="edit-link">',
            '</span>'
        );
    }
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function akina_categorized_blog()
{
    if (false === ($all_the_cool_cats = get_transient('akina_categories'))) {
        // Create an array of all the categories that are attached to posts.
        $all_the_cool_cats = get_categories(array(
            'fields' => 'ids',
            'hide_empty' => 1,
            // We only need to know if there is more than one category.
            'number' => 2,
        ));

        // Count the number of categories that are attached to the posts.
        $all_the_cool_cats = count($all_the_cool_cats);

        set_transient('akina_categories', $all_the_cool_cats);
    }

    if ($all_the_cool_cats > 1) {
        // This blog has more than 1 category so akina_categorized_blog should return true.
        return true;
    } else {
        // This blog has only 1 category so akina_categorized_blog should return false.
        return false;
    }
}

/**
 * Flush out the transients used in akina_categorized_blog.
 */
function akina_category_transient_flusher()
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    // Like, beat it. Dig?
    delete_transient('akina_categories');
}

add_action('edit_category', 'akina_category_transient_flusher');
add_action('save_post', 'akina_category_transient_flusher');

/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Akina
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function akina_body_classes($classes)
{
    // Adds a class of group-blog to blogs with more than 1 published author.
    if (is_multi_author()) {
        $classes[] = 'group-blog';
    }

    // Adds a class of hfeed to non-singular pages.
    if (!is_singular()) {
        $classes[] = 'hfeed';
    }

    return $classes;
}

add_filter('body_class', 'akina_body_classes');

/*-----------------------------------------------------------------------------------*/
/* COMMENT FORMATTING
/*-----------------------------------------------------------------------------------*/

if (!function_exists('akina_comment_format')) {
    function akina_comment_format($comment, $args, $depth)
    {
        $GLOBALS['comment'] = $comment;
        ?>
        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID ?>">
        <div id="comment-<?php comment_ID(); ?>" class="comment_body contents">
            <div class="profile">
                <a target="_blank" rel="nofollow"
                   href="<?php comment_author_url(); ?>"><?php echo get_avatar($comment, 50); ?></a>
            </div>
            <section class="commeta">
                <div class="left">
                    <h4 class="author"><a target="_blank" rel="nofollow"
                                          href="<?php comment_author_url(); ?>"><?php echo get_avatar($comment, 50); ?><?php comment_author(); ?>
                            <span class="isauthor"
                                  title="<?php esc_attr_e('Author', 'akina'); ?>"><i
                                    class="iconfont">&#xe601;</i></span></a></h4>
                </div>
                <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                <div class="right">
                    <div class="info">
                        <time
                            datetime="<?php comment_date('Y-m-d'); ?>"><?php comment_date(get_option('date_format')); ?></time>
                    </div>
                </div>
            </section>
            <div class="body">
                <?php comment_text(); ?>
            </div>
        </div>
        <hr>
        <?php
    }
}

/**
 * post views.
 */
function get_post_views($post_id)
{

    $count_key = 'views';
    $count = get_post_meta($post_id, $count_key, true);

    if ($count == '') {
        delete_post_meta($post_id, $count_key);
        add_post_meta($post_id, $count_key, '0');
        $count = '0';
    }

    echo number_format_i18n($count);

}

function set_post_views()
{

    global $post;

    $post_id = $post->ID;
    $count_key = 'views';
    $count = get_post_meta($post_id, $count_key, true);

    if (is_single() || is_page()) {

        if ($count == '') {
            delete_post_meta($post_id, $count_key);
            add_post_meta($post_id, $count_key, '0');
        } else {
            update_post_meta($post_id, $count_key, $count + 1);
        }

    }

}

add_action('get_header', 'set_post_views');

//检测主题更新
require_once(TEMPLATEPATH . '/inc/theme-update-checker.php');
$wpdaxue_update_checker = new ThemeUpdateChecker(
    'AkinaPro', //主题名字
    'https://iiong.com/wp-upload/themes/info.json'  //info.json 的访问地址
);

// Gravatar头像使用中国服务器
function dw_get_avatar($avatar)
{
    $avatar = str_replace(array("www.gravatar.com", "0.gravatar.com", "1.gravatar.com", "2.gravatar.com", "secure.gravatar.com"), "image.biji.io", $avatar);
    return $avatar;
}

add_filter('get_avatar', 'dw_get_avatar', 10, 3);
// 阻止站内文章互相Pingback 
function theme_noself_ping(&$links)
{
    $home = get_option('home');
    foreach ($links as $l => $link)
        if (0 === strpos($link, $home))
            unset($links[$l]);
}

add_action('pre_ping', 'theme_noself_ping');


// 编辑器增强
function enable_more_buttons($buttons)
{
    $buttons[] = 'hr';
    $buttons[] = 'del';
    $buttons[] = 'sub';
    $buttons[] = 'sup';
    $buttons[] = 'fontselect';
    $buttons[] = 'fontsizeselect';
    $buttons[] = 'cleanup';
    $buttons[] = 'styleselect';
    $buttons[] = 'wp_page';
    $buttons[] = 'anchor';
    $buttons[] = 'backcolor';
    return $buttons;
}

add_filter("mce_buttons_3", "enable_more_buttons");

/*
 * Ajax评论
 */
if (version_compare($GLOBALS['wp_version'], '4.4-alpha', '<')) {
    wp_die('请升级到4.4以上版本');
}
// 提示
if (!function_exists('ajax_comment_err')) {
    function ajax_comment_err($t)
    {
        header('HTTP/1.0 500 Internal Server Error');
        header('Content-Type: text/plain;charset=UTF-8');
        echo $t;
        exit;
    }
}

// 拦截机器评论
class anti_spam
{
    function anti_spam()
    {
        if (!current_user_can('level_0')) {
            add_action('template_redirect', array($this, 'w_tb'), 1);
            add_action('init', array($this, 'gate'), 1);
            add_action('preprocess_comment', array($this, 'sink'), 1);
        }
    }

    function w_tb()
    {
        if (is_singular()) {
            ob_start(create_function('$input', 'return preg_replace("#textarea(.*?)name=([\"\'])comment([\"\'])(.+)/textarea>#",
"textarea$1name=$2w$3$4/textarea><textarea name=\"comment\" cols=\"100%\" rows=\"4\" style=\"display:none\"></textarea>",$input);'));
        }
    }

    function gate()
    {
        if (!empty($_POST['w']) && empty($_POST['comment'])) {
            $_POST['comment'] = $_POST['w'];
        } else {
            $request = $_SERVER['REQUEST_URI'];
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '隐瞒';
            $IP = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] . ' (透过代理)' : $_SERVER["REMOTE_ADDR"];
            $way = isset($_POST['w']) ? '手动操作' : '未经评论表格';
            $spamcom = isset($_POST['comment']) ? $_POST['comment'] : null;
            $_POST['spam_confirmed'] = "请求: " . $request . "\n来路: " . $referer . "\nIP: " . $IP . "\n方式: " . $way . "\n內容: " . $spamcom . "\n -- 已备案 --";
        }
    }

    function sink($comment)
    {
        if (!empty($_POST['spam_confirmed'])) {
            if (in_array($comment['comment_type'], array('pingback', 'trackback'))) return $comment;
// 方法一: 直接挡掉, 將 die();
            die();
// 方法二: 标记为 spam, 留在资料库检查是否误判.
// add_filter('pre_comment_approved', create_function('', 'return "spam";'));
// $comment['comment_content'] = "[ 防火墙提示：此条评论疑似Spam! ]\n". $_POST['spam_confirmed'];
        }
        return $comment;
    }
}

$anti_spam = new anti_spam();

function scp_comment_post($incoming_comment)
{
    // 纯英文评论拦截
    if (!preg_match('/[一-龥]/u', $incoming_comment['comment_content']))
        ajax_comment_err('<p><span style="color:#f55;">提交失败：</span>评论必须包含中文（Chinese），请再次尝试！</p>');
    //die(); // 直接挡掉，无提示
    return ($incoming_comment);
}
add_filter('preprocess_comment', 'scp_comment_post');

// 机器评论验证
function siren_robot_comment()
{
    if (!$_POST['no-robot'] && !is_user_logged_in()) {
        ajax_comment_err('<p><span style="color:#f55;">提交失败：</span>左侧必须打勾，请再次尝试！</p>');
    }
}

add_action('pre_comment_on_post', 'siren_robot_comment');


/*
 * 下载按钮美化
 */
function download($atts, $content = null)
{
    return '<a class="download" href="' . $content . '" rel="external"  
target="_blank" title="下载地址">  
<span><i class="iconfont down">&#xe603;</i>Download</span></a>';
}
add_shortcode("download", "download");

add_action('after_wp_tiny_mce', 'bolo_after_wp_tiny_mce');
function bolo_after_wp_tiny_mce($mce_settings)
{
    ?>
    <script type="text/javascript">
        QTags.addButton('download', '下载按钮', "[download]下载地址[/download]");
        function bolo_QTnextpage_arg1() {
        }
    </script>
<?php }

//评论邮件回复
function comment_mail_notify($comment_id)
{
    $comment = get_comment($comment_id);
    $parent_id = $comment->comment_parent ? $comment->comment_parent : '';
    $spam_confirmed = $comment->comment_approved;
    if (($parent_id != '') && ($spam_confirmed != 'spam')) {
        $wp_email = 'webmaster@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
        $to = trim(get_comment($parent_id)->comment_author_email);
        $subject = '你在 [' . get_option("blogname") . '] 的留言有了回应';
        $message = '
    <table border="1" cellpadding="0" cellspacing="0" width="600" align="center" style="border-collapse: collapse; border-style: solid; border-width: 1;border-color:#ddd;">
	<tbody>
          <tr>
            <td>
				<table align="center" border="0" cellpadding="0" cellspacing="0" width="600" height="48" >
                    <tbody><tr>
                        <td width="100" align="center" style="border-right:1px solid #ddd;">
                            <a href="' . home_url() . '/" target="_blank">' . get_option("blogname") . '</a></td>
                        <td width="300" style="padding-left:20px;"><strong>您有一条来自 <a href="' . home_url() . '" target="_blank" style="color:#6ec3c8;text-decoration:none;">' . get_option("blogname") . '</a> 的回复</strong></td>
						</tr>
					</tbody>
				</table>
			</td>
          </tr>
          <tr>
            <td  style="padding:15px;"><p><strong>' . trim(get_comment($parent_id)->comment_author) . '</strong>, 你好!</span>
              <p>你在《' . get_the_title($comment->comment_post_ID) . '》的留言:</p><p style="border-left:3px solid #ddd;padding-left:1rem;color:#999;">'
            . trim(get_comment($parent_id)->comment_content) . '</p><p>
              ' . trim($comment->comment_author) . ' 给你的回复:</p><p style="border-left:3px solid #ddd;padding-left:1rem;color:#999;">'
            . trim($comment->comment_content) . '</p>
        <center ><a href="' . htmlspecialchars(get_comment_link($parent_id)) . '" target="_blank" style="background-color:#6ec3c8; border-radius:10px; display:inline-block; color:#fff; padding:15px 20px 15px 20px; text-decoration:none;margin-top:20px; margin-bottom:20px;">点击查看完整内容</a></center>
</td>
          </tr>
          <tr>
            <td align="center" valign="center" height="38" style="font-size:0.8rem; color:#999;">Copyright © ' . get_option("blogname") . '</td>
          </tr>
		  </tbody>
  </table>';
        $from = "From: \"" . get_option('blogname') . "\" <$wp_email>";
        $headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
        wp_mail($to, $subject, $message, $headers);
    }
}

add_action('comment_post', 'comment_mail_notify');

// 改密保文章正文提示文字
function change_the_password_form() {
    global $post;
    $label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
    $post_password_info = akina_option('post_password_info');
    $output = '<form class="protected-post-form" action="' . get_option('siteurl') . '/wp-login.php?action=postpass" method="post">
    ' . __( "<p>$post_password_info</p>" ) . '
    <p><label for="' . $label . '">' . __("") . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . esc_attr__("提交") . '" /></p>
    </form>';
    return $output;
}
add_filter( 'the_password_form', 'change_the_password_form' );

//自动给WordPress文章或评论内容的站外链接添加Nofollow属性以及新窗口
add_filter('the_content', 'auto_nofollow'); //nofollow文章内容的站外链接
add_filter('comment_text', 'auto_nofollow'); //nofollow评论内容的站外链接
function auto_nofollow($content) {
    return stripslashes(wp_rel_nofollow($content));
    return preg_replace_callback('/<a>]+/', 'auto_nofollow_callback', $content);
}
function auto_nofollow_callback($matches) {
    $link = $matches[0];
    $site_link = get_bloginfo('url');

    if (strpos($link, 'rel') === false) {
        $link = preg_replace("%(href=S(?!$site_link))%i", 'rel="nofollow" $1', $link);
    } elseif (preg_match("%href=S(?!$site_link)%i", $link)) {
        $link = preg_replace('/rel=S(?!nofollow)S*/i', 'rel="nofollow"', $link);
    }
    return $link;
};
function autoblank($text) {
    $return = str_replace('<a', '<a target="_blank"', $text);
    return $return;
}
add_filter('the_content', 'autoblank');

/*
*ajax点赞
*/
add_action('wp_ajax_nopriv_specs_zan', 'specs_zan');
add_action('wp_ajax_specs_zan', 'specs_zan');
function specs_zan()
{
    global $wpdb, $post;
    $id = $_POST["um_id"];
    $action = $_POST["um_action"];
    if ($action == 'ding') {
        $specs_raters = get_post_meta($id, 'specs_zan', true);
        $expire = time() + 99999999;
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false; // make cookies work with localhost
        setcookie('specs_zan_' . $id, $id, $expire, '/', $domain, false);
        if (!$specs_raters || !is_numeric($specs_raters)) {
            update_post_meta($id, 'specs_zan', 1);
        } else {
            update_post_meta($id, 'specs_zan', ($specs_raters + 1));
        }
        echo get_post_meta($id, 'specs_zan', true);
    }
    die;
}

/**
 * AJAX 提交评论
 * @see wp_handle_comment_submission()
 **/
function ajax_comment_submission()
{
    global $comment, $user;
    $comment = wp_handle_comment_submission(wp_unslash($_POST));
    if (is_wp_error($comment)) {
        header('HTTP/1.1 301 Moved Permanently');
        echo $comment->get_error_message();
        exit;
    }
    $user = wp_get_current_user();
    do_action('set_comment_cookies', $comment, $user);
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <div id="comment-<?php comment_ID(); ?>" class="comment_body">
            <div class="profile">
                <a href="<?php comment_author_url(); ?>"><?php echo get_avatar($comment, 50); ?></a>
            </div>
            <section class="commeta">
                <div class="left">
                    <h4 class="author"><a
                            href="<?php comment_author_url(); ?>"><?php echo get_avatar($comment, 50); ?><?php comment_author(); ?>
                            <span class="isauthor" title="<?php esc_attr_e('Author', 'akina'); ?>"><i class="iconfont">&#xe601;</i></span>
                            &nbsp;&nbsp;评论提交成功！</a></h4>
                </div>
                <div class="right">
                    <div class="info">
                        <time
                            datetime="<?php comment_date('Y-m-d'); ?>"><?php comment_date(get_option('date_format')); ?></time>
                    </div>
                </div>
            </section>
            <div class="body">
                <?php comment_text(); ?>
            </div>
        </div>
    </li>
    <?php die;
}

add_action('wp_ajax_comment-submission', 'ajax_comment_submission');
add_action('wp_ajax_nopriv_comment-submission', 'ajax_comment_submission');

function get_the_link_items($id = null)
{
    $bookmarks = get_bookmarks('orderby=date&category=' . $id);
    $output = '';
    if (!empty($bookmarks)) {
        $output .= '<ul class="link-items fontSmooth">';
        foreach ($bookmarks as $bookmark) {
            $output .= '<li class="link-item"><a class="link-item-inner effect-apollo" href="' . $bookmark->link_url . '" title="' . $bookmark->link_description . '" target="_blank" ><img class="sitename_img" src="https://api.byi.pw/favicon/?url=' . $bookmark->link_url . '&expire=3600"><span class="sitename">' . $bookmark->link_name . '</span><div class="linkdes">' . $bookmark->link_description . '</div></a></li>';
        }
        $output .= '</ul>';
    }
    return $output;
}

function get_link_items()
{
    $linkcats = get_terms('link_category');
    if (!empty($linkcats)) {
        foreach ($linkcats as $linkcat) {
            $result .= '<h3 class="link-title">' . $linkcat->name . '</h3>';
            if ($linkcat->description) $result .= '<div class="link-description">' . $linkcat->description . '</div>';
            $result .= get_the_link_items($linkcat->term_id);
        }
    } else {
        $result = get_the_link_items();
    }
    return $result;
}

function shortcode_link()
{
    return get_link_items();
}

add_shortcode('bigfalink', 'shortcode_link');

//图片七牛云缓存
add_filter('upload_dir', 'wpjam_custom_upload_dir');
function wpjam_custom_upload_dir($uploads)
{
    $upload_path = '';
    $upload_url_path = akina_option('qiniu_cdn');

    if (empty($upload_path) || 'wp-content/uploads' == $upload_path) {
        $uploads['basedir'] = WP_CONTENT_DIR . '/uploads';
    } elseif (0 !== strpos($upload_path, ABSPATH)) {
        $uploads['basedir'] = path_join(ABSPATH, $upload_path);
    } else {
        $uploads['basedir'] = $upload_path;
    }

    $uploads['path'] = $uploads['basedir'] . $uploads['subdir'];

    if ($upload_url_path) {
        $uploads['baseurl'] = $upload_url_path;
        $uploads['url'] = $uploads['baseurl'] . $uploads['subdir'];
    }
    return $uploads;
}

// @父评论
add_filter('comment_text', 'comment_add_at_parent');
function comment_add_at_parent($comment_text)
{
    $comment_ID = get_comment_ID();
    $comment = get_comment($comment_ID);
    if ($comment->comment_parent) {
        $parent_comment = get_comment($comment->comment_parent);
        $comment_text = preg_replace('/<a href="#comment-([0-9]+)?".*?>(.*?)<\/a>/i', '', $comment_text);//去除存在数据库里的@回复
        $comment_text = '<a href="#comment-' . $comment->comment_parent . '" rel="nofollow" data-id="' . $comment->comment_parent . '" class="cute atreply">@' . $parent_comment->comment_author . '</a> : ' . $comment_text;
    }
    return $comment_text;
}


//删除自带小工具
function unregister_default_widgets()
{
    unregister_widget("WP_Widget_Pages");
    unregister_widget("WP_Widget_Calendar");
    unregister_widget("WP_Widget_Archives");
    unregister_widget("WP_Widget_Links");
    unregister_widget("WP_Widget_Meta");
    unregister_widget("WP_Widget_Search");
    unregister_widget("WP_Widget_Text");
    unregister_widget("WP_Widget_Categories");
    unregister_widget("WP_Widget_Recent_Posts");
    unregister_widget("WP_Widget_Recent_Comments");
    unregister_widget("WP_Widget_RSS");
    unregister_widget("WP_Widget_Tag_Cloud");
    unregister_widget("WP_Nav_Menu_Widget");
}

add_action("widgets_init", "unregister_default_widgets", 11);

//footer统计代码
function track_code()
{
    if (akina_option('track_code')) {
        echo akina_option('track_code');
    }
}
add_action("wp_footer","track_code");



//面包屑
function dimox_breadcrumbs() {
    $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
    $delimiter = '->'; // delimiter between crumbs
    $home = '首页'; // text for the 'Home' link
    $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
    $before = '<span class="current">'; // tag before the current crumb
    $after = '</span>'; // tag after the current crumb
    global $post;
    $homeLink = get_bloginfo('url');
    if (is_home() || is_front_page()) {
        if ($showOnHome == 1) echo '<div id="crumbs"><a href="' . $homeLink . '">' . $home . '</a></div>';
    } else {
        echo '<div id="crumbs">转载请注明原文链接：<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
        if ( is_category() ) {
            $thisCat = get_category(get_query_var('cat'), false);
            if ($thisCat->parent != 0) echo get_category_parents($thisCat->parent, TRUE, ' ' . $delimiter . ' ');
            echo $before . 'Archive by category "' . single_cat_title('', false) . '"' . $after;
        } elseif ( is_search() ) {
            echo $before . 'Search results for "' . get_search_query() . '"' . $after;
        } elseif ( is_day() ) {
            echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('d') . $after;
        } elseif ( is_month() ) {
            echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('F') . $after;
        } elseif ( is_year() ) {
            echo $before . get_the_time('Y') . $after;
        } elseif ( is_single() && !is_attachment() ) {
            if ( get_post_type() != 'post' ) {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>';
                if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
            } else {
                $cat = get_the_category(); $cat = $cat[0];
                $cats = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                if ($showCurrent == 0) $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
                echo $cats;
                if ($showCurrent == 1) echo $before . get_the_title() . $after;
            }
        } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
            $post_type = get_post_type_object(get_post_type());
            echo $before . $post_type->labels->singular_name . $after;
        } elseif ( is_attachment() ) {
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID); $cat = $cat[0];
            echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
            echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
            if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
        } elseif ( is_page() && !$post->post_parent ) {
            if ($showCurrent == 1) echo $before . get_the_title() . $after;
        } elseif ( is_page() && $post->post_parent ) {
            $parent_id  = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                $parent_id  = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            for ($i = 0; $i < count($breadcrumbs); $i++) {
                echo $breadcrumbs[$i];
                if ($i != count($breadcrumbs)-1) echo ' ' . $delimiter . ' ';
            }
            if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
        } elseif ( is_tag() ) {
            echo $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;
        } elseif ( is_author() ) {
            global $author;
            $userdata = get_userdata($author);
            echo $before . 'Articles posted by ' . $userdata->display_name . $after;
        } elseif ( is_404() ) {
            echo $before . 'Error 404' . $after;
        }
        if ( get_query_var('paged') ) {
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
            echo __('Page') . ' ' . get_query_var('paged');
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
        }
        echo '</div>';

    }
} // end dimox_breadcrumbs()

//自定义代码
/**
 * WordPress发布文章主动推送到百度，加快收录保护原创【WordPress通用方式】
 * 文章地址：http://zhangge.net/5041.html
 */

if (akina_option('baidu_token')) {
    if (!function_exists('Baidu_Submit')) {
        function Baidu_Submit($post_ID)
        {
            $WEB_TOKEN = akina_option('baidu_token');  //这里请换成你的网站的百度主动推送的token值
            $WEB_DOMAIN = get_option('home');
            //已成功推送的文章不再推送
            if (get_post_meta($post_ID, 'Baidusubmit', true) == 1) return;
            $url = get_permalink($post_ID);
            $api = 'http://data.zz.baidu.com/urls?site=' . $WEB_DOMAIN . '&token=' . $WEB_TOKEN;
            $request = new WP_Http;
            $result = $request->request($api, array('method' => 'POST', 'body' => $url, 'headers' => 'Content-Type: text/plain'));
            $result = json_decode($result['body'], true);
            //如果推送成功则在文章新增自定义栏目Baidusubmit，值为1
            if (array_key_exists('success', $result)) {
                add_post_meta($post_ID, 'Baidusubmit', 1, true);
            }
        }

        add_action('publish_post', 'Baidu_Submit', 0);
    }
}
/**
 * WordPress百度搜索自动推送、主动收录JS优化 By 张戈博客
 * 文章地址：http://zhangge.net/5100.html
 * 转载请保留出处，谢谢合作！
 **/
add_action('wp_footer', 'bdPushData', 999);
//检查百度是否已收录最新改进版本
if (!function_exists('baidu_check_record')) {
    function baidu_check_record($url)
    {
        global $wpdb;
        $post_id = (null === $post_id) ? get_the_ID() : $post_id;
        $baidu_record = get_post_meta($post_id, 'baidu_record', true);
        if ($baidu_record != 1) {
            $url = 'http://www.baidu.com/s?wd=' . $url;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $rs = curl_exec($curl);
            curl_close($curl);
            //如果抓取到的百度结果中不存在【提交网址】这个关键词，则认为该页面已被百度收录
            if (!preg_match_all('/提交网址/u', $rs, $matches) && preg_match_all('/百度为您找到相关结果/u', $rs, $matches)) {
                update_post_meta($post_id, 'baidu_record', 1) || add_post_meta($post_id, 'baidu_record', 1, true);
                return 1;
            } else {
                return 0;
            }
        } else {
            return 1;
        }
    }
}
//检查360是否已收录最新改进版本
if (!function_exists('sll_check_record')) {
    function sll_check_record($sslurl)
    {
        global $wpdb;
        $post_id = (null === $post_id) ? get_the_ID() : $post_id;
        $sll_record = get_post_meta($post_id, 'sll_record', true);
        if ($sll_record != 1) {
            $sslurl = 'http://www.so.com/s?q=' . $sslurl;
            $csslurl = curl_init();
            curl_setopt($csslurl, CURLOPT_URL, $sslurl);
            curl_setopt($csslurl, CURLOPT_RETURNTRANSFER, 1);
            $rs = curl_exec($csslurl);
            curl_close($csslurl);
            //如果抓取到的360结果中不存在【提交网址】这个关键词，则认为该页面已被360收录
            if (!preg_match_all('/提交网址/u', $rs, $matches)) {
                update_post_meta($post_id, 'sll_record', 1) || add_post_meta($post_id, 'sll_record', 1, true);
                return 1;
            } else {
                return 0;
            }
        } else {
            return 1;
        }
    }
}
//输出百度自动推送和360自动收录的js代码
if (!function_exists('bdPushData')) {
    function bdPushData()
    {
        if ($_SERVER['HTTPS'] != "on") {
            $currentUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        } else {
            $currentUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        }
        //判断是否是百度未收录页面，并且当前页面是否等于正规url地址(get_premalink)
        if (baidu_check_record(get_permalink()) == 0 && $currentUrl == get_permalink()) {
            echo "<script>(function(){
            var bp = document.createElement('script');
            var curProtocol = window.location.protocol.split(':')[0];
            if (curProtocol === 'https') {
                bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';        
            } else {
                bp.src = 'http://push.zhanzhang.baidu.com/push.js';
            }
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(bp, s);
            })();</script>";
        }
        if (akina_option('qihu_sitekey')) {
            //判断是否是360未收录页面，并且当前页面是否等于正规url地址(get_premalink)
            $QIHU_WEBSITEKEY = akina_option('qihu_sitekey');
            if (sll_check_record(get_permalink()) == 0 && $currentUrl == get_permalink()) {
                echo "<script>
                    (function(){
                        var src = (document.location.protocol == 'http:') ? 'http://js.passport.qihucdn.com/11.0.1.js?$QIHU_WEBSITEKEY':'https://jspassport.ssl.qhimg.com/11.0.1.js?$QIHU_WEBSITEKEY';
                        document.write('<script src='' + src + '' id='sozz'><\/script>');
                    })();
                </script>";
            }
        }
    }
}

/**
 * WordPress 星火计划原创保护专用META优化代码(最终版) By 张戈博客
 * 文章地址：http://zhangge.net/5084.html
 */
add_action('wp_head', 'starfire', 0);
if (!function_exists('starfire')) {
    function starfire()
    {
        //新增判断，如果是原创文章才加入星火计划META申明
        global $wpdb;
        $post_id = (null === $post_id) ? get_the_ID() : $post_id;
        $copy = get_post_meta($post_id, 'author', true);
        if (is_singular() && empty($copy)) {
            date_default_timezone_set('PRC');
            echo '<meta property="og:type" content="article"/>
        <meta property="article:published_time" content="' . get_the_date('c') . '"/>
        <meta property="og:release_date" content="' . get_the_date('c') . '"/>
        <meta property="article:author" content="';
            bloginfo('name');
            echo '" />';
            echo '<meta property="og:author" content="';
            bloginfo('name');
            echo '" />';
            echo '<meta property="og:url" content="';
            the_permalink();
            echo '"/>';
            //输出文章标题+分隔符+网站名称，不喜欢这种形式的请自行改造（如果不需要这个标签，请删除以下三行）。
            echo '<meta property="og:title" content="' . trim(wp_title('', 0)) . ' | ';
            bloginfo('name');
            echo '" />';
            //输出博客名称，如果想改成其他内容，比如作者请自行修改 bloginfo('name')
            echo '<meta property="article:published_first" content="';
            bloginfo('name');
            echo ',';
            the_permalink();
            //默认截取文章220个字作为摘要，可以自行修改下行220为其他整数
            echo '" /><meta property="og:description" content="' . get_mypost_excerpt($post_id, 220) . '……" />
        <meta property="og:image" content="' . get_mypost_thumbnail($post_id) . '" />
        <meta itemprop="image" content="' . get_mypost_thumbnail($post_id) . '" />';
        }
    }
}

/**
 * WordPress 获取文章摘要整理版 By 张戈博客
 */
function get_mypost_excerpt($post_ID, $len)
{
    if (!function_exists('utf8Substr')) {
        function utf8Substr($str, $from, $len)
        {
            return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' .
                '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s',
                '$1', $str);
        }
    }
    if (!$post_content) {
        $post = get_post($post_ID);
        $post_content = $post->post_content;
    }
    if ($post->post_excerpt) {
        $description = $post->post_excerpt;
    } else {
        if (preg_match('/<p>(.*)<\/p>/iU', trim(strip_tags($post->post_content, "<p>")), $result)) {
            $post_content = $result['1'];
        } else {
            $post_content_r = explode("\n", trim(strip_tags($post->post_content)));
            $post_content = $post_content_r['0'];
        }
        $description = utf8Substr($post_content, 0, $len);
        return $description;
    }
}

/**
 * WordPress 获取文章图片加强版 By 张戈博客
 */
function get_mypost_thumbnail($post_ID)
{
    if (has_post_thumbnail()) {
        $timthumb_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_ID), 'full');
        $url = $timthumb_src[0];
    } else {
        if (!$post_content) {
            $post = get_post($post_ID);
            $post_content = $post->post_content;
        }
        preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', do_shortcode($post_content), $matches);
        if ($matches && isset($matches[1]) && isset($matches[1][0])) {
            $url = $matches[1][0];
        } else {
            $url = '';
        }
    }
    return $url;
}

//WordPress文字标签关键词自动内链
$match_num_from = 1;        //一篇文章中同一個標籤少於幾次不自動鏈接
$match_num_to = 4;        //一篇文章中同一個標籤最多自動鏈接幾次
function tag_sort($a, $b)
{
    if ($a->name == $b->name) return 0;
    return (strlen($a->name) > strlen($b->name)) ? -1 : 1;
}

function tag_link($content)
{
    global $match_num_from, $match_num_to;
    $posttags = get_the_tags();
    if ($posttags) {
        usort($posttags, "tag_sort");
        foreach ($posttags as $tag) {
            $link = get_tag_link($tag->term_id);
            $keyword = $tag->name;
            $cleankeyword = stripslashes($keyword);
            $url = "<a href=\"$link\" title=\"" . str_replace('%s', addcslashes($cleankeyword, '$'), __('查看更多关于 %s 的文章')) . "\"";
            $url .= ' target="_blank"';
            $url .= ">" . addcslashes($cleankeyword, '$') . "</a>";
            $limit = rand($match_num_from, $match_num_to);
            $content = preg_replace('|(<a[^>]+>)(.*)<pre.*?>(' . $ex_word . ')(.*)<\/pre>(</a[^>]*>)|U' . $case, '$1$2%&&&&&%$4$5', $content);
            $content = preg_replace('|(<img)(.*?)(' . $ex_word . ')(.*?)(>)|U' . $case, '$1$2%&&&&&%$4$5', $content);
            $cleankeyword = preg_quote($cleankeyword, '\'');
            $regEx = '\'(?!((<.*?)|(<a.*?)))(' . $cleankeyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s' . $case;
            $content = preg_replace($regEx, $url, $content, $limit);
            $content = str_replace('%&&&&&%', stripslashes($ex_word), $content);
        }
    }
    return $content;
}
add_filter('the_content', 'tag_link', 1);

//字数统计
function count_words($text)
{
    global $post;
    if ('' == $text) {
        $text = $post->post_content;
        if (mb_strlen($output, 'UTF-8') < mb_strlen($text, 'UTF-8')) $output .= mb_strlen(preg_replace('/\s/', '', html_entity_decode(strip_tags($post->post_content))), 'UTF-8');
        return $output;
    }
}

// 增加 markdown 功能到后台编辑器
add_action('admin_menu', 'create_markdown');
add_action('save_post', 'save_markdown', 10, 2);
function create_markdown()
{
    add_meta_box('markdown_box', 'Markdown', 'markdown_html', 'post', 'normal', 'high');
}

function markdown_html($object, $box)
{ ?>
    <textarea name="markdown" id="markdown" cols="60" oninput="markdownEditorChanged()" rows="50"
              style="width: 100%; height:100%"><?php echo htmlspecialchars(get_post_meta($object->ID, 'markdown', true)); ?></textarea>
<?php }

function save_markdown($post_id, $post)
{
    if (!current_user_can('edit_post', $post_id))
        return $post_id;

    $meta_value = get_post_meta($post_id, 'markdown', true);
    $new_meta_value = $_POST['markdown'];

    if ($new_meta_value && '' == $meta_value)
        add_post_meta($post_id, 'markdown', $new_meta_value, true);

    elseif ($new_meta_value != $meta_value)
        update_post_meta($post_id, 'markdown', $new_meta_value);

    elseif ('' == $new_meta_value && $meta_value)
        delete_post_meta($post_id, 'markdown', $meta_value);
}

function markdown_script()
{
    wp_enqueue_script('markdown', get_template_directory_uri() . '/inc/js/marked.js');
    wp_enqueue_script('makemarkdown', get_template_directory_uri() . '/inc/js/makemarkdown.js');
}

add_action('admin_enqueue_scripts', 'markdown_script');

if( akina_option('no_categoty') == yes && !function_exists('no_category_base_refresh_rules') ){

    /*
    Plugin Name: No Category Base (WPML)
    Version: 1.2
    Plugin URI: http://infolific.com/technology/software-worth-using/no-category-base-for-wordpress/
    Description: Removes '/category' from your category permalinks. WPML compatible.
    Author: Marios Alexandrou
    Author URI: http://infolific.com/technology/
    License: GPLv2 or later
    Text Domain: no-category-base-wpml
    */

    /*
    Copyright 2015 Marios Alexandrou
    Copyright 2011 Mines (email: hi@mines.io)
    Copyright 2008 Saurabh Gupta (email: saurabh0@gmail.com)

    Based on the work by Saurabh Gupta (email : saurabh0@gmail.com)

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
    */

    /* hooks */
    register_activation_hook(__FILE__,'no_category_base_refresh_rules');
    register_deactivation_hook(__FILE__,'no_category_base_deactivate');

    /* actions */
    add_action('created_category','no_category_base_refresh_rules');
    add_action('delete_category','no_category_base_refresh_rules');
    add_action('edited_category','no_category_base_refresh_rules');
    add_action('init','no_category_base_permastruct');

    /* filters */
    add_filter('category_rewrite_rules', 'no_category_base_rewrite_rules');
    add_filter('query_vars','no_category_base_query_vars');    // Adds 'category_redirect' query variable
    add_filter('request','no_category_base_request');       // Redirects if 'category_redirect' is set

    function no_category_base_refresh_rules() {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

    function no_category_base_deactivate() {
        remove_filter( 'category_rewrite_rules', 'no_category_base_rewrite_rules' ); // We don't want to insert our custom rules again
        no_category_base_refresh_rules();
    }

    /**
     * Removes category base.
     *
     * @return void
     */
    function no_category_base_permastruct()
    {
        global $wp_rewrite;
        global $wp_version;

        if ( $wp_version >= 3.4 ) {
            $wp_rewrite->extra_permastructs['category']['struct'] = '%category%';
        } else {
            $wp_rewrite->extra_permastructs['category'][0] = '%category%';
        }
    }

    /**
     * Adds our custom category rewrite rules.
     *
     * @param  array $category_rewrite Category rewrite rules.
     *
     * @return array
     */
    function no_category_base_rewrite_rules($category_rewrite) {
        global $wp_rewrite;
        $category_rewrite=array();

        /* WPML is present: temporary disable terms_clauses filter to get all categories for rewrite */
        if ( class_exists( 'Sitepress' ) ) {
            global $sitepress;

            remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
            $categories = get_categories( array( 'hide_empty' => false ) );
            add_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
        } else {
            $categories = get_categories( array( 'hide_empty' => false ) );
        }

        foreach( $categories as $category ) {
            $category_nicename = $category->slug;

            if ( $category->parent == $category->cat_ID ) {
                $category->parent = 0;
            } elseif ( $category->parent != 0 ) {
                $category_nicename = get_category_parents( $category->parent, false, '/', true ) . $category_nicename;
            }

            $category_rewrite['('.$category_nicename.')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
            $category_rewrite["({$category_nicename})/{$wp_rewrite->pagination_base}/?([0-9]{1,})/?$"] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
            $category_rewrite['('.$category_nicename.')/?$'] = 'index.php?category_name=$matches[1]';
        }

        // Redirect support from Old Category Base
        $old_category_base = get_option( 'category_base' ) ? get_option( 'category_base' ) : 'category';
        $old_category_base = trim( $old_category_base, '/' );
        $category_rewrite[$old_category_base.'/(.*)$'] = 'index.php?category_redirect=$matches[1]';

        return $category_rewrite;
    }

    function no_category_base_query_vars($public_query_vars) {
        $public_query_vars[] = 'category_redirect';
        return $public_query_vars;
    }

    /**
     * Handles category redirects.
     *
     * @param $query_vars Current query vars.
     *
     * @return array $query_vars, or void if category_redirect is present.
     */
    function no_category_base_request($query_vars) {
        if( isset( $query_vars['category_redirect'] ) ) {
            $catlink = trailingslashit( get_option( 'home' ) ) . user_trailingslashit( $query_vars['category_redirect'], 'category' );
            status_header( 301 );
            header( "Location: $catlink" );
            exit();
        }

        return $query_vars;
    }

}

/**
 * 私密评论
 * https://fatesinger.com/78901
 */
function siren_private_message_hook($comment_content, $comment)
{
    $comment_ID = $comment->comment_ID;
    $parent_ID = $comment->comment_parent;
    $parent_email = get_comment_author_email($parent_ID);
    $is_private = get_comment_meta($comment_ID, '_private', true);
    $email = $comment->comment_author_email;
    $current_commenter = wp_get_current_commenter();
    if ($is_private) $comment_content = '#私密# ' . $comment_content;
    if ($current_commenter['comment_author_email'] == $email || $parent_email == $current_commenter['comment_author_email'] || current_user_can('delete_user')) return $comment_content;
    if ($is_private) return '该评论为私密评论';
    return $comment_content;
}

add_filter('get_comment_text', 'siren_private_message_hook', 10, 2);

function siren_mark_private_message($comment_id)
{
    if ($_POST['is-private']) {
        update_comment_meta($comment_id, '_private', 'true');
    }
}

add_action('comment_post', 'siren_mark_private_message');

/**
 * 语法高亮
 */
if (akina_option('prism_highlighters') == yes) {
    function add_prism()
    {
        wp_register_style(
            'prismCSS',
            get_stylesheet_directory_uri() . '/prism/prism.css' // '//blogresource.oss-cn-hangzhou.aliyuncs.com/css/prism.css' //自定义路径
        );
        wp_register_script(
            'prismJS',
            get_stylesheet_directory_uri() . '/prism/prism.js'   // '//blogresource.oss-cn-hangzhou.aliyuncs.com/javascript/prism.js' //自定义路径
        );
        wp_enqueue_style('prismCSS');
        wp_enqueue_script('prismJS');
    }

    add_action('wp_enqueue_scripts', 'add_prism');
};
//code end