/**
 * Quality page — construction image focus zoom + pin sync.
 */
(function () {
	'use strict';

	const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function initRoot(root) {
		const frame = root.querySelector('.bd-quality-building__frame');
		const resetBtn = root.querySelector('.bd-quality-building__reset');
		const caption = root.querySelector('[data-bd-quality-caption]');
		const panel = root.closest('.bd-quality-depth__panel--compose');
		if (!frame) return;

		const base = { scale: 1, ox: '50%', oy: '50%' };
		let focusId = null;
		let raf = 0;
		let target = { ...base };
		let current = { scale: 1 };
		let hover = { x: 0, y: 0 };

		const labels = {};
		(panel || root).querySelectorAll('.bd-quality-compose-list__btn').forEach((btn) => {
			const id = btn.getAttribute('data-focus');
			const label = btn.querySelector('.bd-quality-compose-list__label');
			if (id && label) labels[id] = label.textContent.trim();
		});

		const apply = () => {
			raf = 0;
			const ease = focusId ? 0.18 : 0.12;
			current.scale += (target.scale - current.scale) * ease;
			frame.style.transformOrigin = target.ox + ' ' + target.oy;
			const tx = focusId ? 0 : hover.x;
			const ty = focusId ? 0 : hover.y;
			frame.style.transform =
				'translate(' + tx.toFixed(1) + 'px, ' + ty.toFixed(1) + 'px) scale(' + current.scale.toFixed(3) + ')';

			if (Math.abs(target.scale - current.scale) > 0.004) {
				raf = requestAnimationFrame(apply);
			}
		};

		const queue = () => {
			if (!raf) raf = requestAnimationFrame(apply);
		};

		const setActive = (id) => {
			(panel || document).querySelectorAll('[data-focus]').forEach((el) => {
				el.classList.toggle('is-active', el.getAttribute('data-focus') === String(id));
			});
		};

		const showCaption = (id) => {
			if (!caption) return;
			if (!id || !labels[id]) {
				caption.hidden = true;
				caption.textContent = '';
				return;
			}
			caption.hidden = false;
			caption.textContent = id + '. ' + labels[id];
		};

		const clearFocus = () => {
			focusId = null;
			target = { ...base };
			root.classList.remove('is-focused');
			if (resetBtn) resetBtn.hidden = true;
			setActive('');
			showCaption('');
			queue();
		};

		const focusTo = (btn) => {
			const id = btn.getAttribute('data-focus');
			if (focusId === id) {
				clearFocus();
				return;
			}
			focusId = id;
			target = {
				scale: parseFloat(btn.getAttribute('data-scale')) || 2,
				ox: btn.getAttribute('data-ox') || base.ox,
				oy: btn.getAttribute('data-oy') || base.oy,
			};
			hover = { x: 0, y: 0 };
			root.classList.add('is-focused');
			if (resetBtn) resetBtn.hidden = false;
			setActive(id);
			showCaption(id);
			queue();
		};

		(panel || root).querySelectorAll('[data-focus]').forEach((btn) => {
			btn.addEventListener('click', (e) => {
				e.preventDefault();
				e.stopPropagation();
				focusTo(btn);
			});
		});

		if (resetBtn) {
			resetBtn.addEventListener('click', (e) => {
				e.preventDefault();
				clearFocus();
			});
		}

		if (!reduceMotion) {
			root.addEventListener(
				'pointermove',
				(e) => {
					if (focusId) return;
					const rect = root.getBoundingClientRect();
					const nx = ((e.clientX - rect.left) / Math.max(rect.width, 1)) * 2 - 1;
					const ny = ((e.clientY - rect.top) / Math.max(rect.height, 1)) * 2 - 1;
					hover.x = nx * 6;
					hover.y = ny * 4;
					queue();
				},
				{ passive: true }
			);

			root.addEventListener(
				'pointerleave',
				() => {
					hover = { x: 0, y: 0 };
					queue();
				},
				{ passive: true }
			);
		}

		queue();
	}

	function boot() {
		document.querySelectorAll('[data-bd-quality-3d]').forEach(initRoot);
	}

	if (document.readyState !== 'loading') boot();
	else document.addEventListener('DOMContentLoaded', boot);
})();
