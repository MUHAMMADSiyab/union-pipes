const CurrencyMixin = {
    methods: {
        money(amount) {
            if (isNaN(amount)) return 0;
            const formatted = new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: "PKR",
            }).format(amount);

            return formatted
                .replace(/\.\d{2}\b/g, "")
                .replace(/PKR/, "")
                .trim();
        },
    },
};

export default CurrencyMixin;
