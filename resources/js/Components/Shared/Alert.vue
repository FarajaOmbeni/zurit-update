<template>
    <div v-if="show"
        class="flex shadow-lg rounded-lg mb-4 absolute top-6 left-[42%] sm:left-1/2 transform -translate-x-1/2 w-full max-w-xs sm:max-w-sm md:max-w-md"
        :class="[width, 'px-2 sm:px-0']">
        <div class="py-3 sm:py-4 px-4 sm:px-6 rounded-l-lg flex items-center" :class="typeClasses[type].bg">
            <svg xmlns="http://www.w3.org/2000/svg" class="fill-current text-white" viewBox="0 0 16 16" width="16"
                height="16" sm:width="20" sm:height="20">
                <path fill-rule="evenodd" :d="typeClasses[type].icon"></path>
            </svg>
        </div>
        <div
            class="px-3 sm:px-4 py-4 sm:py-6 bg-white rounded-r-lg flex justify-between items-center w-full border border-l-transparent border-gray-200">
            <div class="text-sm sm:text-base">{{ message }}</div>
            <button @click="closeAlert" v-if="dismissible" class="ml-2 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="fill-current text-gray-700" viewBox="0 0 16 16"
                    width="16" height="16" sm:width="20" sm:height="20">
                    <path fill-rule="evenodd"
                        d="M3.72 3.72a.75.75 0 011.06 0L8 6.94l3.22-3.22a.75.75 0 111.06 1.06L9.06 8l3.22 3.22a.75.75 0 11-1.06 1.06L8 9.06l-3.22 3.22a.75.75 0 01-1.06-1.06L6.94 8 3.72 4.78a.75.75 0 010-1.06z">
                    </path>
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const emit = defineEmits(['close']);

const props = defineProps({
    type: {
        type: String,
        default: 'info',
        validator: (value) => ['info', 'success', 'warning', 'danger'].includes(value)
    },
    message: {
        type: String,
        required: true
    },
    dismissible: {
        type: Boolean,
        default: true
    },
    autoClose: {
        type: Boolean,
        default: false
    },
    duration: {
        type: Number,
        default: 5000
    },
    width: {
        type: String,
        default: 'w-full'
    }
});

const show = ref(true);

const typeClasses = {
    info: {
        bg: 'bg-blue-500',
        icon: 'M8 1.5a6.5 6.5 0 100 13 6.5 6.5 0 000-13zM0 8a8 8 0 1116 0A8 8 0 010 8zm6.5-.25A.75.75 0 017.25 7h1a.75.75 0 01.75.75v2.75h.25a.75.75 0 010 1.5h-2a.75.75 0 010-1.5h.25v-2h-.25a.75.75 0 01-.75-.75zM8 6a1 1 0 100-2 1 1 0 000 2z'
    },
    success: {
        bg: 'bg-green-600',
        icon: 'M13.78 4.22a.75.75 0 010 1.06l-7.25 7.25a.75.75 0 01-1.06 0L2.22 9.28a.75.75 0 011.06-1.06L6 10.94l6.72-6.72a.75.75 0 011.06 0z'
    },
    warning: {
        bg: 'bg-yellow-600',
        icon: 'M8.22 1.754a.25.25 0 00-.44 0L1.698 13.132a.25.25 0 00.22.368h12.164a.25.25 0 00.22-.368L8.22 1.754zm-1.763-.707c.659-1.234 2.427-1.234 3.086 0l6.082 11.378A1.75 1.75 0 0114.082 15H1.918a1.75 1.75 0 01-1.543-2.575L6.457 1.047zM9 11a1 1 0 11-2 0 1 1 0 012 0zm-.25-5.25a.75.75 0 00-1.5 0v2.5a.75.75 0 001.5 0v-2.5z'
    },
    danger: {
        bg: 'bg-red-600',
        icon: 'M4.47.22A.75.75 0 015 0h6a.75.75 0 01.53.22l4.25 4.25c.141.14.22.331.22.53v6a.75.75 0 01-.22.53l-4.25 4.25A.75.75 0 0111 16H5a.75.75 0 01-.53-.22L.22 11.53A.75.75 0 010 11V5a.75.75 0 01.22-.53L4.47.22zm.84 1.28L1.5 5.31v5.38l3.81 3.81h5.38l3.81-3.81V5.31L10.69 1.5H5.31zM8 4a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 018 4zm0 8a1 1 0 100-2 1 1 0 000 2z'
    }
};

const closeAlert = () => {
    show.value = false;
    emit('close');
};

onMounted(() => {
    if (props.autoClose) {
        setTimeout(() => {
            show.value = false;
            emit('close');
        }, props.duration);
    }
});
</script>


<!-- 
How to use
 Example parent component
     Basic usage 
    <Alert 
      type="success" 
      message="Operation completed successfully!" 
    />
    
    <!-- With auto-close feature 
    <Alert 
      type="info" 
      message="This alert will disappear in 3 seconds" 
      :auto-close="true"
      :duration="3000"
    />
    
    Non-dismissible alert 
    <Alert 
      type="warning" 
      message="This is an important warning!" 
      :dismissible="false"
    />
    
    Displaying alerts from flash messages
    <Alert 
      v-for="(message, index) in flashMessages.success" 
      :key="`success-${index}`"
      type="success" 
      :message="message" 
    />
    
    <Alert 
      v-for="(message, index) in flashMessages.error" 
      :key="`error-${index}`"
      type="danger" 
      :message="message" 
    />
    
    Full width alert
    <Alert 
      type="info" 
      message="This alert takes the full width of its container" 
      width="w-full"
    />
  </div>
-->