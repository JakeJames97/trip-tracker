<template>
  <div class="auth-content">
    <h1 class="auth-title">Log in to your account</h1>
    <p class="auth-subtitle">Enter your email/username and password below to log in</p>

    <form @submit.prevent="onSubmit" class="auth-form">
      <FormField name="login" label="Email/Username" />
      <FormField name="password" label="Password" type="password" />

      <p v-if="generalError" class="auth-error">{{ generalError }}</p>

      <BaseButton type="submit" :disabled="isSubmitting">
        {{ isSubmitting ? 'Logging in…' : 'Log In' }}
      </BaseButton>
    </form>

    <p class="auth-footer">
      Don't have an account?
      <router-link :to="{ name: 'register' }">Register</router-link>
    </p>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/yup';
import * as yup from 'yup';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/useAuthStore.ts';
import FormField from '@/components/FormField.vue';
import BaseButton from '@/components/BaseButton.vue';
import type { AxiosError } from 'axios';
import type {LoginCredentials} from "@/types/auth.ts";

const auth = useAuthStore();
const router = useRouter();
const generalError = ref<string | null>(null);

const schema = toTypedSchema(
  yup.object({
    login: yup.string().required('Email or username is required'),
    password: yup.string().required('Password is required'),
  }),
);

const { handleSubmit, isSubmitting, setErrors } = useForm({ validationSchema: schema });

const onSubmit = handleSubmit(async (values) => {
  generalError.value = null;
  try {
    await auth.login(values as unknown as LoginCredentials);
    router.push({ name: 'dashboard' });
  } catch (e) {
    const err = e as AxiosError<{ message?: string; errors?: Record<string, string[]> }>;
    if (err.response?.status === 422 && err.response?.data?.errors) {
      const serverErrors: Record<string, string> = {};
      for (const [field, messages] of Object.entries(err.response.data.errors)) {
        serverErrors[field] = messages[0];
      }
      setErrors(serverErrors);
    } else {
      generalError.value = err.response?.data?.message ?? 'Login failed';
    }
  }
});
</script>
