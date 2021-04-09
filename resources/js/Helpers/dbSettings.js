const dbSettings = store => {
    let mutationsToPersist = {
        "framework/setSidebar": {
            refresh: false
        },
        "framework/setDarkmode": {
            refresh: false
        },
        "framework/setPagesize": {
            refresh: true
        },
        "store_settings/setStoreSettings": {
            refresh: false
        },
        "datatable/setSorting": {
            refresh: true
        }
    };

    //called when store is initialized
    store.subscribe((mutation, state) => {
        if (Object.keys(mutationsToPersist).includes(mutation.type)) {
            let datatable = _.cloneDeep(state.datatable);
            _.set(datatable, 'meta', {});

            axios
                .post("/storeAppSettings", {
                    framework: state.framework,
                    datatable: datatable,
                    store_settings: state.store_settings
                })
                .then(
                    response => {
                        if (mutationsToPersist[mutation.type].refresh) {
                            store.commit('framework/refreshData', state.local_settings.active_tab);
                        }
                    },
                    error => {}
                );
        }
    });
};

export default dbSettings;
