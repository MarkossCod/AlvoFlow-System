<script setup>
// Layout comum a todas as páginas autenticadas: topo (marca + logout), navegação inferior
// e o submenu "Mais" (Utilizadores/Sobre/Perfil/tema). "isMarkin" (partilhado via Inertia em
// HandleInertiaRequests) decide se o link de administração de utilizadores aparece.
import { route } from 'ziggy-js';
import { ref, reactive, computed, watch } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { ICONS } from '../icons';
import PedidoDetailModal from '../Components/PedidoDetailModal.vue';
import PedidoEditModal from '../Components/PedidoEditModal.vue';
import ConfirmDialogHost from '../Components/ConfirmDialogHost.vue';
import { confirmDialog } from '../confirmDialog';

const page = usePage();

// Toasts — o backend já envia "flash.success"/"flash.error" em cada resposta (ex: ao criar
// um pedido); antes disso não havia nada a lê-los, por isso ações como "Criar Pedido" pareciam
// não fazer nada (o pedido era criado, só não se via confirmação nenhuma).
const toasts = reactive([]);
let nextToastId = 0;

function pushToast(type, message) {
  const id = ++nextToastId;
  toasts.push({ id, type, message, out: false });
  setTimeout(() => removeToast(id), 3200);
}
function removeToast(id) {
  const toast = toasts.find((t) => t.id === id);
  if (!toast) return;
  toast.out = true;
  setTimeout(() => {
    const idx = toasts.findIndex((t) => t.id === id);
    if (idx !== -1) toasts.splice(idx, 1);
  }, 250);
}

watch(() => page.props.flash?.success, (msg) => { if (msg) pushToast('success', msg); });
watch(() => page.props.flash?.error, (msg) => { if (msg) pushToast('danger', msg); });

const NAV_ITEMS = [
  { key: 'home', icon: 'home', label: 'Início', route: 'home' },
  { key: 'criar', icon: 'plus', label: 'Criar Pedido', route: 'pedidos.criar' },
  { key: 'pesquisar', icon: 'search', label: 'Pesquisar', route: 'pedidos.pesquisar' },
  { key: 'sentinela', icon: 'book', label: 'Sentinela', route: 'sentinela.index' },
  { key: 'visualizar', icon: 'folder', label: 'Visualizar', route: 'pedidos.visualizar' },
  { key: 'painel', icon: 'chart', label: 'Painel', route: 'painel' },
];

// route().current() por si só não é reativo (o Ziggy não é "Vue-aware"), por isso o realce
// do separador ativo ficava sempre preso na primeira página carregada — ao ler "page.url"
// (que o Inertia atualiza a cada navegação) dentro do computed, forçamos o Vue a recalcular
// sempre que a rota muda.
const currentRouteName = computed(() => { void page.url; return route().current(); });
const openSubmenu = ref(false);

const theme = ref(typeof window !== 'undefined' ? (localStorage.getItem('alvoflow-theme') || 'light') : 'light');

function applyTheme(t) {
  theme.value = t;
  localStorage.setItem('alvoflow-theme', t);
  const resolved = t === 'system'
    ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
    : t;
  document.documentElement.setAttribute('data-theme', resolved);
}

function toggleMore() {
  openSubmenu.value = !openSubmenu.value;
}
function closeSubmenu() {
  openSubmenu.value = false;
}

async function doLogout() {
  const ok = await confirmDialog('Terminar sessão?', 'Vai precisar de iniciar sessão novamente para aceder ao AlvoFlow.', { confirmLabel: 'Terminar sessão', danger: false });
  if (!ok) return;
  router.post(route('logout'));
}
</script>

<template>
  <div class="topbar">
    <div class="brand">
      <div class="mark">
        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" width="34" height="34">
          <rect width="64" height="64" rx="16" fill="#132743"/>
          <path d="M32 11 L15 53" stroke="#d9b25e" stroke-width="7" stroke-linecap="round" fill="none"/>
          <path d="M32 11 L49 53" stroke="#d9b25e" stroke-width="7" stroke-linecap="round" fill="none"/>
          <path d="M21 35 L30 40 L21 45" stroke="#f4f6fa" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        </svg>
      </div>
      AlvoFlow
    </div>
    <div class="spacer"></div>
    <button class="icon-btn" title="Terminar sessão" @click="doLogout" v-html="ICONS.logout"></button>
  </div>

  <main class="view-wrap">
    <slot />
  </main>

  <div id="bottomnav">
    <div id="submenu-host">
      <div v-if="openSubmenu" class="submenu show" style="right:0; left:0; margin:auto; width:240px; position:absolute; bottom:64px;">
        <Link v-if="page.props.auth.isMarkin" :href="route('utilizadores')" prefetch @click="closeSubmenu"><span v-html="ICONS.users"></span><span>Utilizadores Registados</span></Link>
        <Link :href="route('sobre')" prefetch @click="closeSubmenu"><span v-html="ICONS.info"></span><span>Sobre o Sistema</span></Link>
        <Link :href="route('perfil')" prefetch @click="closeSubmenu"><span v-html="ICONS.user"></span><span>Configurações de Perfil</span></Link>
        <div style="border-top:1px solid var(--border); margin:6px 0;"></div>
        <div style="font-size:11px; color:var(--text-muted); padding:4px 12px 6px;">TEMA</div>
        <div class="theme-row" style="padding:0 6px 6px;">
          <button :class="{ on: theme === 'light' }" @click="applyTheme('light')"><span v-html="ICONS.sun"></span><span>Claro</span></button>
          <button :class="{ on: theme === 'dark' }" @click="applyTheme('dark')"><span v-html="ICONS.moon"></span><span>Escuro</span></button>
          <button :class="{ on: theme === 'system' }" @click="applyTheme('system')"><span v-html="ICONS.laptop"></span><span>Sistema</span></button>
        </div>
      </div>
    </div>
    <div class="nav-bar">
      <Link
        v-for="item in NAV_ITEMS"
        :key="item.key"
        :href="route(item.route)"
        :class="{ active: currentRouteName === item.route }"
        :data-label="item.label"
        prefetch
        @click="closeSubmenu"
      >
        <span class="nav-icon" v-html="ICONS[item.icon]"></span>
      </Link>
      <button :class="{ active: openSubmenu }" data-label="Mais" @click="toggleMore">
        <span class="nav-icon" v-html="ICONS.more"></span>
      </button>
    </div>
  </div>

  <PedidoDetailModal />
  <PedidoEditModal />
  <ConfirmDialogHost />

  <div id="toasts">
    <div v-for="t in toasts" :key="t.id" class="toast" :class="[t.type, { out: t.out }]">
      <span>{{ t.message }}</span>
    </div>
  </div>
</template>
