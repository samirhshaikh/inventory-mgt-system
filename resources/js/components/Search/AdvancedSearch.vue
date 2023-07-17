<template>
    <VueFinalModal
        class="flex justify-center items-center"
        :content-class="[
            'advanced_search_modal relative p-4 rounded-lg dark:bg-gray-900',
            {
                'bg-gray-700': dark_mode,
                'bg-white': !dark_mode,
            },
        ]"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
    >
        <div class="p-0 text-sm">
            <div
                class="datatable_header"
                :class="{
                    'border-product-color-lighter': dark_mode,
                    'border-product-color': !dark_mode,
                }"
            >
                <h1
                    :class="{
                        'text-product-color-lighter': dark_mode,
                        'text-product-color': !dark_mode,
                    }"
                >
                    Advanced Search
                </h1>
                <div class="search_bar_container">
                    <Button
                        @click.native="$emit('closed')"
                        icon="times"
                        split="border-white"
                        class="bg-red-600"
                    >
                        Close
                    </Button>
                    <Button
                        @click.native="searchData"
                        icon="search"
                        split="border-white"
                        class="ml-1"
                        :class="{
                            'bg-green-600': valid_search,
                            'bg-gray-600 text-gray-500 cursor-not-allowed':
                                !valid_search,
                        }"
                    >
                        Search
                    </Button>
                </div>
            </div>

            <form class="w-full pl-2" autocomplete="off" @submit.prevent>
                <div class="flex flex-wrap items-start">
                    <div
                        class="w-full md:w-1/2 form_field_container"
                        v-for="column in columns"
                    >
                        <label
                            class="form_field_label"
                            :class="{
                                'text-gray-700': !dark_mode,
                                'text-white': dark_mode,
                            }"
                        >
                            {{ column.label }}
                        </label>

                        <input
                            type="text"
                            v-model="column_data[column.key]"
                            class="generic_input"
                            :class="column.class"
                            v-if="column.type == 'string'"
                            @keyup="updateTimer"
                            autocomplete="off"
                        />

                        <input
                            type="number"
                            v-model="column_data[column.key]"
                            class="generic_input"
                            :class="column.class"
                            v-if="column.type == 'currency'"
                            @keyup="updateTimer"
                            autocomplete="off"
                        />

                        <CustomDatePicker
                            :start_date_value="column_data[column.key]"
                            :column_name="column.key"
                            @date-selected="dateSelected"
                            v-if="column.type == 'date'"
                        ></CustomDatePicker>

                        <v-select
                            :label="column.label"
                            :options="column.data"
                            class="generic_vs_select"
                            :class="column.class"
                            @option:selected="updateTimer"
                            v-model="column_data[column.key]"
                            v-if="!loading_suppliers && column.type == 'list'"
                            :filterable="false"
                        >
                            <template v-slot:option="option">
                                <strong>{{ option[column.label] }}</strong>
                                <p
                                    v-if="
                                        option.ContactNo1 || option.ContactNo2
                                    "
                                    class="m-0 p-0"
                                >
                                    {{
                                        option.ContactNo1
                                            ? option.ContactNo1
                                            : option.ContactNo2
                                    }}
                                </p>
                            </template>
                        </v-select>
                    </div>
                </div>
            </form>
        </div>
    </VueFinalModal>
</template>

<style>
.advanced_search_modal {
    width: 750px;
    height: auto;
}
</style>

<script>
import { mapActions, mapState } from "vuex";
import { VueFinalModal } from "vue-final-modal";
import moment from "moment";
import { list_controller } from "../../Helpers/list_controller";

export default {
    name: "AdvancedSearch",

    components: {
        VueFinalModal,
    },

    props: {
        columns: {
            type: Array,
            default: () => [],
        },
        triggerAdvancedSearch: {
            type: Function,
        },
    },

    mixins: [list_controller],

    data() {
        return {
            timer: moment().format(),
            column_data: {},
        };
    },

    mounted() {
        _.forEach(this.columns, (column, key) => {
            this.column_data[column.key] = "";
        });
    },

    computed: {
        valid_search() {
            if (this.timer) {
                let search_data_present = false;

                for (let key in this.column_data) {
                    if (this.column_data.hasOwnProperty(key)) {
                        if (this.column_data[key] != "") {
                            search_data_present = true;
                            break;
                        }
                    }
                }

                return search_data_present;
            }
        },

        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
            expanded_sidebar: (state) => state.framework.expanded_sidebar,
            local_settings: (state) => state.local_settings,
        }),
    },

    methods: {
        dateSelected(date, key) {
            this.column_data[key] = date;

            this.updateTimer();
        },

        clearDate(key) {
            this.column_data[key] = "";

            this.updateTimer();
        },

        updateTimer() {
            this.timer = moment().format();
        },

        searchData() {
            if (this.valid_search) {
                const handler = this.triggerAdvancedSearch;
                if (typeof handler === "function") {
                    this.triggerAdvancedSearch(this.column_data);

                    this.$emit("closed");
                }
            }
        },
    },
};
</script>
