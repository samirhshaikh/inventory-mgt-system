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
                        class="text-xl pt-2 ml-1 w-full"
                        :class="{
                            'text-product-color-lighter': dark_mode,
                            'text-product-color': !dark_mode
                        }"
                    >
                        Return Item
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
                            @click.native="save"
                            :icon="saving_data ? 'sync-alt' : 'check'"
                            :icon_class="saving_data ? 'fa-spin' : ''"
                            split="border-white"
                            class="ml-1"
                            :class="{
                                'bg-green-600': valid_data,
                                'bg-gray-600 text-gray-500 cursor-not-allowed': !valid_data
                            }"
                        >
                            Submit
                        </Button>
                    </div>
                </div>

                <form class="w-full pl-2" autocomplete="off" v-if="!loading">
                    <div class="flex flex-wrap -mx-3 form_field_container">
                        <div class="w-full px-3 mb-6 md:mb-0">
                            <label
                                class="block form_field_label"
                                :class="{
                                   'text-gray-700': !dark_mode,
                                   'text-white': dark_mode
                               }"
                            >
                                Invoice No
                            </label>
                            <label
                                class="block form_value_label"
                                :class="{
                                   'text-gray-600': !dark_mode,
                                   'text-product-color-lighter': dark_mode
                                }"
                            >
                                {{ SalesInvoiceNo }}
                            </label>
                        </div>
                    </div>


                    <div class="flex flex-wrap -mx-3 form_field_container">
                        <div class="w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                IMEI
                            </label>
                            <label
                                class="block form_value_label"
                                :class="{
                                    'text-gray-600': !dark_mode,
                                    'text-product-color-lighter': dark_mode
                                }"
                            >
                                {{ IMEI }}
                            </label>
                        </div>

                        <div class="w-1/2 px-3 mb-6 md:mb-0">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Returned Date
                            </label>

                            <CustomDatePicker
                                :start_date_value="ReturnedDate"
                                v-bind:required_field="true"
                                @dateSelected="dateSelected"
                                @clearDate="clearDate"
                            ></CustomDatePicker>
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-3 form_field_container">
                        <div class="w-full px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Comments
                            </label>
                            <textarea
                                class="w-3/4 generic_input"
                                v-model.trim="Comments"
                                rows="3"
                            />
                        </div>
                    </div>
                </form>

                <Loading v-else />
            </div>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from "vuex";
import moment from "moment";
import Button from "../components/Button";
import {notifications} from "../Helpers/notifications";

export default {
    mixins: [notifications],

    props: {
        SalesInvoiceId: {
            type: Number,
            required: true
        },
        SalesInvoiceNo: {
            type: String,
            required: true
        },
        IMEI: {
            type: String,
            required: true
        },
        refresh: {
            type: Function
        }
    },

    data() {
        return {
            Comments: "",
            ReturnedDate: moment().format("D-MMM-YYYY"),

            saving_data: false,
            loading: false
        };
    },

    computed: {
        valid_data() {
            if (
                this.ReturnedDate == ""
            ) {
                return false;
            }

            return true;
        },

        ...mapState({
            dark_mode: state => state.framework.dark_mode,
            expanded_sidebar: state => state.framework.expanded_sidebar
        })
    },

    mounted() {

    },

    methods: {
        save() {
            //Validate
            if (!this.valid_data) {
                return false;
            }

            let data = {};
            data.InvoiceId = this.SalesInvoiceId;
            data.IMEI = this.IMEI;
            data.Comments = this.Comments;
            data.ReturnedDate = this.ReturnedDate;

            this.saving_data = true;

            axios
                .post(route("sale.return-item"), data)
                .then(response => {
                    this.saving_data = false;

                    if (response.data.message == "record_saved") {
                        this.$notify({
                            group: "messages",
                            title: "Success",
                            text: "Item returned successfully."
                        });

                        this.refresh(this.IMEI);

                        this.$modal.hide(this.$parent.name);
                    } else {
                        this.$notify({
                            group: "messages",
                            title: "Error",
                            type: "error",
                            text: this.formatMessage()
                        });
                    }
                })
                .catch(error => {
                    this.saving_data = false;

                    if (error.response.data.message == "record_not_found") {
                        this.$notify({
                            group: "messages",
                            title: "Error",
                            type: "error",
                            text: this.formatMessage(error.response.data.message, 'Item')
                        });
                    } else {
                        this.$notify({
                            group: "messages",
                            title: "Error",
                            type: "error",
                            text: this.formatMessage()
                        });
                    }
                });
        },

        dateSelected(date) {
            if (date != '' && date != null) {
                this.ReturnedDate = date;
            } else {
                this.ReturnedDate = "";
            }
        },

        clearDate() {
            this.ReturnedDate = "";
        },

        ...mapActions({
            refreshData: "framework/refreshData",
            addError: "errors/addError"
        })
    }
};
</script>
