<template>
  <v-btn color="light" fab x-small @click="exportData" title="Export to CSV"
    ><v-icon>mdi-file-delimited-outline</v-icon></v-btn
  >
</template>

<script>
export default {
  props: ["module", "ids", "data"],

  methods: {
    async exportData() {
      try {
        if (this.ids.length === 0) {
          return alert(`Select ${this.module.replace(/_/gi, " ")} first`);
        }

        const res = await axios.post(
          `/api/export`,
          {
            module: this.module,
            exportType: "csv",
            ids: this.ids,
            ...this.data,
          },
          {
            responseType: "blob",
          }
        );

        // Construct blob object
        const blob = new Blob([res.data], { type: "text/csv" });

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