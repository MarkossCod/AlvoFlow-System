// Substituto do confirm() nativo do browser por um modal com o visual do AlvoFlow.
// Uso: `if (!(await confirmDialog('Título', 'Mensagem.'))) return;`
// Para ações não-destrutivas (ex: logout), passe `{ confirmLabel: 'Terminar sessão', danger: false }`.
// Estado partilhado (singleton) lido pelo <ConfirmDialogHost/>, montado uma vez no AppLayout.
import { reactive } from 'vue';

const state = reactive({
    show: false,
    title: '',
    message: '',
    confirmLabel: 'Confirmar',
    danger: true,
    resolve: null,
});

export function confirmDialog(title, message, { confirmLabel = 'Confirmar', danger = true } = {}) {
    state.title = title;
    state.message = message;
    state.confirmLabel = confirmLabel;
    state.danger = danger;
    state.show = true;
    return new Promise((resolve) => {
        state.resolve = resolve;
    });
}

export function confirmDialogState() {
    return state;
}

export function resolveConfirmDialog(value) {
    state.show = false;
    state.resolve?.(value);
    state.resolve = null;
}
