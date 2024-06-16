import './bootstrap.js';
import { createApp } from 'vue';
import router from './router'; // Import the router
import vuetify from './plugins/vuetify'; 
import '@mdi/font/css/materialdesignicons.css'; 

const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);

// Register the router with the app
app.use(router);
app.use(vuetify);

app.mount('#app');