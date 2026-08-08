/**
 * İletişim hero — sync step list with image cards.
 */
(function () {
	'use strict';

	const ready = (fn) => {
		if (document.readyState !== 'loading') fn();
		else document.addEventListener('DOMContentLoaded', fn);
	};

	ready(() => {
		const root = document.querySelector('[data-bd-contact-hero]');
		if (!root) return;

		const detail = root.querySelector('[data-bd-contact-detail]');
		const steps = Array.from(root.querySelectorAll('[data-bd-contact-step]'));
		const cards = Array.from(root.querySelectorAll('[data-bd-contact-card]'));
		if (!steps.length) return;

		const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		let active = 0;
		let timer = 0;

		function setActive(index) {
			active = Math.max(0, Math.min(index, steps.length - 1));
			const btn = steps[active];

			steps.forEach((step, i) => {
				const on = i === active;
				step.classList.toggle('is-active', on);
				step.setAttribute('aria-pressed', on ? 'true' : 'false');
			});

			cards.forEach((card, i) => {
				const on = i === active;
				card.classList.toggle('is-active', on);
				card.setAttribute('aria-pressed', on ? 'true' : 'false');
			});

			if (detail && btn) {
				detail.textContent = btn.getAttribute('data-text') || '';
			}
		}

		function stopAutoplay() {
			if (timer) {
				window.clearInterval(timer);
				timer = 0;
			}
		}

		function startAutoplay() {
			if (reducedMotion) return;
			stopAutoplay();
			timer = window.setInterval(() => {
				setActive((active + 1) % steps.length);
			}, 4800);
		}

		function bind(el, i) {
			el.addEventListener('mouseenter', () => {
				stopAutoplay();
				setActive(i);
			});
			el.addEventListener('focus', () => {
				stopAutoplay();
				setActive(i);
			});
			el.addEventListener('click', () => {
				setActive(i);
				startAutoplay();
			});
		}

		steps.forEach(bind);
		cards.forEach(bind);

		root.addEventListener('mouseleave', startAutoplay);
		document.addEventListener('visibilitychange', () => {
			if (document.hidden) stopAutoplay();
			else startAutoplay();
		});

		setActive(0);
		startAutoplay();
	});
})();
