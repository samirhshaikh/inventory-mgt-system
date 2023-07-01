<template>
    <div class="text-center">
        <FA :icon="['fas', 'spinner']"></FA> {{ loading_message }}
    </div>
</template>

<script>
export default {
    props: {
        loading_message: {
            type: String,
            default: "Loading...",
        },
    },
};
</script>
