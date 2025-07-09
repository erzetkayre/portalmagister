import { ref, watch } from 'vue';

export function useDebounce<T>(value: T, delay = 300) {
    const debounced = ref(value) as { value: T };
    let timeout: ReturnType<typeof setTimeout>;

    watch(value, (newVal) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            debounced.value = newVal;
        }, delay);
    });

    return debounced;
}
