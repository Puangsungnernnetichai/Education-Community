import lottie from 'lottie-web';

function onReady(callback) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback, { once: true });
        return;
    }
    callback();
}

onReady(() => {
    const nodes = Array.from(document.querySelectorAll('[data-lottie]'));
    if (!nodes.length) return;

    for (const node of nodes) {
        if (node.getAttribute('data-lottie-initialized') === '1') continue;
        const src = node.getAttribute('data-src');
        if (!src) continue;

        node.setAttribute('data-lottie-initialized', '1');
        node.innerHTML = '';

        lottie.loadAnimation({
            container: node,
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: src,
            rendererSettings: {
                preserveAspectRatio: 'xMidYMid meet',
            },
        });
    }
});
