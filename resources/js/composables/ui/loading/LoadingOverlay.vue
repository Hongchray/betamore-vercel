<template>
    <div class="loading-overlay">
        <div class="spinner" />
    </div>
</template>

<script setup lang="ts"></script>

<style scoped>
.loading-overlay {
    position: absolute;
    inset: 0;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
}

.spinner {
    width: 55px;
    height: 55px;
    border: 3px solid rgba(0, 0, 0, 0.1);
    border-top-color: #4f46e5;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@media (prefers-color-scheme: dark) {
    .loading-overlay {
    }

    .spinner {
        border-color: rgba(255, 255, 255, 0.2);
        border-top-color: #a855f7;
    }
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
