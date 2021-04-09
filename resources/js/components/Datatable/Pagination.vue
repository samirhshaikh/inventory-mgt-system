<template>
    <div class="pagination-container">
        <ul
            v-if="total_records"
            class="flex flex-row justify-center pagination"
            style="margin: 0px auto;"
        >
            <!-- First button -->
            <li
                class="page-item first"
                :class="{
                    disabled: current_page_no === 1
                }"
            >
                <a class="page-link" @click="setCurrentPage(1)">
                    {{ labels.first }}
                </a>
            </li>

            <!-- Previous button -->
            <li
                class="page-item previous"
                :class="{
                    disabled: current_page_no === 1
                }"
            >
                <a class="page-link" @click="setCurrentPage(current_page_no - 1)">
                    {{ labels.previous }}
                </a>
            </li>

            <!-- Page number buttons -->
            <li
                v-for="page in pages"
                :key="page"
                class="page-item"
                :class="{
                    active: page === current_page_no
                }"
            >
                <a class="page-link" @click="setCurrentPage(page)">
                    {{ page }}
                </a>
            </li>

            <!-- Next button -->
            <li
                class="page-item next"
                :class="{ disabled: current_page_no === total_pages }"
            >
                <a class="page-link" @click="setCurrentPage(current_page_no + 1)">
                    {{ labels.next }}
                </a>
            </li>

            <!-- Last button -->
            <li
                class="page-item last"
                :class="{ disabled: current_page_no === total_pages }"
            >
                <a class="page-link" @click="setCurrentPage(total_pages)">
                    {{ labels.last }}
                </a>
            </li>
        </ul>
    </div>
</template>

<script>
const default_labels = {
    first: "First",
    last: "Last",
    previous: "Previous",
    next: "Next"
};

export default {
    props: {
        total_records: {
            type: Number,
            default: 0
        },
        page_size: {
            type: Number,
            default: 10
        },
        labels: {
            type: Object,
            default: () => default_labels
        },
        start_page_no: {
            type: Number,
            default: 1
        }
    },

    data() {
        return {
            current_page_no: 1
        };
    },

    computed: {
        pages() {
            let page_range_size = 10;

            let start_page = 0;
            let end_page = 0;
            //if the total pages are less than the page_range_size the just show all the page numbers.
            if (this.total_pages <= page_range_size) {
                start_page = 1;
                end_page = this.total_pages;
            }
            //more pages then page_range_size, let us calculate start_page and end_page
            else {
                let max_pages_before_current_page = Math.floor(page_range_size / 2);
                let max_pages_after_current_page = Math.ceil(page_range_size / 2) - 1;
                if (this.current_page_no <= max_pages_before_current_page) {
                    // current page near the start
                    start_page = 1;
                    end_page = page_range_size;
                } else if (
                    this.current_page_no + max_pages_after_current_page >= this.total_pages
                ) {
                    // current page near the end
                    start_page = this.total_pages - page_range_size + 1;
                    end_page = this.total_pages;
                } else {
                    // current page somewhere in the middle
                    start_page = this.current_page_no - max_pages_before_current_page;
                    end_page = this.current_page_no + max_pages_after_current_page;
                }
            }

            let range = [];
            range.push(start_page);
            let count = start_page;
            while(count++ < end_page) {
                range.push(count);
            }

            return range;
        },

        total_pages() {
            let no_of_pages = Math.floor(this.total_records / this.page_size);
            no_of_pages += this.total_records % this.page_size ? 1 : 0;

            return no_of_pages;
        }
    },

    methods: {
        setCurrentPage(page_no) {
            if (page_no <= 0 || page_no > this.total_pages) {
                return false;
            }

            this.current_page_no = page_no;

            // emit change page event to parent component
            this.$emit("changePage", page_no);
        }
    },

    watch: {
        start_page_no(new_value, old_value) {
            this.current_page_no = new_value;
        }
    }
};
</script>
