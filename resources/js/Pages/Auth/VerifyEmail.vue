<script setup>
import { route } from 'ziggy-js';
import { computed } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { ICONS } from '../../icons';

const page = usePage();
const status = computed(() => page.props.flash?.success);

const form = useForm({});

function reenviar() {
    form.post(route('verification.send'));
}

function sair() {
    router.post(route('logout'));
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
                <div class="auth-icon-badge" v-html="ICONS.mail"></div>
                <h2>Confirme o seu email</h2>
                <p class="sub">
                    Enviámos um link de confirmação para o seu email. Clique nele para ativar a sua conta.
                    Não recebeu? Pode pedir um novo abaixo.
                </p>

                <div v-if="status" class="pass-hints" style="margin-bottom:16px;">
                    <span class="ok">{{ status }}</span>
                </div>

                <button type="button" class="btn btn-primary" :disabled="form.processing" @click="reenviar">
                    <span v-html="ICONS.mail"></span> Reenviar email de confirmação
                </button>
                <p class="auth-foot"><a @click.stop="sair" style="cursor:pointer;">Terminar sessão</a></p>
            </div>
        </div>
    </div>
</template>
