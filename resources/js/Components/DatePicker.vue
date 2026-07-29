<script setup>
// Seletor de data em calendário (substitui o <input type="date"> nativo, cujo popup não dá
// para estilizar e varia muito de aparência entre navegadores/SO). Recebe/emite "YYYY-MM-DD".
import { ref, computed, watch, onBeforeUnmount } from 'vue';

const props = defineProps({ modelValue: { type: String, required: true } });
const emit = defineEmits(['update:modelValue']);

const MESES = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
const DIAS_SEMANA = ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'];

function parse(v) {
    const [y, m, d] = (v || '').split('-').map(Number);
    return { y: y || new Date().getFullYear(), m: (m || 1) - 1, d: d || 1 };
}

const open = ref(false);
const root = ref(null);
const view = ref(parse(props.modelValue));

watch(() => props.modelValue, (v) => { view.value = parse(v); });

const label = computed(() => {
    if (!props.modelValue) return 'Selecione a data';
    const { y, m, d } = parse(props.modelValue);
    return `${d} de ${MESES[m]} de ${y}`;
});

const tituloMes = computed(() => `${MESES[view.value.m]} ${view.value.y}`);

const celulas = computed(() => {
    const { y, m } = view.value;
    const primeiroDiaSemana = new Date(y, m, 1).getDay();
    const totalDias = new Date(y, m + 1, 0).getDate();
    return [...Array(primeiroDiaSemana).fill(null), ...Array.from({ length: totalDias }, (_, i) => i + 1)];
});

function mudarMes(delta) {
    let { y, m } = view.value;
    m += delta;
    if (m < 0) { m = 11; y -= 1; } else if (m > 11) { m = 0; y += 1; }
    view.value = { y, m };
}

function escolher(dia) {
    const mm = String(view.value.m + 1).padStart(2, '0');
    const dd = String(dia).padStart(2, '0');
    emit('update:modelValue', `${view.value.y}-${mm}-${dd}`);
    open.value = false;
}

function isSelecionado(dia) {
    if (!props.modelValue) return false;
    const sel = parse(props.modelValue);
    return sel.y === view.value.y && sel.m === view.value.m && sel.d === dia;
}

function isHoje(dia) {
    const hoje = new Date();
    return hoje.getFullYear() === view.value.y && hoje.getMonth() === view.value.m && hoje.getDate() === dia;
}

function toggle() {
    if (!open.value) view.value = parse(props.modelValue);
    open.value = !open.value;
}

function fecharSeForaClicado(e) {
    if (root.value && !root.value.contains(e.target)) open.value = false;
}

watch(open, (isOpen) => {
    if (isOpen) document.addEventListener('mousedown', fecharSeForaClicado);
    else document.removeEventListener('mousedown', fecharSeForaClicado);
});
onBeforeUnmount(() => document.removeEventListener('mousedown', fecharSeForaClicado));
</script>

<template>
    <div class="datepicker" ref="root">
        <button type="button" class="datepicker-input" @click="toggle">
            <span>{{ label }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="5" width="17" height="15.5" rx="1.6"/><path d="M3.5 9.5h17M8 3v3.6M16 3v3.6"/></svg>
        </button>
        <div v-if="open" class="datepicker-pop">
            <div class="datepicker-head">
                <button type="button" @click="mudarMes(-1)">‹</button>
                <b>{{ tituloMes }}</b>
                <button type="button" @click="mudarMes(1)">›</button>
            </div>
            <div class="datepicker-grid datepicker-dow">
                <span v-for="(d, i) in DIAS_SEMANA" :key="i">{{ d }}</span>
            </div>
            <div class="datepicker-grid">
                <button
                    v-for="(dia, i) in celulas" :key="i" type="button"
                    class="datepicker-day"
                    :class="{ pad: !dia, selected: isSelecionado(dia), today: isHoje(dia) }"
                    :disabled="!dia"
                    @click="dia && escolher(dia)"
                >{{ dia }}</button>
            </div>
        </div>
    </div>
</template>
