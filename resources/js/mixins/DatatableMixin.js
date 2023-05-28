const DatatableMixin = {
    data() {
        return {
            search: "",
            perPage: 3,
            footerProps: {
                itemsPerPageOptions: [3, 50, 100],
            },
        };
    },
};

export default DatatableMixin;
