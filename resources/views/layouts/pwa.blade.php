<link rel="manifest" href="{{ route('pwa.manifest', absolute: false) }}" crossorigin="use-credentials">
<meta name="theme-color" content="#b80012">
<link rel="icon" type="image/png" sizes="192x192" href="/pwa-192x192.png">
<link rel="apple-touch-icon" href="/pwa-192x192.png">

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch((error) => {
                console.error('SIABSEN: gagal mendaftarkan service worker', error);
            });
        }, { once: true });
    }
</script>
