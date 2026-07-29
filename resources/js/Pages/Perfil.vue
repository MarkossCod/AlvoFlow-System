<script setup>
import { usePage } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const user = usePage().props.auth.user;

function initials(name) {
    return (name || '').split(' ').filter(Boolean).map((w) => w[0]).slice(0, 2).join('').toUpperCase();
}
</script>

<template>
    <section class="view active">
        <div class="page-head"><div><h1>Configurações de Perfil</h1><p>Gerencie os seus dados pessoais, segurança e preferências.</p></div></div>

        <div class="profile-layout">
            <div class="card profile-card">
                <div class="profile-avatar-wrap">
                    <div class="profile-avatar">{{ initials(user.name) }}</div>
                    <button class="avatar-edit" title="Alterar foto">📷</button>
                </div>
                <h3>{{ user.name }}</h3>
                <p class="prof-role">Publicador · Balcão de Publicações</p>
            </div>

            <div class="profile-forms">
                <div class="card profile-section">
                    <h4 class="section-title">👤 Dados Pessoais</h4>
                    <div class="form-grid">
                        <div class="field"><label>Nome completo</label><input type="text" :value="user.name" /></div>
                        <div class="field"><label>Email</label><input type="email" :value="user.email" /></div>
                        <div class="field"><label>Congregação</label><input type="text" placeholder="Congregação" /></div>
                        <div class="field"><label>Telefone</label><input type="tel" placeholder="+351 912 345 678" /></div>
                    </div>
                </div>

                <div class="card profile-section">
                    <h4 class="section-title">🔒 Segurança</h4>
                    <div class="form-grid">
                        <div class="field"><label>Nova palavra-passe</label><input type="password" placeholder="••••••••" /></div>
                        <div class="field"><label>Confirmar palavra-passe</label><input type="password" placeholder="••••••••" /></div>
                    </div>
                </div>

                <div class="profile-actions">
                    <button class="btn btn-primary">💾 Guardar alterações</button>
                </div>
            </div>
        </div>
    </section>
</template>
