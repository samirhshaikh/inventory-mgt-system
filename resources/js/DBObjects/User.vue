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
                        User Details
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
                                    'text-white': dark_mode
                                }"
                            >
                                Username
                            </label>
                            <div class="block flex flex-row" v-if="edit_id == ''">
                                <input
                                    class="w-48 generic_input"
                                    type="text"
                                    v-model.trim="row['UserName']"
                                    v-on:blur="isDuplicateName"
                                    autocomplete="off"
                                    :class="{
                                        required_field: name_validation_message != ''
                                    }"
                                    ref="user_name"
                                />

                                <Loading
                                    class="ml-2 mt-3 text-sm"
                                    v-if="checking_duplicate_name"
                                    loading_message="Checking name..."
                                />
                            </div>
                            <label
                                class="block form_value_label"
                                :class="{
                                    'text-gray-600': !dark_mode,
                                    'text-product-color-lighter': dark_mode
                                }"
                                v-else
                            >
                                {{ edit_id }}
                            </label>
                            <p
                                class="form_field_message"
                                :class="{
                                    hidden: name_validation_message == '' || name_validation_message == 'Required'
                                }"
                            >
                                {{ name_validation_message }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-3 form_field_container" v-if="edit_id == ''">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Password
                            </label>
                            <input
                                class="w-48 block generic_input"
                                type="password"
                                v-model.trim="row['Password']"
                                placeholder="******************"
                                autocomplete="off"
                                :class="{
                                    required_field: (row_keys.indexOf('Password') < 0 || row['Password'] == '') && edit_id == ''
                                }"
                            />
                        </div>

                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Confirm Password
                            </label>
                            <input
                                class="w-48 block generic_input"
                                type="password"
                                v-model.trim="row['Confirm_Password']"
                                placeholder="******************"
                                autocomplete="off"
                                :class="{
                                    required_field: (row_keys.indexOf('Confirm_Password') < 0 || row['Confirm_Password'] == '') && edit_id == ''
                                }"
                            />
                            <p
                                class="form_field_message"
                                :class="{
                                    hidden:
                                        row['Confirm_Password'] ==
                                            row['Password'] && edit_id == ''
                                }"
                            >
                                Confirm Password should match with Password
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-3 form_field_container">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Is Admin
                            </label>
                            <toggle-button
                                :value="is_admin"
                                :sync="true"
                                :labels="{ checked: 'Yes', unchecked: 'No' }"
                                @change="toggleIsAdmin()"
                            />
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
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

                    <div class="flex flex-wrap -mx-3 form_field_container" v-if="edit_id != ''">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Created By
                            </label>
                            <label
                                class="block form_value_label"
                                :class="{
                                    'text-gray-600': !dark_mode,
                                    'text-product-color-lighter': dark_mode
                                }"
                            >
                                {{ row["CreatedBy"] }}
                            </label>
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Creation Date
                            </label>
                            <label
                                class="block form_value_label"
                                :class="{
                                    'text-gray-600': !dark_mode,
                                    'text-product-color-lighter': dark_mode
                                }"
                            >
                                {{ row["CreatedDate"] }}
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-3 form_field_container" v-if="edit_id != ''">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Updated By
                            </label>
                            <label
                                class="block form_value_label"
                                :class="{
                                    'text-gray-600': !dark_mode,
                                    'text-product-color-lighter': dark_mode
                                }"
                            >
                                {{ row["UpdatedBy"] }}
                            </label>
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <label
                                class="block form_field_label"
                                :class="{
                                    'text-gray-700': !dark_mode,
                                    'text-white': dark_mode
                                }"
                            >
                                Updated Date
                            </label>
                            <label
                                class="block form_value_label"
                                :class="{
                                    'text-gray-600': !dark_mode,
                                    'text-product-color-lighter': dark_mode
                                }"
                            >
                                {{ row["UpdatedDate"] }}
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
import {notifications} from "../Helpers/notifications";

export default {
    mixins: [notifications],

    props: {
        options: {
            type: Object,
            default: () => ({})
        },
        edit_id: {
            type: String,
            default: ""
        }
    },

    data() {
        return {
            row: {},

            is_admin: false,
            is_active: false,

            saving_data: false,
            checking_duplicate_name: false,
            duplicate_name: false,
            loading: false
        };
    },

    computed: {
        valid_data() {
            if (this.edit_id == "") {
                if (
                    this.name_validation_message != '' ||
                    this.row_keys.indexOf("Password") < 0 ||
                    this.row_keys.indexOf("Confirm_Password") < 0 ||
                    this.row["Password"] == "" ||
                    this.row["Confirm_Password"] == "" ||
                    this.row["Confirm_Password"] != this.row["Password"]
                ) {
                    return false;
                }
            }

            return true;
        },

        name_validation_message() {
            if (this.duplicate_name) {
                return "Duplicate user name. Please choose another user name";
            } else if (
                this.row_keys.indexOf("UserName") < 0 ||
                this.row["UserName"] == ""
            ) {
                return "Required";
            }

            return "";
        },

        row_keys() {
            return Object.keys(this.row);
        },

        ...mapState({
            dark_mode: state => state.framework.dark_mode,
            expanded_sidebar: state => state.framework.expanded_sidebar
        })
    },

    mounted() {
        //Get the data from server
        if (this.edit_id != "") {
            this.loading = true;

            axios
                .get(route("users.get-single"), {
                    params: {
                        username: this.edit_id
                    }
                })
                .then(
                    response => {
                        let record = response.data.response.record;
                        this.is_admin = record.IsAdmin;
                        this.is_active = record.IsActive;

                        this.row = _.cloneDeep(record);

                        this.loading = false;
                    },
                    error => {
                        this.loading = false;
                    }
                );
        } else {
            this.$nextTick(() => {
                this.$refs.user_name.focus();
            });
        }
    },

    methods: {
        toggleIsAdmin() {
            this.is_admin = !this.is_admin;
        },

        toggleIsActive() {
            this.is_active = !this.is_active;
        },

        save() {
            //Validate
            if (!this.valid_data) {
                return false;
            }

            this.row["IsAdmin"] = this.is_admin ? 1 : 0;
            this.row["IsActive"] = this.is_active ? 1 : 0;
            this.row["operation"] = this.edit_id == "" ? "add" : "edit";

            this.saving_data = true;

            axios
                .post(route("users.save"), this.row)
                .then(response => {
                    if (response.data.message == "record_saved") {
                        this.$notify({
                            group: "messages",
                            title: "Success",
                            text: "User " + (this.row["operation"] == "add" ? "added" : "edited") + " successfully."
                        });

                        this.refreshData(this.options.id);
                    }

                    this.saving_data = false;

                    this.$modal.hide(this.$parent.name);
                })
                .catch(error => {
                    this.saving_data = false;

                    if (error.response.data.message == "record_not_found") {
                        this.$notify({
                            group: "messages",
                            title: "Error",
                            type: "error",
                            text: this.formatMessage(error.response.data, this.options.record_name)
                        });
                    } else if (error.response.data.message == "duplicate_name") {
                        this.duplicate_name = true;
                    }
                });
        },

        isDuplicateName() {
            if (this.row_keys.indexOf("UserName") < 0 || this.row["UserName"] == "") {
                return false;
            }

            this.checking_duplicate_name = true;
            this.duplicate_name = false;

            axios
                .post(route("users.check-duplicate-name"), {
                    username: this.row["UserName"]
                })
                .then(
                    response => {
                        this.checking_duplicate_name = false;

                        this.duplicate_name = false;

                        this.checking_duplicate_name = false;
                    }
                )
                .catch(
                    error => {
                        this.checking_duplicate_name = false;

                        if (error.response.data.message == "duplicate_name") {
                            this.duplicate_name = true;
                        }
                    }
                );
        },

        ...mapActions({
            refreshData: "framework/refreshData",
            addError: "errors/addError"
        })
    }
};
</script>
