<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import { ICONS } from '../icons';

defineOptions({ layout: AppLayout });

const funcionalidades = [
    { icon: ICONS.plus, titulo: 'Criação de Pedidos', desc: 'Registo rápido de pedidos de publicações com quantidade e observações.' },
    { icon: ICONS.search, titulo: 'Pesquisa em Tempo Real', desc: 'Filtre pedidos por publicador, publicação, data, estado ou código.' },
    { icon: ICONS.book, titulo: 'Pedidos de Sentinela', desc: 'Lista dinâmica de pedidos por edição, com filtro por status.' },
    { icon: ICONS.folder, titulo: 'Visualização e Calendário', desc: 'Galeria de pedidos e calendário mensal, com exportação .ics.' },
    { icon: ICONS.chart, titulo: 'Painel de Controlo', desc: 'Estatísticas e gráficos em tempo real, com exportação em PDF.' },
    { icon: ICONS.shieldCheck, titulo: 'Autenticação Própria', desc: 'Login por nome de utilizador com recuperação de senha e de utilizador por email.' },
];

const processo = [
    { titulo: 'Levantamento', desc: 'Definição dos ecrãs e fluxos junto do Balcão de Publicações.' },
    { titulo: 'Protótipo', desc: 'Interface validada em HTML/CSS/JS antes da implementação real.' },
    { titulo: 'Migração', desc: 'Reconstrução em Laravel + Inertia (Vue) com base de dados MySQL.' },
    { titulo: 'Produção', desc: 'Deploy no Render com base de dados MySQL gerida na Aiven.' },
];
</script>

<template>
    <section class="view active">
        <div class="page-head"><div><h1>Sobre o Sistema</h1><p>Informações institucionais e processo de desenvolvimento do AlvoFlow.</p></div></div>

        <div class="card sobre-hero" style="margin-bottom:18px;">
            <div class="sobre-hero-badge">
                <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="lgBgSobre" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#17324f"/><stop offset="1" stop-color="#0d1b2e"/></linearGradient>
                        <linearGradient id="lgGoldSobre" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#e0bf76"/><stop offset="1" stop-color="#c9a24b"/></linearGradient>
                    </defs>
                    <rect width="64" height="64" rx="16" fill="url(#lgBgSobre)"/>
                    <path d="M32 11 L15 53" stroke="url(#lgGoldSobre)" stroke-width="7" stroke-linecap="round" fill="none"/>
                    <path d="M32 11 L49 53" stroke="url(#lgGoldSobre)" stroke-width="7" stroke-linecap="round" fill="none"/>
                    <path d="M21 35 L30 40 L21 45" stroke="#f4f6fa" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
            </div>
            <div class="sobre-hero-body">
                <h2>AlvoFlow</h2>
                <p class="sobre-version">Versão 1.0 — Balcão de Publicações</p>
                <p class="sobre-desc">
                    O AlvoFlow é um sistema criado para organizar pedidos de publicações, pedidos de Sentinela e o
                    acompanhamento de entregas do Balcão de Publicações de forma simples, rápida e profissional —
                    substituindo o controlo manual em papel por um fluxo digital acessível a toda a equipa.
                </p>
            </div>
        </div>

        <div class="page-head" style="margin-bottom:14px;"><div><h1 style="font-size:17px;">Funcionalidades</h1></div></div>
        <div class="feature-grid">
            <div class="card info-tile" v-for="f in funcionalidades" :key="f.titulo">
                <b><span v-html="f.icon" style="width:16px;height:16px;display:inline-flex;color:var(--accent);"></span> {{ f.titulo }}</b>
                <p>{{ f.desc }}</p>
            </div>
        </div>

        <div class="page-head" style="margin-bottom:14px;"><div><h1 style="font-size:17px;">Processo de Desenvolvimento</h1></div></div>
        <div class="process-timeline">
            <div class="process-step" v-for="(p, i) in processo" :key="p.titulo">
                <span class="step-num">0{{ i + 1 }}</span>
                <div class="step-ico">{{ i + 1 }}</div>
                <b>{{ p.titulo }}</b>
                <p>{{ p.desc }}</p>
            </div>
        </div>

        <div class="page-head" style="margin:26px 0 14px;"><div><h1 style="font-size:17px;">Acesso em Qualquer Ecrã</h1><p>Interface responsiva, do computador ao telemóvel.</p></div></div>
        <div class="card mockup-card">
            <div class="mockup-gallery">
                <div>
                    <div class="laptop-mockup">
                        <div class="screen">
                            <div class="mock-row"><span class="mock-dot"></span><span class="mock-dot"></span><span class="mock-dot"></span><div class="mock-bar gold"></div></div>
                            <div class="mock-row"><div class="mock-bar"></div></div>
                            <div class="mock-row"><div class="mock-bar"></div></div>
                            <div class="mock-card-row">
                                <div class="mock-card"></div>
                                <div class="mock-card"></div>
                                <div class="mock-card"></div>
                            </div>
                        </div>
                        <div class="base"></div>
                    </div>
                    <div class="mockup-caption">Computador</div>
                </div>
                <div>
                    <div class="phone-mockup">
                        <div class="screen">
                            <div class="notch"></div>
                            <div class="p-row"></div>
                            <div class="p-card"></div>
                            <div class="p-card"></div>
                            <div class="p-card"></div>
                        </div>
                    </div>
                    <div class="mockup-caption">Telemóvel</div>
                </div>
            </div>
        </div>
    </section>
</template>
