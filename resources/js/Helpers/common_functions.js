import Invoice from "../Datatable/Cells/Sale/Invoice";

export const common_functions = {
    methods: {
        viewSalesInvoice(invoice_id) {
            this.setPopperOpen(true);

            this.$modal.show(
                Invoice,
                {
                    invoice_id: String(invoice_id)
                },
                {
                    width: "90%",
                    height: "80%"
                },
                {
                    "opened": event => {
                    },
                    "closed": event => {
                    }
                }
            );
        },
    }
}
