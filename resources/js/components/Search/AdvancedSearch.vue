<template>
    <div
        class="flex h-full border border-product-color"
        :class="{
            'bg-gray-700 text-white': dark_mode
        }"
    >
        <div class="flex-grow flex flex-col justify-between">
            <div class="p-4 overflow-y-auto text-sm flex-grow">
                <div
                    class="flex border-b border-product-color-lighter mb-4 pb-1"
                    :class="{
                        'border-product-color-lighter': dark_mode,
                        'border-product-color': !dark_mode
                    }"
                >
                    <h1
                        class="text-base md:text-xl pt-2 ml-1 w-full"
                        :class="{
                            'text-product-color-lighter': dark_mode,
                            'text-product-color': !dark_mode
                        }"
                    >
                        Advanced Search
                    </h1>
                    <div
                        class="float-right flex justify-end mr-2 text-white w-64"
                    >
                        <Button
                            @click.native="$emit('close')"
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
                                'bg-gray-600 text-gray-500 cursor-not-allowed': !valid_search
                            }"
                        >
                            Search
                        </Button>
                    </div>
                </div>

                <form class="w-full pl-2" autocomplete="off" @submit.prevent>
                    <div
                        class="flex flex-wrap"
                    >
                        <div
                            class="w-1/2 form_field_container"
                            v-for="column in columns"
                        >
                            <label
                                class="block form_field_label capitalize"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
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
                                @dateSelected="dateSelected(column.key, ...arguments)"
                                @clearDate="clearDate(column.key)"
                                v-if="column.type == 'date'"
                            ></CustomDatePicker>

                            <v-select
                                :label="column.label"
                                :options="column.data"
                                class="generic_vs_select"
                                :class="column.class"
                                @input="updateTimer"
                                v-model="column_data[column.key]"
                                v-if="!loading_suppliers && column.type == 'list'"
                                :filterable="false"
                            >
                                <template v-slot:option="option">
                                    <strong>{{ option[column.label] }}</strong>
                                    <p v-if="option.ContactNo1 || option.ContactNo2" class="m-0 p-0">
                                        {{ option.ContactNo1 ? option.ContactNo1 : option.ContactNo2 }}
                                    </p>
                                </template>
                            </v-select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import {mapActions, mapState} from "vuex";
import moment from "moment";
import {list_controller} from "../../Helpers/list_controller";

export default {
    name: "AdvancedSearch",

    props: {
        columns: {
            type: Array,
            default: () => ([])
        },
        triggerAdvancedSearch: {
            type: Function
        }
    },

    mixins: [list_controller],

    data() {
        return {
            timer: moment().format(),
            column_data: {}
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
                        if (this.column_data[key] != '') {
                            search_data_present = true;
                            break;
                        }
                    }
                }

                return search_data_present;
            }
        },

        ...mapState({
            dark_mode: state => state.framework.dark_mode,
            expanded_sidebar: state => state.framework.expanded_sidebar,
            local_settings: state => state.local_settings
        })
    },

    methods: {
        dateSelected(key, date) {
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
                this.triggerAdvancedSearch(this.column_data);

                this.$modal.hide(this.$parent.name);
            }
        },
    },
}
</script>
