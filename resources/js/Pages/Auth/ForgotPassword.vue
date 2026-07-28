<script setup>
import { route } from 'ziggy-js';
import { useForm, Link } from '@inertiajs/vue3';
import { ICONS } from '../../icons';

defineProps({ status: String });

const form = useForm({ email: '' });

function submit() {
    form.post(route('password.email'));
}
</script>

<template>
    <div class="auth-screen">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="mark">
                    <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                        <rect width="64" height="64" rx="16" fill="#132743"/>
                        <path d="M32 11 L15 53" stroke="#d9b25e" stroke-width="7" stroke-linecap="round" fill="none"/>
                        <path d="M32 11 L49 53" stroke="#d9b25e" stroke-width="7" stroke-linecap="round" fill="none"/>
                        <path d="M21 35 L30 40 L21 45" stroke="#f4f6fa" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                </div>
                <div><b>AlvoFlow</b><span>GESTÃO DE PUBLICAÇÕES</span></div>
            </div>

            <div class="auth-view">
                <h2>Recuperar palavra-passe</h2>
                <p class="sub">Indique o seu email e enviaremos um link para redefinir a palavra-passe.</p>

                <div v-if="status" class="msg" style="color:var(--success); margin-bottom:14px;">{{ status }}</div>

                <form @submit.prevent="submit">
                    <div class="field" :class="{ err: form.errors.email }">
                        <label>Email</label>
                        <div class="input-wrap"><span class="ic" v-html="ICONS.mail"></span><input type="email" v-model="form.email" placeholder="ex: nome@email.com" autofocus /></div>
                        <div class="msg">{{ form.errors.email }}</div>
                    </div>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">Enviar link de recuperação</button>
                </form>
                <p class="auth-foot"><Link :href="route('login')">Voltar ao login</Link></p>
            </div>
        </div>
    </div>
</template>
