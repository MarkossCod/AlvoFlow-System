<script setup>
import { route } from 'ziggy-js';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    lista: Array,
    edicoes: Array,
    edicao: String,
    status: String,
    totais: Object,
});

const NOMES = ['Ana Ferreira', 'João Silva', 'Maria Costa', 'Pedro Santos', 'Rita Almeida', 'Carlos Pinto'];
const STATUS_OPTIONS = [
    ['todos', 'Todos'],
    ['Pendente', '⏳ Pendente'],
    ['Entregue', '✅ Entregue'],
];

const form = useForm({
    edicao: '',
    publicador: '',
    tamanho: 'Letra Grande',
    quantidade: 1,
});

function adicionar() {
    form.post(route('sentinela.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('edicao', 'publicador'),
    });
}

function filtrar(campo, valor) {
    router.get(route('sentinela.index'), { edicao: props.edicao, status: props.status, [campo]: valor }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function alternarStatus(s) {
    const novo = s.status === 'Entregue' ? 'Pendente' : 'Entregue';
    router.patch(route('sentinela.update', s.id), { status: novo }, { preserveScroll: true, preserveState: true });
}

function excluir(s) {
    if (!confirm('Excluir este registo de Sentinela?')) return;
    router.delete(route('sentinela.destroy', s.id), { preserveScroll: true });
}
</script>

<template>
    <section class="view active">
        <div class="page-head">
            <div><h1>Pedidos de Sentinela</h1><p>Lista dinâmica de pedidos da revista A Sentinela por publicador, tamanho e status de entrega.</p></div>
            <div style="display:flex; gap:22px;">
                <div class="stat" style="padding:0;"><div class="val" style="font-size:20px;">{{ totais.total }}</div><div class="lbl">Exemplares</div></div>
                <div class="stat" style="padding:0;"><div class="val" style="font-size:20px; color:var(--success);">{{ totais.entregues }}</div><div class="lbl">Entregues</div></div>
                <div class="stat" style="padding:0;"><div class="val" style="font-size:20px; color:var(--warn);">{{ totais.pendentes }}</div><div class="lbl">Pendentes</div></div>
            </div>
        </div>

        <div class="card" style="padding:18px; margin-bottom:20px;">
            <form @submit.prevent="adicionar">
                <div class="form-grid">
                    <div class="field">
                        <label>Edição da Sentinela</label>
                        <input v-model="form.edicao" type="text" placeholder="Ex: Sentinela — Janeiro 2026" required />
                    </div>
                    <div class="field">
                        <label>Publicador</label>
                        <input v-model="form.publicador" type="text" placeholder="Nome completo" list="pub-nomes" required />
                        <datalist id="pub-nomes"><option v-for="n in NOMES" :key="n">{{ n }}</option></datalist>
                    </div>
                    <div class="field">
                        <label>Tamanho</label>
                        <select v-model="form.tamanho">
                            <option value="Letra Grande">Letra Grande</option>
                            <option value="Letra Pequena">Letra Pequena</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Quantidade</label>
                        <input v-model.number="form.quantidade" type="number" min="1" required />
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:auto; margin-top:6px;" :disabled="form.processing">＋ Adicionar à lista</button>
            </form>
        </div>

        <p style="font-size:11.5px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin:0 0 8px;">Filtrar por edição da Sentinela</p>
        <select class="field" style="width:100%; max-width:420px; margin-bottom:16px; padding:11px 14px; border-radius:var(--radius-sm); border:1.5px solid var(--border); background:var(--surface-2); color:var(--text); font-size:14.5px;" :value="edicao" @change="filtrar('edicao', $event.target.value)">
            <option value="todos">Todas as edições</option>
            <option v-for="e in edicoes" :key="e" :value="e">{{ e }}</option>
        </select>

        <p style="font-size:11.5px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin:0 0 8px;">Filtrar por status de entrega</p>
        <div class="filter-row">
            <button v-for="[v, l] in STATUS_OPTIONS" :key="v" class="chip" :class="{ active: status === v }" @click="filtrar('status', v)">{{ l }}</button>
        </div>

        <div class="card sheet-card">
            <div class="sheet-scroll">
                <table class="sheet-table">
                    <thead>
                        <tr><th>#</th><th>Sentinela</th><th>Publicador</th><th>Tamanho</th><th>Qtd.</th><th>Status de Entrega</th><th>⋯</th></tr>
                    </thead>
                    <tbody>
                        <tr v-for="(s, i) in lista" :key="s.id">
                            <td class="rownum">{{ i + 1 }}</td>
                            <td>📖 {{ s.edicao }}</td>
                            <td>{{ s.publicador }}</td>
                            <td><span class="tam-badge" :class="s.tamanho === 'Letra Grande' ? 'grande' : 'pequena'">{{ s.tamanho === 'Letra Grande' ? '🔠' : '🔡' }} {{ s.tamanho }}</span></td>
                            <td class="qtd-cell">{{ s.quantidade }}</td>
                            <td><button class="status-pill" :class="s.status === 'Entregue' ? 'entregue' : 'pendente'" title="Clique para alternar" @click="alternarStatus(s)">{{ s.status === 'Entregue' ? '✅ Entregue' : '⏳ Pendente' }}</button></td>
                            <td><button class="del-cell" title="Excluir" @click="excluir(s)">🗑️</button></td>
                        </tr>
                        <tr v-if="!lista.length"><td colspan="7"><div class="empty"><div class="ico">📭</div><p>Nenhum pedido de Sentinela encontrado para este filtro.</p></div></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="sheet-foot">
                <span>{{ lista.length }} {{ lista.length === 1 ? 'registo' : 'registos' }}</span>
                <span>{{ lista.reduce((a, s) => a + s.quantidade, 0) }} exemplares no total</span>
            </div>
        </div>
    </section>
</template>
