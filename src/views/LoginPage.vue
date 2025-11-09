<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="primary">
        <ion-title>Login</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding d-flex justify-content-center align-items-center vh-100" fullscreen>
      <div class="card shadow-lg p-4 border-0" style="width: 100%; max-width: 400px; border-radius: 1rem;">
        <div class="text-center mb-3">
          <h4 class="fw-bold text-primary">Masuk ke Akun</h4>
          <p class="text-muted small">Silakan login untuk melanjutkan</p>
        </div>

        <ion-item class="mb-3 border rounded">
          <ion-label position="floating">Username</ion-label>
          <ion-input v-model="username" type="text" required></ion-input>
        </ion-item>

        <ion-item class="mb-4 border rounded">
          <ion-label position="floating">Password</ion-label>
          <ion-input v-model="password" type="password" required></ion-input>
        </ion-item>

        <ion-button
          expand="block"
          color="primary"
          class="btn btn-primary w-100 fw-semibold"
          @click="login"
          :disabled="loading"
        >
          {{ loading ? 'Memproses...' : 'Login' }}
        </ion-button>

        <ion-text color="danger" v-if="errorMessage" class="ion-margin-top text-center">
          <p class="mt-3">{{ errorMessage }}</p>
        </ion-text>
      </div>
    </ion-content>
  </ion-page>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

export default defineComponent({
  name: 'LoginPage',
  setup() {
    const router = useRouter();
    const username = ref('');
    const password = ref('');
    const loading = ref(false);
    const errorMessage = ref('');

    const login = async () => {
      errorMessage.value = '';
      loading.value = true;

      try {
        // Kirim data ke PHP backend
        const response = await axios.post('http://localhost/ProjekMobile/server/LoginAPI.php', {
          username: username.value,
          password: password.value
        });

        // Backend mengirimkan: { success: true/false, message: '...' }
        if (response.data.success === true) {
          alert('Login berhasil!');
          await router.push('/home');
        } else {
          errorMessage.value = response.data.message || 'Username atau password salah.';
        }
      } catch (err: any) {
        console.error(err);
        errorMessage.value = 'Tidak dapat terhubung ke server.';
      } finally {
        loading.value = false;
      }
    };

    return { username, password, login, loading, errorMessage };
  }
});
</script>

<style scoped>
ion-content {
  --background: #f8f9fa;
}

ion-item {
  --background: white;
  --border-color: #dee2e6;
  --highlight-color-focused: var(--ion-color-primary);
}

ion-button:hover {
  filter: brightness(1.05);
}

.card {
  background-color: white;
}

@media (max-width: 480px) {
  .card {
    margin: 0 10px;
  }
}
</style>