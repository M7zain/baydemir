/**
 * Living facade lights — soft-feathered window masks; hover dims the window under the cursor.
 */
(function () {
	'use strict';

	const ready = (fn) => {
		if (document.readyState !== 'loading') fn();
		else document.addEventListener('DOMContentLoaded', fn);
	};

	/** Soft edge size in CSS px — blends balcony / edge lights into facade. */
	const FEATHER = 12;
	/** How quickly a hovered window fades to off. */
	const HOVER_SPEED = 0.22;

	ready(() => {
		const hero = document.querySelector('[data-bd-lights-banner]');
		if (!hero) return;

		const media = hero.querySelector('.bd-hero__media--lights');
		if (!media) return;

		const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		const offImg = media.querySelector('.bd-hero__lights--off');
		const onImg = media.querySelector('.bd-hero__lights--on');
		if (!offImg || !onImg) return;

		const dataUrl =
			(window.baydemirLights && baydemirLights.windowsData) ||
			(onImg.src || '').replace(/lights-on\.(png|jpe?g).*$/i, 'js/lights-windows-data.json');

		const canvas = document.createElement('canvas');
		canvas.className = 'bd-hero__lights-canvas';
		canvas.setAttribute('aria-hidden', 'true');
		media.querySelector('.bd-hero__lights-stack')?.appendChild(canvas);

		const ctx = canvas.getContext('2d', { alpha: false });
		if (!ctx) return;

		media.classList.add('is-canvas-lights');

		const imgOff = new Image();
		const imgOn = new Image();
		const scratch = document.createElement('canvas');
		const scratchCtx = scratch.getContext('2d');
		let windows = [];
		let readyFlags = { off: false, on: false, data: false };
		let layout = { dx: 0, dy: 0, dw: 0, dh: 0 };
		let cssW = 1;
		let cssH = 1;
		let dpr = 1;
		let raf = 0;
		let running = false;
		let zones = [];
		let hoverIndex = -1;

		function tryBoot() {
			if (!readyFlags.off || !readyFlags.on || !readyFlags.data) return;
			zones = createZoneState(windows);
			resize();
			start();
		}

		imgOff.onload = () => {
			readyFlags.off = true;
			tryBoot();
		};
		imgOn.onload = () => {
			readyFlags.on = true;
			tryBoot();
		};
		imgOff.src = offImg.currentSrc || offImg.src;
		imgOn.src = onImg.currentSrc || onImg.src;
		if (imgOff.complete && imgOff.naturalWidth) {
			imgOff.onload = null;
			readyFlags.off = true;
		}
		if (imgOn.complete && imgOn.naturalWidth) {
			imgOn.onload = null;
			readyFlags.on = true;
		}

		fetch(dataUrl, { credentials: 'same-origin' })
			.then((r) => (r.ok ? r.json() : Promise.reject()))
			.then((data) => {
				windows = Array.isArray(data.windows) ? data.windows.filter((w) => w.poly && w.poly.length >= 3) : [];
				readyFlags.data = true;
				tryBoot();
			})
			.catch(() => {
				windows = [];
				readyFlags.data = true;
				tryBoot();
			});

		function createZoneState(defs) {
			const now = performance.now();
			return defs.map((z, i) => {
				const lit = Math.random() > 0.18;
				return {
					poly: z.poly,
					litAlpha: lit ? 0.75 + Math.random() * 0.25 : 0.04 + Math.random() * 0.12,
					target: lit ? 1 : 0,
					nextFlip: now + 2800 + Math.random() * 10000 + i * 220,
					holdOn: 7000 + Math.random() * 14000,
					holdOff: 2800 + Math.random() * 6000,
					flicker: Math.random() > 0.93,
					speed: 0.01 + Math.random() * 0.012,
					softMask: null,
					maskX: 0,
					maskY: 0,
					maskW: 0,
					maskH: 0,
				};
			});
		}

		function coverLayout(cw, ch, iw, ih) {
			const ir = iw / ih;
			const cr = cw / ch;
			let dw;
			let dh;
			let dx;
			let dy;
			if (ir > cr) {
				dh = ch;
				dw = ch * ir;
				dx = (cw - dw) / 2;
				dy = 0;
			} else {
				dw = cw;
				dh = cw / ir;
				dx = 0;
				dy = (ch - dh) / 2;
			}
			return { dx, dy, dw, dh };
		}

		function polyBounds(poly) {
			let minX = 1;
			let minY = 1;
			let maxX = 0;
			let maxY = 0;
			for (let i = 0; i < poly.length; i++) {
				const p = poly[i];
				if (p.x < minX) minX = p.x;
				if (p.y < minY) minY = p.y;
				if (p.x > maxX) maxX = p.x;
				if (p.y > maxY) maxY = p.y;
			}
			return { minX, minY, maxX, maxY };
		}

		/** Ray-cast point-in-polygon (normalized image coords). */
		function pointInPoly(nx, ny, poly) {
			let inside = false;
			for (let i = 0, j = poly.length - 1; i < poly.length; j = i++) {
				const xi = poly[i].x;
				const yi = poly[i].y;
				const xj = poly[j].x;
				const yj = poly[j].y;
				const intersect =
					yi > ny !== yj > ny && nx < ((xj - xi) * (ny - yi)) / (yj - yi + 1e-12) + xi;
				if (intersect) inside = !inside;
			}
			return inside;
		}

		function screenToNorm(clientX, clientY) {
			const rect = media.getBoundingClientRect();
			const x = clientX - rect.left;
			const y = clientY - rect.top;
			if (layout.dw <= 0 || layout.dh <= 0) return null;
			return {
				nx: (x - layout.dx) / layout.dw,
				ny: (y - layout.dy) / layout.dh,
			};
		}

		function hitTestWindow(clientX, clientY) {
			const n = screenToNorm(clientX, clientY);
			if (!n || n.nx < 0 || n.nx > 1 || n.ny < 0 || n.ny > 1) return -1;
			// Reverse so later / overlapping windows win the hover.
			for (let i = zones.length - 1; i >= 0; i--) {
				const zone = zones[i];
				const b = polyBounds(zone.poly);
				const pad = 0.004;
				if (
					n.nx < b.minX - pad ||
					n.nx > b.maxX + pad ||
					n.ny < b.minY - pad ||
					n.ny > b.maxY + pad
				) {
					continue;
				}
				if (pointInPoly(n.nx, n.ny, zone.poly)) return i;
			}
			return -1;
		}

		function setHoverIndex(next) {
			if (next === hoverIndex) return;
			const prev = hoverIndex;
			hoverIndex = next;
			media.classList.toggle('is-hover-window', hoverIndex >= 0);
			if (reducedMotion) {
				if (prev >= 0 && zones[prev]) zones[prev].litAlpha = zones[prev].target;
				if (hoverIndex >= 0 && zones[hoverIndex]) zones[hoverIndex].litAlpha = 0;
				if (running) draw(performance.now());
			}
		}

		function onPointerMove(e) {
			if (!zones.length) return;
			setHoverIndex(hitTestWindow(e.clientX, e.clientY));
		}

		function onPointerLeave() {
			setHoverIndex(-1);
		}

		/** Build blurred alpha masks once per layout (feathered window shapes). */
		function rebuildSoftMasks() {
			const pad = FEATHER * 2.5;
			zones.forEach((zone) => {
				const b = polyBounds(zone.poly);
				const x0 = layout.dx + b.minX * layout.dw - pad;
				const y0 = layout.dy + b.minY * layout.dh - pad;
				const mw = Math.max(4, (b.maxX - b.minX) * layout.dw + pad * 2);
				const mh = Math.max(4, (b.maxY - b.minY) * layout.dh + pad * 2);

				const hard = document.createElement('canvas');
				hard.width = Math.max(1, Math.ceil(mw * dpr));
				hard.height = Math.max(1, Math.ceil(mh * dpr));
				const hctx = hard.getContext('2d');
				if (!hctx) return;
				hctx.setTransform(dpr, 0, 0, dpr, 0, 0);
				hctx.clearRect(0, 0, mw, mh);
				hctx.fillStyle = '#fff';
				hctx.beginPath();
				for (let i = 0; i < zone.poly.length; i++) {
					const px = layout.dx + zone.poly[i].x * layout.dw - x0;
					const py = layout.dy + zone.poly[i].y * layout.dh - y0;
					if (i === 0) hctx.moveTo(px, py);
					else hctx.lineTo(px, py);
				}
				hctx.closePath();
				hctx.fill();

				const soft = document.createElement('canvas');
				soft.width = hard.width;
				soft.height = hard.height;
				const sctx = soft.getContext('2d');
				if (!sctx) return;
				sctx.setTransform(dpr, 0, 0, dpr, 0, 0);
				sctx.clearRect(0, 0, mw, mh);
				sctx.filter = 'blur(' + FEATHER + 'px)';
				sctx.drawImage(hard, 0, 0, mw, mh);
				sctx.filter = 'none';

				zone.softMask = soft;
				zone.maskX = x0;
				zone.maskY = y0;
				zone.maskW = mw;
				zone.maskH = mh;
			});
		}

		function resize() {
			const rect = media.getBoundingClientRect();
			cssW = Math.max(1, Math.round(rect.width));
			cssH = Math.max(1, Math.round(rect.height));
			dpr = Math.min(window.devicePixelRatio || 1, 2);
			canvas.width = Math.round(cssW * dpr);
			canvas.height = Math.round(cssH * dpr);
			canvas.style.width = cssW + 'px';
			canvas.style.height = cssH + 'px';
			ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
			layout = coverLayout(cssW, cssH, imgOn.naturalWidth || 1672, imgOn.naturalHeight || 941);
			rebuildSoftMasks();
			if (running) draw(performance.now());
		}

		function drawCover(img) {
			ctx.drawImage(img, layout.dx, layout.dy, layout.dw, layout.dh);
		}

		/** Soft punch: lights-off through feathered window alpha. */
		function punchOffWindow(zone) {
			const offAmount = 1 - Math.min(1, Math.max(0, zone.litAlpha));
			if (offAmount <= 0.02 || !zone.softMask || !scratchCtx) return;

			const mw = zone.maskW;
			const mh = zone.maskH;
			scratch.width = Math.max(1, Math.ceil(mw * dpr));
			scratch.height = Math.max(1, Math.ceil(mh * dpr));
			scratchCtx.setTransform(dpr, 0, 0, dpr, 0, 0);
			scratchCtx.clearRect(0, 0, mw, mh);

			scratchCtx.drawImage(
				imgOff,
				layout.dx - zone.maskX,
				layout.dy - zone.maskY,
				layout.dw,
				layout.dh
			);
			scratchCtx.globalCompositeOperation = 'destination-in';
			scratchCtx.drawImage(zone.softMask, 0, 0, mw, mh);
			scratchCtx.globalCompositeOperation = 'source-over';

			ctx.save();
			ctx.globalAlpha = offAmount;
			ctx.drawImage(scratch, zone.maskX, zone.maskY, mw, mh);
			ctx.restore();
		}

		function updateZones(now) {
			zones.forEach((zone, i) => {
				if (i === hoverIndex) {
					zone.litAlpha += (0 - zone.litAlpha) * HOVER_SPEED;
					return;
				}

				if (now >= zone.nextFlip) {
					if (zone.flicker && Math.random() > 0.75) {
						zone.target = 0.3 + Math.random() * 0.25;
						zone.nextFlip = now + 160 + Math.random() * 280;
					} else {
						zone.target = Math.random() > 0.2 ? 1 : 0;
						zone.nextFlip =
							now +
							(zone.target > 0.5 ? zone.holdOn : zone.holdOff) +
							Math.random() * 2200;
					}
				}
				zone.litAlpha += (zone.target - zone.litAlpha) * zone.speed;
			});

			if (Math.random() > 0.9988 && zones.length) {
				const seed = zones[Math.floor(Math.random() * zones.length)];
				if (zones.indexOf(seed) !== hoverIndex) {
					seed.target = 1;
					seed.nextFlip = now + 4500 + Math.random() * 4000;
				}
			}
		}

		function draw(now) {
			drawCover(imgOff);
			drawCover(imgOn);
			if (zones.length) {
				zones.forEach(punchOffWindow);
			}
			if (!reducedMotion && zones.length) updateZones(now);
		}

		function tick(now) {
			draw(now);
			raf = requestAnimationFrame(tick);
		}

		function start() {
			if (running) return;
			running = true;
			media.addEventListener('pointermove', onPointerMove, { passive: true });
			media.addEventListener('pointerleave', onPointerLeave);
			if (reducedMotion) {
				draw(performance.now());
				return;
			}
			raf = requestAnimationFrame(tick);
		}

		function stop() {
			running = false;
			if (raf) cancelAnimationFrame(raf);
			raf = 0;
			media.removeEventListener('pointermove', onPointerMove);
			media.removeEventListener('pointerleave', onPointerLeave);
			setHoverIndex(-1);
		}

		window.addEventListener('resize', resize);
		document.addEventListener('visibilitychange', () => {
			if (document.hidden) stop();
			else if (readyFlags.off && readyFlags.on && readyFlags.data) start();
		});

		tryBoot();
	});
})();
