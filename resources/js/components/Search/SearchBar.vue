<template>
    <section class="hidden md:block">
        <button
            class="rounded text-green-600 px-2 pt-2"
            :class="{
                'hidden': search_bar_open,
                'hover:text-gray-800': !dark_mode,
                'hover:text-product-color-lighter': dark_mode
            }"
            @click="openSearchBar"
        >
            <FA :icon="['fas', 'search']"></FA>
        </button>

        <div class="flex flex-row search_bar"
            :class="{
                hidden: !search_bar_open
            }"
        >
            <input
                type="text"
                class="search_input"
                ref="search_textbox"
                :placeholder="get_placeholder_text"
                v-model.trim="search_text"
                v-on:keyup="searchData($event)"
            />
            <button
                class="ml-1 text-green-600"
                @click="advancedSearch"
                title="Advanced Search"
                v-if="advanced_search"
            >
                <FA :icon="['fas', 'magic']" class="ml-1"></FA>
            </button>
            <button
                class="ml-1 text-green-600"
                @click="closeSearch"
                title="Close Search"
            >
                <FA :icon="['fas', 'times']" class="ml-1"></FA>
            </button>
        </div>
    </section>
</template>

<style>
.search_bar {
    @apply .bg-gray-200 .text-gray-700 .border .border-gray-400 .rounded .py-1 .px-2 .text-sm
}
.search_input {
    @apply .appearance-none
    .bg-transparent
}
.search_input {
    line-height: 20px;
}
.search_input:focus, .search_input:focus {
    @apply .outline-none
    .bg-transparent
}
</style>

<script>
import {mapActions, mapState} from 'vuex';
import AdvancedSearch from "./AdvancedSearch";

export default {
    name: "SearchBar",

    props: {
        placeholder_text: {
            type: String,
            default: 'Search'
        },
        columns: {
            type: Array,
            default: () => ([])
        },
        advanced_search: {
            type: Boolean,
            default: true
        }
    },

    data() {
        return {
            search_bar_open: false,
            search_text: '',
            current_search_text: ''
        }
    },

    computed: {
        get_placeholder_text() {
            return 'Search ' + this.placeholder_text + '...';
        },

        ...mapState({
            dark_mode: state => state.framework.dark_mode
        })
    },

    methods: {
        openSearchBar() {
            this.search_bar_open = true;

            if (this.search_bar_open) {
                setTimeout(x => {
                    this.$nextTick(() => this.$refs.search_textbox.focus());
                }, 100);
            }
        },

        blurSearchInput(event) {
            if (this.search_text == '') {
                this.search_bar_open = false;
            }
        },

        closeSearch() {
            if (this.current_search_text != "") {
                this.current_search_text = "";
                this.search_text = "";
            }

            this.$emit('clearSearch');

            this.search_bar_open = false;
        },

        fireSearch() {
            if (this.current_search_text != this.search_text) {
                this.current_search_text = this.search_text;
                this.$emit('searchData', this.search_text);
            }
        },

        searchData(event) {
            switch (event.key) {
                case "Escape":
                    this.search_text = '';
                    this.search_bar_open = false;
                    break;
                case "Enter":
                    //Search the data
                    this.fireSearch();
                    break;
            }
        },

        advancedSearch() {
            this.setPopperOpen(true);

            this.$modal.show(
                AdvancedSearch,
                {
                    columns: this.columns,
                    triggerAdvancedSearch: (newValue) => {
                        this.$emit("triggerAdvancedSearch", newValue);
                        this.search_bar_open = false;
                        this.search_text = "";
                    }
                },
                {
                    width: "750px",
                    height: "600px"
                }
            );
        },

        ...mapActions({
            setPopperOpen: "local_settings/setPopperOpen",
            addError: 'errors/addError'
        })
    }
};
</script>
