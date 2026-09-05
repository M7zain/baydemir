/**
 * Baydemir front-end interactions.
 */
(function () {
	'use strict';

	const ready = (fn) => {
		if (document.readyState !== 'loading') fn();
		else document.addEventListener('DOMContentLoaded', fn);
	};

	ready(() => {
		initHeader();
		initMobileNav();
		initProjectFilters();
		initContactForm();
		initReveal();
		initGalleryLightbox();
		initGallerySlider();
	});

	function initHeader() {
		const header = document.querySelector('.bd-header');
		if (!header) return;
		const onScroll = () => {
			header.classList.toggle('is-scrolled', window.scrollY > 24);
		};
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	function initMobileNav() {
		const toggle = document.querySelector('.bd-nav-toggle');
		const nav = document.querySelector('.bd-nav');
		if (!toggle || !nav) return;

		toggle.addEventListener('click', () => {
			const open = nav.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			document.body.classList.toggle('bd-nav-open', open);
		});

		nav.querySelectorAll('a').forEach((link) => {
			link.addEventListener('click', () => {
				nav.classList.remove('is-open');
				toggle.setAttribute('aria-expanded', 'false');
				document.body.classList.remove('bd-nav-open');
			});
		});
	}

	function initProjectFilters() {
		const filters = document.querySelectorAll('[data-project-filter]');
		const cards = document.querySelectorAll('[data-project-cat]');
		if (!filters.length || !cards.length) return;

		filters.forEach((btn) => {
			btn.addEventListener('click', () => {
				filters.forEach((b) => b.classList.remove('is-active'));
				btn.classList.add('is-active');
				const cat = btn.getAttribute('data-project-filter');
				cards.forEach((card) => {
					const match = cat === 'all' || card.getAttribute('data-project-cat') === cat;
					card.hidden = !match;
				});
			});
		});
	}

	function initContactForm() {
		const form = document.getElementById('bd-contact-form');
		if (!form || typeof baydemirData === 'undefined') return;

		if (window.location.hash === '#bd-contact-form' || form.dataset.projectId) {
			form.scrollIntoView({ behavior: 'smooth', block: 'start' });
		}

		form.addEventListener('submit', async (e) => {
			e.preventDefault();
			const status = form.querySelector('.bd-form-status');
			const btn = form.querySelector('button[type="submit"]');
			const data = new FormData(form);
			data.append('action', 'baydemir_contact');
			data.append('nonce', baydemirData.nonce);

			btn.disabled = true;
			if (status) {
				status.hidden = false;
				status.textContent = baydemirData.i18n.loading;
				status.className = 'bd-form-status';
			}

			try {
				const res = await fetch(baydemirData.ajaxUrl, { method: 'POST', body: data });
				const json = await res.json();
				if (json.success) {
					form.reset();
					restoreContactProjectFields(form);
					if (status) {
						status.textContent = json.data?.message || baydemirData.i18n.success;
						status.classList.add('is-success');
					}
				} else {
					throw new Error(json.data?.message || baydemirData.i18n.error);
				}
			} catch (err) {
				if (status) {
					status.textContent = err.message || baydemirData.i18n.error;
					status.classList.add('is-error');
				}
			} finally {
				btn.disabled = false;
			}
		});
	}

	function restoreContactProjectFields(form) {
		if (!form.dataset.projectId) return;
		const projectInput = form.querySelector('input[name="project_id"]');
		if (projectInput) {
			projectInput.value = form.dataset.projectId;
		}
		const subjectInput = form.querySelector('input[name="subject"]');
		if (subjectInput && form.dataset.projectSubject) {
			subjectInput.value = form.dataset.projectSubject;
		}
	}

	function initReveal() {
		const els = document.querySelectorAll('.bd-reveal');
		if (!els.length || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			els.forEach((el) => el.classList.add('is-visible'));
			return;
		}
		const io = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						io.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
		);
		els.forEach((el) => io.observe(el));
	}

	function initGalleryLightbox() {
		const triggers = document.querySelectorAll('[data-bd-lightbox]');
		if (!triggers.length) return;

		const i18n = (window.baydemirData && baydemirData.i18n) || {};
		const images = Array.from(triggers).map((el) => ({
			full: el.getAttribute('data-full') || el.querySelector('img')?.currentSrc || el.querySelector('img')?.src || '',
			alt: el.querySelector('img')?.alt || '',
		}));

		let overlay = null;
		let imgEl = null;
		let counterEl = null;
		let current = 0;

		const svgClose =
			'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>';
		const svgPrev =
			'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>';
		const svgNext =
			'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>';

		function ensureOverlay() {
			if (overlay) return;

			overlay = document.createElement('div');
			overlay.className = 'bd-lightbox';
			overlay.hidden = true;
			overlay.innerHTML =
				'<div class="bd-lightbox__backdrop" data-bd-lightbox-close></div>' +
				'<div class="bd-lightbox__dialog" role="dialog" aria-modal="true" aria-label="' +
				(i18n.lightboxTitle || 'Galeri') +
				'">' +
				'<button type="button" class="bd-lightbox__close" data-bd-lightbox-close aria-label="' +
				(i18n.lightboxClose || 'Kapat') +
				'">' +
				svgClose +
				'</button>' +
				'<button type="button" class="bd-lightbox__nav bd-lightbox__nav--prev" data-bd-lightbox-prev aria-label="' +
				(i18n.lightboxPrev || 'Önceki') +
				'">' +
				svgPrev +
				'</button>' +
				'<figure class="bd-lightbox__figure"><img class="bd-lightbox__img" src="" alt="" /></figure>' +
				'<button type="button" class="bd-lightbox__nav bd-lightbox__nav--next" data-bd-lightbox-next aria-label="' +
				(i18n.lightboxNext || 'Sonraki') +
				'">' +
				svgNext +
				'</button>' +
				'<p class="bd-lightbox__counter" aria-live="polite"></p>' +
				'</div>';

			document.body.appendChild(overlay);
			imgEl = overlay.querySelector('.bd-lightbox__img');
			counterEl = overlay.querySelector('.bd-lightbox__counter');

			overlay.querySelectorAll('[data-bd-lightbox-close]').forEach((el) => {
				el.addEventListener('click', close);
			});
			overlay.querySelector('[data-bd-lightbox-prev]').addEventListener('click', () => step(-1));
			overlay.querySelector('[data-bd-lightbox-next]').addEventListener('click', () => step(1));
		}

		function show(index) {
			current = (index + images.length) % images.length;
			const item = images[current];
			imgEl.src = item.full;
			imgEl.alt = item.alt;
			counterEl.textContent =
				images.length > 1 ? current + 1 + ' / ' + images.length : '';
			overlay.querySelector('.bd-lightbox__nav--prev').hidden = images.length < 2;
			overlay.querySelector('.bd-lightbox__nav--next').hidden = images.length < 2;
		}

		function open(index) {
			ensureOverlay();
			show(index);
			overlay.hidden = false;
			document.body.classList.add('bd-lightbox-open');
			overlay.querySelector('.bd-lightbox__close').focus();
		}

		function close() {
			if (!overlay) return;
			overlay.hidden = true;
			document.body.classList.remove('bd-lightbox-open');
			if (triggers[current]) {
				triggers[current].focus();
			}
		}

		function step(dir) {
			show(current + dir);
		}

		triggers.forEach((el, index) => {
			el.addEventListener('click', () => open(index));
		});

		document.addEventListener('keydown', (e) => {
			if (!overlay || overlay.hidden) return;
			if (e.key === 'Escape') {
				e.preventDefault();
				close();
			} else if (e.key === 'ArrowLeft') {
				e.preventDefault();
				step(-1);
			} else if (e.key === 'ArrowRight') {
				e.preventDefault();
				step(1);
			}
		});
	}

	/** Project gallery: show one row at a time, navigate with arrows. */
	function initGallerySlider() {
		const root = document.querySelector('[data-bd-gallery-slider]');
		if (!root) return;

		const grid = root.querySelector('[data-bd-gallery]');
		const nav = root.querySelector('[data-bd-gallery-nav]');
		const prevBtn = root.querySelector('[data-bd-gallery-prev]');
		const nextBtn = root.querySelector('[data-bd-gallery-next]');
		const status = root.querySelector('[data-bd-gallery-status]');
		if (!grid || !nav || !prevBtn || !nextBtn) return;

		const items = Array.from(grid.querySelectorAll('.bd-gallery-item'));
		if (!items.length) return;

		let page = 0;
		let resizeTimer = 0;

		function columnCount() {
			const styles = window.getComputedStyle(grid);
			const cols = styles.gridTemplateColumns;
			if (!cols || cols === 'none') return 1;
			return cols.split(' ').filter(Boolean).length || 1;
		}

		function pageSize() {
			return Math.max(1, columnCount());
		}

		function pageCount() {
			return Math.max(1, Math.ceil(items.length / pageSize()));
		}

		function render() {
			const size = pageSize();
			const pages = pageCount();
			page = Math.max(0, Math.min(page, pages - 1));

			const start = page * size;
			const end = start + size;
			items.forEach((item, i) => {
				item.hidden = i < start || i >= end;
			});

			const multi = pages > 1;
			nav.hidden = !multi;
			prevBtn.disabled = page <= 0;
			nextBtn.disabled = page >= pages - 1;
			if (status) {
				status.textContent = multi ? page + 1 + ' / ' + pages : '';
			}
		}

		prevBtn.addEventListener('click', () => {
			page -= 1;
			render();
		});
		nextBtn.addEventListener('click', () => {
			page += 1;
			render();
		});

		window.addEventListener(
			'resize',
			() => {
				window.clearTimeout(resizeTimer);
				resizeTimer = window.setTimeout(render, 120);
			},
			{ passive: true }
		);

		render();
	}

})();
