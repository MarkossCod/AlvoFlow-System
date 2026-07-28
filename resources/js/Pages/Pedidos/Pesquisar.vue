<script setup>
import { route } from 'ziggy-js';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import PedidoCard from '../../Components/PedidoCard.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    resultados: Array,
    q: String,
    tipo: String,
});

const SEARCH_TYPES = [
    ['todos', 'Todos os campos'],
    ['publicador', 'Publicador'],
    ['publicacao', 'Publicação'],
    ['data', 'Data'],
    ['estado', 'Estado'],
    ['id', 'Código'],
];

const query = ref(props.q || '');
const tipo = ref(props.tipo || 'todos');
let timer = null;

function buscar() {
    router.get(route('pedidos.pesquisar'), { q: query.value, tipo: tipo.value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

watch(query, () => {
    clearTimeout(timer);
    timer = setTimeout(buscar, 250);
});

function setTipo(t) {
    tipo.value = t;
    buscar();
}
</script>

<template>
    <section class="view active">
        <div class="page-head"><div><h1>Pesquisar Pedido</h1><p>Pesquisa instantânea por publicador, publicação, data, estado ou código.</p></div></div>

        <div class="search-wrap">
            <span class="sico">🔍</span>
            <input v-model="query" type="text" placeholder="Pesquisar..." />
            <span class="clr" :style="{ display: query ? 'block' : 'none' }" @click="query = ''">✕</span>
        </div>

        <div class="filter-row">
            <button v-for="[v, l] in SEARCH_TYPES" :key="v" class="chip" :class="{ active: tipo === v }" @click="setTipo(v)">{{ l }}</button>
        </div>

        <div class="pedido-grid">
            <template v-if="query">
                <PedidoCard v-for="p in resultados" :key="p.id" :pedido="p" />
                <div v-if="!resultados.length" class="empty" style="grid-column:1/-1;"><div class="ico">📭</div><p>Nenhum pedido encontrado para "{{ query }}".</p></div>
            </template>
            <div v-else class="empty" style="grid-column:1/-1;"><div class="ico">📭</div><p>Comece a escrever para pesquisar pedidos.</p></div>
        </div>
    </section>
</template>
