// Service worker do AlvoFlow — só torna a app instalável e acelera a abertura seguinte,
// não é um modo offline completo. Cacheia SÓ os ficheiros estáticos do build (JS/CSS/ícones,
// nomes com hash do Vite — cache-first é seguro porque um hash nunca muda de conteúdo).
// Nunca cacheia páginas nem dados (pedidos/sentinela/painel são por utilizador e mudam sempre —
// cachear isso arriscava mostrar dados errados ou de outra sessão quando offline). Sem lista
// fixa de ficheiros para pré-cache: fica em cache o que for passando pela rede.
const CACHE = 'alvoflow-static-v1';

function isCacheable(url) {
    return url.origin === self.location.origin
        && (url.pathname.startsWith('/build/') || url.pathname.startsWith('/icons/') || url.pathname === '/manifest.webmanifest');
}

self.addEventListener('install', () => self.skipWaiting());

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);
    if (request.method !== 'GET' || !isCacheable(url)) return; // tudo o resto vai direto para a rede

    event.respondWith(
        caches.match(request).then((cached) => cached || fetch(request).then((response) => {
            const copy = response.clone();
            // waitUntil mantém o service worker vivo até a cópia ficar gravada — sem isto o
            // browser podia matar o worker assim que a resposta chegasse à página, antes do
            // cache.put() (que corre à parte, sem estar ligado à promise do respondWith) terminar.
            event.waitUntil(caches.open(CACHE).then((cache) => cache.put(request, copy)));
            return response;
        }))
    );
});
