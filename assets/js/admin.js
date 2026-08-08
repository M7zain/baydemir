/**
 * Admin gallery media picker + icon picker.
 *
 * @package Baydemir
 */

(function ($) {
	'use strict';

	function parseIds(val) {
		return (val || '')
			.split(',')
			.map(function (id) {
				return parseInt(id, 10);
			})
			.filter(Boolean);
	}

	function syncInput($input, ids) {
		$input.val(ids.join(','));
	}

	function setEmptyState($gallery, ids) {
		$gallery.toggleClass('is-empty', !ids.length);
	}

	function renderPreview($input, $preview, $gallery) {
		var ids = parseIds($input.val());
		$preview.empty();
		setEmptyState($gallery, ids);

		if (!ids.length) {
			return;
		}

		ids.forEach(function (id) {
			var attachment = wp.media.attachment(id);
			var $item = $('<div />', {
				class: 'bd-gallery-item',
				'data-id': id,
				draggable: false,
			});
			var $remove = $('<button />', {
				type: 'button',
				class: 'bd-gallery-item__remove',
				title: (window.baydemirAdmin && baydemirAdmin.i18n.removeImage) || 'Remove',
				html: '&times;',
			});
			$item.append($remove);
			$preview.append($item);

			attachment.fetch().then(function () {
				var url =
					attachment.get('sizes') && attachment.get('sizes').thumbnail
						? attachment.get('sizes').thumbnail.url
						: attachment.get('url');
				$item.prepend(
					$('<img />', {
						src: url,
						alt: '',
					})
				);
			});
		});
	}

	function readOrder($preview) {
		return $preview
			.children('.bd-gallery-item')
			.map(function () {
				return parseInt($(this).attr('data-id'), 10);
			})
			.get()
			.filter(Boolean);
	}

	function initGallery() {
		var $input = $('#baydemir_gallery');
		var $preview = $('#baydemir-gallery-preview');
		var $gallery = $preview.closest('.bd-gallery');
		if (!$input.length || !$preview.length) {
			return;
		}

		renderPreview($input, $preview, $gallery);

		if ($.fn.sortable) {
			$preview.sortable({
				items: '.bd-gallery-item',
				tolerance: 'pointer',
				opacity: 0.8,
				update: function () {
					var ids = readOrder($preview);
					syncInput($input, ids);
					setEmptyState($gallery, ids);
				},
			});
		}

		var frame;
		function openMedia() {
			if (frame) {
				frame.open();
				return;
			}
			frame = wp.media({
				title: (window.baydemirAdmin && baydemirAdmin.i18n.galleryTitle) || 'Gallery',
				button: {
					text: (window.baydemirAdmin && baydemirAdmin.i18n.galleryButton) || 'Select',
				},
				multiple: true,
			});
			frame.on('select', function () {
				var selection = frame.state().get('selection');
				var ids = selection.map(function (att) {
					return att.id;
				});
				var existing = parseIds($input.val());
				var merged = existing.concat(ids).filter(function (v, i, a) {
					return a.indexOf(v) === i;
				});
				syncInput($input, merged);
				renderPreview($input, $preview, $gallery);
			});
			frame.open();
		}

		$('#baydemir-gallery-add, #baydemir-gallery-add-empty').on('click', function (e) {
			e.preventDefault();
			openMedia();
		});

		$('#baydemir-gallery-clear').on('click', function (e) {
			e.preventDefault();
			syncInput($input, []);
			$preview.empty();
			setEmptyState($gallery, []);
		});

		$preview.on('click', '.bd-gallery-item__remove', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).closest('.bd-gallery-item').remove();
			var ids = readOrder($preview);
			syncInput($input, ids);
			setEmptyState($gallery, ids);
		});
	}

	function initIconPicker() {
		var $input = $('#baydemir_icon');
		var $picker = $('.bd-icon-picker');
		if (!$input.length || !$picker.length) {
			return;
		}

		$picker.on('click', '.bd-icon-picker__btn', function (e) {
			e.preventDefault();
			var $btn = $(this);
			var icon = $btn.data('icon');
			if (!icon) {
				return;
			}
			$input.val(icon);
			$picker.find('.bd-icon-picker__btn').removeClass('is-selected').attr('aria-selected', 'false');
			$btn.addClass('is-selected').attr('aria-selected', 'true');
		});
	}

	function initCoverPicker() {
		var $input = $('#baydemir_cover');
		var $wrap = $('#baydemir-cover-wrap');
		var $preview = $('#baydemir-cover-preview');
		if (!$input.length || !$wrap.length) {
			return;
		}

		var frame;
		$('#baydemir-cover-add').on('click', function (e) {
			e.preventDefault();
			if (frame) {
				frame.open();
				return;
			}
			frame = wp.media({
				title: (window.baydemirAdmin && baydemirAdmin.i18n.coverTitle) || 'Cover',
				button: {
					text: (window.baydemirAdmin && baydemirAdmin.i18n.coverButton) || 'Select',
				},
				library: { type: 'image' },
				multiple: false,
			});
			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				var url =
					attachment.sizes && attachment.sizes.medium
						? attachment.sizes.medium.url
						: attachment.url;
				$input.val(attachment.id);
				$preview.html($('<img />', { src: url, alt: '' }));
				$wrap.removeClass('is-empty');
			});
			frame.open();
		});

		$('#baydemir-cover-clear').on('click', function (e) {
			e.preventDefault();
			$input.val('');
			$preview.empty();
			$wrap.addClass('is-empty');
		});
	}

	function initProjectSort() {
		var $list = $('#baydemir-project-sort');
		var $status = $('#baydemir-order-status');
		if (!$list.length || !$.fn.sortable) {
			return;
		}

		$list.sortable({
			items: '.bd-pm-items__row',
			handle: '.bd-pm-item__handle',
			placeholder: 'bd-pm-items__placeholder',
			tolerance: 'pointer',
			opacity: 0.9,
			update: function () {
				var order = $list
					.children('.bd-pm-items__row')
					.map(function () {
						return parseInt($(this).attr('data-id'), 10);
					})
					.get()
					.filter(Boolean);

				if (!window.baydemirAdmin || !baydemirAdmin.ajaxUrl) {
					return;
				}

				$status
					.prop('hidden', false)
					.removeClass('is-error is-ok')
					.text('…');

				$.post(baydemirAdmin.ajaxUrl, {
					action: 'baydemir_reorder_projects',
					nonce: baydemirAdmin.nonce,
					order: order,
				})
					.done(function (res) {
						if (res && res.success) {
							$status
								.addClass('is-ok')
								.text((baydemirAdmin.i18n && baydemirAdmin.i18n.orderSaved) || 'Saved');
						} else {
							$status
								.addClass('is-error')
								.text((baydemirAdmin.i18n && baydemirAdmin.i18n.orderError) || 'Error');
						}
					})
					.fail(function () {
						$status
							.addClass('is-error')
							.text((baydemirAdmin.i18n && baydemirAdmin.i18n.orderError) || 'Error');
					});
			},
		});
	}

	function initMediaFields() {
		var frames = new WeakMap();

		$(document).on('click', '[data-bd-media] .bd-media-field__add', function (e) {
			e.preventDefault();
			var $field = $(this).closest('[data-bd-media]');
			var $input = $field.find('.bd-media-field__input');
			var $preview = $field.find('.bd-media-field__preview');
			if (!$input.length || !$preview.length) {
				return;
			}

			var frame = frames.get($field[0]);
			if (frame) {
				frame.open();
				return;
			}

			frame = wp.media({
				title: (window.baydemirAdmin && baydemirAdmin.i18n.coverTitle) || 'Select image',
				button: {
					text: (window.baydemirAdmin && baydemirAdmin.i18n.coverButton) || 'Select',
				},
				library: { type: 'image' },
				multiple: false,
			});
			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				var url =
					attachment.sizes && attachment.sizes.medium
						? attachment.sizes.medium.url
						: attachment.sizes && attachment.sizes.thumbnail
							? attachment.sizes.thumbnail.url
							: attachment.url;
				$input.val(attachment.id);
				$preview.html($('<img />', { src: url, alt: '' }));
				$field.removeClass('is-empty');
			});
			frames.set($field[0], frame);
			frame.open();
		});

		$(document).on('click', '[data-bd-media] .bd-media-field__clear', function (e) {
			e.preventDefault();
			var $field = $(this).closest('[data-bd-media]');
			$field.find('.bd-media-field__input').val('');
			$field.find('.bd-media-field__preview').empty();
			$field.addClass('is-empty');
		});
	}

	function initTestimonialsManager() {
		var $list = $('[data-tm-list]');
		var $tpl = $('[data-tm-template]');
		var $add = $('[data-tm-add]');
		if (!$list.length || !$tpl.length || !$add.length) {
			return;
		}

		var max = (window.baydemirAdmin && baydemirAdmin.tmMax) || 20;

		function reindex() {
			$list.find('[data-tm-card]').each(function (i) {
				var $card = $(this);
				$card.find('.bd-testimonial-admin__title').text(
					((window.baydemirAdmin && baydemirAdmin.i18n && baydemirAdmin.i18n.tmCard) || 'Kart %d').replace('%d', String(i + 1))
				);
				$card.find('[name]').each(function () {
					var name = $(this).attr('name') || '';
					$(this).attr('name', name.replace(/items\[[^\]]+\]/, 'items[' + i + ']'));
				});
			});
			$add.prop('disabled', $list.find('[data-tm-card]').length >= max);
		}

		$add.on('click', function (e) {
			e.preventDefault();
			var count = $list.find('[data-tm-card]').length;
			if (count >= max) {
				window.alert(
					((window.baydemirAdmin && baydemirAdmin.i18n && baydemirAdmin.i18n.tmMax) || 'Max %d').replace('%d', String(max))
				);
				return;
			}
			var html = $tpl.prop('outerHTML')
				.replace(/data-tm-template/g, '')
				.replace(/\s*hidden/g, '')
				.replace(/__INDEX__/g, String(count));
			var $card = $(html).removeAttr('hidden').removeAttr('data-tm-template');
			$card.find('.bd-media-field').addClass('is-empty');
			$card.find('.bd-media-field__preview').empty();
			$card.find('.bd-media-field__input').val('');
			$card.find('input[type="text"], textarea').val('');
			$card.find('select').each(function () {
				this.selectedIndex = 0;
			});
			$list.append($card);
			reindex();
		});

		$list.on('click', '[data-tm-remove]', function (e) {
			e.preventDefault();
			var msg =
				(window.baydemirAdmin && baydemirAdmin.i18n && baydemirAdmin.i18n.tmRemoveConfirm) ||
				'Remove?';
			if (!window.confirm(msg)) {
				return;
			}
			$(this).closest('[data-tm-card]').remove();
			if (!$list.find('[data-tm-card]').length) {
				$add.trigger('click');
			}
			reindex();
		});

		reindex();
	}

	$(function () {
		initGallery();
		initIconPicker();
		initCoverPicker();
		initMediaFields();
		initTestimonialsManager();
		initProjectSort();
		initCategorySort();
	});

	function initCategorySort() {
		var $list = $('#the-list');
		var $status = $('#baydemir-cat-order-status');
		if (!$list.length || !$.fn.sortable) {
			return;
		}
		if (!$('body').hasClass('taxonomy-project_category')) {
			return;
		}

		$list.sortable({
			items: 'tr',
			handle: '.bd-cat-order-handle',
			axis: 'y',
			placeholder: 'bd-cat-order-placeholder',
			helper: function (e, ui) {
				ui.children().each(function () {
					$(this).width($(this).width());
				});
				return ui;
			},
			update: function () {
				var order = $list
					.children('tr')
					.map(function () {
						var id = $(this).attr('id') || '';
						var match = id.match(/^tag-(\d+)$/);
						return match ? parseInt(match[1], 10) : 0;
					})
					.get()
					.filter(Boolean);

				if (!window.baydemirAdmin || !baydemirAdmin.ajaxUrl || !order.length) {
					return;
				}

				$status
					.prop('hidden', false)
					.removeClass('is-error is-ok')
					.text('…');

				$.post(baydemirAdmin.ajaxUrl, {
					action: 'baydemir_reorder_categories',
					nonce: baydemirAdmin.nonce,
					order: order,
				})
					.done(function (res) {
						if (res && res.success) {
							$status
								.addClass('is-ok')
								.text((baydemirAdmin.i18n && baydemirAdmin.i18n.orderSaved) || 'Saved');
						} else {
							$status
								.addClass('is-error')
								.text((baydemirAdmin.i18n && baydemirAdmin.i18n.orderError) || 'Error');
						}
					})
					.fail(function () {
						$status
							.addClass('is-error')
							.text((baydemirAdmin.i18n && baydemirAdmin.i18n.orderError) || 'Error');
					});
			},
		});
	}
})(jQuery);
