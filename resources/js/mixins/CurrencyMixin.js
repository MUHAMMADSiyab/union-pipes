const CurrencyMixin = {
    methods: {
        money(amount) {
            return new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: "PKR"
            })
                .format(amount)
                .replace(/PKR/, "")
                .trim();
        }
    }
};

export default CurrencyMixin;
