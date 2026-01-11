function onReady(callback) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback, { once: true });
        return;
    }
    callback();
}

function initMarquee(container) {
    const speedAttr = container.getAttribute('data-marquee-speed');
    const speed = Number.isFinite(Number(speedAttr)) ? Number(speedAttr) : 0.45;

    let rafId = null;
    let paused = false;

    let isPointerDown = false;
    let isDragging = false;
    let didDrag = false;
    let activePointerId = null;
    let startX = 0;
    let startScrollLeft = 0;

    function getHalfWidth() {
        const width = container.scrollWidth;
        return width > 0 ? width / 2 : 0;
    }

    function tick() {
        rafId = window.requestAnimationFrame(tick);

        if (paused) return;

        const half = getHalfWidth();
        if (!half) return;

        container.scrollLeft += speed;
        if (container.scrollLeft >= half) {
            container.scrollLeft -= half;
        }
    }

    function stop() {
        if (rafId != null) {
            window.cancelAnimationFrame(rafId);
            rafId = null;
        }
    }

    function start() {
        if (rafId != null) return;
        rafId = window.requestAnimationFrame(tick);
    }

    function onPointerMove(event) {
        if (!isPointerDown) return;
        if (activePointerId != null && event.pointerId != null && event.pointerId !== activePointerId) return;

        const half = getHalfWidth();
        if (!half) return;

        const dx = event.clientX - startX;

        // Start treating as drag after a small threshold.
        if (!isDragging && Math.abs(dx) >= 6) {
            isDragging = true;
            didDrag = true;
            container.classList.add('is-dragging');
        }

        if (!isDragging) return;

        let next = startScrollLeft - dx;

        // Keep the scroll position within [0, half) to make the loop seamless.
        while (next < 0) next += half;
        while (next >= half) next -= half;

        container.scrollLeft = next;
    }

    function endDrag(event) {
        if (!isPointerDown) return;
        if (activePointerId != null && event.pointerId != null && event.pointerId !== activePointerId) return;

        if (activePointerId != null && typeof container.hasPointerCapture === 'function' && container.hasPointerCapture(activePointerId)) {
            try {
                container.releasePointerCapture(activePointerId);
            } catch (e) {
                // ignore
            }
        }

        isPointerDown = false;
        isDragging = false;
        activePointerId = null;
        container.classList.remove('is-dragging');

        window.removeEventListener('pointermove', onPointerMove, true);
        window.removeEventListener('pointerup', endDrag, true);
        window.removeEventListener('pointercancel', endDrag, true);

        // Resume auto-slide.
        paused = false;

        // Allow click again on next tick.
        window.setTimeout(() => {
            didDrag = false;
        }, 0);
    }

    container.addEventListener('pointerdown', (event) => {
        if (event.button != null && event.button !== 0) return;

        // Prevent native link dragging / text selection from interfering with drag-to-scroll.
        event.preventDefault();

        paused = true;
        isPointerDown = true;
        isDragging = false;
        didDrag = false;
        activePointerId = event.pointerId != null ? event.pointerId : null;
        startX = event.clientX;
        startScrollLeft = container.scrollLeft;

        if (event.pointerId != null && container.setPointerCapture) {
            try {
                container.setPointerCapture(event.pointerId);
            } catch (e) {
                // ignore
            }
        }

        // Track moves globally so dragging works even if the pointer leaves the container.
        window.addEventListener('pointermove', onPointerMove, true);
        window.addEventListener('pointerup', endDrag, true);
        window.addEventListener('pointercancel', endDrag, true);
    });

    // If the user actually dragged, suppress the click on links.
    container.addEventListener(
        'click',
        (event) => {
            if (!didDrag) return;
            event.preventDefault();
            event.stopPropagation();
        },
        true
    );

    // Prevent native link-dragging from interfering.
    container.addEventListener('dragstart', (event) => {
        event.preventDefault();
    });

    // Start slightly away from 0 so the seam is less noticeable.
    const half = getHalfWidth();
    if (half && container.scrollLeft === 0) {
        container.scrollLeft = 1;
    }

    start();

    return {
        start,
        stop,
    };
}

onReady(() => {
    const containers = Array.from(document.querySelectorAll('[data-marquee]'));
    for (const container of containers) {
        initMarquee(container);
    }
});
