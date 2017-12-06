<?php
class eCards_Additional_Images {
	protected static $instance = null;

	private $eCardDb;

    private $meta_key = '_ecards_additional_images';

    protected function __construct() {
        global $wpdb;

        $this->eCardDb = $wpdb;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        add_action('add_meta_boxes', array($this, 'attachMetaBox'));
        add_action('save_post', array($this, 'savePostImages'));
    }

    public function attachMetaBox() {
        $screens = array('post', 'page');

        foreach ($screens as $screen) {
            add_meta_box('twp-attach-post-images', esc_html__('eCards Additional Images', 'ecards'), array($this, 'attachMetaBoxHTML'), $screen, 'side');
        }
    }

    public function attachMetaBoxHTML($post) {
        wp_nonce_field('ecards', 'ecards_additional_images_plugin_nonce');

        $images_arr = get_post_meta($post->ID, $this->meta_key, true);
        $images_str = '';
        $images = array();

        if ($images_arr) {
            $images = $this->getImagesFromIds($images_arr);
            $images_str = implode('|', $images_arr);
        }

        $params = array(
            'post_id' => $post->ID,
            'width' => 640,
            'height' => 557,
            'TB_iframe' => 1,
            'type' => 'image',
        );

        $href = admin_url('media-upload.php?' . http_build_query($params));

        echo '<p>Use this box to add images from your <strong>Media Library</strong>.</p>

        <p class="hide-if-no-js">
            <a href="' . esc_url($href) . '" id="twp-attach-post-images-uploader" class="button button-secondary">Select image(s)</a>
            <p><small>Use <code class="codor">CTRL</code> key to select multiple images. Note that attaching images may detach them from other posts or pages.</small></p>
            <input type="hidden" id="twp-attach-post-images-selected" name="selected_post_image" value="' . esc_html($images_str) . '">
        </p>

        <div class="hide-if-no-js" id="twp-attach-post-images-list-container">
            <ul id="twp-attach-post-images-list">';
                if (!empty($images)) : foreach ($images as $image) :
                    echo '<li>
                        <img src="' . esc_url($image->url) . '" alt="">
                        <a href="javascript:void(0)" class="delete" data-id="' . (int) $image->id . '"><span class="dashicons dashicons-trash"></span></a>
                    </li>';
                endforeach; endif;
            echo '</ul>
            <div style="clear:both;"></div>
        </div>

        <script type="text/html" id="twp-attach-post-images-list-item-tpl">
            <li><img src="{src}" alt=""><a href="javascript:void(0)" class="delete" data-id="{id}"><span class="dashicons dashicons-trash"></span></a></li>
        </script>';
    }

    public function savePostImages($post_id) {
        if (!isset($_POST['ecards_additional_images_plugin_nonce']))
            return $post_id;

        $nonce = $_POST['ecards_additional_images_plugin_nonce'];

        if (!wp_verify_nonce($nonce, 'ecards'))
            return $post_id;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;

        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id))
                return $post_id;
        } else {
            if (!current_user_can('edit_post', $post_id))
                return $post_id;
        }

        $selected_images = sanitize_text_field($_POST['selected_post_image']);
        $image_ids = explode('|', $selected_images);
        foreach ($image_ids as $i => $id) {
            if ($id > 0) {
                $my_post = array(
                    'ID' => $id,
                    'post_parent' => $post_id,
                );
                wp_update_post($my_post);
            }
        }

        update_post_meta($post_id, $this->meta_key, array_values($image_ids));
    }

    private function getImagesFromIds($ids, $size = 'thumbnail') {
        $images = array();

        foreach ($ids as $id) {
            $meta = wp_get_attachment_image_src($id, $size);

            $info = array();

            $info['id'] = $id;
            $info['url'] = $meta[0];
            $info['width'] = $meta[1];
            $info['height'] = $meta[2];
            $info['is_original'] = !$meta[3];

            $images[] = (object) $info;
        }

        return $images;
    }
}
