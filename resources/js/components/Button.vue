<template>
<button class="flex rounded items-center cursor-pointer hover:opacity-75" @click.stop="stopClick()">
    <div class="p-1 text-xs" v-if="icon">
        <FA class="fa-fw" :icon="['fas', icon]" :class="icon_class"></FA>
    </div>
    <div class="p-1 text-sm w-full truncate hidden md:block" :class="labelClass" v-if="this.$slots.default">
        <slot></slot>
    </div>
</button>
</template>

<script>
export default {
    name: "Button",

    props: ['icon', 'split', 'icon_class'],

    methods: {
        stopClick() {

        }
    },

    computed: {
        labelClass() {
            if (this.split && this.icon) {
                return [
                    'border-l',
                    this.split
                ];
            } else {
                return '';
            }
        }
    }
}
</script>
