import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Load non-critical UI modules after Alpine starts.
// If any module errors, we still want Alpine-powered pages (games, modals) to work.
Promise.allSettled([
	import('./lottie-hero'),
	import('./marquee-drag'),
	import('./posts'),
]).catch(() => {
	// ignore
});
