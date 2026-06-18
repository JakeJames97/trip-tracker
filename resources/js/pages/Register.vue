<template>
  <div class="auth-content">
    <h1 class="auth-title">Register Your Account</h1>
    <p class="auth-subtitle">Enter your username, email and password below to register</p>

    <form @submit.prevent="onSubmit" class="auth-form">
      <FormField name="username" label="Username" />
      <FormField name="email" label="Email" type="email" />
      <FormField name="password" label="Password" type="password" />

      <p v-if="generalError" class="auth-error">{{ generalError }}</p>

      <BaseButton type="submit" :disabled="isSubmitting">
        {{ isSubmitting ? 'Registering…' : 'Register' }}
      </BaseButton>
    </form>

    <p class="auth-footer">
      Already have an account?
      <router-link :to="{ name: 'login' }">Login</router-link>
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
import type { AxiosError } from 'axios';
import FormField from "@/components/FormField.vue";
import BaseButton from "@/components/BaseButton.vue";

const auth = useAuthStore();
const router = useRouter();
const generalError = ref<string | null>(null);

const schema = toTypedSchema(
  yup.object({
    username: yup.string().required('Username is required'),
    email: yup.string().required('Email is required').email('Enter a valid email address'),
    password: yup.string().required('Password is required').min(8, 'Password must be at least 8 characters'),
  }),
);

const { defineField, handleSubmit, isSubmitting, setErrors } = useForm({
  validationSchema: schema,
});

const onSubmit = handleSubmit(async (values) => {
  generalError.value = null;

  try {
    await auth.register(values);
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
      generalError.value = err.response?.data?.message ?? 'Something went wrong. Please try again.';
    }
  }
});
</script>
