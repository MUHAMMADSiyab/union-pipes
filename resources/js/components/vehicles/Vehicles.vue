<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Vehicles</h5>

      <v-row>
        <v-col
          xl="4"
          lg="4"
          md="4"
          sm="12"
          cols="12"
          v-for="(vehicle, i) in vehicles"
          :key="i"
        >
          <v-card class="mx-auto" max-width="344">
            <v-img src="/storage/common/vehicle-bg.png" height="200px"></v-img>

            <v-card-title>
              {{ vehicle.registration_no }}
              <v-chip color="primary" class="ml-auto"
                >{{ vehicle.chambers_count }} Chambers</v-chip
              >
            </v-card-title>

            <v-card-subtitle>
              <span title="Capacity" class="font-weight-bold gray--text">
                {{ vehicle.capacity }} <sub>Ltrs.</sub>
              </span>
            </v-card-subtitle>

            <v-card-actions>
              <v-btn
                x-small
                text
                color="secondary"
                :to="`/vehicles/edit/${vehicle.id}`"
                title="Edit"
                v-if="can('vehicle_edit')"
              >
                <v-icon small>mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                x-small
                text
                color="red darken-2"
                @click="setVehicleId(vehicle.id)"
                title="Delete"
                v-if="can('vehicle_delete')"
              >
                <v-icon small>mdi-delete</v-icon>
              </v-btn>
              <v-btn
                x-small
                text
                color="info darken-2"
                :to="`/vehicle_transactions/vehicle/${vehicle.id}`"
                title="Vehicle Transactions (کھاتے)"
                v-if="can('vehicle_transaction_access')"
              >
                <v-icon small>mdi-account-cash-outline</v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>

        <!-- Confirmation -->
        <Confirmation
          ref="confirmationComponent"
          :id="vehicleId"
          @confirmDeletion="
            vehicleId ? handleVehicleDelete() : handleMultipleVehiclesDelete()
          "
        />
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import EditVehicle from "./EditVehicle.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";

export default {
  components: {
    EditVehicle,
    Navbar,
    Confirmation,
  },

  data() {
    return {
      allVehicles: [],
      vehicleId: null,
    };
  },

  methods: {
    ...mapActions({
      getVehicles: "vehicle/getVehicles",
      getVehicle: "vehicle/getVehicle",
      deleteVehicle: "vehicle/deleteVehicle",
      deleteMultipleVehicles: "vehicle/deleteMultipleVehicles",
    }),

    setVehicleId(id) {
      this.vehicleId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleVehicleDelete() {
      await this.deleteVehicle(this.vehicleId);
      this.vehicleId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultipleVehiclesDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultipleVehicles(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      vehicles: "vehicle/vehicles",
      vehicle: "vehicle/vehicle",
      loading: "loading",
    }),
  },

  async mounted() {
    await this.getVehicles();

    this.allVehicles = this.vehicles;
  },
};
</script>