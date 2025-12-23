<template>
    <div>
        <!-- Thumbnail -->
        <img v-if="image" :src="image" @click="visible = true" class="w-32 cursor-zoom-in rounded shadow" :class="className" alt="Thumbnail" />
        <vue-easy-lightbox :visible="visible" :imgs="[image]" @hide="visible = false" />
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import VueEasyLightbox from 'vue-easy-lightbox';
defineProps<{
    image: string;
    className?: string;
}>();
const visible = ref(false);
</script>
