/**
 * Project list featured toggle.
 *
 * @package Baydemir
 */

(function ($) {
	'use strict';

	$(function () {
		if (!window.baydemirAdminList) {
			return;
		}

		$(document).on('click', '.bd-featured-toggle', function (e) {
			e.preventDefault();
			var $btn = $(this);
			if ($btn.data('busy')) {
				return;
			}
			$btn.data('busy', true);

			$.post(baydemirAdminList.ajaxUrl, {
				action: 'baydemir_toggle_featured',
				nonce: baydemirAdminList.nonce,
				post_id: $btn.data('post-id'),
			})
				.done(function (res) {
					if (!res || !res.success) {
						return;
					}
					var on = !!res.data.featured;
					$btn.toggleClass('is-on', on).attr('aria-pressed', on ? 'true' : 'false');
					$btn.find('.dashicons')
						.toggleClass('dashicons-star-filled', on)
						.toggleClass('dashicons-star-empty', !on);
				})
				.always(function () {
					$btn.data('busy', false);
				});
		});
	});
})(jQuery);
