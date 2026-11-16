<script setup>
import {useForm} from "@inertiajs/vue3";
import {nextTick, useTemplateRef} from "vue";

const form = useForm({
    name: "",
    email: "",
    message: "",
    website: ""
});

const nameInput = useTemplateRef('nameInput');
const emailInput = useTemplateRef('emailInput');
const messageInput = useTemplateRef('messageInput');

function submitContact() {
    form.clearErrors('delivery')
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onNetworkError: () => {
            form.setError(
                'delivery',
                'Network error. Please check your connection and try again.'
            );
            return false;

        },
        onError: async (errors) => {
            await nextTick();

            if (errors.name) {
                nameInput.value?.focus();
            } else if (errors.email) {
                emailInput.value?.focus();
            } else if (errors.message) {
                messageInput.value?.focus();
            }
        },
    });
}
</script>

<template>
    <form class="form" @submit.prevent="submitContact()">
        <div class="form__group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" placeholder="Your name" autocomplete="name" required
                v-model="form.name" :aria-invalid="form.errors.name ? 'true' : 'false'"
                :aria-describedby="form.errors.name ? 'name-error' : null" ref="nameInput">
            <p id="name-error" class="form__error" v-if="form.errors.name">{{ form.errors.name }}</p>
        </div>
        <div class="form__group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Your email" autocomplete="email" required
                v-model="form.email" :aria-invalid="form.errors.email ? 'true' : 'false'"
                :aria-describedby="form.errors.email ? 'email-error' : null" ref="emailInput">
            <p id="email-error" class="form__error" v-if="form.errors.email">{{ form.errors.email }}</p>
        </div>
        <div class="form__group form__group--full">
            <label for="message">Message</label>
            <textarea name="message" id="message" cols="30" rows="10" placeholder="Enter your message..." required
                v-model="form.message" :aria-invalid="form.errors.message ? 'true' : 'false'"
                :aria-describedby="form.errors.message ? 'message-error' : null" ref="messageInput"></textarea>
            <p id="message-error" class="form__error" v-if="form.errors.message">{{ form.errors.message }}</p>
        </div>
        <div class="form__honeypot">
            <label for="website">Leave this field empty</label>
            <input type="text" name="website" id="website" v-model="form.website" tabindex="-1" autocomplete="off">
        </div>
        <button type="submit" class="button button--primary" :disabled="form.processing">
            {{ form.processing ? 'Sending…' : 'Send message' }}
        </button>
        <p v-if="form.recentlySuccessful" role="status">Thanks! Your message has been sent.</p>
        <p v-if="form.errors.delivery" role="alert" class="form__error">{{ form.errors.delivery }}</p>
    </form>
</template>
