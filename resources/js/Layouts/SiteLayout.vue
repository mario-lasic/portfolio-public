<script setup>
import {usePage} from '@inertiajs/vue3';
import SeoHead from '../Components/SeoHead.vue';

const page = usePage();
import SiteHeader from "../Components/SiteHeader.vue";
import SiteFooter from "../Components/SiteFooter.vue";
</script>
<template>
    <SeoHead v-if="page.props.seo" :seo="page.props.seo" />
    <a href="#main-content" class="skip-link">Skip to content</a>
    <SiteHeader></SiteHeader>
    <main id="main-content" class="site-container">
        <slot></slot>
    </main>
    <SiteFooter></SiteFooter>
</template>
