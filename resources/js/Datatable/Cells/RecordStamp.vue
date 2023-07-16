<template>
    <div class="w-full md:w-1/2 form_field_container">
        <label
            class="form_field_label"
            :class="{
                'text-gray-700': !dark_mode,
                'text-white': dark_mode,
            }"
        >
            Created By
        </label>
        <label
            class="form_value_label"
            :class="{
                'text-gray-600': !dark_mode,
                'text-product-color-lighter': dark_mode,
            }"
        >
            {{ getColumnValue("CreatedBy") }}
        </label>
    </div>

    <div class="w-full md:w-1/2 form_field_container">
        <label
            class="form_field_label"
            :class="{
                'text-gray-700': !dark_mode,
                'text-white': dark_mode,
            }"
        >
            Creation Date
        </label>
        <label
            class="form_value_label"
            :class="{
                'text-gray-600': !dark_mode,
                'text-product-color-lighter': dark_mode,
            }"
        >
            {{ getColumnValue("CreatedDate") }}
        </label>
    </div>

    <div class="w-full md:w-1/2 form_field_container">
        <label
            class="form_field_label"
            :class="{
                'text-gray-700': !dark_mode,
                'text-white': dark_mode,
            }"
        >
            Updated By
        </label>
        <label
            class="block form_value_label"
            :class="{
                'text-gray-600': !dark_mode,
                'text-product-color-lighter': dark_mode,
            }"
        >
            {{ getColumnValue("UpdatedBy") }}
        </label>
    </div>
    <div class="w-full md:w-1/2 form_field_container">
        <label
            class="form_field_label"
            :class="{
                'text-gray-700': !dark_mode,
                'text-white': dark_mode,
            }"
        >
            Updated Date
        </label>
        <label
            class="block form_value_label"
            :class="{
                'text-gray-600': !dark_mode,
                'text-product-color-lighter': dark_mode,
            }"
        >
            {{ getColumnValue("UpdatedDate") }}
        </label>
    </div>
</template>

<script>
import { mapState } from "vuex";

export default {
    props: {
        row: {},
    },

    computed: {
        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
            expanded_sidebar: (state) => state.framework.expanded_sidebar,
            local_settings: (state) => state.local_settings,
        }),
    },

    methods: {
        getColumnValue(column) {
            return _.get(this.row, column);
        },
    },
};
</script>
