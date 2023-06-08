const DatatableMixin = {
    data() {
        return {
            search: "",
            perPage: 50,
            footerProps: {
                itemsPerPageOptions: [20, 50, 100],
            },
        };
    },
};

export default DatatableMixin;
