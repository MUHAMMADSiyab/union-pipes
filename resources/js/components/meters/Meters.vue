<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Meters</h5>

      <v-row>
        <v-col
          xl="4"
          lg="4"
          md="4"
          sm="12"
          cols="12"
          v-for="(meter, i) in meters"
          :key="i"
        >
          <v-card class="mx-auto" max-width="344">
            <v-card-title>
              {{ meter.name }}
              <span v-if="meter.code">({{ meter.code }})</span>

              <span class="ml-auto">
                <v-icon :size="80" color="info">mdi-speedometer</v-icon>
              </span>
            </v-card-title>

            <v-card-subtitle class="mt-2">
              <span
                class="d-block indigo--text"
                v-if="meter.dispenser"
                title="Dispenser"
              >
                <v-icon color="indigo">mdi-doorbell</v-icon>
                {{ meter.dispenser.name }}
              </span>

              <small class="d-block mt-1 ml-2" v-if="meter.description"
                >{{ meter.description.substr(0, 50) }}..</small
              >
            </v-card-subtitle>

            <v-card-actions>
              <v-btn
                x-small
                text
                color="secondary"
                :to="`/meters/edit/${meter.id}`"
                title="Edit"
                v-if="can('meter_edit')"
              >
                <v-icon small>mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                x-small
                text
                color="red darken-2"
                @click="setMeterId(meter.id)"
                title="Delete"
                v-if="can('meter_delete')"
              >
                <v-icon small>mdi-delete</v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>

        <!-- Confirmation -->
        <Confirmation
          ref="confirmationComponent"
          :id="meterId"
          @confirmDeletion="
            meterId ? handleMeterDelete() : handleMultipleMetersDelete()
          "
        />
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import EditMeter from "./EditMeter.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";

export default {
  components: {
    EditMeter,
    Navbar,
    Confirmation,
  },

  data() {
    return {
      allMeters: [],
      meterId: null,
    };
  },

  methods: {
    ...mapActions({
      getMeters: "meter/getMeters",
      getMeter: "meter/getMeter",
      getProducts: "product/getProducts",
      deleteMeter: "meter/deleteMeter",
      deleteMultipleMeters: "meter/deleteMultipleMeters",
    }),

    setMeterId(id) {
      this.meterId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMeterDelete() {
      await this.deleteMeter(this.meterId);
      this.meterId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultipleMetersDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultipleMeters(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      meters: "meter/meters",
      meter: "meter/meter",
      products: "product/products",
      loading: "loading",
    }),
  },

  async mounted() {
    // remove actions if no access is given
    if (!this.can("meter_edit") && !this.can("meter_delete")) {
      this.headers = this.headers.filter(
        (header) => header.value !== "actions"
      );
    }

    await Promise.all([this.getMeters(), this.getProducts()]);

    this.allMeters = this.meters;
  },
};
</script>