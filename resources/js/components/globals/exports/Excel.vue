<template>
    <v-btn
        color="light"
        class="ma-2 text-right"
        fab
        x-small
        @click="exportData"
        title="Export to Excel"
        ><v-icon>mdi-microsoft-excel</v-icon></v-btn
    >
</template>

<script>
export default {
    props: ["module", "ids", "data", "local"],

    methods: {
        async exportData() {
            try {
                if (this.ids.length === 0) {
                    return alert(
                        `Select ${this.module.replace(/_/gi, " ")} first`
                    );
                }

                const res = await axios.post(
                    `/api/export?local=${this.local ? true : false}`,
                    {
                        module: this.module,
                        exportType: "xlsx",
                        ids: this.ids,
                        ...this.data,
                    },
                    {
                        responseType: "blob",
                    }
                );

                // Construct blob object
                const blob = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });

                // Download the file
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = this.module;
                link.click();
                URL.revokeObjectURL(link.href);
            } catch (error) {
                console.log(error);
            }
        },
    },
};
</script>
