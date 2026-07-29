<script setup>
// Renderiza o pedido de confirmação atual do confirmDialog.js. Montado uma única vez no
// AppLayout — qualquer página só precisa chamar `confirmDialog(titulo, mensagem)`.
import { confirmDialogState, resolveConfirmDialog } from '../confirmDialog';

const state = confirmDialogState();
</script>

<template>
    <div v-if="state.show" class="confirm-overlay" @click.self="resolveConfirmDialog(false)">
        <div class="confirm-box">
            <h3>{{ state.title }}</h3>
            <p>{{ state.message }}</p>
            <div class="confirm-actions">
                <button type="button" class="btn btn-outline" @click="resolveConfirmDialog(false)">Cancelar</button>
                <button type="button" class="btn confirm-danger" @click="resolveConfirmDialog(true)">Excluir</button>
            </div>
        </div>
    </div>
</template>
