(function () {
    function onReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn, { once: true });
            return;
        }
        fn();
    }

    onReady(function () {
        if (!window.lottie || !window.lottie.loadAnimation) return;

        var nodes = Array.prototype.slice.call(document.querySelectorAll('[data-lottie]'));
        for (var i = 0; i < nodes.length; i++) {
            var node = nodes[i];
            var src = node.getAttribute('data-src');
            if (!src) continue;

            window.lottie.loadAnimation({
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
})();
