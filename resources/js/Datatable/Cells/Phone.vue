<template>
    <div class="leading-tight">
        {{ phone_details }}
    </div>
</template>

<script>
import {datatable_cell} from "./datatable_cell";

export default {
    mixins: [datatable_cell],

    computed: {
        data() {
            if (this.column == '') {
                return this.row;
            } else {
                return this.row[this.column];
            }
        },

        phone_details() {
            if (typeof this.data == "undefined") {
                return "";
            }

            let phone_details = [];
            if (this.data.hasOwnProperty("manufacturer")) {
                phone_details.push(this.data["manufacturer"]["Name"]);
            }
            if (this.data.hasOwnProperty("model")) {
                phone_details.push(this.data["model"]["Name"]);
            }
            if (this.data.hasOwnProperty("color")) {
                phone_details.push(this.data["color"]["Name"]);
            }

            return phone_details.join(" - ");
        }
    }
}
</script>
