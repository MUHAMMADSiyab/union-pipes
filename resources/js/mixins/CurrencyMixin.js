const CurrencyMixin = {
    methods: {
        money(amount) {
            if (isNaN(amount)) return 0;
            return new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: "PKR",
            })
                .format(amount)
                .replace(/PKR/, "")
                .trim();
        },
    },
};

export default CurrencyMixin;
