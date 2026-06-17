import eslint from '@eslint/js';
import eslintPluginVue from 'eslint-plugin-vue';
import typescriptEslint from 'typescript-eslint';
import globals from 'globals';

export default typescriptEslint.config(
    {
        files: ['resources/js/**/*.{ts,vue}'],
        extends: [
            eslint.configs.recommended,
            ...typescriptEslint.configs.recommended,
            ...eslintPluginVue.configs['flat/recommended'],
        ],
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'module',
            globals: globals.browser,
            parserOptions: {
                parser: typescriptEslint.parser,
            },
        },
      rules: {
        "vue/max-attributes-per-line": ["error", {
          "singleline": {
            "max": 4
          },
          "multiline": {
            "max": 1
          }
        }],
        "vue/singleline-html-element-content-newline": false
      }
    },
    {
        files: ['vite.config.ts'],
        extends: [eslint.configs.recommended, ...typescriptEslint.configs.recommended],
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'module',
            globals: globals.node,
        },
    },
);
