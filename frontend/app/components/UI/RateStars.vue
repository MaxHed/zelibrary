<template>
    <div class="flex items-center" :aria-label="ariaLabel" role="img">
        <div class="flex items-center gap-0.5">
            <span
                v-for="i in outOf"
                :key="i"
                class="relative inline-block leading-none"
                :class="sizeClass"
            >
                <!-- base (vide) -->
                <span class="text-gray-300 select-none">★</span>
                <!-- overlay (plein / demi) -->
                <span
                    class="absolute inset-0 overflow-hidden text-yellow-500 select-none"
                    :style="{ width: overlayWidth(i) }"
                >
                    ★
                </span>
            </span>
        </div>
        <span v-if="showValue" class="ml-2 text-sm opacity-70">{{ displayValue }}</span>
    </div>
    
</template>

<script setup lang="ts">
const props = defineProps<{
    rate: number
    outOf?: number
    size?: 'sm' | 'md' | 'lg'
    showValue?: boolean
}>()

const outOf = computed(() => Math.max(1, props.outOf ?? 5))
const clampedRate = computed(() => Math.min(outOf.value, Math.max(0, props.rate ?? 0)))

// Affichage arrondi au demi
const roundedToHalf = computed(() => Math.round(clampedRate.value * 2) / 2)

const overlayWidth = (index: number) => {
    // index est la position 1..outOf
    const diff = roundedToHalf.value - (index - 1)
    if (diff >= 1) return '100%'
    if (diff >= 0.5) return '50%'
    if (diff > 0) return `${Math.max(0, Math.min(1, diff)) * 100}%` // sécurité si besoin
    return '0%'
}

const sizeClass = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'text-base'
        case 'lg':
            return 'text-3xl'
        case 'md':
        default:
            return 'text-2xl'
    }
})

const showValue = computed(() => props.showValue ?? false)
const displayValue = computed(() => `${roundedToHalf.value.toFixed(1)} / ${outOf.value}`)
const ariaLabel = computed(() => `Note ${roundedToHalf.value} sur ${outOf.value}`)
</script>

<style scoped>
</style>
