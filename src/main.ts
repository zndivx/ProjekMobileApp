import { createApp } from 'vue';
import App from './App.vue';
import router from './router/index';
import axios from 'axios';
import '@ionic/vue/css/core.css';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

import '@ionic/vue/css/core.css';

axios.defaults.baseURL = 'http://localhost/ProjekMobile/server/';
axios.defaults.headers.common['Content-Type'] = 'application/json';

axios.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Inisialisasi Vue App
const app = createApp(App);

app.use(router);
app.mount('#app');