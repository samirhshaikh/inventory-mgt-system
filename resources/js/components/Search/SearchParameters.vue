<template>
    <section>
        <div class="flex flex-wrap">
            <div
                v-for="column in columns"
                class="mr-2 mb-2 py-2 px-2 bg-product-color rounded cursor-pointer"
                title="Click to remove"
                @click="removeColumnFromSearch(column.key)"
                v-if="getColumnData(column.key) != ''"
            >
                <div class="text-sm text-white font-semibold">
                    {{ column.label }}
                </div>
                <div class="mt-1 text-xs text-white">
                    {{ getColumnData(column.key) }}
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: "SearchParameters",

    props: {
        columns: {
            type: Array,
            default: () => [],
        },
        search_data: {
            type: Object,
            default: () => ({}),
        },
    },

    methods: {
        removeColumnFromSearch(key) {
            _.set(this.search_data, key, "");

            this.$emit("advancedSearchDataModified", this.search_data);
        },

        getColumnData(column_key) {
            let return_value = "";
            _.forEach(this.search_data, (value, key) => {
                // console.log([column_key, value, key]);
                if (
                    key == column_key &&
                    value != "" &&
                    value != null &&
                    return_value == ""
                ) {
                    return_value = value;
                }
            });
            return return_value;
        },
    },
};
</script>
