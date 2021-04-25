/**
 * Mixin file. Description of codes sent from server
 */
export const notifications = {
    methods: {
        formatMessage(error_code, record_name) {
            switch (error_code) {
                case "record_not_found":
                    return record_name + "  not found.";
                case "record_reference_found":
                    return record_name + " used in other table. Cannot delete it.";
                case "invalid_data":
                    return "Invalid data. Please rectify data."
                case "status_changed":
                    return "Status changed successfully."
                case "record_deleted":
                    return record_name + " deleted successfully."
                case "unknown_error":
                default:
                    return "Some error occurred. Please try again.";
            }
        }
    }
}
