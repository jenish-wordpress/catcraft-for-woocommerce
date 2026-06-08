/**
 * Frontend JavaScript for CatCraft for WooCommerce.
 * Initialises Swiper sliders on page load.
 * Slider config is read from data attributes set by render.php.
 */
( function () {
	function catcraft_init_sliders() {
		if ( typeof Swiper === 'undefined' ) {
			return;
		}

		var catcraft_sliders = document.querySelectorAll(
			'.cat-display-layout-slider .catcraft-swiper'
		);

		catcraft_sliders.forEach( function ( catcraft_swiper_el ) {
			if ( catcraft_swiper_el.swiper ) {
				return;
			}

			var catcraft_block_wrap = catcraft_swiper_el.closest( '.cat-display-block' );
			var catcraft_columns    = parseInt( catcraft_block_wrap ? catcraft_block_wrap.dataset.columns : 3, 10 ) || 3;
			var catcraft_loop       = catcraft_block_wrap ? catcraft_block_wrap.dataset.loop === 'true' : false;

			new Swiper( catcraft_swiper_el, {
				slidesPerView: 1,
				spaceBetween: 20,
				loop: catcraft_loop,
				navigation: {
					nextEl: catcraft_swiper_el.querySelector( '.swiper-button-next' ),
					prevEl: catcraft_swiper_el.querySelector( '.swiper-button-prev' ),
				},
				pagination: {
					el: catcraft_swiper_el.querySelector( '.swiper-pagination' ),
					clickable: true,
				},
				breakpoints: {
					640:  { slidesPerView: Math.min( 2, catcraft_columns ) },
					768:  { slidesPerView: Math.min( 3, catcraft_columns ) },
					1024: { slidesPerView: catcraft_columns },
				},
				on: {
					init: function () {
						catcraft_swiper_el.classList.add( 'swiper-initialized' );
					},
				},
			} );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', catcraft_init_sliders );
	} else {
		catcraft_init_sliders();
	}
} )();