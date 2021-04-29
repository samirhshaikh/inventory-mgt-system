/**
 * Mixin file. It maintains the cache of the following items:
 * Suppliers
 * Colors
 * Models
 * Manufacturers
 * Customers
 * Stock Types
 * Networks
 * Phone sizes
 * Payment Types
 *
 * ToDo: Need to remove customers from here as it can be pretty big list. So need to make customer select box ajax connected.
 */
import {mapState, mapActions} from "vuex";
import Supplier from "../DBObjects/Supplier";
import Customer from "../DBObjects/Customer";
import ObjectTypeName from "../DBObjects/ObjectTypeName";

export const list_controller = {
    data() {
        return {
            loading_suppliers: false,
            loading_handset_colors: false,
            loading_handset_models: false,
            loading_handset_manufacturers: false,
            loading_customers: false,

            suppliers: [],
            handset_colors: [],
            handset_models: [],
            handset_manufacturers: [],
            customers: [],

            suppliers_simple: [],
            handset_colors_simple: [],
            handset_models_simple: [],
            handset_manufacturers_simple: [],
            customers_simple: [],

            stock_types: [],
            networks: [],
            phone_sizes: [],
            stock_statuses: [],
            payment_types: [],
        }
    },

    computed: {
        ...mapState({
            local_settings: state => state.local_settings,
            store_settings: state => state.store_settings
        })
    },

    mounted() {
        this.stock_types = this.store_settings.stock_types.split(',');
        this.networks = this.store_settings.networks.split(',');
        this.phone_sizes = this.store_settings.phone_sizes.split(',');
        this.stock_statuses = this.store_settings.stock_statuses.split(',');
        this.payment_types = this.store_settings.payment_types.split(',');

        this.load_suppliers();

        this.load_handset_colors();

        this.load_handset_models();

        this.load_handset_manufacturers();

        this.load_customers();
    },

    methods: {
        load_suppliers() {
            if (
                this.local_settings.cached_data.hasOwnProperty("suppliers") &&
                this.local_settings.cached_data["suppliers"].length
            ) {
                this.suppliers = this.local_settings.cached_data["suppliers"];
                this.load_suppliers_simple();
            } else {
                this.loading_suppliers = true;
                axios.post(route("datatable.suppliers.data"), {
                    get_all_records: 1,
                    order_by: 'SupplierName'
                }).then(
                    response => {
                        this.suppliers = response.data.rows;

                        this.setCachedData({
                            key: "suppliers",
                            data: response.data.rows
                        });

                        this.loading_suppliers = false;

                        this.load_suppliers_simple();
                    },
                    error => {
                        this.addError(error);

                        this.loading_suppliers = false;
                    }
                );
            }
        },

        load_suppliers_simple() {
            this.suppliers_simple = [];
            _.forEach(this.suppliers, (data, key) => {
                if (!this.suppliers_simple.includes(data["SupplierName"])) {
                    this.suppliers_simple.push(data["SupplierName"]);
                }
            });
        },

        load_customers() {
            if (
                this.local_settings.cached_data.hasOwnProperty("customers") &&
                this.local_settings.cached_data["customers"].length
            ) {
                this.customers = this.local_settings.cached_data["customers"];
                this.load_customers_simple();
            } else {
                this.loading_customers = true;
                axios.post(route("datatable.customers.data"), {
                    get_all_records: 1,
                    order_by: 'CustomerName'
                }).then(
                    response => {
                        this.customers = response.data.rows;

                        this.setCachedData({
                            key: "customers",
                            data: response.data.rows
                        });

                        this.loading_customers = false;

                        this.load_customers_simple();
                    },
                    error => {
                        this.addError(error);

                        this.loading_customers = false;
                    }
                );
            }
        },

        load_customers_simple() {
            this.customers_simple = [];
            _.forEach(this.customers, (data, key) => {
                if (!this.customers_simple.includes(data["CustomerName"])) {
                    this.customers_simple.push(data["CustomerName"]);
                }
            });
        },

        load_handset_colors() {
            if (
                this.local_settings.cached_data.hasOwnProperty("handset_colors") &&
                this.local_settings.cached_data["handset_colors"].length
            ) {
                this.handset_colors = this.local_settings.cached_data["handset_colors"];
                this.load_handset_colors_simple();
            } else {
                this.loading_handset_colors = true;
                axios.post(route("datatable.handset-colors.data"), {
                    get_all_records: 1,
                    order_by: 'Name'
                }).then(
                    response => {
                        this.handset_colors = response.data.rows;

                        this.setCachedData({
                            key: "handset_colors",
                            data: response.data.rows
                        });

                        this.loading_handset_colors = false;

                        this.load_handset_colors_simple();
                    },
                    error => {
                        this.addError(error);

                        this.loading_handset_colors = false;
                    }
                );
            }
        },

        load_handset_colors_simple() {
            this.handset_colors_simple = [];
            _.forEach(this.handset_colors, (data, key) => {
                if (!this.handset_colors_simple.includes(data["Name"])) {
                    this.handset_colors_simple.push(data["Name"]);
                }
            });
        },

        load_handset_models() {
            if (
                this.local_settings.cached_data.hasOwnProperty("handset_models") &&
                this.local_settings.cached_data["handset_models"].length
            ) {
                this.handset_models = this.local_settings.cached_data["handset_models"];
                this.load_handset_models_simple();
            } else {
                this.loading_handset_models = true;
                axios.post(route("datatable.handset-models.data"), {
                    get_all_records: 1,
                    order_by: 'Name'
                }).then(
                    response => {
                        this.handset_models = response.data.rows;

                        this.setCachedData({
                            key: "handset_models",
                            data: response.data.rows
                        });

                        this.loading_handset_models = false;
                        this.load_handset_models_simple();
                    },
                    error => {
                        this.addError(error);

                        this.loading_handset_models = false;
                    }
                );
            }
        },

        load_handset_models_simple() {
            this.handset_models_simple = [];
            _.forEach(this.handset_models, (data, key) => {
                if (!this.handset_models_simple.includes(data["Name"])) {
                    this.handset_models_simple.push(data["Name"]);
                }
            });
        },

        load_handset_manufacturers() {
            if (
                this.local_settings.cached_data.hasOwnProperty(
                    "handset_manufacturers"
                ) &&
                this.local_settings.cached_data["handset_manufacturers"].length
            ) {
                this.handset_manufacturers = this.local_settings.cached_data["handset_manufacturers"];
                this.load_handset_manufacturers_simple();
            } else {
                this.loading_handset_manufacturers = true;
                axios.post(route("datatable.handset-manufacturers.data"), {
                    get_all_records: 1,
                    order_by: 'Name'
                }).then(
                    response => {
                        this.handset_manufacturers = response.data.rows;

                        this.setCachedData({
                            key: "handset_manufacturers",
                            data: response.data.rows
                        });

                        this.loading_handset_manufacturers = false;
                        this.load_handset_manufacturers_simple();
                    },
                    error => {
                        this.addError(error);

                        this.loading_handset_manufacturers = false;
                    }
                );
            }
        },

        load_handset_manufacturers_simple() {
            this.handset_manufacturers_simple = [];
            _.forEach(this.handset_manufacturers, (data, key) => {
                if (!this.handset_manufacturers_simple.includes(data["Name"])) {
                    this.handset_manufacturers_simple.push(data["Name"]);
                }
            });
        },

        addSupplier() {
            this.setPopperOpen(true);

            this.$modal.show(
                Supplier,
                {
                    edit_id: "",
                    options: {
                        id: "suppliers",
                        record_name: "Supplier",
                        cache_data: true
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

        addCustomer() {
            this.setPopperOpen(true);

            this.$modal.show(
                Customer,
                {
                    edit_id: "",
                    options: {
                        id: "customers",
                        record_name: "Customer",
                        cache_data: true
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

        addHandsetManufacturer() {
            let routes = [];
            routes["get-single"] = route("handset-manufacturers.get-single");
            routes["save"] = route("handset-manufacturers.save");
            routes["check-duplicate-name"] = route("handset-manufacturers.check-duplicate-name");

            this.addObjectTypeName({
                id: "handset_manufacturers",
                record_name: "Manufacturer",
                routes: routes,
                cache_data: true
            });
        },

        addHandsetModel() {
            let routes = [];
            routes["get-single"] = route("handset-models.get-single");
            routes["save"] = route("handset-models.save");
            routes["check-duplicate-name"] = route("handset-models.check-duplicate-name");

            this.addObjectTypeName({
                id: "handset_models",
                record_name: "Model",
                routes: routes,
                cache_data: true
            });
        },

        addHandsetColor() {
            let routes = [];
            routes["get-single"] = route("handset-colors.get-single");
            routes["save"] = route("handset-colors.save");
            routes["check-duplicate-name"] = route("handset-colors.check-duplicate-name");

            this.addObjectTypeName({
                id: "handset_colors",
                record_name: "Color",
                routes: routes,
                cache_data: true
            });
        },

        addObjectTypeName(options) {
            this.setPopperOpen(true);

            this.$modal.show(
                ObjectTypeName,
                {
                    edit_id: "",
                    options: options
                },
                {
                    width: "650px",
                    height: "600px"
                }
            );
        },

        ...mapActions({
            setCachedData: "local_settings/setCachedData",
            addError: "errors/addError"
        })
    }
}
