<script setup lang="ts">
import { isVNode } from 'vue'
import { useToast } from './use-toast'
import {
  Toast,
  ToastClose,
  ToastDescription,
  ToastProvider,
  ToastTitle,
  ToastViewport,
} from '.'

// Icon imports
import {
  CheckCircle,
  AlertCircle,
  Info,
  AlertTriangle,
} from 'lucide-vue-next'

const { toasts } = useToast()

// Helper to select icon by variant
const getIcon = (variant: string | undefined) => {
  switch (variant) {
    case 'destructive':
      return AlertCircle
    case 'default':
      return CheckCircle
    case 'error':
      return AlertCircle
    case 'warning':
      return AlertTriangle
    case 'info':
    default:
      return Info
  }
}

// Helper to set icon color by variant
const getIconColor = (variant: string | undefined) => {
  switch (variant) {
    case 'destructive':
      return 'text-green-500'
    case 'error':
      return 'text-red-500'
    case 'warning':
      return 'text-yellow-500'
    case 'info':
    default:
      return 'text-blue-500'
  }
}
</script>

<template>
  <ToastProvider>
    <Toast v-for="toast in toasts" :key="toast.id" v-bind="toast">
      <div class="flex items-start gap-3">
        <!-- Variant Icon -->
        <component
          :is="getIcon(toast.variant ?? undefined)"
          :class="['mt-0.5 h-5 w-5', getIconColor(toast.variant ?? undefined)]"
        />

        <!-- Toast Content -->
        <div class="grid gap-1 flex-1">
          <ToastTitle v-if="toast.title">
            {{ toast.title }}
          </ToastTitle>

          <template v-if="toast.description">
            <ToastDescription v-if="isVNode(toast.description)">
              <component :is="toast.description" />
            </ToastDescription>
            <ToastDescription v-else>
              {{ toast.description }}
            </ToastDescription>
          </template>
        </div>

        <!-- Close Button -->
        <ToastClose />
      </div>

      <!-- Optional Action -->
      <component :is="toast.action" />
    </Toast>
    <ToastViewport />
  </ToastProvider>
</template>
