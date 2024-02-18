<template>
    <div
        class="flex h-full border border-product-color"
        :class="{
            'bg-gray-700 text-white': dark_mode,
        }"
    >
        <div class="flex-grow flex flex-col justify-between">
            <div class="p-4 overflow-y-auto text-sm flex-grow">
                <div
                    class="flex border-b border-product-color-lighter mb-4 pb-1"
                    :class="{
                        'border-product-color-lighter': dark_mode,
                        'border-product-color': !dark_mode,
                    }"
                >
                    <h1
                        class="text-base md:text-xl pt-2 ml-1 w-full"
                        :class="{
                            'text-product-color-lighter': dark_mode,
                            'text-product-color': !dark_mode,
                        }"
                    >
                        {{ options.record_name }} Details
                    </h1>
                    <div
                        class="float-right flex justify-end mr-2 text-white w-64"
                    >
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
                    <div class="flex flex-wrap -mx-3 form_field_container">
                        <div class="w-full px-3">
                            <label
                                class="block form_field_label"
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
                                    maxlength="255"
                                    v-on:blur="isDuplicateName"
                                    autocomplete="off"
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
                                    hidden: name_validation_message == '',
                                }"
                            >
                                {{ name_validation_message }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-3 form_field_container">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode,
                                }"
                            >
                                Make
                            </label>
                            <v-select
                                label="Name"
                                v-model="row['MakeId']"
                                :reduce="(manufacturer) => manufacturer.Id"
                                :options="handset_manufacturers"
                                class="w-48 generic_vs_select"
                                v-if="!loading_handset_manufacturers"
                                :class="{
                                    required_field:
                                        row['MakeId'] == '' ||
                                        row['MakeId'] == null,
                                }"
                            ></v-select>
                            <Loading v-else />
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode,
                                }"
                            >
                                Model
                            </label>
                            <v-select
                                :value="row['ModelId']"
                                label="Name"
                                v-model="row['ModelId']"
                                :reduce="(model) => model.Id"
                                :options="handset_models"
                                class="w-64 generic_vs_select"
                                v-if="!loading_handset_models"
                                :class="{
                                    required_field:
                                        row['ModelId'] == '' ||
                                        row['ModelId'] == null,
                                }"
                            ></v-select>
                            <Loading v-else />
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-3 form_field_container">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode,
                                }"
                            >
                                Color
                            </label>
                            <v-select
                                :value="row['ColorId']"
                                label="Name"
                                v-model="row['ColorId']"
                                :reduce="(color) => color.Id"
                                :options="handset_colors"
                                class="w-64 generic_vs_select"
                                v-if="!loading_handset_colors"
                                :class="{
                                    required_field:
                                        row['ColorId'] == '' ||
                                        row['ColorId'] == null,
                                }"
                            ></v-select>
                            <Loading v-else />
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode,
                                }"
                            >
                                Is Active
                            </label>
                            <toggle-button
                                :value="is_active"
                                :sync="true"
                                :labels="{ checked: 'Yes', unchecked: 'No' }"
                                @change="toggleIsActive()"
                            />
                        </div>
                    </div>

                    <div
                        class="flex flex-wrap -mx-3 form_field_container"
                        v-if="edit_id != ''"
                    >
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode,
                                }"
                            >
                                Created By
                            </label>
                            <label
                                class="block form_value_label"
                                :class="{
                                    'text-gray-600': !dark_mode,
                                    'text-product-color-lighter': dark_mode,
                                }"
                            >
                                {{ getColumnValue("CreatedBy") }}
                            </label>
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode,
                                }"
                            >
                                Creation Date
                            </label>
                            <label
                                class="block form_value_label"
                                :class="{
                                    'text-gray-600': !dark_mode,
                                    'text-product-color-lighter': dark_mode,
                                }"
                            >
                                {{ getColumnValue("CreatedDate") }}
                            </label>
                        </div>
                    </div>

                    <div
                        class="flex flex-wrap -mx-3 form_field_container"
                        v-if="edit_id != ''"
                    >
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode,
                                }"
                            >
                                Updated By
                            </label>
                            <label
                                class="block form_value_label"
                                :class="{
                                    'text-gray-600': !dark_mode,
                                    'text-product-color-lighter': dark_mode,
                                }"
                            >
                                {{ getColumnValue("UpdatedBy") }}
                            </label>
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode,
                                }"
                            >
                                Updated Date
                            </label>
                            <label
                                class="block form_value_label"
                                :class="{
                                    'text-gray-600': !dark_mode,
                                    'text-product-color-lighter': dark_mode,
                                }"
                            >
                                {{ getColumnValue("UpdatedDate") }}
                            </label>
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
import { notifications } from "../Helpers/notifications";

export default {
    mixins: [notifications],

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

            handset_colors: [],
            handset_models: [],
            handset_manufacturers: [],

            loading_handset_colors: false,
            loading_handset_models: false,
            loading_handset_manufacturers: false,

            is_active: false,

            saving_data: false,
            checking_duplicate_name: false,
            duplicate_name: false,
            loading: false,
        };
    },

    computed: {
        valid_data() {
            if (
                this.name_validation_message != "" ||
                this.row_keys.indexOf("MakeId") < 0 ||
                this.row["MakeId"] == "" ||
                this.row_keys.indexOf("ModelId") < 0 ||
                this.row["ModelId"] == "" ||
                this.row_keys.indexOf("ColorId") < 0 ||
                this.row["ColorId"] == ""
            ) {
                return false;
            }

            return true;
        },

        name_validation_message() {
            if (this.duplicate_name) {
                return "Duplicate name. Please choose another name.";
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
            local_settings: (state) => state.local_settings,
        }),
    },

    mounted() {
        //Get the data from server
        if (this.edit_id != "") {
            this.loading = true;

            axios
                .get(route("handsets.get-single"), {
                    params: {
                        Id: this.edit_id,
                    },
                })
                .then(
                    (response) => {
                        let record = response.data.response.record;
                        this.is_active = record.IsActive;

                        this.row = _.cloneDeep(record);

                        this.loading = false;
                    },
                    (error) => {
                        this.loading = false;
                    }
                );
        }

        //get cached data
        //handset_color, handset_models, handset_manufacturers
        if (
            this.local_settings.cached_data.hasOwnProperty("handset_colors") &&
            this.local_settings.cached_data["handset_colors"].length
        ) {
            this.handset_colors =
                this.local_settings.cached_data["handset_colors"];
        } else {
            this.loading_handset_colors = true;
            axios.post(route("handset-colors.data")).then(
                (response) => {
                    this.handset_colors = response.data.rows;

                    this.setCachedData({
                        key: "handset_colors",
                        data: response.data.rows,
                    });

                    this.loading_handset_colors = false;
                },
                (error) => {
                    this.addError(error);

                    this.loading_handset_colors = false;
                }
            );
        }

        if (
            this.local_settings.cached_data.hasOwnProperty("handset_models") &&
            this.local_settings.cached_data["handset_models"].length
        ) {
            this.handset_models =
                this.local_settings.cached_data["handset_models"];
        } else {
            this.loading_handset_models = true;
            axios.post(route("handset-models.data")).then(
                (response) => {
                    this.handset_models = response.data.rows;

                    this.setCachedData({
                        key: "handset_models",
                        data: response.data.rows,
                    });

                    this.loading_handset_models = false;
                },
                (error) => {
                    this.addError(error);

                    this.loading_handset_models = false;
                }
            );
        }

        if (
            this.local_settings.cached_data.hasOwnProperty(
                "handset_manufacturers"
            ) &&
            this.local_settings.cached_data["handset_manufacturers"].length
        ) {
            this.handset_manufacturers =
                this.local_settings.cached_data["handset_manufacturers"];
        } else {
            this.loading_handset_manufacturers = true;
            axios.post(route("handset-manufacturers.data")).then(
                (response) => {
                    this.handset_manufacturers = response.data.rows;

                    this.setCachedData({
                        key: "handset_manufacturers",
                        data: response.data.rows,
                    });

                    this.loading_handset_manufacturers = false;
                },
                (error) => {
                    this.addError(error);

                    this.loading_handset_manufacturers = false;
                }
            );
        }
    },

    methods: {
        toggleIsAdmin() {
            this.is_admin = !this.is_admin;
        },

        toggleIsActive() {
            this.is_active = !this.is_active;
        },

        getColumnValue(column) {
            return _.get(this.row, column);
        },

        save() {
            //Validate
            if (!this.valid_data) {
                return false;
            }

            this.row["IsActive"] = this.is_active ? 1 : 0;
            this.row["operation"] = this.edit_id == "" ? "add" : "edit";

            //save the user
            this.saving_data = true;

            axios
                .post(route("handsets.save"), this.row)
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

                        this.refreshData(this.options.id);
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
                .post(route("handsets.check-duplicate-name"), {
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
            setCachedData: "local_settings/setCachedData",
            addError: "errors/addError",
        }),
    },
};
</script>
