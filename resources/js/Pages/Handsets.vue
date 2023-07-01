<template>
    <Layout :title="options.record_name + 's'">
        <div class="px-4 py-4">
            <div
                class="flex items-stretch datatable_header"
                :class="{
                    'border-product-color-lighter bg-white': !dark_mode,
                    'border-product-color bg-gray-800': dark_mode,
                }"
            >
                <h1
                    class="pt-1 ml-2 text-product-color text-2xl tracking-tight w-full"
                >
                    <FA :icon="['fas', 'mobile-alt']" class="mr-1"></FA>
                    {{ options.record_name }}s
                </h1>
                <div class="mr-2 flex flex-row">
                    <SearchBar
                        :placeholder_text="options.record_name + 's'"
                        v-if="options.enable_search"
                        class="mr-1"
                        @searchData="searchData"
                    ></SearchBar>
                    <Button
                        @click.native="newRecord"
                        icon="plus"
                        split="border-white"
                        class="text-white bg-green-600 float-right"
                        >New {{ options.record_name }}
                    </Button>
                </div>
            </div>

            <HandsetsDatatable
                :columns="this.columns"
                :options="this.options"
                :search_text="this.search_text"
            ></HandsetsDatatable>
        </div>
    </Layout>
</template>

<script>
import { mapState, mapActions } from "vuex";
import lazyLoadComponent from "@/Helpers/lazyLoadComponent.js";
import loading from "@/Misc/Loading.vue";
import Handset from "../DBObjects/Handset.vue";

export default {
    props: {
        columns: {
            type: Array,
            default: () => [],
        },
        options: {
            type: Object,
            default: () => ({}),
        },
    },

    components: {
        HandsetsDatatable: lazyLoadComponent({
            componentFactory: () => import("@/Datatable/Datatable"),
            loading: loading,
        }),
    },

    data() {
        return {
            search_text: "",
        };
    },

    computed: {
        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
        }),
    },

    created() {
        this.setTableMetaData({
            columns: this.columns,
            options: this.options,
        });

        this.setActiveTab(this.options.id);
    },

    methods: {
        newRecord() {
            this.setPopperOpen(true);

            this.$modal.show(
                Handset,
                {
                    edit_id: "",
                    options: this.options,
                },
                {
                    width: "750px",
                    height: "600px",
                }
            );
        },

        searchData(search_text) {
            this.search_text = search_text;
        },

        ...mapActions({
            setTableMetaData: "datatable/setTableMetaData",
            setActiveTab: "local_settings/setActiveTab",
            setPopperOpen: "local_settings/setPopperOpen",
            addError: "errors/addError",
        }),
    },
};
</script>
