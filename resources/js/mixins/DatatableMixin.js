const DatatableMixin = {
    data() {
        return {
            search: "",
            perPage: 20,
            footerProps: {
                itemsPerPageOptions: [20, 50, 100, -1]
            }
        };
    }
};

export default DatatableMixin;
