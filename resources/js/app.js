import '../css/app.css';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import { reveal } from './directives/reveal';

createApp(App).use(createPinia()).use(router).directive('reveal', reveal).mount('#app');
