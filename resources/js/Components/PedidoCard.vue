<script setup>
import { verPedido } from '../detailStore';

const props = defineProps({ pedido: { type: Object, required: true } });

function badgeClass(estado) {
    return estado === 'Aberto' ? 'aberto' : estado === 'Em Andamento' ? 'andamento' : 'concluido';
}
function barColor(estado) {
    return estado === 'Aberto' ? 'var(--warn)' : estado === 'Em Andamento' ? 'var(--accent)' : 'var(--success)';
}
function initials(name) {
    return (name || '').split(' ').filter(Boolean).map((w) => w[0]).slice(0, 2).join('').toUpperCase();
}
function formatDateBR(iso) {
    const [y, m, d] = (iso || '').split('-');
    return y && m && d ? `${d}/${m}/${y}` : iso || '';
}
</script>

<template>
    <div class="pedido-card">
        <div class="bar" :style="{ background: barColor(pedido.estado) }"></div>
        <div class="head">
            <div class="who">
                <div class="avatar" :style="{ background: `color-mix(in srgb, ${barColor(pedido.estado)} 18%, var(--surface-2))`, color: barColor(pedido.estado) }">
                    {{ initials(pedido.publicador) }}
                </div>
                <div><div class="pub">{{ pedido.publicador }}</div><div class="id">{{ pedido.codigo }}</div></div>
            </div>
            <span class="badge" :class="badgeClass(pedido.estado)">{{ pedido.estado }}</span>
        </div>
        <div class="title"><span class="t-ico">📖</span> {{ pedido.publicacao }}</div>
        <div class="meta">
            <span>🔢 Qtd: {{ pedido.quantidade }}</span>
            <span>📅 {{ formatDateBR(pedido.data) }}</span>
            <span v-if="pedido.criado_por">👤 {{ pedido.criado_por }}</span>
        </div>
        <div class="card-actions-row">
            <slot name="actions">
                <button class="btn-view" @click="verPedido(pedido)">👁️ Ver detalhes</button>
            </slot>
        </div>
    </div>
</template>
