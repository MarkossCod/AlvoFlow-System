<script setup>
import { route } from 'ziggy-js';
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ICONS } from '../../icons';

const props = defineProps({
    email: String,
    token: String,
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const form = useForm({
    token: props.token,
    email: props.email ?? '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('password.update'));
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
                <h2>Redefinir palavra-passe</h2>
                <p class="sub">Escolha uma nova palavra-passe para a sua conta.</p>

                <form @submit.prevent="submit">
                    <div class="field" :class="{ err: form.errors.email }">
                        <label>Email</label>
                        <div class="input-wrap"><span class="ic" v-html="ICONS.mail"></span><input type="email" v-model="form.email" placeholder="ex: nome@email.com" /></div>
                        <div class="msg">{{ form.errors.email }}</div>
                    </div>
                    <div class="field" :class="{ err: form.errors.password }">
                        <label>Nova palavra-passe</label>
                        <div class="input-wrap">
                            <span class="ic" v-html="ICONS.lock"></span>
                            <input :type="showPassword ? 'text' : 'password'" v-model="form.password" placeholder="Mínimo 8 caracteres" autofocus />
                            <button type="button" class="eye-btn" tabindex="-1" @click="showPassword = !showPassword" v-html="showPassword ? ICONS.eyeOff : ICONS.eye"></button>
                        </div>
                        <div class="msg">{{ form.errors.password }}</div>
                    </div>
                    <div class="field">
                        <label>Confirmar palavra-passe</label>
                        <div class="input-wrap">
                            <span class="ic" v-html="ICONS.lock"></span>
                            <input :type="showPasswordConfirmation ? 'text' : 'password'" v-model="form.password_confirmation" placeholder="Repita a palavra-passe" />
                            <button type="button" class="eye-btn" tabindex="-1" @click="showPasswordConfirmation = !showPasswordConfirmation" v-html="showPasswordConfirmation ? ICONS.eyeOff : ICONS.eye"></button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">Redefinir palavra-passe</button>
                </form>
                <p class="auth-foot"><Link :href="route('login')">Voltar ao login</Link></p>
            </div>
        </div>
    </div>
</template>
