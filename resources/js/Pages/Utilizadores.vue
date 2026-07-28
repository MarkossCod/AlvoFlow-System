<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import { ICONS } from '../icons';

defineOptions({ layout: AppLayout });

defineProps({
    utilizadores: Array,
});

function formatDateHora(value) {
    if (!value) return '—';
    const d = new Date(value);
    return d.toLocaleDateString('pt-PT') + ' ' + d.toTimeString().slice(0, 5);
}
</script>

<template>
    <section class="view active">
        <div class="page-head">
            <div><h1>Utilizadores Registados</h1><p>Contas com acesso ao AlvoFlow — {{ utilizadores.length }} no total.</p></div>
        </div>

        <div class="user-list">
            <div class="card user-row" v-for="u in utilizadores" :key="u.id">
                <div class="user-avatar">{{ u.username.charAt(0).toUpperCase() }}</div>
                <div class="who">
                    <b>{{ u.username }}</b>
                    <span>{{ u.email || 'sem email associado' }}</span>
                </div>
                <div class="since">Registado em<br>{{ formatDateHora(u.created_at) }}</div>
            </div>
            <div v-if="!utilizadores.length" class="empty"><div class="ico" v-html="ICONS.users"></div><p>Ainda não há utilizadores registados.</p></div>
        </div>
    </section>
</template>
