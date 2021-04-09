<template>
    <div class="w-full p-4 border-l-4" :class="classes" role="alert">
        <p class="font-bold mb-2">{{ title }}</p>
        <p class="text-sm mb-0"><slot></slot></p>
    </div>
</template>

<script>
export default {
    props: [
        'type'
    ],

    data() {
        return {
            alert_types: {
                info: {
                    title: 'Information',
                    classes: 'bg-blue-100 border-blue-500 text-blue-700'
                },
                success: {
                    title: 'Success',
                    classes: 'bg-green-100 border-green-500 text-green-700'
                },
                warning: {
                    title: 'Warning',
                    classes: 'bg-orange-100 border-orange-500 text-orange-700'
                },
                error: {
                    title: 'Error',
                    classes: 'bg-red-100 border-red-500 text-red-700'
                }
            }
        }
    },

    computed: {
        classes() {
            return this.alert_types[this.type].classes;
        },

        title() {
            return this.alert_types[this.type].title;
        }
    }
}
</script>