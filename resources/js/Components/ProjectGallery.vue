<script setup>
import {computed, ref} from "vue";

const props = defineProps({
    images: Array
});
const currentIndex = ref(0);
const currentImage = computed(() => {
    return props.images[currentIndex.value];
});

function nextImage() {
    if (currentIndex.value < props.images.length - 1) {
        currentIndex.value++;
    }
}

function previousImage() {
    if (currentIndex.value > 0) {
        currentIndex.value--;
    }
}

const previousDisabled = computed(() => {
    return currentIndex.value <= 0;
});
const nextDisabled = computed(() => {
    return currentIndex.value >= props.images.length - 1;
});

</script>

<template>
    <div class="project-gallery" v-if="images.length > 0">
        <figure>
            <div class="project-gallery__frame">
                <img :src="currentImage.src" :alt="currentImage.alt"
                    :width="currentImage.width" :height="currentImage.height" class="project-gallery__image">
            </div>
            <figcaption class="project-gallery__caption" aria-live="polite" aria-atomic="true">{{ currentIndex + 1 }} of
                {{ images.length }} -
                {{ currentImage.caption }}
            </figcaption>
        </figure>
        <div class="project-gallery__buttons" v-if="images.length > 1">
            <button type="button" class="button button--secondary" @click="previousImage()"
                :aria-disabled="previousDisabled">Previous
            </button>
            <button type="button" class="button button--secondary" @click="nextImage()"
                :aria-disabled="nextDisabled">Next
            </button>
        </div>
    </div>
</template>
