<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Nozzles</h5>

      <v-row>
        <v-col
          xl="4"
          lg="4"
          md="4"
          sm="12"
          cols="12"
          v-for="(nozzle, i) in nozzles"
          :key="i"
        >
          <v-card class="mx-auto" max-width="344">
            <v-img src="storage/common/nozzle.png" height="200px"></v-img>

            <v-card-title>
              {{ nozzle.name }}
              <span v-if="nozzle.code">({{ nozzle.code }})</span>
            </v-card-title>

            <v-card-subtitle class="mt-2">
              <span class="d-block indigo--text" v-if="nozzle.dispenser">
                <v-icon color="indigo">mdi-doorbell</v-icon>
                {{ nozzle.dispenser.name }}
              </span>

              <small class="d-block mt-1 ml-2" v-if="nozzle.description"
                >{{ nozzle.description.substr(0, 50) }}..</small
              >
            </v-card-subtitle>

            <v-card-actions>
              <v-btn
                x-small
                text
                color="secondary"
                :to="`/nozzles/edit/${nozzle.id}`"
                title="Edit"
                v-if="can('nozzle_edit')"
              >
                <v-icon small>mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                x-small
                text
                color="red darken-2"
                @click="setNozzleId(nozzle.id)"
                title="Delete"
                v-if="can('nozzle_delete')"
              >
                <v-icon small>mdi-delete</v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>

        <!-- Confirmation -->
        <Confirmation
          ref="confirmationComponent"
          :id="nozzleId"
          @confirmDeletion="
            nozzleId ? handleNozzleDelete() : handleMultipleNozzlesDelete()
          "
        />
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import EditNozzle from "./EditNozzle.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";

export default {
  components: {
    EditNozzle,
    Navbar,
    Confirmation,
  },

  data() {
    return {
      allNozzles: [],
      nozzleId: null,
    };
  },

  methods: {
    ...mapActions({
      getNozzles: "nozzle/getNozzles",
      getNozzle: "nozzle/getNozzle",
      getProducts: "product/getProducts",
      deleteNozzle: "nozzle/deleteNozzle",
      deleteMultipleNozzles: "nozzle/deleteMultipleNozzles",
    }),

    setNozzleId(id) {
      this.nozzleId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleNozzleDelete() {
      await this.deleteNozzle(this.nozzleId);
      this.nozzleId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultipleNozzlesDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultipleNozzles(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      nozzles: "nozzle/nozzles",
      nozzle: "nozzle/nozzle",
      products: "product/products",
      loading: "loading",
    }),
  },

  async mounted() {
    // remove actions if no access is given
    if (!this.can("nozzle_edit") && !this.can("nozzle_delete")) {
      this.headers = this.headers.filter(
        (header) => header.value !== "actions"
      );
    }

    await Promise.all([this.getNozzles(), this.getProducts()]);

    this.allNozzles = this.nozzles;
  },
};
</script>