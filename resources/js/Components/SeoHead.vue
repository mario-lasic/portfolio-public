<script setup>
import {Head} from '@inertiajs/vue3';

defineProps({seo: {type: Object, required: true}});
</script>

<template>
    <Head :title="seo.title">
        <meta name="description" :content="seo.description" head-key="description">
        <link rel="canonical" :href="seo.canonical" head-key="canonical">
        <meta property="og:type" content="website" head-key="og:type">
        <meta property="og:site_name" content="Mario Lasić — Portfolio" head-key="og:site_name">
        <meta property="og:locale" content="en_US" head-key="og:locale">
        <meta property="og:title" :content="seo.title" head-key="og:title">
        <meta property="og:description" :content="seo.description" head-key="og:description">
        <meta property="og:url" :content="seo.canonical" head-key="og:url">
        <meta property="og:image" :content="seo.image" head-key="og:image">
        <meta property="og:image:width" content="1200" head-key="og:image:width">
        <meta property="og:image:height" content="630" head-key="og:image:height">
        <meta property="og:image:alt" content="Mario Lasić — Junior Backend Developer, PHP &amp; Laravel" head-key="og:image:alt">
        <meta name="twitter:card" content="summary_large_image" head-key="twitter:card">
        <meta name="twitter:title" :content="seo.title" head-key="twitter:title">
        <meta name="twitter:description" :content="seo.description" head-key="twitter:description">
        <meta name="twitter:image" :content="seo.image" head-key="twitter:image">
        <meta name="twitter:image:alt" content="Mario Lasić — Junior Backend Developer, PHP &amp; Laravel" head-key="twitter:image:alt">
    </Head>
</template>
