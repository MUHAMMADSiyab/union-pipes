<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Sells</h5>

      <v-row>
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="sells"
            class="elevation-1"
            item-key="id"
            :search="search"
            :items-per-page="perPage"
            :loading="loading"
            :show-select="can('sell_delete') && !printMode"
            loading-text="Loading sells..."
            :footer-props="footerProps"
            v-model="selectedItems"
            dense
          >
            <!-- S# -->
            <template slot="item.sno" slot-scope="props">{{
              props.index + 1
            }}</template>

            <!-- Amounts -->
            <template slot="item.petrol_sold_amount" slot-scope="props">
              <span> {{ money(props.item.petrol_sold_amount) }} </span>
            </template>
            <template slot="item.diesel_sold_amount" slot-scope="props">
              <span> {{ money(props.item.diesel_sold_amount) }} </span>
            </template>
            <template slot="item.total_sell_amount" slot-scope="props">
              <span> {{ money(props.item.total_sell_amount) }} </span>
            </template>

            <!-- Actions -->
            <template slot="item.actions" slot-scope="props">
              <v-menu offset-y>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn x-small v-bind="attrs" v-on="on" text title="Action">
                    <v-icon small>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>

                <v-list dense>
                  <v-list-item
                    link
                    :to="`/sells/edit/${props.item.id}`"
                    title="Edit"
                    v-if="can('sell_edit')"
                  >
                    <v-list-item-title>Edit</v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    link
                    :to="`/sells/${props.item.id}`"
                    title="Details"
                    v-if="can('sell_show')"
                  >
                    <v-list-item-title>Details</v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    @click="setCurrentFinalReadings(props.item.id)"
                    title="Enter Final Readings"
                    v-if="can('sell_edit')"
                  >
                    <v-list-item-title>Final Readings</v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    @click="setSellId(props.item.id)"
                    title="Delete"
                    v-if="can('sell_delete')"
                  >
                    <v-list-item-title>Delete</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </template>

            <!-- Top -->
            <template v-slot:top v-if="!printMode">
              <v-btn
                color="error"
                small
                :disabled="!selectedItems.length"
                class="ma-2 text-right"
                @click="deleteMultiple"
                ><v-icon left>mdi-trash-can-outline</v-icon> Delete
                Selected</v-btn
              >

              <v-btn
                color="success"
                small
                link
                to="/sells/add"
                class="ma-2 text-right"
                v-if="can('sell_create')"
              >
                <v-icon left>mdi-plus-thick</v-icon>
                New Sell</v-btn
              >

              <!-- Export -->
              <Excel
                module="sells"
                :ids="selectedItems.map((item) => item.id)"
              />
              <CSV module="sells" :ids="selectedItems.map((item) => item.id)" />
              <PDF module="sells" :ids="selectedItems.map((item) => item.id)" />

              <v-text-field
                v-model="search"
                placeholder="Search"
                class="mx-4"
                append-icon="mdi-magnify"
              ></v-text-field>
            </template>
          </v-data-table>

          <!-- Update Final Readings -->
          <v-dialog v-model="finalReadingsDialog" width="1000">
            <UpdateFinalReadings
              :sell-id="currentFinalReadings"
              @closeDialog="closeFinalReadingsDialog"
            />
          </v-dialog>

          <!-- Confirmation -->
          <Confirmation
            ref="confirmationComponent"
            :id="sellId"
            @confirmDeletion="
              sellId ? handleSellDelete() : handleMultipleSellsDelete()
            "
          />
        </v-col>
      </v-row>

      <alert />
    </v-container>
  </div>
</template>
    
    <script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../mixins/DatatableMixin";
import CurrencyMixin from "../../mixins/CurrencyMixin";
import Navbar from "../navs/Navbar";
import Confirmation from "../globals/Confirmation";
import UpdateFinalReadings from "./partial/UpdateFinalReadings.vue";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";

export default {
  mixins: [DatatableMixin, CurrencyMixin],

  components: {
    Navbar,
    Confirmation,
    UpdateFinalReadings,
    Excel,
    CSV,
    PDF,
  },

  data() {
    return {
      finalReadingsDialog: false,
      currentFinalReadings: null,
      headers: [
        { text: "S#", value: "sno" },
        { text: "Sell Date", value: "sell_date" },
        { text: "Petrol Price", value: "petrol_price" },
        { text: "Diesel Price", value: "diesel_price" },
        {
          text: "Petrol Sold Qty. (Ltrs.)",
          value: "petrol_sold_quantity",
        },
        {
          text: "Diesel Sold Qty. (Ltrs.)",
          value: "diesel_sold_quantity",
        },
        { text: "Petrol Sell Amount", value: "petrol_sold_amount" },
        { text: "Diesel Sell Amount", value: "diesel_sold_amount" },
        { text: "Total Sell Amount", value: "total_sell_amount" },
        { text: "Actions", value: "actions", align: " d-print-none" },
      ],
      selectedItems: [],
      sellId: null,
    };
  },

  methods: {
    ...mapActions({
      getSells: "sell/getSells",
      getSell: "sell/getSell",
      deleteSell: "sell/deleteSell",
      deleteMultipleSells: "sell/deleteMultipleSells",
    }),

    setCurrentFinalReadings(sellId) {
      this.currentFinalReadings = sellId;
      this.finalReadingsDialog = true;
    },

    closeFinalReadingsDialog() {
      this.currentFinalReadings = null;
      this.finalReadingsDialog = false;
    },

    setSellId(id) {
      this.sellId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleSellDelete() {
      await this.deleteSell(this.sellId);
      this.sellId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultipleSellsDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultipleSells(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      sells: "sell/sells",
      sell: "sell/sell",
      loading: "loading",
    }),
  },

  mounted() {
    // remove actions if no access is given
    if (!this.can("sell_edit") && !this.can("sell_delete")) {
      this.headers = this.headers.filter(
        (header) => header.value !== "actions"
      );
    }
    this.getSells();
  },
};
</script>