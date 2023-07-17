<template></template>

<script>
export default {
    props: {
        options: {
            type: Array,
            default: [],
        },
        value: {
            type: String,
            default: "",
        },
        onChange: {
            type: Function,
        },
    },
};
</script>
