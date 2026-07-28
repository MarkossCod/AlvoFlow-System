import { reactive } from 'vue';

// Estado partilhado do modal de detalhes/edição de pedido, usado por várias páginas
// (Home, Pesquisar, Visualizar) sem precisar de passar props/eventos por vários níveis.
export const detailStore = reactive({
    pedido: null,
    editing: false,
});

export function verPedido(pedido) {
    detailStore.pedido = pedido;
    detailStore.editing = false;
}

export function fecharDetail() {
    detailStore.pedido = null;
    detailStore.editing = false;
}

export function editarPedido() {
    detailStore.editing = true;
}

export function fecharEdicao() {
    detailStore.editing = false;
}
