import { mapActions, mapGetters } from "vuex";

const PrintMixin = {
    methods: {
        ...mapActions(["setPrintMode"]),

        async print() {
            await this.setPrintMode(true);

            window.print();

            this.setPrintMode(false);
        },

        getPrintClass(colClass, authorized = false) {
            return {
                xl: !this.printMode && authorized ? colClass : 12,
                lg: !this.printMode && authorized ? colClass : 12,
                md: !this.printMode && authorized ? colClass : 12,
                sm: !this.printMode && authorized ? colClass : 12
            };
        }
    },

    computed: {
        ...mapGetters(["printMode"])
    }
};

export default PrintMixin;
