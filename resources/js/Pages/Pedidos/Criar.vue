<script setup>
import { route } from 'ziggy-js';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import DatePicker from '../../Components/DatePicker.vue';

defineOptions({ layout: AppLayout });

// O campo NÃO se pode chamar "data": o objeto que o useForm() devolve já tem um método
// interno chamado data() (usado por dentro para serializar o formulário no submit); um campo
// com esse nome era silenciosamente substituído por essa função, e o v-model do DatePicker
// ficava a apontar para uma função em vez de uma string — daí o campo aparecer em branco.
const form = useForm({
    publicador: '',
    publicacao: '',
    quantidade: 1,
    data_solicitacao: new Date().toISOString().slice(0, 10),
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
                        <DatePicker v-model="form.data_solicitacao" />
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
