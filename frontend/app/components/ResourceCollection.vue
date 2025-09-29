<template>
  <section class="page-width mx-auto py-6">
    <div class="flex flex-col gap-4">
      <div class="flex items-center gap-3">
        <input
          v-model.trim="search"
          type="search"
          class="border rounded px-3 py-2 w-full"
          :placeholder="placeholder"
          @keyup.enter="applySearch"
        />
        <button class="px-4 py-2 bg-[var(--color-primary)] text-white rounded" @click="applySearch">
          Rechercher
        </button>
      </div>

      <div v-if="loading" class="text-sm opacity-70">Chargement...</div>
      <div v-else-if="error" class="text-sm text-red-600">{{ error }}</div>

      <ul v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <li v-for="(item, idx) in items" :key="(item as any)[itemKey] ?? idx" class="border rounded p-4">
          <slot name="item" :item="item" :index="idx">
            <h3 class="font-semibold mb-1">{{ (item as any).title ?? (item as any).name ?? `#${(item as any).id ?? idx}` }}</h3>
            <p class="text-sm opacity-80" v-if="(item as any).summary">{{ (item as any).summary }}</p>
          </slot>
        </li>
      </ul>

      <div class="flex items-center justify-between mt-4" v-if="totalPages > 1">
        <button class="px-3 py-2 border rounded" :disabled="page <= 1" @click="goToPage(page - 1)">
          Précédent
        </button>
        <div class="text-sm">Page {{ page }} / {{ totalPages }}</div>
        <button class="px-3 py-2 border rounded" :disabled="page >= totalPages" @click="goToPage(page + 1)">
          Suivant
        </button>
      </div>
    </div>
  </section>
  
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useApi } from '@/composable/useApi'

type AnyRecord = Record<string, any>

const props = defineProps<{
  resourcePath: string
  searchProps?: string[]
  itemsPerPage?: number
  placeholder?: string
  withCredentials?: boolean
  additionalParams?: AnyRecord
  itemKey?: string
}>()

const placeholder = computed(() => props.placeholder ?? 'Rechercher...')
const itemsPerPage = computed(() => props.itemsPerPage ?? 12)
const searchProps = computed(() => props.searchProps ?? [])
const withCredentials = computed(() => props.withCredentials ?? true)
const itemKey = computed(() => props.itemKey ?? 'id')

const { get } = useApi()

const items = ref<any[]>([])
const totalItems = ref<number>(0)
const loading = ref<boolean>(false)
const error = ref<string | null>(null)

const page = ref<number>(1)
const search = ref<string>('')

const totalPages = computed(() => {
  const perPage = itemsPerPage.value
  if (perPage <= 0) return 1
  return Math.max(1, Math.ceil(totalItems.value / perPage))
})

function buildQueryParams() {
  const params: Record<string, any> = {
    page: page.value,
    itemsPerPage: itemsPerPage.value,
  }
  // API Platform SearchFilter: applique la recherche sur chaque propriété fournie
  if (search.value && searchProps.value.length > 0) {
    for (const prop of searchProps.value) {
      params[prop] = search.value
    }
  }
  // Paramètres additionnels éventuels
  if (props.additionalParams) {
    Object.assign(params, props.additionalParams)
  }
  return params
}

async function fetchCollection() {
  try {
    loading.value = true
    error.value = null

    const params = buildQueryParams()
    const res: any = await get(props.resourcePath, { params }, withCredentials.value)

    const member = res?.['hydra:member'] ?? res?.member ?? []
    const total = res?.['hydra:totalItems'] ?? res?.totalItems ?? member.length
    items.value = member as any[]
    totalItems.value = Number(total) || 0
  } catch (e: any) {
    error.value = e?.message ?? 'Erreur de chargement'
  } finally {
    loading.value = false
  }
}

function goToPage(p: number) {
  page.value = Math.min(Math.max(1, p), totalPages.value)
}

function applySearch() {
  page.value = 1
}

watch([page, itemsPerPage], fetchCollection)
// déclenche une nouvelle requête quand la recherche change avec un debounce léger
let searchTimer: any
watch(search, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    applySearch()
  }, 300)
})

onMounted(fetchCollection)

// Expose pour intégration parent si nécessaire
defineExpose({ fetchCollection, page, itemsPerPage, search })
</script>

<style scoped>
</style>


