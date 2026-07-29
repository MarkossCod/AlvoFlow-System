<script setup>
import { route } from 'ziggy-js';
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { detailStore, fecharDetail, editarPedido } from '../detailStore';
import { confirmDialog } from '../confirmDialog';

const ESTADOS = ['Aberto', 'Em Andamento', 'Concluído'];

const pedido = computed(() => detailStore.pedido);

function badgeClass(estado) {
    return estado === 'Aberto' ? 'aberto' : estado === 'Em Andamento' ? 'andamento' : 'concluido';
}
function formatDateBR(iso) {
    const [y, m, d] = (iso || '').split('-');
    return y && m && d ? `${d}/${m}/${y}` : iso || '';
}

function mudarEstado() {
    const idx = ESTADOS.indexOf(pedido.value.estado);
    const proximo = ESTADOS[(idx + 1) % ESTADOS.length];
    router.patch(route('pedidos.update', pedido.value.id), { estado: proximo }, {
        preserveScroll: true,
        onSuccess: () => { pedido.value.estado = proximo; },
    });
}

async function excluir() {
    const ok = await confirmDialog('Excluir pedido?', `O pedido de "${pedido.value.publicador}" será removido permanentemente.`);
    if (!ok) return;
    router.delete(route('pedidos.destroy', pedido.value.id), {
        preserveScroll: true,
        onSuccess: fecharDetail,
    });
}
</script>

<template>
    <div v-if="pedido" id="detail-overlay" class="show" @click="(e) => e.target.id === 'detail-overlay' && fecharDetail()">
        <div class="detail-box">
            <div class="detail-head">
                <div>
                    <span class="badge" :class="badgeClass(pedido.estado)">{{ pedido.estado }}</span>
                    <h3>{{ pedido.publicador }}</h3>
                    <div class="id">{{ pedido.codigo }}</div>
                </div>
                <button class="icon-btn" @click="fecharDetail">✕</button>
            </div>
            <div class="detail-grid">
                <div><span class="lbl">Publicação</span><div class="val">{{ pedido.publicacao }}</div></div>
                <div><span class="lbl">Quantidade</span><div class="val">{{ pedido.quantidade }}</div></div>
                <div><span class="lbl">Data da Solicitação</span><div class="val">{{ formatDateBR(pedido.data) }}</div></div>
                <div><span class="lbl">Estado</span><div class="val">{{ pedido.estado }}</div></div>
            </div>
            <div v-if="pedido.observacoes" style="margin-bottom:4px;">
                <span class="lbl">Observações</span>
                <div class="obs">{{ pedido.observacoes }}</div>
            </div>
            <div class="detail-actions">
                <button class="btn btn-outline" @click="editarPedido">✏️ Editar</button>
                <button class="btn btn-outline" @click="mudarEstado">🔁 Mudar Estado</button>
                <button class="btn" style="background:var(--danger); color:#fff; box-shadow:none;" @click="excluir">🗑️ Excluir</button>
            </div>
        </div>
    </div>
</template>
