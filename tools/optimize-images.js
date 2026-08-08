/**
 * Compress large theme images for a smaller install zip.
 * Run: node optimize-images.js
 */
const fs = require('fs');
const path = require('path');
const sharp = require('sharp');

const root = path.resolve(__dirname, '..');

const jobs = [
	// Opaque photos → JPEG
	{ rel: 'assets/lights-on.png', out: 'assets/lights-on.jpg', type: 'jpeg', quality: 82, maxW: 1672 },
	{ rel: 'assets/lights-off.png', out: 'assets/lights-off.jpg', type: 'jpeg', quality: 82, maxW: 1672 },
	{ rel: 'assets/images/kalite.png', out: 'assets/images/kalite.jpg', type: 'jpeg', quality: 82, maxW: 1600 },
	// Transparent UI art → compressed PNG, capped width
	{ rel: 'assets/images/teklif-al.png', out: 'assets/images/teklif-al.png', type: 'png', maxW: 960 },
	{ rel: 'assets/images/deneyim.png', out: 'assets/images/deneyim.png', type: 'png', maxW: 720 },
	{ rel: 'assets/images/tamamlanan-projeler.png', out: 'assets/images/tamamlanan-projeler.png', type: 'png', maxW: 720 },
	{ rel: 'assets/images/construction.png', out: 'assets/images/construction.png', type: 'png', maxW: 900 },
];

async function run() {
	for (const job of jobs) {
		const input = path.join(root, job.rel);
		const output = path.join(root, job.out);
		if (!fs.existsSync(input)) {
			console.log('skip missing', job.rel);
			continue;
		}
		const before = fs.statSync(input).size;
		let pipeline = sharp(input).rotate();
		const meta = await sharp(input).metadata();
		if (job.maxW && meta.width && meta.width > job.maxW) {
			pipeline = pipeline.resize({ width: job.maxW, withoutEnlargement: true });
		}
		if (job.type === 'jpeg') {
			await pipeline.jpeg({ quality: job.quality, mozjpeg: true }).toFile(output + '.tmp');
		} else {
			await pipeline.png({ compressionLevel: 9, palette: false, effort: 10 }).toFile(output + '.tmp');
		}
		fs.renameSync(output + '.tmp', output);
		if (job.rel !== job.out && fs.existsSync(input) && path.resolve(input) !== path.resolve(output)) {
			fs.unlinkSync(input);
		}
		const after = fs.statSync(output).size;
		console.log(
			`${job.out}: ${(before / 1024 / 1024).toFixed(2)}MB → ${(after / 1024 / 1024).toFixed(2)}MB`
		);
	}
}

run().catch((err) => {
	console.error(err);
	process.exit(1);
});
