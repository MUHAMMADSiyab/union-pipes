<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Tanks</h5>

      <v-row>
        <v-col
          xl="4"
          lg="4"
          md="4"
          sm="12"
          cols="12"
          v-for="(tank, i) in tanks"
          :key="i"
        >
          <v-card class="mx-auto" max-width="344">
            <v-img
              src="/storage/common/tank-bg.png"
              height="200px"
              contain
            ></v-img>

            <v-card-title>
              {{ tank.name }} <span v-if="tank.code">({{ tank.code }})</span>

              <v-chip
                color="primary"
                class="ml-auto"
                title="Tank's Current Fuel Quantity"
                >{{ tank.current_fuel_quantity }} Ltrs.</v-chip
              >
            </v-card-title>

            <v-card-subtitle class="mt-2">
              <span title="Limit">
                <v-icon color="success">mdi-arrow-collapse-up</v-icon>
                {{ tank.limit }} <sub>Ltrs.</sub>
              </span>
              <span title="Lower Limit">
                <v-icon color="error">mdi-arrow-down</v-icon>
                {{ tank.lower_limit }} <sub>Ltrs.</sub>
              </span>
            </v-card-subtitle>

            <v-card-actions>
              <v-btn
                x-small
                text
                color="secondary"
                :to="`/tanks/edit/${tank.id}`"
                title="Edit"
                v-if="can('tank_edit')"
              >
                <v-icon small>mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                x-small
                text
                color="red darken-2"
                @click="setTankId(tank.id)"
                title="Delete"
                v-if="can('tank_delete')"
              >
                <v-icon small>mdi-delete</v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>

        <!-- Confirmation -->
        <Confirmation
          ref="confirmationComponent"
          :id="tankId"
          @confirmDeletion="
            tankId ? handleTankDelete() : handleMultipleTanksDelete()
          "
        />
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import EditTank from "./EditTank.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";

export default {
  components: {
    EditTank,
    Navbar,
    Confirmation,
  },

  data() {
    return {
      allTanks: [],
      tankId: null,
    };
  },

  methods: {
    ...mapActions({
      getTanks: "tank/getTanks",
      getTank: "tank/getTank",
      getProducts: "product/getProducts",
      deleteTank: "tank/deleteTank",
      deleteMultipleTanks: "tank/deleteMultipleTanks",
    }),

    setTankId(id) {
      this.tankId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleTankDelete() {
      await this.deleteTank(this.tankId);
      this.tankId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultipleTanksDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultipleTanks(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      tanks: "tank/tanks",
      tank: "tank/tank",
      products: "product/products",
      loading: "loading",
    }),
  },

  async mounted() {
    await Promise.all([this.getTanks(), this.getProducts()]);

    this.allTanks = this.tanks;
  },
};
</script>