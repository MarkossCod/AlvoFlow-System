<script setup>
import { route } from 'ziggy-js';
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { detailStore, fecharEdicao } from '../detailStore';

const pedido = computed(() => detailStore.pedido);

const form = useForm({ quantidade: 1, observacoes: '' });

watch(() => detailStore.editing, (editing) => {
    if (editing && pedido.value) {
        form.quantidade = pedido.value.quantidade;
        form.observacoes = pedido.value.observacoes || '';
    }
});

function salvar() {
    form.patch(route('pedidos.update', pedido.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            pedido.value.quantidade = form.quantidade;
            pedido.value.observacoes = form.observacoes;
            fecharEdicao();
        },
    });
}
</script>

<template>
    <div v-if="detailStore.editing && pedido" id="edit-overlay" class="show" @click="(e) => e.target.id === 'edit-overlay' && fecharEdicao()">
        <div class="detail-box">
            <div class="detail-head">
                <div>
                    <h3 style="margin:0;">Editar pedido</h3>
                    <div class="id">{{ pedido.publicador }} · {{ pedido.codigo }}</div>
                </div>
                <button class="icon-btn" @click="fecharEdicao">✕</button>
            </div>
            <div class="field">
                <label>Quantidade</label>
                <input v-model.number="form.quantidade" type="number" min="1" />
            </div>
            <div class="field">
                <label>Observações</label>
                <textarea v-model="form.observacoes" placeholder="Detalhes adicionais..."></textarea>
            </div>
            <div class="detail-actions">
                <button class="btn btn-outline" @click="fecharEdicao">Cancelar</button>
                <button class="btn btn-primary" style="width:auto;" :disabled="form.processing" @click="salvar">💾 Guardar alterações</button>
            </div>
        </div>
    </div>
</template>
