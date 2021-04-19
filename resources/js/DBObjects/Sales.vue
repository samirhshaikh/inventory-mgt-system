<template>

</template>

<script>
import {mapState, mapActions} from "vuex";
import moment from "moment";
import lazyLoadComponent from "@/Helpers/lazyLoadComponent.js";
import loading from "@/Misc/Loading.vue";
import helper_functions from "../store/modules/helper_functions";
import {list_controller} from "./list_controller";
import {notifications} from "../Helpers/notifications";

export default {
    props: {
        options: {
            type: Object,
            default: () => ({})
        },
        edit_id: {
            type: String,
            default: ""
        },
        phones: {
            type: Array,
            default: () => ([])
        },
    },

    mixins: [list_controller, notifications],

}
</script>
