<template>
    <div
        class="overflow-hidden bg-light-gray login_bg flex items-center justify-center h-screen"
    >
        <div class="w-full max-w-xs" id="login_box_container">
            <form class="bg-white shadow-md rounded px-8 pt-4 pb-2 mb-4 mt-1">
                <p
                    class="text-center text-blue-600 text-2xl border-b pt-1 border-gray"
                >
                    IMS
                </p>
                <div
                    class="mt-4 p-2 border border-red-500 bg-red-200 rounded-sm text-center text-xs"
                    v-if="error_message != ''"
                >
                    {{ error_message }}
                </div>
                <div class="mt-6">
                    <label
                        class="block text-gray-700 text-sm font-bold mb-1"
                        for="username"
                        >Username</label
                    >
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        :class="{
                            'border-red-500':
                                invalid_fields.indexOf('username') >= 0,
                        }"
                        id="username"
                        type="text"
                        v-model.trim="username"
                    />
                </div>
                <div class="mt-4">
                    <label
                        class="block text-gray-700 text-sm font-bold mb-1"
                        for="password"
                        >Password</label
                    >
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        :class="{
                            'border-red-500':
                                invalid_fields.indexOf('password') >= 0,
                        }"
                        id="password"
                        type="password"
                        v-model.trim="password"
                    />
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <button
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="button"
                        @click="signIn()"
                    >
                        <FA :icon="['fa', 'spinner']" v-if="loading"></FA>
                        Sign In
                    </button>
                    <!--                    <a-->
                    <!--                        class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800"-->
                    <!--                        href="#"-->
                    <!--                        >Forgot Password?</a-->
                    <!--                    >-->
                </div>

                <p class="text-center text-gray-500 text-xs pt-6">
                    &copy;{{ getYear }} All rights reserved.
                </p>
            </form>
        </div>
    </div>
</template>

<script>
import moment from "moment";

export default {
    name: "Login",

    props: [],

    data() {
        return {
            username: "",
            password: "",
            invalid_fields: [],
            error_message: "",
            loading: false,
        };
    },

    created() {},

    methods: {
        signIn() {
            if (this.isFormValid()) {
                this.loading = true;

                axios
                    .post("/doLogin", {
                        username: this.username,
                        password: this.password,
                    })
                    .then((response) => {
                        if (
                            typeof response.data.error === "undefined" ||
                            response.data.error == ""
                        ) {
                            this.error_message = "";

                            this.$inertia.get(route("dashboard"), {
                                method: "get",
                            });
                        } else {
                            this.error_message = response.data.error;
                        }

                        this.loading = false;
                    })
                    .catch((error) => {
                        this.error_message =
                            error.response.data.error ??
                            "Some error occurred. Please try again.";

                        this.loading = false;
                    });
            }
        },

        isFormValid() {
            this.invalid_fields = [];

            if (this.username == "") {
                this.invalid_fields.push("username");
            }

            if (this.password == "") {
                this.invalid_fields.push("password");
            }

            return this.invalid_fields.length == 0;
        },
    },

    computed: {
        getYear() {
            return moment().format("YYYY");
        },
    },
};
</script>
