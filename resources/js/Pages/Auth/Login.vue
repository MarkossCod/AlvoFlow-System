<script setup>
import { route } from 'ziggy-js';
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ICONS } from '../../icons';

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

function submit() {
    form.post(route('login'), {
        preserveScroll: true,
        onSuccess: () => {
            window.dispatchEvent(new CustomEvent('alvoflow:enter', { detail: { title: 'Acesso confirmado' } }));
        },
    });
}
</script>

<template>
    <div class="auth-screen">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="mark">
                    <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="lgBgB" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#17324f"/><stop offset="1" stop-color="#0d1b2e"/></linearGradient>
                            <linearGradient id="lgGoldB" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#e0bf76"/><stop offset="1" stop-color="#c9a24b"/></linearGradient>
                        </defs>
                        <rect width="64" height="64" rx="16" fill="url(#lgBgB)"/>
                        <path d="M32 11 L15 53" stroke="url(#lgGoldB)" stroke-width="7" stroke-linecap="round" fill="none"/>
                        <path d="M32 11 L49 53" stroke="url(#lgGoldB)" stroke-width="7" stroke-linecap="round" fill="none"/>
                        <path d="M21 35 L30 40 L21 45" stroke="#f4f6fa" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                </div>
                <div><b>AlvoFlow</b><span>GESTÃO DE PUBLICAÇÕES</span></div>
            </div>

            <div class="auth-view">
                <h2>Entrar</h2>
                <p class="sub">Aceda à sua conta para gerir os pedidos.</p>

                <form @submit.prevent="submit">
                    <div class="field" :class="{ err: form.errors.username }">
                        <label>Nome de utilizador</label>
                        <div class="input-wrap"><span class="ic" v-html="ICONS.user"></span><input type="text" v-model="form.username" placeholder="o seu nome de utilizador" autofocus /></div>
                        <div class="msg">{{ form.errors.username }}</div>
                    </div>
                    <div class="field" :class="{ err: form.errors.password }">
                        <label>Palavra-passe</label>
                        <div class="input-wrap">
                            <span class="ic" v-html="ICONS.lock"></span>
                            <input :type="showPassword ? 'text' : 'password'" v-model="form.password" placeholder="••••••••" />
                            <button type="button" class="eye-btn" tabindex="-1" @click="showPassword = !showPassword" v-html="showPassword ? ICONS.eyeOff : ICONS.eye"></button>
                        </div>
                        <div class="msg">{{ form.errors.password }}</div>
                    </div>
                    <div class="auth-switch">
                        <label style="font-size:12.5px; color:var(--text-muted); display:flex; gap:6px; align-items:center;">
                            <input type="checkbox" v-model="form.remember" style="width:auto;" /> Manter sessão
                        </label>
                        <Link :href="route('password.request')" style="font-size:12.5px; color:var(--text-muted);">Esqueceu a palavra-passe?</Link>
                    </div>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">Entrar</button>
                </form>
                <p class="auth-foot">Ainda não tem conta? <Link :href="route('register')">Criar conta</Link></p>
            </div>
        </div>
    </div>
</template>
