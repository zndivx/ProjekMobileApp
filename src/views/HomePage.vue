<template>
  <div class="container">
    <h1>CRUD User (Vue + PHP MySQL)</h1>

    <div v-if="message" class="message">{{ message }}</div>

    <form @submit.prevent="handleSubmit" class="form">
      <input
        v-model="form.name"
        type="text"
        placeholder="Nama"
        class="input"
        :disabled="loading"
      />
      <input
        v-model="form.email"
        type="email"
        placeholder="Email"
        class="input"
        :disabled="loading"
      />
      <button type="submit" class="btn-primary" :disabled="loading">
        {{ loading ? "Loading..." : editId ? "Update" : "Tambah" }}
      </button>
      <button
        v-if="editId"
        type="button"
        class="btn-cancel"
        @click="handleCancel"
        :disabled="loading"
      >
        Batal
      </button>
    </form>

    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="loading && !users.length">
          <td colspan="4" class="loading">Loading data...</td>
        </tr>
        <tr v-else-if="!users.length">
          <td colspan="4" class="loading">Tidak ada data</td>
        </tr>
        <tr v-for="u in users" :key="u.id">
          <td>{{ u.id }}</td>
          <td>{{ u.name }}</td>
          <td>{{ u.email }}</td>
          <td>
            <button class="btn-edit" @click="handleEdit(u)">Edit</button>
            <button class="btn-delete" @click="handleDelete(u.id)">Hapus</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";

const API_URL = "http://localhost/ProjekMobile/server/api.php";

const users = ref([]);
const form = ref({ name: "", email: "" });
const editId = ref(null);
const loading = ref(false);
const message = ref("");
let refreshInterval = null;

const getUsers = async () => {
  const res = await axios.get(API_URL);
  return res.data;
};

const addUser = async (data) => {
  const res = await axios.post(API_URL, data, {
    headers: { "Content-Type": "application/json" },
  });
  return res.data;
};

const updateUser = async (id, data) => {
  const res = await axios.put(API_URL, { id, ...data }, {
    headers: { "Content-Type": "application/json" },
  });
  return res.data;
};

const deleteUser = async (id) => {
  const res = await axios.delete(API_URL, {
    data: { id },
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
  });
  return res.data;
};

const fetchUsers = async () => {
  try {
    const newData = await getUsers();

    if (JSON.stringify(newData) !== JSON.stringify(users.value)) {
      users.value = newData;
    }
  } catch (err) {
    console.error("Gagal mengambil data:", err);
    message.value = "Gagal mengambil data dari server.";
  }
};

onMounted(() => {
  fetchUsers();

  refreshInterval = setInterval(fetchUsers, 2000);
});

onBeforeUnmount(() => {
  if (refreshInterval) clearInterval(refreshInterval);
});

const handleSubmit = async () => {
  if (!form.value.name || !form.value.email) {
    message.value = "Nama dan Email wajib diisi!";
    return;
  }

  loading.value = true;
  try {
    if (editId.value) {
      await updateUser(editId.value, form.value);
      message.value = "Data berhasil diupdate!";
      editId.value = null;
    } else {
      await addUser(form.value);
      message.value = "Data berhasil ditambahkan!";
    }

    form.value = { name: "", email: "" };
    await fetchUsers();
  } catch (err) {
    console.error(err);
    message.value = "Gagal menyimpan data ke server.";
  } finally {
    loading.value = false;
  }
};

const handleDelete = async (id) => {
  if (confirm("Yakin ingin menghapus data ini?")) {
    loading.value = true;
    try {
      await deleteUser(id);
      message.value = "Data berhasil dihapus!";
      await fetchUsers();
    } catch (err) {
      console.error(err);
      message.value = "Gagal menghapus data.";
    } finally {
      loading.value = false;
    }
  }
};

const handleEdit = (user) => {
  form.value = { name: user.name, email: user.email };
  editId.value = user.id;
  message.value = "";
};

const handleCancel = () => {
  form.value = { name: "", email: "" };
  editId.value = null;
};
</script>

<style scoped>
.container {
  padding: 30px;
  font-family: Arial, sans-serif;
  max-width: 800px;
  margin: 0 auto;
}
h1 {
  text-align: center;
  margin-bottom: 20px;
}
.form {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
}
.input {
  flex: 1;
  min-width: 200px;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
}
.btn-primary,
.btn-cancel,
.btn-edit,
.btn-delete {
  border: none;
  border-radius: 5px;
  padding: 8px 15px;
  cursor: pointer;
}
.btn-primary {
  background: #007bff;
  color: white;
}
.btn-cancel {
  background: #6c757d;
  color: white;
}
.btn-edit {
  background: #ffc107;
  margin-right: 5px;
}
.btn-delete {
  background: #dc3545;
  color: white;
}
.table {
  width: 100%;
  border-collapse: collapse;
}
th,
td {
  border-bottom: 1px solid #ddd;
  padding: 10px;
  text-align: left;
}
.loading {
  text-align: center;
  color: #777;
}
.message {
  background: #d4edda;
  color: #155724;
  padding: 10px;
  border: 1px solid #c3e6cb;
  border-radius: 5px;
  margin-bottom: 10px;
}
</style>