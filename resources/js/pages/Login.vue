<template>
  <div class="auth-content">
    <h1 class="auth-title">Log in to your account</h1>
    <p class="auth-subtitle">Enter your email and password below to log in</p>

    <form class="auth-form">
      <label class="form-field">
        <span class="form-field__label">Email</span>
        <input type="email" required autocomplete="email"/>
      </label>
      <label class="form-field">
        <span class="form-field__label">Password</span>
        <input type="password" required autocomplete="current-password"/>
      </label>
      <button type="submit" class="auth-submit">
        Log in
      </button>
    </form>

    <p class="auth-footer">
      Don't have an account?
      <router-link :to="{ name: 'register' }">Register</router-link>
    </p>
  </div>
</template>
