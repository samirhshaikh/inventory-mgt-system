<template>
    <div class="flex flex-row items-center">
        <v-select
            :value="customer_id"
            v-model="customer_id"
            label="CustomerName"
            :reduce="(customer) => customer.id"
            :options="customers"
            class="w-72 generic_vs_select"
            :loading="loading_customers"
            :class="{
                required_field: required_field,
            }"
            ref="customer_picker"
            :filterable="false"
            @search="onSearch"
            @update:modelValue="$emit('onOptionSelected', customer_id)"
        >
            <template v-slot:option="option">
                <strong>{{ option.CustomerName }}</strong>
                <p
                    v-if="option.ContactNo1 || option.ContactNo2"
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
            @click.native="edit"
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
import { mapState, mapActions } from "vuex";
import Customer from "../DBObjects/Customer";
import { useModal } from "vue-final-modal";

export default {
    name: "CustomerPicker",

    props: {
        selected_value: "",
        required_field: false,
        enable_add: false,
        enable_edit: false,
    },

    data() {
        return {
            loading_customers: false,
            customer_id: "",
            customers: [],
        };
    },

    computed: {
        refresh_customers: (state) => state.framework.refresh_customers,

        ...mapState({
            refresh_customers: (state) => state.framework.refresh_customers,
        }),
    },

    mounted() {
        this.loadData();
    },

    methods: {
        onSearch(query) {
            this.loadData(query);
        },

        loadData(query) {
            this.loading_customers = true;

            //Load the first page of customers
            axios
                .get(route("customers.data"), {
                    params: {
                        get_all_records: typeof query === "undefined" ? 0 : 1,
                        order_by: "CustomerName",
                        search_text: typeof query === "undefined" ? "" : query,
                    },
                })
                .then(
                    (response) => {
                        this.customers = response.data.rows;

                        if (this.selected_value && !query) {
                            //Check of the customer is there in first page loaded or not.
                            const object = this.customers.find(
                                (item) => item.id == this.selected_value
                            );

                            //If not there then we need to get the details of it and add it to available options
                            if (!object) {
                                axios
                                    .get(route("customers.get-single"), {
                                        params: {
                                            id: this.selected_value,
                                        },
                                    })
                                    .then((response) => {
                                        let record =
                                            response.data.response.record;
                                        this.customers.push(record);

                                        this.customer_id = this.selected_value;
                                    });
                            } else {
                                this.customer_id = this.selected_value;
                            }
                        }

                        this.loading_customers = false;

                        this.$emit("onDataLoadComplete", true);
                    },
                    (error) => {
                        this.addError(error);

                        this.loading_customers = false;

                        this.$emit("onDataLoadComplete", true);
                    }
                );
        },

        add() {
            const parent = this;

            this.setPopperOpen(true);

            const { open, close } = useModal({
                component: Customer,
                attrs: {
                    edit_id: "",
                    options: {
                        id: "customers",
                        record_name: "Customer",
                        cache_data: true,
                    },
                    customerSaved: (id) => {
                        parent.customer_id = { id: id };
                        this.$emit("onOptionSelected", id);
                    },
                    onConfirm() {
                        close();
                    },
                    onClosed() {
                        parent.setPopperOpen(false);
                    },
                },
            });

            open();
        },

        edit() {
            const parent = this;

            this.setPopperOpen(true);

            const { open, close } = useModal({
                component: Customer,
                attrs: {
                    edit_id: String(parent.customer_id),
                    options: {
                        id: "customers",
                        record_name: "Customer",
                        cache_data: true,
                    },
                    customerSaved: (id) => {},
                    onConfirm() {
                        close();
                    },
                    onClosed() {
                        parent.setPopperOpen(false);
                    },
                },
            });

            open();
        },

        ...mapActions({
            setPopperOpen: "local_settings/setPopperOpen",
            addError: "errors/addError",
        }),
    },

    watch: {
        refresh_customers: function () {
            console.log("refresh_customers");
            this.loadData();
        },

        selected_value: function (new_value) {
            this.customer_id = new_value;
        },
    },
};
</script>
