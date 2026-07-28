<script setup>
import { route } from 'ziggy-js';
import { Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
import PedidoCard from '../Components/PedidoCard.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    stats: Object,
    recentes: Array,
});
</script>

<template>
    <section class="view active">
        <div class="page-head">
            <div><h1>Olá, Publicador 👋</h1><p>Aqui está o resumo do Balcão de Publicações hoje.</p></div>
        </div>

        <div class="stat-grid">
            <div class="card stat">
                <div class="top"><div class="ico">📦</div></div>
                <div class="val">{{ stats.total }}</div>
                <div class="lbl">Total de Pedidos</div>
            </div>
            <div class="card stat">
                <div class="top"><div class="ico">🟡</div></div>
                <div class="val">{{ stats.abertos }}</div>
                <div class="lbl">Pedidos Abertos</div>
            </div>
            <div class="card stat">
                <div class="top"><div class="ico">🟢</div></div>
                <div class="val">{{ stats.concluidos }}</div>
                <div class="lbl">Concluídos</div>
            </div>
            <div class="card stat">
                <div class="top"><div class="ico">📅</div></div>
                <div class="val">{{ stats.hoje }}</div>
                <div class="lbl">Solicitados Hoje</div>
            </div>
        </div>

        <div class="card" style="padding:18px; margin-bottom:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                <h3 style="margin:0; font-size:15px;">Pedidos recentes</h3>
                <Link :href="route('pedidos.visualizar')" style="font-size:12.5px; color:var(--accent); font-weight:600; text-decoration:none;">Ver todos →</Link>
            </div>
            <div class="pedido-grid">
                <PedidoCard v-for="p in recentes" :key="p.id" :pedido="p" />
                <div v-if="!recentes.length" class="empty" style="grid-column:1/-1;"><div class="ico">📭</div><p>Ainda não há pedidos registados.</p></div>
            </div>
        </div>
    </section>
</template>
