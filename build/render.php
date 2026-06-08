<?php

/**
 * Render callback for CatCraft for WooCommerce block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
	exit;
}

// Check if WooCommerce is active.
if (! class_exists('WooCommerce')) {
	echo '<div class="cat-display-error">' . esc_html__('WooCommerce is not active.', 'catcraft-for-woocommerce') . '</div>';
	return;
}

// Get block attributes with defaults.
$catcraft_layout     = isset($attributes['layout']) ? $attributes['layout'] : 'grid';
$catcraft_columns    = isset($attributes['columns']) ? (int) $attributes['columns'] : 3;
$catcraft_limit      = isset($attributes['limit']) ? (int) $attributes['limit'] : 6;
$catcraft_show_all   = isset($attributes['showAll']) ? (bool) $attributes['showAll'] : false;
$catcraft_order_by   = isset($attributes['orderBy']) ? $attributes['orderBy'] : 'name';
$catcraft_order      = isset($attributes['order']) ? $attributes['order'] : 'ASC';
$catcraft_show_count = isset($attributes['showCount']) ? (bool) $attributes['showCount'] : false;
$catcraft_hide_empty = isset($attributes['hideEmpty']) ? (bool) $attributes['hideEmpty'] : true;

// Query arguments.
$catcraft_args = array(
	'taxonomy'   => 'product_cat',
	'orderby'    => $catcraft_order_by,
	'order'      => $catcraft_order,
	'hide_empty' => $catcraft_hide_empty,
);

if (! $catcraft_show_all) {
	$catcraft_args['number'] = $catcraft_limit;
}

$catcraft_categories = get_terms($catcraft_args);

if (empty($catcraft_categories) || is_wp_error($catcraft_categories)) {
	echo '<div class="cat-display-empty">' . esc_html__('No categories found.', 'catcraft-for-woocommerce') . '</div>';
	return;
}

/**
 * Render a single category item.
 *
 * @param object $cat_display_item_category    The category term object.
 * @param bool   $cat_display_item_show_count  Whether to show product count.
 * @param string $cat_display_item_extra_class Extra CSS class (e.g. swiper-slide).
 * @return string HTML output.
 */
if (! function_exists('catcraft_render_item')) {
	function catcraft_render_item($cat_display_item_category, $cat_display_item_show_count = false, $cat_display_item_extra_class = '')
	{
		if (! isset($cat_display_item_category->term_id)) {
			return '';
		}

		$cat_display_item_link = get_term_link($cat_display_item_category->term_id, 'product_cat');
		if (is_wp_error($cat_display_item_link)) {
			return '';
		}

		$cat_display_item_thumbnail_id = get_term_meta($cat_display_item_category->term_id, 'thumbnail_id', true);
		$cat_display_item_image_url    = $cat_display_item_thumbnail_id
			? wp_get_attachment_url($cat_display_item_thumbnail_id)
			: wc_placeholder_img_src();

		$cat_display_item_classes = array_filter(array('cat-display-item', $cat_display_item_extra_class));

		ob_start();
?>
<a href="<?php echo esc_url($cat_display_item_link); ?>"
    class="<?php echo esc_attr(implode(' ', $cat_display_item_classes)); ?>">
    <div class="cat-display-image">
        <?php if ($cat_display_item_image_url) : ?>
        <img src="<?php echo esc_url($cat_display_item_image_url); ?>"
            alt="<?php echo esc_attr($cat_display_item_category->name); ?>" loading="lazy" />
        <?php endif; ?>
    </div>
    <div class="cat-display-content">
        <h4 class="cat-display-title"><?php echo esc_html($cat_display_item_category->name); ?></h4>
        <?php if ($cat_display_item_show_count) : ?>
        <span class="cat-display-count">
            <?php
						echo esc_html(
							sprintf(
								/* translators: %s: number of products */
								_n('%s Product', '%s Products', $cat_display_item_category->count, 'catcraft-for-woocommerce'),
								number_format_i18n($cat_display_item_category->count)
							)
						);
						?>
        </span>
        <?php endif; ?>
    </div>
</a>
<?php
		return ob_get_clean();
	}
}

// Generate unique ID for this block instance.
$catcraft_block_id = 'catcraft-' . wp_unique_id();

// Determine if slider should loop.
$catcraft_loop = count($catcraft_categories) > $catcraft_columns ? 'true' : 'false';

// Wrapper classes.
$catcraft_wrapper_classes = array(
	'cat-display-block',
	'cat-display-layout-' . $catcraft_layout,
	'cat-display-cols-' . $catcraft_columns,
);

$catcraft_wrapper_attrs = get_block_wrapper_attributes(
	array(
		'class'        => implode(' ', $catcraft_wrapper_classes),
		'data-layout'  => $catcraft_layout,
		'data-columns' => (string) $catcraft_columns,
		'data-loop'    => $catcraft_loop,
	)
);
?>

<div <?php echo $catcraft_wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() returns safely escaped output. 
		?>>
    <?php if ('slider' === $catcraft_layout) : ?>

    <div class="swiper catcraft-swiper" id="<?php echo esc_attr($catcraft_block_id); ?>">
        <div class="swiper-wrapper">
            <?php
				foreach ($catcraft_categories as $catcraft_category) {
					echo wp_kses_post(catcraft_render_item($catcraft_category, $catcraft_show_count, 'swiper-slide'));
				}
				?>
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-pagination"></div>
    </div>

    <?php else : ?>

    <div class="cat-display-grid">
        <?php
			foreach ($catcraft_categories as $catcraft_category) {
				echo wp_kses_post(catcraft_render_item($catcraft_category, $catcraft_show_count));
			}
			?>
    </div>

    <?php endif; ?>
</div>