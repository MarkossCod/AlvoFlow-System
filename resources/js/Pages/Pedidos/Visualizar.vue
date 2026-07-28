<script setup>
import { route } from 'ziggy-js';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import PedidoCard from '../../Components/PedidoCard.vue';
import { ICONS } from '../../icons';

defineOptions({ layout: AppLayout });

const props = defineProps({
    pedidos: Array,
    estado: String,
});

const FILTERS = ['todos', 'Aberto', 'Em Andamento', 'Concluído'];
const modo = ref('galeria');

function setFiltro(f) {
    router.get(route('pedidos.visualizar'), { estado: f }, { preserveState: true, preserveScroll: true, replace: true });
}

/* ============ CALENDÁRIO ============ */
const DOWS = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
const calendario = computed(() => {
    const now = new Date();
    const y = now.getFullYear();
    const m = now.getMonth();
    const first = new Date(y, m, 1);
    const startDow = first.getDay();
    const daysInMonth = new Date(y, m + 1, 0).getDate();
    const dias = [];
    for (let i = 0; i < startDow; i++) dias.push(null);
    for (let d = 1; d <= daysInMonth; d++) {
        const dateStr = `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        const items = props.pedidos.filter((p) => p.data === dateStr);
        dias.push({ dia: d, items });
    }
    return dias;
});

/* ============ EXPORTAR .ICS ============ */
function icsDateRange(dataStr) {
    const d = dataStr.replace(/-/g, '');
    const next = new Date(dataStr + 'T00:00:00');
    next.setDate(next.getDate() + 1);
    const d2 = next.toISOString().slice(0, 10).replace(/-/g, '');
    return [d, d2];
}
function pedidoEventDetails(p) {
    let details = `Publicador: ${p.publicador}\nPublicação: ${p.publicacao}\nQuantidade: ${p.quantidade}\nEstado: ${p.estado}\nCódigo: ${p.codigo}`;
    if (p.observacoes) details += `\nObservações: ${p.observacoes}`;
    return details;
}
function exportarICS() {
    const list = props.pedidos;
    if (!list.length) return;
    const pad = (n) => String(n).padStart(2, '0');
    const now = new Date();
    const stamp = `${now.getFullYear()}${pad(now.getMonth() + 1)}${pad(now.getDate())}T${pad(now.getHours())}${pad(now.getMinutes())}${pad(now.getSeconds())}Z`;
    const escapeICS = (s) => String(s).replace(/\\/g, '\\\\').replace(/,/g, '\\,').replace(/;/g, '\\;').replace(/\n/g, '\\n');
    let ics = 'BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//AlvoFlow//Pedidos//PT\r\nCALSCALE:GREGORIAN\r\n';
    list.forEach((p) => {
        const [d1, d2] = icsDateRange(p.data);
        ics += 'BEGIN:VEVENT\r\n';
        ics += `UID:${p.codigo}@alvoflow\r\n`;
        ics += `DTSTAMP:${stamp}\r\n`;
        ics += `DTSTART;VALUE=DATE:${d1}\r\n`;
        ics += `DTEND;VALUE=DATE:${d2}\r\n`;
        ics += `SUMMARY:${escapeICS('Entrega — ' + p.publicacao + ' (' + p.publicador + ')')}\r\n`;
        ics += `DESCRIPTION:${escapeICS(pedidoEventDetails(p))}\r\n`;
        ics += 'END:VEVENT\r\n';
    });
    ics += 'END:VCALENDAR\r\n';
    const blob = new Blob([ics], { type: 'text/calendar;charset=utf-8' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'alvoflow_pedidos.ics';
    a.click();
}
</script>

<template>
    <section class="view active">
        <div class="page-head">
            <div><h1>Visualizar Pedidos</h1><p>Vitrine em cards ou vista de calendário. Exporte para o Google Agenda quando quiser.</p></div>
            <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                <button v-if="modo === 'calendario'" class="btn btn-outline" title="Descarrega um ficheiro .ics para importar no Google Agenda" @click="exportarICS">📆 Exportar p/ Google Agenda</button>
                <div class="seg">
                    <button :class="{ active: modo === 'galeria' }" @click="modo = 'galeria'"><span class="seg-ico" v-html="ICONS.grid"></span> Galeria</button>
                    <button :class="{ active: modo === 'calendario' }" @click="modo = 'calendario'"><span class="seg-ico" v-html="ICONS.calendar"></span> Calendário</button>
                </div>
            </div>
        </div>

        <div class="filter-row">
            <button v-for="f in FILTERS" :key="f" class="chip" :class="{ active: estado === f || (estado === undefined && f === 'todos') }" @click="setFiltro(f)">{{ f === 'todos' ? 'Todos' : f }}</button>
        </div>

        <div v-if="modo === 'galeria'" class="pedido-grid">
            <PedidoCard v-for="p in pedidos" :key="p.id" :pedido="p" />
            <div v-if="!pedidos.length" class="empty" style="grid-column:1/-1;"><div class="ico">📭</div><p>Nenhum pedido para este filtro.</p></div>
        </div>
        <div v-else>
            <div class="cal-grid">
                <div v-for="d in DOWS" :key="d" class="cal-dow">{{ d }}</div>
            </div>
            <div class="cal-grid" style="margin-top:6px;">
                <div v-for="(dia, i) in calendario" :key="i" class="cal-day" :style="{ visibility: dia ? 'visible' : 'hidden' }">
                    <template v-if="dia">
                        <div class="num">{{ dia.dia }}</div>
                        <span v-for="p in dia.items.slice(0, 2)" :key="p.id" class="pill" :title="`${p.publicador} — ${p.publicacao}`">{{ p.publicador.split(' ')[0] }}: {{ p.publicacao }}</span>
                        <span v-if="dia.items.length > 2" class="pill">+{{ dia.items.length - 2 }} mais</span>
                    </template>
                </div>
            </div>
        </div>
    </section>
</template>
