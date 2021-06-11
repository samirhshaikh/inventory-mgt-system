export const datatable_cell = {
    props: {
        row: {
            default: () => ({})
        },
        column: {
            default: ""
        },
        column_details: {
            default: () => ({})
        },
        options: {
            default: () => ({})
        },
        expanded_row_id: {
            type: Number,
            default: 0
        }
    },

    data() {
        return {
            deleting_record: false
        }
    },

    computed: {
        data() {
            return _.get(this.row, this.column, false)
        },

        isAdmin() {
            return this.$page.user_details.IsAdmin;
        }
    }
}
