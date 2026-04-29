# API Integration Guide - Nuxt.js

## 🔄 Integrasi dengan Nuxt.js

### 1. Setup Axios Plugin

File: `plugins/api.ts`

```typescript
import axios from "axios";
import type { NuxtApp } from "#app";

export default defineNuxtPlugin((nuxtApp: NuxtApp) => {
    const api = axios.create({
        baseURL: process.env.NUXT_PUBLIC_API_URL || "http://localhost:8000",
        headers: {
            "Content-Type": "application/json",
        },
        withCredentials: true,
    });

    // Add token to requests
    api.interceptors.request.use((config) => {
        const token = useCookie("api_token").value;
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    });

    // Handle responses
    api.interceptors.response.use(
        (response) => response,
        (error) => {
            if (error.response?.status === 401) {
                // Redirect to login
                useCookie("api_token").value = null;
                navigateTo("/login");
            }
            return Promise.reject(error);
        },
    );

    return {
        provide: {
            api,
        },
    };
});
```

### 2. Setup Environment Variables

File: `.env.local`

```env
NUXT_PUBLIC_API_URL=http://localhost:8000
```

### 3. Composable untuk Authentication

File: `composables/useAuth.ts`

```typescript
import type { MemberResource } from "~/types/api";

interface LoginPayload {
    id_jemaat: string;
    password: string;
}

export const useAuth = () => {
    const { $api } = useNuxtApp();
    const user = ref<MemberResource | null>(null);
    const token = useCookie("api_token");
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    // Login
    const login = async (credentials: LoginPayload) => {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await $api.post("/api/jemaat/login", credentials);
            token.value = response.data.data.token;
            user.value = response.data.data.member;
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || "Login gagal";
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    // Logout
    const logout = async () => {
        try {
            await $api.post("/api/jemaat/logout");
            token.value = null;
            user.value = null;
        } catch (err) {
            console.error("Logout error:", err);
        }
    };

    // Get current user
    const getMe = async () => {
        try {
            const response = await $api.get("/api/jemaat/biodata");
            user.value = response.data.data;
            return response.data.data;
        } catch (err) {
            console.error("Get user error:", err);
        }
    };

    return {
        user: readonly(user),
        token: readonly(token),
        isLoading: readonly(isLoading),
        error: readonly(error),
        login,
        logout,
        getMe,
    };
};
```

### 4. Composable untuk Member Data

File: `composables/useMember.ts`

```typescript
import type { MemberResource, LetterResource } from "~/types/api";

interface UpdateBiodataPayload {
    nama_lengkap?: string;
    jenis_kelamin?: string;
    tempat_lahir?: string;
    alamat?: string;
    no_telepon?: string;
    status_aktif?: boolean;
}

export const useMember = () => {
    const { $api } = useNuxtApp();
    const biodata = ref<MemberResource | null>(null);
    const surat = ref<LetterResource[]>([]);
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    // Get biodata
    const getBiodata = async () => {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await $api.get("/api/jemaat/biodata");
            biodata.value = response.data.data;
            return response.data.data;
        } catch (err: any) {
            error.value =
                err.response?.data?.message || "Gagal mengambil biodata";
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    // Update biodata
    const updateBiodata = async (payload: UpdateBiodataPayload) => {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await $api.put("/api/jemaat/biodata", payload);
            biodata.value = response.data.data;
            return response.data.data;
        } catch (err: any) {
            error.value =
                err.response?.data?.message || "Gagal mengupdate biodata";
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    // Get surat
    const getSurat = async (keyword?: string) => {
        isLoading.value = true;
        error.value = null;
        try {
            const params = keyword ? { keyword } : {};
            const response = await $api.get("/api/jemaat/surat", { params });
            surat.value = response.data.data;
            return response.data.data;
        } catch (err: any) {
            error.value =
                err.response?.data?.message || "Gagal mengambil surat";
            surat.value = [];
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    // Download surat
    const downloadSurat = async (id: number, filename?: string) => {
        try {
            const response = await $api.get(
                `/api/jemaat/surat/${id}/download`,
                {
                    responseType: "blob",
                },
            );

            // Create download link
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement("a");
            link.href = url;
            link.setAttribute("download", filename || `surat_${id}.pdf`);
            document.body.appendChild(link);
            link.click();
            link.parentNode?.removeChild(link);
            window.URL.revokeObjectURL(url);
        } catch (err: any) {
            error.value =
                err.response?.data?.message || "Gagal mendownload surat";
            throw err;
        }
    };

    return {
        biodata: readonly(biodata),
        surat: readonly(surat),
        isLoading: readonly(isLoading),
        error: readonly(error),
        getBiodata,
        updateBiodata,
        getSurat,
        downloadSurat,
    };
};
```

### 5. Type Definitions

File: `types/api.ts`

```typescript
export interface MemberResource {
    id: number;
    id_jemaat: string;
    nama_lengkap: string;
    jenis_kelamin: string;
    tanggal_lahir: string;
    tempat_lahir: string;
    alamat: string;
    no_telepon: string;
    status_aktif: boolean;
    role: string;
    created_at: string;
    updated_at: string;
}

export interface LetterResource {
    id: number;
    member_id: number;
    tipe_surat: string;
    letter_type: string;
    nomor_surat: string;
    tanggal_surat: string;
    keterangan: string;
    pdf_path: string;
    created_at: string;
    updated_at: string;
}

export interface ApiResponse<T> {
    status: "success" | "error";
    message: string;
    data: T;
}

export interface ApiListResponse<T> {
    status: "success" | "error";
    data: T[];
}
```

### 6. Contoh Login Page

File: `pages/login.vue`

```vue
<template>
    <div
        class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8"
    >
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2
                    class="mt-6 text-center text-3xl font-extrabold text-gray-900"
                >
                    Login Jemaat
                </h2>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
                <div v-if="error" class="rounded-md bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800">{{ error }}</p>
                </div>

                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="id_jemaat" class="sr-only">ID Jemaat</label>
                        <input
                            id="id_jemaat"
                            v-model="form.id_jemaat"
                            type="text"
                            autocomplete="username"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="ID Jemaat (DDMMYYYY)"
                        />
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Password"
                        />
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="isLoading"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    >
                        <span v-if="isLoading">Loading...</span>
                        <span v-else>Login</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
const router = useRouter();
const { login, isLoading, error } = useAuth();

const form = ref({
    id_jemaat: "",
    password: "",
});

const handleLogin = async () => {
    try {
        await login({
            id_jemaat: form.value.id_jemaat,
            password: form.value.password,
        });
        await router.push("/dashboard");
    } catch (err) {
        console.error("Login error:", err);
    }
};
</script>
```

### 7. Contoh Dashboard Page

File: `pages/dashboard.vue`

```vue
<template>
    <div class="container mx-auto px-4 py-8">
        <div v-if="biodata" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Biodata Section -->
            <div>
                <h1 class="text-2xl font-bold mb-4">Biodata Jemaat</h1>
                <div class="bg-white shadow rounded-lg p-6">
                    <p><strong>Nama:</strong> {{ biodata.nama_lengkap }}</p>
                    <p><strong>ID Jemaat:</strong> {{ biodata.id_jemaat }}</p>
                    <p>
                        <strong>Tempat Lahir:</strong>
                        {{ biodata.tempat_lahir }}
                    </p>
                    <p>
                        <strong>Tanggal Lahir:</strong>
                        {{ biodata.tanggal_lahir }}
                    </p>
                    <p><strong>Alamat:</strong> {{ biodata.alamat }}</p>
                    <p><strong>No Telepon:</strong> {{ biodata.no_telepon }}</p>
                    <p>
                        <strong>Status:</strong>
                        {{ biodata.status_aktif ? "Aktif" : "Tidak Aktif" }}
                    </p>
                </div>
            </div>

            <!-- Surat Section -->
            <div>
                <h1 class="text-2xl font-bold mb-4">Daftar Surat</h1>
                <input
                    v-model="keyword"
                    type="text"
                    placeholder="Cari surat..."
                    @input="searchSurat"
                    class="w-full px-4 py-2 border rounded mb-4"
                />
                <div v-if="surat.length > 0" class="space-y-2">
                    <div
                        v-for="s in surat"
                        :key="s.id"
                        class="bg-white shadow rounded p-4"
                    >
                        <p class="font-semibold">{{ s.tipe_surat }}</p>
                        <p class="text-sm text-gray-500">{{ s.nomor_surat }}</p>
                        <button
                            @click="downloadSurat(s.id, `${s.nomor_surat}.pdf`)"
                            class="mt-2 text-indigo-600 hover:text-indigo-700"
                        >
                            Download PDF
                        </button>
                    </div>
                </div>
                <p v-else class="text-gray-500">Tidak ada surat</p>
            </div>
        </div>

        <button
            @click="logout"
            class="mt-8 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
        >
            Logout
        </button>
    </div>
</template>

<script setup lang="ts">
definePageMeta({
    middleware: "auth",
});

const { logout } = useAuth();
const { biodata, surat, getSurat, downloadSurat } = useMember();

const keyword = ref("");

onMounted(async () => {
    await getSurat();
});

const searchSurat = async () => {
    if (keyword.value) {
        await getSurat(keyword.value);
    } else {
        await getSurat();
    }
};
</script>
```

### 8. Auth Middleware

File: `middleware/auth.ts`

```typescript
export default defineNuxtRouteMiddleware((to, from) => {
    const token = useCookie("api_token");

    if (!token.value) {
        return navigateTo("/login");
    }
});
```

---

## 🧪 Testing di Nuxt.js

```typescript
// Di terminal / console
import { useAuth } from "#app";

const auth = useAuth();

// Login
await auth.login({
    id_jemaat: "31051990",
    password: "12345",
});

// Get member data
const member = useMember();
await member.getBiodata();
console.log(member.biodata);

// Get surat
await member.getSurat();
console.log(member.surat);

// Search surat
await member.getSurat("nikah");

// Download surat
await member.downloadSurat(1, "surat_nikah.pdf");

// Logout
await auth.logout();
```

---

## 📋 Checklist Setup

- [ ] Install `axios` di Nuxt.js
- [ ] Buat `plugins/api.ts`
- [ ] Buat `composables/useAuth.ts`
- [ ] Buat `composables/useMember.ts`
- [ ] Buat `types/api.ts`
- [ ] Buat login page
- [ ] Buat dashboard page
- [ ] Buat auth middleware
- [ ] Setup `.env.local` dengan API URL
- [ ] Test login
- [ ] Test biodata endpoints
- [ ] Test surat endpoints
