<!DOCTYPE html>
<html lang="pt-PT" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title inertia>{{ config('app.name', 'AlvoFlow') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 64 64%22><rect width=%2264%22 height=%2264%22 rx=%2216%22 fill=%22%23132743%22/><path d=%22M32 11 L15 53%22 stroke=%22%23d9b25e%22 stroke-width=%227%22 stroke-linecap=%22round%22 fill=%22none%22/><path d=%22M32 11 L49 53%22 stroke=%22%23d9b25e%22 stroke-width=%227%22 stroke-linecap=%22round%22 fill=%22none%22/><path d=%22M21 35 L30 40 L21 45%22 stroke=%22%23f4f6fa%22 stroke-width=%225%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 fill=%22none%22/></svg>">
    <script>
        // Aplica o tema guardado antes do primeiro paint, para não haver "flash" de tema errado.
        document.documentElement.setAttribute('data-theme', localStorage.getItem('alvoflow-theme') || 'light');
    </script>
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body>
    <div id="splash">
        <div class="splash-inner">
            <div class="splash-logo">
                <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="lgBgSplash" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#17324f"/><stop offset="1" stop-color="#0d1b2e"/></linearGradient>
                        <linearGradient id="lgGoldSplash" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#e0bf76"/><stop offset="1" stop-color="#c9a24b"/></linearGradient>
                    </defs>
                    <rect width="64" height="64" rx="16" fill="url(#lgBgSplash)"/>
                    <path d="M32 11 L15 53" stroke="url(#lgGoldSplash)" stroke-width="7" stroke-linecap="round" fill="none"/>
                    <path d="M32 11 L49 53" stroke="url(#lgGoldSplash)" stroke-width="7" stroke-linecap="round" fill="none"/>
                    <path d="M21 35 L30 40 L21 45" stroke="#f4f6fa" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
            </div>
            <h1>AlvoFlow</h1>
            <p>Balcão de Publicações</p>
            <div class="splash-bar"><span></span></div>
        </div>
    </div>

    @inertia

    <div id="enter-transition">
        <div class="enter-inner">
            <div class="enter-ring">
                <svg viewBox="0 0 92 92"><circle class="bg" cx="46" cy="46" r="40"/><circle class="fg" cx="46" cy="46" r="40"/></svg>
                <span class="enter-check">✓</span>
            </div>
            <h2 id="enter-title">Acesso confirmado</h2>
            <p>A preparar o seu painel, um momento...</p>
        </div>
    </div>
    <script>
        // Animação de entrada (login/registo), disparada pelas páginas Vue via CustomEvent.
        // Mostrada já no início do pedido (não só quando a resposta chega) para não haver
        // o "flash" da página seguinte antes da animação aparecer; se o login/registo falhar,
        // "alvoflow:enter:cancel" esconde tudo na hora para mostrar os erros do formulário.
        window.addEventListener('alvoflow:enter', function (e) {
            var el = document.getElementById('enter-transition');
            document.getElementById('enter-title').textContent = (e.detail && e.detail.title) || 'Acesso confirmado';
            el.classList.add('show');
            setTimeout(function () { el.classList.remove('show'); }, 1450);
        });
        window.addEventListener('alvoflow:enter:cancel', function () {
            document.getElementById('enter-transition').classList.remove('show');
        });

        // Splash inicial: garante uns instantes de animação da logo antes de mostrar
        // a tela de login/painel, mesmo que os assets carreguem quase instantaneamente.
        window.addEventListener('load', function () {
            setTimeout(function () {
                var s = document.getElementById('splash');
                if (s) s.classList.add('hide');
            }, 700);
        });
    </script>
</body>
</html>
