<template>
  <div class="auth-content">
    <h1 class="auth-title">Register Your Account</h1>
    <p class="auth-subtitle">Enter your username, email and password below to register</p>

    <form class="auth-form">
      <label class="form-field">
        <span class="form-field__label">Username</span>
        <input type="text" required />
      </label>
      <label class="form-field">
        <span class="form-field__label">Email</span>
        <input type="email" required />
      </label>
      <label class="form-field">
        <span class="form-field__label">Password</span>
        <input type="password" required />
      </label>
      <button type="submit" class="auth-submit">
        Register
      </button>
    </form>

    <p class="auth-footer">
      Already have an account?
      <router-link :to="{ name: 'login' }">Login</router-link>
    </p>
  </div>
</template>
