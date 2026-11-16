<script setup>
import {Link} from "@inertiajs/vue3";
import SiteLayout from "../Layouts/SiteLayout.vue";
import ContactForm from "../Components/ContactForm.vue";


const backends = [
    {name: "PHP", icon: "/icons/skills/php.svg"},
    {name: "Laravel", icon: "/icons/skills/laravel.svg"},
    {name: "MySQL", icon: "/icons/skills/mysql.svg"},
    {name: "PDO", icon: "/icons/skills/php.svg"},];
const frontends = [
    {name: "HTML", icon: "/icons/skills/html5.svg"},
    {name: "CSS", icon: "/icons/skills/css.svg"}
];
const tools = [
    {name: "Git", icon: "/icons/skills/git.svg"},
    {name: "GitHub", icon: "/icons/skills/github.svg"},
    {name: "PhpStorm", icon: "/icons/skills/phpstorm.svg"},
    {name: "diagrams.net", icon: "/icons/skills/diagramsdotnet.svg"},
    {name: "Postman", icon: "/icons/skills/postman.svg"},
    {name: "Railway", icon: "/icons/skills/railway.svg"},
];

</script>
<template>
    <SiteLayout>
        <section class="hero home-section">
            <div class="hero__content">
                <h1>Junior Backend Developer</h1>
                <span class="hero__stack">PHP & Laravel</span>
                <p>I build web applications with PHP and Laravel and am looking for my first backend development role. I
                    enjoy investigating why something doesn't work, understanding the cause, and finding a solution.</p>
            </div>
            <div class="hero__actions">
                <Link href="/projects" class="button button--primary">View projects</Link>
                <a href="#contact" class="button button--secondary">Get in touch</a>
            </div>
        </section>
        <section class="about home-section">
            <div class="about__content">
                <h2>About</h2>
                <p>My background is in CNC operation and programming, including self-taught CAD/CAM work with bSolid. I’m
                    now moving into web development, building on my PHP backend training at Algebra and practical projects
                    using PHP, Laravel and MySQL.</p>
                <p>I enjoy investigating problems and understanding how to make things work. I’m looking for my first
                    backend development role while learning Vue and working towards full-stack development.</p>
            </div>
            <aside class="cv-card">
                <span class="cv-card__badge" aria-hidden="true">CV</span>
                <h3>Curriculum vitae</h3>
                <p>Available upon request.</p>
                <a href="#contact" class="button button--secondary">Request CV</a>
            </aside>
        </section>
        <section class="skills home-section">
            <h2>Skills</h2>
            <div class="skills__grid">
                <div class="skill-card">
                    <h3>Backend</h3>
                    <ul class="skills__list">
                        <li v-for="backend in backends" :key="backend.name">
                            <span class="skill-icon" aria-hidden="true" v-if="backend.icon"
                                :style="{ maskImage: 'url(' + backend.icon + ')'}"></span>
                            {{ backend.name }}
                        </li>
                    </ul>
                </div>
                <div class="skill-card">
                    <h3>Frontend</h3>
                    <ul class="skills__list">
                        <li v-for="frontend in frontends" :key="frontend.name">
                            <span class="skill-icon" aria-hidden="true" v-if="frontend.icon"
                                :style="{ maskImage: 'url(' + frontend.icon + ')'}"></span>
                            {{ frontend.name }}
                        </li>
                    </ul>
                </div>
                <div class="skill-card">
                    <h3>Tools and platforms</h3>
                    <ul class="skills__list">
                        <li v-for="tool in tools" :key="tool.name">
                            <span class="skill-icon" aria-hidden="true" v-if="tool.icon"
                                :style="{ maskImage: 'url(' + tool.icon + ')'}"></span>
                            {{ tool.name }}
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <section id="contact" class="contact home-section">
            <h2>Contact</h2>
            <ContactForm></ContactForm>
            <p>Or email me at: <a href="mailto:mario.lasic@proton.me">mario.lasic@proton.me</a></p>
        </section>
    </SiteLayout>
</template>
