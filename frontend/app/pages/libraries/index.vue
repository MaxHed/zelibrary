<template>
  <ResourceCollection resource-path="/libraries" :search-props="['name']">
    <template #item="{ item }">
      <NuxtLink :to="`/libraries/${item.id}`">
        <div class="flex flex-col gap-2">
          <h3 class="font-semibold">{{ item.name }}</h3>
          <p v-if="item.description" class="text-sm opacity-80 line-clamp-3">
            {{ item.description }}
          </p>
          <div class="text-xs opacity-70" v-if="item.booksCount !== undefined">
            {{ item.booksCount }} livres
          </div>
        </div>
      </NuxtLink>
    </template>
  </ResourceCollection>
</template>

<script setup>
import ResourceCollection from "@/components/ResourceCollection.vue";
</script>

<style scoped></style>
