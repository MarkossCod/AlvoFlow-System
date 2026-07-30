<script setup>
// Dashboard: estatísticas de pedidos, gráficos (Chart.js), exportação em PDF e um resumo
// de monitoramento/conta. "isMarkin" controla o link para a administração de utilizadores.
import { onMounted, onUnmounted, computed, ref, watch } from 'vue';
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

// Tipo de gráfico escolhido em cada cartão — "Adicione a opção de visualizar os dados por
// diferentes tipos de gráficos". Trocar o tipo destrói e recria o Chart.js (mais simples e
// robusto do que mutar config.type, que tem opções incompatíveis entre tipos, ex: cutout/scales).
const donutType = ref('doughnut'); // doughnut | pie | bar
const diaType = ref('bar'); // bar | line
let donutChart = null;
let diaChart = null;

// Nunca cacheado: os gráficos são canvas (Chart.js pinta o texto/linhas a partir destas cores,
// não de CSS), por isso têm de ser relidos sempre que renderizam — incluindo depois de mudar de
// tema (ver MutationObserver mais abaixo), senão os eixos ficavam com a cor do tema antigo até
// recarregar a página.
function getCores() {
    const style = getComputedStyle(document.documentElement);
    return {
        warn: style.getPropertyValue('--warn').trim() || '#b8860b',
        accent: style.getPropertyValue('--accent').trim() || '#0f6c7c',
        success: style.getPropertyValue('--success').trim() || '#1e8a5f',
        texto: style.getPropertyValue('--text-muted').trim() || '#666',
        grelha: style.getPropertyValue('--border').trim() || 'rgba(0,0,0,.1)',
    };
}

function renderDonut() {
    const { warn, accent, success, texto, grelha } = getCores();
    donutChart?.destroy();
    donutChart = new Chart(document.getElementById('chart-donut'), {
        type: donutType.value,
        data: {
            labels: ['Abertos', 'Em Andamento', 'Concluídos'],
            datasets: [{
                data: [stats.value.abertos, stats.value.andamento, stats.value.concluidos],
                backgroundColor: [warn, accent, success],
                borderWidth: 0,
                borderRadius: donutType.value === 'bar' ? 6 : 0,
            }],
        },
        // maintainAspectRatio:false -> o tamanho vem só do .chart-canvas-wrap (CSS), nunca da
        // largura do cartão (era isso que inchava o gráfico "rosca" para centenas de pixels).
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: donutType.value !== 'bar', position: 'bottom', labels: { color: texto } } },
            cutout: donutType.value === 'doughnut' ? '68%' : 0,
            // precision:0 -> os pedidos são sempre um número inteiro; sem isto o Chart.js
            // dividia o eixo em passos "bonitos" tipo 0.1/0.2, sem sentido nenhum para uma contagem.
            scales: donutType.value === 'bar' ? {
                y: { beginAtZero: true, ticks: { color: texto, precision: 0 }, grid: { color: grelha } },
                x: { ticks: { color: texto }, grid: { display: false } },
            } : undefined,
        },
    });
}

function renderDia() {
    const { accent, texto, grelha } = getCores();
    diaChart?.destroy();
    diaChart = new Chart(document.getElementById('chart-dia'), {
        type: diaType.value,
        data: {
            labels: Object.keys(props.porDia).map(formatDateBR),
            datasets: [{
                label: 'Pedidos',
                data: Object.values(props.porDia),
                backgroundColor: accent,
                borderColor: accent,
                borderRadius: diaType.value === 'bar' ? 6 : 0,
                tension: diaType.value === 'line' ? 0.35 : 0,
                fill: diaType.value === 'line',
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { color: texto, precision: 0 }, grid: { color: grelha } },
                x: { ticks: { color: texto }, grid: { color: grelha } },
            },
        },
    });
}

onMounted(() => { renderDonut(); renderDia(); });
watch(donutType, renderDonut);
watch(diaType, renderDia);

// Recria os gráficos quando o tema (Claro/Escuro) muda enquanto esta página está aberta —
// sem isto, os eixos/legenda ficavam com a cor do tema anterior até um recarregamento manual.
let temaObserver = new MutationObserver(() => { renderDonut(); renderDia(); });
temaObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
onUnmounted(() => temaObserver.disconnect());

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
                <div class="chart-card-head">
                    <div><h4>Distribuição por Estado</h4><p>Proporção de pedidos por situação atual.</p></div>
                    <div class="chart-type-switch">
                        <button type="button" :class="{ on: donutType === 'doughnut' }" @click="donutType = 'doughnut'">Rosca</button>
                        <button type="button" :class="{ on: donutType === 'pie' }" @click="donutType = 'pie'">Pizza</button>
                        <button type="button" :class="{ on: donutType === 'bar' }" @click="donutType = 'bar'">Barras</button>
                    </div>
                </div>
                <div class="chart-canvas-wrap"><canvas id="chart-donut"></canvas></div>
            </div>
            <div class="card chart-card">
                <div class="chart-card-head">
                    <div><h4>Pedidos nos últimos 14 dias</h4><p>Volume diário de pedidos criados.</p></div>
                    <div class="chart-type-switch">
                        <button type="button" :class="{ on: diaType === 'bar' }" @click="diaType = 'bar'">Barras</button>
                        <button type="button" :class="{ on: diaType === 'line' }" @click="diaType = 'line'">Linha</button>
                    </div>
                </div>
                <div class="chart-canvas-wrap"><canvas id="chart-dia"></canvas></div>
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
