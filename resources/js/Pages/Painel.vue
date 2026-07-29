<script setup>
// Dashboard: estatísticas de pedidos, gráficos (Chart.js), exportação em PDF e um resumo
// de monitoramento/conta. "isMarkin" controla o link para a administração de utilizadores.
import { onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Chart from 'chart.js/auto';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';
import { route } from 'ziggy-js';
import AppLayout from '../Layouts/AppLayout.vue';
import StatCard from '../Components/StatCard.vue';
import { ICONS } from '../icons';

defineOptions({ layout: AppLayout });

const props = defineProps({
    porEstado: Object,
    porDia: Object,
    pedidos: Array,
    monitoramento: Object,
});

function formatDateHora(value) {
    if (!value) return '—';
    const d = new Date(value);
    return d.toLocaleDateString('pt-PT') + ' ' + d.toTimeString().slice(0, 5);
}

const stats = computed(() => ({
    total: props.pedidos.length,
    abertos: props.porEstado['Aberto'] || 0,
    andamento: props.porEstado['Em Andamento'] || 0,
    concluidos: props.porEstado['Concluído'] || 0,
}));

const isMarkin = computed(() => usePage().props.auth.isMarkin);

function formatDateBR(iso) {
    const [y, m, d] = (iso || '').split('-');
    return y && m && d ? `${d}/${m}/${y}` : iso || '';
}

onMounted(() => {
    const style = getComputedStyle(document.documentElement);
    const warn = style.getPropertyValue('--warn').trim() || '#b8860b';
    const accent = style.getPropertyValue('--accent').trim() || '#0f6c7c';
    const success = style.getPropertyValue('--success').trim() || '#1e8a5f';

    new Chart(document.getElementById('chart-donut'), {
        type: 'doughnut',
        data: {
            labels: ['Abertos', 'Em Andamento', 'Concluídos'],
            datasets: [{ data: [stats.value.abertos, stats.value.andamento, stats.value.concluidos], backgroundColor: [warn, accent, success], borderWidth: 0 }],
        },
        options: { plugins: { legend: { position: 'bottom' } }, cutout: '68%' },
    });

    new Chart(document.getElementById('chart-dia'), {
        type: 'bar',
        data: {
            labels: Object.keys(props.porDia).map(formatDateBR),
            datasets: [{ label: 'Pedidos', data: Object.values(props.porDia), backgroundColor: accent, borderRadius: 6 }],
        },
        options: { plugins: { legend: { display: false } } },
    });
});

function exportarPDF() {
    const doc = new jsPDF({ unit: 'pt', format: 'a4' });
    const pageW = doc.internal.pageSize.getWidth();

    doc.setFillColor(23, 50, 79);
    doc.rect(0, 0, pageW, 64, 'F');
    doc.setFillColor(201, 162, 75);
    doc.roundedRect(30, 14, 36, 36, 8, 8, 'F');
    doc.setTextColor(23, 50, 79).setFont('helvetica', 'bold').setFontSize(17);
    doc.text('A', 48, 38, { align: 'center' });
    doc.setTextColor(255, 255, 255).setFont('helvetica', 'bold').setFontSize(15);
    doc.text('AlvoFlow', 80, 32);
    doc.setFont('helvetica', 'normal').setFontSize(9).setTextColor(215, 222, 235);
    const now = new Date();
    doc.text('Relatório de Pedidos — Balcão de Publicações', 80, 46);
    doc.setTextColor(220, 225, 235);
    doc.text('Gerado em ' + now.toLocaleDateString('pt-PT') + ' às ' + now.toTimeString().slice(0, 5), pageW - 30, 38, { align: 'right' });

    autoTable(doc, {
        startY: 90,
        head: [['Código', 'Publicador', 'Publicação', 'Quantidade', 'Data', 'Estado']],
        body: props.pedidos.map((p) => [p.codigo, p.publicador, p.publicacao, String(p.quantidade), formatDateBR(p.data), p.estado]),
        headStyles: { fillColor: [23, 50, 79] },
        styles: { fontSize: 9 },
    });

    doc.save('alvoflow_relatorio.pdf');
}
</script>

<template>
    <section class="view active">
        <div class="page-head">
            <div><h1>Painel de Controlo</h1><p>Visão geral dos pedidos do Balcão de Publicações.</p></div>
            <button class="btn btn-primary" style="width:auto; background:linear-gradient(135deg,var(--danger),#8e2a20);" @click="exportarPDF"><span v-html="ICONS.folder"></span> Exportar PDF</button>
        </div>

        <div class="stat-grid">
            <StatCard :href="route('pedidos.visualizar')" :icon="ICONS.folder" :value="stats.total" label="Total de Pedidos" />
            <StatCard :href="route('pedidos.visualizar', { estado: 'Aberto' })" :icon="ICONS.clock" color="warn" :value="stats.abertos" label="Abertos" />
            <StatCard :href="route('pedidos.visualizar', { estado: 'Em Andamento' })" :icon="ICONS.activity" :value="stats.andamento" label="Em Andamento" />
            <StatCard :href="route('pedidos.visualizar', { estado: 'Concluído' })" :icon="ICONS.shieldCheck" color="success" :value="stats.concluidos" label="Concluídos" />
        </div>

        <div class="chart-grid">
            <div class="card chart-card">
                <h4>Distribuição por Estado</h4>
                <p>Proporção de pedidos por situação atual.</p>
                <canvas id="chart-donut" height="220"></canvas>
            </div>
            <div class="card chart-card">
                <h4>Pedidos nos últimos 14 dias</h4>
                <p>Volume diário de pedidos criados.</p>
                <canvas id="chart-dia" height="220"></canvas>
            </div>
        </div>

        <div class="page-head" style="margin:26px 0 14px;"><div><h1 style="font-size:17px;">Monitoramento do Sistema</h1><p>Indicadores gerais, além dos pedidos.</p></div></div>
        <div class="stat-grid">
            <StatCard :href="isMarkin ? route('utilizadores') : ''" :icon="ICONS.users" color="gold" :value="monitoramento.publicadores" label="Publicadores Registados" />
            <StatCard :href="route('sentinela.index')" :icon="ICONS.book" color="warn" :value="monitoramento.sentinelaPendentes" label="Sentinela Pendentes" />
            <StatCard :icon="ICONS.gauge" color="success" :value="monitoramento.taxaConclusao + '%'" label="Taxa de Conclusão" />
            <StatCard :icon="ICONS.server" color="muted" small :value="formatDateHora(monitoramento.ultimaAtualizacao)" label="Última Atividade no Sistema" />
        </div>

        <div class="page-head" style="margin:26px 0 14px;"><div><h1 style="font-size:17px;">A sua conta</h1></div></div>
        <div class="stat-grid">
            <StatCard :icon="ICONS.shieldCheck" color="muted" small :value="monitoramento.nomeUtilizador" label="Nome de Utilizador" />
            <StatCard :icon="ICONS.user" color="muted" small :value="formatDateHora(monitoramento.contaCriadaEm)" label="Conta Criada Em" />
            <StatCard :icon="ICONS.activity" color="muted" small :value="formatDateHora(monitoramento.sessaoDesde)" label="Última Atividade da Sessão" />
        </div>
    </section>
</template>
