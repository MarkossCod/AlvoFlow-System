<script setup>
// Card de estatística reutilizável (ícone + valor + rótulo), usado no Painel e na lista de
// Utilizadores. Se "href" for passado, o card vira um link (Inertia) clicável; senão é só um card.
import { Link } from '@inertiajs/vue3';

defineProps({
    icon: { type: String, required: true },
    value: { type: [String, Number], required: true },
    label: { type: String, required: true },
    color: { type: String, default: '' },
    href: { type: String, default: '' },
    // Valores de texto mais longos (datas, nomes) usam letra menor que os números curtos.
    small: { type: Boolean, default: false },
});
</script>

<template>
    <component :is="href ? Link : 'div'" :href="href || undefined" class="card stat">
        <div class="top"><div class="ico" :class="color" v-html="icon"></div></div>
        <div class="val" :style="small ? 'font-size:15px' : ''">{{ value }}</div>
        <div class="lbl">{{ label }}</div>
    </component>
</template>
