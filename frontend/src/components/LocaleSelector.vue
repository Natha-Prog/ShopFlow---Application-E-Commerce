<template>
  <div
    ref="rootRef"
    :class="variant === 'header'
      ? 'relative hidden sm:flex items-center rounded-lg border border-yellow-600/30 bg-yellow-600/10 backdrop-blur-sm h-9'
      : 'relative flex items-center rounded-lg border border-gray-200 bg-gray-50 h-10 w-full'"
  >
    <!-- Language -->
    <button
      type="button"
      :class="triggerClass"
      :aria-expanded="langOpen"
      aria-haspopup="listbox"
      @click.stop="toggleLang"
    >
      <svg class="w-3.5 h-3.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span class="font-semibold tracking-wide">{{ currentLang.toUpperCase() }}</span>
      <svg
        class="w-3 h-3 shrink-0 opacity-70 transition-transform duration-200"
        :class="{ 'rotate-180': langOpen }"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        aria-hidden="true"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <div :class="dividerClass" aria-hidden="true" />

    <!-- Currency -->
    <div :class="currencyClass" :title="currencyInfo.name">
      <svg class="w-3.5 h-3.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span class="font-semibold tracking-wide">{{ currencyInfo.symbol }}</span>
    </div>

    <!-- Language dropdown -->
    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="opacity-0 -translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-1"
    >
      <ul
        v-if="langOpen"
        role="listbox"
        :aria-label="currentLang === 'fr' ? 'Langue' : 'Language'"
        class="absolute top-[calc(100%+6px)] left-0 min-w-[160px] bg-white rounded-lg shadow-xl border border-gray-100 py-1 z-50"
      >
        <li
          v-for="lang in languages"
          :key="lang.code"
          role="option"
          :aria-selected="currentLang === lang.code"
        >
          <button
            type="button"
            class="w-full flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gold-pale transition"
            :class="{ 'bg-gold-pale text-gold-dark font-medium': currentLang === lang.code }"
            @click="selectLanguage(lang.code)"
          >
            <span class="w-6 text-center text-xs font-bold text-gray-500">{{ lang.code.toUpperCase() }}</span>
            <span class="flex-1 text-left">{{ lang.label }}</span>
            <svg
              v-if="currentLang === lang.code"
              class="w-4 h-4 text-gold shrink-0"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              aria-hidden="true"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </button>
        </li>
      </ul>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useI18n } from '@/composables/useI18n'
import { useCurrency } from '@/composables/useCurrency'

const { variant = 'header' } = defineProps<{
  variant?: 'header' | 'mobile'
}>()

const { setLanguage, getLanguage } = useI18n()
const { currencyInfo, getCurrency } = useCurrency()

const rootRef = ref<HTMLElement | null>(null)
const langOpen = ref(false)
const currentLang = ref<'fr' | 'en'>(getLanguage() as 'fr' | 'en')
const currentCurrency = ref(getCurrency())

const languages = [
  { code: 'fr' as const, label: 'Français' },
  { code: 'en' as const, label: 'English' },
]

const triggerClass = computed(() =>
  variant === 'header'
    ? 'flex items-center gap-1.5 px-3 h-full text-xs font-medium text-gold-light hover:bg-yellow-600/20 transition rounded-l-lg'
    : 'flex items-center gap-1.5 px-3 h-full text-xs font-medium text-gray-700 hover:bg-gray-100 transition rounded-l-lg'
)

const dividerClass = computed(() =>
  variant === 'header' ? 'w-px h-4 bg-yellow-600/30 shrink-0' : 'w-px h-4 bg-gray-200 shrink-0'
)

const currencyClass = computed(() =>
  variant === 'header'
    ? 'flex items-center gap-1.5 px-3 h-full text-xs text-gold-light select-none'
    : 'flex items-center gap-1.5 px-3 h-full text-xs text-gray-700 select-none'
)

function toggleLang() {
  langOpen.value = !langOpen.value
}

function selectLanguage(code: 'fr' | 'en') {
  setLanguage(code)
  currentLang.value = code
  langOpen.value = false
}

function handleClickOutside(e: MouseEvent) {
  if (rootRef.value && !rootRef.value.contains(e.target as Node)) {
    langOpen.value = false
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>
