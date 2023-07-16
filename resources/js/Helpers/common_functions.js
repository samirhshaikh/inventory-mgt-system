import Invoice from "../Datatable/Cells/Sale/Invoice";
import { useModal } from "vue-final-modal";

export const common_functions = {
    methods: {
        viewSalesInvoice(invoice_id) {
            const parent = this;

            this.setPopperOpen(true);

            const { open, close } = useModal({
                component: Invoice,
                attrs: {
                    invoice_id: String(invoice_id),
                    onConfirm() {
                        close();
                    },
                    onClosed() {
                        parent.setPopperOpen(false);
                    },
                },
            });

            open();
        },
    },
};
