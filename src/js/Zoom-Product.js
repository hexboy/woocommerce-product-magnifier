import $ from 'jquery';

$(document).ready(function() {
	// console.log('iplug zoom main script is running');
	var removeZoomZrea = 0;
	var mousePos = {
		X: 0,
		Y: 0
	};
	var largeImage = {
		width: 0,
		height: 0,
		src: '',
		ratioW: 1,
		ratioH: 1
	};
	// console.log(ipzo);

	// initail
	$('<div id="image-zoom-area"></div>').appendTo($('body'));
	$('#image-zoom-area').hide();
	if (ipzo.transparency * 1) {
		$('#image-zoom-area').addClass('transparent');
	}
	$('#image-zoom-area').hover(function() {
		clearTimeout(removeZoomZrea);
	}, function() {
		removeZoomZrea = setTimeout(function() {
			$('#image-zoom-area').hide();
		}, 200);
	});

	$('figure.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image').hover(
		// mouse enter
		function() {
			if ($(window).width() < 768) {
				return;
			}

			var img = $(this).find('img').eq(0);
			img.attr('data-tmp-title', img.attr('title'));
			img.attr('title', '');
			clearTimeout(removeZoomZrea);

			// check have image src link
			largeImage.src = img.attr('data-large_image');
			largeImage.width = img.attr('data-large_image_width') * ipzo.zoomLevel;
			largeImage.height = img.attr('data-large_image_height') * ipzo.zoomLevel;

			if (largeImage.width > ipzo.maxWidth) {
				largeImage.width = ipzo.maxWidth;
			}
			if (largeImage.height > ipzo.maxHeight) {
				largeImage.height = ipzo.maxHeight;
			}

			largeImage.ratioW = largeImage.width / img.width();
			largeImage.ratioH = largeImage.height / img.height();

			$('#image-zoom-area').show();

			$('#image-zoom-area').css({
				width: ipzo.magnifierSize,
				height: ipzo.magnifierSize,
				borderRadius: (ipzo.isCircle * 1) ? '50%' : 0,
				backgroundImage: 'url(' + largeImage.src + '),url(' + ipzo.bg + ')',
				backgroundSize: largeImage.width + 'px ' + largeImage.height + 'px' + ',155px 155px',
				backgroundRepeat: 'no-repeat, repeat'
			});

		},
		// mouse out
		function() {
			var img = $(this).find('img').eq(0);
			img.attr('title', img.attr('data-tmp-title'));
			removeZoomZrea = setTimeout(function() {
				$('#image-zoom-area').hide();
			}, 200);

		});

	$('figure.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image').mousemove(function(e) {
		if ($(window).width() < 768) {
			return;
		}

		// set magnifier position
		// method 1
		var imageOffset = $(this).find('img').offset();
		// mousePos.X = Math.round(e.pageX - imageOffset.left);
		// mousePos.Y = Math.round(e.pageY - imageOffset.top);

		// method 2
		mousePos.X = Math.round(e.pageX - $(document).scrollLeft() + 10);
		mousePos.Y = Math.round(e.pageY - $(document).scrollTop() + 10);

		$('#image-zoom-area').css({
			top: mousePos.Y,
			left: mousePos.X
		});

		// set magnifier background position
		var backgroundX = -1 * (e.pageX - imageOffset.left) * largeImage.ratioW + ipzo.magnifierSize / 2;
		var backgroundY = -1 * (e.pageY - imageOffset.top) * largeImage.ratioH + ipzo.magnifierSize / 2;
		backgroundX = Math.round(backgroundX);
		backgroundY = Math.round(backgroundY);
		$('#image-zoom-area').css({
			backgroundPosition: backgroundX + 'px ' + backgroundY + 'px'
		});
		// console.log(largeImage);
		// console.log(backgroundX, backgroundY);

	});

});
