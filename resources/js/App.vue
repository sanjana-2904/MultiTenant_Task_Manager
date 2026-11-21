
<script setup>
import { reactive, ref } from 'vue';
import { DxTextBox } from 'devextreme-vue/text-box';
import { DxButton } from 'devextreme-vue/button';

const form = reactive({
    companyName: '',
    ownerName: '',
    email: '',
    password: '',
    confirmPassword: '',
});

const errors = reactive({});
const successMessage = ref('');
const submitting = ref(false);

const validateForm = () => {
    Object.keys(errors).forEach(key => delete errors[key]);
    successMessage.value = '';

    if (!form.companyName.trim()) {
        errors.companyName = 'Company name is required.';
    }

    if (!form.ownerName.trim()) {
        errors.ownerName = 'Owner name is required.';
    }

    if (!form.email.trim()) {
        errors.email = 'Email address is required.';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Please enter a valid email address.';
    }

    if (!form.password) {
        errors.password = 'Password is required.';
    } else if (form.password.length < 8) {
        errors.password = 'Password must be at least 8 characters.';
    }

    if (!form.confirmPassword) {
        errors.confirmPassword = 'Please confirm your password.';
    } else if (form.password !== form.confirmPassword) {
        errors.confirmPassword = 'Passwords do not match.';
    }

    return Object.keys(errors).length === 0;
};

const submitRegistration = async () => {
    if (!validateForm()) {
        return;
    }

    submitting.value = true;
    successMessage.value = '';

    try {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');

        if (!csrfToken) {
            throw new Error('CSRF token not found.');
        }

        const response = await fetch('/company/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(form),
        });

        const data = await response.json();

        if (!response.ok) {
            if (response.status === 422 && data.errors) {
                Object.keys(data.errors).forEach(key => {
                    errors[key] = data.errors[key][0];
                });
            } else {
                successMessage.value =
                    data.message || 'Registration failed.';
            }

            return;
        }

        successMessage.value = data.message;

    } catch (error) {
        console.error('Registration error:', error);
        successMessage.value =
            'Something went wrong. Please try again.';
    } finally {
        submitting.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen grid md:grid-cols-2 bg-white">

        <!-- Left Section -->
        <div class="hidden md:flex flex-col justify-between bg-slate-900 text-white p-12">

            <div>
                <h1 class="text-3xl font-bold">
                    TaskManager
                </h1>

                <p class="text-slate-400 mt-2">
                    Manage your team. Manage your work.
                </p>
            </div>

            <div>
                <h2 class="text-4xl font-bold leading-tight">
                    Bring your team
                    <br>
                    and tasks together.
                </h2>

                <p class="text-slate-400 mt-5 text-lg">
                    A smarter way to manage your company's
                    tasks, employees and productivity.
                </p>
            </div>

            <p class="text-slate-500 text-sm">
                © 2026 TaskManager. All rights reserved.
            </p>

        </div>

        <!-- Right Section -->
        <div class="flex items-center justify-center p-6 md:p-12">

            <div class="w-full max-w-md">

                <h2 class="text-3xl font-bold text-slate-900">
                    Create your account
                </h2>

                <p class="text-slate-500 mt-2 mb-8">
                    Register your company to get started.
                </p>

                <form class="space-y-5" @submit.prevent="submitRegistration">

                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Company Name
                        </label>

                        <DxTextBox
                            v-model:value="form.companyName"
                            placeholder="Enter company name"
                        />

                        <p v-if="errors.companyName" class="text-red-600 text-sm mt-1">
                            {{ errors.companyName }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Owner Name
                        </label>

                        <DxTextBox
                            v-model:value="form.ownerName"
                            placeholder="Enter your name"
                        />

                        <p v-if="errors.ownerName" class="text-red-600 text-sm mt-1">
                            {{ errors.ownerName }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Email Address
                        </label>

                        <DxTextBox
                            v-model:value="form.email"
                            placeholder="Enter email address"
                        />

                        <p v-if="errors.email" class="text-red-600 text-sm mt-1">
                            {{ errors.email }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Password
                        </label>

                        <DxTextBox
                            v-model:value="form.password"
                            mode="password"
                            placeholder="Enter password"
                        />

                        <p v-if="errors.password" class="text-red-600 text-sm mt-1">
                            {{ errors.password }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Confirm Password
                        </label>

                        <DxTextBox
                            v-model:value="form.confirmPassword"
                            mode="password"
                            placeholder="Confirm password"
                        />

                        <p v-if="errors.confirmPassword" class="text-red-600 text-sm mt-1">
                            {{ errors.confirmPassword }}
                        </p>
                    </div>

                    <p v-if="successMessage" class="text-green-600 text-sm">
                        {{ successMessage }}
                    </p>

                    <DxButton
                        text="Create Account"
                        type="default"
                        styling-mode="contained"
                        width="100%"
                        :disabled="submitting"
                        @click="submitRegistration"
                    />

                </form>

                <p class="text-center text-sm text-slate-500 mt-6">
                    Already have an account?
                    <span class="text-blue-600 font-semibold">
                        Sign in
                    </span>
                </p>

            </div>

        </div>

    </div>
</template>
