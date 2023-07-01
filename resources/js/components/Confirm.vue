<template>
    <div class="p-2">
        <h2
            class="border-b border-gray-400 text-gray-700 text-base text-center w-full pb-2 capitalize"
        >
            {{ title }}
        </h2>

        <div class="mt-4 px-1 text-sm text-gray-600">
            {{ text }}
        </div>

        <div class="flex flex-row items-stretch w-full mt-4 text-white">
            <Button
                :icon="no_button.icon"
                split="border-white"
                class="flex-1"
                :class="no_button.icon_class"
                @click.native="close"
            >
                {{ no_button.title }}
            </Button>
            <Button
                :icon="yes_button.icon"
                split="border-white"
                class="flex-1 ml-2"
                :class="yes_button.icon_class"
                @click.native="click($event)"
            >
                {{ yes_button.title }}
            </Button>
        </div>
    </div>
</template>

<script>
export default {
    name: "Confirm",

    props: {
        title: {
            type: String,
            default: "Confirm",
        },
        text: {
            type: String,
            default: "",
            required: true,
        },
        no_button: {
            type: Object,
            default: () => ({
                title: "No",
                icon: "times",
                icon_class: "bg-red-600",
            }),
        },
        yes_button: {
            type: Object,
            default: () => ({
                title: "Yes",
                icon: "check",
                icon_class: "bg-green-600",
            }),
        },
        yes_handler: {
            type: Function,
        },
    },

    methods: {
        close() {
            this.$modal.hide(this.$parent.name);
        },

        click(event, source = "click") {
            const handler = this.yes_handler;
            if (typeof handler === "function") {
                handler(event, { source });

                this.$modal.hide(this.$parent.name);
            }
        },
    },
};
</script>
