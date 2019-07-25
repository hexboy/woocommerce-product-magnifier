import $ from 'jquery';

$(document).ready(function() {
	const imageSelector =
		'figure.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image img';
	let image = null;
	let timerHandle = null;
	let mousePos = {
		X: 0,
		Y: 0
	};
	let largeImage = {
		width: 0,
		height: 0,
		src: '',
		ratioW: 1,
		ratioH: 1
	};

	let lastImageSrc = '';
	let imageTitle = '';

	// initial
	$('<div id="image-zoom-area"></div>').appendTo($('body'));
	$('#image-zoom-area').hide();
	if (ipzo.transparency * 1) {
		$('#image-zoom-area').addClass('transparent');
	}
	if (ipzo.isCircle * 1) {
		$('#image-zoom-area').addClass('isCircle');
	}
	$('#image-zoom-area').hover(
		function() {
			clearTimeout(timerHandle);
		},
		function() {
			timerHandle = setTimeout(function() {
				$('#image-zoom-area').hide();
			}, 200);
		}
	);

	$(imageSelector).hover(
		// mouse enter
		function() {
			if ($(window).width() < 768) {
				return;
			}

			image = $(this).eq(0);
			imageTitle = image.attr('title');
			image.attr('title', '');
			clearTimeout(timerHandle);

			// check image have src
			largeImage.src = image.attr('data-large_image');
			largeImage.width =
				image.attr('data-large_image_width') * ipzo.zoomLevel;
			largeImage.height =
				image.attr('data-large_image_height') * ipzo.zoomLevel;

			if (largeImage.width > ipzo.maxWidth) {
				largeImage.width = ipzo.maxWidth;
			}
			if (largeImage.height > ipzo.maxHeight) {
				largeImage.height = ipzo.maxHeight;
			}

			largeImage.ratioW = largeImage.width / image.width();
			largeImage.ratioH = largeImage.height / image.height();

			if (lastImageSrc != largeImage.src) {
				lastImageSrc = largeImage.src;
				let img = `<img src="${
					largeImage.src
				}" onload="this.parentNode.classList.remove('loading')" style="width:${
					largeImage.width
				}px;height:${largeImage.height}px;"/>`;
				$('#image-zoom-area')
					.show()
					.html(img)
					.css({
						width: ipzo.magnifierSize,
						height: ipzo.magnifierSize
					})
					.addClass('loading');
			} else {
				$('#image-zoom-area').show();
			}
		},
		// mouse out
		function() {
			image.attr('title', imageTitle);
			timerHandle = setTimeout(function() {
				$('#image-zoom-area').hide();
			}, 200);
		}
	);

	$(imageSelector).mousemove(function(e) {
		if ($(window).width() < 768) {
			return;
		}

		// set magnifier position
		var imageOffset = $(this).offset();
		mousePos.X = Math.round(e.pageX - $(document).scrollLeft() + 10);
		mousePos.Y = Math.round(e.pageY - $(document).scrollTop() + 10);

		// set magnifier background position
		var backgroundX =
			-1 * (e.pageX - imageOffset.left) * largeImage.ratioW +
			ipzo.magnifierSize / 2;
		var backgroundY =
			-1 * (e.pageY - imageOffset.top) * largeImage.ratioH +
			ipzo.magnifierSize / 2;
		backgroundX = Math.round(backgroundX);
		backgroundY = Math.round(backgroundY);

		$('#image-zoom-area img').css({
			top: backgroundY,
			left: backgroundX
		});

		$('#image-zoom-area').css({
			top: mousePos.Y,
			left: mousePos.X
		});
	});
});
