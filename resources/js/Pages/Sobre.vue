<script setup>
import AppLayout from '../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
</script>

<template>
    <section class="view active">
        <div class="page-head"><div><h1>Sobre o Sistema</h1><p>Informações institucionais e processo de desenvolvimento do AlvoFlow.</p></div></div>

        <div class="card" style="padding:24px; margin-bottom:18px;">
            <p>O AlvoFlow é um sistema para o Balcão de Publicações, criado para organizar pedidos de publicações, pedidos de Sentinela e o acompanhamento de entregas de forma simples e profissional.</p>
        </div>

        <div class="card" style="padding:24px;">
            <b>⚙️ Funcionalidades</b>
            <p>Criação e gestão de pedidos, pesquisa em tempo real, painel de controlo e relatórios em PDF, exportação para o Google Agenda e lista dinâmica de pedidos de Sentinela.</p>
        </div>
    </section>
</template>
