{{-- Runs before first paint: the saved choice wins, otherwise the device setting. --}}
<script>
    (function () {
        var saved = null;
        try { saved = localStorage.getItem('riva.theme'); } catch (e) {}
        var dark = saved ? saved === '"dark"' : window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.documentElement.dataset.theme = dark ? 'dark' : 'light';
    })();
</script>
