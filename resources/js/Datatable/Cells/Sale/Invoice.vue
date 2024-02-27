<template>
    <VueFinalModal
        class="flex justify-center items-center"
        :content-class="[
            'invoice_modal relative p-4 rounded-lg',
            {
                'bg-gray-700': dark_mode,
                'bg-white': !dark_mode,
            },
        ]"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
    >
        <div class="flex flex-row justify-end text-white">
            <Button
                @click.native="$emit('closed')"
                icon="times"
                split="border-white"
                class="bg-red-600"
            >
                Close
            </Button>
            <Button
                @click.native="printPDF"
                icon="check"
                split="border-white"
                class="ml-1 bg-green-600"
                v-if="!loading"
            >
                Print
            </Button>
        </div>

        <iframe
            id="pdf-frame"
            class="w-full h-full mt-2 pdf"
            v-show="!loading"
        ></iframe>

        <Loading v-if="loading"></Loading>
    </VueFinalModal>
</template>

<style>
.invoice_modal {
    width: 90%;
    height: 90%;
}
.pdf {
    height: calc(100% - 28px);
}
</style>

<script>
import { VueFinalModal } from "vue-final-modal";
import { mapState } from "vuex";

export default {
    components: {
        VueFinalModal,
    },

    props: {
        invoice_id: {
            type: String,
            required: true,
        },
    },

    data() {
        return {
            loading: true,
            pdf_link: "",
            pdf_source: null,
            pdf_config: {
                sidebar: false,
            },
            pdf_content: null,
        };
    },

    computed: {
        ...mapState({
            dark_mode: (state) => state.framework.dark_mode,
        }),

        filename() {
            return "Invoice_" + this.invoice_id;
        },
    },

    mounted() {
        axios
            .get(route("sale.get-pdf-invoice"), {
                params: {
                    id: this.invoice_id,
                },
                responseType: "blob",
            })
            .then(
                (response) => {
                    this.loading = false;

                    let file = new File(
                        [response.data],
                        "Invoice_" + this.invoice_id + ".pdf",
                        { type: "application/pdf" }
                    );
                    document.querySelector("#pdf-frame").src =
                        URL.createObjectURL(file);

                    // let objectURL = URL.createObjectURL(response.data);
                    // document.querySelector('#pdf-frame').src = '';
                    // document.querySelector('#pdf-frame').src = objectURL;
                    // objectURL = URL.revokeObjectURL(response.data);
                },
                (error) => {
                    console.log(error);
                }
            );
    },

    methods: {
        printPDF() {
            document.querySelector("#pdf-frame").contentWindow.print();
            // this.$refs.myPdfComponent.print();
        },
    },
};
</script>
