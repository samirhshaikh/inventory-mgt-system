<template></template>

<script>
export default {
    name: "Window",

    data() {
        return {
            window: {
                width: 0,
                height: 0,
            },
        };
    },

    created() {
        window.addEventListener("resize", this.handleResize);
        this.handleResize();
    },

    destroyed() {
        window.removeEventListener("resize", this.handleResize);
    },

    methods: {
        handleResize() {
            this.window.width = window.innerWidth;
            this.window.height = window.innerHeight;

            this.$emit("resizeWindow", this.window.width);
        },
    },
};
</script>

<style scoped></style>
