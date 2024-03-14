import { useModal } from "vue-final-modal";
import SalesInvoice from "../Datatable/Cells/Sale/SalesInvoice";
import RepairInvoice from "../Datatable/Cells/Repair/RepairInvoice";

export const common_functions = {
    methods: {
        viewSalesInvoice(invoice_id) {
            const parent = this;

            this.setPopperOpen(true);

            const { open, close } = useModal({
                component: SalesInvoice,
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

        viewReturnsInvoice(invoice_id) {
            const parent = this;

            this.setPopperOpen(true);

            const { open, close } = useModal({
                component: RepairInvoice,
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
