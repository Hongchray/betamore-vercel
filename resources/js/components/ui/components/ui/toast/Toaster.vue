<script setup lang="ts">
import { isVNode, computed } from 'vue'
import { AlertCircle, CheckCircle, Info, XCircle } from 'lucide-vue-next'
import { useToast } from './use-toast'
import { Toast, ToastClose, ToastDescription, ToastProvider, ToastTitle, ToastViewport } from '.'

const { toasts } = useToast()

const getIconComponent = (variant?: string) => {
  switch (variant) {
    case 'success':
    case 'default':
      return CheckCircle
    case 'destructive':
    case 'error':
      return XCircle
    case 'warning':
      return AlertCircle
    default:
      return Info
  }
}

const getIconColorClass = (variant?: string) => {
  switch (variant) {
    case 'success':
    case 'default':
      return 'text-green-500'
    case 'destructive':
    case 'error':
      return 'text-red-500'
    case 'warning':
      return 'text-yellow-500'
    default:
      return 'text-blue-500'
  }
}
</script>

<template>
  <ToastProvider>
    <Toast v-for="toast in toasts" :key="toast.id" v-bind="toast">
      <div class="flex items-start gap-3">
        <!-- Icon -->
        <component 
          :is="getIconComponent(toast.variant)" 
          :class="getIconColorClass(toast.variant)" 
          class="mt-0.5 h-5 w-5 flex-shrink-0" 
        />
        
        <!-- Content -->
        <div class="grid gap-1 flex-1 min-w-0">
          <ToastTitle v-if="toast.title" class="text-sm font-medium">
            {{ toast.title }}
          </ToastTitle>
          <template v-if="toast.description">
            <ToastDescription v-if="isVNode(toast.description)" class="text-sm opacity-90">
              <component :is="toast.description" />
            </ToastDescription>
            <ToastDescription v-else class="text-sm opacity-90">
              {{ toast.description }}
            </ToastDescription>
          </template>
        </div>
        
        <!-- Close Button -->
        <ToastClose class="flex-shrink-0" />
      </div>
      
      <!-- Action Component (if exists) -->
      <component v-if="toast.action" :is="toast.action" class="mt-3" />
    </Toast>
    <ToastViewport />
  </ToastProvider>
</template>
