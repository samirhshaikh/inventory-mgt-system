<template>
    <VueFinalModal
        class="flex justify-center items-center"
        :content-class="[
            'object_type_name_modal relative p-4 rounded-lg',
            {
                'bg-gray-700': dark_mode,
                'bg-white': !dark_mode,
            },
        ]"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
    >
        <div class="p-0 overflow-y-auto text-sm">
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
                    {{ options.record_name }} Details
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
                        @click.native="save"
                        :icon="saving_data ? 'sync-alt' : 'check'"
                        :icon_class="saving_data ? 'fa-spin' : ''"
                        split="border-white"
                        class="ml-1"
                        :class="{
                            'bg-green-600': valid_data,
                            'bg-gray-600 text-gray-500 cursor-not-allowed':
                                !valid_data,
                        }"
                    >
                        Save
                    </Button>
                </div>
            </div>

            <form class="w-full pl-2" autocomplete="off" v-if="!loading">
                <div class="flex flex-wrap items-start">
                    <div class="w-full form_field_container">
                        <label
                            class="form_field_label"
                            :class="{
                                'text-gray-700': !dark_mode,
                                'text-white': dark_mode,
                            }"
                        >
                            Name
                        </label>
                        <div class="block flex flex-row">
                            <input
                                class="w-48 generic_input"
                                type="text"
                                v-model.trim="row['Name']"
                                v-on:blur="isDuplicateName"
                                maxlength="255"
                                autocomplete="off"
                                ref="input_name"
                                :class="{
                                    required_field:
                                        name_validation_message != '',
                                }"
                            />

                            <Loading
                                class="ml-2 mt-3 text-sm"
                                v-if="checking_duplicate_name"
                                loading_message="Checking name..."
                            />
                        </div>

                        <p
                            class="form_field_message"
                            :class="{
                                hidden:
                                    name_validation_message == '' ||
                                    name_validation_message == 'Required',
                            }"
                        >
                            {{ name_validation_message }}
                        </p>
                    </div>

                    <RecordStamp :row="row" v-if="edit_id != ''" />
                </div>
            </form>

            <Loading v-else />
        </div>
    </VueFinalModal>
</template>

<style>
.object_type_name_modal {
    width: 650px;
    height: auto;
}
</style>

<script>
import { mapState, mapActions } from "vuex";
import { VueFinalModal } from "vue-final-modal";
import { notifications } from "../Helpers/notifications";

export default {
    mixins: [notifications],

    components: {
        VueFinalModal,
    },

    props: {
        options: {
            type: Object,
            default: () => ({}),
        },
        edit_id: {
            type: String,
            default: "",
        },
    },

    data() {
        return {
            row: {},

            saving_data: false,
            checking_duplicate_name: false,
            duplicate_name: false,
            loading: false,
        };
    },

    computed: {
        valid_data() {
            if (this.name_validation_message != "") {
                return false;
            }

            return true;
        },

        name_validation_message() {
            if (this.duplicate_name) {
                return "Duplicate name. Please choose another name";
            } else if (
                this.row_keys.indexOf("Name") < 0 ||
                this.row["Name"] == ""
            ) {
                return "Required";
            }

            return "";
        },

        row_keys() {
            return Object.keys(this.row);
        },

        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
            expanded_sidebar: (state) => state.framework.expanded_sidebar,
        }),
    },

    mounted() {
        //Get the data from server
        if (this.edit_id != "") {
            this.loading = true;

            axios
                .get(this.options.routes["get-single"], {
                    params: {
                        Id: this.edit_id,
                    },
                })
                .then(
                    (response) => {
                        let record = response.data.response.record;

                        this.row = _.cloneDeep(record);

                        this.loading = false;

                        this.$nextTick(() => {
                            this.$refs.input_name.focus();
                        });
                    },
                    (error) => {
                        this.loading = false;
                    }
                );
        } else {
            this.$nextTick(() => {
                this.$refs.input_name.focus();
            });
        }
    },

    methods: {
        save() {
            //Validate
            if (!this.valid_data) {
                return false;
            }

            this.row["operation"] = this.edit_id == "" ? "add" : "edit";

            this.saving_data = true;

            axios
                .post(this.options.routes["save"], this.row)
                .then((response) => {
                    if (response.data.message == "record_saved") {
                        this.$notify({
                            group: "messages",
                            title: "Success",
                            text:
                                this.options.record_name +
                                " " +
                                (this.row["operation"] == "add"
                                    ? "added"
                                    : "edited") +
                                " successfully.",
                        });

                        //Reset the cache
                        if (
                            this.options.hasOwnProperty("cache_data") &&
                            this.options.cache_data
                        ) {
                            this.resetCachedData(this.options.id);
                        }

                        this.refreshData(this.options.id);

                        switch (this.options.id) {
                            case "handset_models":
                                this.refreshHandsetModels();
                                break;
                            case "handset_manufacturers":
                                this.refreshHandsetManufacturers();
                                break;
                            case "handset_colors":
                                this.refreshHandsetColors();
                                break;
                        }
                    }

                    this.saving_data = false;

                    this.$emit("confirm");
                })
                .catch((error) => {
                    this.saving_data = false;

                    if (error.response.data.message == "record_not_found") {
                        this.$notify({
                            group: "messages",
                            title: "Error",
                            type: "error",
                            text: this.formatMessage(
                                error.response.data.message,
                                this.options.record_name
                            ),
                        });
                    } else if (
                        error.response.data.message == "duplicate_name"
                    ) {
                        this.duplicate_name = true;
                    }
                });
        },

        isDuplicateName() {
            if (this.row_keys.indexOf("Name") < 0 || this.row["Name"] == "") {
                return false;
            }

            this.checking_duplicate_name = true;
            this.duplicate_name = false;

            axios
                .post(this.options.routes["check-duplicate-name"], {
                    Id: this.row["Id"],
                    Name: this.row["Name"],
                })
                .then((response) => {
                    this.checking_duplicate_name = false;

                    this.duplicate_name = false;

                    this.checking_duplicate_name = false;
                })
                .catch((error) => {
                    this.checking_duplicate_name = false;

                    if (error.response.data.message == "duplicate_name") {
                        this.duplicate_name = true;
                    }
                });
        },

        ...mapActions({
            refreshData: "framework/refreshData",
            resetCachedData: "local_settings/resetCachedData",
            refreshHandsetModels: "framework/refreshHandsetModels",
            refreshHandsetManufacturers:
                "framework/refreshHandsetManufacturers",
            refreshHandsetColors: "framework/refreshHandsetColors",
            addError: "errors/addError",
        }),
    },
};
</script>
