<template>
    <div class="flex flex-row items-center">
        <v-select
            :value="customer_id"
            label="CustomerName"
            :reduce="customer => customer.Id"
            :options="customer_sales"
            class="w-72 generic_vs_select"
            :loading="loading_customer_sales"
            :class="{
                required_field: required_field
            }"
            ref="customer_id"
            :filterable="false"
            @search="onSearch"
            @input="onOptionSelected"
        >
            <template v-slot:option="option">
                <strong>{{ option.CustomerName }}</strong>
                <p v-if="option.ContactNo1 || option.ContactNo2" class="m-0 p-0">
                    {{ option.ContactNo1 ? option.ContactNo1 : option.ContactNo2 }}
                </p>
            </template>
        </v-select>

        <Button
            @click.native="add"
            icon="plus"
            split="border-white"
            class="ml-1 bg-green-600 text-white"
            v-if="enable_add"
        >
            New
        </Button>
        <Button
            @click.native="edit(customer_id)"
            icon="pen"
            split="border-white"
            class="ml-1 bg-green-600 text-white"
            v-if="customer_id && enable_edit"
        >
            Edit
        </Button>
    </div>
</template>

<script>
import {mapState, mapActions} from "vuex";
import Customer from "../DBObjects/CustomerSale";
import helper_functions from "../Helpers/helper_functions";

export default {
    name: "CustomerSalesPicker",

    props: {
        selected_value: '',
        required_field: false,
        enable_add: false,
        enable_edit: false
    },

    data() {
        return {
            loading_customer_sales: false,
            customer_id: this.selected_value,
            customer_sales: [],
        }
    },

    computed: {
        refresh_customer_sales: state => state.framework.refresh_customer_sales,

        ...mapState({
            refresh_customer_sales: state => state.framework.refresh_customer_sales,
        })
    },

    mounted() {
        this.loadData();
    },

    methods: {
        onSearch(query) {
            this.loadData(query)
        },

        onOptionSelected(value) {
            this.$emit('onOptionSelected', value);
        },

        loadData(query) {
            this.loading_customer_sales = true;

            axios
                .get(route("datatable.customer_sales.data"), {
                    params: {
                        get_all_records: typeof query === 'undefined' ? 0 : 1,
                        order_by: 'CustomerName',
                        search_text: typeof query === 'undefined' ? '' : query
                    }
                })
                .then(
                    response => {
                        this.customer_sales = response.data.rows;

                        if (this.customer_id && !query) {
                            const object = helper_functions.searchJsonObjects(this.customer_sales, "Id", this.customer_id);
                            if (!Object.keys(object).length) {
                                axios
                                    .get(route("customer_sales.get-single"), {
                                        params: {
                                            Id: this.customer_id
                                        }
                                    })
                                    .then(
                                        response => {
                                            let record = response.data.response.record;
                                            this.customer_sales.push(record);
                                        },
                                    );
                            }
                        }

                        this.loading_customer_sales = false;

                        this.$emit('onDataLoadComplete', true);
                    },
                    error => {
                        this.addError(error);

                        this.loading_customer_sales = false;

                        this.$emit('onDataLoadComplete', true);
                    }
                );
        },

        add() {
            this.setPopperOpen(true);

            this.$modal.show(
                Customer,
                {
                    edit_id: "",
                    options: {
                        id: "customer_sales",
                        record_name: "Customer",
                        cache_data: true
                    },
                    customerSaved: (id) => {
                        this.customer_id = id;
                        this.$emit('onOptionSelected', id);
                    }
                },
                {
                    width: "750px",
                    height: "600px"
                },
                {
                    "closed": event => {
                    }
                }
            );
        },

        edit(customer_id) {
            this.setPopperOpen(true);

            this.$modal.show(
                Customer,
                {
                    edit_id: String(customer_id),
                    options: {
                        id: "customer_sales",
                        record_name: "Customer",
                        cache_data: true
                    },
                    customerSaved: (id) => {
                    }
                },
                {
                    width: "750px",
                    height: "600px"
                }
            );
        },

        ...mapActions({
            setPopperOpen: 'local_settings/setPopperOpen',
            addError: "errors/addError"
        })
    },

    watch: {
        refresh_customer_sales: function () {
            this.loadData();
        },

        selected_value: function(new_value) {
            this.customer_id = new_value;
        }
    }
}
</script>
