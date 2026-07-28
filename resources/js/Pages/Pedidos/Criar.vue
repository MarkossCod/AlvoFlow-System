<script setup>
import { route } from 'ziggy-js';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const form = useForm({
    publicador: '',
    publicacao: '',
    quantidade: 1,
    data: new Date().toISOString().slice(0, 10),
    observacoes: '',
});

function submit() {
    form.post(route('pedidos.store'), {
        onSuccess: () => form.reset('publicador', 'publicacao', 'observacoes'),
    });
}
</script>

<template>
    <section class="view active">
        <div class="page-head"><div><h1>Criar Pedido</h1><p>Registe um novo pedido de publicação.</p></div></div>
        <div class="card" style="padding:22px; margin-bottom:24px;">
            <form @submit.prevent="submit">
                <div class="form-grid">
                    <div class="field">
                        <label>Nome do Publicador</label>
                        <input v-model="form.publicador" type="text" placeholder="Nome completo" required />
                    </div>
                    <div class="field">
                        <label>Publicação Solicitada</label>
                        <input v-model="form.publicacao" type="text" placeholder="Ex: Sentinela, Despertai, Bíblia..." list="pub-list" required />
                        <datalist id="pub-list">
                            <option>A Sentinela</option>
                            <option>Despertai!</option>
                            <option>Bíblia — Tradução do Novo Mundo</option>
                            <option>Ame as Pessoas</option>
                            <option>Ensina</option>
                            <option>Cartão de Visita</option>
                        </datalist>
                    </div>
                    <div class="field">
                        <label>Quantidade</label>
                        <input v-model.number="form.quantidade" type="number" min="1" required />
                    </div>
                    <div class="field">
                        <label>Data da Solicitação</label>
                        <input v-model="form.data" type="date" required />
                    </div>
                    <div class="field full">
                        <label>Observações (opcional)</label>
                        <textarea v-model="form.observacoes" placeholder="Detalhes adicionais..."></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:auto; margin-top:6px;" :disabled="form.processing">＋ Criar pedido</button>
            </form>
        </div>
    </section>
</template>
