<template>
    <div class="flex flex-row items-center">
        <v-select
            :value="supplier_id"
            v-model="supplier_id"
            label="SupplierName"
            :reduce="(supplier) => supplier.id"
            :options="
                source === 'parts_suppliers' ? parts_suppliers : suppliers
            "
            class="w-48 generic_vs_select"
            :loading="
                source === 'parts_suppliers'
                    ? loading_parts_suppliers
                    : loading_suppliers
            "
            :class="{
                required_field: required_field,
            }"
            ref="supplier_picker"
            :filterable="true"
            @update:modelValue="$emit('onOptionSelected', supplier_id)"
        ></v-select>

        <Button
            @click.native="addSupplier(source)"
            icon="plus"
            split="border-white"
            class="ml-1 bg-green-600 text-white"
            v-if="enable_add"
        >
            New
        </Button>
    </div>
</template>

<script>
import { mapState } from "vuex";
import { list_controller } from "../Helpers/list_controller";

export default {
    name: "SupplierPicker",

    props: {
        source: {
            type: String,
            default: "suppliers",
        },
        selected_value: "",
        required_field: false,
        enable_add: false,
        enable_edit: false,
    },

    mixins: [list_controller],

    data() {
        return {
            supplier_id: this.selected_value,
        };
    },

    computed: {
        refresh_suppliers: (state) => state.framework.refresh_suppliers,

        ...mapState({
            refresh_suppliers: (state) => state.framework.refresh_suppliers,
        }),
    },

    mounted() {},

    methods: {
        onSearch(query) {
            this.loadData(query);
        },
    },

    watch: {
        refresh_suppliers: function () {
            this.source === "parts_suppliers"
                ? this.load_parts_suppliers()
                : this.load_suppliers();
        },

        selected_value: function (new_value) {
            this.supplier_id = new_value;
        },
    },
};
</script>
