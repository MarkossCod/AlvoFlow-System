<script setup>
import { route } from 'ziggy-js';
import { computed, ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ICONS } from '../../icons';

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const strength = computed(() => Math.min(form.password.length / 12, 1));
const strengthColor = computed(() => (strength.value < 0.4 ? 'var(--danger)' : strength.value < 0.75 ? 'var(--warn)' : 'var(--success)'));
const hints = computed(() => ({
    len: form.password.length >= 8,
    upper: /[A-Z]/.test(form.password),
    num: /[0-9]/.test(form.password),
}));

function submit() {
    if (!form.terms) {
        return;
    }
    form.post(route('register'), {
        onSuccess: () => {
            window.dispatchEvent(new CustomEvent('alvoflow:enter', {
                detail: { title: 'Bem-vindo, ' + (form.name.split(' ')[0] || '') + '!' },
            }));
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
                        <rect width="64" height="64" rx="16" fill="#132743"/>
                        <path d="M32 11 L15 53" stroke="#d9b25e" stroke-width="7" stroke-linecap="round" fill="none"/>
                        <path d="M32 11 L49 53" stroke="#d9b25e" stroke-width="7" stroke-linecap="round" fill="none"/>
                        <path d="M21 35 L30 40 L21 45" stroke="#f4f6fa" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                </div>
                <div><b>AlvoFlow</b><span>GESTÃO DE PUBLICAÇÕES</span></div>
            </div>

            <div class="auth-view">
                <h2>Criar conta</h2>
                <p class="sub">Registe-se em poucos segundos para começar a gerir pedidos.</p>

                <form @submit.prevent="submit">
                    <div class="field" :class="{ err: form.errors.name }">
                        <label>Nome completo</label>
                        <div class="input-wrap"><span class="ic" v-html="ICONS.user"></span><input type="text" v-model="form.name" placeholder="O seu nome" /></div>
                        <div class="msg">{{ form.errors.name }}</div>
                    </div>
                    <div class="field" :class="{ err: form.errors.username }">
                        <label>Nome de utilizador</label>
                        <div class="input-wrap"><span class="ic" v-html="ICONS.user"></span><input type="text" v-model="form.username" placeholder="Escolha um nome de utilizador" /></div>
                        <div class="msg">{{ form.errors.username }}</div>
                    </div>
                    <div class="field" :class="{ err: form.errors.email }">
                        <label>Email</label>
                        <div class="input-wrap"><span class="ic" v-html="ICONS.mail"></span><input type="email" v-model="form.email" placeholder="ex: nome@email.com" /></div>
                        <div class="msg">{{ form.errors.email }}</div>
                    </div>

                    <div class="field-divider"></div>

                    <div class="field" :class="{ err: form.errors.password }">
                        <label>Palavra-passe</label>
                        <div class="input-wrap">
                            <span class="ic" v-html="ICONS.lock"></span>
                            <input :type="showPassword ? 'text' : 'password'" v-model="form.password" placeholder="Mínimo 8 caracteres" />
                            <button type="button" class="eye-btn" tabindex="-1" @click="showPassword = !showPassword" v-html="showPassword ? ICONS.eyeOff : ICONS.eye"></button>
                        </div>
                        <div class="strength"><span :style="{ width: (strength * 100) + '%', background: strengthColor }"></span></div>
                        <div class="pass-hints">
                            <span :class="{ ok: hints.len }">8+ caracteres</span>
                            <span :class="{ ok: hints.upper }">1 maiúscula</span>
                            <span :class="{ ok: hints.num }">1 número</span>
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

                    <label class="terms-check">
                        <input type="checkbox" v-model="form.terms" />
                        <span>Concordo com os <a @click.stop>Termos de Utilização</a> e a <a @click.stop>Política de Privacidade</a> do AlvoFlow.</span>
                    </label>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">Criar conta</button>
                </form>
                <p class="auth-foot">Já tem conta? <Link :href="route('login')">Entrar</Link></p>
            </div>
        </div>
    </div>
</template>
