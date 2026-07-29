<script setup>
// Ecrã de administração de utilizadores — só acessível pela conta "markin" (ver
// middleware EnsureIsMarkin em routes/web.php). Permite ver quem está registado, o que
// cada um andou a fazer por último (last_seen_at/last_url, gravados pelo middleware
// TrackUserActivity em todas as requisições autenticadas), editar dados de outras contas
// e excluí-las.
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '../../../Layouts/AppLayout.vue';
import { ICONS } from '../../../icons';

defineOptions({ layout: AppLayout });

defineProps({
    utilizadores: Array,
});

function formatDateHora(value) {
    if (!value) return 'Nunca';
    const d = new Date(value);
    return d.toLocaleDateString('pt-PT') + ' ' + d.toTimeString().slice(0, 5);
}

// "Online agora" = teve atividade nos últimos 5 minutos.
function estaOnline(lastSeenAt) {
    return !!lastSeenAt && (Date.now() - new Date(lastSeenAt).getTime()) < 5 * 60 * 1000;
}

const editandoId = ref(null);
const forms = {};

function abrirEdicao(u) {
    forms[u.id] = useForm({ username: u.username, email: u.email || '', password: '', password_confirmation: '' });
    editandoId.value = u.id;
}
function cancelarEdicao() {
    editandoId.value = null;
}
function guardar(u) {
    forms[u.id].patch(route('utilizadores.update', u.id), {
        preserveScroll: true,
        onSuccess: () => { editandoId.value = null; },
    });
}
function excluir(u) {
    if (!confirm(`Excluir a conta "${u.username}"? Esta ação não pode ser desfeita.`)) return;
    router.delete(route('utilizadores.destroy', u.id), { preserveScroll: true });
}
</script>

<template>
    <section class="view active">
        <div class="page-head">
            <div><h1>Utilizadores Registados</h1><p>Contas com acesso ao AlvoFlow — {{ utilizadores.length }} no total.</p></div>
        </div>

        <div class="user-list">
            <div class="card user-item" v-for="u in utilizadores" :key="u.id">
                <template v-if="editandoId !== u.id">
                    <div class="user-row">
                        <div class="user-avatar">{{ u.username.charAt(0).toUpperCase() }}</div>
                        <div class="who">
                            <b>{{ u.username }}</b>
                            <span>{{ u.email || 'sem email associado' }}</span>
                            <span>
                                <span class="online-dot" :class="{ on: estaOnline(u.last_seen_at) }"></span>
                                {{ estaOnline(u.last_seen_at) ? 'Online agora — ' + (u.last_url || '') : 'Última atividade: ' + formatDateHora(u.last_seen_at) }}
                            </span>
                        </div>
                        <div class="user-meta">
                            <div class="since">Registado em<br>{{ formatDateHora(u.created_at) }}</div>
                            <div class="user-actions">
                                <button class="icon-btn" title="Editar" @click="abrirEdicao(u)" v-html="ICONS.edit"></button>
                                <button class="icon-btn" title="Excluir" @click="excluir(u)" v-html="ICONS.trash" style="color:var(--danger);"></button>
                            </div>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <form @submit.prevent="guardar(u)" class="form-grid" style="align-items:start;">
                        <div class="field" :class="{ err: forms[u.id].errors.username }">
                            <label>Nome de utilizador</label>
                            <input type="text" v-model="forms[u.id].username" />
                            <div class="msg">{{ forms[u.id].errors.username }}</div>
                        </div>
                        <div class="field" :class="{ err: forms[u.id].errors.email }">
                            <label>Email</label>
                            <input type="email" v-model="forms[u.id].email" />
                            <div class="msg">{{ forms[u.id].errors.email }}</div>
                        </div>
                        <div class="field" :class="{ err: forms[u.id].errors.password }">
                            <label>Nova palavra-passe (opcional)</label>
                            <input type="password" v-model="forms[u.id].password" placeholder="Deixe em branco para manter" />
                            <div class="msg">{{ forms[u.id].errors.password }}</div>
                        </div>
                        <div class="field">
                            <label>Confirmar nova palavra-passe</label>
                            <input type="password" v-model="forms[u.id].password_confirmation" />
                        </div>
                        <div class="full" style="display:flex; gap:8px; justify-content:flex-end;">
                            <button type="button" class="btn btn-outline" @click="cancelarEdicao">Cancelar</button>
                            <button type="submit" class="btn btn-primary" style="width:auto;" :disabled="forms[u.id].processing">Guardar</button>
                        </div>
                    </form>
                </template>
            </div>
            <div v-if="!utilizadores.length" class="empty"><div class="ico" v-html="ICONS.users"></div><p>Ainda não há utilizadores registados.</p></div>
        </div>
    </section>
</template>
