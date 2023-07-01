/**
 * State storage to datatable objects.
 */

const state = {
    sorting: {
        users: {
            column: "UpdatedDate",
            direction: "desc",
        },
        handsets: {
            column: "UpdatedDate",
            direction: "desc",
        },
        handset_colors: {
            column: "UpdatedDate",
            direction: "desc",
        },
        handset_models: {
            column: "UpdatedDate",
            direction: "desc",
        },
        phonestock: {
            column: "UpdatedDate",
            direction: "desc",
        },
        suppliers: {
            column: "UpdatedDate",
            direction: "desc",
        },
        sales: {
            column: "UpdatedDate",
            direction: "desc",
        },
        purchase: {
            column: "UpdatedDate",
            direction: "desc",
        },
    },
    meta: {},
};

const getters = {
    columns_in_active_tab: (state) => (active_tab) => {
        if (typeof state.meta[active_tab] !== "undefined") {
            return _.cloneDeep(state.meta[active_tab].columns);
        }

        return [];
    },

    sorting: (state) => (tab) => {
        if (state.sorting.hasOwnProperty(tab)) {
            return state.sorting[tab];
        }

        return {
            column: null,
            direction: "",
        };
    },
};

const actions = {
    setTableMetaData({ commit }, payload) {
        commit("setTableMetaData", payload);
    },

    setSorting({ commit }, payload) {
        commit("setSorting", payload);
    },
};

const mutations = {
    setDatatableFromAppSetting(state, payload) {
        console.log(["restore datatable state", payload]);
        for (let key in payload) {
            if (state.hasOwnProperty(key)) {
                state[key] = payload[key];
            }
        }
    },

    setTableMetaData(state, payload) {
        if (Object.keys(payload).length) {
            state.meta[payload.options.id] = payload;
        } else {
            state.meta = payload;
        }
    },

    setSorting(state, payload) {
        if (Object.keys(state.sorting).indexOf(payload.tab) >= 0) {
            //only change the column if it is different from current
            if (
                typeof payload.column !== "undefined" &&
                state.sorting[payload.tab].column != payload.column
            ) {
                state.sorting[payload.tab].column = payload.column;
                return;
            }

            //otherwise just change the direction
            if (state.sorting[payload.tab].direction == "desc") {
                state.sorting[payload.tab].direction = "asc";
            } else {
                state.sorting[payload.tab].direction = "desc";
            }
        } else {
            state.sorting[payload.tab] = {
                column: payload.column,
                direction: "asc",
            };
        }
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};
