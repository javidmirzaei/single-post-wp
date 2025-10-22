<?php
/**
 * Custom Comments Template for Beautiful Post
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="bp-comments-wrapper">
    <?php if (have_comments()) : ?>
        <h2 class="bp-comments-title">
            <?php
            $comments_number = get_comments_number();
            $persian_numbers = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
            $english_numbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
            $persian_count = str_replace($english_numbers, $persian_numbers, $comments_number);
            
            if ($comments_number == 1) {
                echo '۱ نظر';
            } else {
                echo $persian_count . ' نظر';
            }
            ?>
        </h2>

        <ol class="bp-comment-list">
            <?php
            wp_list_comments(array(
                'style' => 'ol',
                'short_ping' => true,
                'avatar_size' => 50,
                'callback' => 'beautiful_post_custom_comment'
            ));
            ?>
        </ol>

        <?php
        the_comments_navigation();
        ?>

    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="bp-no-comments">امکان ثبت نظر جدید وجود ندارد.</p>
    <?php endif; ?>

    <?php
    comment_form(array(
        'class_container' => 'bp-comment-form',
        'title_reply' => 'نظر خود را بنویسید',
        'title_reply_to' => 'پاسخ به %s',
        'cancel_reply_link' => 'لغو پاسخ',
        'label_submit' => 'ارسال نظر',
        'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required" placeholder="نظر شما..."></textarea></p>',
        'fields' => array(
            'author' => '<p class="comment-form-author"><input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" maxlength="245" required="required" placeholder="نام شما" /></p>',
            'email' => '<p class="comment-form-email"><input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" maxlength="100" aria-describedby="email-notes" required="required" placeholder="ایمیل شما" /></p>',
            'url' => '<p class="comment-form-url"><input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" maxlength="200" placeholder="وب‌سایت (اختیاری)" /></p>',
        ),
    ));
    ?>
</div>

<?php
/**
 * Custom comment callback
 */
function beautiful_post_custom_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    
    $persian_numbers = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    $english_numbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    
    ?>
    <li <?php comment_class('bp-comment'); ?> id="comment-<?php comment_ID(); ?>">
        <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
            <div class="bp-comment-author">
                <?php echo get_avatar($comment, 50, '', '', array('class' => 'bp-comment-avatar')); ?>
                <div class="bp-comment-meta">
                    <h4 class="bp-comment-author-name"><?php comment_author(); ?></h4>
                    <time class="bp-comment-date">
                        <?php
                        $date = get_comment_date('j F Y');
                        echo str_replace($english_numbers, $persian_numbers, $date);
                        ?>
                    </time>
                </div>
            </div>
            
            <div class="bp-comment-content">
                <?php comment_text(); ?>
            </div>
            
            <?php if (get_comment_reply_link(array('depth' => $depth, 'max_depth' => $args['max_depth']))) : ?>
            <div class="bp-comment-reply">
                <?php
                comment_reply_link(array_merge($args, array(
                    'add_below' => 'div-comment',
                    'depth' => $depth,
                    'max_depth' => $args['max_depth'],
                    'reply_text' => 'پاسخ'
                )));
                ?>
            </div>
            <?php endif; ?>
        </article>
    <?php
}

